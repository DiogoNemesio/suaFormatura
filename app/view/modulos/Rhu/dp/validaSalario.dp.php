<?php
#################################################################################
## Includes
#################################################################################
if (defined('DOC_ROOT')) {
	include_once(DOC_ROOT . 'includeNoAuth.php');
}else{
	include_once('../includeNoAuth.php');
}

#################################################################################
## Resgata as variáveis postadas
#################################################################################
if (isset($_GET['codFuncao']))			$codFuncao	= \Zage\App\Util::antiInjection($_GET["codFuncao"]);
if (isset($_GET['salario']))			$salario	= \Zage\App\Util::antiInjection($_GET["salario"]);

try {
	$funcao	= $em->getRepository('Entidades\ZgrhuFuncionarioFuncao')->findOneBy(array ('codigo' => $codFuncao));
} catch (\Exception $e) {
	\Zage\App\Erro::halt($e->getMessage());
}

//$log->debug("Salario Antes: ".$salario);
//$log->debug("Faixa Inicial Antes: ".\Zage\App\Util::toPhpNumber($funcao->getSalarioInicial()));
//$log->debug("Faixa Final Antes: ".\Zage\App\Util::toPhpNumber($funcao->getSalarioFinal()));

$inicial	= \Zage\App\Util::to_float($funcao->getSalarioInicial());
$final		= \Zage\App\Util::to_float($funcao->getSalarioFinal());
$salario	= \Zage\App\Util::to_float($salario);

//$log->debug("Salario Depois: ".$salario);
//$log->debug("Faixa Inicial: ".$inicial);
//$log->debug("Faixa Final: ".$final);

if ($funcao != null && ($salario >= $inicial) && ($salario <= $final)) {
	
	$array["faixa"]	= 1;
	
}else{
	$array["faixa"]	= 0;
	
}

//$log->debug("OK: ".$array["faixa"]);

echo json_encode($array);