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
if (isset($_POST['codResposta']))		$codResposta		= \Zage\App\Util::antiInjection($_POST['codResposta']);
if (isset($_POST['resposta'])) 			$resposta			= \Zage\App\Util::antiInjection($_POST['resposta']);
if (isset($_POST['codPergunta'])) 		$codPergunta		= \Zage\App\Util::antiInjection($_POST['codPergunta']);
#################################################################################
## Limpar a variável de erro
#################################################################################
$err	= false;

#################################################################################
## Fazer validação dos campos
#################################################################################
/** Pergunta **/
if (!isset($resposta) || (empty($resposta))) {
	$system->criaAviso(\Zage\App\Aviso\Tipo::ERRO,"A Resposta deve ser preenchida !!!");
	$err	= 1;
}

if ((!empty($resposta)) && (strlen($resposta) > 200)) {
	$system->criaAviso(\Zage\App\Aviso\Tipo::ERRO,"A Pergunta não deve conter mais de 200 caracteres");
	$err	= 1;
}

/** Pergunta **/
if (!isset($codPergunta) || (empty($codPergunta))) {
	$system->criaAviso(\Zage\App\Aviso\Tipo::ERRO,"A pergunta deve ser preenchido !!!");
	$err	= 1;
}

if ($err != null) {
	echo '1'.\Zage\App\Util::encodeUrl('||'.htmlentities($err));
 	exit;
}
 
#################################################################################
## Salvar no banco															#####
#################################################################################
try {
	
	if (isset($codResposta) && (!empty($codResposta))) {
 		$oResposta	= $em->getRepository('Entidades\ZgappEnqueteResposta')->findOneBy(array('codigo' => $codResposta));
 		if (!$oResposta) $oResposta	= new \Entidades\ZgappEnqueteResposta();
 	}else{
 		$oResposta	= new \Entidades\ZgappEnqueteResposta();
 	}
 	
 	$oUsuario		= $em->getRepository('Entidades\ZgsegUsuario')->findOneBy(array('codigo' => $system->getCodUsuario()));
 	$oPergunta		= $em->getRepository('Entidades\ZgappEnquetePergunta')->findOneBy(array('codigo' => $codPergunta));
 	
 	$oResposta->setCodUsuario($oUsuario);
 	$oResposta->setCodPergunta($oPergunta);
 	$oResposta->setResposta($resposta);
 	$oResposta->setDataResposta(new \DateTime("now"));
 	
 	$em->persist($oResposta);
 	$em->flush();
 	$em->detach($oResposta);
 	
 	 	
} catch (\Exception $e) {
 	$system->criaAviso(\Zage\App\Aviso\Tipo::ERRO,$e->getMessage());
 	echo '1'.\Zage\App\Util::encodeUrl('||'.htmlentities($e->getMessage()));
 	exit;
}
 
$system->criaAviso(\Zage\App\Aviso\Tipo::INFO,"Informações salvas com sucesso");
echo '0'.\Zage\App\Util::encodeUrl('|'.$oResposta->getCodigo());