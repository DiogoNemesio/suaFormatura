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
if (isset($_POST['codIndice'])) 		$codIndice		= \Zage\App\Util::antiInjection($_POST['codIndice']);
if (isset($_POST['docTipo']))	 		$docTipo		= \Zage\App\Util::antiInjection($_POST['docTipo']);
if (isset($_POST['nome'])) 				$nome			= \Zage\App\Util::antiInjection($_POST['nome']);
if (isset($_POST['descricao']))			$descricao		= \Zage\App\Util::antiInjection($_POST['descricao']);
if (isset($_POST['obrigatorio'])) 		$obrigatorio	= \Zage\App\Util::antiInjection($_POST['obrigatorio']);
if (isset($_POST['visivel']))	 		$visivel		= \Zage\App\Util::antiInjection($_POST['visivel']);
if (isset($_POST['ativo']))	 			$ativo			= \Zage\App\Util::antiInjection($_POST['ativo']);
if (isset($_POST['tipo'])) 				$tipo			= \Zage\App\Util::antiInjection($_POST['tipo']);
if (isset($_POST['mascara'])) 			$mascara		= \Zage\App\Util::antiInjection($_POST['mascara']);
if (isset($_POST['tamanho'])) 			$tamanho		= \Zage\App\Util::antiInjection($_POST['tamanho']);
if (isset($_POST['valores'])) 			$valores		= \Zage\App\Util::antiInjection($_POST['valores']);
if (isset($_POST['valorPadrao'])) 		$valorPadrao	= \Zage\App\Util::antiInjection($_POST['valorPadrao']);

#################################################################################
## Limpar a variável de erro
#################################################################################
$err	= false;

#################################################################################
## Fazer validação dos campos
#################################################################################
 
/** Nome **/
if (!isset($nome) || (empty($nome))) {
 	$system->criaAviso(\Zage\App\Aviso\Tipo::ERRO,"Campo Nome deve conter 60 caracteres");
	$err	= 1;
}

if ((!empty($nome)) && (strlen($nome) > 60)) {
	$system->criaAviso(\Zage\App\Aviso\Tipo::ERRO,"Campo Nome não deve ter mais de 60 caracteres");
	$err	= 1;
}

$oIndice	= $em->getRepository('Entidades\ZgdocIndice')->findOneBy(array('nome' => $nome, 'codDocumentoTipo' => $docTipo));

if (($oIndice != null) && ($oIndice->getCodigo() != $codIndice)){
	$system->criaAviso(\Zage\App\Aviso\Tipo::ERRO,$tr->trans("NOME do índice já existe"));
	$err 	= 1;
}
 
/** Tamanho**/
if (!is_numeric($tamanho) && (!empty($tamanho))) {
 	$system->criaAviso(\Zage\App\Aviso\Tipo::ERRO,"Campo Tamanho deve conter apenas números");
	$err	= 1;
}
 
if (is_numeric($tamanho) && $tamanho > 4000) {
	$system->criaAviso(\Zage\App\Aviso\Tipo::ERRO,"Campo Tamanho não deve ser maior que 4000");
	$err	= 1;
}

/** Obrigatorio **/
if (isset($obrigatorio) && (!empty($obrigatorio))) {
	$obrigatorio	= 1;
}else{
	$obrigatorio	= 0;
}

/** Visível **/
if (isset($visivel) && (!empty($visivel))) {
	$visivel	= 1;
}else{
	$visivel	= 0;
}

/** Ativo **/
if (isset($ativo) && (!empty($ativo))) {
	$ativo	= 1;
}else{
	$ativo	= 0;
}

if ($err != null) {
	echo '1'.\Zage\App\Util::encodeUrl('||'.htmlentities($err));
 	exit;
}
 
#################################################################################
## Salvar no banco
#################################################################################
try {
	
	if (empty($tamanho)) {
		$tamanho = 4000;
	}
 
	if (isset($codIndice) && (!empty($codIndice))) {
 		$oIndice	= $em->getRepository('Entidades\ZgdocIndice')->findOneBy(array('codigo' => $codIndice));
 		if (!$oIndice) $oIndice	= new \Entidades\ZgdocIndice();
 	}else{
 		$oIndice	= new \Entidades\ZgdocIndice();
 	}
 	
 	$oDocTipo	= $em->getRepository('Entidades\ZgdocDocumentoTipo')->findOneBy(array('codigo' => $docTipo));
 	$oTipo		= $em->getRepository('Entidades\ZgdocIndiceTipo')->findOneBy(array('codigo' => $tipo));
 	
 	if (!$oDocTipo) {
 		$system->criaAviso(\Zage\App\Aviso\Tipo::ERRO,$tr->trans("Tipo de Documento não encontrado"));
		echo '1'.\Zage\App\Util::encodeUrl('||');
 		exit;
 	}
 	
 	if (!$oTipo) {
 		$system->criaAviso(\Zage\App\Aviso\Tipo::ERRO,$tr->trans("Tipo de Índice não encontrado"));
		echo '1'.\Zage\App\Util::encodeUrl('||');
 		exit;
 	}
 	
 	$oIndice->setCodDocumentoTipo($oDocTipo);
 	$oIndice->setNome($nome);
 	$oIndice->setDescricao($descricao);
 	$oIndice->setIndObrigatorio($obrigatorio);
 	$oIndice->setIndVisivel($visivel);
 	$oIndice->setCodTipo($oTipo);
 	$oIndice->setTamanho($tamanho);
 	$oIndice->setIndAtivo($ativo);
 	$oIndice->setMascara($mascara);
 	$oIndice->setValorPadrao($valorPadrao);
 	
 	$em->persist($oIndice);
 	$em->flush();
 	
 	
 	/**
 	 * Salvar os valores (Lista de valores )
 	 */
 	if ($valores) {
 		
 		$aValores	= explode(",", $valores);
 		
 		/** Excluir **/
 		$infoValores		= $em->getRepository('Entidades\ZgdocIndiceTipoValor')->findBy(array('codIndice' => $codIndice));

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
 			
 			$infoValor		= $em->getRepository('Entidades\ZgdocIndiceTipoValor')->findBy(array('codIndice' => $codIndice , 'valor' => $aValores[$i]));
 			
 			if (!$infoValor) {
	 			$oValor		= new \Entidades\ZgdocIndiceTipoValor();
	 			$oValor->setCodIndice($oIndice);
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
 	
 	$em->detach($oIndice);
 	
 	
} catch (\Exception $e) {
 	$system->criaAviso(\Zage\App\Aviso\Tipo::ERRO,$e->getMessage());
 	echo '1'.\Zage\App\Util::encodeUrl('||'.htmlentities($e->getMessage()));
 	exit;
}
 
$system->criaAviso(\Zage\App\Aviso\Tipo::INFO,"Informações salvas com sucesso");
echo '0'.\Zage\App\Util::encodeUrl('|'.$oIndice->getCodigo());