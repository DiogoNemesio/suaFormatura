<?php
#################################################################################
## Includes
#################################################################################
if (defined('DOC_ROOT')) {
	include_once(DOC_ROOT . 'include.php');
}else{
	include_once('./include.php');
}

#################################################################################
## Variáveis globais
#################################################################################
global $system,$em,$tr;


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
## Resgata a url desse script
#################################################################################
$url		= ROOT_URL . "/Fin/". basename(__FILE__)."?id=".$id;

#################################################################################
## Resgata os dados do grid
#################################################################################
try {
	$formandos	= \Zage\Fmt\Formatura::listaFormandos($system->getCodOrganizacao());
	$oOrgFmt	= $em->getRepository('Entidades\ZgfmtOrganizacaoFormatura')->findOneBy(array('codOrganizacao' => $system->getCodOrganizacao()));
} catch (\Exception $e) {
	\Zage\App\Erro::halt($e->getMessage());
}

#################################################################################
## Calcula a quantidade de formandos
#################################################################################
$totalFormandos			= \Zage\Fmt\Formatura::getNumFormandos($system->getCodOrganizacao());

#################################################################################
## Variáveis usadas no cálculo das mensalidades
#################################################################################
$dataConclusao			= $oOrgFmt->getDataConclusao();
if (!$dataConclusao)	\Zage\App\Erro::halt("Data de Conclusão não informada");
$hoje					= new DateTime('now');
$interval				= $dataConclusao->diff($hoje);
$numMesesConc			= (($interval->format('%y') * 12) + $interval->format('%m'));
$diaVencimento			= ($oOrgFmt->getDiaVencimento()) ? $oOrgFmt->getDiaVencimento() : 5;
$dataVenc				= date($system->config["data"]["dateFormat"],mktime(0, 0, 0, date('m') + 1, $diaVencimento , date('Y')));

#################################################################################
## Taxas / Configurações
#################################################################################
$indRepTaxaSistema		= ($oOrgFmt->getIndRepassaTaxaSistema() !== null) ? $oOrgFmt->getIndRepassaTaxaSistema() : 1;
$taxaAdmin				= \Zage\App\Util::to_float($oOrgFmt->getTaxaAdministracao());
$taxaUso				= ($indRepTaxaSistema) ? \Zage\App\Util::to_float(\Zage\Adm\Contrato::getValorLicenca($system->getCodOrganizacao())) : 0;
$mostraIndTaxaSistema	= ($indRepTaxaSistema) ? null : "hidden";
$chkIndTaxaSistema		= ($indRepTaxaSistema) ? "checked" : null;

#################################################################################
## Formatar as taxas
#################################################################################
if ($taxaAdmin		< 0)		$taxaAdmin		= 0;
if ($taxaUso		< 0)		$taxaUso		= 0;

#################################################################################
## Buscar o orçamento aceite, caso exista um, pois ele será usado como base
## Para calcular o valor pendente a ser gerado
## Se não existir, emitir um erro
#################################################################################
$orcamento				= \Zage\Fmt\Orcamento::getVersaoAceita($system->getCodOrganizacao());
if (!$orcamento)		\Zage\App\Erro::halt("Nenhum orçamento aceito");
$valorOrcado			= \Zage\App\Util::to_float($oOrgFmt->getValorPrevistoTotal());
$qtdFormandosBase		= (int) $oOrgFmt->getQtdePrevistaFormandos();
$mensalidadeFormando	= $valorOrcado / $qtdFormandosBase;

#################################################################################
## Calcular o valor já provisionado por formando
#################################################################################
$oValorProv				= \Zage\Fmt\Financeiro::getValorProvisionadoPorFormando($system->getCodOrganizacao());

