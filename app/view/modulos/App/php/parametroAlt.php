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
## Resgata as informações do banco
#################################################################################
if ($codParametro) {
	try {
		$info			= $em->getRepository('Entidades\ZgappParametro')->findOneBy(array('codigo' => $codParametro));
		$infoVal		= $em->getRepository('Entidades\ZgappParametroTipoValor')->findBy(array('codParametro' => $codParametro));
		
	} catch (\Exception $e) {
		\Zage\App\Erro::halt($e->getMessage());
	}
	$codModulo		= $info->getCodModulo()->getCodigo();
	$parametro		= $info->getParametro();
	$tipo			= $info->getCodTipo()->getCodigo();
	$uso			= $info->getCodUso()->getCodigo();
	$secao			= ($info->getCodSecao()) ? $info->getCodSecao()->getCodigo() : null;
	$descSecao		= ($info->getCodSecao()) ? $info->getCodSecao()->getNome() : null;
	$descricao		= $info->getDescricao();
	$valorPadrao	= $info->getValorPadrao();
	$tamanho		= $info->getTamanho();
	$obrigatorio	= ($info->getIndObrigatorio() 	== 1) ? "checked" : null;
	
	if ($infoVal) {
		$valores = null;
		foreach ($infoVal as $val) {
			$valores		.= $val->getValor().',';
		}
		$valores	= substr($valores,0,-1);
	}else{
		$valores = null;
	}
}else{
	
	$codModulo		= "";
	$parametro		= "";
	$tipo			= "";
	$uso			= "";
	$secao			= "";
	$descricao		= "";
	$valorPadrao	= "";
	$valores		= "";
	$tamanho		= "";
	$descSecao		= "";
	$obrigatorio	= 'checked';
}

#################################################################################
## Url Voltar
#################################################################################
$urlVoltar			= ROOT_URL."/App/parametroLis.php?id=".$id;

#################################################################################
## Url Novo
#################################################################################
$uid 				= \Zage\App\Util::encodeUrl('_codMenu_='.$_codMenu_.'&_icone_='.$_icone_.'&codParametro=');
$urlNovo			= ROOT_URL."/App/parametroAlt.php?id=".$uid;

#################################################################################
## Select de Tipos
#################################################################################
try {
	$aTipo	= $em->getRepository('Entidades\ZgappParametroTipo')->findAll();
	$oTipo	= $system->geraHtmlCombo($aTipo, 'CODIGO', 'NOME', $tipo, null);
	
} catch (\Exception $e) {
	\Zage\App\Erro::halt($e->getMessage(),__FILE__,__LINE__);
}

#################################################################################
## Select de Uso
#################################################################################
try {
	$aUso	= $em->getRepository('Entidades\ZgappParametroUso')->findAll();
	$oUso	= $system->geraHtmlCombo($aUso, 'CODIGO', 'NOME', $uso, null);

} catch (\Exception $e) {
	\Zage\App\Erro::halt($e->getMessage(),__FILE__,__LINE__);
}

#################################################################################
## Select dos módulos
#################################################################################
try {
	$aModulo	= $em->getRepository('Entidades\ZgappModulo')->findBy(array(), array('nome' => 'ASC'));
	$oModulo	= $system->geraHtmlCombo($aModulo, 'CODIGO', 'NOME', $codModulo, null);

} catch (\Exception $e) {
	\Zage\App\Erro::halt($e->getMessage(),__FILE__,__LINE__);
}

#################################################################################
## Select do seção
#################################################################################
try {
	$aSecao		= $em->getRepository('Entidades\ZgappParametroSecao')->findBy(array(), array('nome' => 'ASC'));
	$oSecao		= $system->geraHtmlCombo($aSecao, 'CODIGO', 'NOME', $secao, null);

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
$tpl->set('URL_FORM'			,$_SERVER['SCRIPT_NAME']);
$tpl->set('URLVOLTAR'			,$urlVoltar);
$tpl->set('URLNOVO'				,$urlNovo);
$tpl->set('ID'					,$id);
$tpl->set('COD_PARAMETRO'		,$codParametro);
$tpl->set('PARAMETRO'			,$parametro);
$tpl->set('COD_SECAO'			,$secao);
$tpl->set('SECAO'				,$oSecao);
$tpl->set('DESC_SECAO'			,$descSecao);
$tpl->set('DESCRICAO'			,$descricao);
$tpl->set('VALORES'				,$valores);
$tpl->set('VALOR_PADRAO'		,$valorPadrao);
$tpl->set('TAMANHO'				,$tamanho);
$tpl->set('TIPO'				,$oTipo);
$tpl->set('USO'					,$oUso);
$tpl->set('MODULO'				,$oModulo);
$tpl->set('OBRIGATORIO'			,$obrigatorio);
$tpl->set('DP'					,\Zage\App\Util::getCaminhoCorrespondente(__FILE__,\Zage\App\ZWS::EXT_DP,\Zage\App\ZWS::CAMINHO_RELATIVO));


#################################################################################
## Por fim exibir a página HTML
#################################################################################
$tpl->show();

