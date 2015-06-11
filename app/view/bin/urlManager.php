<?php
#################################################################################
## Includes
#################################################################################
if (defined('DOC_ROOT')) {
	include_once(DOC_ROOT . 'includeNoAuth.php');
}else{
	include_once('../includeNoAuth.php');
}

#################################################################################
## Verifica se a url foi passada
#################################################################################
if (isset($_GET['url'])) {
	$url = \Zage\App\Util::antiInjection($_GET["url"]);
}else{
	include(BIN_PATH . 'notFoundExt.php');
	exit;
}

#################################################################################
## Formata a URL
#################################################################################
if (!$url) {
	include_once (DOC_ROOT . '/view/index.php');
}else{
	
	#################################################################################
	## Tentar detectar se a Url é a identificação de uma Organização
	#################################################################################
	if (substr($url,-1) == "/") $url	= substr($url,0,-1);
	$pieces 	= explode("/", $url);
	
	if (sizeof($pieces) == 1) {
		$_ident			= $pieces[0];
		//$log->debug("Ident: ".$_ident);
		
		$ret			= \Zage\Fmt\Organizacao::buscaPorIdentificacao($_ident);
		
		if ($ret && sizeof($ret) > 0) {
			$_org		= $ret[0];
		}
		
		if (isset($_org)) {
			$log->debug("Organização encontrada: ".$_org->getNome());
			include(DOC_ROOT . '/view/index.php');
			exit;
		}else{
			$modulo	= ucfirst($pieces[0]);
		}
	}else{
		$modulo	= ucfirst($pieces[0]);
	}
	
	
	if (!isset($pieces[1])) {
		$script	= null;
		$modulo	= null;
	}else{
		$script	= $pieces[1];
		
		if ($modulo == "Doc") {
			if ($script == "View" && isset($pieces[2])) {
				$fileId		= $pieces[2];
				$script 	= "docView.php";
			}elseif ($script == "Down" && isset($pieces[2])) {
				$fileId		= $pieces[2];
				$script 	= "docDown.php";
			}
		}
		
	}
	
	if (strlen($modulo) != 3) {
		include(BIN_PATH . 'notFoundExt.php');
		exit;
	}else{
		$aScr	= explode("?", $script);
		
		if (sizeof($aScr) > 1) {
			$script	= $aScr[0];
		} 
		
		if (file_exists(MOD_PATH . "/$modulo/php/$script")) {
			include_once MOD_PATH . "/$modulo/php/$script";
			exit;
		}elseif (file_exists(MOD_PATH . "/$modulo/dp/$script")) {
			include_once MOD_PATH . "/$modulo/dp/$script";
			exit;
		}else{
			$log->debug("Url não encontrada ($url)");
			include(BIN_PATH . 'notFoundExt.php');
			exit;
		}
		
	}
	
	
}

?>