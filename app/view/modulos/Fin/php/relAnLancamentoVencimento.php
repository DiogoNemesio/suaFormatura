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
global $em,$system,$tr,$log;

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
## Resgata as informações do Relatório
#################################################################################
$info			= $em->getRepository('Entidades\ZgappMenu')->findOneBy(array('codigo' => $_codMenu_));

#################################################################################
## Resgata as informações da organização
#################################################################################
$oOrg			= $em->getRepository('Entidades\ZgadmOrganizacao')->findOneBy(array('codigo' => $system->getCodOrganizacao()));

#################################################################################
## Resgata os parâmetros passados pelo formulario
#################################################################################
$codTipoFiltro	= (isset($codTipoFiltro)) 	? $codTipoFiltro		: 'D';
$dataFiltro		= (isset($dataFiltro)) 		? $dataFiltro			: date($system->config["data"]["dateFormat"]);
$mesFiltro		= (isset($mesFiltro)) 		? $mesFiltro			: date('m/Y');
$dataIniFiltro	= (isset($dataIniFiltro)) 	? $dataIniFiltro		: date($system->config["data"]["dateFormat"]);
$dataFimFiltro	= (isset($dataFimFiltro)) 	? $dataFimFiltro		: date($system->config["data"]["dateFormat"]);

#################################################################################
## Url de geração do PDF
#################################################################################
$urlRel			= ROOT_URL . "/Fin/relAnLancamentoVencimentoPDF.php";

#################################################################################
## Select da Forma de Pagamento
#################################################################################
try {
	$aFormaPag	= $em->getRepository('Entidades\ZgfinFormaPagamento')->findBy(array(),array('descricao' => 'ASC'));
	$oFormaPag	= $system->geraHtmlCombo($aFormaPag,	'CODIGO', 'DESCRICAO',	'', null);
} catch (\Exception $e) {
	\Zage\App\Erro::halt($e->getMessage(),__FILE__,__LINE__);
}

#################################################################################
## Select da Conta de Débito
#################################################################################
try {
	$aConta		= $em->getRepository('Entidades\ZgfinConta')->findBy(array('codOrganizacao' => $system->getCodOrganizacao()),array('nome' => 'ASC'));
	$oConta		= $system->geraHtmlCombo($aConta,	'CODIGO', 'NOME',	'', null);
} catch (\Exception $e) {
	\Zage\App\Erro::halt($e->getMessage(),__FILE__,__LINE__);
}


#################################################################################
## Select da Categoria
#################################################################################
try {
	$aCatDeb	= \Zage\Fin\Categoria::listaCombo("D",$oOrg->getCodTipo()->getCodigo());
	$aCatCre	= \Zage\Fin\Categoria::listaCombo("C",$oOrg->getCodTipo()->getCodigo());
	$oCat   = "";
	if ($aCatDeb) {
		$aCatTemp	= array();
		$i 			= 0;

		$oCat		.= '<optgroup label="Categorias de Débito">';
		foreach ($aCatDeb as $cat) {
			$tDesc 	= ($cat->getCodCategoriaPai() != null) ? $cat->getCodCategoriaPai()->getDescricao() . "/" . $cat->getDescricao() : $cat->getDescricao();
			$aCatTemp[$tDesc]	= $cat->getCodigo();

		}

		ksort($aCatTemp);

		foreach ($aCatTemp as $cDesc => $cCod) {
			$oCat .= "<option value=\"".$cCod."\">".$cDesc.'</option>';
		}
		
		$oCat		.= '</optgroup>';
		
	}

	if ($aCatCre) {
		$aCatTemp	= array();
		$i 			= 0;
	
		$oCat		.= '<optgroup label="Categorias de Crédito">';
		foreach ($aCatCre as $cat) {
			$tDesc 	= ($cat->getCodCategoriaPai() != null) ? $cat->getCodCategoriaPai()->getDescricao() . "/" . $cat->getDescricao() : $cat->getDescricao();
			$aCatTemp[$tDesc]	= $cat->getCodigo();
	
		}
	
		ksort($aCatTemp);
	
		foreach ($aCatTemp as $cDesc => $cCod) {
			$oCat .= "<option value=\"".$cCod."\">".$cDesc.'</option>';
		}
			
		$oCat		.= '</optgroup>';
	
	}
	

} catch (\Exception $e) {
	\Zage\App\Erro::halt($e->getMessage(),__FILE__,__LINE__);
}

#################################################################################
## Select do Centro de Custo
#################################################################################
try {
	$aCentroCusto	= $em->getRepository('Entidades\ZgfinCentroCusto')->findBy(array('codOrganizacao' => $system->getCodOrganizacao(),'indDebito' => 1),array('descricao' => 'ASC'));
	$oCentroCusto	= $system->geraHtmlCombo($aCentroCusto,	'CODIGO', 'DESCRICAO',	'', null);
} catch (\Exception $e) {
	\Zage\App\Erro::halt($e->getMessage(),__FILE__,__LINE__);
}

#################################################################################
## Select do Status
#################################################################################
try {
	$aStatus	= $em->getRepository('Entidades\ZgfinContaStatusTipo')->findAll(array('descricao' => 'ASC'));
	$oStatus	= $system->geraHtmlCombo($aStatus,	'CODIGO', 'DESCRICAO',	'', null);
} catch (\Exception $e) {
	\Zage\App\Erro::halt($e->getMessage(),__FILE__,__LINE__);
}

#################################################################################
## Carregando o template html
#################################################################################
$tpl	= new \Zage\App\Template();
$tpl->load(\Zage\App\Util::getCaminhoCorrespondente(__FILE__, \Zage\App\ZWS::EXT_HTML));

#################################################################################
## Define os valores das variáveis
#################################################################################
$tpl->set('ID'					,$id);
$tpl->set('TITULO'				,$info->getNome());
$tpl->set('DATA_INI_FILTRO'		,$dataIniFiltro);
$tpl->set('DATA_FIM_FILTRO'		,$dataFimFiltro);
$tpl->set('CATEGORIAS'			,$oCat);
$tpl->set('STATUS'				,$oStatus);
$tpl->set('CENTRO_CUSTO'		,$oCentroCusto);
$tpl->set('FORMAS_PAG'			,$oFormaPag);
$tpl->set('CONTAS'				,$oConta);
$tpl->set('URL_REL'				,$urlRel);


#################################################################################
## Por fim exibir a página HTML
#################################################################################
$tpl->show();