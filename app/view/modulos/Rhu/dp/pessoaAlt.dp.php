<?php
use Zage\App\Mascara\Tipo\Cpf;
#################################################################################
## Includes
#################################################################################
if (defined('DOC_ROOT')) {
	include_once(DOC_ROOT . 'include.php');
}else{
 	include_once('../include.php');
}
 
global $em,$system,$log,$tr;

#################################################################################
## Resgata os parâmetros passados pelo formulário
#################################################################################
if (isset($_POST['codPessoa']))			$codPessoa		= \Zage\App\Util::antiInjection($_POST['codPessoa']);
if (isset($_POST['nome']))				$nome			= \Zage\App\Util::antiInjection($_POST['nome']);
if (isset($_POST['sexo']))				$sexo			= \Zage\App\Util::antiInjection($_POST['sexo']);
if (isset($_POST['nomeMae']))			$nomeMae		= \Zage\App\Util::antiInjection($_POST['nomeMae']);
if (isset($_POST['nomePai']))			$nomePai		= \Zage\App\Util::antiInjection($_POST['nomePai']);
if (isset($_POST['dataNascimento']))	$dataNascimento	= \Zage\App\Util::antiInjection($_POST['dataNascimento']);
if (isset($_POST['codEstadoCivil']))	$codEstadoCivil	= \Zage\App\Util::antiInjection($_POST['codEstadoCivil']);
if (isset($_POST['email']))				$email			= \Zage\App\Util::antiInjection($_POST['email']);
if (isset($_POST['codNaturalidade']))	$codNaturalidade= \Zage\App\Util::antiInjection($_POST['codNaturalidade']);
if (isset($_POST['codNacionalidade']))	$codNacionalidade= \Zage\App\Util::antiInjection($_POST['codNacionalidade']);
if (isset($_POST['codInstrucao']))		$codInstrucao	= \Zage\App\Util::antiInjection($_POST['codInstrucao']);
if (isset($_POST['indEstrangeiro']))	$indEstrangeiro	= \Zage\App\Util::antiInjection($_POST['indEstrangeiro']);
/** CPF,Rg **/
if (isset($_POST['cpf']))				$cpf			= \Zage\App\Util::antiInjection($_POST['cpf']);
if (isset($_POST['rg']))				$rg				= \Zage\App\Util::antiInjection($_POST['rg']);
if (isset($_POST['rgDataEmissao']))		$rgDataEmissao	= \Zage\App\Util::antiInjection($_POST['rgDataEmissao']);
if (isset($_POST['rgUf']))				$rgUf			= \Zage\App\Util::antiInjection($_POST['rgUf']);
if (isset($_POST['orgaoExpedidor']))	$orgaoExpedidor	= \Zage\App\Util::antiInjection($_POST['orgaoExpedidor']);
/** Endereco **/
if (isset($_POST['codLogradouro']))		$codLogradouro	= \Zage\App\Util::antiInjection($_POST['codLogradouro']);
if (isset($_POST['descLogradouro']))	$descLogradouro	= \Zage\App\Util::antiInjection($_POST['descLogradouro']);
if (isset($_POST['bairro']))			$bairro			= \Zage\App\Util::antiInjection($_POST['bairro']);
if (isset($_POST['complemento']))		$complemento	= \Zage\App\Util::antiInjection($_POST['complemento']);
if (isset($_POST['numero']))			$numero			= \Zage\App\Util::antiInjection($_POST['numero']);
if (isset($_POST['cep']))				$cep			= \Zage\App\Util::antiInjection($_POST['cep']);
/** Carteira Habilitação **/
if (isset($_POST['numHabilitacao']))	$numHabilitacao	= \Zage\App\Util::antiInjection($_POST['numHabilitacao']);
if (isset($_POST['catHabilitacao']))	$catHabilitacao	= \Zage\App\Util::antiInjection($_POST['catHabilitacao']);
if (isset($_POST['cnhVencimento']))		$cnhVencimento	= \Zage\App\Util::antiInjection($_POST['cnhVencimento']);
if (isset($_POST['cnhEmissao']))		$cnhEmissao		= \Zage\App\Util::antiInjection($_POST['cnhEmissao']);
/** Titulo eleitor **/
if (isset($_POST['titEleitor']))		$titEleitor		= \Zage\App\Util::antiInjection($_POST['titEleitor']);
if (isset($_POST['titEleitorZona']))	$titEleitorZona	= \Zage\App\Util::antiInjection($_POST['titEleitorZona']);
if (isset($_POST['titEleitorSecao']))	$titEleitorSecao= \Zage\App\Util::antiInjection($_POST['titEleitorSecao']);
/** RNE **/
if (isset($_POST['rne']))				$rne			 = \Zage\App\Util::antiInjection($_POST['rne']);
if (isset($_POST['rneOrgaoEmissor']))	$rneOrgaoEmissor = \Zage\App\Util::antiInjection($_POST['rneOrgaoEmissor']);
if (isset($_POST['rneDataEmissao']))	$rneDataEmissao  = \Zage\App\Util::antiInjection($_POST['rneDataEmissao']);
if (isset($_POST['indNaturalizado']))	$indNaturalizado = \Zage\App\Util::antiInjection($_POST['indNaturalizado']);
/** Passaporte **/
if (isset($_POST['passaporteNro']))		$passaporteNro	 = \Zage\App\Util::antiInjection($_POST['passaporteNro']);
if (isset($_POST['passDataEmissao']))	$passDataEmissao = \Zage\App\Util::antiInjection($_POST['passDataEmissao']);
if (isset($_POST['passDataValidade']))	$passDataValidade= \Zage\App\Util::antiInjection($_POST['passDataValidade']);
if (isset($_POST['passPaisOrigem']))	$passPaisOrigem  = \Zage\App\Util::antiInjection($_POST['passPaisOrigem']);
/** Carteira de trabalho **/
if (isset($_POST['cartTrabalho']))		$cartTrabalho	  = \Zage\App\Util::antiInjection($_POST['cartTrabalho']);
if (isset($_POST['cartTrabalhoSerie']))	$cartTrabalhoSerie= \Zage\App\Util::antiInjection($_POST['cartTrabalhoSerie']);
if (isset($_POST['cartTrabalhoUf']))	$cartTrabalhoUf	  = \Zage\App\Util::antiInjection($_POST['cartTrabalhoUf']);
if (isset($_POST['cartTrabalhoData']))	$cartTrabalhoData = \Zage\App\Util::antiInjection($_POST['cartTrabalhoData']);
if (isset($_POST['cartTrabalhoVenc']))	$cartTrabalhoVenc = \Zage\App\Util::antiInjection($_POST['cartTrabalhoVenc']);
if (isset($_POST['nit']))				$nit			  = \Zage\App\Util::antiInjection($_POST['nit']);

