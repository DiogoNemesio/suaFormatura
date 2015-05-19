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
## Resgata as informações do banco
#################################################################################
if ($codTipo) {
	try {
		$info = $em->getRepository('Entidades\ZgdocDispositivoArmTipo')->findOneBy(array('codOrganizacao' => $system->getCodOrganizacao(), 'codigo' => $codTipo));
		
	} catch (\Exception $e) {
		\Zage\App\Erro::halt($e->getMessage());
	}

	$nome			= $info->getNome();
	$sigla			= $info->getSigla();
	$qtdeMax 		= $info->getQtdeMax();
	$ativo			= ($info->getIndAtivo()			== 1) ? "checked" : null;
	
}else{
	$nome			= '';
	$sigla			= '';
	$qtdeMax		= '';
	$ativo			= 'checked';
}

#################################################################################
## Url Voltar
#################################################################################
$urlVoltar			= ROOT_URL."/Doc/dispTipoLis.php?id=".$id;

#################################################################################
## Url Novo
#################################################################################
$uid = \Zage\App\Util::encodeUrl('_codMenu_='.$_codMenu_.'&_icone_='.$_icone_.'&codTipo=');
$urlNovo			= ROOT_URL."/Doc/dispTipoAlt.php?id=".$uid;

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
$tpl->set('COD_TIPO'			,$codTipo);
$tpl->set('NOME'				,$nome);
$tpl->set('SIGLA'				,$sigla);
$tpl->set('QTDE_MAX'			,$qtdeMax);
$tpl->set('ATIVO'				,$ativo);
$tpl->set('DP'					,\Zage\App\Util::getCaminhoCorrespondente(__FILE__,\Zage\App\ZWS::EXT_DP,\Zage\App\ZWS::CAMINHO_RELATIVO));


#################################################################################
## Por fim exibir a página HTML
#################################################################################
$tpl->show();

