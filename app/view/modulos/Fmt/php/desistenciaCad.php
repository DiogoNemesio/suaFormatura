<?php
#################################################################################
## Includes
#################################################################################
if (defined('DOC_ROOT')) {
	include_once(DOC_ROOT . 'include.php');
}else{
	include_once('../include.php');
}

#################################################################################
## Variáveis globais
#################################################################################
global $system,$em,$tr,$log;

#################################################################################
## Resgata a variável ID que está criptografada
#################################################################################
if (isset($_GET['id'])) {
	$id = \Zage\App\Util::antiInjection($_GET["id"]);
}elseif (isset($_POST['id'])) {
	$id = \Zage\App\Util::antiInjection($_POST["id"]);
}elseif (isset($id)) 	{
	$id = \Zage\App\Util::antiInjection($id);
}else{
	\Zage\App\Erro::halt('Falta de Parâmetros');
}

#################################################################################
## Descompacta o ID
#################################################################################
\Zage\App\Util::descompactaId($id);

#################################################################################
## Verifica se o usuário tem permissão no menu
#################################################################################
$system->checaPermissao($_codMenu_);

#################################################################################
## Verificar parâmetro obrigatório
#################################################################################
if (!isset($codFormando)) \Zage\App\Erro::halt('Falta de Parâmetros 2');

#################################################################################
## Verificar se o usuário existe
#################################################################################
$oUsuario	= $em->getRepository('Entidades\ZgsegUsuario')->findOneBy(array('codigo' => $codFormando));
if (!$oUsuario) \Zage\App\Erro::halt('Formando não existe');

#################################################################################
## Resgatar o status da associação com a Formatura
#################################################################################
$oStatus	= $em->getRepository('Entidades\ZgsegUsuarioOrganizacao')->findOneBy(array('codUsuario' => $codFormando,'codOrganizacao' => $system->getCodOrganizacao()));
$codStatus	= ($oStatus->getCodStatus()) ? $oStatus->getCodStatus()->getCodigo() : null;
$codPerfil	= ($oStatus->getCodPerfil()) ? $oStatus->getCodPerfil(): null;
if (!$codPerfil) \Zage\App\Erro::halt('Perfil inválido para o Formando');

#################################################################################
## Verificar o status da associação a Formatura, para definir se poderá ou não
## desistir da formatura
#################################################################################
switch ($codStatus) {
	case "A":
	case "P":
	case "B":
		$podeDesistir	= true;
		break;
	default:
		$podeDesistir	= false;
		break;
}

if (!$podeDesistir)	\Zage\App\Erro::halt('Tentativa indevida de desistência: 0x6a31');

#################################################################################
## Verificar se o usuário tem perfil de formando nessa organização
#################################################################################
if ($codPerfil->getCodTipoUsuario()->getCodigo() != "F") {
	\Zage\App\Erro::halt('Esse usuário não é um formando');
}

#################################################################################
## Resgatar o valor por formando
#################################################################################
$oOrgFmt	= $em->getRepository('Entidades\ZgfmtOrganizacaoFormatura')->findOneBy(array('codOrganizacao' => $system->getCodOrganizacao()));

if ($oOrgFmt->getValorPrevistoTotal() && $oOrgFmt->getQtdePrevistaFormandos()){
	$valorFormatura = round(($oOrgFmt->getValorPrevistoTotal()/$oOrgFmt->getQtdePrevistaFormandos()),2);
}

#################################################################################
## Resgatar os valores ja pago por esse formando
#################################################################################
$aPago				= \Zage\Fmt\Financeiro::getValorPagoFormando($system->getCodOrganizacao(),$oUsuario->getCpf());
$aProvisionado		= \Zage\Fmt\Financeiro::getValorProvisionadoUnicoFormando($system->getCodOrganizacao(),$oUsuario->getCpf());
$valPagoMensalidade	= \Zage\App\Util::to_float($aPago["mensalidade"]);
$valProvMensalidade	= \Zage\App\Util::to_float($aProvisionado["mensalidade"]);
$valPagoSistema		= \Zage\App\Util::to_float($aPago["sistema"]);
$valProvSistema		= \Zage\App\Util::to_float($aProvisionado["sistema"]);
$taxaUso			= \Zage\App\Util::to_float(\Zage\Adm\Contrato::getValorLicenca($system->getCodOrganizacao()));
$taxaAdmin			= \Zage\App\Util::to_float($oOrgFmt->getTaxaAdministracao());
$diaVencimento		= ($oOrgFmt->getDiaVencimento()) ? $oOrgFmt->getDiaVencimento() : 5;
$dataVenc			= date($system->config["data"]["dateFormat"],mktime(0, 0, 0, date('m') + 1, $diaVencimento , date('Y')));

