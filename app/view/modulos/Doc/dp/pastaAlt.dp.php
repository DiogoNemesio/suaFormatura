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
if (isset($_POST['codPastaPai'])) 	$codPastaPai	= \Zage\App\Util::antiInjection($_POST['codPastaPai']);
if (isset($_POST['codPasta'])) 		$codPasta		= \Zage\App\Util::antiInjection($_POST['codPasta']);
if (isset($_POST['nome'])) 			$nome			= \Zage\App\Util::antiInjection($_POST['nome']);
if (isset($_POST['descricao'])) 	$descricao		= \Zage\App\Util::antiInjection($_POST['descricao']);


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

if (!$codPastaPai) $codPastaPai = null;

/** Verifica se o nome já existe no mesmo nível **/
$existePasta	= $em->getRepository('Entidades\ZgdocPasta')->findOneBy(array('codEmpresa' => $system->getCodEmpresa(),'codPastaPai' => $codPastaPai,'nome' => $nome));

if (is_object($existePasta) && $existePasta->getCodigo() != $codPasta) {
	$err	= $tr->trans("Nome já utilizado, escolha outro nome de pasta");
}

if ($err != null) {
	echo '1'.\Zage\App\Util::encodeUrl('||'.htmlentities($err));
	exit;
}

#################################################################################
## Salvar no banco
#################################################################################
try {

	if (isset($codPasta) && (!empty($codPasta))) {
		$oPasta	= $em->getRepository('Entidades\ZgdocPasta')->findOneBy(array('codigo' => $codPasta));
		if (!$oPasta) $oPasta	= new \Entidades\ZgdocPasta();
	}else{
		$oPasta		= new \Entidades\ZgdocPasta();
	}
	
	if ($codPastaPai != null) {
		$oPastaPai	= $em->getRepository('Entidades\ZgdocPasta')->findOneBy(array('codigo' => $codPastaPai));
	}else{
		$oPastaPai	= null;
	}
	
	$oPasta->setNome($nome);
	$oPasta->setDescricao($descricao);
	$oPasta->setCodPastaPai($oPastaPai);
	$oPasta->setCodEmpresa($emp);
	
	$em->persist($oPasta);
	$em->flush();
	$em->detach($oPasta);

} catch (\Exception $e) {
	echo '1'.\Zage\App\Util::encodeUrl('||'.htmlentities($e->getMessage()));
	exit;
}

echo '0'.\Zage\App\Util::encodeUrl('|'.$oPasta->getCodigo().'|'.htmlentities($tr->trans("Informações salvas com sucesso")));
