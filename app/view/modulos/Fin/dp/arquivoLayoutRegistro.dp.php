<?php
#################################################################################
## Includes
#################################################################################
if (defined('DOC_ROOT')) {
	include_once(DOC_ROOT . 'include.php');
}else{
 	include_once('../include.php');
}
 
global $system,$tr,$log,$em;

#################################################################################
## Resgata os parâmetros passados pelo formulario
#################################################################################
if (isset($_POST['codLayout']))			$codLayout			= \Zage\App\Util::antiInjection($_POST['codLayout']);
if (isset($_POST['codTipoRegistro']))	$codTipoRegistro	= \Zage\App\Util::antiInjection($_POST['codTipoRegistro']);
if (isset($_POST['ordem']))				$ordem				= \Zage\App\Util::antiInjection($_POST['ordem']);
if (isset($_POST['posicao']))			$posicao			= \Zage\App\Util::antiInjection($_POST['posicao']);
if (isset($_POST['tamanho']))			$tamanho			= \Zage\App\Util::antiInjection($_POST['tamanho']);
if (isset($_POST['codFormato']))		$codFormato			= \Zage\App\Util::antiInjection($_POST['codFormato']);
if (isset($_POST['codVariavel']))		$codVariavel		= \Zage\App\Util::antiInjection($_POST['codVariavel']);
if (isset($_POST['valorFixo']))			$valorFixo			= \Zage\App\Util::antiInjection($_POST['valorFixo']);
if (isset($_POST['codRegistro']))		$codRegistro		= \Zage\App\Util::antiInjection($_POST['codRegistro']);

#################################################################################
## Caso não venha as variáveis (ARRAY) inicializar eles
#################################################################################
if (!isset($ordem))				$ordem				= array();
if (!isset($posicao))			$posicao			= array();
if (!isset($tamanho))			$tamanho			= array();
if (!isset($codFormato))		$codFormato			= array();
if (!isset($codVariavel))		$codVariavel		= array();
if (!isset($valorFixo))			$valorFixo			= array();
if (!isset($codRegistro))		$codRegistro		= array();


$log->debug("POst: ".serialize($_POST));

#################################################################################
## Limpar a variável de erro
#################################################################################
$err	= false;

#################################################################################
## Fazer validação dos campos
#################################################################################
/** CodLayout **/
if (!isset($codLayout) || (empty($codLayout))) {
 	$system->criaAviso(\Zage\App\Aviso\Tipo::ERRO,"Campo 'COD_LAYOUT' é obrigatório");
	$err	= 1;
}

$oLayout	 = $em->getRepository('Entidades\ZgfinArquivoLayout')->findOneBy(array('codigo' => $codLayout));

if (!$oLayout){
	$system->criaAviso(\Zage\App\Aviso\Tipo::ERRO,$tr->trans("Layout não encontrado !!!"));
	$err 	= 1;
}

/** CodTipoRegistro **/
if (!isset($codTipoRegistro) || (empty($codTipoRegistro))) {
	$system->criaAviso(\Zage\App\Aviso\Tipo::ERRO,"Campo 'COD_TIPO_REGISTRO' é obrigatório");
	$err	= 1;
}

$oTipoRegistro	 = $em->getRepository('Entidades\ZgfinArquivoRegistroTipo')->findOneBy(array('codigo' => $codTipoRegistro));

if (!$oTipoRegistro){
	$system->criaAviso(\Zage\App\Aviso\Tipo::ERRO,$tr->trans("Tipo de registro não encontrado !!!"));
	$err 	= 1;
}

/** Ordem **/
if (!is_array($ordem)) {
	$system->criaAviso(\Zage\App\Aviso\Tipo::ERRO,$tr->trans("Campo ORDEM inválido !!!"));
	$err 	= 1;
}

/** Posição **/
if (!is_array($posicao)) {
	$system->criaAviso(\Zage\App\Aviso\Tipo::ERRO,$tr->trans("Campo POSIÇÃO inválido !!!"));
	$err 	= 1;
}

/** Tamanho **/
if (!is_array($tamanho)) {
	$system->criaAviso(\Zage\App\Aviso\Tipo::ERRO,$tr->trans("Campo TAMANHO inválido !!!"));
	$err 	= 1;
}

/** CodFormato **/
if (!is_array($codFormato)) {
	$system->criaAviso(\Zage\App\Aviso\Tipo::ERRO,$tr->trans("Campo COD_FORMATO inválido !!!"));
	$err 	= 1;
}

/** CodVariável **/
if (!is_array($codVariavel)) {
	$system->criaAviso(\Zage\App\Aviso\Tipo::ERRO,$tr->trans("Campo COD_VARIÁVEL inválido !!!"));
	$err 	= 1;
}

/** Valor Fixo **/
if (!is_array($valorFixo)) {
	$system->criaAviso(\Zage\App\Aviso\Tipo::ERRO,$tr->trans("Campo VALOR FIXO inválido !!!"));
	$err 	= 1;
}

/** CodRegistro **/
if (!is_array($codRegistro)) {
	$system->criaAviso(\Zage\App\Aviso\Tipo::ERRO,$tr->trans("Campo COD_REGISTRO inválido !!!"));
	$err 	= 1;
}