#################################################################################
## Formatar as taxas
#################################################################################
//if ($taxaAdmin		< 0)		$taxaAdmin		= 0;
//if ($taxaUso		< 0)		$taxaUso		= 0;

$dataConclusao		= $oOrgFmt->getDataConclusao();
if (!$dataConclusao)	\Zage\App\Erro::halt("Data de Conclusão não informada");

$hoje				= new DateTime('now');
$dataAtivacao		= ($oStatus->getDataCadastro()) ? $oStatus->getDataCadastro() : null;
if (!$dataAtivacao)	\Zage\App\Erro::halt("Formando: ".$oUsuario->getNome()." sem data de Ativação !!!");

$interval1			= $dataAtivacao->diff($hoje);
$interval2			= $dataConclusao->diff($dataAtivacao);
$interval3			= $dataConclusao->diff($hoje);

$numMesesUso		= (($interval1->format('%y') * 12) + $interval1->format('%m'));
$numMesesTotal		= (($interval2->format('%y') * 12) + $interval2->format('%m'));
$numMesesConc		= (($interval3->format('%y') * 12) + $interval3->format('%m'));

$valDevidoSistema	= round($numMesesUso * $taxaUso,2);
$valTotalSistema	= round($numMesesTotal * $taxaUso,2);


#################################################################################
## Resgatar os eventos
#################################################################################
$eventos		= $em->getRepository('Entidades\ZgfmtEvento')->findBy(array('codFormatura' => $system->getCodOrganizacao()));
$numEventos		= 0;
if ($eventos){
	$htmlEventos 	= '';
	$htmlEventos	.= '<div class="col-sm-12" align="center">';
	$htmlEventos	.= '<h5 align="center">Selecione os eventos que o formando ainda irá participar</h5>';
	$htmlEventos	.= '<table id="tabItemID" zg-table-orc="1" class="table table-hover table-condensed">';
	
	for ($i = 0; $i < sizeof($eventos); $i++){
		$numEventos		+= 1;
		$checked		= "";
		
		$htmlEventos	.= '<tr>';
		$htmlEventos	.= '<td class="col-sm-1 center"><label class="position-relative"><input type="checkbox" '.$checked.' name="codEventoSel[]" zg-name="selItem" class="ace" value="'.$eventos[$i]->getCodigo().'" onchange="calculaValorTotalEventoDesCad();" /><span class="lbl"></span></label></td>';
		$htmlEventos	.= '<td class="col-sm-1">'.$eventos[$i]->getCodTipoEvento()->getDescricao().'</td>';
		$htmlEventos	.= '<td class="col-sm-1 left"><span></span><input class="input-small" id="valor_'.$eventos[$i]->getCodigo().'_ID" type="text" name="aValor[]" value="'.\Zage\App\Util::formataDinheiro($eventos[$i]->getValorAvulso()).'" zg-codigo="'.$eventos[$i]->getCodigo().'" zg-name="valor" autocomplete="off" zg-data-toggle="mask" zg-data-mask="dinheiro" onchange="calculaValorTotalEventoDesCad();"></td>';
		
		
		
		/**
		
		$htmlEventos .= '<div class="checkbox">';		
		$htmlEventos .= '<label class="block">';
		$htmlEventos .= '<input name="form-field-checkbox" type="checkbox" class="ace" />';
		
		$htmlEventos .= '<span class="lbl bigger-120"> '.$eventos[$i]->getCodTipoEvento()->getDescricao().'</span>';
		$htmlEventos .= '</label>';
		$htmlEventos .= '</div>';
		
		**/
	}  
	
	$htmlEventos	.= '</table>';
	$htmlEventos	.= '</div>';
	
	
	
}else{
	$htmlEventos 	= 'Nenhum evento';
}



//$log->info("Valor ja pago de Mensalidade do Formando: ".$valPagoMensalidade);
//$log->info("Valor ja pago de Sistema do Formando: ".$valPagoSistema);
//$log->info("Saldo a Cancelar: ".$saldoCancelar);
//$log->info("Saldo de Sistema: ".$saldoSistema);

#################################################################################
## Gerenciar as URls
#################################################################################
if (!isset($urlVoltar) || (!$urlVoltar)) {
	$urlVoltar			= ROOT_URL . "/Fin/contaReceberLis.php?id=".$id;
	//$urlVoltar			= ROOT_URL . "/Fin/contaReceberRecLis.php?id=".$id;
}else{
	$urlVoltar			= $urlVoltar . "&id=".$id;
}


#################################################################################
## Select do tipo de Base de Calculo
#################################################################################
try {
	$aBaseCalc	= $em->getRepository('Entidades\ZgfmtBaseCalculoTipo')->findAll();
	$oBaseCalc	= $system->geraHtmlCombo($aBaseCalc,	'CODIGO', 'DESCRICAO',	null, 		null);
} catch (\Exception $e) {
	\Zage\App\Erro::halt($e->getMessage(),__FILE__,__LINE__);
}