/** Reservista **/
if (isset($_POST['cartReservista']))	$cartReservista	   = \Zage\App\Util::antiInjection($_POST['cartReservista']);
if (isset($_POST['codReservistaCat']))	$codReservistaCat  = \Zage\App\Util::antiInjection($_POST['codReservistaCat']);
if (isset($_POST['certReservistaVenc']))$certReservistaVenc= \Zage\App\Util::antiInjection($_POST['certReservistaVenc']);
/** Deficiencia **/
if (isset($_POST['indDeficienciaF']))	$indDeficienciaF   = \Zage\App\Util::antiInjection($_POST['indDeficienciaF']);
if (isset($_POST['indDeficienciaA']))	$indDeficienciaA   = \Zage\App\Util::antiInjection($_POST['indDeficienciaA']);
if (isset($_POST['indDeficienciaFa']))	$indDeficienciaFa  = \Zage\App\Util::antiInjection($_POST['indDeficienciaFa']);
if (isset($_POST['indDeficienciaV']))	$indDeficienciaV   = \Zage\App\Util::antiInjection($_POST['indDeficienciaV']);
if (isset($_POST['indDeficienciaM']))	$indDeficienciaM   = \Zage\App\Util::antiInjection($_POST['indDeficienciaM']);
if (isset($_POST['indDeficienciaMo']))	$indDeficienciaMo  = \Zage\App\Util::antiInjection($_POST['indDeficienciaMo']);
/** Contato **/
if (isset($_POST['codTipoTel']))		$codTipoTel			= $_POST['codTipoTel'];
if (isset($_POST['codTelefone']))		$codTelefone		= $_POST['codTelefone'];
if (isset($_POST['telefone']))			$telefone			= $_POST['telefone'];

if (!isset($codTipoTel))				$codTipoTel			= array();
if (!isset($codTelefone))				$codTelefone		= array();
if (!isset($telefone))					$telefone			= array();

