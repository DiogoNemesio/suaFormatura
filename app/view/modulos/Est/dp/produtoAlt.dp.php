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
## Resgata os parâmetros passados pelo formulario
#################################################################################
if (isset($_POST['codMaterialGrupo']))	$codMaterialGrupo	= \Zage\App\Util::antiInjection($_POST['codMaterialGrupo']);
if (isset($_POST['descricao']))			$descricao			= \Zage\App\Util::antiInjection($_POST['descricao']);
if (isset($_POST['descricaoCom']))		$descricaoCom		= \Zage\App\Util::antiInjection($_POST['descricaoCom']);
if (isset($_POST['referencia']))		$referencia			= \Zage\App\Util::antiInjection($_POST['referencia']);
if (isset($_POST['ncm']))				$ncm				= \Zage\App\Util::antiInjection($_POST['ncm']);
if (isset($_POST['codUniMed']))			$codUniMed			= \Zage\App\Util::antiInjection($_POST['codUniMed']);
if (isset($_POST['codSubGrupo']))	 	$codSubgrupo		= \Zage\App\Util::antiInjection($_POST['codSubGrupo']);
if (isset($_POST['ativo']))	 			$ativo				= \Zage\App\Util::antiInjection($_POST['ativo']);

#################################################################################
## Limpar a variável de erro
#################################################################################
$err	= false;

#################################################################################
## Fazer validação dos campos
#################################################################################

$log->debug($ncm);
/** Descrição**/
if ((!empty($descricao)) && (strlen($descricao) > 100)) {
 	$system->criaAviso(\Zage\App\Aviso\Tipo::ERRO,$tr->trans("Campo DESCRIÇÃO não deve conter mais de 100 caracteres"));
	$err	= 1;
}

if ((empty($descricao))) {
	$system->criaAviso(\Zage\App\Aviso\Tipo::ERRO,$tr->trans("Campo DESCRIÇÃO é obrigatório"));
	$err	= 1;
}

$oProdutoBusca	= $em->getRepository('Entidades\ZgestProduto')->findOneBy(array('descricao' => $descricao));

if($oProdutoBusca != null && ($oProduto->getCodigo() != $codProduto)){
	$system->criaAviso(\Zage\App\Aviso\Tipo::ERRO,$tr->trans("Já existe um produto com essa descrição"));
	$err	= 1;
}

/** Descrição Completa**/
if ((empty($descricaoCom))) {
	$system->criaAviso(\Zage\App\Aviso\Tipo::ERRO,$tr->trans("Campo DESCRIÇÃO COMPLETA é obrigatório"));
	$err	= 1;
}

/** Unidade de Medida**/
if ((empty($codUniMed))) {
	$system->criaAviso(\Zage\App\Aviso\Tipo::ERRO,$tr->trans("Não existe unidade de medida cadastrado!"));
	$err	= 1;
}

/** SubGrupo**/
if ((empty($codSubgrupo))) {
	$system->criaAviso(\Zage\App\Aviso\Tipo::ERRO,$tr->trans("Campo de SUBGRUPO é obrigatório"));
	$err	= 1;
}else{
	$oSubgrupo	= $em->getRepository('Entidades\ZgestSubgrupoMaterial')->findOneBy(array('codigo' => $codSubgrupo));
	
	if($oSubgrupo != null){
		$system->criaAviso(\Zage\App\Aviso\Tipo::ERRO,$tr->trans("Sub-grupo inválido, favor preencher corretamente"));
		$err	= 1;
	}
}
/** Ativo **/
if (isset($ativo) && (!empty($ativo))) {
	$ativo	= 1;
}else{
	$ativo	= 0;
}


if ($err != null) {
	echo '1'.\Zage\App\Util::encodeUrl('||'.htmlentities($err));
 	exit;
}
 
#################################################################################
## Salvar no banco
#################################################################################
try {
	
	if (isset($codProduto) && (!empty($codProduto))) {
 		$oProduto	= $em->getRepository('Entidades\ZgestProduto')->findOneBy(array('codigo' => $codProduto));
 		if (!$oProduto) $oProduto	= new \Entidades\ZgestProduto();
 	}else{
 		$oProduto	= new \Entidades\ZgestProduto();
 	}
 	
 	$oOrganização		= $em->getRepository('Entidades\ZgadmOrganizacao')->findOneBy(array('codigo' => $system->getCodOrganizacao()));
 	$oSubgrupoMaterial	= $em->getRepository('Entidades\ZgestSubgrupoMaterial')->findOneBy(array('codigo' => $codSubgrupo));
 	$oUniMed			= $em->getRepository('Entidades\ZgestUnidadeMedida')->findOneBy(array('codigo' => $codUniMed));
 	
 	$oProduto->setCodOrganizao($oOrganização);
 	$oProduto->setDescricao($descricao);
 	$oProduto->setDescricaoCompleta($descricaoCom);
 	$oProduto->setCodUnidadeMedida($oUniMed);
 	$oProduto->setReferencia($referencia);
 	$oProduto->setCodNcm($ncm);
 	$oProduto->setIndAtivo($ativo);
 	
 	$em->persist($oProduto);
 	$em->flush();
 	$em->detach($oProduto);
 	 	
} catch (\Exception $e) {
 	$system->criaAviso(\Zage\App\Aviso\Tipo::ERRO,$e->getMessage());
 	echo '1'.\Zage\App\Util::encodeUrl('||'.htmlentities($e->getMessage()));
 	exit;
}
 
$system->criaAviso(\Zage\App\Aviso\Tipo::INFO,"Informações salvas com sucesso");
echo '0'.\Zage\App\Util::encodeUrl('|'.$oProduto->getCodigo());