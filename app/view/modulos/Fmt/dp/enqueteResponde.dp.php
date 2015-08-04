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
if (isset($_POST['codPergunta']))			$codPergunta		= \Zage\App\Util::antiInjection($_POST['codPergunta']);
## Tipos respostas ##
if (isset($_POST['data']))					$data				= \Zage\App\Util::antiInjection($_POST['data']);
if (isset($_POST['livre']))					$livre				= \Zage\App\Util::antiInjection($_POST['livre']);
if (isset($_POST['lista']))					$lista				= \Zage\App\Util::antiInjection($_POST['lista']);
if (isset($_POST['numero']))				$numero				= \Zage\App\Util::antiInjection($_POST['numero']);
if (isset($_POST['simNao']))				$simNao				= \Zage\App\Util::antiInjection($_POST['simNao']);

if (isset($data))   $resposta = $data;
if (isset($livre))  $resposta = $livre;
if (isset($lista))  $resposta = $lista;
if (isset($numero)) $resposta = $numero;
if (isset($simNao)) $resposta = $simNao;

#################################################################################
## Limpar a variável de erro
#################################################################################
$err	= false;

#################################################################################
## Fazer validação dos campos
#################################################################################
/** Cargo **/
if ((empty($resposta))) {
	die ('1'.\Zage\App\Util::encodeUrl('||'.htmlentities($tr->trans("Campo RESPOSTA é obrigatório!"))));
	$err	= 1;
}

if ((!empty($resposta)) && (strlen($resposta) > 200)) {
	die ('1'.\Zage\App\Util::encodeUrl('||'.htmlentities($tr->trans("Campo RESPOSTA não deve conter mais de 200 caracteres!"))));
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
	
 	if (!$oResposta) $oResposta	= new \Entidades\ZgappEnqueteResposta();
 	
 	$oUsuario	= $em->getRepository('Entidades\ZgsegUsuario')->findOneBy(array('codigo' => $system->getCodUsuario()));
 	$oPergunta	= $em->getRepository('Entidades\ZgappEnquetePergunta')->findOneBy(array('codigo' => $codPergunta));
 	
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
 
die ('0'.\Zage\App\Util::encodeUrl('||'.htmlentities($tr->trans("Pergunta respondida com sucesso."))));
echo '0'.\Zage\App\Util::encodeUrl('|'.$oResposta->getCodigo());
