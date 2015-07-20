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
if (isset($_POST['codOrganizacao']))	$codOrganizacao		= \Zage\App\Util::antiInjection($_POST['codOrganizacao']);
if (isset($_POST['tipo']))				$tipo				= \Zage\App\Util::antiInjection($_POST['tipo']);
if (isset($_POST['ident']))				$ident				= \Zage\App\Util::antiInjection($_POST['ident']);
if (isset($_POST['email']))				$email				= \Zage\App\Util::antiInjection($_POST['email']);

if ($tipo == 'J'){
	
	if (isset($_POST['razao'])) 		$razao				= \Zage\App\Util::antiInjection($_POST['razao']);
	if (isset($_POST['cnpj']))	 		$cgc				= \Zage\App\Util::antiInjection($_POST['cnpj']);
	if (isset($_POST['fantasia'])) 		$nome				= \Zage\App\Util::antiInjection($_POST['fantasia']);
	if (isset($_POST['inscrEst'])) 		$inscEstadual		= \Zage\App\Util::antiInjection($_POST['inscrEst']);
	if (isset($_POST['inscrMun'])) 		$inscMunicipal		= \Zage\App\Util::antiInjection($_POST['inscrMun']);
	if (isset($_POST['dataInicio'])) 	$dataNascimento		= \Zage\App\Util::antiInjection($_POST['dataInicio']);
	$rg				= null;
	$sexo			= null;
	
}elseif ($tipo == 'F'){
	
	if (isset($_POST['nome'])) 			$nome				= \Zage\App\Util::antiInjection($_POST['nome']);
	if (isset($_POST['cpf']))	 		$cgc				= \Zage\App\Util::antiInjection($_POST['cpf']);
	if (isset($_POST['rg']))	 		$rg					= \Zage\App\Util::antiInjection($_POST['rg']);
	if (isset($_POST['dataNas'])) 		$dataNascimento		= \Zage\App\Util::antiInjection($_POST['dataNas']);
	if (isset($_POST['sexo']))	 		$sexo				= \Zage\App\Util::antiInjection($_POST['sexo']);
	$razão			= null;
	$inscEstadual	= null;
	$inscMunicipal	= null;
}

if (isset($_POST['codLogradouro']))		$codLogradouro	= \Zage\App\Util::antiInjection($_POST['codLogradouro']);
if (isset($_POST['endCorreto']))		$endCorreto		= \Zage\App\Util::antiInjection($_POST['endCorreto']);
if (isset($_POST['descLogradouro']))	$descLogradouro	= \Zage\App\Util::antiInjection($_POST['descLogradouro']);
if (isset($_POST['bairro']))			$bairro			= \Zage\App\Util::antiInjection($_POST['bairro']);
if (isset($_POST['complemento']))		$complemento	= \Zage\App\Util::antiInjection($_POST['complemento']);
if (isset($_POST['numero']))			$numero			= \Zage\App\Util::antiInjection($_POST['numero']);
if (isset($_POST['cep']))				$cep			= \Zage\App\Util::antiInjection($_POST['cep']);

if (isset($_POST['codTipoTel']))		$codTipoTel			= $_POST['codTipoTel'];
if (isset($_POST['codTelefone']))		$codTelefone		= $_POST['codTelefone'];
if (isset($_POST['telefone']))			$telefone			= $_POST['telefone'];

if (isset($_POST['segmento']))			$codSegmento			= $_POST['segmento'];

if (!isset($codTipoTel))				$codTipoTel			= array();
if (!isset($codTelefone))				$codTelefone		= array();
if (!isset($telefone))					$telefone			= array();

#################################################################################
## Limpar a variável de erro
#################################################################################
$err	= false;

