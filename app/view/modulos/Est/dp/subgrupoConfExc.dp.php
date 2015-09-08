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
if (isset($_POST['codConf'])) 		$codConf		= \Zage\App\Util::antiInjection($_POST['codConf']);

#################################################################################
## Limpar a variável de erro
#################################################################################
$err	= false;

#################################################################################
## Verificar se a pasta existe e excluir
#################################################################################
try {

	if (!isset($codConf) || (!$codConf)) {
		die('1'.\Zage\App\Util::encodeUrl('||'.htmlentities($tr->trans('Parâmetro %s não informado',array('%s' => "codConf")))));
	}
	
	$oConf	= $em->getRepository('Entidades\ZgestSubgrupoConf')->findOneBy(array('codigo' => $codConf));
	
	/** Verificar se existe a configuração **/
	if (!$oConf) {
		die('1'.\Zage\App\Util::encodeUrl('||'.htmlentities($tr->trans('A configuração não foi encontrada!'))));
	}
	
	/** Verificar se a configuração já esta sendo usada **/
	$oProdutoConfValor	= $em->getRepository('Entidades\ZgestProdutoSubgrupoValor')->findBy(array('codSubgrupoConf' => $codConf));
	
	if ($oProdutoConfValor) {
		die('1'.\Zage\App\Util::encodeUrl('||'.htmlentities($tr->trans('Esta configuração está em uso e não pode ser excluída!'))));
	}
	
	/** Remover valores da LISTA DE VALORES **/
	$oSubgrupoConfValor	= $em->getRepository('Entidades\ZgestSubgrupoConfValor')->findBy(array('codSubgrupoConf' => $codConf));
	
	for ($i = 0; $i < sizeof($oSubgrupoConfValor); $i++){
		$em->remove($oSubgrupoConfValor[$i]);
	}
	
	/** Remover configuração **/
	$em->remove($oConf);
	$em->flush();

} catch (\Exception $e) {
	echo '1'.\Zage\App\Util::encodeUrl('||'.htmlentities($e->getMessage()));
	exit;
}

echo '0'.\Zage\App\Util::encodeUrl('|'.$oConf->getCodigo().'|'.htmlentities($tr->trans("Configuração excluída com sucesso")));
