<?php
#################################################################################
## Includes
#################################################################################
if (defined('DOC_ROOT')) {
	include_once(DOC_ROOT . 'include.php');
}else{
	include_once('../include.php');
}


//$log->debug('POST:'.serialize($_POST));

#################################################################################
## Resgata os parâmetros passados pelo formulario
#################################################################################
if (isset($_POST['codDocumento'])) 	{
	$codDocumento	= \Zage\App\Util::antiInjection($_POST['codDocumento']);
}else{
	$system->criaAviso(\Zage\App\Aviso\Tipo::ERRO,$tr->trans('Falta de Parâmetros (%s) ',array('%s' => 'codDocumento')));
	echo '1'.\Zage\App\Util::encodeUrl('||');
	exit;
}


#################################################################################
## Verificar se o documento existe e resgatar os índices
#################################################################################
$doc	= $em->getRepository('Entidades\ZgdocDocumento')->findOneBy(array('codigo' => $codDocumento));

if (!$doc) {
	$system->criaAviso(\Zage\App\Aviso\Tipo::ERRO,$tr->trans('Documento (%s) não encontrado',array('%s' => $codDocumento)));
	echo '1'.\Zage\App\Util::encodeUrl('||');
	exit;
}else{
	$indices		= \Zage\Doc\Indice::lista($doc->getCodTipo()->getCodigo());
	if (!$indices) {
		$system->criaAviso(\Zage\App\Aviso\Tipo::ERRO,$tr->trans('Índices do documento (%s) não encontrado',array('%s' => $codDocumento)));
		echo '1'.\Zage\App\Util::encodeUrl('||');
		exit;
	}
}


#############################################################################################
## Verificar se o array de campos foi passado pelo formulário
#############################################################################################
if (isset($_POST["_zgIndice"])) {
	$_zgIndice = $_POST["_zgIndice"];
}else{
	$system->criaAviso(\Zage\App\Aviso\Tipo::ERRO,$tr->trans('Variável POST mal formada'). ', file: '.__FILE__);
	echo '1'.\Zage\App\Util::encodeUrl('||');
	exit;
}


#############################################################################################
## Resgata os parâmetros passados pelo formulario novamente, agora os campos dos índices
#############################################################################################
$clear	= false;
for ($i = 0; $i < sizeof($indices); $i++) {
	if (isset($_zgIndice[$indices[$i]->getCodigo()])) {
		$valorIndice	= \Zage\App\Util::antiInjection($_zgIndice[$indices[$i]->getCodigo()]);
	}else{
		$system->criaAviso(\Zage\App\Aviso\Tipo::ERRO,$tr->trans('Variável POST mal formada'). ', file: '.__FILE__);
		echo '1'.\Zage\App\Util::encodeUrl('||');
		exit;
	}
	
	#############################################################################################
	## Validar o campo
	#############################################################################################
	$tipo			= $indices[$i]->getCodTipo()->getCodigo();
	$err			= null;
	
	if ($tipo == "N") {
		if (!is_numeric($valorIndice)) 	$err	= 1;
	}elseif ($tipo == "DT") {
		if (\Zage\App\Util::validaData($valorIndice,$system->config["data"]["dateFormat"]) == false) {
			$err	= 1;
		}
	}elseif ($tipo == "DIN") {
		/** Retirar o dígito de milhar **/
		$valorIndice	= str_replace('.', '', $valorIndice);
	}elseif ($tipo == "P") {
		/** Retirar o % da string **/
		$valorIndice	= str_replace('%', '', $valorIndice);
	}
	
	if ($err !== null) {
		if ($clear	== true) {
			$em->clear();
		}
		$system->criaAviso(\Zage\App\Aviso\Tipo::ERRO,$tr->trans('Campo %s inválido',array('%s' => $indices[$i]->getNome())));
		echo '1'.\Zage\App\Util::encodeUrl('||');
		exit;
		
	}
	
	try {
		
		#################################################################################
		## Verificar se a informação já existe
		#################################################################################
		$oIndice	=	$em->getRepository('Entidades\ZgdocIndiceValor')->findOneBy(array('codDocumento' => $codDocumento,'codIndice' => $indices[$i]->getCodigo())); 
		
		if (!$oIndice) {
			$oIndice	= new \Entidades\ZgdocIndiceValor();
		}
		
		$dataAtual	= new \DateTime("now");
		$oIndice->setCodDocumento($doc);
		$oIndice->setCodIndice($indices[$i]);
		$oIndice->setValor($valorIndice);
		$oIndice->setData($dataAtual);
		$oIndice->setCodUsuario($_user);
		$em->persist($oIndice);
		$clear	= true;
	} catch (\Exception $e) {
		$system->criaAviso(\Zage\App\Aviso\Tipo::ERRO,$e->getMessage());
		echo '1'.\Zage\App\Util::encodeUrl('||'.htmlentities($e->getMessage()));
		exit;
	}
}



#################################################################################
## Atualiza o status do Documento para Indexado
#################################################################################
try {
		
	$doc->setIndIndexado(1);
	$em->persist($doc);
	$em->flush();
	$em->clear();
	
} catch (\Exception $e) {
	$system->criaAviso(\Zage\App\Aviso\Tipo::ERRO,$e->getMessage());
	echo '1'.\Zage\App\Util::encodeUrl('||'.htmlentities($e->getMessage()));
	exit;
}

echo '0'.\Zage\App\Util::encodeUrl('||');
