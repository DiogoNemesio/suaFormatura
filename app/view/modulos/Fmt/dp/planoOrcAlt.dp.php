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
global $em,$system,$log,$tr;

#################################################################################
## Resgata os parâmetros passados pelo formulário
#################################################################################
if (isset($_POST['codVersao']))				$codPlano			= \Zage\App\Util::antiInjection($_POST['codVersao']);
if (isset($_POST['codEvento']))				$codEvento			= \Zage\App\Util::antiInjection($_POST['codEvento']);
if (isset($_POST['codOrcamento']))			$codOrcamento		= \Zage\App\Util::antiInjection($_POST['codOrcamento']);
if (isset($_POST['item']))					$item				= \Zage\App\Util::antiInjection($_POST['item']);
if (isset($_POST['codTipoItem']))			$codTipoItem		= \Zage\App\Util::antiInjection($_POST['codTipoItem']);
if (isset($_POST['codCategoria']))			$codCategoria		= \Zage\App\Util::antiInjection($_POST['codCategoria']);
if (isset($_POST['indAtivo']))				$indAtivo			= \Zage\App\Util::antiInjection($_POST['indAtivo']);
if (isset($_POST['indPadrao']))				$indPadrao			= \Zage\App\Util::antiInjection($_POST['indPadrao']);
if (isset($_POST['valorPadrao']))			$valorPadrao		= \Zage\App\Util::antiInjection($_POST['valorPadrao']);
if (isset($_POST['aObs']))					$aObs				= \Zage\App\Util::antiInjection($_POST['aObs']);
if (isset($_POST['pctMaxDesconto']))		$pctMaxDesconto		= \Zage\App\Util::antiInjection($_POST['pctMaxDesconto']);
if (isset($_POST['versao']))				$versao				= \Zage\App\Util::antiInjection($_POST['versao']);
if (isset($_POST['indVersao']))				$indVersao			= \Zage\App\Util::antiInjection($_POST['indVersao']);

#################################################################################
## Caso não venha as variáveis (ARRAY) inicializar eles
#################################################################################
if (!isset($item))				$item			= array();
if (!isset($codTipoItem))		$codTipoItem	= array();
if (!isset($codCategoria))		$codCategoria	= array();
if (!isset($indAtivo))			$indAtivo		= array();
if (!isset($indPadrao))			$indPadrao		= array();
if (!isset($valorPadrao))		$valorPadrao	= array();

//$log->info("aIndAtivo: ".serialize($indAtivo));
//$log->info("aIndObr: ".serialize($indPadrao));

if ($codOrcamento == null){
	$codOrcamento	= array();
}

#################################################################################
## Fazer validação dos campos
#################################################################################
/** codEvento **/
if (empty($codEvento)){
	die ('1'.\Zage\App\Util::encodeUrl('||'.htmlentities($tr->trans("PARÂMETRO NÃO INFORMANDO : COD_EVENTO"))));
}

/** codVersao **/
if (!is_array($codOrcamento)) {
	die ('1'.\Zage\App\Util::encodeUrl('||'.htmlentities($tr->trans("VARIÁVEL INVÁLIDA : COD_VERSAO"))));
}

/** item **/
if (!empty($item)){
	for ($v = 0; $v < sizeof($item); $v++) {
		if ($item[$v] == null){
			die ('1'.\Zage\App\Util::encodeUrl('||'.htmlentities($tr->trans("Não pode haver uma linha sem o nome do item preenchido!"))));
			
		}
	}
}


/** item **/
if (!is_array($item)) {
	die ('1'.\Zage\App\Util::encodeUrl('||'.htmlentities($tr->trans("Item não é um array!"))));
}

#################################################################################
## Validar o tamanho dos arrays
#################################################################################
$numItens	= sizeof($codOrcamento);

