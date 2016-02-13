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
		$oItens		= $em->getRepository('Entidades\ZgfmtItemOrcContratoFornec')->findBy(array('codItemContrato' => $oItemContrato->getCodigo()));
	}else{
		$oItens		= null;
	}
	
	
	#################################################################################
	## Resgata os fornecedores
	#################################################################################
	$aPessoas			= \Zage\Fin\Pessoa::busca($system->getCodOrganizacao(),null,false,true,false);
	
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
	$qtde			= $oItens[$i]->getQuantidade();
	$codFornecedor	= $oItens[$i]->getCodPessoa();
	$fornecedor		= ($oItens[$i]->getCodPessoa()) ? $oItens[$i]->getCodPessoa()->getNome() : null;
	$data			= ($oItens[$i]->getDataOperacao()) ? $oItens[$i]->getDataOperacao()->format($system->config["data"]["dateFormat"]) : null;
	$codItem		= $oItens[$i]->getCodigo();
	$qtdeContratada	+= $qtde;
	$tabItens		.= '<tr><td class="center">'.$qtde.'<input type="hidden" name="aQtde[]" value="'.$qtde.'"></td><td class="">'.$fornecedor.'<input type="hidden" name="aCodPessoa[]" value="'.$codFornecedor.'"></td><td class="center">'.$data.'<input type="hidden" name="aData[]" value="'.$data.'"></td><td class="center"><span class="center" zgdelete onclick="delRowConForCad($(this));"><i class="fa fa-trash bigger-150 red"></i></span><input type="hidden" name="codItem[]" value="'.$codItem.'"></td></tr>';
}
$falta			= $qtdeEvento - $qtdeContratada;



#################################################################################
## Select dos Fornecedores
#################################################################################
try {
	$oPessoas	= $system->geraHtmlCombo($aPessoas,	'CODIGO', 'NOME',	null, null);
} catch (\Exception $e) {
	\Zage\App\Erro::halt($e->getMessage(),__FILE__,__LINE__);
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
$tpl->set('FORNECEDORES'			,$oPessoas);
$tpl->set('QTDE_CONTRATADA'			,$qtdeContratada);
$tpl->set('QTDE_EVENTO'				,$qtdeEvento);
$tpl->set('QTDE_FALTA'				,$falta);
$tpl->set('DP'						,\Zage\App\Util::getCaminhoCorrespondente(__FILE__,\Zage\App\ZWS::EXT_DP,\Zage\App\ZWS::CAMINHO_RELATIVO));
$tpl->set('IC'						,$_icone_);
$tpl->set('COD_MENU'				,$_codMenu_);
$tpl->set('FORMATO_DATA'			,$system->config["data"]["jsDateFormat"]);

#################################################################################
## Por fim exibir a página HTML
#################################################################################
$tpl->show();
