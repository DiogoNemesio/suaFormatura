<?php
#################################################################################
## Includes
#################################################################################
if (defined('DOC_ROOT')) {
	include_once(DOC_ROOT . 'include.php');
}else{
 	include_once('../include.php');
}

use \Zend\Mail;
use \Zend\Mail\Message;
use \Zend\Mime\Message as MimeMessage;
use \Zend\Mime\Part as MimePart;
Use \Zend\Mime;

#################################################################################
## Resgata os parâmetros passados pelo formulario
#################################################################################

if (isset($_POST['codSegmento'])) 		$codSegmento	= \Zage\App\Util::antiInjection($_POST['codSegmento']);
if (isset($_POST['associacao'])) 		$associacao			= \Zage\App\Util::antiInjection($_POST['associacao']);
if (!isset($associacao))				$associacao			= array();

#################################################################################
## Limpar a variável de erro
#################################################################################
$err	= false;

#################################################################################
## Fazer validação dos campos
#################################################################################
if (!isset($codSegmento) || empty($codSegmento)) {
	$system->criaAviso(\Zage\App\Aviso\Tipo::ERRO,"Parâmentro COD_SEGMENTO não encontrado.");
	$err	= 1;
}else{
	$oSegmento = $em->getRepository('Entidades\ZgfinSegmentoMercado')->findOneBy(array('codigo' => $codSegmento));
	if (!$oSegmento){
		$system->criaAviso(\Zage\App\Aviso\Tipo::ERRO,"Ops!! Não encontrar o segmento de mercado selecionado. Caso o problema continue entre em contato com o suporte do portal SUAFORMATURA.COM");
		$err	= 1;
	}
}

if ($err != null) {
	echo '1'.\Zage\App\Util::encodeUrl('||'.htmlentities($err));
	exit;
}

#################################################################################
## Salvar no banco
#################################################################################
try {
	
	#################################################################################
	## Salvar a associação entre segmento de mercado e categoria
	#################################################################################	
	//Retirar categoria
	$segCatAssociado	= $em->getRepository('Entidades\ZgfinSegmentoCategoria')->findBy(array('codSegmento' => $codSegmento));
	for ($i = 0; $i < sizeof($segCatAssociado); $i++) {
		if (!in_array($segCatAssociado[$i]->getCodCategoria()->getCodigo(), $associacao)) {
			try {
				$em->remove($segCatAssociado[$i]);
			} catch (\Exception $e) {
				echo '1'.\Zage\App\Util::encodeUrl('||'.htmlentities("Não foi possível excluir a categoria: ".$segCatAssociado[$i]->getCodCategoria()->getDescricao()." Erro: ".$e->getMessage()));
				exit;
			}
		}
	}
	//Atribuir categoria
	for ($i = 0; $i < sizeof($associacao); $i++) {
		$oAssociacao		= $em->getRepository('Entidades\ZgfinSegmentoCategoria')->findOneBy(array('codSegmento' => $codSegmento, 'codCategoria' => $associacao[$i]));
		if (!$oAssociacao) {
			$oAssociacao		= new \Entidades\ZgfinSegmentoCategoria();
		}	
		
		$oCategoria		= $em->getRepository('Entidades\ZgfinCategoria')->findOneBy(array('codigo' => $associacao[$i]));
		
		$oAssociacao->setCodSegmento($oSegmento);
		$oAssociacao->setCodCategoria($oCategoria);
		
		try {
			$em->persist($oAssociacao);
		} catch (\Exception $e) {
			echo '1'.\Zage\App\Util::encodeUrl('||'.htmlentities("Não foi possível associar a categoria: ".$associacao[$i]." Erro: ".$e->getMessage()));
			exit;
		}
	}

	#################################################################################
	## Salvar as informações
	#################################################################################
	try {
		$em->flush();
		$em->clear();
	} catch (Exception $e) {
		$log->debug("Erro ao salvar o usuário:". $e->getTraceAsString());
		throw new \Exception("Ops!! Não conseguimos realizar a operação. Caso o problema continue entre em contato com o suporte do portal SUAFORMATURA.COM");
	}
		
} catch (\Exception $e) {
	$system->criaAviso(\Zage\App\Aviso\Tipo::ERRO,$e->getMessage());
	echo '1'.\Zage\App\Util::encodeUrl('||'.htmlentities($e->getMessage()));
	exit;
}


$system->criaAviso(\Zage\App\Aviso\Tipo::INFO,$tr->trans("Informações salvas com sucesso"));
echo '0'.\Zage\App\Util::encodeUrl('|');
