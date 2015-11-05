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
global $em,$system,$tr,$log;

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

$log->info("POST ORC ACEITE: ".serialize($_POST));

#################################################################################
## Resgata as variáveis postadas
#################################################################################
if (isset($_POST['codVersaoOrc'])) 		$codVersaoOrc			= \Zage\App\Util::antiInjection($_POST['codVersaoOrc']);

#################################################################################
## Verificar parâmetro obrigatório
#################################################################################
if (!isset($codVersaoOrc)) \Zage\App\Erro::halt('Falta de Parâmetros 1');

#################################################################################
## Validações, verificar se a versão do orcamento existe
#################################################################################
$orcamento			= $em->getRepository('Entidades\ZgfmtOrcamento')->findOneBy(array('codigo' => $codVersaoOrc));

if (!$orcamento) {
	die ('1'.\Zage\App\Util::encodeUrl('||'.htmlentities($tr->trans("Orçamento não encontrado!"))));
}


#################################################################################
## Salvar no banco
#################################################################################
try {
	$oOrganizacao	= $em->getRepository('Entidades\ZgadmOrganizacao')->findOneBy(array('codigo' => $system->getCodOrganizacao()));
	$oUser			= $em->getRepository('Entidades\ZgsegUsuario')->findOneBy(array('codigo' => $system->getCodUsuario()));
	
	$orcamento->setIndAceite(1);
	$orcamento->setCodUsuarioAceite($oUser);
	$oOrc->setCodOrganizacao($oOrganizacao);
	$oOrc->setCodPlanoOrc($oPlanoOrc);
	$oOrc->setCodUsuario($oUser);
	$oOrc->setDataCadastro(new \DateTime("now"));
	$oOrc->setDataConclusao($oDataConc);
	//$oOrc->setIndAceite($indAceite);
	$oOrc->setNumMeses($numMeses);
	$oOrc->setQtdeConvidados($numConvidado);
	$oOrc->setQtdeFormandos($numFormando);
	$oOrc->setTaxaSistema($taxaSistema);
	$oOrc->setVersao($versao);
 	 
 	$em->persist($oOrc);
 	
 	#################################################################################
 	## Itens
 	#################################################################################
 	foreach ($codItemSel as $codItem => $item) {
 		$oItem			= $em->getRepository('Entidades\ZgfmtPlanoOrcItem')->findOneBy(array('codigo' => $codItem));
 		$valor			= \Zage\App\Util::to_float($aValor[$codItem]);
 		$qtde			= (int) $aQtde[$codItem];
 		$oOrcItem		= new \Entidades\ZgfmtOrcamentoItem();
 		$oOrcItem->setCodItem($oItem);
 		$oOrcItem->setCodOrcamento($oOrc);
 		$oOrcItem->setIndHabilitado(1);
 		$oOrcItem->setObservacao($aObs[$codItem]);
 		$oOrcItem->setQuantidade($qtde);
 		$oOrcItem->setValorUnitario($valor);
 		$em->persist($oOrcItem);
 	}
 	
	
	#################################################################################
 	## Salvar as informações
 	#################################################################################
	$em->flush();
	$em->clear();
 	
} catch (\Exception $e) {
 	$log->err("Erro ao salvar o Orçamento:". $e->getTraceAsString());
 	//throw new \Exception("Erro ao salvar o Orçamento. Uma mensagem de depuração foi salva em log, entre em contato com os administradores do sistema !!!");
	echo '1'.\Zage\App\Util::encodeUrl('||'.htmlentities($e->getMessage()));
 	exit;
}
 
echo '0'.\Zage\App\Util::encodeUrl('|'.$oOrc->getCodigo());