#################################################################################
## Fazer validação dos campos
#################################################################################
/******* IDENTIFICAÇÃO *********/
if (!isset($ident) || (empty($ident))) {
	$system->criaAviso(\Zage\App\Aviso\Tipo::ERRO,$tr->trans("A identificação deve ser preenchida!"));
	$err	= 1;
}elseif ((!empty($ident)) && (strlen($ident) > 60)) {
	$system->criaAviso(\Zage\App\Aviso\Tipo::ERRO,$tr->trans("A identificação não deve conter mais de 60 caracteres!"));
	$err	= 1;
}else{
	$oParceiro	= $em->getRepository('Entidades\ZgadmOrganizacao')->findOneBy(array('identificacao' => $ident));
	
	if($oParceiro != null && ($oParceiro->getCodigo() != $codOrganizacao)){
		$system->criaAviso(\Zage\App\Aviso\Tipo::ERRO,$tr->trans("Já existe um parceiro cadastrado com esta identificação! Por favor, informe outra."));
		$err	= 1;
	}
}

/******* EMAIL *********/
if ((!empty($email)) && (strlen($email) > 200)) {
	$system->criaAviso(\Zage\App\Aviso\Tipo::ERRO,$tr->trans("O email não deve conter mais de 200 caracteres!"));
	$err	= 1;
}elseif (!empty($email)){
	if(\Zage\App\Util::validarEMail($email) == false){
		$system->criaAviso(\Zage\App\Aviso\Tipo::ERRO,$tr->trans("Email inválido!"));
		$err	= 1;
	}
}

/******* SEGMENTO *********/
if (!isset($codSegmento) || (empty($codSegmento))) {
	$system->criaAviso(\Zage\App\Aviso\Tipo::ERRO,$tr->trans("O segmento de mercado deve ser preenchido!"));
	$err	= 1;
}

/******* VALIDAÇÕES DE PJ E PF *********/
if (!isset($tipo) || (empty($tipo))) {
	$system->criaAviso(\Zage\App\Aviso\Tipo::ERRO,$tr->trans("O tipo deve ser preenchido!"));
	$err	= 1;
}

if ($tipo == 'J'){
	/******* CNPJ *********/
	$valCgc			= new \Zage\App\Validador\Cnpj();
	if (empty($cgc)) {
		$system->criaAviso(\Zage\App\Aviso\Tipo::ERRO,$tr->trans("O CNPJ deve ser preenchido!"));
		$err	= 1;
	}else{
		if ($valCgc->isValid($cgc) == false) {
			$system->criaAviso(\Zage\App\Aviso\Tipo::ERRO,$tr->trans("CNPJ inválido!"));
			$err	= 1;
		}else{
			$oParceiro	= $em->getRepository('Entidades\ZgadmOrganizacao')->findOneBy(array('cgc' => $cgc));
			if($oParceiro != null && ($oParceiro->getCodigo() != $codOrganizacao)){
				$system->criaAviso(\Zage\App\Aviso\Tipo::ERRO,$tr->trans("Já existe um parceiro cadastrado com este CNPJ! Por favor,  verifique os dados informados."));
				$err	= 1;
			}
		}
	}
	
	/******* Nome *********/
	if (!isset($razao) || (empty($razao))) {
		$system->criaAviso(\Zage\App\Aviso\Tipo::ERRO,$tr->trans("A razão social deve ser preenchida!"));
		$err	= 1;
	}elseif ((!empty($razao)) && (strlen($razao) > 100)) {
		$system->criaAviso(\Zage\App\Aviso\Tipo::ERRO,$tr->trans("A razão social não deve conter mais de 100 caracteres!"));
		$err	= 1;
	}
	
	/******* Fantasia *********/
	if (!isset($nome) || (empty($nome))) {
		$system->criaAviso(\Zage\App\Aviso\Tipo::ERRO,$tr->trans("O nome fantasia deve ser preenchido!"));
		$err	= 1;
	}elseif ((!empty($nome)) && (strlen($nome) > 100)) {
		$system->criaAviso(\Zage\App\Aviso\Tipo::ERRO,$tr->trans("O nome fantasia não deve conter mais de 100 caractes!"));
		$err	= 1;
	}
	
	/******* Inscrição Municipal *********/
	if ((!empty($inscMunicipal)) && (strlen($inscMunicipal) > 18)) {
		$system->criaAviso(\Zage\App\Aviso\Tipo::ERRO,$tr->trans("O número da inscrição municipal não deve conter mais de 18 caracteres!"));
		$err	= 1;
	}
	
	/******* Inscrição Estadual *********/
	if ((!empty($inscEstadual)) && (strlen($inscEstadual) > 18)) {
		$system->criaAviso(\Zage\App\Aviso\Tipo::ERRO,$tr->trans("O número da inscrição estadual não deve conter mais de 18 caracteres!"));
		$err	= 1;
	}
	
	/******** Início de Atividade ***********/
	if (!empty($dataNascimento)) {
		if (\Zage\App\Util::validaData($dataNascimento, $system->config["data"]["dateFormat"]) == false) {
			$system->criaAviso(\Zage\App\Aviso\Tipo::ERRO,$tr->trans("Data da abertura inválida!"));
			$err	= 1;
		}
	}
}

