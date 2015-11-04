<?php
################################################################################
# Includes
################################################################################
if (defined ( 'DOC_ROOT' )) {
	include_once (DOC_ROOT . 'include.php');
} else {
	include_once ('../include.php');
}

################################################################################
# Resgata a variável ID que está criptografada
################################################################################
if (isset ( $_GET ['id'] )) {
	$id = \Zage\App\Util::antiInjection ( $_GET ["id"] );
} elseif (isset ( $_POST ['id'] )) {
	$id = \Zage\App\Util::antiInjection ( $_POST ["id"] );
} elseif (isset ( $id )) {
	$id = \Zage\App\Util::antiInjection ( $id );
} else {
	\Zage\App\Erro::halt ( 'Falta de Parâmetros' );
}

################################################################################
# Descompacta o ID
################################################################################
\Zage\App\Util::descompactaId ( $id );

################################################################################
# Verifica se o usuário tem permissão no menu
################################################################################
$system->checaPermissao ( $_codMenu_ );

################################################################################
# Select de Formando
################################################################################
try {
	$aFormando = $em->getRepository('Entidades\ZgfinPessoa')->findBy(array('codOrganizacao' => $system->getCodOrganizacao(), 'codTipoPessoa' => 'O','indAtivo' => 1),array('nome' => 'ASC'));
	$oFormando = $system->geraHtmlCombo($aFormando, 'CODIGO', 'NOME', $codFormando, '');
} catch (\Exception $e) {
	\Zage\App\Erro::halt($e->getMessage(),__FILE__,__LINE__);
}

################################################################################
# Select de Forma de Pagamento
################################################################################
try {
	$aFormaPag = $em->getRepository('Entidades\ZgfinFormaPagamento')->findBy(array(),array('descricao' => 'ASC'));
	$oFormaPag = $system->geraHtmlCombo($aFormaPag, 'CODIGO', 'DESCRICAO', $codFormaPag, '');
} catch (\Exception $e) {
	\Zage\App\Erro::halt($e->getMessage(),__FILE__,__LINE__);
}

################################################################################
# Select de Conta Recebimento
################################################################################
try {
	$aConta = $em->getRepository('Entidades\ZgfinConta')->findBy(array(),array('nome' => 'ASC'));
	$oConta = $system->geraHtmlCombo($aConta, 'CODIGO', 'NOME', $codConta, '');
} catch (\Exception $e) {
	\Zage\App\Erro::halt($e->getMessage(),__FILE__,__LINE__);
}

################################################################################
# Url Voltar
################################################################################
$urlVoltar = ROOT_URL . "/Fmt/conviteExtraAlunosLis.php?id=" . $id;

################################################################################
# Url Novo
################################################################################
$uid = \Zage\App\Util::encodeUrl ( '_codMenu_=' . $_codMenu_ . '&_icone_=' . $_icone_ );
$urlNovo = ROOT_URL . "/Fmt/conviteExtraVenda.php?id=" . $uid;

################################################################################
# Carregando o template html
################################################################################
$tpl = new \Zage\App\Template ();
$tpl->load ( \Zage\App\Util::getCaminhoCorrespondente ( __FILE__, \Zage\App\ZWS::EXT_HTML ) );

################################################################################
# Define os valores das variáveis
################################################################################
$tpl->set ( 'URL_FORM'			   , $_SERVER ['SCRIPT_NAME'] );
$tpl->set ( 'URLVOLTAR'			   , $urlVoltar );
$tpl->set ( 'URLNOVO'		 	   , $urlNovo );
$tpl->set ( 'ID'				   , $id );
$tpl->set ( 'COD_CONV_VENDA'	   , $codConvVenda);
$tpl->set ( 'COD_FORMANDO'	   	   , $oFormando);
$tpl->set ( 'COD_FORMA_PAG'	   	   , $oFormaPag);
$tpl->set ( 'COD_CONTA'	   		   , $oConta);

$tpl->set ( 'IC'				   ,$_icone_);
$tpl->set ( 'DP', \Zage\App\Util::getCaminhoCorrespondente ( __FILE__, \Zage\App\ZWS::EXT_DP, \Zage\App\ZWS::CAMINHO_RELATIVO ) );

################################################################################
# Por fim exibir a página HTML
################################################################################
$tpl->show ();