/** Dependentes **/
if (isset($_POST['codDependente']))		$codDependente		= $_POST['codDependente'];
if (isset($_POST['nomeDependente']))	$nomeDependente		= $_POST['nomeDependente'];
if (isset($_POST['sexoDependente']))	$sexoDependente		= $_POST['sexoDependente'];
if (isset($_POST['dataNascimentoD']))	$dataNascimentoD	= $_POST['dataNascimentoD'];
if (isset($_POST['indDeficiente']))		$indDeficiente		= $_POST['indDeficiente'];

if (!isset($codDependente))				$codDependente		= array();
if (!isset($nomeDependente))			$nomeDependente		= array();
if (!isset($sexoDependente))			$sexoDependente		= array();
if (!isset($dataNascimentoD))			$dataNascimentoD	= array();
if (!isset($indDeficiente))				$indDeficiente		= array();
#################################################################################
## Limpar a variável de erro
#################################################################################
$err	= false;
#################################################################################
## Fazer validação dos campos
#################################################################################
/** NOME **/
if (!isset($nome) || empty($nome)) {
	$system->criaAviso(\Zage\App\Aviso\Tipo::ERRO,$tr->trans("Preencha o nome completo do funcionário !!"));
	$err	= 1;
}

if ((!empty($nome)) && (strlen($nome) > 60)) {
	$system->criaAviso(\Zage\App\Aviso\Tipo::ERRO,$tr->trans("Campo NOME não deve conter mais de 60 caracteres"));
	$err	= 1;
}

/** Sexo **/
if (!isset($sexo) || empty($sexo)) {
	$system->criaAviso(\Zage\App\Aviso\Tipo::ERRO,$tr->trans("Preencha o sexo do funcionário !!"));
	$err	= 1;
}

/** Data Nascimento **/
if (!isset($dataNascimento) || empty($dataNascimento)) {
	$system->criaAviso(\Zage\App\Aviso\Tipo::ERRO,$tr->trans("Preencha a data de nascimento do funcinário !!"));
	$err	= 1;
}

/** Estado Civil **/
if (!isset($codEstadoCivil) || empty($codEstadoCivil)) {
	$system->criaAviso(\Zage\App\Aviso\Tipo::ERRO,$tr->trans("Preencha o estado civil do funcionário !!"));
	$err	= 1;
}

/** Instrução **/
if (!isset($codInstrucao) || empty($codInstrucao)) {
	$system->criaAviso(\Zage\App\Aviso\Tipo::ERRO,$tr->trans("Preencha o grau de instrução do funcionário !!"));
	$err	= 1;
}