if ($tipo == 'F'){
	/******* CPF *********/
	$valCgc			= new \Zage\App\Validador\Cpf();
	if (empty($cgc)) {
		$system->criaAviso(\Zage\App\Aviso\Tipo::ERRO,$tr->trans("O CPF deve ser preenchido!"));
		$err	= 1;
	}else{
		if ($valCgc->isValid($cgc) == false) {
			$system->criaAviso(\Zage\App\Aviso\Tipo::ERRO,$tr->trans("CPF inválido!"));
			$err	= 1;
		}else{
			$oParceiro	= $em->getRepository('Entidades\ZgadmOrganizacao')->findOneBy(array('cgc' => $cgc));
			if($oParceiro != null && ($oParceiro->getCodigo() != $codOrganizacao)){
				$system->criaAviso(\Zage\App\Aviso\Tipo::ERRO,$tr->trans("Já existe um parceiro cadastrado com este CPF! Por favor,  verifique os dados informados."));
				$err	= 1;
			}
		}
	}
	
	/******* Nome *********/
	if (!isset($nome) || (empty($nome))) {
		$system->criaAviso(\Zage\App\Aviso\Tipo::ERRO,$tr->trans("O nome completo deve ser preenchido!"));
		$err	= 1;
	}elseif ((!empty($nome)) && (strlen($nome) > 100)) {
		$system->criaAviso(\Zage\App\Aviso\Tipo::ERRO,$tr->trans("O nome não deve conter mais de 100 caracteres!"));
		$err	= 1;
	}
	
	/******* RG *********/
	if ((!empty($rg)) && (strlen($rg) > 14)) {
		$system->criaAviso(\Zage\App\Aviso\Tipo::ERRO,$tr->trans("O número do RG não deve conter mais de 14 caracteres!"));
		$err	= 1;
	}
	
	/******* Data Nascimento *********/
	if (!empty($dataNascimento)) {
		if (\Zage\App\Util::validaData($dataNascimento, $system->config["data"]["dateFormat"]) == false) {
			$system->criaAviso(\Zage\App\Aviso\Tipo::ERRO,$tr->trans("Data de nascimento inválida"));
			$err	= 1;
		}
	}	
}

