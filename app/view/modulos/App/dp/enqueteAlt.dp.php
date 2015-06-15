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
if (isset($_POST['codEnquete']))		$codEnquete			= \Zage\App\Util::antiInjection($_POST['codEnquete']);
if (isset($_POST['pergunta'])) 			$pergunta			= \Zage\App\Util::antiInjection($_POST['pergunta']);
if (isset($_POST['descricao'])) 		$descricao			= \Zage\App\Util::antiInjection($_POST['descricao']);
if (isset($_POST['codTipo'])) 			$codTipo			= \Zage\App\Util::antiInjection($_POST['codTipo']);
if (isset($_POST['dataPrazo']))			$dataPrazo			= \Zage\App\Util::antiInjection($_POST['dataPrazo']);
if (isset($_POST['tamanho']))			$tamanho			= \Zage\App\Util::antiInjection($_POST['tamanho']);
if (isset($_POST['valores'])) 			$valores			= \Zage\App\Util::antiInjection($_POST['valores']);
#################################################################################
## Limpar a variável de erro
#################################################################################
$err	= false;

#################################################################################
## Fazer validação dos campos
#################################################################################
/** Pergunta **/
if (!isset($pergunta) || (empty($pergunta))) {
	$system->criaAviso(\Zage\App\Aviso\Tipo::ERRO,"A Pergunta deve ser preenchida !!!");
	$err	= 1;
}

if ((!empty($pergunta)) && (strlen($pergunta) > 200)) {
	$system->criaAviso(\Zage\App\Aviso\Tipo::ERRO,"A Pergunta não deve conter mais de 200 caracteres");
	$err	= 1;
}

/** Descrição **/
if ((!empty($descricao)) && (strlen($descricao) > 100)) {
	$system->criaAviso(\Zage\App\Aviso\Tipo::ERRO,"Campo DESCRIÇÃO não deve conter mais de 100 caracteres");
	$err	= 1;
}

/** Tipo da pergunta **/
if (!isset($codTipo) || (empty($codTipo))) {
	$system->criaAviso(\Zage\App\Aviso\Tipo::ERRO,"O tipo da pergunta deve ser preenchido !!!");
	$err	= 1;
}

/** Tamanho **/
if ((empty($tamanho))) {
	$tamanho = null;
}else{
	$val = is_numeric($tamanho);
	if ($val == true){
		if($tamanho == 0){
			$tamanho = null;
		}
	}else{
		$system->criaAviso(\Zage\App\Aviso\Tipo::ERRO,"O Tamanho deve conter apenas números !!!");
		$err	= 1;
	}
}

/** Data 
if (!isset($dataCadastro) || (empty($dataCadastro))) {
	$system->criaAviso(\Zage\App\Aviso\Tipo::ERRO,"Campo DATA CADASTRO é obrigatório");
	$err	= 1;
}else{
	$valData	= new \Zage\App\Validador\DataBR();
	
	if ($valData->isValid($dataCadastro) == false) {
		$system->criaAviso(\Zage\App\Aviso\Tipo::ERRO,"Campo DATA CADASTRO inválido");
		$err	= 1;
	}
}
**/
if ($err != null) {
	echo '1'.\Zage\App\Util::encodeUrl('||'.htmlentities($err));
 	exit;
}
 
#################################################################################
## Salvar no banco															#####
#################################################################################
try {
	
	if (isset($codEnquete) && (!empty($codEnquete))) {
 		$oEnquete	= $em->getRepository('Entidades\ZgappEnquetePergunta')->findOneBy(array('codOrganizacao' => $system->getCodOrganizacao(), 'codigo' => $codEnquete));
 		if (!$oEnquete) $oEnquete	= new \Entidades\ZgappEnquetePergunta();
 	}else{
 		$oEnquete	= new \Entidades\ZgappEnquetePergunta();
 	}
 	
 	if (!empty($dataPrazo)) {
 		$dataPrazo		= DateTime::createFromFormat($system->config["data"]["dateFormat"], $dataPrazo);
 	}else{
 		$dataPrazo		= null;
 	}
 	
 	$oOrganizacao	= $em->getRepository('Entidades\ZgadmOrganizacao')->findOneBy(array('codigo' => $system->getCodOrganizacao()));
 	$oTipo			= $em->getRepository('Entidades\ZgappEnquetePerguntaTipo')->findOneBy(array('codigo' => $codTipo));
 	$oStatus		= $em->getRepository('Entidades\ZgappEnqueteStatus')->findOneBy(array('codigo' => 'A'));
 	
 	$oEnquete->setCodOrganizacao($oOrganizacao);
 	$oEnquete->setCodTipo($oTipo);
 	$oEnquete->setCodStatus($oStatus);
 	$oEnquete->setPergunta($pergunta);
 	$oEnquete->setDescricao($descricao);
 	$oEnquete->setDataCadastro(new \DateTime("now"));
 	$oEnquete->setDataPrazo($dataPrazo);
 	$oEnquete->setTamanho($tamanho);
 	
 	$em->persist($oEnquete);
 	$em->flush();
 	
 	/**
 	 * Salvar os valores (Lista de valores )
 	 */
 	if ($valores) {
 			
 		$aValores	= explode(",", $valores);
 			
 		/** Excluir **/
 		$infoValores		= $em->getRepository('Entidades\ZgappEnquetePerguntaValor')->findBy(array('codPergunta' => $codPergunta));
 	
 		for ($i = 0; $i < sizeof($infoValores); $i++) {
 			if (!in_array($infoValores[$i]->getValor(), $aValores)) {
 				try {
 					$em->remove($infoValores[$i]);
 					$em->flush();
 				} catch (\Exception $e) {
 					$system->criaAviso(\Zage\App\Aviso\Tipo::ERRO,"Não foi possível excluir da lista de valores o valor: ".$infoValores[$i]->getValor()." Erro: ".$e->getMessage());
 					echo '1'.\Zage\App\Util::encodeUrl('||'.htmlentities($e->getMessage()));
 					exit;
 				}
 			}
 	
 		}
 	
 		/** Criar **/
 		for ($i = 0; $i < sizeof($aValores); $i++) {
 	
 			$infoValor		= $em->getRepository('Entidades\ZgappEnquetePerguntaValor')->findBy(array('codPergunta' => $codPergunta , 'valor' => $aValores[$i]));
 	
 			if (!$infoValor) {
 				$oValor		= new \Entidades\ZgappEnquetePerguntaValor();
 				$oValor->setcodPergunta($oEnquete);
 				$oValor->setValor($aValores[$i]);
 					
 				try {
 					$em->persist($oValor);
 					$em->flush();
 					$em->detach($oValor);
 				} catch (\Exception $e) {
 					$system->criaAviso(\Zage\App\Aviso\Tipo::ERRO,"Não foi possível cadastrar o valor: ".$aValores[$i]." Erro: ".$e->getMessage());
 					echo '1'.\Zage\App\Util::encodeUrl('||'.htmlentities($e->getMessage()));
 					exit;
 				}
 			}
 	
 		}
 	
 	}
 	
 	$em->detach($oEnquete);
 	
 	 	
} catch (\Exception $e) {
 	$system->criaAviso(\Zage\App\Aviso\Tipo::ERRO,$e->getMessage());
 	echo '1'.\Zage\App\Util::encodeUrl('||'.htmlentities($e->getMessage()));
 	exit;
}
 
$system->criaAviso(\Zage\App\Aviso\Tipo::INFO,"Informações salvas com sucesso");
echo '0'.\Zage\App\Util::encodeUrl('|'.$oEnquete->getCodigo());