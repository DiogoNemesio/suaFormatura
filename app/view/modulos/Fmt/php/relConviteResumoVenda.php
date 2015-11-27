<?php
#################################################################################
## Includes
#################################################################################
if (defined('DOC_ROOT')) {
	include_once(DOC_ROOT . 'include.php');
}else{
	include_once('./include.php');
}

#################################################################################
## Variáveis globais
#################################################################################
global $system,$em,$tr,$_user;


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
## Resgata a url desse script
#################################################################################
$url		= ROOT_URL . "/Fmt/". basename(__FILE__)."?id=".$id;

try {
	$rsm 	= new Doctrine\ORM\Query\ResultSetMapping();
	$query 	= $em->createNativeQuery("
		SELECT V.COD_FORMANDO, SUM(V.VALOR_TOTAL) VALOR_TOTAL, E.COD_TIPO_EVENTO, SUM(I.QUANTIDADE) QTDE, I.VALOR_UNITARIO
			FROM `ZGFMT_CONVITE_EXTRA_VENDA` V
			LEFT OUTER JOIN `ZGFIN_CONTA_RECEBER` C ON (V.COD_TRANSACAO = C.COD_TRANSACAO)
			LEFT OUTER JOIN `ZGFMT_CONVITE_EXTRA_VENDA_ITEM` I ON (V.CODIGO = I.COD_VENDA)
			LEFT OUTER JOIN `ZGFMT_EVENTO` E ON (I.COD_EVENTO = E.CODIGO)
			GROUP BY E.COD_TIPO_EVENTO, V.COD_FORMANDO", $rsm);
	//$query->setParameter('codOrg'	, $system->getCodOrganizacao());
	//$query->setParameter('codCat'	, $catMen);
	//$query->setParameter('dataVenc'	, $dtVenc);
	
	
	$vendas = $query->getResult();
	$log->info($vendas);
	
} catch (\Exception $e) {
	\Zage\App\Erro::halt($e->getMessage());
}

#################################################################################
## Gerar a url de histórico de pagamentos
#################################################################################
$urlVoltar				= ROOT_URL."/Fmt/conviteExtraTransf.php?id=".$id;

#################################################################################
## Carregando o template html
#################################################################################
$tpl	= new \Zage\App\Template();
$tpl->load(\Zage\App\Util::getCaminhoCorrespondente(__FILE__, \Zage\App\ZWS::EXT_HTML));

#################################################################################
## Define os valores das variáveis
#################################################################################
$tpl->set('IC'				,$_icone_);
$tpl->set('ID'				,$id);
$tpl->set('URL_VOLTAR'		,$urlVoltar);

#################################################################################
## Por fim exibir a página HTML
#################################################################################
$tpl->show();
