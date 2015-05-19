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
## Resgata os parâmetros passados
#################################################################################
if (isset($_GET['codEndereco']))		$codEndereco		= \Zage\App\Util::antiInjection($_GET['codEndereco']);

#################################################################################
## Resgata as informações do banco
#################################################################################
if ($codEndereco) {
	try {
		$info			= $em->getRepository('Entidades\ZgdocEndereco')->findOneBy(array('codigo' => $codEndereco));
		
	} catch (\Exception $e) {
		\Zage\App\Erro::halt($e->getMessage());
	}

	$nome			= $info->getNome();
	$tipo			= $info->getCodTipo()->getCodigo();
	$ativo			= ($info->getIndAtivo()			== 1) ? "checked" : null;
	$bloqueado		= ($info->getIndBloqueado()		== 1) ? "checked" : null;
	$rua			= $info->getRua();
	$estante		= $info->getEstante();
	$prateleira		= $info->getPrateleira();
	$coluna			= $info->getColuna();
	$readonly 		= 'readonly';
	$disabled		= 'disabled';
		
}else{
	$nome			= '';
	$tipo			= '';
	$ativo			= 'checked';
	$bloqueado		= '';
	$rua			= '';
	$estante		= '';
	$prateleira		= '';
	$coluna			= '';
	$readonly 		= '';
	$disabled		= '';
}

#################################################################################
## Url Voltar
#################################################################################
$urlVoltar			= ROOT_URL."/Doc/enderecoLis.php?id=".$id;

#################################################################################
## Url Novo
#################################################################################
$uid 				= \Zage\App\Util::encodeUrl('_codMenu_='.$_codMenu_.'&_icone_='.$_icone_.'&codLocal='.$codLocal.'&codEndereco=');
$urlNovo			= ROOT_URL."/Doc/enderecoAlt.php?id=".$uid;
$urlGerar			= ROOT_URL."/Doc/enderecoGer.php?id=".$uid;

#################################################################################
## Select de Tipos
#################################################################################
try {
	$aTipo	= $em->getRepository('Entidades\ZgdocEnderecoTipo')->findAll();
	$oTipo	= $system->geraHtmlCombo($aTipo, 'CODIGO', 'DESCRICAO', $tipo, null);
	
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
$tpl->set('URLGERAR'			,$urlGerar);
$tpl->set('ID'					,$id);
$tpl->set('COD_ENDERECO'		,$codEndereco);
$tpl->set('COD_LOCAL'			,$codLocal);
$tpl->set('NOME'				,$nome);
$tpl->set('ATIVO'				,$ativo);
$tpl->set('BLOQUEADO'			,$bloqueado);
$tpl->set('RUA'					,$rua);
$tpl->set('ESTANTE'				,$estante);
$tpl->set('PRATELEIRA'			,$prateleira);
$tpl->set('COLUNA'				,$coluna);
$tpl->set('READONLY'			,$readonly);
$tpl->set('DISABLED'			,$disabled);
$tpl->set('TIPOS'				,$oTipo);
$tpl->set('DP'					,\Zage\App\Util::getCaminhoCorrespondente(__FILE__,\Zage\App\ZWS::EXT_DP,\Zage\App\ZWS::CAMINHO_RELATIVO));


#################################################################################
## Por fim exibir a página HTML
#################################################################################
$tpl->show();