#################################################################################
## Salvar no banco
#################################################################################
try {
	$oPlano		= $em->getRepository('Entidades\ZgfmtPlanoOrcamentario')->findOneBy(array('codigo' => $codPlano));
	
	if (!$oPlano) {
		$oPlano	= new \Entidades\ZgfmtPlanoOrcamentario();
		$oPlano->setDataCadastro(new \DateTime("now"));
	}
	
	#################################################################################
	## Constroi os objetos
	#################################################################################
	$oOrganizacao	 = $em->getRepository('Entidades\ZgadmOrganizacao')->findOneBy(array('codigo' => $system->getCodOrganizacao()));

	if (isset($indVersao) && (!empty($indVersao))) {
		$indVersao = 1;
	}else{
		$indVersao = 0;
	}

	$oPlano->setCodOrganizacao($oOrganizacao);
	$oPlano->setVersao($versao);
	$oPlano->setIndAtivo($indVersao);
	
	$em->persist($oPlano);
	
	#################################################################################
	## Apagar os itens do orçamento
	#################################################################################
	$orcamentos			= $em->getRepository('Entidades\ZgfmtPlanoOrcItem')->findBy(array('codPlano' => $codPlano, 'codGrupoItem' => $codEvento));

	for ($i = 0; $i < sizeof($orcamentos); $i++) {
		
		if (!in_array($orcamentos[$i]->getCodigo(), $codOrcamento)) {
			try {
				$em->remove($orcamentos[$i]);
			} catch (\Exception $e) {
				echo '1'.\Zage\App\Util::encodeUrl('||'.htmlentities("Não foi possível excluir o Orcamento de item: ".$orcamentos[$i]->getItem()." Erro: ".$e->getMessage()));
				exit;
			}
		}
	}

	#################################################################################
	## Criar / Alterar
	#################################################################################
	for ($i = 0; $i < $numItens; $i++) {

		#################################################################################
		## Verifica se o registro já existe no banco
		#################################################################################
		$oOrcamento		= $em->getRepository('Entidades\ZgfmtPlanoOrcItem')->findOneBy(array('codigo' => $codOrcamento[$i]));
		
		if (!$oOrcamento) {
			$oOrcamento	= new \Entidades\ZgfmtPlanoOrcItem();
			$oOrcamento->setDataCadastro(new \DateTime("now"));
		}
		
		$indAtivoLinha			= (isset($indAtivo[$i])) 		? 1 : 0;
		$indPadraoLinha			= (isset($indPadrao[$i]))		? 1 : 0;
		
		$valorPadraoLinha		= (isset($valorPadrao[$i]) && $valorPadrao[$i] > 0)			? \Zage\App\Util::to_float($valorPadrao[$i]) : null;
		if (!isset($pctMaxDesconto[$i]) || !$pctMaxDesconto[$i]) {
			$_pctMaxDesconto	= null;
		}else{
			$_pctMaxDesconto		= \Zage\App\Util::to_float(str_replace("%", "", $pctMaxDesconto[$i]));
		}
		
		
		#################################################################################
		## Constroi os objetos
		#################################################################################
		$oCodCategoria	= $em->getRepository('Entidades\ZgfinCategoria')->findOneBy(array('codigo' => $codCategoria[$i]));
		$oCodTipoItem	= $em->getRepository('Entidades\ZgfmtPlanoOrcItemTipo')->findOneBy(array('codigo' => $codTipoItem[$i]));
		$oCodTipoEvento	= $em->getRepository('Entidades\ZgfmtPlanoOrcGrupoItem')->findOneBy(array('codigo' => $codEvento));
		
		$oOrcamento->setCodPlano($oPlano);
		$oOrcamento->setCodGrupoItem($oCodTipoEvento);
		$oOrcamento->setCodCategoria($oCodCategoria);
		$oOrcamento->setCodTipoItem($oCodTipoItem);
		$oOrcamento->setItem($item[$i]);
		$oOrcamento->setOrdem(($i+1));
		$oOrcamento->setIndAtivo($indAtivoLinha);
		$oOrcamento->setIndPadrao($indPadraoLinha);
		$oOrcamento->setValorPadrao($valorPadraoLinha);
		$oOrcamento->setTextoDescritivo($aObs[$i]);
		$oOrcamento->setPctMaxDescontoVendedor($_pctMaxDesconto);
		
		$em->persist($oOrcamento);
	}

	$em->flush();
	$em->clear();

} catch (\Exception $e) {
	echo '1'.\Zage\App\Util::encodeUrl('||'.htmlentities($e->getMessage()));
	exit;
}

echo '0'.\Zage\App\Util::encodeUrl('|'.$oPlano->getCodigo());