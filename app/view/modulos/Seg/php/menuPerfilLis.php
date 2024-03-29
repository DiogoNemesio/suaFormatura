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
	\Zage\App\Erro::halt('Falta de Parâmetros');
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
## Resgata os parâmetros passados pelo formulario de pesquisa
#################################################################################
if (isset($_GET['codPerfil'])) 		$codPerfil		= \Zage\App\Util::antiInjection($_GET['codPerfil']);
if (isset($_GET['codTipoOrg'])) 	$codTipoOrg		= \Zage\App\Util::antiInjection($_GET['codTipoOrg']);
if (isset($_GET['codMenu'])) 		$codMenu		= \Zage\App\Util::antiInjection($_GET['codMenu']);

#################################################################################
## Resgata os parâmetros passados
#################################################################################
if (!isset($codMenu)) {
	\Zage\App\Erro::halt('Falta de parâmetros 3');
}

#################################################################################
## Resgata os dados da árvore
#################################################################################
try {

	$perfis	= $em->getRepository('Entidades\ZgsegPerfil')->findAll(array('nome' => 'ASC'));
	$tipos	= $em->getRepository('Entidades\ZgadmOrganizacaoTipo')->findAll(array('descricao' => 'ASC'));

	if (isset($codPerfil)) {
		$_SESSION['Seg']['codPerfil']	= $codPerfil;
	}elseif (isset($_SESSION['Seg']['codPerfil'])) {
		$codPerfil						= $_SESSION['Seg']['codPerfil'];
	}elseif (!isset($codPerfil) && !empty($perfis)) {
		$codPerfil	= $perfis[0]->getCodigo();
	}else{
		$codPerfil 	= null;
	}
	

	if (isset($codTipoOrg)) {
		$_SESSION['Seg']['codTipoOrg']	= $codTipoOrg;
	}elseif (isset($_SESSION['Seg']['codTipoOrg'])) {
		$codTipoOrg						= $_SESSION['Seg']['codTipoOrg'];
	}elseif (!isset($codTipoOrg) && !empty($tipos)) {
		$codTipoOrg	= $tipos[0]->getCodigo();
	}else{
		$codTipoOrg 	= null;
	}
	
	
	//\Doctrine\Common\Util\Debug::dump($associados);
	//exit;
	
	if ( (\Zage\Seg\Menu::estaAssociado($codMenu,$codPerfil,$codTipoOrg) == true) || (!$codMenu) ) {
		
		$associados		= \Zage\Seg\Menu::listaAssociados($codPerfil,$codMenu,$codTipoOrg);
		$disponiveis	= \Zage\Seg\Menu::listaDisponiveis($codPerfil,$codMenu,$codTipoOrg);
		
		$liAss			= "";
		$liDis			= "";
		
		for ($i = 0; $i < sizeof($associados); $i++) {
			if ($associados[$i]->getCodTipo()->getCodigo() == "M") {
				$classe		= "fa fa-list";
			}else{
				$classe 	= "fa fa-external-link";
			}
			$liAss		.= '<li id="zgId_"'.$associados[$i]->getCodigo().'" class="ui-state-default" zg-data-id="'.$associados[$i]->getCodigo().'"><i class="ace-icon bigger-120 green '.$classe.'"></i>&nbsp;'.$associados[$i]->getNome().'&nbsp;('.$associados[$i]->getCodTipo()->getCodigo().')</li>';
		}
		for ($i = 0; $i < sizeof($disponiveis); $i++) {
			if ($disponiveis[$i]->getCodTipo()->getCodigo() == "M") {
				$classe		= "fa fa-list";
			}else{
				$classe 	= "fa fa-external-link";
			}
			$liDis		.= '<li id="zgDis_"'.$disponiveis[$i]->getCodigo().'" class="ui-state-default" zg-data-id="'.$disponiveis[$i]->getCodigo().'"><i class="ace-icon bigger-120 green '.$classe.'"></i>&nbsp;'.$disponiveis[$i]->getNome().'&nbsp;('.$disponiveis[$i]->getCodTipo()->getCodigo().')</li>';
		}
	}else{
		
		$associados		= array();
		$disponiveis	= array();
		
		$liAss			= "";
		$liDis			= "";
		
	}
	
} catch(\Exception $e) {
	\Zage\App\Erro::halt($e->getMessage());
}

#################################################################################
## Select2 
#################################################################################
try {
	$oPerfis	= $system->geraHtmlCombo($perfis,	'CODIGO', 'NOME', $codPerfil, null);
	$oTipos		= $system->geraHtmlCombo($tipos,	'CODIGO', 'DESCRICAO', $codTipoOrg, null);
	
} catch (\Exception $e) {
	\Zage\App\Erro::halt($e->getMessage(),__FILE__,__LINE__);
}

#################################################################################
## Resgata a url desse script
#################################################################################
$url			= ROOT_URL."/Seg/".basename(__FILE__)."?id=".$id;
$uid			= \Zage\App\Util::encodeUrl('_codMenu_='.$_codMenu_.'&_icone_='.$_icone_.'&codPerfil='.$codPerfil.'&codTipoOrg='.$codTipoOrg.'&codMenu='.$codMenu);
$dpUrl			= \Zage\App\Util::getCaminhoCorrespondente(__FILE__,\Zage\App\ZWS::EXT_DP,\Zage\App\ZWS::CAMINHO_RELATIVO)."?id=".$uid;

#################################################################################
## Gera a localização (breadcrumb)
#################################################################################
$local          = $system->mostraLocalizacao($codMenu);

#################################################################################
## Carregando o template html
#################################################################################
$tpl	= new \Zage\App\Template();
$tpl->load(\Zage\App\Util::getCaminhoCorrespondente(__FILE__, \Zage\App\ZWS::EXT_HTML));

#################################################################################
## Define os valores das variáveis
#################################################################################
$tpl->set('ID'					,$id);
$tpl->set('URL'					,$url);
$tpl->set('PERFIS'				,$oPerfis);
$tpl->set('TIPOS_ORG'			,$oTipos);
$tpl->set('COD_PERFIL'			,$codPerfil);
$tpl->set('COD_MENU'			,$codMenu);
$tpl->set('LISTA_ASS'			,$liAss);
$tpl->set('LISTA_DIS'			,$liDis);
$tpl->set('BREADCRUMB'			,$local);
$tpl->set('DP'					,$dpUrl);

#################################################################################
## Por fim exibir a página HTML
#################################################################################
$tpl->show();

