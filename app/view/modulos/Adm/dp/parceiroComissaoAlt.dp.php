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
if (isset($_POST['codOrganizacao']))		$codOrganizacao		= \Zage\App\Util::antiInjection($_POST['codOrganizacao']);
if (isset($_POST['codPlano']))				$codPlano			= \Zage\App\Util::antiInjection($_POST['codPlano']);
if (isset($_POST['codVendaPlano']))			$codVendaPlano		= \Zage\App\Util::antiInjection($_POST['codVendaPlano']);
if (isset($_POST['dataBase']))				$dataBase			= \Zage\App\Util::antiInjection($_POST['dataBase']);
if (isset($_POST['codTipoComissao']))		$codTipoComissao	= \Zage\App\Util::antiInjection($_POST['codTipoComissao']);
if (isset($_POST['valorComissao']))			$valorComissao		= \Zage\App\Util::antiInjection($_POST['valorComissao']);
if (isset($_POST['pctComissao']))			$pctComissao		= \Zage\App\Util::antiInjection($_POST['pctComissao']);

#################################################################################
## Limpar a variável de erro
#################################################################################
$err	= false;

#################################################################################
## Fazer validação dos campos
#################################################################################
/** Data base **/
if (!empty($dataBase)) {
	$dataBase = DateTime::createFromFormat($system->config["data"]["dateFormat"], $dataBase);
	if ($dataBase < new \DateTime("now")) {
		$system->criaAviso(\Zage\App\Aviso\Tipo::ERRO,"A data base não pode ser menor que a data de hoje.");
		$err	= 1;
	}
}else{
	$system->criaAviso(\Zage\App\Aviso\Tipo::ERRO,"Informe a data base.");
	$err	= 1;
}

/** Porcentagem ou valor de comissão **/
if ($codTipoComissao == 'V'){
	if ($valorComissao){
		$valorComissao	= \Zage\App\Util::to_float($valorComissao);
	}else{
		$valorComissao = 0;
	}
	$pctComissao = 0;
}elseif ($codTipoComissao == 'P'){
	if ($pctComissao)	{
		$pctComissao		= \Zage\App\Util::to_float(str_replace("%", "", $pctComissao));
	}else{
		$pctComissao = 0;
	}
	$valorComissao = 0;
}


if ($err != null) {
	echo '1'.\Zage\App\Util::encodeUrl('||'.htmlentities($err));
 	exit;
}
 
#################################################################################
## Salvar no banco
#################################################################################
$em->getConnection()->beginTransaction();
try {
	
	#################################################################################
	## Resgatar objetos
	#################################################################################
	$oOrg	= $em->getRepository('Entidades\ZgadmOrganizacao')->findOneBy(array('codigo' => $codOrganizacao));
	$oPlano			= $em->getRepository('Entidades\ZgadmPlano')->findOneBy(array('codigo' => $codPlano));
	
	#################################################################################
	## SALVAR VENDA-PLANO
	#################################################################################
	if (isset($codVendaPlano) && (!empty($codVendaPlano))) {
 		$oVendaPlano	= $em->getRepository('Entidades\ZgadmOrganizacaoVendaPlano')->findOneBy(array('codigo' => $codVendaPlano));
 		if (!$oVendaPlano){
 			$oVendaPlano	= new \Entidades\ZgadmOrganizacaoVendaPlano();
 			$oVendaPlano->setCodOrganizacao($oOrg);
 			$oVendaPlano->setCodPlano($oPlano);
 			$oVendaPlano->setDataCadastro(new \DateTime("now"));
 			$oVendaPlano->getIndHabilitado(1);
 		}
 	}else{
 		$oVendaPlano	= $em->getRepository('Entidades\ZgadmOrganizacaoVendaPlano')->findOneBy(array('codOrganizacao' => $codOrganizacao , 'codPlano' => $codPlano));
 		if (!$oVendaPlano){
 			$oVendaPlano	= new \Entidades\ZgadmOrganizacaoVendaPlano();
 			$oVendaPlano->setCodOrganizacao($oOrg);
 			$oVendaPlano->setCodPlano($oPlano);
 			$oVendaPlano->setDataCadastro(new \DateTime("now"));
 			$oVendaPlano->setIndHabilitado(1);
 		}
 	}
 	
 	$oVendaPlano->setDataUltimaAlteracao(new \DateTime("now"));
 	
 	$em->persist($oVendaPlano);
 	
 	#################################################################################
 	## SALVAR VENDA-COMISSÃO
 	#################################################################################
 	$oUsuario = $em->getRepository('Entidades\ZgsegUsuario')->findOneBy(array('codigo' => $system->getCodUsuario()));
 	
 	$oVendaComissao	= new \Entidades\ZgadmOrganizacaoVendaComissao();
 	
 	$oVendaComissao->setCodVendaPlano($oVendaPlano);
 	$oVendaComissao->setCodUsuario($oUsuario);
 	$oVendaComissao->setDataBase($dataBase);
 	$oVendaComissao->setPctComissao($pctComissao);
 	$oVendaComissao->setValorComissao($valorComissao);
 	$oVendaComissao->setDataCadastro(new \DateTime("now"));
 	
 	$em->persist($oVendaComissao);
	
 	#################################################################################
 	## SALVAR - FLUSH
 	#################################################################################
	$em->flush();
	$em->clear();
	$em->getConnection()->commit();

} catch (\Exception $e) {
	$em->getConnection()->rollback();
 	$system->criaAviso(\Zage\App\Aviso\Tipo::ERRO,$e->getMessage());
 	echo '1'.\Zage\App\Util::encodeUrl('||'.htmlentities($e->getMessage()));
 	exit;
}
 
$system->criaAviso(\Zage\App\Aviso\Tipo::INFO,"Informações salvas com sucesso");
echo '0'.\Zage\App\Util::encodeUrl('|'.$oVendaPlano->getCodigo());