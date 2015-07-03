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
	$aConta		= $em->getRepository('Entidades\ZgfinConta')->findBy(array('codFilial' => $system->getCodEmpresa()),array('nome' => 'ASC'));
	$oConta		= $system->geraHtmlCombo($aConta,	'CODIGO', 'NOME',	$_SESSION["_CRLIS_codContaRecFiltro"], null);
} catch (\Exception $e) {
	\Zage\App\Erro::halt($e->getMessage(),__FILE__,__LINE__);
}


#################################################################################
## Select da Categoria
#################################################################################
try {
	$aCat	= \Zage\Fin\Categoria::listaCombo("C");
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
## Select do Centro de Custo
#################################################################################
try {
	$aCentroCusto	= $em->getRepository('Entidades\ZgfinCentroCusto')->findBy(array('codEmpresa' => $system->getCodMatriz(),'indDebito' => 1),array('descricao' => 'ASC'));
	$oCentroCusto	= $system->geraHtmlCombo($aCentroCusto,	'CODIGO', 'DESCRICAO',	$_SESSION["_CRLIS_codCentroCustoFiltro"], null);
} catch (\Exception $e) {
	\Zage\App\Erro::halt($e->getMessage(),__FILE__,__LINE__);
}

#################################################################################
## Select do Status
#################################################################################
try {
	$aStatus	= $em->getRepository('Entidades\ZgfinContaStatusTipo')->findAll(array('descricao' => 'ASC'));
	$oStatus	= $system->geraHtmlCombo($aStatus,	'CODIGO', 'DESCRICAO',	$_SESSION["_CRLIS_codStatusFiltro"], null);
} catch (\Exception $e) {
	\Zage\App\Erro::halt($e->getMessage(),__FILE__,__LINE__);
}

#################################################################################
## Definir a URL do filtro
#################################################################################
$urlFiltro		= ROOT_URL . "/Fin/contaReceberLis.php?id=".$id;

#################################################################################
## Carregando o template html
#################################################################################
$tpl	= new \Zage\App\Template();
$tpl->load(\Zage\App\Util::getCaminhoCorrespondente(__FILE__, \Zage\App\ZWS::EXT_HTML));

#################################################################################
## Define os valores das variáveis
#################################################################################
$tpl->set('ID'				,$id);
$tpl->set('TITULO'			,'Pesquisa de contas a receber');
$tpl->set('FILTER_URL'		,$urlFiltro);
$tpl->set('DIVCENTRAL'		,$system->getDivCentral());
$tpl->set('DP_MODAL'		,\Zage\App\Util::getCaminhoCorrespondente(__FILE__,\Zage\App\ZWS::EXT_DP,\Zage\App\ZWS::CAMINHO_RELATIVO));
$tpl->set('CATEGORIAS'		,$oCat);
$tpl->set('STATUS'			,$oStatus);
$tpl->set('CENTRO_CUSTO'	,$oCentroCusto);
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

