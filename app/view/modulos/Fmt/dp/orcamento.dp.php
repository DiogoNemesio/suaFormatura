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
## Resgata os parâmetros passados pelo formulario
#################################################################################
if (isset($_POST['codItemSel']))		$codItemSel			= \Zage\App\Util::antiInjection($_POST['codItemSel']);
if (isset($_POST['taxaSistema'])) 		$taxaSistema		= \Zage\App\Util::antiInjection($_POST['taxaSistema']);
if (isset($_POST['numMeses'])) 			$numMeses			= \Zage\App\Util::antiInjection($_POST['numMeses']);
if (isset($_POST['indAceite'])) 		$indAceite			= \Zage\App\Util::antiInjection($_POST['indAceite']);
if (isset($_POST['numFormando'])) 		$numFormando		= \Zage\App\Util::antiInjection($_POST['numFormando']);
if (isset($_POST['numConvidado'])) 		$numConvidado		= \Zage\App\Util::antiInjection($_POST['numConvidado']);
if (isset($_POST['dataConclusao'])) 	$dataConclusao		= \Zage\App\Util::antiInjection($_POST['dataConclusao']);
if (isset($_POST['codPlanoOrc'])) 		$codPlanoOrc		= \Zage\App\Util::antiInjection($_POST['codPlanoOrc']);
if (isset($_POST['aQtde'])) 			$aQtde				= \Zage\App\Util::antiInjection($_POST['aQtde']);
if (isset($_POST['aValor'])) 			$aValor				= \Zage\App\Util::antiInjection($_POST['aValor']);
if (isset($_POST['aObs'])) 				$aObs				= \Zage\App\Util::antiInjection($_POST['aObs']);

//$log->info("POST ORC: ".serialize($_POST));

#################################################################################
## Verificar parâmetro obrigatório
#################################################################################
if (!isset($codItemSel)) \Zage\App\Erro::halt('Falta de Parâmetros 2');
if (!is_array($codItemSel)) \Zage\App\Erro::halt('Parâmetros incorretos');

#################################################################################
## Verificar parâmetros
#################################################################################
if (!is_array($aQtde) 			|| sizeof($aQtde) 		< 1)  \Zage\App\Erro::halt('Parâmetro 2 incorreto');
if (!is_array($aValor) 			|| sizeof($aValor) 		< 1)  \Zage\App\Erro::halt('Parâmetro 3 incorreto');
if (!is_array($aObs) 			|| sizeof($aObs) 		< 1)  \Zage\App\Erro::halt('Parâmetro 4 incorreto');


#################################################################################
## Formatar os campos
#################################################################################
$taxaSistema			= \Zage\App\Util::to_float($taxaSistema);
$numMeses				= (int) $numMeses;

#################################################################################
## Fazer validação dos campos
#################################################################################
/******* Número de Formandos *********/
if (!isset($numFormando) || (empty($numFormando))) {
	die ('1'.\Zage\App\Util::encodeUrl('||'.htmlentities($tr->trans("Quantidade de formandos deve ser informada!"))));
}

/******* Número de Convidados *********/
if (!isset($numConvidado) || (empty($numConvidado))) {
	die ('1'.\Zage\App\Util::encodeUrl('||'.htmlentities($tr->trans("Quantidade de convidados deve ser informada!"))));
}

/******* Plano de Orçamento *********/
if (!isset($codPlanoOrc) || (empty($codPlanoOrc))) {
	die ('1'.\Zage\App\Util::encodeUrl('||'.htmlentities($tr->trans("Selecione o Plano de Orçamento"))));
}else{
	$oPlanoOrc	= $em->getRepository('Entidades\ZgfmtPlanoOrcamentario')->findOneBy(array('codigo' => $codPlanoOrc));
	if (!$oPlanoOrc) {
		die ('1'.\Zage\App\Util::encodeUrl('||'.htmlentities($tr->trans("Selecione um Plano de Orçamento existente"))));
	}
}

/******* Data de Conclusão *********/
if (\Zage\App\Util::validaData($dataConclusao, $system->config["data"]["dateFormat"]) == false) {
	die ('1'.\Zage\App\Util::encodeUrl('||'.htmlentities($tr->trans("Data de conclusão inválida"))));
}

/******* Indicador de aceite *********/
if (isset($indAceite) || $indAceite) {
	$indAceite		= 1;
}else{
	$indAceite		= 0;
}
 
#################################################################################
## Salvar no banco
#################################################################################
try {
	$oOrganizacao	= $em->getRepository('Entidades\ZgadmOrganizacao')->findOneBy(array('codigo' => $system->getCodOrganizacao()));
	$oUser			= $em->getRepository('Entidades\ZgsegUsuario')->findOneBy(array('codigo' => $system->getCodUsuario()));
	$oDataConc		= \DateTime::createFromFormat($system->config["data"]["dateFormat"], $dataConclusao);
	$ultVersao		= \Zage\Fmt\Orcamento::getUltimoNumeroVersao($system->getCodOrganizacao());
	$versao			= ($ultVersao) ? ($ultVersao + 1) : 1;
	
	$oOrc			= new \Entidades\ZgfmtOrcamento();
	$oOrc->setCodOrganizacao($oOrganizacao);
	$oOrc->setCodPlanoOrc($oPlanoOrc);
	$oOrc->setCodUsuario($oUser);
	$oOrc->setDataCadastro(new \DateTime("now"));
	$oOrc->setDataConclusao($oDataConc);
	$oOrc->setIndAceite(0);
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
 		$valor			= ($aValor[$codItem]) ? \Zage\App\Util::to_float($aValor[$codItem]) : 0; 
 		$qtde			= (int) $aQtde[$codItem];
 		$oOrcItem		= new \Entidades\ZgfmtOrcamentoItem();
 		$oOrcItem->setCodItem($oItem);
 		$oOrcItem->setCodOrcamento($oOrc);
 		$oOrcItem->setIndHabilitado(1);
 		$oOrcItem->setTextoDescritivo($aObs[$codItem]);
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