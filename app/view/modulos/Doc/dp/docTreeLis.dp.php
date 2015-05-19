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
## Resgata os parâmetros passados pelo formulario
#################################################################################
if (isset($_POST['codPasta'])) 		$codPasta		= \Zage\App\Util::antiInjection($_POST['codPasta']);


if (!isset($codPasta) || $codPasta == 0) $codPasta	= null;


#################################################################################
## Montar os dados da árvore
#################################################################################
$result 	= array();
$data 		= array();


#################################################################################
## Resgata os dados da árvore
#################################################################################
try {
	$pastas		= \Zage\Doc\Pasta::lista($codPasta);
	if ($codPasta !== null){
		$tipos		= \Zage\Doc\DocumentoTipo::lista($codPasta);
	}else{
		$tipos		= array();
	}
} catch(\Exception $e) {
	$result['status'] = 'ERR';
	$result['message'] = $e->getMessage();
	exit;
}

if ($pastas) {
	foreach ($pastas as $pasta) {
		$opcoes	= 	"<span id=\"spanPasta_".$pasta->getCodigo()."\" data-rel=\"zgPasta\">".$pasta->getNome()."</span>";
		
		$item = array(
			'name' 						=> $opcoes,
			'type' 						=> 'folder',
			'icon-class' 				=> 'blue',
			'additionalParameters' 		=>  array('codPasta' => $pasta->getCodigo())
		);
		$item['additionalParameters']['children'] = true;
		$data["P".$pasta->getCodigo()] = $item;
	}
}	

if ($tipos) {
	foreach ($tipos as $tipo) {
		$opcoes	= 	"<span id=\"spanItemPasta_".$tipo->getCodigo()."\" data-rel=\"zgItemPasta\">".$tipo->getNome()."</span>";
		
		$item = array(
			'name' 						=> $opcoes,
			'type' 						=> 'item',
			'additionalParameters' 		=>  array('codTipo' => $tipo->getCodigo(),'codPasta' => $tipo->getCodPasta()->getCodigo())
		);
		$item['additionalParameters']['children'] = false;
		$data["I".$tipo->getCodigo()] = $item;
	}
}


$result['status'] = 'OK';
$result['data'] = $data;
	
echo json_encode($result);