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
if (isset($_POST['codEndereco']))		$codEndereco		= \Zage\App\Util::antiInjection($_POST['codEndereco']);
if (isset($_POST['nome'])) 				$nome				= \Zage\App\Util::antiInjection($_POST['nome']);
if (isset($_POST['codTipo']))			$codTipo			= \Zage\App\Util::antiInjection($_POST['codTipo']);
if (isset($_POST['codLocal']))	 		$codLocal			= \Zage\App\Util::antiInjection($_POST['codLocal']);
if (isset($_POST['ativo']))	 			$ativo				= \Zage\App\Util::antiInjection($_POST['ativo']);
if (isset($_POST['bloqueado']))			$bloqueado			= \Zage\App\Util::antiInjection($_POST['bloqueado']);
if (isset($_POST['rua']))	 			$rua				= \Zage\App\Util::antiInjection($_POST['rua']);
if (isset($_POST['estante']))	 		$estante			= \Zage\App\Util::antiInjection($_POST['estante']);
if (isset($_POST['prateleira']))	 	$prateleira			= \Zage\App\Util::antiInjection($_POST['prateleira']);
if (isset($_POST['coluna']))	 		$coluna				= \Zage\App\Util::antiInjection($_POST['coluna']);



#################################################################################
## Limpar a variável de erro
#################################################################################
$err	= false;

#################################################################################
## Fazer validação dos campos
#################################################################################
/** Nome **/
if ($codTipo == "L" &&  (!isset($nome) || (empty($nome)))) {
 	$system->criaAviso(\Zage\App\Aviso\Tipo::ERRO,$tr->trans("Campo NOME é obrigatório"));
	$err	= 1;
}

if ($codTipo == "L" && (!empty($nome)) && (strlen($nome) > 60)) {
	$system->criaAviso(\Zage\App\Aviso\Tipo::ERRO,$tr->trans("Campo NOME não deve conter mais de 60 caracteres"));
	$err	= 1;
}

/** Endereço **/
if ($codTipo == "E" &&  (!isset($rua) || (empty($rua)))) {
	$system->criaAviso(\Zage\App\Aviso\Tipo::ERRO,$tr->trans("Campo Rua é obrigatório para o Tipo ESTANTE"));
	$err	= 1;
}
if ($codTipo == "E" &&  (!isset($estante) || (empty($estante)))) {
	$system->criaAviso(\Zage\App\Aviso\Tipo::ERRO,$tr->trans("Campo Estante é obrigatório para o Tipo ESTANTE"));
	$err	= 1;
}
if ($codTipo == "E" &&  (!isset($prateleira) || (empty($prateleira)))) {
	$system->criaAviso(\Zage\App\Aviso\Tipo::ERRO,$tr->trans("Campo Prateleira é obrigatório para o Tipo ESTANTE"));
	$err	= 1;
}
if ($codTipo == "E" &&  (!isset($coluna) || (empty($coluna)))) {
	$system->criaAviso(\Zage\App\Aviso\Tipo::ERRO,$tr->trans("Campo Coluna é obrigatório para o Tipo ESTANTE"));
	$err	= 1;
}

if ($codTipo == "B" &&  (!isset($rua) || (empty($rua)))) {
	$system->criaAviso(\Zage\App\Aviso\Tipo::ERRO,$tr->trans("Campo Rua é obrigatório para o Tipo BLOCADO"));
	$err	= 1;
}
if ($codTipo == "B" &&  (!isset($estante) || (empty($estante)))) {
	$system->criaAviso(\Zage\App\Aviso\Tipo::ERRO,$tr->trans("Campo Estante é obrigatório para o Tipo BLOCADO"));
	$err	= 1;
}


$oTipo		= $em->getRepository('Entidades\ZgdocEnderecoTipo')->findOneBy(array('codigo' => $codTipo));
if (!$oTipo) {
	$system->criaAviso(\Zage\App\Aviso\Tipo::ERRO,$tr->trans("Tipo de Endereço não existe"));
	$err 	= 1;
}

$oLocal		= $em->getRepository('Entidades\ZgdocLocal')->findOneBy(array('codigo' => $codLocal));
if (!$oLocal) {
	$system->criaAviso(\Zage\App\Aviso\Tipo::ERRO,$tr->trans("Local do Endereço não existe"));
	$err 	= 1;
}

/** Ativo **/
if (isset($ativo) && (!empty($ativo))) {
	$ativo	= 1;
}else{
	$ativo	= 0;
}

/** Bloqueado **/
if (isset($bloqueado) && (!empty($bloqueado))) {
	$bloqueado	= 1;
}else{
	$bloqueado	= 0;
}

/** Montagem do nome **/
if ($codTipo == "E") {
	$nome  = $rua.'-'.$estante.'-'.$prateleira.'-'.$coluna;
}elseif ($codTipo == "B") {
	$nome  = $rua.'-'.$estante;
}


$oNome	= $em->getRepository('Entidades\ZgdocEndereco')->findOneBy(array('nome' => $nome, 'codLocal' => $codLocal));

if (($oNome != null) && ($oNome->getCodigo() != $codEndereco)){
	$system->criaAviso(\Zage\App\Aviso\Tipo::ERRO,$tr->trans(" NOME do Endereço já existe"));
	$err 	= 1;
}



if ($err != null) {
	echo '1'.\Zage\App\Util::encodeUrl('||'.htmlentities($err));
 	exit;
}
 
#################################################################################
## Salvar no banco
#################################################################################
try {
	
	if (isset($codEndereco) && (!empty($codEndereco))) {
 		$oEndereco	= $em->getRepository('Entidades\ZgdocEndereco')->findOneBy(array('codigo' => $codEndereco));
 		if (!$oEndereco) $oEndereco	= new \Entidades\ZgdocEndereco();
 	}else{
 		$oEndereco		= new \Entidades\ZgdocEndereco();
 	}
 	
 	$oEndereco->setNome($nome);
 	$oEndereco->setCodTipo($oTipo);
 	$oEndereco->setCodLocal($oLocal);
 	$oEndereco->setIndAtivo($ativo);
 	$oEndereco->setIndBloqueado($bloqueado);
 	$oEndereco->setRua($rua);
 	$oEndereco->setEstante($estante);
 	$oEndereco->setPrateleira($prateleira);
 	$oEndereco->setColuna($coluna);
 	
 	$em->persist($oEndereco);
 	$em->flush();
 	$em->detach($oEndereco);
 	 	
} catch (\Exception $e) {
 	$system->criaAviso(\Zage\App\Aviso\Tipo::ERRO,$e->getMessage());
 	echo '1'.\Zage\App\Util::encodeUrl('||'.htmlentities($e->getMessage()));
 	exit;
}
 
$system->criaAviso(\Zage\App\Aviso\Tipo::INFO," Informações salvas com sucesso");
echo '0'.\Zage\App\Util::encodeUrl('|'.$oEndereco->getCodigo());