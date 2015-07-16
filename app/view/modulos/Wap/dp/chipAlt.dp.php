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
## Variáveis globais
#################################################################################
global $em,$system,$tr;

#################################################################################
## Resgata os parâmetros passados pelo formulário
#################################################################################
if (isset($_POST['codChip']))				$codChip		= \Zage\App\Util::antiInjection($_POST['codChip']);
if (isset($_POST['identificacao']))			$identificacao	= \Zage\App\Util::antiInjection($_POST['identificacao']);
if (isset($_POST['numero']))				$numero			= \Zage\App\Util::antiInjection($_POST['numero']);
if (isset($_POST['codPais']))				$codPais		= \Zage\App\Util::antiInjection($_POST['codPais']);

#################################################################################
## Limpar a variável de erro
#################################################################################
$err	= false;

#################################################################################
## Fazer validação dos campos
#################################################################################
/** Código **/
if (!isset($codChip)) {
	$system->criaAviso(\Zage\App\Aviso\Tipo::ERRO,$tr->trans("Falta de parâmetros !!"));
	$err	= 1;
}

/** País **/
if (!isset($codPais) || empty($codPais)) {
	$system->criaAviso(\Zage\App\Aviso\Tipo::ERRO,$tr->trans("Campo País é obrigatório !!"));
	$err	= 1;
}else{
	$oPais	= $em->getRepository('\Entidades\ZgadmPais')->findOneBy(array('codigo' => $codPais));
	if (!$oPais) {
		$system->criaAviso(\Zage\App\Aviso\Tipo::ERRO,$tr->trans("País não encontrado !!"));
		$err	= 1;
	}
}

/** Organização **/
$oOrg		= $em->getRepository('\Entidades\ZgadmOrganizacao')->findOneBy(array('codigo' => $system->getCodOrganizacao()));


if ($err != null) {
	echo '1'.\Zage\App\Util::encodeUrl('||'.htmlentities($err));
 	exit;
}
 
#################################################################################
## Salvar no banco
#################################################################################
try {
	
	$chip		= new \Zage\Wap\Chip();
	
	$chip->_setCodigo($codChip);
	$chip->setCodOrganizacao($oOrg);
	$chip->setCodPais($oPais);
	$chip->setNumero($numero);
	$chip->setIdentificacao($identificacao);
	
	$codigo	= $chip->salvar();
 	
} catch (\Exception $e) {
 	$system->criaAviso(\Zage\App\Aviso\Tipo::ERRO,$e->getMessage());
 	echo '1'.\Zage\App\Util::encodeUrl('||'.htmlentities($e->getMessage()));
 	exit;
}
 
$system->criaAviso(\Zage\App\Aviso\Tipo::INFO,"Informações salvas com sucesso");
echo '0'.\Zage\App\Util::encodeUrl('|'.$codigo);