/** VALIDADES DE CPF E RG DE ACORDO COM A NACIONALIDADE **/
if (!isset($indEstrangeiro) || (empty($indEstrangeiro))){
	$indEstrangeiro = 0;
	
	/** CPF - se for brasileiro CPF é obrigatorio **/
	if (!isset($cpf) || (empty($cpf))) {
		$system->criaAviso(\Zage\App\Aviso\Tipo::ERRO,$tr->trans("Preencha o CPF !!"));
		$err	= 1;
	}
	
	/** RG - se for brasileiro RG é obrigatorio **/
	if (!isset($rg) || (empty($rg))) {
		$system->criaAviso(\Zage\App\Aviso\Tipo::ERRO,$tr->trans("Preencha o RG !!"));
		$err	= 1;
	}
	
	/** RG(ORGAO EXPEDIDOR) - se for brasileiro ORGAO EXPEDIDOR do RG é obrigatorio **/
	if (!isset($orgaoExpedidor) || (empty($orgaoExpedidor))) {
		$system->criaAviso(\Zage\App\Aviso\Tipo::ERRO,$tr->trans("Preencha o Órgão Expedidor do RG !!"));
		$err	= 1;
	}
	
	/** RG(UF) - se for brasileiro ESTADO EMISSOR do RG é obrigatorio **/
	if (!isset($rgUf) || (empty($rgUf))) {
		$system->criaAviso(\Zage\App\Aviso\Tipo::ERRO,$tr->trans("Preencha o Estado de emissão do RG !!"));
		$err	= 1;
	}
	
	/** NATURALIDADE **/
	if (!isset($codNaturalidade) || empty($codNaturalidade)) {
		$system->criaAviso(\Zage\App\Aviso\Tipo::ERRO,$tr->trans("Preencha a naturalidade !!"));
		$err	= 1;
	}
	
	/** NACIONALIDADE é brasileira**/
	$codNacionalidade = 'BR';
	
	//Se a pessoa FOR ESTRAGEIRO
}else {
	$indEstrangeiro = 1;
	
	//Estrangeiro naturalizado
	if (isset($indNaturalizado) || (!empty($indNaturalizado))){
		$indNaturalizado = 1;
		/** CPF - se for entrangeiro naturalizado o CPF é obrigatorio**/
		if (!isset($cpf) || (empty($cpf))) {
			$system->criaAviso(\Zage\App\Aviso\Tipo::ERRO,$tr->trans("Para estrangeiro naturalizado o CPF deve ser preenchido !!"));
			$err	= 1;
		}
		
		/** RG - se for estrangeiro naturalizado RG é obrigatorio **/
		if (!isset($rg) || (empty($rg))) {
			$system->criaAviso(\Zage\App\Aviso\Tipo::ERRO,$tr->trans("Para estrangeiro naturalizado o RG deve ser preenchido !!"));
			$err	= 1;
		}
		
		/** RG(ORGAO EXPEDIDOR) - se for estrangeiro naturalizado ORGAO EXPEDIDOR do RG é obrigatorio **/
		if (!isset($orgaoExpedidor) || (empty($orgaoExpedidor))) {
			$system->criaAviso(\Zage\App\Aviso\Tipo::ERRO,$tr->trans("Para estrangeiro naturalizado o Órgão Expedidor do RG deve ser preenchido !!"));
			$err	= 1;
		}
		
		/** RG(UF) - se for estrangeiro naturalizado ESTADO EMISSOR do RG é obrigatorio **/
		if (!isset($rgUf) || (empty($rgUf))) {
			$system->criaAviso(\Zage\App\Aviso\Tipo::ERRO,$tr->trans("Para estrangeiro naturalizado o Estado de emissão do RG deve ser preenchido !!"));
			$err	= 1;
		}
		
	}else{
		$indNaturalizado = 0;
		//Para estrangeiro não naturalizado o Passaporte é obrigatório
		/** PASSAPORTE **/
		if (!isset($passaporteNro) || (empty($passaporteNro))) {
			$system->criaAviso(\Zage\App\Aviso\Tipo::ERRO,$tr->trans("Para estrangeiro não naturalizado, o Passaporte deve ser preenchido !!"));
			$err	= 1;
		}
		
		/** PASSAPORTE - DATA EMISSÃO **/
		if (!isset($passDataEmissao) || (empty($passDataEmissao))) {
			$system->criaAviso(\Zage\App\Aviso\Tipo::ERRO,$tr->trans("Para estrangeiro não naturalizado, a data de emissão Passaporte deve ser preenchido !!"));
			$err	= 1;
		}
		
		/** PASSAPORTE - PAIS ORIGEM **/
		if (!isset($passPaisOrigem) || (empty($passPaisOrigem))) {
			$system->criaAviso(\Zage\App\Aviso\Tipo::ERRO,$tr->trans("Para estrangeiro não naturalizado, o país de origem Passaporte deve ser preenchido !!"));
			$err	= 1;
		}
		
		/** PASSAPORTE - DATA VALIDADE**/
		if (!isset($passDataValidade) || (empty($passDataValidade))) {
			$system->criaAviso(\Zage\App\Aviso\Tipo::ERRO,$tr->trans("Para estrangeiro não naturalizado, a data de validade do Passaporte deve ser preenchido !!"));
			$err	= 1;
		}
		
		/** RNE **/
		if (!isset($rne) || (empty($rne))) {
			$system->criaAviso(\Zage\App\Aviso\Tipo::ERRO,$tr->trans("Para estrangeiro não naturalizado, o RNE deve ser preenchido !!"));
			$err	= 1;
		}
		
		/** RNE - ORGAO EMISSOR **/
		if (!isset($rneOrgaoEmissor) || (empty($rneOrgaoEmissor))) {
			$system->criaAviso(\Zage\App\Aviso\Tipo::ERRO,$tr->trans("Para estrangeiro não naturalizado, o ÓrgÃo Emissor do RNE deve ser preenchido !!"));
			$err	= 1;
		}
		
		/** RNE - DATA EMISSAO **/
		if (!isset($rneDataEmissao) || (empty($rneDataEmissao))) {
			$system->criaAviso(\Zage\App\Aviso\Tipo::ERRO,$tr->trans("Para estrangeiro não naturalizado, a data de emissão do RNE deve ser preenchido !!"));
			$err	= 1;
		}
	}
	
	/** NACIONALIDADE **/
	if (!isset($codNacionalidade) || (empty($codNacionalidade))) {
		$system->criaAviso(\Zage\App\Aviso\Tipo::ERRO,$tr->trans("Preencha a nacionalidade !!"));
		$err	= 1;
	}else{
		if ($codNacionalidade == 'BR'){
			$system->criaAviso(\Zage\App\Aviso\Tipo::ERRO,$tr->trans("Para estrangeiros, o Brasil não pode ser selecionado como país natal !!"));
			$err	= 1;
		}
	}
	
	/** NATURALIDADE**/
	$codNaturalidade = null;
}

