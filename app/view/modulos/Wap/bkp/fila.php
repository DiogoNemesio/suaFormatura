<?php
#################################################################################
## Includes
#################################################################################
if (defined('DOC_ROOT')) {
	include_once(DOC_ROOT . 'include.php');
}else{
	include_once('../../../include.php');
}
require_once(CLASS_PATH . "/WhatsAPI/whatsprot.class.php");


$servidores 	= array (1,2,3,4);
$chips			= array	(1,2,3,4,5,6,7,8,9,10,11,12);
$numPrefixo		= 15;
$prefixoAtual	= 1;



foreach ($servidores as $servidor) {
	$oServ		= $em->getRepository('Entidades\ZgwapServidor')->findOneBy(array( 'codigo' => $servidor));
	foreach ($chips as $chip) {
		$ochip		= $em->getRepository('Entidades\ZgwapChip')->findOneBy(array( 'codigo' => $chip));
		for ($i = 1; $i <= $numPrefixo; $i++) {

			try {
				$oPre		= $em->getRepository('Entidades\ZgwapPrefixo')->findOneBy(array( 'codigo' => $prefixoAtual));
				
				$fila		= new \Entidades\ZgwapFila();
				$fila->setCodChip($ochip);
				$fila->setCodServidor($oServ);
				$fila->setCodPrefixo($oPre);	
				$em->persist($fila);
				$em->flush();
				$em->detach($fila);
				
				$prefixoAtual++;
				echo "Servidor: $servidor chip: $chip Prefixo: $prefixoAtual\n";
				
				
				if ($prefixoAtual > 718) {
					exit;
				}
				
			} catch (\Exception $e) {
				die($e->getMessage());
			}
		}
	}
}

$em->flush();
$em->clear();