#################################################################################
## Montar o array para facilitar a impressão no grid dos valores provisionados
## Montar um array que será enviado ao Html para validar se os formandos
## selecionados tem os mesmos valores de mensalidade e sistema
#################################################################################
$aValorProv				= array();
$aCodigos				= array();
for ($i = 0; $i < sizeof($oValorProv); $i++) {
	$total													= \Zage\App\Util::to_float($oValorProv[$i]["mensalidade"]) + \Zage\App\Util::to_float($oValorProv[$i]["sistema"]);
	$aCodigos[$oValorProv[$i][0]->getCgc()]["MENSALIDADE"]	= \Zage\App\Util::to_float($oValorProv[$i]["mensalidade"]);
	$aCodigos[$oValorProv[$i][0]->getCgc()]["SISTEMA"]		= \Zage\App\Util::to_float($oValorProv[$i]["sistema"]);
	$aCodigos[$oValorProv[$i][0]->getCgc()]["TOTAL"]		= $total;
	$aValorProv[$oValorProv[$i][0]->getCgc()]				= $total;
}

#################################################################################
## Cria o objeto do Grid (bootstrap)
#################################################################################
$grid			= \Zage\App\Grid::criar(\Zage\App\Grid\Tipo::TP_BOOTSTRAP,"GeraMensalidade");
$checkboxName	= "selItemGeracaoConta";
$grid->adicionaCheckBox($checkboxName);
$grid->adicionaTexto($tr->trans('NOME'),				25	,$grid::CENTER	,'nome');
$grid->adicionaTexto($tr->trans('CPF'),					13	,$grid::CENTER	,'cpf','cpf');
$grid->adicionaTexto($tr->trans('STATUS'),				17	,$grid::CENTER	,'');
$grid->adicionaMoeda($tr->trans('R$ PROVISIONADO'),		15	,$grid::CENTER	,'');
$grid->adicionaTexto($tr->trans('R$ PROVISIONAR'),		15	,$grid::CENTER	,'');
$grid->adicionaTexto($tr->trans('MESES USO'),			10	,$grid::CENTER	,'');
$grid->importaDadosDoctrine($formandos);

