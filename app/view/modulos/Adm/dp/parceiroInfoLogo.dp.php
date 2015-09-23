<?php
#################################################################################
## Includes
#################################################################################
if (defined('DOC_ROOT')) {
	include_once(DOC_ROOT . 'include.php');
}else{
 	include_once('../include.php');
}

global $log,$em,$system,$tr;

#################################################################################
## Resgata os parâmetros passados pelo formulario
#################################################################################
$codOrganizacao 	= $system->getCodOrganizacao();
$logoNome 			= $_FILES['logomarca']["name"];
$logoTipo 			= $_FILES['logomarca']["type"];
$tempLoc 			= $_FILES['logomarca']["tmp_name"];
//$logoTamanho		= $_FILES['logomarca']["size"];


#################################################################################
## Verificar se foi via Ajax ou iframe
#################################################################################
$ajax 	= isset($_SERVER['HTTP_X_REQUESTED_WITH']) && $_SERVER['HTTP_X_REQUESTED_WITH']==='XMLHttpRequest';

#################################################################################
## Criar a variável de retorno
#################################################################################
$result	= array();

#################################################################################
## Fazer validação dos campos
#################################################################################
if (!$tempLoc) {
	$log->err($tr->trans('Erro, variável logomarca não informada !!!'));
	$result['status'] 	= 'ERR';
	$result['message']	= $tr->trans('Erro, variável logomarca não informada !!!');
	returnLogoFunc($result);
}

if (!is_uploaded_file($tempLoc)) {
	$log->err($tr->trans('Arquivo não pode ser salvo, pois não foi transferido através de uma requisição POST HTTP'));
	$result['status'] 	= 'ERR';
	$result['message']	= $tr->trans('Arquivo não pode ser salvo, pois não foi transferido através de uma requisição POST HTTP');
	returnLogoFunc($result);
}

/** Verifica se o arquivo existe e pode ser lido **/
if (!file_exists($tempLoc) || !is_readable($tempLoc)) {
	$log->err($tr->trans("Arquivo %s não existe ou não pode ser lido",array("%s" => $tempLoc)));
	$result['status'] 	= 'ERR';
	$result['message']	= $tr->trans("Arquivo %s não existe ou não pode ser lido",array("%s" => $tempLoc));
	returnLogoFunc($result);
}


#################################################################################
## Salvar no banco
#################################################################################
try {
	#################################################################################
	## Resgata os objetos (chave estrangeiras)
	#################################################################################
	$oOrganizacao	= $em->getRepository('\Entidades\ZgadmOrganizacao')->findOneBy(array('codigo' => $codOrganizacao));

	#################################################################################
	## Redimensionar a logomarca
	#################################################################################
	$logoExt 		= pathinfo($logoNome, PATHINFO_EXTENSION);
	$logoPath		= $tempLoc."_thumb.".$logoExt;
	move_uploaded_file($tempLoc , $logoPath);
	$ret			= resizeLogo($logoPath,$logoPath,280);
	
	#################################################################################
	## LogoMarca
	#################################################################################
	$tamArq			= filesize($logoPath);
	$data 			= fread(fopen($logoPath, 'r'), $tamArq);

	#################################################################################
	## Verifica se a logo já existe
	#################################################################################
	$oLogo	= $em->getRepository('\Entidades\ZgadmOrganizacaoLogo')->findOneBy(array('codOrganizacao' => $codOrganizacao));
	if (!$oLogo)	{
		$oLogo	= new \Entidades\ZgadmOrganizacaoLogo();
		$oLogo->setCodOrganizacao($oOrganizacao);
	}
		
	$oLogo->setLogomarca($data);
	$oLogo->setMimetype($logoTipo);
	$oLogo->setTamanho($tamArq);
	$oLogo->setNome($logoNome);
	$em->persist($oLogo);
			
	#################################################################################
	## Flush
	#################################################################################
	$em->flush();
	$em->clear();
	
	
	$result['status'] 	= 'OK';
	$result['message']	= $tr->trans('Logomarca alterada com sucesso');
	$result['url']		= ROOT_URL . "/Adm/mostraLogomarca.php?logo=".rand(1,500000);
	returnLogoFunc($result);
	
} catch (\Exception $e) {
	$result['status'] 	= 'ERR';
	$result['message']	= htmlentities($e->getMessage());
	returnLogoFunc($result);
}
 


function returnLogoFunc($res) {
	global $ajax;
	
	$result = json_encode($res);
	
	if ($ajax) {
		//if request was ajax(modern browser), just echo it back
		echo $result;
	}
	else {
		//if request was from an older browser not supporting ajax upload
		//then we have used an iframe instead and the response is sent back to the iframe as a script
		echo '<script language="javascript" type="text/javascript">';
		echo 'window.top.window.jQuery("#'.$_POST['temporary-iframe-id'].'").data("deferrer").resolve('.$result.');';
		echo '</script>';
	}
	exit;
}


function resizeLogo($in_file, $out_file, $new_width, $new_height=FALSE)
{
	$image = null;
	$extension = strtolower(preg_replace('/^.*\./', '', $in_file));
	switch($extension)
	{
		case 'jpg':
		case 'jpeg':
			$image = imagecreatefromjpeg($in_file);
			break;
		case 'png':
			$image = imagecreatefrompng($in_file);
			break;
		case 'gif':
			$image = imagecreatefromgif($in_file);
			break;
	}
	if(!$image || !is_resource($image)) return false;


	$width = imagesx($image);
	$height = imagesy($image);
	if($new_height === FALSE)
	{
		$new_height = (int)(($height * $new_width) / $width);
	}


	$new_image = imagecreatetruecolor($new_width, $new_height);
	imagecopyresampled($new_image, $image, 0, 0, 0, 0, $new_width, $new_height, $width, $height);

	$ret = imagejpeg($new_image, $out_file, 80);

	imagedestroy($new_image);
	imagedestroy($image);

	return $ret;
}