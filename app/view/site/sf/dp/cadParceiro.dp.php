<?php 
#################################################################################
## Includes
#################################################################################
if (defined('DOC_ROOT')) {
	include_once(DOC_ROOT . 'includeNoAuth.php');
}else{
	include_once('../../../includeNoAuth.php');
}
 
#################################################################################
## Variáveis globais
#################################################################################
global $em,$system,$tr;

#################################################################################
## Resgata os parâmetros passados pelo formulario
#################################################################################
if (isset($_POST['tipo']))				$tipo				= \Zage\App\Util::antiInjection($_POST['tipo']);
if (isset($_POST['email']))				$email				= \Zage\App\Util::antiInjection($_POST['email']);
if (isset($_POST['telComercial']))		$telComercial		= \Zage\App\Util::antiInjection($_POST['telComercial']);
if (isset($_POST['telCelular']))		$telCelular			= \Zage\App\Util::antiInjection($_POST['telCelular']);
if (isset($_POST['cidade']))			$cidade				= \Zage\App\Util::antiInjection($_POST['cidade']);
if (isset($_POST['atividade']))			$atividade			= \Zage\App\Util::antiInjection($_POST['atividade']);

if ($tipo == 'J'){
	
	if (isset($_POST['razao'])) 		$razao				= \Zage\App\Util::antiInjection($_POST['razao']);
	if (isset($_POST['cnpj']))	 		$cgc				= \Zage\App\Util::antiInjection($_POST['cnpj']);
	if (isset($_POST['fantasia'])) 		$nome				= \Zage\App\Util::antiInjection($_POST['fantasia']);
	
}elseif ($tipo == 'F'){
	
	if (isset($_POST['nome'])) 			$nome				= \Zage\App\Util::antiInjection($_POST['nome']);
	if (isset($_POST['cpf']))	 		$cgc				= \Zage\App\Util::antiInjection($_POST['cpf']);
	$razao			= null;
}

#################################################################################
## Limpar a variável de erro
#################################################################################
$err	= false;

#################################################################################
## Fazer validação dos campos
#################################################################################
/******* EMAIL *********/
if ((!empty($email)) && (strlen($email) > 200)) {
	die ('1'.\Zage\App\Util::encodeUrl('||'.htmlentities($tr->trans("O email não deve conter mais de 200 caracteres!"))));
}elseif (!empty($email)){
	if(\Zage\App\Util::validarEMail($email) == false){
		die ('1'.\Zage\App\Util::encodeUrl('||'.htmlentities($tr->trans("O email está inválido. Por favor, verifique!"))));
	}
}

/******* ATIVIDADE *********/
if (!isset($atividade) || (empty($atividade))) {
	die ('1'.\Zage\App\Util::encodeUrl('||'.htmlentities($tr->trans("Selecione a sua atividade principal!"))));
}

/******* CIDADE *********/
if (!isset($cidade) || (empty($cidade))) {
	die ('1'.\Zage\App\Util::encodeUrl('||'.htmlentities($tr->trans("Selecione a cidade sede!"))));
}

/******* VALIDAÇÕES DE PJ E PF *********/
if (!isset($tipo) || (empty($tipo))) {
	die ('1'.\Zage\App\Util::encodeUrl('||'.htmlentities($tr->trans("Selecione se você é PJ ou PF!"))));
}

if ($tipo == 'J'){
	/******* CNPJ *********/
	$valCgc			= new \Zage\App\Validador\Cnpj();
	if (empty($cgc)) {
		die ('1'.\Zage\App\Util::encodeUrl('||'.htmlentities($tr->trans("O CNPJ deve ser preenchido!"))));
	}else{
		if ($valCgc->isValid($cgc) == false) {
			die ('1'.\Zage\App\Util::encodeUrl('||'.htmlentities($tr->trans("CNPJ inválido!"))));
		}
	}
	
	/******* Razão *********/
	if (!isset($razao) || (empty($razao))) {
		die ('1'.\Zage\App\Util::encodeUrl('||'.htmlentities($tr->trans("A razão social deve ser preenchida!"))));
	}elseif ((!empty($razao)) && (strlen($razao) > 100)) {
		die ('1'.\Zage\App\Util::encodeUrl('||'.htmlentities($tr->trans("A razão social não deve conter mais de 100 caracteres!"))));
	}
	
	/******* Fantasia *********/
	if (!isset($nome) || (empty($nome))) {
		die ('1'.\Zage\App\Util::encodeUrl('||'.htmlentities($tr->trans("O nome fantasia deve ser preenchido!"))));
	}elseif ((!empty($nome)) && (strlen($nome) > 100)) {
		die ('1'.\Zage\App\Util::encodeUrl('||'.htmlentities($tr->trans("O nome fantasia não deve conter mais de 100 caractes!"))));
	}

}

if ($tipo == 'F'){
	/******* CPF *********/
	$valCgc			= new \Zage\App\Validador\Cpf();
	if (empty($cgc)) {
		die ('1'.\Zage\App\Util::encodeUrl('||'.htmlentities($tr->trans("O CPF deve ser preenchido!"))));
	}else{
		if ($valCgc->isValid($cgc) == false) {
			die ('1'.\Zage\App\Util::encodeUrl('||'.htmlentities($tr->trans("CPF inválido!"))));
		}
	}
	
	/******* Nome *********/
	if (!isset($nome) || (empty($nome))) {
		die ('1'.\Zage\App\Util::encodeUrl('||'.htmlentities($tr->trans("O nome completo deve ser preenchido!"))));
	}elseif ((!empty($nome)) && (strlen($nome) > 100)) {
		die ('1'.\Zage\App\Util::encodeUrl('||'.htmlentities($tr->trans("O nome não deve conter mais de 100 caracteres!"))));
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
	
	$oParceiro = new \Entidades\ZgadmOrganizacaoPrecadastro();
 	
 	$oTipoAtividade	= $em->getRepository('Entidades\ZgadmOrganizacaoPrecadastroAtividade')->findOneBy(array('codigo' => $atividade));
 	$oCidade		= $em->getRepository('Entidades\ZgadmCidade')->findOneBy(array('codigo' => $cidade));
 	$oTipo			= $em->getRepository('Entidades\ZgadmOrganizacaoPessoaTipo')->findOneBy(array('codigo' => $tipo));
 	
 	$oParceiro->setCodTipoPessoa($oTipo);
 	$oParceiro->setNome($nome);
 	$oParceiro->setRazao($razao);
 	$oParceiro->setCgc($cgc);
 	$oParceiro->setEmail($email);
 	$oParceiro->setTelefoneComercial($telComercial);
 	$oParceiro->setTelefoneCelular($telCelular);
 	
 	$oParceiro->setCodOrganizacaoPrecadastroAtividade($oTipoAtividade);
 	$oParceiro->setCodCidade($oCidade);
 	
 	$em->persist($oParceiro);
 	$em->flush();	
 	$em->clear();
 	
} catch (\Exception $e) {
 	$system->criaAviso(\Zage\App\Aviso\Tipo::ERRO,$e->getMessage());
 	echo '1'.\Zage\App\Util::encodeUrl('||'.htmlentities($e->getMessage()));
 	exit;
}
 
echo '0'.\Zage\App\Util::encodeUrl('|'.$oParceiro->getCodigo());