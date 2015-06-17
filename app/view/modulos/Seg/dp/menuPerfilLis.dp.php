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
	$system->criaAviso(\Zage\App\Aviso\Tipo::ERRO,$tr->trans('Falta de parâmetros (%s)',array('%s' => 'id')));
	echo '1'.\Zage\App\Util::encodeUrl('||');
	exit;
}

#################################################################################
## Descompacta o ID
#################################################################################
\Zage\App\Util::descompactaId($id);

#################################################################################
## Resgata os parâmetros passados pelo formulario
#################################################################################
if (isset($_POST['itens']))			$itens		= $_POST['itens'];

if (!isset($codMenu)) {
	$system->criaAviso(\Zage\App\Aviso\Tipo::ERRO,$tr->trans('Falta de parâmetros (%s)',array('%s' => 'codMenu')));
	echo '1'.\Zage\App\Util::encodeUrl('||');
	exit;
}

if (!isset($codPerfil)) {
	$system->criaAviso(\Zage\App\Aviso\Tipo::ERRO,$tr->trans('Falta de parâmetros (%s)',array('%s' => 'codPerfil')));
	echo '1'.\Zage\App\Util::encodeUrl('||');
	exit;
}

if (!isset($codTipoOrg)) {
	$system->criaAviso(\Zage\App\Aviso\Tipo::ERRO,$tr->trans('Falta de parâmetros (%s)',array('%s' => 'codTipoCond')));
	echo '1'.\Zage\App\Util::encodeUrl('||');
	exit;
}

if (!isset($itens) || (!$itens)) $itens = array();


#################################################################################
## Salvar no banco
#################################################################################
try {	
	
	/** Desassociar  **/
	if (empty($codMenu)) $codMenu = null;
	$menus		= \Zage\Seg\Menu::listaAssociados($codPerfil, $codMenu, $codTipoOrg);
	
	for ($i = 0; $i < sizeof($menus); $i++) {
		if (!in_array($menus[$i]->getCodigo(), $itens)) {
			$erro = \Zage\Seg\Menu::desassociaFilhos($menus[$i]->getCodigo(),$codPerfil,$codTipoOrg);
			if ($erro) {
				$system->criaAviso(\Zage\App\Aviso\Tipo::ERRO,$erro);
				echo '1'.\Zage\App\Util::encodeUrl('||');
				exit;
			}
		}
	}
	
	/** Associar e ajustar as ordens **/
	$oPerfil			= $em->getRepository('Entidades\ZgsegPerfil')->findOneBy(array('codigo' => $codPerfil));
	if (!$oPerfil)	{
		$system->criaAviso(\Zage\App\Aviso\Tipo::ERRO,$tr->trans("Perfil não encontrado").': '.$codPerfil);
		echo '1'.\Zage\App\Util::encodeUrl('||');
		exit;
	}
	
	$oTipo			= $em->getRepository('Entidades\ZgadmOrganizacaoTipo')->findOneBy(array('codigo' => $codTipoOrg));
	if (!$oTipo)	{
		$system->criaAviso(\Zage\App\Aviso\Tipo::ERRO,$tr->trans("Tipo de organização não encontrado").': '.$codTipoOrg);
		echo '1'.\Zage\App\Util::encodeUrl('||');
		exit;
	}
	
	
	for ($i = 0; $i < sizeof($itens); $i++) {
	
		$ordem			= $i + 1;
		$oMenuPerfil	= $em->getRepository('Entidades\ZgappMenuPerfil')->findOneBy(array('codMenu' => $itens[$i],'codPerfil' => $codPerfil,'codTipoOrganizacao' => $codTipoOrg));
		$oMenu			= $em->getRepository('Entidades\ZgappMenu')->findOneBy(array('codigo' => $itens[$i]));
		
		if (!$oMenuPerfil) {
			$oMenuPerfil		= new \Entidades\ZgappMenuPerfil();
		}
	
		
		if ($oMenuPerfil->getOrdem() != $ordem) {
			
			$oMenuPerfil->setCodMenu($oMenu);
			$oMenuPerfil->setOrdem($ordem);
			$oMenuPerfil->setCodPerfil($oPerfil);
			$oMenuPerfil->setCodTipoOrganizacao($oTipo);
					
			try {
				$em->persist($oMenuPerfil);
				$em->flush();
				$em->detach($oMenuPerfil);
			} catch (\Exception $e) {
				$system->criaAviso(\Zage\App\Aviso\Tipo::ERRO,"Não foi possível associar o menu: ".$itens[$i]." Erro: ".$e->getMessage());
				echo '1'.\Zage\App\Util::encodeUrl('||'.htmlentities($e->getMessage()));
				exit;
			}
		}
	
	}

} catch (\Exception $e) {
	$system->criaAviso(\Zage\App\Aviso\Tipo::ERRO,$e->getMessage());
	echo '1'.\Zage\App\Util::encodeUrl('||'.htmlentities($e->getMessage()));
	exit;
}

echo '0'.\Zage\App\Util::encodeUrl('||');
