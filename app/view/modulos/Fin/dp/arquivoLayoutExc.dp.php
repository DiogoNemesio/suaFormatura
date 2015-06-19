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
if (isset($_POST['codLayout'])) 		$codLayout		= \Zage\App\Util::antiInjection($_POST['codLayout']);

#################################################################################
## Limpar a variável de erro
#################################################################################
$err	= false;

#################################################################################
## Verificar se o Layout existe e excluir
#################################################################################
try {

	if (!isset($codLayout) || (!$codLayout)) {
		$system->criaAviso(\Zage\App\Aviso\Tipo::ERRO,$tr->trans('Parâmetro não informado'));
		die('1'.\Zage\App\Util::encodeUrl('||'));
	}
	
	$oLayout	= $em->getRepository('Entidades\ZgfinArquivoLayout')->findOneBy(array('codigo' => $codLayout));
	
	if (!$oLayout) {
		$system->criaAviso(\Zage\App\Aviso\Tipo::ERRO,$tr->trans('Layout não encontrado'));
		die('1'.\Zage\App\Util::encodeUrl('||'));
	}

	
	#################################################################################
	## Verificar se o layout está em uso
	#################################################################################
	$layout			= $em->getRepository('Entidades\ZgfinArquivoLayoutRegistro')->findOneBy(array('codLayout' => $codLayout));
	
	if ($layout)	{
		$system->criaAviso(\Zage\App\Aviso\Tipo::ERRO,$tr->trans('Layout "%s" está em uso e não pode ser excluído (REGISTRO DE LAYOUT)',array('%s' => $info->getNome())));
		die('1'.\Zage\App\Util::encodeUrl('||'));
	}
	
	
	#################################################################################
	## Excluir as carteiras que estão associadas ao layout
	#################################################################################
	$carteiras		= $em->getRepository('Entidades\ZgfinArquivoLayoutCarteira')->findOneBy(array('codLayout' => $codLayout));
	for ($i = 0; $i < sizeof($carteiras); $i++) {
		$em->remove($carteiras[$i]);
	}
	
	$em->remove($oLayout);
	$em->flush();

} catch (\Exception $e) {
	$system->criaAviso(\Zage\App\Aviso\Tipo::ERRO,$e->getMessage());
	die('1'.\Zage\App\Util::encodeUrl('||'));
	exit;
}


$system->criaAviso(\Zage\App\Aviso\Tipo::INFO,$tr->trans("Layout excluído com sucesso"));
echo '0'.\Zage\App\Util::encodeUrl('|'.$oLayout->getCodigo().'|');