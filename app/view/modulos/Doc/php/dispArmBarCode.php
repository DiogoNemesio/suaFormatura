<?php 
#################################################################################
## Includes
#################################################################################
if (defined('DOC_ROOT')) {
	include_once(DOC_ROOT . 'include.php');
}else{
	include_once('../include.php');
}

require_once(PKG_PATH . '/barcode/class/BCGFontFile.php');
require_once(PKG_PATH . '/barcode/class/BCGColor.php');
require_once(PKG_PATH . '/barcode/class/BCGDrawing.php');
require_once(PKG_PATH . '/barcode/class/BCGcode128.barcode.php');
require_once(PKG_PATH . '/barcode/class/BCGcode39.barcode.php');


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
if ($codDisp) {
	try {
		$info = $em->getRepository('Entidades\ZgdocDispositivoArm')->findOneBy(array('codEmpresa' => $system->getCodEmpresa(), 'codigo' => $codDisp));
	} catch (\Exception $e) {
		\Zage\App\Erro::halt($e->getMessage());
	}
	
	if (!$info) {
		\Zage\App\Erro::halt($tr->trans('Dispositivo não encontrado !!!'));
	}

	$ident			= $info->getIdentificacao();
	
	/*
	 * 
	 * 
	 * 
	 * 
	 * CRIAR UMA CLASSE PARA SUBSTITUIR O CÓDIGO ABAIXO
	 * 
	 * 
	 * 
	 * 
	 * 
	 * 
	 * 
	 * 
	 * 
	 * 
	 */
	
	header('Content-disposition: attachment; filename="barCode_'.$info->getIdentificacao().'.png"');
	header('Content-Type: image/png');
	
	
	$colorFont		= new BCGColor(0, 0, 0);
	$colorBack 		= new BCGColor(255, 255, 255);
	$font 			= new BCGFontFile(PKG_PATH . '/barcode/font/Arial.ttf', 18);
	
	$code 			= new BCGcode39(); // Or another class name from the manual
	$code->setScale(2); // Resolution
	$code->setThickness(40); // Thickness
	$code->setForegroundColor($colorFont); // Color of bars
	$code->setBackgroundColor($colorBack); // Color of spaces
	$code->setFont($font); // Font (or 0)
	$code->parse($ident); // Text
	
	$drawing 		= new BCGDrawing('', $colorBack);
	$drawing->setBarcode($code);
	$drawing->draw();
	
	
	$drawing->finish(BCGDrawing::IMG_FORMAT_PNG);
	
}else{
	\Zage\App\Erro::halt($tr->trans('Falta de parâmetros'));
}




?>