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
global $em,$tr,$system;


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
if (!isset($codFormando)) 		{
	\Zage\App\Erro::halt($tr->trans('Falta de Parâmetros').' (COD_USUARIO)');
}

if (!isset($codRifa)) 		{
	\Zage\App\Erro::halt($tr->trans('Falta de Parâmetros').' (COD_RIFA)');
}
#################################################################################
## Resgata as informações do banco
#################################################################################
$info 			= $em->getRepository('Entidades\ZgfmtRifa')->findOneBy(array('codigo' => $codRifa));
$infoUsu		= $em->getRepository('Entidades\ZgsegUsuario')->findOneBy(array('codigo' => $codFormando));
$infoVenda		= $em->getRepository('Entidades\ZgfmtRifaFormando')->findOneBy(array('codRifa' => $codRifa, 'codFormando' => $codFormando));

if (!$info)		\Zage\App\Erro::halt($tr->trans('Rifa não encontrada').' (COD_RIFA)');
if (!$infoUsu)	\Zage\App\Erro::halt($tr->trans('Formando não encontrado').' (COD_RIFA)');

#################################################################################
## Grupo de Associação da rifa com a conta
#################################################################################
$codGrpAssociacao	= "RIFA_".$info->getCodigo(). "_".$codFormando;

$infoVendas 	= $em->getRepository('Entidades\ZgfmtRifaNumero')->findBy(array('codRifa' => $codRifa, 'codFormando' => $codFormando));
$infoVendasNum	= sizeof($infoVendas);
$qtdePagar 		= ($infoVendasNum < $info->getQtdeObrigatorio()) ? $info->getQtdeObrigatorio() : $infoVendasNum;
$qtdeVendida	= ($infoVenda) ? $infoVenda->getQtdeVendida() : $qtdePagar;
$totalReceber	= $qtdeVendida * $info->getValorUnitario();

#################################################################################
## Verificar se a conta já foi gerada
#################################################################################
$oConta			= $em->getRepository('Entidades\ZgfinContaReceber')->findOneBy(array('codOrganizacao' => $system->getCodOrganizacao(),'codGrupoAssociacao' => $codGrpAssociacao));
if ($oConta)	{
	$totalPago		= \Zage\Fin\ContaReceber::getValorJaRecebido($oConta->getCodigo());
	$valorReceber	= $totalReceber - $totalPago;
}else{
	$totalPago		= 0;
	$valorReceber	= $totalReceber;
}

#################################################################################
## Verificar as informações da rifa
#################################################################################
if ($info->getIndRifaEletronica() == 1 || $oConta){
	$readonly = 'readonly';
}else{
	$readonly = '';
}


#################################################################################
## Urls
#################################################################################
$vid				= \Zage\App\Util::encodeUrl('_codMenu_='.$_codMenu_.'&_icone_='.$_icone_.'&codRifa='.$codRifa.'&url='.$url);
$urlVoltar			= ROOT_URL . "/Fmt/rifaFin.php?id=".$vid;

#################################################################################
## Carregando o template html
#################################################################################
$tpl	= new \Zage\App\Template();
$tpl->load(\Zage\App\Util::getCaminhoCorrespondente(__FILE__, \Zage\App\ZWS::EXT_HTML));

#################################################################################
## Select da Forma de Pagamento
#################################################################################
try {
	$aFormaPag	= $em->getRepository('Entidades\ZgfinFormaPagamento')->findBy(array(),array('descricao' => 'ASC'));
	$oFormaPag	= $system->geraHtmlCombo($aFormaPag,	'CODIGO', 'DESCRICAO',	'DH', null);
} catch (\Exception $e) {
	\Zage\App\Erro::halt($e->getMessage(),__FILE__,__LINE__);
}

#################################################################################
## Select da Conta
#################################################################################
try {
	$aConta		= $em->getRepository('Entidades\ZgfinConta')->findBy(array('codOrganizacao' => $system->getCodOrganizacao()),array('nome' => 'ASC'));
	$oConta		= $system->geraHtmlCombo($aConta,	'CODIGO', 'NOME',	null, null);
} catch (\Exception $e) {
	\Zage\App\Erro::halt($e->getMessage(),__FILE__,__LINE__);
}


#################################################################################
## Define os valores das variáveis
#################################################################################
$tpl->set('ID'					,$id);
$tpl->set('TITULO'				,'Receber pagamento');

$tpl->set('COD_USUARIO'			,$codFormando);

$tpl->set('NOME_RIFA'			,$info->getNome());
$tpl->set('VALOR_RIFA'			,$info->getValorUnitario());
$tpl->set('QTDE_OBRI'			,$info->getQtdeObrigatorio());
$tpl->set('IND_ELETRONICA'		,$info->getIndRifaEletronica());
$tpl->set('QTDE_PAGAR'			,$qtdePagar);
$tpl->set('QTDE_VENDIDA'		,$qtdeVendida);
$tpl->set('VALOR_RECEBER'		,\Zage\App\Util::formataDinheiro($valorReceber));
$tpl->set('MAX_VALOR'			,$valorReceber);
$tpl->set('VALOR_TOTAL'			,$totalReceber);
$tpl->set('VALOR_PAGO'			,$totalPago);
$tpl->set('NOME_FORMANDO'		,$infoUsu->getNome());
$tpl->set('COD_RIFA'			,$codRifa);

$tpl->set('QTDE_GERADA'			,$qtdePagar);
$tpl->set('DISABLED'			,null);
$tpl->set('READONLY'			,$readonly);
$tpl->set('TEXTO'				,null);

$tpl->set('FORMAS_PAG'			,$oFormaPag);
$tpl->set('CONTAS'				,$oConta);

$tpl->set('URL_VOLTAR'			,$urlVoltar);
$tpl->set('DP_MODAL'			,\Zage\App\Util::getCaminhoCorrespondente(__FILE__,\Zage\App\ZWS::EXT_DP,\Zage\App\ZWS::CAMINHO_RELATIVO));

#################################################################################
## Por fim exibir a página HTML
#################################################################################
$tpl->show();

