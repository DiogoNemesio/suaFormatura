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
if (isset($_POST['codCargo']))					$codCargo				= \Zage\App\Util::antiInjection($_POST['codCargo']);
if (isset($_POST['cargo']))						$cargo					= \Zage\App\Util::antiInjection($_POST['cargo']);
if (isset($_POST['codCbo']))					$codCbo					= \Zage\App\Util::antiInjection($_POST['codCbo']);
if (isset($_POST['intAtivo']))					$intAtivo				= \Zage\App\Util::antiInjection($_POST['intAtivo']);

if (isset($_POST['indAtivoF']))					$indAtivoF				= $_POST['indAtivoF'];
if (isset($_POST['salarioInicial']))			$salarioInicial			= $_POST['salarioInicial'];
if (isset($_POST['salarioFinal']))				$salarioFinal			= $_POST['salarioFinal'];
if (isset($_POST['codFuncao']))					$codFuncao				= $_POST['codFuncao'];
if (isset($_POST['funcao']))					$funcao					= $_POST['funcao'];

if (!isset($codFuncao))							$codFuncao				= array();
if (!isset($salarioInicial))					$salarioInicial			= array();
if (!isset($salarioFinal))						$salarioFinal			= array();
if (!isset($indAtivoF))							$indAtivoF				= array();
if (!isset($funcao))							$funcao					= array();
#################################################################################
## Limpar a variável de erro
#################################################################################
$err	= false;

#################################################################################
## Fazer validação dos campos
#################################################################################
/** Cargo **/
if ((empty($cargo))) {
	$system->criaAviso(\Zage\App\Aviso\Tipo::ERRO,$tr->trans("Campo CARGO é obrigatório"));
	$err	= 1;
}

/** Cod Cbo **/
if ((empty($codCbo))) {
	$system->criaAviso(\Zage\App\Aviso\Tipo::ERRO,$tr->trans("Campo COD CBO é obrigatório"));
	$err	= 1;
}

/** Funcao **/
if ((empty($funcao))) {
	$system->criaAviso(\Zage\App\Aviso\Tipo::ERRO,$tr->trans("Campo FUNCAO é obrigatório"));
	$err	= 1;
}

/** IntAtivo **/
if (isset($intAtivo) && (!empty($intAtivo))) {
	$intAtivo	= 1;
}else{
	$intAtivo	= 0;
}

$oCargo	= $em->getRepository('Entidades\ZgrhuFuncionarioCargo')->findOneBy(array('descricao' => $cargo));

if($oCargo != null && ($oCargo->getCodigo() != $codCargo)){
	$system->criaAviso(\Zage\App\Aviso\Tipo::ERRO,$tr->trans("Este CARGO já foi cadastrado!"));
	$err	= 1;
}

if ($err != null) {
	echo '1'.\Zage\App\Util::encodeUrl('||'.htmlentities($err));
 	exit;
}
 
#################################################################################
## Salvar no banco
#################################################################################
try {
	
	if (isset($codCargo) && (!empty($codCargo))) {
 		$oCargo	= $em->getRepository('Entidades\ZgrhuFuncionarioCargo')->findOneBy(array('codigo' => $codCargo));
 		if (!$oCargo) $oCargo	= new \Entidades\ZgrhuFuncionarioCargo();
 	}else{
 		$oCargo	= new \Entidades\ZgrhuFuncionarioCargo();
 	}
 	
 	$oCbo	 = $em->getRepository('Entidades\ZgrhuFuncionarioCbo')->findOneBy(array('codigo' => $codCbo));
 	
 	$oCargo->setDescricao($cargo); 
 	$oCargo->setCodCbo($oCbo);
 	$oCargo->setIndAtivo($intAtivo);
 	
 	$em->persist($oCargo);
 	$em->flush();
 	//$em->detach($oCargo);
 	
 	#################################################################################
 	for($i = 0; $i < sizeof ( $codFuncao ); $i ++) {

 		$oFuncao	= $em->getRepository('Entidades\ZgrhuFuncionarioFuncao')->findOneBy(array('descricao' => $funcao [$i], 'codCargo' => $oCargo->getCodigo ()));
 			
 		if($oFuncao != null && ($oFuncao->getCodigo() != $codCargo)){
 			$system->criaAviso(\Zage\App\Aviso\Tipo::ERRO,$tr->trans("Esta FUNÇÂO já foi cadastrado!"));
 			$err	= 1;
 		}
 		
 		$infoFuncao = $em->getRepository ( 'Entidades\ZgrhuFuncionarioFuncao' )->findOneBy ( array (
 				'codigo' => $codFuncao [$i],
 				'codCargo' => $oCargo->getCodigo ()
 		) );
 	
 		if (! $infoFuncao) {
 			$infoFuncao = new \Entidades\ZgrhuFuncionarioFuncao ();
 		}
 		
 		/** IndAtivoF **/
 		if (isset($indAtivoF [$i]) && (!empty($indAtivoF [$i]))) {
 			$indAtivoF	= 1;
 		}else{
 			$indAtivoF	= 0;
 		}
 	
 		$salarioInicial = \Zage\App\Util::toMysqlNumber($salarioInicial [$i]);
 		$salarioFinal   = \Zage\App\Util::toMysqlNumber($salarioFinal [$i]);
 		
 		$infoFuncao->setCodCargo ( $oCargo );
 		$infoFuncao->setSalarioInicial($salarioInicial);
 		$infoFuncao->setSalarioFinal($salarioFinal);
 		$infoFuncao->setIndAtivo($indAtivoF);
 		$infoFuncao->setDescricao ( $funcao [$i] );
 			
 		try {
 			$em->persist ( $infoFuncao );
 			$em->flush ();
 			$em->detach ( $infoFuncao );
 		} catch ( \Exception $e ) {
 			$system->criaAviso ( \Zage\App\Aviso\Tipo::ERRO, "Não foi possível cadastrar a funcao: " . $funcao [$i] . " Erro: " . $e->getMessage () );
 			echo '1' . \Zage\App\Util::encodeUrl ( '||' . htmlentities ( $e->getMessage () ) );
 			exit ();
 		}
 	}
 		
} catch (\Exception $e) {
 	$system->criaAviso(\Zage\App\Aviso\Tipo::ERRO,$e->getMessage());
 	echo '1'.\Zage\App\Util::encodeUrl('||'.htmlentities($e->getMessage()));
 	exit;
}
 
$system->criaAviso(\Zage\App\Aviso\Tipo::INFO,"Informações salvas com sucesso");
echo '0'.\Zage\App\Util::encodeUrl('|'.$oCargo->getCodigo());