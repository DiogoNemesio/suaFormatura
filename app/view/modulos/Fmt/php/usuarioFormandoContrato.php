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
global $em,$system,$tr,$log;

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
## Resgata os parâmetros passados
#################################################################################
if (isset($_GET['fid']))			$fid				= \Zage\App\Util::antiInjection($_GET['fid']);

#################################################################################
## Resgata os parâmetros passados pelo formulario de pesquisa
#################################################################################
if (!isset($codUsuario)) 		{
	
	if (!isset($fid)) 	\Zage\App\Erro::halt($tr->trans('Falta de Parâmetros').' (COD_USUARIO)');
	
	#################################################################################
	## Descompacta o FID
	#################################################################################
	\Zage\App\Util::descompactaId($fid);
	if (!isset($aSelFormandos))	\Zage\App\Erro::halt($tr->trans('Falta de Parâmetros').' (A_SEL_FOR)');
	
	$aSelFormandos		= explode(",",$aSelFormandos);
	if (sizeof($aSelFormandos) == 1) $codUsuario = $aSelFormandos[0];
}else{
	$aSelFormandos		= array($codUsuario);
}

#################################################################################
## Resgata as informações dos Formandos
#################################################################################
$aFormandos			= $em->getRepository('Entidades\ZgsegUsuario')->findBy(array('codigo' => $aSelFormandos));
$numFormandosSel	= sizeof($aFormandos);
if (!$aFormandos)	\Zage\App\Erro::halt($tr->trans('Formando não encontrado'));

if ($numFormandosSel == 1) {
	$nome		= $aFormandos[0]->getNome(); 
}else{
	$nome		= "Vários formandos selecionados";
}


$codOrganizacao = $system->getCodOrganizacao();
if (!isset($codOrganizacao)) 		{
	\Zage\App\Erro::halt($tr->trans('Falta de Parâmetros').' (COD_ORGANIZACAO)');
}else{
	$oOrgFmt	= $em->getRepository('Entidades\ZgfmtOrganizacaoFormatura')->findOneBy(array('codOrganizacao' => $system->getCodOrganizacao()));
}

#################################################################################
## Verifica se tem algum formando que não pode ter o contrato alterado, a restrição
## é caso o formando tenha mensalidade gerada.
#################################################################################
$podeAlterar	= true;
for ($i = 0; $i < $numFormandosSel; $i++) {
	
	#################################################################################
	## Resgata o registro da Pessoa associada ao Formando
	#################################################################################
	$oPessoa			= \Zage\Fin\Pessoa::getPessoaUsuario($system->getCodOrganizacao(),$aFormandos[$i]->getCodigo());
	if (!$oPessoa) 		die('1'.\Zage\App\Util::encodeUrl('||'.htmlentities('Violação de acesso, 0x912FB, Pessoa não encontrada')));
	
	#################################################################################
	## Verificar se o usuário pode alterar o contrato, só pode alterar caso não
	## tenha mensalidade gerada
	#################################################################################
	$temMensalidade	= \Zage\Fmt\Financeiro::temMensalidadeGerada($system->getCodOrganizacao(),$oPessoa->getCodigo());
	if ($temMensalidade)	$podeAlterar	= false;
	//$podeAlterar	= ($temMensalidade) ? false : true;
}


#################################################################################
## Caso seja alteração em massa, e tenha algum formando que não possa ter o 
## contrato alterado, emitir um erro
#################################################################################
if (($numFormandosSel > 1) && ($podeAlterar == false))  {
	$log->err("0x14721549: Erro de violação de acesso, Organização: ".$system->getCodOrganizacao()." Usuário: ".$system->getCodUsuario());
	\Zage\App\Erro::halt("0x14721549: Erro de violação de acesso !!!");
}


#################################################################################
## Variáveis usadas no cálculo das mensalidades
#################################################################################
$dataConclusao			= $oOrgFmt->getDataConclusao();
if (!$dataConclusao)	\Zage\App\Erro::halt("Data de Conclusão não informada");
$diaVencimento			= ($oOrgFmt->getDiaVencimento()) ? $oOrgFmt->getDiaVencimento() : 5;
$dataVenc				= date($system->config["data"]["dateFormat"],mktime(0, 0, 0, date('m') + 1, $diaVencimento , date('Y')));
$oDataVenc				= \DateTime::createFromFormat($system->config["data"]["dateFormat"],$dataVenc);
$interval				= $dataConclusao->diff($oDataVenc);
$numMesesConc			= (($interval->format('%y') * 12) + $interval->format('%m'));
$numMesesConc			= ($numMesesConc > 0) ? $numMesesConc : 0;
$texto					= ($numMesesConc == 1) ? "$numMesesConc mês" : "$numMesesConc meses";
$texto					.= " (".$dataConclusao->format($system->config["data"]["dateFormat"]).")";

