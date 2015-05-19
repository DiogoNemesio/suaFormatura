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
if (isset($_POST['codTipo']))			$codTipo			= \Zage\App\Util::antiInjection($_POST['codTipo']);
if (isset($_POST['codLocal']))	 		$codLocal			= \Zage\App\Util::antiInjection($_POST['codLocal']);
if (isset($_POST['gerRua']))	 		$rua				= \Zage\App\Util::antiInjection($_POST['gerRua']);
if (isset($_POST['gerEstante']))		$estante			= \Zage\App\Util::antiInjection($_POST['gerEstante']);
if (isset($_POST['gerPrateleira']))	 	$prateleira			= \Zage\App\Util::antiInjection($_POST['gerPrateleira']);
if (isset($_POST['gerColuna']))	 		$coluna				= \Zage\App\Util::antiInjection($_POST['gerColuna']);
if (isset($_POST['gerPar']))	 		$par				= \Zage\App\Util::antiInjection($_POST['gerPar']);
if (isset($_POST['gerImpar']))	 		$impar				= \Zage\App\Util::antiInjection($_POST['gerImpar']);


if (!isset($codTipo) || !isset($codLocal)) {
	echo '1'.\Zage\App\Util::encodeUrl('||'.htmlentities($tr->trans("Falta de Parâmetros")));
	exit;
}


#################################################################################
## Limpar a variável de erro
#################################################################################
$err	= false;

#################################################################################
## Fazer validação dos campos
#################################################################################
/** Endereço **/
if ($codTipo == "E" &&  (!isset($rua) || (empty($rua)))) {
	echo '1'.\Zage\App\Util::encodeUrl('||'.htmlentities($tr->trans("Campo Rua é obrigatório para o Tipo ESTANTE")));
	exit;
}
if ($codTipo == "E" &&  (!isset($estante) || (empty($estante)))) {
	echo '1'.\Zage\App\Util::encodeUrl('||'.htmlentities($tr->trans("Campo Rua é obrigatório para o Tipo ESTANTE")));
	exit;
}
if ($codTipo == "E" &&  (!isset($prateleira) || (empty($prateleira)))) {
	echo '1'.\Zage\App\Util::encodeUrl('||'.htmlentities($tr->trans("Campo Prateleira é obrigatório para o Tipo ESTANTE")));
	exit;
}
if ($codTipo == "E" &&  (!isset($coluna) || (empty($coluna)))) {
	echo '1'.\Zage\App\Util::encodeUrl('||'.htmlentities($tr->trans("Campo Coluna é obrigatório para o Tipo ESTANTE")));
	exit;
}

if ($codTipo == "B" &&  (!isset($rua) || (empty($rua)))) {
	echo '1'.\Zage\App\Util::encodeUrl('||'.htmlentities($tr->trans("Campo Rua é obrigatório para o Tipo BLOCADO")));
	exit;
}
if ($codTipo == "B" &&  (!isset($estante) || (empty($estante)))) {
	echo '1'.\Zage\App\Util::encodeUrl('||'.htmlentities($tr->trans("Campo Estante é obrigatório para o Tipo BLOCADO")));
	exit;
}


$oTipo		= $em->getRepository('Entidades\ZgdocEnderecoTipo')->findOneBy(array('codigo' => $codTipo));
if (!$oTipo) {
	echo '1'.\Zage\App\Util::encodeUrl('||'.htmlentities($tr->trans("Tipo de Endereço não existe")));
	exit;
}

$oLocal		= $em->getRepository('Entidades\ZgdocLocal')->findOneBy(array('codigo' => $codLocal));
if (!$oLocal) {
	echo '1'.\Zage\App\Util::encodeUrl('||'.htmlentities($tr->trans("Local do Endereço não existe")));
	exit;
}

$ativo		= 1;
$bloqueado	= 0;

/** Par **/
if (isset($par) && (!empty($par))) {
	$par	= 1;
}else{
	$par	= 0;
}

/** Impar **/
if (isset($impar) && (!empty($impar))) {
	$impar	= 1;
}else{
	$impar	= 0;
}


/** Monta os arrays de cada parte **/
$aRua	= split("-",$rua);
$aEst	= split("-",$estante);
if ($codTipo == "E") {
	$aPra	= split("-",$prateleira);
	$aCol	= split("-",$coluna);
}

/** valida os campos **/


$log->debug("Rua: ".$rua);
$log->debug("Tamanho aRua: ".sizeof($aRua));

/** RUA **/
if (sizeof($aRua) > 2) {
	echo '1'.\Zage\App\Util::encodeUrl('||'.htmlentities($tr->trans("Campo Rua inválido")));
	exit;
}else{
	if (sizeof($aRua) == 1) {
		$ruaIni		= (int) $rua;
		$ruaFim		= (int) $rua;
	}else{
		$ruaIni		= (int) $aRua[0]; 
		$ruaFim		= (int) $aRua[1];
	}
	
	$log->debug("RuaIni: ".$ruaIni." RuaFim: ".$ruaFim);
	if (!is_integer($ruaIni) || (!is_integer($ruaFim))) {
		echo '1'.\Zage\App\Util::encodeUrl('||'.htmlentities($tr->trans("Campo Rua inválido") . ' *'));
		exit;
	}
}