#################################################################################
## Popula os valores dos botões
#################################################################################
for ($i = 0; $i < sizeof($formandos); $i++) {

	#################################################################################
	## Atualizar a coluna status com o status da associação do formando a Organização (Formatura)
	#################################################################################
	$oStatus	= $em->getRepository('Entidades\ZgsegUsuarioOrganizacao')->findOneBy(array('codUsuario' => $formandos[$i]->getCodigo(),'codOrganizacao' => $system->getCodOrganizacao()));
	$codStatus	= ($oStatus->getCodStatus()) ? $oStatus->getCodStatus()->getCodigo() : null;
	$status		= ($oStatus->getCodStatus()) ? $oStatus->getCodStatus()->getDescricao() : null;
	$grid->setValorCelula($i,3,$status);
	
	#################################################################################
	## Verificar o status da associação a Formatura, para definir se poderá ou não 
	## Gerar mensalidade para o Formando
	#################################################################################
	switch ($codStatus) {
		case "A":
		case "P":
		case "B":
		case "D":
			$podeGerar	= true;
			break;
		default:
			$podeGerar	= false;
			break;
				
	}

	if ($podeGerar	== true) {
		
		#################################################################################
		## Definir o valor da Checkbox
		#################################################################################
		$grid->setValorCelula($i,0,$formandos[$i]->getCpf());
		
	}else{
		
		#################################################################################
		## Desabilitar a checkBox
		#################################################################################
		$grid->desabilitaCelula($i, 0);
	}
	
	#################################################################################
	## Definir os valores totais
	#################################################################################
	$valProvisionado			= (isset($aValorProv[$formandos[$i]->getCpf()])) ? $aValorProv[$formandos[$i]->getCpf()] : 0;
	$saldo						= round($mensalidadeFormando - $valProvisionado,2);
	$aCodigos[$formandos[$i]->getCpf()]["SALDO"]	= $saldo;
	$grid->setValorCelula($i,4,$valProvisionado);
	
	#################################################################################
	## Valor a provisionar
	#################################################################################
	if ($saldo > 0){
		$grid->setValorCelula($i, 5, "<span style='color:red'><i class='fa fa-arrow-down red'></i> ".\Zage\App\Util::to_money($saldo)."</span>");
	}else if ($saldo == 0) {
		$grid->setValorCelula($i, 5, "<span style='color:green'><i class='fa fa-check-circle green'></i> ".\Zage\App\Util::to_money($saldo)."</span>");
	}else{
		$grid->setValorCelula($i, 5, "<span style='color:green'><i class='fa fa-arrow-up green'></i> ".\Zage\App\Util::to_money($saldo)."</span>");
	}
	
	#################################################################################
	## Número de meses de uso do sistema
	#################################################################################
	$dataAtivacao			= ($oStatus->getDataCadastro()) ? $oStatus->getDataCadastro() : null;
	if (!$dataAtivacao)		\Zage\App\Erro::halt("Formando: ".$formandos[$i]->getNome()." sem data de Ativação !!!");
	$interval				= $dataConclusao->diff($dataAtivacao);
	$numMesesConc			= (($interval->format('%y') * 12) + $interval->format('%m'));
	$grid->setValorCelula($i,6,$numMesesConc);
	$aCodigos[$formandos[$i]->getCpf()]["NUM_MESES"]	= $numMesesConc;
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
## Gerar o código html do grid
#################################################################################
try {
	$htmlGrid	= $grid->getHtmlCode();
} catch (\Exception $e) {
	\Zage\App\Erro::halt($e->getMessage());
}

#################################################################################
## Url Voltar
#################################################################################
$urlVoltar				= ROOT_URL."/Fin/contaReceberLis.php?id=".$id;

#################################################################################
## Carregando o template html
#################################################################################
$tpl	= new \Zage\App\Template();
$tpl->load(\Zage\App\Util::getCaminhoCorrespondente(__FILE__, \Zage\App\ZWS::EXT_HTML));

#################################################################################
## Define os valores das variáveis
#################################################################################
$tpl->set('GRID'					,$htmlGrid);
$tpl->set('IC'						,$_icone_);
$tpl->set('ID'						,$id);
$tpl->set('FILTER_URL'				,$url);
$tpl->set('DIVCENTRAL'				,$system->getDivCentral());
$tpl->set('CHECK_NAME'				,$checkboxName);
$tpl->set('NUM_MESES_MAX'			,$numMesesConc);
$tpl->set('TOTAL_FORMANDOS'			,$totalFormandos);
$tpl->set('DATA_CONCLUSAO'			,$dataConclusao->format($system->config["data"]["dateFormat"]));
$tpl->set('DATA_VENC'				,$dataVenc);
$tpl->set('VALOR_TOTAL_FORM_FMT'	,\Zage\App\Util::to_money($valorOrcado));
$tpl->set('VALOR_TOTAL_FORMATURA'	,\Zage\App\Util::formataDinheiro($valorOrcado));
$tpl->set('MENSALIDADE_FORMANDO'	,\Zage\App\Util::formataDinheiro($mensalidadeFormando));
$tpl->set('MENSALIDADE_FORMANDO_FMT',\Zage\App\Util::to_money($mensalidadeFormando));
$tpl->set('SALDO_FORMANDO_FMT'		,\Zage\App\Util::to_money($saldo));
$tpl->set('TAXA_USO'				,\Zage\App\Util::formataDinheiro($taxaUso));
$tpl->set('TAXA_ADMIN_FMT'			,\Zage\App\Util::to_money($taxaAdmin));
$tpl->set('TAXA_ADMIN'				,\Zage\App\Util::formataDinheiro($taxaAdmin));
$tpl->set('MOSTRA_IND_TAXA_SISTEMA'	,$mostraIndTaxaSistema);
$tpl->set('CHK_IND_TAXA_SISTEMA'	,$chkIndTaxaSistema);

$tpl->set('CONTAS'					,$oConta);
$tpl->set('FORMAS_PAG'				,$oFormaPag);
$tpl->set('FORMATO_DATA'			,$system->config["data"]["jsDateFormat"]);
$tpl->set('DP'						,\Zage\App\Util::getCaminhoCorrespondente(__FILE__,\Zage\App\ZWS::EXT_DP,\Zage\App\ZWS::CAMINHO_RELATIVO));
$tpl->set('URL_VOLTAR'				,$urlVoltar);
$tpl->set('JSON_CODIGOS'			,json_encode($aCodigos));


#################################################################################
## Por fim exibir a página HTML
#################################################################################
$tpl->show();
