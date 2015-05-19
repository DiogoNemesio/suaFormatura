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
## Resgata a variável ID que está criptografada
#################################################################################
if (isset($_GET['id'])) {
	$id = \Zage\App\Util::antiInjection($_GET["id"]);
}elseif (isset($_POST['id'])) {
	$id = \Zage\App\Util::antiInjection($_POST["id"]);
}elseif (isset($id)) 	{
	$id = \Zage\App\Util::antiInjection($id);
}else{
	\Zage\Erro::halt('Falta de Parâmetros');
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
## Resgata os parâmetros passados pelo formulario
#################################################################################
if (isset($_POST['perfil'])) 		$perfil	= $_POST['perfil'];
if (!isset($codUsuario) || (!$codUsuario)) {
	\Zage\Erro::halt('Falta de Parâmetros 2');
}

#################################################################################
## Resgata as informações do usuário
#################################################################################
$info 		= $em->getRepository('Entidades\ZgsegUsuario')->findOneBy(array('codOrganizacao' => $system->getCodOrganizacao(),'codigo' => $codUsuario));

#################################################################################
## Limpar a variável de erro
#################################################################################
$err	= false;

#################################################################################
## Fazer validação dos campos
#################################################################################

#################################################################################
## Salvar no banco
#################################################################################

#################################################################################
## Resgata as empresas
#################################################################################
$aEmps		= $em->getRepository('Entidades\ZgadmEmpresa')->findBy(array('codOrganizacao' => $system->getCodOrganizacao()));

for ($i = 0; $i < sizeof($aEmps); $i++) {
	
	$oUsuEmp	= $em->getRepository('Entidades\ZgsegUsuarioEmpresa')->findOneBy(array('codUsuario' => $codUsuario,'codEmpresa' => $aEmps[$i]->getCodigo(),'codPerfil' => $perfil[$aEmps[$i]->getCodigo()]));
	
	if ((isset($_POST[$aEmps[$i]->getCodigo()])) && (strtoupper($_POST[$aEmps[$i]->getCodigo()]) == "ON")) {
		if (!isset($perfil[$aEmps[$i]->getCodigo()])) {
			echo '1'.\Zage\App\Util::encodeUrl('|'.$aEmps[$i]->getCodigo().'|'.htmlentities("Perfil não selecionado para a empresa: ".$aEmps[$i]->getCodigo()));
			exit;
		}
		
		if (!$oUsuEmp) $oUsuEmp = new \Entidades\ZgsegUsuarioEmpresa(); 

		$oPerfil		= $em->getRepository('Entidades\ZgsegPerfil')->findOneBy(array('codOrganizacao' => $system->getCodOrganizacao(),'codigo' => $perfil[$aEmps[$i]->getCodigo()]));  
		
		$oUsuEmp->setCodEmpresa($aEmps[$i]);
		$oUsuEmp->setCodUsuario($info);
		$oUsuEmp->setCodPerfil($oPerfil);
		
		$em->persist($oUsuEmp);
	}else{
		if ($oUsuEmp) {
			$em->remove($oUsuEmp);
		}
	}
}

#################################################################################
## Salvar no banco
#################################################################################
try {

	$em->flush();
	$em->clear();

} catch (\Exception $e) {
	$system->criaAviso(\Zage\App\Aviso\Tipo::ERRO,$e->getMessage());
	echo '1'.\Zage\App\Util::encodeUrl('||'.htmlentities($e->getMessage()));
	exit;
}



if (trim($err) == "") {
	$system->criaAviso(\Zage\App\Aviso\Tipo::INFO,$tr->trans('Acesso salvo com sucesso !!!'));
	echo '0'.\Zage\App\Util::encodeUrl('|'.$codUsuario.'|'.htmlentities('Acesso salvo com sucesso !!!'));
}else{
	$system->criaAviso(\Zage\App\Aviso\Tipo::INFO,$tr->trans(htmlentities($err)));
	echo '1'.\Zage\App\Util::encodeUrl('|'.$codUsuario.'|'.htmlentities($err));
}
