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
global $em,$system,$tr;

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
## Verifica se o parâmetro foi informado
#################################################################################
if (!isset($codItem))	die("Falta de parâmetros 1");

#################################################################################
## Resgata as informações do banco
#################################################################################
try {
	
	$oItemOrc		= $em->getRepository('Entidades\ZgfmtOrcamentoItem')->findOneBy(array('codigo' => $codItem));
	if (!$oItemOrc)	die("Falta de parâmetros 2");
	$oItemContrato	= $em->getRepository('Entidades\ZgfmtItemOrcContrato')->findOneBy(array('codOrganizacao' => $system->getCodOrganizacao(), 'codItemOrcamento' => $codItem));
	if ($oItemContrato)	{
		$oItens			= $em->getRepository('Entidades\ZgfmtItemOrcContratoFornec')->findBy(array('codItemContrato' => $oItemContrato->getCodigo()));
	}else{
		$oItens			= null;
	}
	
} catch (\Exception $e) {
	\Zage\App\Erro::halt($e->getMessage());
}

#################################################################################
## Montar a tabela de fornecedores já contratados
#################################################################################
$tabItens		= "";
$qtdeContratada	= 0;
$qtdeEvento		= $oItemOrc->getQuantidade();
for ($i = 0; $i < sizeof($oItens); $i++) {
	$tabItens	.= "";
	$qtdeContratada	+= $oItens[$i]->getQuantidade();
}


#################################################################################
## Título da página
#################################################################################
$titulo			= "Contrato de fornecedores para o item: <b>".$oItemOrc->getCodItem()->getItem()."</b>";


#################################################################################
## Carregando o template html
#################################################################################
$tpl	= new \Zage\App\Template();
$tpl->load(\Zage\App\Util::getCaminhoCorrespondente(__FILE__, \Zage\App\ZWS::EXT_HTML));

#################################################################################
## Define os valores das variáveis
#################################################################################
$tpl->set('URL_FORM'				,$_SERVER['SCRIPT_NAME']);
$tpl->set('ID'						,$id);
$tpl->set('TITULO'					,$titulo);
$tpl->set('TAB_ITENS'				,$tabItens);
$tpl->set('QTDE_CONTRATADA'			,$qtdeContratada);
$tpl->set('QTDE_EVENTO'				,$qtdeEvento);
$tpl->set('DP'						,\Zage\App\Util::getCaminhoCorrespondente(__FILE__,\Zage\App\ZWS::EXT_DP,\Zage\App\ZWS::CAMINHO_RELATIVO));
$tpl->set('IC'						,$_icone_);
$tpl->set('COD_MENU'				,$_codMenu_);

#################################################################################
## Por fim exibir a página HTML
#################################################################################
$tpl->show();
