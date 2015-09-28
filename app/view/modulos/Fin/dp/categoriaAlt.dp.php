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
if (isset($_POST['codCategoriaPai'])) 	$codCategoriaPai	= \Zage\App\Util::antiInjection($_POST['codCategoriaPai']);
if (isset($_POST['codCategoria'])) 		$codCategoria		= \Zage\App\Util::antiInjection($_POST['codCategoria']);
if (isset($_POST['descricao'])) 		$descricao			= \Zage\App\Util::antiInjection($_POST['descricao']);
if (isset($_POST['ativa'])) 			$ativa				= \Zage\App\Util::antiInjection($_POST['ativa']);
if (isset($_POST['codTipo'])) 			$codTipo			= \Zage\App\Util::antiInjection($_POST['codTipo']);

#################################################################################
## Limpar a variável de erro
#################################################################################
$err	= false;

#################################################################################
## Fazer validação dos campos
#################################################################################
/** Nome **/
if (!isset($descricao) || (empty($descricao)) || (strlen($descricao) < 1)) {
	$err	= $tr->trans("Campo Descrição inválido, O campo deve ter pelo menos 2 Caracteres");
}
/** Tipo **/
$oTipo		= $em->getRepository('Entidades\ZgfinCategoriaTipo')->findOneBy(array('codigo' => $codTipo));
if (!$oTipo) {
	$err	= $tr->trans("Tipo de Categoria inválido !!!");
} 
 
if (!$codCategoriaPai) $codCategoriaPai = null;

/** Verifica se o nome já existe no mesmo nível **/
$existeCat	= $em->getRepository('Entidades\ZgfinCategoria')->findOneBy(array('codOrganizacao' => $system->getCodOrganizacao(),'codCategoriaPai' => $codCategoriaPai,'descricao' => $descricao,'codTipo' => $codTipo));

if (is_object($existeCat) && $existeCat->getCodigo() != $codCategoria) {
	$err	= $tr->trans("Descrição já utilizada, escolha outra descricação para a Categoria");
}

if (isset($ativa) && (!empty($ativa))) {
	$ativa	= 1;
}else{
	$ativa	= 0;
}


if ($err != null) {
	echo '1'.\Zage\App\Util::encodeUrl('||'.htmlentities($err));
	exit;
}

#################################################################################
## Salvar no banco
#################################################################################
try {

	if (isset($codCategoria) && (!empty($codCategoria))) {
		$oCat	= $em->getRepository('Entidades\ZgfinCategoria')->findOneBy(array('codigo' => $codCategoria,'codOrganizacao' => $system->getCodOrganizacao()));
		if (!$oCat) $oCat	= new \Entidades\ZgfinCategoria();
	}else{
		$oCat		= new \Entidades\ZgfinCategoria();
	}
	
	#################################################################################
	## Resgatar o objeto da Matriz
	#################################################################################
	$oMat	= $em->getRepository('Entidades\ZgadmOrganizacao')->findOneBy(array('codigo' => $system->getCodOrganizacao()));
	
	
	if ($codCategoriaPai != null) {
		$oCatPai	= $em->getRepository('Entidades\ZgfinCategoria')->findOneBy(array('codigo' => $codCategoriaPai,'codOrganizacao' => $system->getCodOrganizacao()));
	}else{
		$oCatPai	= null;
	}
	
	$oCat->setDescricao($descricao);
	$oCat->setCodCategoriaPai($oCatPai);
	$oCat->setCodOrganizacao($oMat);
	$oCat->setIndAtiva($ativa);
	$oCat->setCodTipo($oTipo);
	
	$em->persist($oCat);
	$em->flush();
	$em->detach($oCat);

} catch (\Exception $e) {
	echo '1'.\Zage\App\Util::encodeUrl('||'.htmlentities($e->getMessage()));
	exit;
}

echo '0'.\Zage\App\Util::encodeUrl('|'.$oCat->getCodigo().'|'.htmlentities($tr->trans("Informações salvas com sucesso")));
