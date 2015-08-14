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
$formandos		= $em->getRepository('Entidades\ZgsegUsuario')->findBy(array('codigo' => $aSelFormandos));

if (sizeof($formandos) == 0) 	\Zage\App\Erro::halt($tr->trans('Formando[s] não encontrado !!!'));

#################################################################################
## Inicia o html da tabela
#################################################################################
$htmlTab	= '';

#################################################################################
## Faz o loop nas parcelas para montar a tabela
#################################################################################
for ($i = 0 ;$i < sizeof($formandos); $i++) {

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
$tpl->set('ID'					,$id);
$tpl->set('TITULO'				,'Geração de Contas');
$tpl->set('URL_VOLTAR'			,$urlVoltar);
$tpl->set('DP_MODAL'			,\Zage\App\Util::getCaminhoCorrespondente(__FILE__,\Zage\App\ZWS::EXT_DP,\Zage\App\ZWS::CAMINHO_RELATIVO));

#################################################################################
## Por fim exibir a página HTML
#################################################################################
$tpl->show();

