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
global $em,$system,$tr;

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
## Resgata os parâmetros passados pelo formulario de pesquisa
#################################################################################
if (!isset($codUsuario)) 		{
	\Zage\App\Erro::halt($tr->trans('Falta de Parâmetros').' (COD_USUARIO)');
}

$codOrganizacao = $system->getCodOrganizacao();
if (!isset($codOrganizacao)) 		{
	\Zage\App\Erro::halt($tr->trans('Falta de Parâmetros').' (COD_ORGANIZACAO)');
}else{
	$oOrgFmt	= $em->getRepository('Entidades\ZgfmtOrganizacaoFormatura')->findOneBy(array('codOrganizacao' => $system->getCodOrganizacao()));
}

#################################################################################
## Resgata as informações de contrato
#################################################################################
$oContrato = $em->getRepository('Entidades\ZgfmtContratoFormando')->findOneBy(array('codOrganizacao' => $codOrganizacao , 'codFormando' => $codUsuario));

if ($oContrato){
	$numMeses 	= $oContrato->getNumMeses();
}else{
	$numMeses	= null;
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
$valPorFormando			= $valorOrcado / $qtdFormandosBase;

#################################################################################
## Urls
#################################################################################
$uid 				= \Zage\App\Util::encodeUrl('_codMenu_='.$_codMenu_.'&_icone_='.$_icone_);
$urlVoltar			= ROOT_URL . "/Fmt/usuarioFormandoLis.php?id=".$uid;

#################################################################################
## Carregando o template html
#################################################################################
$tpl	= new \Zage\App\Template();
$tpl->load(\Zage\App\Util::getCaminhoCorrespondente(__FILE__, \Zage\App\ZWS::EXT_HTML));

#################################################################################
## Define os valores das variáveis
#################################################################################
$tpl->set('ID'						,$id);
$tpl->set('TITULO'					,'Contrato');

$tpl->set('COD_ORGANIZACAO'			,$codOrganizacao);
$tpl->set('COD_USUARIO'				,$codUsuario);

$tpl->set('NUM_MESES'				,$numMeses);
$tpl->set('MAX_PARCELAS'			,$numMeses);
$tpl->set('DATA_VENC'				,$dataVenc);
$tpl->set('DATA_CONCLUSAO'			,$dataConclusao->format($system->config["data"]["dateFormat"]));
$tpl->set('VAL_POR_FORMANDO'		,\Zage\App\Util::formataDinheiro($valPorFormando));
$tpl->set('VAL_POR_FORMANDO_FMT'	,\Zage\App\Util::to_money($valPorFormando));
$tpl->set('FORMATO_DATA'			,$system->config["data"]["jsDateFormat"]);

//$tpl->set('DISABLED'				,$podeEnviar);
$tpl->set('TEXTO'					,$texto);

$tpl->set('URL_VOLTAR'				,$urlVoltar);
$tpl->set('DP_MODAL'				,\Zage\App\Util::getCaminhoCorrespondente(__FILE__,\Zage\App\ZWS::EXT_DP,\Zage\App\ZWS::CAMINHO_RELATIVO));

#################################################################################
## Por fim exibir a página HTML
#################################################################################
$tpl->show();

