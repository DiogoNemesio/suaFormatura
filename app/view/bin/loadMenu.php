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
## Resgata as informaçoes do menu
#################################################################################
$info	= $em->getRepository('Entidades\ZgappMenu')->find($_codMenu_);
if (!$info) \Zage\App\Erro::halt('Menu não encontrado !!!');

#################################################################################
## Verifica se o usuário tem permissão no menu
#################################################################################
if ($info->getIndFixo() == 0) $system->checaPermissao($_codMenu_);

#################################################################################
## Verifica se o script existe
#################################################################################
if ($info->getCodModulo()) {
	$modulo	= $info->getCodModulo()->getApelido();
}else{
	$modulo	= "";
}

if ($info->getLink()) {
	$script	= $info->getLink();
}else{
	$script	= "";
}

if ($info->getIndSistema() == '0' && $modulo !== "Ext") {
	die('Tentativa de violação de segurança (código SEG01) ');
}

if (strlen($modulo) != 3 || !$script) {
	include(BIN_PATH . 'notFound.php');
	exit;
}else{
	
	$aScr	= explode("?", $script);
	
	if (sizeof($aScr) > 1) {
		$script	= $aScr[0];
		//$log->debug("Script 2: ".$aScr[1]);
	}
	
	if (file_exists(MOD_PATH . "/$modulo/php/$script")) {
		#################################################################################
		## Atualiza o histórico do acesso do menu
		#################################################################################
		$hist	= $em->getRepository('Entidades\ZgappMenuHistAcesso')->findOneBy(array('codMenu' => $_codMenu_,'codUsuario' => $system->getCodUsuario()));
		if (!$hist) {
			$hist = new \Entidades\ZgappMenuHistAcesso();
			$hist->setCodMenu($info);
			$hist->setCodUsuario($_user);
			$hist->setDataUltAcesso(new \DateTime('now'));
			$hist->setQuantidade(1);
		}else{
			$hist->setQuantidade($hist->getQuantidade() + 1);
			$hist->setDataUltAcesso(new \DateTime('now'));
		}
		$em->persist($hist);
		$em->flush();
		$em->detach($hist);
		
		include_once MOD_PATH . "/$modulo/php/$script";
		exit;
	}else{
		include(BIN_PATH . 'notFound.php');
		exit;
	}

}


