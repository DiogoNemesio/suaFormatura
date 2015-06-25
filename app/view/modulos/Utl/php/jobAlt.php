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
if (!isset($codJob)) \Zage\App\Erro::halt('Falta de Parâmetros 2');

#################################################################################
## Resgata as informações do banco
#################################################################################
if (!empty($codJob)) {
	try {
		$info = $em->getRepository('Entidades\ZgutlJob')->findOneBy(array('codigo' => $codJob));
	} catch (\Exception $e) {
		\Zage\App\Erro::halt($e->getMessage());
	}

	$codAtividade	= ($info->getCodAtividade() != null) ? $info->getCodAtividade()->getCodigo()	: null;
	$codModulo		= ($info->getCodModulo() 	!= null) ? $info->getCodModulo()->getCodigo() 		: null;
	$comando		= $info->getComando();
	$indAtivo		= $info->getIndAtivo();
	$dataUltExe		= ($info->getDataUltimaExecucao() != null)	? $info->getDataUltimaExecucao()->format($system->config["data"]["datetimeSimplesFormat"])	: null;
	$dataPrxExe		= ($info->getDataProximaExecucao() != null) ? $info->getDataProximaExecucao()->format($system->config["data"]["datetimeSimplesFormat"]) : null;
	$ano			= $info->getAno();
	$mes			= $info->getMes();
	$dia			= $info->getDia();
	$hora			= $info->getHora();
	$minuto			= $info->getMinuto();
	$segundo		= $info->getSegundo();

}else{
	$codAtividade	= null;
	$codModulo		= null;
	$comando		= null;
	$indAtivo		= null;
	$dataUltExe		= null;
	$dataPrxExe		= null;
	$ano			= null;
	$mes			= null;
	$dia			= null;
	$hora			= null;
	$minuto			= null;
	$segundo		= null;
}


################################################################################
# Select da atividade
################################################################################
try {
	$aAtividade = $em->getRepository('Entidades\ZgutlAtividade')->findBy(array(),array('identificacao' => 'ASC'));
	$oAtividade = $system->geraHtmlCombo($aAtividade, 'codigo', 'identificacao', $codAtividade, '');
} catch (\Exception $e) {
	\Zage\App\Erro::halt($e->getMessage(),__FILE__,__LINE__);
}


################################################################################
# Select do módulo
################################################################################
try {
	$aModulo = $em->getRepository('Entidades\ZgappModulo')->findBy(array(),array('nome' => 'ASC'));
	$oModulo = $system->geraHtmlCombo($aModulo, 'codigo', 'nome', $codModulo, '');
} catch (\Exception $e) {
	\Zage\App\Erro::halt($e->getMessage(),__FILE__,__LINE__);
}


################################################################################
# Select do Mês
################################################################################
try {
	$aMeses	= array();
	for ($i = 1; $i <= 12; $i++) {
		$aMeses[$i]["CODIGO"]	= str_pad($i, 2, "0", STR_PAD_LEFT);
		$aMeses[$i]["VALOR"]	= \Zage\App\Util::mesPorExtenso($aMeses[$i]["CODIGO"]);
	}
	$aMeses[$i]["CODIGO"]	= "*";
	$aMeses[$i]["VALOR"]	= $tr->trans("Todos os meses");
	
	$oModulo = $system->geraHtmlCombo($aMeses, 'CODIGO', 'VALOR', $mes, '');
} catch (\Exception $e) {
	\Zage\App\Erro::halt($e->getMessage(),__FILE__,__LINE__);
}


#################################################################################
## Url Voltar
#################################################################################
$urlVoltar			= ROOT_URL."/Utl/jobLis.php?id=".$id;

#################################################################################
## Url Novo
#################################################################################
$uid 				= \Zage\App\Util::encodeUrl('_codMenu_='.$_codMenu_.'&_icone_='.$_icone_.'&codJob=');
$urlNovo			= ROOT_URL."/Utl/jobAlt.php?id=".$uid;

#################################################################################
## Carregando o template html
#################################################################################
$tpl	= new \Zage\App\Template();
$tpl->load(\Zage\App\Util::getCaminhoCorrespondente(__FILE__, \Zage\App\ZWS::EXT_HTML));

#################################################################################
## Define os valores das variáveis
#################################################################################
$tpl->set('URL_FORM'			,$_SERVER['SCRIPT_NAME']);
$tpl->set('URLVOLTAR'			,$urlVoltar);
$tpl->set('URLNOVO'				,$urlNovo);
$tpl->set('ID'					,$id);
$tpl->set('COD_JOB'				,$codJob);
$tpl->set('COD_ATIVIDADE'		,$codAtividade);
$tpl->set('COD_MODULO'			,$codModulo);
$tpl->set('COMANDO'				,$comando);
$tpl->set('IND_ATIVO'			,$indAtivo);
$tpl->set('INTERVALO'			,$intervalo);
$tpl->set('DATA_ULT_EXE'		,$dataUltExe);
$tpl->set('DATA_PRX_EXE'		,$dataPrxExe);
$tpl->set('ATIVIDADES'			,$oAtividade);
$tpl->set('MODULOS'				,$oModulo);
$tpl->set('APP_BS_TA_MINLENGTH'	,\Zage\Adm\Parametro::getValor('APP_BS_TA_MINLENGTH'));
$tpl->set('APP_BS_TA_ITENS'		,\Zage\Adm\Parametro::getValor('APP_BS_TA_ITENS'));
$tpl->set('APP_BS_TA_TIMEOUT'	,\Zage\Adm\Parametro::getValor('APP_BS_TA_TIMEOUT'));
$tpl->set('DP'					,\Zage\App\Util::getCaminhoCorrespondente(__FILE__,\Zage\App\ZWS::EXT_DP,\Zage\App\ZWS::CAMINHO_RELATIVO));

#################################################################################
## Por fim exibir a página HTML
#################################################################################
$tpl->show();

