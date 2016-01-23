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
global $em,$system;

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
## Verificar parâmetro obrigatório
#################################################################################
if (!isset($codConta)) \Zage\App\Erro::halt('Falta de Parâmetros 2');

#################################################################################
## Resgata as informações do banco
#################################################################################
if (!empty($codConta)) {
	try {
		$info = $em->getRepository('Entidades\ZgfinConta')->findOneBy(array('codOrganizacao' => $system->getCodOrganizacao(), 'codigo' => $codConta));
	} catch (\Exception $e) {
		\Zage\App\Erro::halt($e->getMessage());
	}

	$codTipo		= ($info->getCodTipo() != null) ? $info->getCodTipo()->getCodigo() : null;
	$nome			= $info->getNome();
	$saldoInicial	= \Zage\App\Util::toPHPNumber($info->getSaldoInicial());
	$dataInicial	= ($info->getDataInicial() != null) ? $info->getDataInicial()->format($system->config["data"]["dateFormat"]) : null;
	$codAgencia		= ($info->getcodAgencia() != null) ? $info->getcodAgencia()->getCodigo() : null;
	$ccorrente		= $info->getCcorrente();
	$ccorrenteDV	= $info->getCcorrenteDV();
	$ativa			= ($info->getIndAtiva()	== 1) ? "checked" : null;
	$codCarteira	= ($info->getcodCarteira() != null) ? $info->getcodCarteira()->getCodigo() : null;
	$valorBoleto	= ($info->getValorBoleto()) ? \Zage\App\Util::formataDinheiro($info->getValorBoleto()) : null;
	$valorJuros		= ($info->getValorJuros()) ? $info->getValorJuros() : 0;
	$valorMora		= ($info->getValorMora()) ? $info->getValorMora() : 0;
	$pctJuros		= ($info->getPctJuros()) ? $info->getPctJuros() : 0;
	$pctMora		= ($info->getPctMora()) ? $info->getPctMora() : 0;
	$instrucao		= $info->getInstrucao();
	$indCheckAlt	= 1;
	$codCedente		= $info->getCodigoCedente();
	
	// Botão de seleção de tipo do juros
	if ($valorJuros > 0){
		$codTipoJuros = 'V';
	}elseif ($pctJuros >= 0){
		$codTipoJuros = 'P';
	}else{
		$codTipoJuros = 'P';
	}
	
	// Botão de seleção de tipo de mora
	if ($valorMora > 0){
		$codTipoMora = 'V';
	}elseif ($pctMora >= 0){
		$codTipoMora = 'P';
	}else{
		$codTipoMora = 'P';
	}
	
	// Saldo Inicial
	if ($saldoInicial > 0){
		$saldoInicial = \Zage\App\Util::formataDinheiro($saldoInicial);
	}else{
		$saldoInicial = null;
	}

}else{
	$codTipo		= null;
	$nome			= null;
	$saldoInicial	= null;
	$dataInicial	= null;
	$codAgencia		= null;
	$ccorrente		= null;
	$ccorrenteDV	= null;
	$ativa			= "checked";
	$codCarteira	= null;
	$codTipoMora	= 'P';
	$codTipoJuros	= 'P';
	$valorBoleto	= null;
	$valorJuros		= null;
	$valorMora		= null;
	$pctJuros		= null;
	$pctMora		= null;
	$instrucao		= null;
	$indCheckAlt	= null;
	$codCedente		= null;
}

#################################################################################
## Select do tipo de Conta
#################################################################################
try {
	$aConta		= $em->getRepository('Entidades\ZgfinContaTipo')->findAll();
	$oConta		= $system->geraHtmlCombo($aConta,	'CODIGO', 'DESCRICAO',	$codTipo, 		null);
} catch (\Exception $e) {
	\Zage\App\Erro::halt($e->getMessage(),__FILE__,__LINE__);
}

#################################################################################
## Select de agências
#################################################################################
try {
	$aAgencia	= $em->getRepository('Entidades\ZgfinAgencia')->findBy(array('codOrganizacao' => $system->getCodOrganizacao()));
	$oAgencia	= $system->geraHtmlCombo($aAgencia,	'CODIGO', 'NOME',	$codAgencia, '');
} catch (\Exception $e) {
	\Zage\App\Erro::halt($e->getMessage(),__FILE__,__LINE__);
}

$arrayBanco	= array();
for ($i = 0; $i < sizeof($aAgencia); $i++) {
	$arrayBanco[$aAgencia[$i]->getCodigo()] = $aAgencia[$i]->getCodBanco()->getCodBanco();
}

#################################################################################
## Url Voltar
#################################################################################
$urlVoltar			= ROOT_URL."/Fin/contaLis.php?id=".$id;

#################################################################################
## Url Novo
#################################################################################
$uid 				= \Zage\App\Util::encodeUrl('_codMenu_='.$_codMenu_.'&_icone_='.$_icone_.'&codConta=');
$urlNovo			= ROOT_URL."/Fin/contaAlt.php?id=".$uid;

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
$tpl->set('COD_CONTA'			,$codConta);
$tpl->set('CONTAS'				,$oConta);
$tpl->set('NOME'				,$nome);
$tpl->set('SALDO_INICIAL'		,$saldoInicial);
$tpl->set('DATA_INICIAL'		,$dataInicial);
$tpl->set('ATIVA'				,$ativa);
$tpl->set('COD_AGENCIA'			,$codAgencia);
$tpl->set('AGENCIAS'			,$oAgencia);
$tpl->set('CCORRENTE'			,$ccorrente);
$tpl->set('CCORRENTEDV'			,$ccorrenteDV);
$tpl->set('COD_CARTEIRA'		,$codCarteira);
$tpl->set('VALOR_BOLETO'		,$valorBoleto);
$tpl->set('VALOR_JUROS'			,\Zage\App\Util::formataDinheiro($valorJuros));
$tpl->set('VALOR_MORA'			,\Zage\App\Util::formataDinheiro($valorMora));
$tpl->set('COD_TIPO_JUROS'		,$codTipoJuros);
$tpl->set('COD_TIPO_MORA'		,$codTipoMora);
$tpl->set('PCT_JUROS'			,\Zage\App\Util::formataDinheiro($pctJuros));
$tpl->set('PCT_MORA'			,\Zage\App\Util::formataDinheiro($pctMora));
$tpl->set('INSTRUCAO'			,$instrucao);
$tpl->set('COD_CEDENTE'			,$codCedente);
$tpl->set('ARRAY_BANCO'			,json_encode($arrayBanco));
$tpl->set('IND_CHECK_ALT'		,$indCheckAlt);
$tpl->set('DP'					,\Zage\App\Util::getCaminhoCorrespondente(__FILE__,\Zage\App\ZWS::EXT_DP,\Zage\App\ZWS::CAMINHO_RELATIVO));

#################################################################################
## Por fim exibir a página HTML
#################################################################################
$tpl->show();

