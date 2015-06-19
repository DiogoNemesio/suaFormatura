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
if (isset($_POST['codBanco']))			$codBanco			= \Zage\App\Util::antiInjection($_POST['codBanco']);
if (isset($_POST['carteiras'])) 		$carteiras			= \Zage\App\Util::antiInjection($_POST['carteiras']);

#################################################################################
## Fazer validação dos campos
#################################################################################
/** Banco **/
if (!isset($codBanco) || (empty($codBanco))) {
	die ('1'.\Zage\App\Util::encodeUrl('||'."O Banco não foi informado !!!"));
}else{
	$oBanco	= $em->getRepository('Entidades\ZgfinBanco')->findOneBy(array('codigo' => $codBanco));
	if (!$oBanco) {
		die ('1'.\Zage\App\Util::encodeUrl('||'."Banco não encontrado !!!"));
	}
}


if (isset($carteiras) || (!empty($carteiras))) {
	$aCarteiras	= explode(",", $carteiras);
	for ($i = 0; $i < sizeof($aCarteiras); $i++) {
		$aCarteiras[$i]	= trim($aCarteiras[$i]);
		if (strlen($aCarteiras[$i]) > 3) {
			die ('1'.\Zage\App\Util::encodeUrl('||'."Carteira '".trim($aCarteiras[$i])."' tem mais que 3 caracteres !!!"));
		}
	}
}else{
	$aCarteiras	= array();
}
 
#################################################################################
## Salvar no banco															#####
#################################################################################
try {
 	
 	/**
 	 * Salvar os carteiras (Lista de carteiras )
 	 */
 	if (sizeof($aCarteiras) > 0) {
 			
 		/** Excluir **/
 		$infoCarteiras		= $em->getRepository('Entidades\ZgfinCarteira')->findBy(array('codBanco' => $codBanco));
 	
 		for ($i = 0; $i < sizeof($infoCarteiras); $i++) {
 			if (!in_array($infoCarteiras[$i]->getCodCarteira(), $aCarteiras)) {
 				try {
 					
 					#################################################################################
 					## Verifica se a carteira está em uso
 					#################################################################################
 					$contas		= $em->getRepository('Entidades\ZgfinConta')->findBy(array('codCarteira' => $infoCarteiras[$i]->getCodCarteira()));
 					
 					if ($contas) {
 						die ('1'.\Zage\App\Util::encodeUrl('||'."Carteira '".trim($aCarteiras[$i])."' está em uso em alguma conta corrente!!!"));
 					}

 					$layouts	= $em->getRepository('Entidades\ZgfinArquivoLayoutCarteira')->findBy(array('codCarteira' => $infoCarteiras[$i]->getCodCarteira()));
 				 	if ($layouts) {
 						die ('1'.\Zage\App\Util::encodeUrl('||'."Carteira '".trim($aCarteiras[$i])."' está em uso em algum layout de arquivo de banco !!!"));
 					}
 					
 					$em->remove($infoCarteiras[$i]);
 				} catch (\Exception $e) {
 					echo '1'.\Zage\App\Util::encodeUrl('||'.htmlentities("Não foi possível excluir da lista de carteiras o valor: ".$infoCarteiras[$i]->getCodCarteira()." Erro: ".$e->getMessage()));
 					exit;
 				}
 			}
 	
 		}
 	
 		/** Criar **/
 		for ($i = 0; $i < sizeof($aCarteiras); $i++) {
 	
 			$infoValor		= $em->getRepository('Entidades\ZgfinCarteira')->findBy(array('codBanco' => $codBanco , 'codCarteira' => trim($aCarteiras[$i])));
 	
 			if (!$infoValor) {
 				$oValor		= new \Entidades\ZgfinCarteira();
 				$oValor->setCodBanco($oBanco);
 				$oValor->setCodCarteira(trim($aCarteiras[$i]));
 					
 				try {
 					$em->persist($oValor);
 				} catch (\Exception $e) {
 					echo '1'.\Zage\App\Util::encodeUrl('||'.htmlentities("Não foi possível cadastrar o valor: ".$aCarteiras[$i]." Erro: ".$e->getMessage()));
 					exit;
 				}
 			}
 	
 		}
 		
 		$em->flush();
 		$em->clear();
 			
 	
 	}
 	
} catch (\Exception $e) {
 	echo '1'.\Zage\App\Util::encodeUrl('||'.htmlentities($e->getMessage()));
 	exit;
}
echo '0'.\Zage\App\Util::encodeUrl('||'.htmlentities("Informações salvas com sucesso !!!"));