#################################################################################
## Select da Forma de Pagamento
#################################################################################
try {
	$aFormaPag	= $em->getRepository('Entidades\ZgfinFormaPagamento')->findBy(array(),array('descricao' => 'ASC'));
	$oFormaPag	= $system->geraHtmlCombo($aFormaPag,	'CODIGO', 'DESCRICAO',	'BOL', null);
} catch (\Exception $e) {
	\Zage\App\Erro::halt($e->getMessage(),__FILE__,__LINE__);
}

#################################################################################
## Select da Conta de Crédito
#################################################################################
try {

	#################################################################################
	## Verifica se a formatura está sendo administrada por um Cerimonial, para resgatar as contas do cerimonial tb
	#################################################################################
	$oFmtAdm		= \Zage\Fmt\Formatura::getCerimonalAdm($system->getCodOrganizacao());

	if ($oFmtAdm)	{
		$aCntCer	= $em->getRepository('Entidades\ZgfinConta')->findBy(array('codOrganizacao' => $oFmtAdm->getCodigo()),array('nome' => 'ASC'));
	}else{
		$aCntCer	= null;
	}

	$aConta		= $em->getRepository('Entidades\ZgfinConta')->findBy(array('codOrganizacao' => $system->getCodOrganizacao()),array('nome' => 'ASC'));

	if ($aCntCer) {
		$oConta		= "<optgroup label='Contas do Cerimonial'>";
		for ($i = 0; $i < sizeof($aCntCer); $i++) {
			$valBol		= ($aCntCer[$i]->getCodTipo()->getCodigo() == "CC") ? \Zage\Fmt\Financeiro::getValorBoleto($aCntCer[$i]->getCodigo()) : 0;
			$oConta	.= "<option value='".$aCntCer[$i]->getCodigo()."' zg-val-boleto='".$valBol."'>".$aCntCer[$i]->getNome()."</option>";
		}
		$oConta		.= '</optgroup>';
	}

	if ($aConta) {
		$oConta		.= ($aCntCer) ? "<optgroup label='Contas da Formatura'>" : '';
		for ($i = 0; $i < sizeof($aConta); $i++) {
			$valBol		= ($aConta[$i]->getCodTipo()->getCodigo() == "CC") ? \Zage\Fmt\Financeiro::getValorBoleto($aConta[$i]->getCodigo()) : 0;
			$oConta	.= "<option value='".$aConta[$i]->getCodigo()."' zg-val-boleto='".$valBol."'>".$aConta[$i]->getNome()."</option>";
		}
		$oConta		.= ($aCntCer) ? '</optgroup>' : '';
	}


} catch (\Exception $e) {
	\Zage\App\Erro::halt($e->getMessage(),__FILE__,__LINE__);
}


#################################################################################
## Carregando o template html
#################################################################################
$tpl	= new \Zage\App\Template();
$tpl->load(\Zage\App\Util::getCaminhoCorrespondente(__FILE__, \Zage\App\ZWS::EXT_HTML));

#################################################################################
## Define os valores das variáveis
#################################################################################
$tpl->set('ID'					,$id);
$tpl->set('TITULO'				,'Desistência');
$tpl->set('HTML_EVENTOS'		,$htmlEventos);
$tpl->set('URL_VOLTAR'			,$urlVoltar);

$tpl->set('TOTAL_MENSALIDADE'	,$valorFormatura);
$tpl->set('PAGO_MENSALIDADE'	,$valPagoMensalidade);
$tpl->set('TOTAL_SISTEMA'		,$valTotalSistema);
$tpl->set('DEVIDO_SISTEMA'		,$valDevidoSistema);
$tpl->set('PAGO_SISTEMA'		,$valPagoSistema);
$tpl->set('BASES_CALCULO'		,$oBaseCalc);
$tpl->set('NUM_EVENTOS'			,$numEventos);
$tpl->set('CONTAS'				,$oConta);
$tpl->set('FORMAS_PAG'			,$oFormaPag);
$tpl->set('TAXA_ADMIN_FMT'		,\Zage\App\Util::to_money($taxaAdmin));
$tpl->set('TAXA_ADMIN'			,\Zage\App\Util::formataDinheiro($taxaAdmin));
$tpl->set('NUM_MESES_MAX'		,$numMesesConc);
$tpl->set('DATA_VENC'			,$dataVenc);
$tpl->set('DP'					,\Zage\App\Util::getCaminhoCorrespondente(__FILE__,\Zage\App\ZWS::EXT_DP,\Zage\App\ZWS::CAMINHO_RELATIVO));


#################################################################################
## Por fim exibir a página HTML
#################################################################################
$tpl->show();