#################################################################################
## Buscar o orçamento aceite, caso exista um, pois ele será usado como base
## Para calcular o valor pendente a ser gerado
## Se não existir, emitir um erro
#################################################################################
$orcamento				= \Zage\Fmt\Orcamento::getVersaoAceita($system->getCodOrganizacao());
if (!$orcamento)		\Zage\App\Erro::halt("Nenhum orçamento aceito");
$valorOrcado			= \Zage\App\Util::to_float($oOrgFmt->getValorPrevistoTotal());
$qtdFormandosBase		= (int) $oOrgFmt->getQtdePrevistaFormandos();
$valOrcPorFormando		= \Zage\App\Util::to_float(round($valorOrcado / $qtdFormandosBase,2));

#################################################################################
## Resgatar os eventos da Formatura
#################################################################################
$aEventos		= $em->getRepository('Entidades\ZgfmtEvento')->findBy(array('codFormatura' => $system->getCodOrganizacao()));

#################################################################################
## Resgata as informações de contrato
#################################################################################
if ($numFormandosSel == 1) {
	$oContrato 		= $em->getRepository('Entidades\ZgfmtContratoFormando')->findOneBy(array('codOrganizacao' => $codOrganizacao , 'codFormando' => $codUsuario));
	$totalParcelas	= 0;
	
	#################################################################################
	## Resgatar o status do usuário na organização
	#################################################################################
	$codStatusUsuario	= \Zage\Seg\Usuario::getStatusOrganizacao($codUsuario,$codOrganizacao);
	
	
	if ($oContrato){
		
		$codStatus		= $oContrato->getCodStatus()->getCodigo();
		if ($codStatusUsuario == "T") {
			$iconStatus		= "fa fa-ban red";
		}elseif ($codStatus 		== "A") {
			$iconStatus		= "fa fa-check-circle green";
		}elseif ($codStatus	== "C") {
			$iconStatus		= "fa fa-ban red";
		}else{
			$iconStatus		= "fa fa-star-half-o orange";
		}
		
		$titulo			= "Contrato <span class='pull-right'>Status: <i class='".$iconStatus."'></i>&nbsp;".$oContrato->getCodStatus()->getDescricao()."</span>";
		
		if (($podeAlterar == true) && ($codStatus == "A") && $codStatusUsuario != "T")	{
			$readonly		= "";
			$roData			= "datepicker";
			$hidSubmit		= "";
		}else{
			$readonly		= "readonly";
			$roData			= "";
			$hidSubmit		= "hidden";
		}
		
		$numMeses 			= $oContrato->getNumMeses();
		$codFormaPag		= ($oContrato->getCodFormaPagamento())	? $oContrato->getCodFormaPagamento()->getCodigo()	: null;
		$codTipoContrato	= ($oContrato->getCodTipoContrato())	? $oContrato->getCodTipoContrato()->getCodigo()		: null;
		
		#################################################################################
		## Montar um array com os eventos que já foram selecionados no contrato
		#################################################################################
		if ($codTipoContrato	== "P") {
			$aSelEventos		= array();
			$totalEventos		= 0;
			for ($i = 0; $i < sizeof($aEventos); $i++) {

				#################################################################################
				## Verificar se o evento está marcado e calcular o valor dele
				#################################################################################
				$sel		= $em->getRepository('Entidades\ZgfmtEventoParticipacao')->findOneBy(array('codOrganizacao' => $codOrganizacao,'codEvento' => $aEventos[$i]->getCodigo(),'codFormando' => $codUsuario)); 
				if ($sel)	{
					$valorEvento										= \Zage\Fmt\Evento::getValor($aEventos[$i]->getCodigo());
					$aSelEventos[$sel->getCodEvento()->getCodigo()]		= $valorEvento;
					$totalEventos										+= $valorEvento;
				}
			}
		}
		
		#################################################################################
		## Calcular o valor por formando com base no tipo de contrato
		#################################################################################
		if ($codTipoContrato		== "T") {
			$valPorFormando			= $valOrcPorFormando;
		}else{
			$valPorFormando			= $totalEventos;
		}
		
		
		#################################################################################
		## Carregar as parcelas
		#################################################################################
		$tabParcelas		= '';
		if ($oContrato->getCodigo()) {
			$parcelas		= $em->getRepository('Entidades\ZgfmtContratoFormandoParcela')->findBy(array('codContrato' => $oContrato->getCodigo()));
			$numParcelas	= sizeof($parcelas);
			if ($numParcelas > 0) {
				$tabHid			= "";
			}else{
				$tabHid			= "hidden";
			}
			for ($i = 0; $i < sizeof($parcelas); $i++) {
				$descParcela	= $parcelas[$i]->getParcela() . " / " . $numParcelas;
				$valParcela		= \Zage\App\Util::formataDinheiro($parcelas[$i]->getValor());
				$totalParcelas	+= \Zage\App\Util::to_float($parcelas[$i]->getValor());
				$data			= $parcelas[$i]->getDataVencimento()->format($system->config["data"]["dateFormat"]);
				$onchange		= ($podeAlterar)	? 'onchange="atualizaEValidaParcelasUsuFmtCnt();"' : null;
				$tabParcelas		.= '<tr>
					<td class="center" style="width: 20px;"><div class="inline" zg-type="zg-div-msg"></div></td><td class="center" style="width: 40px;">'.$descParcela.'</td>
					<td style="width: 120px;"><input type="text" class="form-control input-sm " '.$readonly.' name="aValor[]" value="'.$valParcela.'" '.$onchange.' maxlength="20" autocomplete="off" zg-data-toggle="mask" zg-data-mask="dinheiro" zg-data-mask-retira="0"></td>
					<td style="width: 120px;"><input type="text" class="form-control input-sm '.$roData.'" '.$readonly.' name="aData[]" value="'.$data.'" '.$onchange.'maxlength="10" autocomplete="off" zg-data-toggle="mask" zg-data-mask="data"></td>
					</tr>'; 
	
			}
		}else{
			$tabHid			= "hidden";
		}
	}else{
		if ($codStatusUsuario	== "T") {
			$readonly			= "readonly";
			$roData				= "";
			$hidSubmit			= "hidden";
			$titulo				= "Contrato <span class='pull-right'>Status: NÃO PODE GERAR (DESISTENTE)</span>";
		}else{
			$readonly			= "";
			$roData				= "datepicker";
			$hidSubmit			= "";
			$titulo				= "Contrato <span class='pull-right'>Status: NOVO</span>";
		}
		$numMeses			= null;
		$codFormaPag		= "BOL";
		$tabParcelas		= null;
		$tabHid				= "hidden";
		$codTipoContrato	= "T";
		$valPorFormando		= $valOrcPorFormando;
		
	}
}else{
	$numMeses			= null;
	$codFormaPag		= "BOL";
	$tabParcelas		= null;
	$tabHid				= "hidden";
	$codTipoContrato	= "T";
	$readonly			= "";
	$roData				= "datepicker";
	$hidSubmit			= "";
	$valPorFormando		= $valOrcPorFormando;
	$titulo				= "Contrato <span class='pull-right'>Status: NOVO (Em massa)</span>";
}