/******* ENDEREÇO *********/
if (isset($codLogradouro) && (!empty($codLogradouro))){
	
	/******* CEP *********/
	if (!isset($cep) || (empty($cep))) {
		$system->criaAviso(\Zage\App\Aviso\Tipo::ERRO,$tr->trans("O CEP deve ser preenchido!"));
		$err	= 1;
	}elseif ((!empty($cep)) && (strlen($cep) > 8)) {
		$system->criaAviso(\Zage\App\Aviso\Tipo::ERRO,$tr->trans("O CEP não deve conter mais de 8 caracteres!"));
		$err	= 1;
	}
	
	/******* ENDEREÇO *********/
	if (!isset($descLogradouro) || (empty($descLogradouro))) {
		$system->criaAviso(\Zage\App\Aviso\Tipo::ERRO,$tr->trans("O logradouro deve ser preenchido!"));
		$err	= 1;
	}elseif ((!empty($descLogradouro)) && (strlen($descLogradouro) > 100)) {
		$system->criaAviso(\Zage\App\Aviso\Tipo::ERRO,$tr->trans("O logradouro não deve conter mais de 100 caracteres!"));
		$err	= 1;
	}
	
	/******* BAIRRO *********/
	if (!isset($bairro) || (empty($bairro))) {
		$system->criaAviso(\Zage\App\Aviso\Tipo::ERRO,$tr->trans("O bairro deve ser preenchido!"));
		$err	= 1;
	}elseif ((!empty($bairro)) && (strlen($bairro) > 60)) {
		$system->criaAviso(\Zage\App\Aviso\Tipo::ERRO,$tr->trans("O bairro não deve conter mais de 60 caracteres!"));
		$err	= 1;
	}
	
	/******* NÚMERO *********/
	if ((!empty($numero)) && (strlen($numero) > 10)) {
		$system->criaAviso(\Zage\App\Aviso\Tipo::ERRO,$tr->trans("O número não deve conter mais de 10 caracteres!"));
		$err	= 1;
	}
	
	/******* COMPLEMENTO *********/
	if ((!empty($complemento)) && (strlen($complemento) > 100)) {
		$system->criaAviso(\Zage\App\Aviso\Tipo::ERRO,$tr->trans("O complemento do endereço não deve conter mais de 100 caracteres!"));
		$err	= 1;
	}
	
	//Verificar o endereço informado é corresponte a base dos correios
	if (isset($endCorreto) && (!empty($endCorreto))) {
		$endCorreto	= 1;
	}else{
		$oLogradouro	= $em->getRepository('Entidades\ZgadmLogradouro')->findOneBy(array('codigo' => $codLogradouro));
		
		if (($oLogradouro->getDescricao() != $descLogradouro) || ($oLogradouro->getCodBairro()->getDescricao() != $bairro)){
			$endCorreto	= 0;
		}else{
			$endCorreto	= 1;
		}
	}
}else{
	$endCorreto = null; //Se não houver o codLogradouro o indicador deve ser nulo
}

if ($err != null) {
	echo '1'.\Zage\App\Util::encodeUrl('||'.htmlentities($err));
 	exit;
}
 
