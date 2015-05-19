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
# Resgata as informações do banco
################################################################################
if ($codFuncionario) {
	try {
		$info = $em->getRepository ( 'Entidades\ZgrhuFuncionario' )->findOneBy (array ('codigo' => $codFuncionario));
	} catch ( \Exception $e ) {
		\Zage\App\Erro::halt ( $e->getMessage () );
	}
	
	$codPessoa		 = ($info->getPessoa()) ? $info->getPessoa()->getCodigo() : null;
	$codFuncao		 = ($info->getFuncao()) ? $info->getFuncao()->getCodigo() : null;
	$codSituacao	 = ($info->getCodSituacao()) ? $info->getCodSituacao()->getCodigo() : null;
	$codTipo		 = ($info->getCodTipo()) ? $info->getCodTipo()->getCodigo() : null;
	
	$chapa			 = ($info->getChapa()) ? $info->getChapa() : null;
	$jornada		 = ($info->getJornada()) ? $info->getJornada() : null;
	$dataAdmissao	 = ($info->getDataAdmissao() != null) ? $info->getDataAdmissao()->format($system->config["data"]["dateFormat"]) : null;
	$codTipoAdmissao = ($info->getCodTipoAdmissao()) ? $info->getCodTipoAdmissao() : null;
	$motivoAdmissao  = ($info->getMotivoAdmissao()) ? $info->getMotivoAdmissao() : null;
	$dataPrazoContr	 = ($info->getPrazoContraDeterminado() != null) ? $info->getPrazoContraDeterminado()->format($system->config["data"]["dateFormat"]) : null;
	$prazoExp		 = ($info->getPrazoExperiencia() != null) ? $info->getPrazoExperiencia()->format($system->config["data"]["dateFormat"]) : null;
	$salario		 = ($info->getSalario()) ? $info->getSalario() : null;
	$dataDemissao	 = ($info->getDataDemissao() != null) ? $info->getDataDemissao()->format($system->config["data"]["dateFormat"]) : null;
	
} else {
	
	///$codPessoa  	 	 = null;
	$codFuncao			 = null;
	$codSituacao  	 	 = null;
	$codTipo			 = null;
	
	$chapa		  	 	 = null;
	$jornada			 = null;
	$dataAdmissao  	 	 = null;
	$codTipoAdmissao	 = null;
	$motivoAdmissao 	 = null;
	$dataPrazoContr 	 = null;
	$prazoExp			 = null;
	$salario			 = null;
	$dataDemissao		 = null;
}
################################################################################
# Select de Funcao
################################################################################
try {
	$aFuncao = $em->getRepository('Entidades\ZgrhuFuncionarioFuncao')->findBy(array(),array('descricao' => 'ASC'));
	$oFuncao = $system->geraHtmlCombo($aFuncao, 'CODIGO', 'DESCRICAO', $codFuncao, '');
} catch (\Exception $e) {
	\Zage\App\Erro::halt($e->getMessage(),__FILE__,__LINE__);
}
################################################################################
# Select de Funcao
################################################################################
try {
	$aTipo = $em->getRepository('Entidades\ZgrhuFuncionarioTipo')->findBy(array(),array('descricao' => 'ASC'));
	$oTipo = $system->geraHtmlCombo($aTipo, 'CODIGO', 'DESCRICAO', $codTipo, '');
} catch (\Exception $e) {
	\Zage\App\Erro::halt($e->getMessage(),__FILE__,__LINE__);
}
################################################################################
# Select de Situacao
################################################################################
try {
	$aSituacao = $em->getRepository('Entidades\ZgrhuFuncionarioSituacao')->findBy(array(),array('descricao' => 'ASC'));
	$oSituacao = $system->geraHtmlCombo($aSituacao, 'CODIGO', 'DESCRICAO', $codSituacao, '');
} catch (\Exception $e) {
	\Zage\App\Erro::halt($e->getMessage(),__FILE__,__LINE__);
}
################################################################################
# Select de Tipo Admissao
################################################################################
try {
	$aTipoAdmissao = $em->getRepository('Entidades\ZgrhuFuncionarioAdmissaoTipo')->findBy(array(),array('nome' => 'ASC'));
	$oTipoAdmissao = $system->geraHtmlCombo($aTipoAdmissao, 'CODIGO', 'NOME', $codTipoAdmissao, '');
} catch (\Exception $e) {
	\Zage\App\Erro::halt($e->getMessage(),__FILE__,__LINE__);
}
################################################################################
# Select de Motivo Admissao
################################################################################
try {
	$aMotivoAdmissao = $em->getRepository('Entidades\ZgrhuFuncionarioAdmissaoMotivo')->findBy(array(),array('nome' => 'ASC'));
	$oMotivoAdmissao = $system->geraHtmlCombo($aMotivoAdmissao, 'CODIGO', 'NOME', $motivoAdmissao, '');
} catch (\Exception $e) {
	\Zage\App\Erro::halt($e->getMessage(),__FILE__,__LINE__);
}
################################################################################
# Url Voltar
################################################################################
$urlVoltar = ROOT_URL . "/Rhu/pessoaLis.php?id=" . $id;

################################################################################
# Url Novo
################################################################################
$uid = \Zage\App\Util::encodeUrl ( '_codMenu_=' . $_codMenu_ . '&_icone_=' . $_icone_ . '&codFuncionario=' );
$urlNovo = ROOT_URL . "/Rhu/pessoaAdmitir.php?id=" . $uid;

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
$tpl->set ( 'COD_FUNCIONARIO'	   , $codFuncionario);
$tpl->set ( 'COD_PESSOA'		   , $codPessoa);
$tpl->set ( 'COD_FUNCAO'		   , $oFuncao);
$tpl->set ( 'COD_SITUACAO'		   , $oSituacao);
$tpl->set ( 'COD_TIPO'			   , $oTipo);
$tpl->set ( 'CHAPA'				   , $chapa);
$tpl->set ( 'JORNADA'			   , $jornada);
$tpl->set ( 'DATA_ADMISSAO'		   , $dataAdmissao);
$tpl->set ( 'COD_TIPO_ADMISSAO'	   , $oTipoAdmissao);
$tpl->set ( 'COD_MOTIVO_ADMISSAO'  , $oMotivoAdmissao);
$tpl->set ( 'DATA_PRAZO_CONTR'	   , $dataPrazoContr);
$tpl->set ( 'PRAZO_EXP'			   , $prazoExp);
$tpl->set ( 'SALARIO'			   , $salario);
$tpl->set ( 'DATA_DEMISSAO'		   , $dataDemissao);

$tpl->set ( 'DP', \Zage\App\Util::getCaminhoCorrespondente ( __FILE__, \Zage\App\ZWS::EXT_DP, \Zage\App\ZWS::CAMINHO_RELATIVO ) );

################################################################################
# Por fim exibir a página HTML
################################################################################
$tpl->show ();

