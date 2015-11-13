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
## Resgata os parâmetros passados pelo formulário
#################################################################################
if (isset($_POST['codTipoVenda']))			$codTipoVenda			= \Zage\App\Util::antiInjection($_POST['codTipoVenda']);
if (isset($_POST['taxaAdministracao']))		$taxaAdministracao		= \Zage\App\Util::antiInjection($_POST['taxaAdministracao']);
if (isset($_POST['codContaRec']))			$codContaRec			= \Zage\App\Util::antiInjection($_POST['codContaRec']);
if (isset($_POST['indAddTaxaBoleto']))		$indAddTaxaBoleto		= \Zage\App\Util::antiInjection($_POST['indAddTaxaBoleto']);
if (isset($_POST['diasVencimento']))		$diasVencimento		= \Zage\App\Util::antiInjection($_POST['diasVencimento']);

if (isset($_POST['codFormaPag'])) 			$formaPag		= \Zage\App\Util::antiInjection($_POST['codFormaPag']);
if (!isset($formaPag))						$formaPag		= array();



#################################################################################
## Limpar a variável de erro
#################################################################################
$err	= false;
#################################################################################
## Fazer validação dos campos
#################################################################################
/******* Tipo de venda *********/
if (!isset($codTipoVenda) && (empty($codTipoVenda))) {
	$system->criaAviso(\Zage\App\Aviso\Tipo::ERRO,$tr->trans("Selecione uma forma de venda!"));
	$err	= 1;
}

/******* Forma de pagamento *********/
if ((empty($formaPag))) {
	$system->criaAviso(\Zage\App\Aviso\Tipo::ERRO,$tr->trans("Selecione pelo menos uma forma de pagamento!"));
	$err	= 1;
}

/******* Conta corrente *********/
$oConta			= $em->getRepository('Entidades\ZgfinConta')->findOneBy(array('codigo' => $codContaRec));
if (in_array('BOL', $formaPag)) {
	if (!isset($codContaRec) || (empty($codContaRec))) {
		$system->criaAviso(\Zage\App\Aviso\Tipo::ERRO,$tr->trans("Quando o a forma de pagamento do boleto tiver selecionado, uma conta corrente deve ser selecionada!"));
		$err	= 1;
	}elseif ($oConta->getCodCarteira() == null){
		$system->criaAviso(\Zage\App\Aviso\Tipo::ERRO,$tr->trans("A conta selecionada não está configurada para emitir boleto!"));
		$err	= 1;
	}
}

/******* Taxa de adm *********/
if ((!empty($taxaAdministracao))) {
	$taxaAdministracao = \Zage\App\Util::to_float($taxaAdministracao);
}

/******* Ind adicionar custo do boleto na taxa de comodidade *********/
if (isset($indAddTaxaBoleto) && (!empty($indAddTaxaBoleto))) {
	$indAddTaxaBoleto	= 1;
}else{
	$indAddTaxaBoleto	= 0;
}


if ($err != null) {
	echo '1'.\Zage\App\Util::encodeUrl('||'.htmlentities($err));
 	exit;
}
 
#################################################################################
## Salvar no banco
#################################################################################
try {
	
	#################################################################################
	## VERIFICAR SE A CONFIGURAÇÃO JÁ EXISTE
	#################################################################################
	$oConviteConf	= $em->getRepository('Entidades\ZgfmtConviteExtraVendaConf')->findOneBy(array('codVendaTipo' => $codTipoVenda , 'codFormatura' => $system->getCodOrganizacao()));
	
	if (!$oConviteConf){
		$oConviteConf	= new \Entidades\ZgfmtConviteExtraVendaConf();
	}
	 	
 	#################################################################################
 	## RESGATAR OBJETOS
 	#################################################################################
 	$oOrganizacao	= $em->getRepository('Entidades\ZgadmOrganizacao')->findOneBy(array('codigo' => $system->getCodOrganizacao()));
 	$oTipo			= $em->getRepository('Entidades\ZgfmtConviteExtraVendaTipo')->findOneBy(array('codigo' => $codTipoVenda));
 	
 	#################################################################################
 	## SETAR VALORES
 	#################################################################################
 	$oConviteConf->setCodFormatura($oOrganizacao); 
 	$oConviteConf->setCodVendaTipo($oTipo);
 	$oConviteConf->setTaxaAdministracao($taxaAdministracao);
 	$oConviteConf->setCodContaBoleto($oConta);
 	$oConviteConf->setIndAdicionarTaxaBoleto($indAddTaxaBoleto);
 	$oConviteConf->setDiasVencimentoBoleto($diasVencimento);
 	
 	$em->persist($oConviteConf);
 	
 	
 	#################################################################################
 	## Salvar as formas de pagamento
 	#################################################################################
  	//Retirar forma de pagamento
 	$oFormas		= $em->getRepository('Entidades\ZgfmtConviteExtraVendaForma')->findBy(array('codOrganizacao' => $system->getCodOrganizacao() , 'codVendaTipo' => $codTipoVenda));
 	for ($i = 0; $i < sizeof($oFormas); $i++) {
 		if (!in_array($oFormas[$i]->getCodFormaPagamento()->getCodigo(), $formaPag)) {
 			try {
 				$em->remove($oFormas[$i]);
 				
 			} catch (\Exception $e) {
 				echo '1'.\Zage\App\Util::encodeUrl('||'.htmlentities("Não foi possível excluir da lista de carteiras o valor: ".$infoCarteiras[$i]->getCodCarteira()->getCodigo()." Erro: ".$e->getMessage()));
 				exit;
 			}
 		}
 	}
 	//Atribuir forma de pagamento
 	for ($i = 0; $i < sizeof($formaPag); $i++) {
 		$oForma		= $em->getRepository('Entidades\ZgfmtConviteExtraVendaForma')->findOneBy(array('codOrganizacao' => $system->getCodOrganizacao() , 'codVendaTipo' => $codTipoVenda , 'codFormaPagamento' => $formaPag[$i]));
 		if (!$oForma) {
 			$oFormaPag = $em->getRepository('Entidades\ZgfinFormaPagamento')->findOneBy(array('codigo' => $formaPag[$i]));
 			
 			$oForma		= new \Entidades\ZgfmtConviteExtraVendaForma();
 			$oForma->setCodFormaPagamento($oFormaPag);
 			$oForma->setCodOrganizacao($oOrganizacao);
 			$oForma->setCodVendaTipo($oTipo);
 			
 			try {
 				$em->persist($oForma);
 			} catch (\Exception $e) {
 				echo '1'.\Zage\App\Util::encodeUrl('||'.htmlentities("Não foi possível cadastrar o valor: ".$acesso[$i]." Erro: ".$e->getMessage()));
 				exit;
 			} 				
 		}
 	}
 	
 	#################################################################################
 	## SALVAR
 	#################################################################################
	$em->flush();
	$em->clear();

} catch (\Exception $e) {
 	$system->criaAviso(\Zage\App\Aviso\Tipo::ERRO,$e->getMessage());
 	echo '1'.\Zage\App\Util::encodeUrl('||'.htmlentities($e->getMessage()));
 	exit;
}
 
$system->criaAviso(\Zage\App\Aviso\Tipo::INFO,"Informações salvas com sucesso");
echo '0'.\Zage\App\Util::encodeUrl('|'.$oConviteConf->getCodigo());
