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
## Resgata as informações da Organização
#################################################################################
$oOrg		= $em->getRepository('Entidades\ZgadmOrganizacao')->findOneBy(array('codigo' => $system->getCodOrganizacao()));

#################################################################################
## Select da Forma de Pagamento
#################################################################################
try {
	$aFormaPag	= $em->getRepository('Entidades\ZgfinFormaPagamento')->findBy(array(),array('descricao' => 'ASC'));
	$oFormaPag	= $system->geraHtmlCombo($aFormaPag,	'CODIGO', 'DESCRICAO',	$_SESSION["_CRLIS_codFormaPagFiltro"], null);
} catch (\Exception $e) {
	\Zage\App\Erro::halt($e->getMessage(),__FILE__,__LINE__);
}

#################################################################################
## Select da Conta de Débito
#################################################################################
try {
	$aConta		= $em->getRepository('Entidades\ZgfinConta')->findBy(array('codOrganizacao' => $system->getCodOrganizacao()),array('nome' => 'ASC'));
	$oConta		= $system->geraHtmlCombo($aConta,	'CODIGO', 'NOME',	$_SESSION["_CRLIS_codContaRecFiltro"], null);
} catch (\Exception $e) {
	\Zage\App\Erro::halt($e->getMessage(),__FILE__,__LINE__);
}


#################################################################################
## Select da Categoria
#################################################################################
try {
	$aCat	= \Zage\Fin\Categoria::listaCombo("C",$oOrg->getCodTipo()->getCodigo());
	$oCat   = "";
	if ($aCat) {
		$aCatTemp	= array();
		$i 			= 0;

		foreach ($aCat as $cat) {
			$tDesc 	= ($cat->getCodCategoriaPai() != null) ? $cat->getCodCategoriaPai()->getDescricao() . "/" . $cat->getDescricao() : $cat->getDescricao();
			$aCatTemp[$tDesc]	= $cat->getCodigo();

		}

		ksort($aCatTemp);

		foreach ($aCatTemp as $cDesc => $cCod) {
			if ($_SESSION["_CRLIS_codCategoriaFiltro"] !== null) {
				(in_array($cCod, $_SESSION["_CRLIS_codCategoriaFiltro"])) ? $selected = "selected=\"selected\"" : $selected = "";
			}else{
				$selected = "";
			}
			$oCat .= "<option value=\"".$cCod."\" $selected>".$cDesc.'</option>';
		}
	}
} catch (\Exception $e) {
	\Zage\App\Erro::halt($e->getMessage(),__FILE__,__LINE__);
}

#################################################################################
## Multi select dos usuários
#################################################################################
try {
	$aUsuarios	= \Zage\Fmt\Organizacao::listaUsuarioCadFormatura($system->getCodOrganizacao());
	$oUsuarios	= $system->geraHtmlCombo($aUsuarios,	'CODIGO', 'NOME', null, null);
} catch (\Exception $e) {
	\Zage\App\Erro::halt($e->getMessage(),__FILE__,__LINE__);
}

#################################################################################
## Multi Select do Status
#################################################################################
try {
	$codStatusSelected = ["A","AA"];
	$aStatus	= $em->getRepository('Entidades\ZgadmOrganizacaoStatusTipo')->findAll(array('descricao' => 'ASC'));
	$oStatus	= $system->geraHtmlCombo($aStatus,	'CODIGO', 'DESCRICAO',	$codStatusSelected , null);
} catch (\Exception $e) {
	\Zage\App\Erro::halt($e->getMessage(),__FILE__,__LINE__);
}

#################################################################################
## Definir a URL do filtro
#################################################################################
$urlFiltro		= ROOT_URL . "/Fmt/formaturaLis.php?id=".$id;

#################################################################################
## Carregando o template html
#################################################################################
$tpl	= new \Zage\App\Template();
$tpl->load(\Zage\App\Util::getCaminhoCorrespondente(__FILE__, \Zage\App\ZWS::EXT_HTML));

#################################################################################
## Define os valores das variáveis
#################################################################################
$tpl->set('ID'				,$id);
$tpl->set('TITULO'			,'Pesquisar Formaturas');
$tpl->set('FILTER_URL'		,$urlFiltro);
$tpl->set('DIVCENTRAL'		,$system->getDivCentral());
$tpl->set('DP_MODAL'		,\Zage\App\Util::getCaminhoCorrespondente(__FILE__,\Zage\App\ZWS::EXT_DP,\Zage\App\ZWS::CAMINHO_RELATIVO));
$tpl->set('CATEGORIAS'		,$oCat);
$tpl->set('STATUS'			,$oStatus);
$tpl->set('CENTRO_CUSTO'	,$oUsuarios);
$tpl->set('FORMAS_PAG'		,$oFormaPag);
$tpl->set('CONTAS'			,$oConta);
$tpl->set('VALOR_INI'		,$_SESSION["_CRLIS_valorIniFiltro"]);
$tpl->set('VALOR_FIM'		,$_SESSION["_CRLIS_valorFimFiltro"]);
$tpl->set('DESCRICAO'		,$_SESSION["_CRLIS_descricaoFiltro"]);
$tpl->set('CLIENTE'			,$_SESSION["_CRLIS_clienteFiltro"]);

#################################################################################
## Por fim exibir a página HTML
#################################################################################
$tpl->show();