/** CPF **/
if (!empty($cpf)) {
	$valCpf			= new \Zage\App\Validador\Cpf();
	if ($valCpf->isValid($cpf) == false){
		$system->criaAviso(\Zage\App\Aviso\Tipo::ERRO,$tr->trans("CPF inválido !!"));
		$err	= 1;
	}else{
		$oCpf	= $em->getRepository('Entidades\ZgrhuPessoa')->findOneBy(array('codOrganizacao' => $system->getCodOrganizacao(), 'cpf' => $cpf));
		if (($oCpf != null) && ($oCpf->getCodigo() != $codPessoa)){
			$system->criaAviso(\Zage\App\Aviso\Tipo::ERRO,$tr->trans("CPF já cadastrado"));
			$err 	= 1;
		}
	}
}

/** Deficiencia **/
if (isset($indDeficienciaF) && (!empty($indDeficienciaF))) {
	$indDeficienciaF	= 1;
	if (isset($indDeficienciaA) && (!empty($indDeficienciaA))) {
		$indDeficienciaA	= 1;
	}else{
		$indDeficienciaA	= 0;
	}
	if (isset($indDeficienciaFa) && (!empty($indDeficienciaFa))) {
		$indDeficienciaFa	= 1;
	}else{
		$indDeficienciaFa	= 0;
	}
	if (isset($indDeficienciaM) && (!empty($indDeficienciaM))) {
		$indDeficienciaM	= 1;
	}else{
		$indDeficienciaM	= 0;
	}
	if (isset($indDeficienciaMo) && (!empty($indDeficienciaMo))) {
		$indDeficienciaMo	= 1;
	}else{
		$indDeficienciaMo	= 0;
	}
	if (isset($indDeficienciaV) && (!empty($indDeficienciaV))) {
		$indDeficienciaV	= 1;
	}else{
		$indDeficienciaV	= 0;
	}
} else {
	$indDeficienciaF	= 0;
	$indDeficienciaA	= 0;
	$indDeficienciaFa	= 0;
	$indDeficienciaM	= 0;
	$indDeficienciaMo	= 0;
	$indDeficienciaV	= 0;
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
	## Resgata os objetos (chave estrangeiras)
	#################################################################################
	$oOrganizacao	 = $em->getRepository('Entidades\ZgadmOrganizacao')->findOneBy(array('codigo' => $system->getCodOrganizacao()));
	$oLogradouro	 = $em->getRepository('Entidades\ZgadmLogradouro')->findOneBy(array('codigo' => $codLogradouro));
	$oSexo			 = $em->getRepository('Entidades\ZgsegSexoTipo')->findOneBy(array('codigo' => $sexo));
	$oCatHabilitacao = $em->getRepository('Entidades\ZgrhuCnhCategoria')->findOneBy(array('codigo' => $catHabilitacao));
	$oEstadoCivil	 = $em->getRepository('Entidades\ZgrhuPessoaTipoEstadoCivil')->findOneBy(array('codigo' => $codEstadoCivil));
	$oNaturidade	 = $em->getRepository('Entidades\ZgadmCidade')->findOneBy(array('codigo' => $codNaturalidade));
	$oNacionalidade	 = $em->getRepository('Entidades\ZgadmPais')->findOneBy(array('codigo' => $codNacionalidade));
	$oPassPaisOrigem = $em->getRepository('Entidades\ZgadmPais')->findOneBy(array('codigo' => $passPaisOrigem));
	$oInstrucao		 = $em->getRepository('Entidades\ZgrhuPessoaInstrucaoTipo')->findOneBy(array('codigo' => $codInstrucao));	
	$oCartTrabalhoUf = $em->getRepository('Entidades\ZgadmEstado')->findOneBy(array('codUf' => $cartTrabalhoUf));
	$oRgUf 			 = $em->getRepository('Entidades\ZgadmEstado')->findOneBy(array('codUf' => $rgUf));
	$oReservistaCat  = $em->getRepository('Entidades\ZgrhuReservistaCategoria')->findOneBy(array('codigo' => $codReservistaCat));
	
	#################################################################################
	## Configurações da data
	#################################################################################
	if (!empty($dataNascimento)) {
		$dataNascimento		= DateTime::createFromFormat($system->config["data"]["dateFormat"], $dataNascimento);
	}else{
		$dataNascimento		= null;
	}
	
	if (!empty($rgDataEmissao)) {
		$rgDataEmissao		= DateTime::createFromFormat($system->config["data"]["dateFormat"], $rgDataEmissao);
	}else{
		$rgDataEmissao		= null;
	}
	
	if (!empty($cnhEmissao)) {
		$cnhEmissao		= DateTime::createFromFormat($system->config["data"]["dateFormat"], $cnhEmissao);
	}else{
		$cnhEmissao		= null;
	}
	
	if (!empty($cnhVencimento)) {
		$cnhVencimento		= DateTime::createFromFormat($system->config["data"]["dateFormat"], $cnhVencimento);
	}else{
		$cnhVencimento		= null;
	}
	
	if (!empty($cartTrabalhoVenc)) {
		$cartTrabalhoVenc		= DateTime::createFromFormat($system->config["data"]["dateFormat"], $cartTrabalhoVenc);
	}else{
		$cartTrabalhoVenc		= null;
	}
	
	if (!empty($cartTrabalhoData)) {
		$cartTrabalhoData		= DateTime::createFromFormat($system->config["data"]["dateFormat"], $cartTrabalhoData);
	}else{
		$cartTrabalhoData		= null;
	}
	
	if (!empty($certReservistaVenc)) {
		$certReservistaVenc		= DateTime::createFromFormat($system->config["data"]["dateFormat"], $certReservistaVenc);
	}else{
		$certReservistaVenc		= null;
	}
	
	if (!empty($passDataEmissao)) {
		$passDataEmissao		= DateTime::createFromFormat($system->config["data"]["dateFormat"], $passDataEmissao);
	}else{
		$passDataEmissao		= null;
	}
	
	if (!empty($passDataValidade)) {
		$passDataValidade		= DateTime::createFromFormat($system->config["data"]["dateFormat"], $passDataValidade);
	}else{
		$passDataValidade		= null;
	}
	
	if (!empty($rneDataEmissao)) {
		$rneDataEmissao		= DateTime::createFromFormat($system->config["data"]["dateFormat"], $rneDataEmissao);
	}else{
		$rneDataEmissao		= null;
	}
	
	#################################################################################
	## Salvar o Pessoa
	#################################################################################
	if (isset($codPessoa) && (!empty($codPessoa))) {
 		$oPessoa	= $em->getRepository('Entidades\ZgrhuPessoa')->findOneBy(array('codOrganizacao' => $system->getCodOrganizacao(), 'codigo' => $codPessoa));
 		if (!$oPessoa) $oPessoa	= new \Entidades\ZgrhuPessoa();
 	}else{
 		$oPessoa	= new \Entidades\ZgrhuPessoa();
 	}
 	
 	$oPessoa->setCodOrganizacao($oOrganizacao);
 	$oPessoa->setNome($nome);
 	$oPessoa->setNomePai($nomePai);
 	$oPessoa->setNomeMae($nomeMae);
 	$oPessoa->setEmail($email);
 	$oPessoa->setCodTipoEstadoCivil($oEstadoCivil);
 	$oPessoa->setCodNaturalidade($oNaturidade);
 	$oPessoa->setCodNacionalidade($oNacionalidade);
 	$oPessoa->setCodInstrucao($oInstrucao);
 	$oPessoa->setSexo($oSexo);
 	$oPessoa->setDataNascimento($dataNascimento);
 	$oPessoa->setIndEstrangeiro($indEstrangeiro);

 	$oPessoa->setCpf($cpf);
 	$oPessoa->setRg($rg);
 	$oPessoa->setCodUfRg($oRgUf);
 	$oPessoa->setRgDataEmissao($rgDataEmissao);
 	$oPessoa->setRgOrgaoExpedidor($orgaoExpedidor);
 	
 	$oPessoa->setCodLogradouro($oLogradouro);
 	$oPessoa->setEndereco($descLogradouro);
 	$oPessoa->setBairro($bairro);
 	$oPessoa->setComplemento($complemento);
 	$oPessoa->setNumero($numero);
 	
 	$oPessoa->setCnhNumero($numHabilitacao);
 	$oPessoa->setCodCnhCategoria($oCatHabilitacao);
 	$oPessoa->setCnhVencimento($cnhVencimento);
 	$oPessoa->setCnhEmissao($cnhEmissao);
 	
 	$oPessoa->setTituloEleitor($titEleitor);
 	$oPessoa->setTituloEleitorZona($titEleitorZona);
 	$oPessoa->setTituloEleitorSecao($titEleitorSecao);
 	
 	$oPessoa->setRne($rne);
 	$oPessoa->setRneOrgaoEmissor($rneOrgaoEmissor);
 	$oPessoa->setRneDataEmissao($rneDataEmissao);
 	
 	$oPessoa->setPassaporteNro($passaporteNro);
 	$oPessoa->setPassaporteDataEmissao($passDataEmissao);
 	$oPessoa->setPassaporteDataValidade($passDataValidade);
 	$oPessoa->setPassaportePaisOrigem($oPassPaisOrigem);
 	$oPessoa->setIndNaturalizado($indNaturalizado);
 	
 	$oPessoa->setCarteiraTrabalho($cartTrabalho);
 	$oPessoa->setCarteiraTrabalhoSerie($cartTrabalhoSerie);
 	$oPessoa->setCarteiraTrabalhoData($cartTrabalhoData);
 	$oPessoa->setCarteiraTrabalhoVencimento($cartTrabalhoVenc);
 	$oPessoa->setCodCarteiraTrabalhoUf($oCartTrabalhoUf);
 	$oPessoa->setNit($nit);
 	
 	$oPessoa->setCertificadoReservista($cartReservista);
 	$oPessoa->setCodReservistaCategoria($oReservistaCat);
 	$oPessoa->setCertificadoReservistaVencimento($certReservistaVenc);
 	
 	$oPessoa->setIndDeficienteFisico($indDeficienciaF);
 	$oPessoa->setIndDeficienteAuditivo($indDeficienciaA);
 	$oPessoa->setIndDeficienteFala($indDeficienciaFa);
 	$oPessoa->setIndDeficienteMental($indDeficienciaM);
 	$oPessoa->setIndDeficienteMobilidade($indDeficienciaMo);
 	$oPessoa->setIndDeficienteVisual($indDeficienciaV);
 	
 	//$log->debug($cpf);
 	
 	$em->persist($oPessoa);
 	$em->flush();
 	//$em->detach($oPessoa);
 	
 	#################################################################################
 	## Contato
 	#################################################################################
 	$telefones		= $em->getRepository('Entidades\ZgrhuPessoaTelefone')->findBy(array('codPessoa' => $codPessoa));
 	
 	#################################################################################
 	## Exclusão
 	#################################################################################
 	for($i = 0; $i < sizeof ( $telefones ); $i ++) {
		if (! in_array ( $telefones [$i]->getCodigo (), $codTelefone )) {
			try {
				$em->remove ( $telefones [$i] );
				$em->flush ();
			} catch ( \Exception $e ) {
				$system->criaAviso ( \Zage\App\Aviso\Tipo::ERRO, "Não foi possível excluir o telefone: " . $telefones [$i]->getTelefone () . " Erro: " . $e->getMessage () );
				echo '1' . \Zage\App\Util::encodeUrl ( '||' . htmlentities ( $e->getMessage () ) );
				exit ();
			}
		}
	}
 	
 	#################################################################################
 	## Criação / Alteração
 	#################################################################################
 	for($i = 0; $i < sizeof ( $codTelefone ); $i ++) {
		$infoTel = $em->getRepository ( 'Entidades\ZgrhuPessoaTelefone' )->findOneBy ( array (
				'codigo' => $codTelefone [$i],
				'codPessoa' => $oPessoa->getCodigo () 
		) );
		
		if (! $infoTel) {
			$infoTel = new \Entidades\ZgrhuPessoaTelefone ();
		}
		
		if ($infoTel->getCodTipoTelefone () !== $codTipoTel [$i] || $infoTel->getTelefone () !== $telefone [$i]) {
			
			$oTipoTel = $em->getRepository ( 'Entidades\ZgappTelefoneTipo' )->findOneBy ( array (
					'codigo' => $codTipoTel [$i] 
			) );
			
			$infoTel->setCodPessoa ( $oPessoa );
			$infoTel->setCodTipoTelefone ( $oTipoTel );
			$infoTel->setTelefone ( $telefone [$i] );
			
			try {
				$em->persist ( $infoTel );
				$em->flush ();
				$em->detach ( $infoTel );
			} catch ( \Exception $e ) {
				$system->criaAviso ( \Zage\App\Aviso\Tipo::ERRO, "Não foi possível cadastrar o telefone: " . $telefone [$i] . " Erro: " . $e->getMessage () );
				echo '1' . \Zage\App\Util::encodeUrl ( '||' . htmlentities ( $e->getMessage () ) );
				exit ();
			}
		}
	}
	
	#################################################################################
	## Dependentes
	#################################################################################
	$dependentes		= $em->getRepository('Entidades\ZgrhuPessoaDependente')->findBy(array('codPessoa' => $codPessoa));
	
	#################################################################################
	## Exclusão
	#################################################################################
	for($i = 0; $i < sizeof ( $dependentes ); $i ++) {
		if (! in_array ( $dependentes [$i]->getCodigo (), $codDependente )) {
			try {
				$em->remove ( $dependentes [$i] );
				$em->flush ();
			} catch ( \Exception $e ) {
				$system->criaAviso ( \Zage\App\Aviso\Tipo::ERRO, "Não foi possível excluir o telefone: " . $dependentes [$i]->getNome () . " Erro: " . $e->getMessage () );
				echo '1' . \Zage\App\Util::encodeUrl ( '||' . htmlentities ( $e->getMessage () ) );
				exit ();
			}
		}
	}
	
	#################################################################################
	## Criação / Alteração
	#################################################################################
	for($i = 0; $i < sizeof ( $codDependente ); $i ++) {

		$infoDep = $em->getRepository ( 'Entidades\ZgrhuPessoaDependente' )->findOneBy ( array (
				'codigo' => $codDependente [$i],
				'codPessoa' => $oPessoa->getCodigo () 
		) );
		
		if (! $infoDep) {
			$infoDep = new \Entidades\ZgrhuPessoaDependente ();
		}
		
		if (isset($indDeficiente [$i]) && (!empty($indDeficiente [$i]))) {
			$indDeficiente	= 1;
		}else{
			$indDeficiente	= 0;
		}
		
		if (!empty($dataNascimentoD [$i])) {
			$dataNascimentoD	= DateTime::createFromFormat($system->config["data"]["dateFormat"], $dataNascimentoD [$i]);
		}else{
			$dataNascimento		= null;
		}
		
		//if ($infoTel->getCodTipoTelefone () !== $codTipoTel [$i] || $infoTel->getTelefone () !== $telefone [$i]) {
			
			$oSexoDep = $em->getRepository ( 'Entidades\ZgsegSexoTipo' )->findOneBy ( array (
					'codigo' => $sexoDependente [$i] 
			) );
			
			$infoDep->setCodPessoa ( $oPessoa );
			$infoDep->setNome($nomeDependente [$i]);
			$infoDep->setSexo($oSexoDep);
			$infoDep->setIndDeficiente($indDeficiente);
			$infoDep->setDataNascimento($dataNascimento);
			
			try {
				$em->persist ( $infoDep );
				$em->flush ();
				$em->detach ( $infoDep );
			} catch ( \Exception $e ) {
				$system->criaAviso ( \Zage\App\Aviso\Tipo::ERRO, "Não foi possível cadastrar o dependente: " . $nomeDependente [$i] . " Erro: " . $e->getMessage () );
				echo '1' . \Zage\App\Util::encodeUrl ( '||' . htmlentities ( $e->getMessage () ) );
				exit ();
			}
		//}
	}
 	
 	 	
} catch (\Exception $e) {
 	$system->criaAviso(\Zage\App\Aviso\Tipo::ERRO,$e->getMessage());
 	echo '1'.\Zage\App\Util::encodeUrl('||'.htmlentities($e->getMessage()));
 	exit;
}
 
$system->criaAviso(\Zage\App\Aviso\Tipo::INFO,"Informações salvas com sucesso");
echo '0'.\Zage\App\Util::encodeUrl('|'.$oPessoa->getCodigo());