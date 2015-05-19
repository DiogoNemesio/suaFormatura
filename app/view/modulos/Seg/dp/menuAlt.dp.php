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
if (isset($_POST['codMenuPai']))		$codMenuPai		= \Zage\App\Util::antiInjection($_POST['codMenuPai']);
if (isset($_POST['codMenu'])) 			$codMenu		= \Zage\App\Util::antiInjection($_POST['codMenu']);
if (isset($_POST['codModulo']))			$codModulo		= \Zage\App\Util::antiInjection($_POST['codModulo']);
if (isset($_POST['codTipo']))			$codTipo		= \Zage\App\Util::antiInjection($_POST['codTipo']);
if (isset($_POST['nome'])) 				$nome			= \Zage\App\Util::antiInjection($_POST['nome']);
if (isset($_POST['descricao']))			$descricao		= \Zage\App\Util::antiInjection($_POST['descricao']);
if (isset($_POST['icone'])) 			$icone			= \Zage\App\Util::antiInjection($_POST['icone']);
if (isset($_POST['link'])) 				$link			= \Zage\App\Util::antiInjection($_POST['link']);

#################################################################################
## Limpar a variável de erro
#################################################################################
$err	= false;

#################################################################################
## Fazer validação dos campos
#################################################################################
if (!isset($nome) || !$nome) {
	$err = $tr->trans('Campo "nome" é obrigatório');
}

if ($err != null) {
	echo '1'.\Zage\App\Util::encodeUrl('||'.htmlentities($err));
	exit;
}

#################################################################################
## Salvar no banco
#################################################################################
try {
	
	$modulo			= $em->getRepository('Entidades\ZgappModulo')->findOneBy(array('codigo' => $codModulo));
	
	if (!$modulo) {
		echo '1'.\Zage\App\Util::encodeUrl('||'.htmlentities($tr->trans('Módulo não encontrado')));
		exit;
	}
	
	$tipo			= $em->getRepository('Entidades\ZgappMenuTipo')->findOneBy(array('codigo' => $codTipo));
	
	if (!$tipo) {
		echo '1'.\Zage\App\Util::encodeUrl('||'.htmlentities($tr->trans('Tipo de Menu não encontrado')));
		exit;
	}
	
	$menuPai		= $em->getRepository('Entidades\ZgappMenu')->findOneBy(array('codigo' => $codMenuPai));
	if (!$menuPai) 	{
		$nivel				= 0;
	}else{
		$nivel				= $menuPai->getNivel() + 1;
	}
	

	$menu		= $em->getRepository('Entidades\ZgappMenu')->findOneBy(array('codigo' => $codMenu));
	if (!$menu) 	{
		$menu			= new \Entidades\ZgappMenu();
	}
	
	$oOrg		= $em->getRepository('Entidades\ZgadmOrganizacao')->findOneBy(array('codigo' => $system->getCodOrganizacao()));
	
	if (!$oOrg) {
		echo '1'.\Zage\App\Util::encodeUrl('||'.htmlentities($tr->trans('Organização não encontrada')));
		exit;
	}
	
	$indFixo		= 0;
	$indSistema		= 1;
	
	$menu->setCodOrganizacao($oOrg);
	$menu->setNome($nome);
	$menu->setDescricao($descricao);
	$menu->setCodTipo($tipo);
	$menu->setLink($link);
	$menu->setNivel($nivel);
	$menu->setIcone($icone);
	$menu->setCodMenuPai($menuPai);
	$menu->setCodModulo($modulo);
	$menu->setIndFixo($indFixo);
	$menu->setIndSistema($indSistema);
	
	$em->persist($menu);
	$em->flush();
	$em->detach($menu);

} catch (\Exception $e) {
	echo '1'.\Zage\App\Util::encodeUrl('||'.htmlentities($e->getMessage()));
	exit;
}

echo '0'.\Zage\App\Util::encodeUrl('|'.$menu->getCodigo().'|'.htmlentities($tr->trans("Informações salvas com sucesso")));