#################################################################################
## Select da Forma de Pagamento
#################################################################################
try {
	$aFormaPag	= $em->getRepository('Entidades\ZgfinFormaPagamento')->findBy(array(),array('descricao' => 'ASC'));
	$oFormaPag	= $system->geraHtmlCombo($aFormaPag,	'CODIGO', 'DESCRICAO',	$codFormaPag, null);
} catch (\Exception $e) {
	\Zage\App\Erro::halt($e->getMessage(),__FILE__,__LINE__);
}

#################################################################################
## Select de Sexo
#################################################################################
try {
	$aContratoTipo	= $em->getRepository('Entidades\ZgfmtContratoFormandoTipo')->findBy(array(),array('descricao' => ASC));
	$oContratoTipo	= $system->geraHtmlCombo($aContratoTipo, 'CODIGO', 'DESCRICAO',	$codTipoContrato, null);
} catch (\Exception $e) {
	\Zage\App\Erro::halt($e->getMessage(),__FILE__,__LINE__);
}


#################################################################################
## Tabela de eventos
#################################################################################
$tabEvento		= '';
for ($i = 0; $i < sizeof($aEventos); $i++) {
	$valorEvento		= \Zage\Fmt\Evento::getValor($aEventos[$i]->getCodigo());
	$checked			= (isset($aSelEventos[$aEventos[$i]->getCodigo()])) ? 'checked="checked"' : null;
	$tabEvento	.= '<tr>
		<td style="text-align: center;"><label class="position-relative"><input type="checkbox" '.$checked.' name="codEventoSel[]" onchange="calculaValorPorFormandoUsuFmtCnt();" value="'.$aEventos[$i]->getCodigo().'" zg-val-evento="'.\Zage\App\Util::formataDinheiro($valorEvento).'" class="ace"/><span class="lbl"></span></label></td>
		<td style="text-align: center;">'.$aEventos[$i]->getCodTipoEvento()->getDescricao().'</td>
		<td style="text-align: center;">'.\Zage\App\Util::to_money($valorEvento).'</td>
		</tr>';
}

