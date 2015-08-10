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
global $em,$system,$_codMenu_;

#################################################################################
## Resgata a variável ID que está criptografada
#################################################################################
if (isset($_GET['id'])) {
	$id = \Zage\App\Util::antiInjection($_GET["id"]);
}elseif (isset($_POST['id'])) {
	$id = \Zage\App\Util::antiInjection($_POST["id"]);
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
## Verifica os parâmetros
#################################################################################
if (!isset($codOrganizacao)) \Zage\App\Erro::halt('Falta de Parâmetros 2');


#################################################################################
## Resgata as informações do banco
#################################################################################
try {
	$oOrgCer	= $em->getRepository('Entidades\ZgfmtOrganizacaoCerimonial')->findOneBy(array('codOrganizacao' => $codOrganizacao));
	
	if ($oOrgCer)	{
		$codPlano	= ($oOrgCer->getCodPlanoFormatura()) ? $oOrgCer->getCodPlanoFormatura()->getCodigo() : null;
	}else{
		$codPlano	= null;
	}

	
} catch (\Exception $e) {
	\Zage\App\Erro::halt($e->getMessage());
}



#################################################################################
## Resgata as informaões dos planos
#################################################################################
try {
	$aPlanos	= $em->getRepository('Entidades\ZgadmPlano')->findBy(array('codTipoLicenca' => array('F')),array('nome' => 'ASC'));
	$oPlanos	= $system->geraHtmlCombo($aPlanos,'CODIGO', 'NOME', $codPlano, '');

} catch (\Exception $e) {
	\Zage\App\Erro::halt($e->getMessage(),__FILE__,__LINE__);
}

#################################################################################
## Urls
#################################################################################
$urlVoltar			= ROOT_URL . "/Fmt/parceiroLis.php?id=".$id;

#################################################################################
## Carregando o template html
#################################################################################
$tpl	= new \Zage\App\Template();
$tpl->load(\Zage\App\Util::getCaminhoCorrespondente(__FILE__, \Zage\App\ZWS::EXT_HTML));

#################################################################################
## Define os valores das variáveis
#################################################################################
$tpl->set('TITULO'					,"Configuração do Cerimonial");
$tpl->set('ID'						,$id);
$tpl->set('COD_ORGANIZACAO'			,$codOrganizacao);
$tpl->set('COD_PLANO'				,$codPlano);
$tpl->set('PLANOS'					,$oPlanos);
$tpl->set('URL_VOLTAR'				,$urlVoltar);
$tpl->set('DP_MODAL'				,\Zage\App\Util::getCaminhoCorrespondente(__FILE__,\Zage\App\ZWS::EXT_DP,\Zage\App\ZWS::CAMINHO_RELATIVO));


#################################################################################
## Por fim exibir a página HTML
#################################################################################
$tpl->show();
