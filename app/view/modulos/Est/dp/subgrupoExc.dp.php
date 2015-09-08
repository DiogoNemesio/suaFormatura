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
if (isset($_POST['codSubgrupo'])) 		$codSubgrupo		= \Zage\App\Util::antiInjection($_POST['codSubgrupo']);

#################################################################################
## Limpar a variável de erro
#################################################################################
$err	= false;

#################################################################################
## Verificar se a pasta existe e excluir
#################################################################################
try {

	if (!isset($codSubgrupo) || (!$codSubgrupo)) {
		die('1'.\Zage\App\Util::encodeUrl('||'.htmlentities($tr->trans('Parâmetro COD_SUBGRUPO não informado'))));
	}
	
	$info	= $em->getRepository('Entidades\ZgestSubgrupo')->findOneBy(array('codigo' => $codSubgrupo));

	/** Verificar se o subgrupo existe **/
	if (!$info) {
		die('1'.\Zage\App\Util::encodeUrl('||'.htmlentities($tr->trans('Subgrupo (categoria) não encontrado'))));
	}
	
	/** Verificar se existe alguma configuração para a subgrupo **/
	$oConf		= $em->getRepository('Entidades\ZgestSubgrupoConf')->findBy(array('codSubgrupo' => $codSubgrupo));
	if ($oConf) {
		die('1'.\Zage\App\Util::encodeUrl('||'.htmlentities($tr->trans('Este subgrupo (categoria) não pode ser excluído pois existe alguma configuração vinculada.'))));
	}
	
	/** Remover associação com tipo de organização **/
	$oSubgrupoOrg = $em->getRepository('Entidades\ZgestSubgrupoOrg')->findBy(array('codSubgrupo' => $codSubgrupo));
	
	for ($i = 0; $i < sizeof($oSubgrupoOrg); $i++){
		$em->remove($oSubgrupoOrg[$i]);
	}
	
	/** Remover subgrupo **/
	$em->remove($info);
	$em->flush();

} catch (\Exception $e) {
	echo '1'.\Zage\App\Util::encodeUrl('||'.htmlentities($e->getMessage()));
	exit;
}

echo '0'.\Zage\App\Util::encodeUrl('|'.$info->getCodigo().'|'.htmlentities($tr->trans("Subgrupo excluído com sucesso")));
