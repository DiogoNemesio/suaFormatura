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
if (isset($_POST['codLayout']))			$codLayout			= \Zage\App\Util::antiInjection($_POST['codLayout']);
if (isset($_POST['carteiras'])) 		$carteiras			= \Zage\App\Util::antiInjection($_POST['carteiras']);

if (!isset($carteiras))		$carteiras	= array();

$log->debug("POST Carteiras: ".serialize($_POST));

#################################################################################
## Fazer validação dos campos
#################################################################################
/** CodLayout **/
if (!isset($codLayout) || (empty($codLayout))) {
 	die ('1'.\Zage\App\Util::encodeUrl('||'.htmlentities("Campo CodLayout é obrigatório")));
}

$oLayout	= $em->getRepository('Entidades\ZgfinArquivoLayout')->findOneBy(array('codigo' => $codLayout));

if (!$oLayout) {
	die ('1'.\Zage\App\Util::encodeUrl('||'.htmlentities("Layout não encontrado !!!")));
}

#################################################################################
## Salvar no banco															#####
#################################################################################
try {
 	
	#################################################################################
	## Salvar as carteiras (Lista de carteiras )
	#################################################################################
 	/** Excluir **/
 	$infoCarteiras		= $em->getRepository('Entidades\ZgfinArquivoLayoutCarteira')->findBy(array('codLayout' => $codLayout));
 	for ($i = 0; $i < sizeof($infoCarteiras); $i++) {
 		if (!in_array($infoCarteiras[$i]->getCodCarteira()->getCodigo(), $carteiras)) {
 			try {
 				$em->remove($infoCarteiras[$i]);
 			} catch (\Exception $e) {
 				echo '1'.\Zage\App\Util::encodeUrl('||'.htmlentities("Não foi possível excluir da lista de carteiras o valor: ".$infoCarteiras[$i]->getCodCarteira()->getCodigo()." Erro: ".$e->getMessage()));
 				exit;
 			}
 		}
	}
 	 	/** Criar **/
 	for ($i = 0; $i < sizeof($carteiras); $i++) {
 		$infoValor		= $em->getRepository('Entidades\ZgfinArquivoLayoutCarteira')->findOneBy(array('codLayout' => $codLayout, 'codCarteira' => trim($carteiras[$i])));
 		if (!$infoValor) {
 			$oCarteira	= $em->getRepository('Entidades\ZgfinCarteira')->findOneBy(array('codigo' => trim($carteiras[$i])));
 			$oValor		= new \Entidades\ZgfinArquivoLayoutCarteira();
 			$oValor->setCodCarteira($oCarteira);
 			$oValor->setCodLayout($oLayout);
 			try {
 				$em->persist($oValor);
 			} catch (\Exception $e) {
 				echo '1'.\Zage\App\Util::encodeUrl('||'.htmlentities("Não foi possível cadastrar o valor: ".$carteiras[$i]." Erro: ".$e->getMessage()));
 				exit;
 			}
		}
	}
 		
	$em->flush();
	$em->clear();
 			
 	
} catch (\Exception $e) {
 	echo '1'.\Zage\App\Util::encodeUrl('||'.htmlentities($e->getMessage()));
 	exit;
}
echo '0'.\Zage\App\Util::encodeUrl('||'.htmlentities("Informações salvas com sucesso !!!"));