#################################################################################
## Urls
#################################################################################
$uid 				= \Zage\App\Util::encodeUrl('_codMenu_='.$_codMenu_.'&_icone_='.$_icone_);
if (!isset($urlVoltar) || (!$urlVoltar)) {
	$urlVoltar			= ROOT_URL . "/Fmt/usuarioFormandoLis.php?id=".$uid;
}else{
	$urlVoltar			= $urlVoltar . "?id=".$id;
}

#################################################################################
## Carregando o template html
#################################################################################
$tpl	= new \Zage\App\Template();
$tpl->load(\Zage\App\Util::getCaminhoCorrespondente(__FILE__, \Zage\App\ZWS::EXT_HTML));

#################################################################################
## Define os valores das variáveis
#################################################################################
$tpl->set('ID'						,$id);
$tpl->set('FID'						,$fid);
$tpl->set('TITULO'					,$titulo);

$tpl->set('COD_ORGANIZACAO'			,$codOrganizacao);
$tpl->set('COD_USUARIO'				,$codUsuario);
$tpl->set('A_SEL_FORMANDOS'			,implode(",", $aSelFormandos));

$tpl->set('NUM_MESES'				,$numMeses);
$tpl->set('MAX_PARCELAS'			,$numMeses);
$tpl->set('DATA_VENC'				,$dataVenc);
$tpl->set('DATA_CONCLUSAO'			,$dataConclusao->format($system->config["data"]["dateFormat"]));
$tpl->set('VAL_ORC_POR_FORMANDO'	,\Zage\App\Util::formataDinheiro($valOrcPorFormando));
$tpl->set('VAL_POR_FORMANDO'		,\Zage\App\Util::formataDinheiro($valPorFormando));
$tpl->set('VAL_POR_FORMANDO_FMT'	,\Zage\App\Util::to_money($valPorFormando));
$tpl->set('FORMATO_DATA'			,$system->config["data"]["jsDateFormat"]);
$tpl->set('FORMAS_PAG'				,$oFormaPag);
$tpl->set('TAB_PARCELAS'			,$tabParcelas);
$tpl->set('TAB_HID'					,$tabHid);
$tpl->set('TOTAL_PARCELAS_FMT'		,\Zage\App\Util::to_money($totalParcelas));
$tpl->set('TAB_EVENTOS'				,$tabEvento);
$tpl->set('TIPO_CONTRATO'			,$oContratoTipo);

$tpl->set('NOME'					,$nome);
$tpl->set('TEXTO'					,$texto);
$tpl->set('READONLY'				,$readonly);
$tpl->set('RO_DATA'					,$roData);
$tpl->set('HID_SUBMIT'				,$hidSubmit);

$tpl->set('URL_VOLTAR'				,$urlVoltar);
$tpl->set('DP_MODAL'				,\Zage\App\Util::getCaminhoCorrespondente(__FILE__,\Zage\App\ZWS::EXT_DP,\Zage\App\ZWS::CAMINHO_RELATIVO));

#################################################################################
## Por fim exibir a página HTML
#################################################################################
$tpl->show();