#################################################################################
## Validar o tamanho dos arrays
#################################################################################
$numReg	= sizeof($codRegistro);

/** Ordem **/
if (sizeof($ordem) != $numReg) {
	$system->criaAviso(\Zage\App\Aviso\Tipo::ERRO,$tr->trans("Campo ORDEM com tamanho inválido !!!"));
	$err 	= 1;
}

/** Posição **/
if (sizeof($posicao) != $numReg) {
	$system->criaAviso(\Zage\App\Aviso\Tipo::ERRO,$tr->trans("Campo POSIÇÃO com tamanho inválido !!!"));
	$err 	= 1;
}

/** Tamanho **/
if (sizeof($tamanho) != $numReg) {
	$system->criaAviso(\Zage\App\Aviso\Tipo::ERRO,$tr->trans("Campo TAMANHO com tamanho inválido !!!"));
	$err 	= 1;
}

/** CodFormato **/
if (sizeof($codFormato) != $numReg) {
	$system->criaAviso(\Zage\App\Aviso\Tipo::ERRO,$tr->trans("Campo COD_FORMATO com tamanho inválido !!!"));
	$err 	= 1;
}

/** CodVariável **/
if (sizeof($codVariavel) != $numReg) {
	$system->criaAviso(\Zage\App\Aviso\Tipo::ERRO,$tr->trans("Campo COD_VARIAVEL com tamanho inválido !!!"));
	$err 	= 1;
}

/** Valor Fixo **/
if (sizeof($valorFixo) != $numReg) {
	$system->criaAviso(\Zage\App\Aviso\Tipo::ERRO,$tr->trans("Campo VALOR FIXO com tamanho inválido !!!"));
	$err 	= 1;
}

#################################################################################
## Validar os valores fixos
#################################################################################
for ($i = 0; $i < $numReg; $i++) {
	if (!empty($valorFixo[$i])) {
		if (strlen($valorFixo[$i]) > $tamanho[$i]) {
			$system->criaAviso(\Zage\App\Aviso\Tipo::ERRO,$tr->trans("O Valor fixo da posição '".$posicao[$i]."' tem mais caracteres que o tamanho do campo !!!"));
			$err 	= 1;
		}
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
	## Apagar os registros
	#################################################################################
	$registros			= $em->getRepository('Entidades\ZgfinArquivoLayoutRegistro')->findBy(array('codLayout' => $codLayout,'codTipoRegistro' => $codTipoRegistro),array('ordem' => "ASC"));
	
	for ($i = 0; $i < sizeof($registros); $i++) {
		if (!in_array($registros[$i]->getCodigo(), $codRegistro)) {
			try {
				$em->remove($registros[$i]);
			} catch (\Exception $e) {
				$system->criaAviso(\Zage\App\Aviso\Tipo::ERRO,"Não foi possível excluir o registro de ordem: ".$registros[$i]->getOrdem()." Erro: ".$e->getMessage());
				echo '1'.\Zage\App\Util::encodeUrl('||'.htmlentities($e->getMessage()));
				exit;
			}
		}
	}
	
	#################################################################################
	## Criar / Alterar
	#################################################################################
	for ($i = 0; $i < $numReg; $i++) {
		
		#################################################################################
		## Verifica se o registro já existe no banco
		#################################################################################
		if (!empty($codRegistro[$i])) {
			$oReg		= $em->getRepository('Entidades\ZgfinArquivoLayoutRegistro')->findOneBy(array('codigo' => $codRegistro[$i]));
			if (!$oReg)	$oReg	= new \Entidades\ZgfinArquivoLayoutRegistro();
		}else{
			$oReg	= new \Entidades\ZgfinArquivoLayoutRegistro();
		}
		
		#################################################################################
		## Constroi os objetos
		#################################################################################
		$oFormato		= $em->getRepository('Entidades\ZgfinArquivoCampoFormato')->findOneBy(array('codigo' => $codFormato[$i]));
		if (!empty($codVariavel[$i])) {
			$oVariavel		= $em->getRepository('Entidades\ZgfinArquivoVariavel')->findOneBy(array('codigo' => $codVariavel[$i]));
		}else{
			$oVariavel		= null;
		}
		
		
		$oReg->setCodFormato($oFormato);
		$oReg->setCodLayout($oLayout);
		$oReg->setCodTipoRegistro($oTipoRegistro);
		$oReg->setCodVariavel($oVariavel);
		$oReg->setOrdem($ordem[$i]);
		$oReg->setPosicaoInicial($posicao[$i]);
		$oReg->setTamanho($tamanho[$i]);
		$oReg->setValorFixo($valorFixo[$i]);
		
		$em->persist($oReg);
	}
	
 	$em->flush();
 	$em->clear();
 	 	
} catch (\Exception $e) {
 	$system->criaAviso(\Zage\App\Aviso\Tipo::ERRO,$e->getMessage());
 	echo '1'.\Zage\App\Util::encodeUrl('||'.htmlentities($e->getMessage()));
 	exit;
}
 
$system->criaAviso(\Zage\App\Aviso\Tipo::INFO,"Informações salvas com sucesso");
echo '0'.\Zage\App\Util::encodeUrl('||');