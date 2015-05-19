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
if (isset($_POST['codTipo'])) 		$codTipo	= \Zage\App\Util::antiInjection($_POST['codTipo']);
if (isset($_POST['codPasta'])) 		$codPasta	= \Zage\App\Util::antiInjection($_POST['codPasta']);
if (isset($_POST['nome'])) 			$nome		= \Zage\App\Util::antiInjection($_POST['nome']);
if (isset($_POST['descricao'])) 	$descricao	= \Zage\App\Util::antiInjection($_POST['descricao']);


#################################################################################
## Limpar a variável de erro
#################################################################################
$err	= false;

#################################################################################
## Fazer validação dos campos
#################################################################################
/** Nome **/
if (!isset($nome) || (empty($nome)) || (strlen($nome) < 2)) {
	$err	= $tr->trans("Campo Nome inválido, O campo deve ter pelo menos 3 Caracteres");
}

/** Verifica se o nome já existe no mesmo nível **/
$existeTipo	= $em->getRepository('Entidades\ZgdocDocumentoTipo')->findOneBy(array('codPasta' => $codPasta,'nome' => $nome));

if (is_object($existeTipo) && $existeTipo->getCodigo() != $codTipo) {
	$err	= $tr->trans("Nome já utilizado, escolha outro nome");
}


if ($err != null) {
	echo '1'.\Zage\App\Util::encodeUrl('||'.htmlentities($err));
	exit;
}

#################################################################################
## Salvar no banco
#################################################################################
try {

	if (isset($codTipo) && (!empty($codTipo))) {
		$oTipo	= $em->getRepository('Entidades\ZgdocDocumentoTipo')->findOneBy(array('codigo' => $codTipo));
		if (!$oTipo) $oTipo	= new \Entidades\ZgdocDocumentoTipo();
	}else{
		$oTipo	= new \Entidades\ZgdocDocumentoTipo();
	}
	
	$oPasta			= $em->getRepository('Entidades\ZgdocPasta')->findOneBy(array('codigo' => $codPasta));
	
	if (!$oPasta)	{
		die ('1'.\Zage\App\Util::encodeUrl('||'.htmlentities($tr->trans('Pasta não encontrada'))));
	}
	
	$oTipo->setCodPasta($oPasta);
	$oTipo->setNome($nome);
	$oTipo->setDescricao($descricao);
	
	$em->persist($oTipo);
	$em->flush();
	$em->detach($oTipo);

} catch (\Exception $e) {
	echo '1'.\Zage\App\Util::encodeUrl('||'.htmlentities($e->getMessage()));
	exit;
}

echo '0'.\Zage\App\Util::encodeUrl('|'.$oTipo->getCodigo().'|'.htmlentities($tr->trans("Informações salvas com sucesso")));