#################################################################################
## Salvar no banco
#################################################################################
try {
	if (isset($codOrganizacao) && (!empty($codOrganizacao))){
 		$oParceiro	= $em->getRepository('Entidades\ZgadmOrganizacao')->findOneBy(array('codigo' => $codOrganizacao));
 		if (!$oParceiro) {
 			$oParceiro	= new \Entidades\ZgadmOrganizacao();
 			$oParceiro->setDataCadastro(new \DateTime("now"));
 		}
 	}else{
 		$oParceiro	= new \Entidades\ZgadmOrganizacao();
 		$oParceiro->setDataCadastro(new \DateTime("now"));
 	}
 	
 	$oSexo				= $em->getRepository('Entidades\ZgsegSexoTipo')->findOneBy(array('codigo' => $sexo));
 	$oTipoPessoa		= $em->getRepository('Entidades\ZgadmOrganizacaoPessoaTipo')->findOneBy(array('codigo' => $tipo));
 	$oTipoOrganizacao	= $em->getRepository('Entidades\ZgadmOrganizacaoTipo')->findOneBy(array('codigo' => $codSegmento));
 	$oCodLogradouro		= $em->getRepository('Entidades\ZgadmLogradouro')->findOneBy(array('codigo' => $codLogradouro));
 	$oCodStatus			= $em->getRepository('Entidades\ZgadmOrganizacaoStatusTipo')->findOneBy(array('codigo' => A));
 	
 	if (!empty($dataNascimento)) {
 		$dtNasc		= DateTime::createFromFormat($system->config["data"]["dateFormat"], $dataNascimento);
 	}else{
 		$dtNasc		= null;
 	}
 	
 	$oParceiro->setIdentificacao($ident);
 	$oParceiro->setNome($nome);
 	$oParceiro->setRazao($razao);
 	$oParceiro->setCgc($cgc);
 	$oParceiro->setRg($rg);
 	$oParceiro->setCodTipoPessoa($oTipoPessoa);
 	$oParceiro->setCodTipo($oTipoOrganizacao);
 	$oParceiro->setInscEstadual($inscEstadual);
 	$oParceiro->setInscMunicipal($inscMunicipal);
 	$oParceiro->setEmail($email);
 	$oParceiro->setDataNascimento($dtNasc);
 	$oParceiro->setCodStatus($oCodStatus);
 	$oParceiro->setCodSexo($oSexo);
 	
 	$oParceiro->setCodLogradouro($oCodLogradouro);
 	$oParceiro->setIndEndCorreto($endCorreto);
 	$oParceiro->setCep($cep);
 	$oParceiro->setEndereco($descLogradouro);
 	$oParceiro->setBairro($bairro);
 	$oParceiro->setNumero($numero);
 	$oParceiro->setComplemento($complemento);
 	
 	$em->persist($oParceiro);
 	$em->flush();
 	//$em->detach($oParceiro);
 	
 	#################################################################################
 	## Telefones
 	#################################################################################
 	$telefones		= $em->getRepository('Entidades\ZgadmOrganizacaoTelefone')->findBy(array('codOrganizacao' => $codOrganizacao));
 	
 	/***** Exclusão *****/
 	for ($i = 0; $i < sizeof($telefones); $i++) {
 		if (!in_array($telefones[$i]->getCodigo(), $codTelefone)) {
 			try {
 				$em->remove($telefones[$i]);
 				$em->flush();
 			} catch (\Exception $e) {
 				$system->criaAviso(\Zage\App\Aviso\Tipo::ERRO,"Não foi possível excluir o telefone: ".$telefones[$i]->getTelefone()." Erro: ".$e->getMessage());
 				echo '1'.\Zage\App\Util::encodeUrl('||'.htmlentities($e->getMessage()));
 				exit;
 			}
 		}
 	}
 	
 	/***** Criação / Alteração *****/
 	for ($i = 0; $i < sizeof($codTelefone); $i++) {
 		$infoTel		= $em->getRepository('Entidades\ZgadmOrganizacaoTelefone')->findOneBy(array('codigo' => $codTelefone[$i] , 'codOrganizacao' => $oParceiro->getCodigo()));
 	
 		if (!$infoTel) {
 			$infoTel		= new \Entidades\ZgadmOrganizacaoTelefone();
 		}
 		
 		if ($infoTel->getCodTipoTelefone() !== $codTipoTel[$i] || $infoTel->getTelefone() !== $telefone[$i]) {
 			
 			$oTipoTel	= $em->getRepository('Entidades\ZgappTelefoneTipo')->findOneBy(array('codigo' => $codTipoTel[$i]));
 			
 			$infoTel->setCodOrganizacao($oParceiro);
 			$infoTel->setCodTipoTelefone($oTipoTel);
 			$infoTel->setTelefone($telefone[$i]);
 		 	
 			try {
 				$em->persist($infoTel);
 				$em->flush();
 				$em->detach($infoTel);
 			} catch (\Exception $e) {
 				$system->criaAviso(\Zage\App\Aviso\Tipo::ERRO,"Não foi possível cadastrar o telefone: ".$telefone[$i]." Erro: ".$e->getMessage());
 				echo '1'.\Zage\App\Util::encodeUrl('||'.htmlentities($e->getMessage()));
 				exit;
 			}
 		}
 	}
	
} catch (\Exception $e) {
 	$system->criaAviso(\Zage\App\Aviso\Tipo::ERRO,$e->getMessage());
 	echo '1'.\Zage\App\Util::encodeUrl('||'.htmlentities($e->getMessage()));
 	exit;
}
 
$system->criaAviso(\Zage\App\Aviso\Tipo::INFO,"Informações salvas com sucesso");
echo '0'.\Zage\App\Util::encodeUrl('|'.$oParceiro->getCodigo());