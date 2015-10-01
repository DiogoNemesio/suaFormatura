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
if (isset($_POST['codProduto']))		$codProduto			= \Zage\App\Util::antiInjection($_POST['codProduto']);
if (isset($_POST['nome']))				$nome				= \Zage\App\Util::antiInjection($_POST['nome']);
if (isset($_POST['descricao']))			$descricao			= \Zage\App\Util::antiInjection($_POST['descricao']);
if (isset($_POST['codTipoMaterial']))	$codTipoMaterial	= \Zage\App\Util::antiInjection($_POST['codTipoMaterial']);
if (isset($_POST['codSubgrupo']))	 	$codSubgrupo		= \Zage\App\Util::antiInjection($_POST['codSubgrupo']);
if (isset($_POST['ativo']))	 			$ativo				= \Zage\App\Util::antiInjection($_POST['ativo']);
if (isset($_POST['indExposicao']))	 	$indExposicao		= \Zage\App\Util::antiInjection($_POST['indExposicao']);

if (isset($_POST['preReserva']))	 	$preReserva			= \Zage\App\Util::antiInjection($_POST['preReserva']);
if (isset($_POST['diasIndis']))	 		$diasIndis			= \Zage\App\Util::antiInjection($_POST['diasIndis']);
#################################################################################
## Limpar a variável de erro
#################################################################################
$err	= false;

#################################################################################
## Fazer validação dos campos
#################################################################################
/** Descrição**/
if ((!empty($descricao)) && (strlen($descricao) > 500)) {
 	$system->criaAviso(\Zage\App\Aviso\Tipo::ERRO,$tr->trans("Campo DESCRIÇÃO não deve conter mais de 500 caracteres"));
	$err	= 1;
}

/** Nome**/
if ((!empty($nome)) && (strlen($nome) > 100)) {
	$system->criaAviso(\Zage\App\Aviso\Tipo::ERRO,$tr->trans("Campo NOME não deve conter mais de 100 caracteres"));
	$err	= 1;
}

if ((empty($nome))) {
	$system->criaAviso(\Zage\App\Aviso\Tipo::ERRO,$tr->trans("Campo NOME é obrigatório"));
	$err	= 1;
}

$oProdutoBusca	= $em->getRepository('Entidades\ZgestProduto')->findOneBy(array('nome' => $nome));

if($oProdutoBusca != null && ($oProduto->getCodigo() != $codProduto)){
	$system->criaAviso(\Zage\App\Aviso\Tipo::ERRO,$tr->trans("Já existe um produto com esse nome"));
	$err	= 1;
}

/** Tipo Material**/
if ((empty($codTipoMaterial))) {
	$system->criaAviso(\Zage\App\Aviso\Tipo::ERRO,$tr->trans("Campo de TIPO MATERIAL é obrigatório"));
	$err	= 1;
}

/** SubGrupo**/
if ((empty($codSubgrupo))) {
	$system->criaAviso(\Zage\App\Aviso\Tipo::ERRO,$tr->trans("Campo de SUBGRUPO é obrigatório"));
	$err	= 1;
}

/** Ativo **/
if (isset($ativo) && (!empty($ativo))) {
	$ativo	= 1;
}else{
	$ativo	= 0;
}

/** indExposicao **/
if (isset($indExposicao) && (!empty($indExposicao))) {
	$indExposicao	= 1;
}else{
	$indExposicao	= 0;
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
 	$oSubgrupo			= $em->getRepository('Entidades\ZgestSubgrupo')->findOneBy(array('codigo' => $codSubgrupo));
 	$oTipoMaterial		= $em->getRepository('Entidades\ZgestTipoProduto')->findOneBy(array('codigo' => $codTipoMaterial));
 	
 	$oProduto->setCodOrganizacao($oOrganização);
 	$oProduto->setCodTipoMaterial($oTipoMaterial);
 	$oProduto->setCodSubgrupo($oSubgrupo);
 	$oProduto->setNome($nome);
 	$oProduto->setDescricao($descricao);
 	$oProduto->setIndAtivo($ativo);
 	$oProduto->setIndExposicao($indExposicao);
 	$oProduto->setQuantidade(1);
 	$oProduto->setNumDiasIndisponivel($diasIndis);
 	$oProduto->setQtdeDiasPreReserva($preReserva);
 	$oProduto->setDataCadastro(new \DateTime("now"));
 	
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