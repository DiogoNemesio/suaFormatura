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
## Resgata as informações do banco
#################################################################################
if ($codProduto) {
	try {
		$info = $em->getRepository('Entidades\ZgestProduto')->findOneBy(array('codOrganizacao' => $system->getCodOrganizacao(), 'codigo' => $codProduto));

	} catch (\Exception $e) {
		\Zage\App\Erro::halt($e->getMessage());
	}

	$descricao		= $info->getDescricao();
	$codUniMed		= ($info->getCodUnidadeMedida() != null) ? $info->getCodUnidadeMedida()->getCodigo() : null;
	$referencia		= $info->getReferencia();
	$descricaoCom	= $info->getDescricaoCompleta();
	$ativo			= ($info->getIndAtivo()	== 1) ? "checked" : null;
	$codNcm			= $info->getCodNcm();
	$codSubgrupo	= ($info->getCodSubgrupoMateiral() != null) ? $info->getCodSubgrupoMateiral()->getCodigo() : null;
	$margemLucro	= $info->getPctMargemLucro();
	$codTipoPreço	= ($info->getCodTipoPrecoVenda() != null) ? $info->getCodTipoPrecoVenda()->getCodigo() : null;
	$valorVenda		= $info->getValorVenda();
	$desconto		= $info->getPctMaxDesconto();
	$observacao		= $info->getObservacao();

}else{
	$descricao		= '';
	$codUniMed		= '';
	$referencia		= '';
	$descricaoCom	= '';
	$ativo			= 'checked';
	$codNcm			= '';
	$codSubgrupo	= '';
	$margemLucro	= '';
	$codTipoPreço	= '';
	$valorVenda		= '';
	$desconto		= '';
	$observacao		= '';
	
}

#################################################################################
## Url Voltar
#################################################################################
$urlVoltar			= ROOT_URL."/Est/produtoLis.php?id=".$id;

#################################################################################
## Url Novo
#################################################################################
$uid = \Zage\App\Util::encodeUrl('_codMenu_='.$_codMenu_.'&_icone_='.$_icone_.'&codProduto=');
$urlNovo			= ROOT_URL."/Est/produtoAlt.php?id=".$uid;

#################################################################################
## Select das unidades de medida
#################################################################################
try {
	$aUnidades		= $em->getRepository('Entidades\ZgestUnidadeMedida')->findBy(array('codOrganizacao' => $system->getCodOrganizacao()));
	$oUnidades		= $system->geraHtmlCombo($aUnidades,	'CODIGO', 'DESCRICAO',	$codUniMed, 		null);
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
$tpl->set('URL_FORM'				,$_SERVER['SCRIPT_NAME']);
$tpl->set('URLVOLTAR'				,$urlVoltar);
$tpl->set('URLNOVO'					,$urlNovo);
$tpl->set('ID'						,$id);
$tpl->set('COD_PRODUTO'				,$codProduto);
$tpl->set('ATIVO'					,$ativo);
$tpl->set('DESCRICAO'				,$descricao);
$tpl->set('UNIDADES'				,$oUnidades);
$tpl->set('DESCRICAO_COMPLETA'		,$descricaoCom);
$tpl->set('NCM'						,$codNcm);
$tpl->set('REFERENCIA'				,$referencia);
$tpl->set('SUBGRUPO'				,$codSubgrupo);
$tpl->set('APP_BS_TA_MINLENGTH'		,\Zage\Adm\Parametro::getValor('APP_BS_TA_MINLENGTH'));
$tpl->set('APP_BS_TA_ITENS'			,\Zage\Adm\Parametro::getValor('APP_BS_TA_ITENS'));
$tpl->set('APP_BS_TA_TIMEOUT'		,\Zage\Adm\Parametro::getValor('APP_BS_TA_TIMEOUT'));
$tpl->set('DP'						,\Zage\App\Util::getCaminhoCorrespondente(__FILE__,\Zage\App\ZWS::EXT_DP,\Zage\App\ZWS::CAMINHO_RELATIVO));
$tpl->set('IC'						,$_icone_);
$tpl->set('COD_MENU'				,$_codMenu_);

#################################################################################
## Por fim exibir a página HTML
#################################################################################
$tpl->show();
