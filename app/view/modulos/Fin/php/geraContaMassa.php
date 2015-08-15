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
global $em,$tr,$system;

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
if (!isset($_GET["cid"])) \Zage\App\Erro::halt('Falta de Parâmetros 2');

#################################################################################
## Descompacta o CID
#################################################################################
$cid			= \Zage\App\Util::antiInjection($_GET["cid"]);
\Zage\App\Util::descompactaId($cid);

#################################################################################
## Monta o array
#################################################################################
if (!isset($aSelFormandos)) \Zage\App\Erro::halt('Falta de Parâmetros 3');
$aSelFormandos		= explode(",",$aSelFormandos);

#################################################################################
## Resgata as informações do banco
#################################################################################
try {

	$formandos	= $em->getRepository('Entidades\ZgsegUsuario')->findBy(array('codigo' => $aSelFormandos));
	$oOrgFmt	= $em->getRepository('Entidades\ZgfmtOrganizacaoFormatura')->findOneBy(array('codOrganizacao' => $system->getCodOrganizacao()));
//	$contrato	= $em->getRepository('Entidades\ZgadmContrato')->findOneBy(array('codOrganizacao' => $system->getCodOrganizacao()));

} catch (\Exception $e) {
	\Zage\App\Erro::halt($e->getMessage());
}

if (sizeof($formandos) == 0) 	\Zage\App\Erro::halt($tr->trans('Formando[s] não encontrado !!!'));

#################################################################################
## Calcular as informações
#################################################################################
$totalformandos			= \Zage\Fmt\Formatura::getNumFormandosAtivos($system->getCodOrganizacao());
$numFormandos			= sizeof($formandos);
$dataConclusao			= $oOrgFmt->getDataConclusao();
if (!$dataConclusao)		\Zage\App\Erro::halt($tr->trans('Data de conclusão da formatura não informada!!!'));

$hoje					= new DateTime('now');
$interval				= $dataConclusao->diff($hoje);
$numMesesConc			= $interval->format('%m');
$diaVencimento			= $oOrgFmt->getDiaVencimento();
$valorPorFormando		= \Zage\App\Util::formataDinheiro($oOrgFmt->getValorPorFormando());
$valorPorBoleto			= \Zage\App\Util::formataDinheiro($oOrgFmt->getValorPorBoleto());
$taxaPorFormando		= \Zage\App\Util::formataDinheiro(\Zage\Adm\Contrato::getValorLicenca($system->getCodOrganizacao()));
$valorTotalFormatura	= \Zage\App\Util::formataDinheiro(\Zage\Fmt\Formatura::getValorTotal($system->getCodOrganizacao()));
$totalPorFormando		= \Zage\App\Util::formataDinheiro(($valorTotalFormatura / $totalformandos));
$valorMensalidade		= \Zage\App\Util::formataDinheiro((($valorTotalFormatura / $numFormandos) / $numMesesConc));

#################################################################################
## Faz o loop nas parcelas para montar a tabela
#################################################################################
for ($i = 0 ;$i < sizeof($formandos); $i++) {

}

if ($valorPorFormando	< 0) 	$valorPorFormando	= 0;
if ($valorPorBoleto		< 0)	$valorPorBoleto		= 0;
if ($taxaPorFormando	< 0)	$taxaPorFormando	= 0;
if (!$diaVencimento)			$diaVencimento		= 5;

#################################################################################
## Montar o select do dia de vencimento
#################################################################################
$oDiaVenc	= "";
for ($i = 1; $i <= 31; $i++) {
	$selected = ($i == $diaVencimento) ? " selected " : "";
	$dia	= str_pad($i, 2, "0",STR_PAD_LEFT);
	if ($i > 28) {
		$dia	.= " * (ou o último dia do mês)";
	}

	$oDiaVenc	.= "<option value='".$i."' $selected>".$dia."</option>";
}

#################################################################################
## Montar o select do número de meses
#################################################################################
$oNumMeses	= "";
for ($i = 1; $i <= $numMesesConc; $i++) {
	if ($i == 1) {
		$selected 	= null;
		$meses		= $i . " Mês";
	}else{
		$meses		= $i . " Meses";
		if ($i == $numMesesConc) {
			$meses 		.= " (todos)";
			$selected 	= "selected";
		}else{
			$selected 	= null;
		}
		
	}
	$oNumMeses	.= "<option value='".$i."' $selected>".$meses."</option>";
}


#################################################################################
## Urls
#################################################################################
$urlVoltar			= ROOT_URL . "/Fin/geraContaLis.php?id=".$id;

#################################################################################
## Carregando o template html
#################################################################################
$tpl	= new \Zage\App\Template();
$tpl->load(\Zage\App\Util::getCaminhoCorrespondente(__FILE__, \Zage\App\ZWS::EXT_HTML));

#################################################################################
## Define os valores das variáveis
#################################################################################
$tpl->set('ID'						,$id);
$tpl->set('TITULO'					,'Geração de Contas');
$tpl->set('URL_VOLTAR'				,$urlVoltar);
$tpl->set('DIAS_VENC'				,$oDiaVenc);
$tpl->set('NUMERO_MESES'			,$oNumMeses);
$tpl->set('NUM_FORMANDOS'			,$numFormandos);
$tpl->set('TOTAL_FORMANDOS'			,$totalformandos);
$tpl->set('NUM_MESES_CONCLUSAO'		,$numMesesConc);
$tpl->set('DATA_CONCLUSAO'			,$dataConclusao->format($system->config["data"]["dateFormat"]));
$tpl->set('VALOR_TOTAL_FORM_FMT'	,\Zage\App\Util::to_money($valorTotalFormatura));
$tpl->set('VALOR_TOTAL_FORMATURA'	,$valorTotalFormatura);
$tpl->set('TOTAL_POR_FORMANDO'		,$totalPorFormando);
$tpl->set('TOTAL_POR_FORMANDO_FMT'	,\Zage\App\Util::to_money($totalPorFormando));
$tpl->set('VALOR_FORMANDO'			,$valorMensalidade);
$tpl->set('DP_MODAL'				,\Zage\App\Util::getCaminhoCorrespondente(__FILE__,\Zage\App\ZWS::EXT_DP,\Zage\App\ZWS::CAMINHO_RELATIVO));

#################################################################################
## Por fim exibir a página HTML
#################################################################################
$tpl->show();

