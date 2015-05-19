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
## Verifica se o usuário está autenticado
#################################################################################
include_once(BIN_PATH . 'auth.php');

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
## Verifica se o módulo foi passado
#################################################################################
if (!isset($_codModulo_)) {
	\Zage\App\Erro::halt('Falta de Parâmetros 2');
}

#################################################################################
## Resgata os módulos que o usuário tem acesso
#################################################################################
$menus	= \Zage\Seg\Usuario::listaMenusAcesso($system->getCodUsuario(),$system->getCodEmpresa(),$_codModulo_);

$html		= "";
$n			= 1;
$maxRows	= 10;
for ($i = 0; $i < sizeof($menus); $i++) {
	if ($n == 1) $html	.= '<div class="row"><div class="col-xs-12"><p>';
	
	$url	= \Zage\App\Menu\Tipo::montaUrl($menus[$i]->getLink(),$menus[$i]->getCodigo());
	$mId	= \Zage\App\Menu\Tipo::geraId($url,$menus[$i]->getCodigo());
	if ($menus[$i]->getCodModulo()) {
		$classe	= $menus[$i]->getCodModulo()->getClasseCss(); 
	}else{
		$class 	= "btn-info";
	}
	
	$html	.= '<a href="javascript:zgLoadMenu(\''.$url.'\',\''.$mId.'\');" class="btn btn-app '.$classe.'"><i class="'.$menus[$i]->getIcone().' fa-2x"></i><br>'.$menus[$i]->getNome().'</a>';
	
	if ($n == $maxRows) {
		$n = 1;
		$html	.= '</p></div></div>';
	}
	$n++;
}

if ($n !== 1) {
	$html	.= '</p></div></div>';
}

#################################################################################
## Carregando o template html
#################################################################################
$tpl	= new \Zage\App\Template();
$tpl->load(\Zage\App\Util::getCaminhoCorrespondente(__FILE__, \Zage\App\ZWS::EXT_HTML));

#################################################################################
## Define os valores das variáveis
#################################################################################
$tpl->set('HTML'	,$html);


#################################################################################
## Por fim exibir a página HTML
#################################################################################
$tpl->show();