/** Estante **/
if (sizeof($aEst) > 2) {
	echo '1'.\Zage\App\Util::encodeUrl('||'.htmlentities($tr->trans("Campo Estante inválido")));
	exit;
}else{
	if (sizeof($aEst) == 1) {
		$estIni		= (int) $estante;
		$estFim		= (int) $estante;
	}else{
		$estIni		= (int) $aEst[0];
		$estFim		= (int) $aEst[1];
	}

	if (!is_integer($estIni) || (!is_integer($estFim))) {
		echo '1'.\Zage\App\Util::encodeUrl('||'.htmlentities($tr->trans("Campo Estante inválido")));
		exit;
	}
}

if ($codTipo == "E") {
	
	/** Prateleira **/
	if (sizeof($aPra) > 2) {
		echo '1'.\Zage\App\Util::encodeUrl('||'.htmlentities($tr->trans("Campo Prateleira inválido")));
		exit;
	}else{
		if (sizeof($aPra) == 1) {
			$praIni		= (int) $prateleira;
			$praFim		= (int) $prateleira;
		}else{
			$praIni		= (int) $aPra[0];
			$praFim		= (int) $aPra[1];
		}
	
		if (!is_integer($praIni) || (!is_integer($praFim))) {
			echo '1'.\Zage\App\Util::encodeUrl('||'.htmlentities($tr->trans("Campo Prateleira inválido")));
			exit;
		}
	}

	/** Coluna **/
	if (sizeof($aCol) > 2) {
		echo '1'.\Zage\App\Util::encodeUrl('||'.htmlentities($tr->trans("Campo Coluna inválido")));
		exit;
	}else{
		if (sizeof($aCol) == 1) {
			$colIni		= (int) $coluna;
			$colFim		= (int) $coluna;
		}else{
			$colIni		= (int) $aCol[0];
			$colFim		= (int) $aCol[1];
		}
	
		if (!is_integer($colIni) || (!is_integer($colFim))) {
			echo '1'.\Zage\App\Util::encodeUrl('||'.htmlentities($tr->trans("Campo Coluna inválido")));
			exit;
		}
	}
}else{
	$colIni		= 0;
	$colFim		= 0;
	$praIni		= 0;
	$praFim		= 0;
}


$numEnd		= 0; /** Número de endereços **/
$numCri		= 0; /** Número de endereços criados **/
$commit		= 30; 

//$log->debug("RuaIni: $ruaIni RuaFim: $ruaFim EstIni: $estIni EstFim: $estFim PraIni: $praIni PraFim: $praFim ColIni: $colIni ColFim: $colFim");

/** Faz o loop para montagem do nome e inserção no banco **/
for ($i = $ruaIni; $i <= $ruaFim; $i++) {
	for ($j = $estIni; $j <= $estFim; $j++) {
		
		if ($par != $impar) {
			if ( (($par == 1) && ($j % 2 != 0)) || (($impar == 1) && ($j % 2 == 0)) ) {
				continue;
			} 
		}
		
		for ($k = $praIni; $k <= $praFim; $k++) {
			for ($l = $colIni; $l <= $colFim; $l++) {
				if ($codTipo == "E") {
					$nome = $i . '-' .$j . '-' . $k . '-' . $l;
				}else{
					$nome = $i . '-' . $j;
				}
			
		
				$log->debug("codTipo == E, I = $i, J = $j, K = $k, L = $l");
			
				$log->debug("Nome: $nome");
			
				$oNome	= $em->getRepository('Entidades\ZgdocEndereco')->findOneBy(array('nome' => $nome, 'codLocal' => $codLocal));
			
				if (empty($oNome)) {
	
					#################################################################################
					## Salvar no banco
					#################################################################################
					try {
					
						$oEndereco		= new \Entidades\ZgdocEndereco();
						$oEndereco->setNome($nome);
						$oEndereco->setCodTipo($oTipo);
						$oEndereco->setCodLocal($oLocal);
						$oEndereco->setIndAtivo($ativo);
						$oEndereco->setIndBloqueado($bloqueado);
						$oEndereco->setRua($rua);
						$oEndereco->setEstante($estante);
						if ($codTipo == "E") {
							$oEndereco->setPrateleira($prateleira);
							$oEndereco->setColuna($coluna);
						}
						
						$numCri++;
						
						$em->persist($oEndereco);
						
						//if (($numCri % $commit) == 0) {
						//	$em->flush();
						//	$em->clear(); // Detaches all objects from Doctrine!
						//}
					
					} catch (\Exception $e) {
						echo '1'.\Zage\App\Util::encodeUrl('||'.htmlentities($e->getMessage()));
						exit;
					}
				}		
			}
		}
	}
	$numEnd++;
}

$em->flush();
$em->clear(); // Detaches all objects from Doctrine!

echo '0'.\Zage\App\Util::encodeUrl('|'.$numCri.'|'.htmlentities($tr->trans("Foram gerados %s endereços com sucesso",array('%s' => $numCri))));