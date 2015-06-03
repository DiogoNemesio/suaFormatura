<?php
#################################################################################
## Includes
#################################################################################
if (defined('DOC_ROOT')) {
	include_once(DOC_ROOT . 'includeNoAuth.php');
}else{
	include_once('../includeNoAuth.php');
}

if (!isset($_org) && (!isset($_SESSION['_codOrg']))) {
	die ("Organização não definida !!!");
}else{
	
	$system->setCodLang(1);
	
	if (isset($_org) && ($_org instanceof \Entidades\ZgadmOrganizacao) ) {
		$system->setCodOrganizacao($_org->getCodigo());
	}else{
		$system->setCodOrganizacao($_SESSION['_codOrg']);
	}
	
	/** Define a organização **/
	$db->setOrganizacao($system->getCodOrganizacao());
	
	$log->debug("Código da Organização: ".$system->getCodOrganizacao());
	
}
include_once(BIN_PATH . 'auth.php');

//\Doctrine\Common\Util\Debug::dump($_user);
//echo "LoggedUser: ".$db->getLoggedUser()."<BR>";



#################################################################################
## Resgata a variável ID que está criptografada
#################################################################################
if (isset($_GET['id'])) {
	$id = \Zage\App\Util::antiInjection($_GET["id"]);
}elseif (isset($_POST['id'])) {
	$id = \Zage\App\Util::antiInjection($_POST["id"]);
}else{
	$id	= null;
}

#################################################################################
## Descompacta o ID
#################################################################################
\Zage\App\Util::descompactaId($id);

#################################################################################
## Descobre o módulo que será iniciado
#################################################################################
/*if (isset($_codModulo_)) {
	$system->selecionaModulo($_codModulo_);
	$codModulo	= $_codModulo_;
}elseif ($_user->getUltModuloAcesso()) {
	$codModulo	= $_user->getUltModuloAcesso()->getCodigo();
	$urlInicial	= "#";
}else{
	$codModulo	= null;
	$urlInicial	= ROOT_URL . "/App/modulo.php?id=";
}*/

#################################################################################
## Verifica se o módulo tem o Dashboard
#################################################################################
/*if (isset($_mod) && is_object($_mod)) {
	if (file_exists(MOD_PATH . "/".$_mod->getApelido().'/php/dashboard.php')) {
		$urlInicial	= ROOT_URL . "/" . $_mod->getApelido()."/dashboard.php?id=".$id;
	}else{
		$urlInicial	= "#";
	}
}else{
	$urlInicial	= "#";
}*/


#################################################################################
## Verificar se é para trocar senha do usuário
#################################################################################
$urlTrocaSenha		= ROOT_URL . "/App/alteraSenha.php?id=".$id;

if ((isset($_user)) && ($_user->getIndTrocarSenha() == 1)) {
	$urlInicial			= $urlTrocaSenha;
	$indTrocarSenha		= 1;
}else{
	$indTrocarSenha		= 0;
}



#################################################################################
## Define a constante do site inicial
#################################################################################
$system->setHomeUrl($_SERVER['REQUEST_URI']);

#################################################################################
## Define o nome do iframe Central
#################################################################################
$system->setDivCentral("zgDivCentral");

#################################################################################
## Cria o objeto do Menu
#################################################################################
$menu	= \Zage\App\Menu::criar(\Zage\App\Menu\Tipo::TIPO1);
$menu->setTarget($system->getDivCentral());

#################################################################################
## Carrega os menus fixos
#################################################################################
$menus 	= $em->getRepository('Entidades\ZgappMenu')->findBy(array('indFixo' => '1'));

#################################################################################
## Adiciona os menus fixos
#################################################################################
foreach ($menus as $dados) {
	$url 	= \Zage\App\Menu\Tipo::montaUrlCompleta($dados->getCodigo());
	$menu->adicionaLinkFixo($dados->getCodigo(), $dados->getNome(), $dados->getIcone(), $url, $dados->getDescricao());
}

#################################################################################
## Carrega os menus do módulo
#################################################################################
$menus 	= \Zage\Seg\Usuario::listaMenusAcesso($_user->getCodigo());

#################################################################################
## Adiciona os menus no objeto
#################################################################################
if ($menus) {
	foreach ($menus as $dados) {
		if ($dados->getCodTipo()->getCodigo() == "M") {
			$codMenuPai	= $dados->getCodMenuPai() ? $dados->getCodMenuPai()->getCodigo() : null;  
			$menu->adicionaPasta($dados->getCodigo(), $dados->getNome(), $dados->getIcone(),$codMenuPai);
		}elseif ($dados->getCodTipo()->getCodigo() == "L") {
			$url 	= \Zage\App\Menu\Tipo::montaUrlCompleta($dados->getCodigo());
			$codMenuPai	= $dados->getCodMenuPai() ? $dados->getCodMenuPai()->getCodigo() : null;
			$menu->adicionaLink($dados->getCodigo(), $dados->getNome(), $dados->getIcone(), $url, $dados->getDescricao(),$codMenuPai);
		}else{
			die('Tipo de Menu desconhecido');
		}
	}
}

#################################################################################
## Gera o código javascript das máscaras
#################################################################################
$mascaras	= $em->getRepository('Entidades\ZgappMascara')->findAll();
$htmlMask		= "";
for ($i = 0; $i < sizeof($mascaras); $i++) {
	if ($mascaras[$i]->getIndReversa() == 1) {
		$reverse	= ",reverse: true";
	}else{
		$reverse	= "";
	}
	
	if ($mascaras[$i]->getIndTamanhoFixo() === 0) {
		$maxLen	= ",maxlength: false";
	}else{
		$maxLen	= "";
	}
	
	$htmlMask	.= "'".strtolower($mascaras[$i]->getNome())."': { mascara: '".$mascaras[$i]->getMascara()."' $reverse $maxLen},";
}
$htmlMask = substr($htmlMask, 0 , -1);


#################################################################################
## Carregando o template html
#################################################################################
$tpl	= new \Zage\App\Template();
$tpl->load(HTML_PATH . "/index.html");

#################################################################################
## Define os valores das variáveis
#################################################################################
$tpl->set('MENU_CODE'			,$menu->getHtml());
$tpl->set('URL_FORM'			,$_SERVER['REQUEST_URI']);
$tpl->set('DIVCENTRAL'			,$system->getDivCentral());
$tpl->set('URLINICIAL'			,$urlInicial);
$tpl->set('IND_TROCAR_SENHA'	,$indTrocarSenha);
$tpl->set('TROCA_SENHA_URL'		,$urlTrocaSenha);
$tpl->set('MASCARAS'			,$htmlMask);
$tpl->set('TITULO'				,$titulo);

#################################################################################
## Por fim exibir a página HTML
#################################################################################
$tpl->show();

?>