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
if (isset($_POST['codPessoa']))			$codPessoa			= \Zage\App\Util::antiInjection($_POST['codPessoa']);
if (isset($_POST['tipo']))				$tipo				= \Zage\App\Util::antiInjection($_POST['tipo']);
if (isset($_POST['ativo']))				$ativo				= \Zage\App\Util::antiInjection($_POST['ativo']);
if (isset($_POST['link']))				$link				= \Zage\App\Util::antiInjection($_POST['link']);
if (isset($_POST['email']))				$email				= \Zage\App\Util::antiInjection($_POST['email']);
if (isset($_POST['tipoCadPessoa']))		$tipoCadPessoa		= \Zage\App\Util::antiInjection($_POST['tipoCadPessoa']);
if (isset($_POST['indEstrangeiro']))	$indEstrangeiro		= \Zage\App\Util::antiInjection($_POST['indEstrangeiro']);
if (isset($_POST['aSegs']))				$aSegs				= \Zage\App\Util::antiInjection($_POST['aSegs']);

if ($tipo == 'J'){
	
	if (isset($_POST['razao'])) 		$nome				= \Zage\App\Util::antiInjection($_POST['razao']);
	if (isset($_POST['cnpj']))	 		$cgc				= \Zage\App\Util::antiInjection($_POST['cnpj']);
	if (isset($_POST['fantasia'])) 		$fantasia			= \Zage\App\Util::antiInjection($_POST['fantasia']);
	if (isset($_POST['inscrEst'])) 		$inscEstadual		= \Zage\App\Util::antiInjection($_POST['inscrEst']);
	if (isset($_POST['inscrMun'])) 		$inscMunicipal		= \Zage\App\Util::antiInjection($_POST['inscrMun']);
	if (isset($_POST['dataInicio'])) 	$dataNascimento		= \Zage\App\Util::antiInjection($_POST['dataInicio']);
	$rg				= '';
	$sexo			= '';
	
}elseif ($tipo == 'F'){
	
	if (isset($_POST['nome'])) 			$nome				= \Zage\App\Util::antiInjection($_POST['nome']);
	if (isset($_POST['cpf']))	 		$cgc				= \Zage\App\Util::antiInjection($_POST['cpf']);
	if (isset($_POST['rg']))	 		$rg					= \Zage\App\Util::antiInjection($_POST['rg']);
	if (isset($_POST['dataNas'])) 		$dataNascimento		= \Zage\App\Util::antiInjection($_POST['dataNas']);
	if (isset($_POST['sexo']))	 		$sexo				= \Zage\App\Util::antiInjection($_POST['sexo']);
	$fantasia		= '';
	$inscEstadual	= '';
	$inscMunicipal	= '';
}

if (isset($_POST['codTipoTel']))		$codTipoTel			= $_POST['codTipoTel'];
if (isset($_POST['codTelefone']))		$codTelefone		= $_POST['codTelefone'];
if (isset($_POST['telefone']))			$telefone			= $_POST['telefone'];

if (isset($_POST['codConta']))			$codConta			= $_POST['codConta'];
if (isset($_POST['codBanco']))			$codBanco			= $_POST['codBanco'];
if (isset($_POST['agencia']))			$agencia			= $_POST['agencia'];
if (isset($_POST['ccorrente']))			$ccorrente			= $_POST['ccorrente'];

if (isset($_POST['codTipoEnd']))		$codTipoEnd			= $_POST['codTipoEnd'];
if (isset($_POST['cep']))				$cep				= $_POST['cep'];
if (isset($_POST['bairro']))			$bairro				= $_POST['bairro'];
if (isset($_POST['endereco']))			$endereco			= $_POST['endereco'];
if (isset($_POST['numero']))			$numero				= $_POST['numero'];
if (isset($_POST['complemento']))		$complemento		= $_POST['complemento'];
if (isset($_POST['codCidade']))			$codCidade			= $_POST['codCidade'];
if (isset($_POST['codLogradouro']))		$codLogradouro		= $_POST['codLogradouro'];
if (isset($_POST['codEndereco']))		$codEndereco		= $_POST['codEndereco'];

if (!isset($codTipoTel))				$codTipoTel			= array();
if (!isset($codTelefone))				$codTelefone		= array();
if (!isset($telefone))					$telefone			= array();

if (!isset($codConta))					$codConta			= array();
if (!isset($codBanco))					$codBanco			= array();
if (!isset($agencia))					$agencia			= array();
if (!isset($ccorrente))					$ccorrente			= array();

if (!isset($codTipoEnd))				$codTipoEnd			= array();
if (!isset($cep))						$cep				= array();
if (!isset($bairro))					$bairro				= array();
if (!isset($endereco))					$endereco			= array();
if (!isset($numero))					$numero				= array();
if (!isset($complemento))				$complemento		= array();
if (!isset($codCidade))					$codCidade			= array();
if (!isset($codLogradouro))				$codLogradouro		= array();
if (!isset($codEndereco))				$codEndereco		= array();



#################################################################################
## Limpar a variável de erro
#################################################################################
$err	= false;

#################################################################################
## Fazer validação dos campos
#################################################################################
if ($tipo == 'J'){
	/******* Nome *********/
	if (!isset($nome) || (empty($nome))) {
		$system->criaAviso(\Zage\App\Aviso\Tipo::ERRO," Campo RAZÃO é obrigatório");
		$err	= 1;
	}
	
	if ((!empty($nome)) && (strlen($nome) > 100)) {
		$system->criaAviso(\Zage\App\Aviso\Tipo::ERRO," Campo RAZÃO não deve conter mais de 100 caracteres");
		$err	= 1;
	}
	
	/******* Fantasia *********/
	if (!isset($fantasia) || (empty($fantasia))) {
		$system->criaAviso(\Zage\App\Aviso\Tipo::ERRO,"Campo FANTASIA é obrigatório");
		$err	= 1;
	}
	
	if ((!empty($fantasia)) && (strlen($fantasia) > 60)) {
		$system->criaAviso(\Zage\App\Aviso\Tipo::ERRO,"Campo FANTASIA não deve conter mais de 60 caracteres");
		$err	= 1;
	}
	
	/******** Início de Atividade ***********/
	if (!empty($dataNascimento)) {
		if (\Zage\App\Util::validaData($dataNascimento, $system->config["data"]["dateFormat"]) == false) {
			$system->criaAviso(\Zage\App\Aviso\Tipo::ERRO,$tr->trans("Campo Início de Atividade inválido"));
			$err	= 1;
		}
	}
	
	
}

if ($tipo == 'F'){
	/******* Nome *********/
	if (!isset($nome) || (empty($nome))) {
		$system->criaAviso(\Zage\App\Aviso\Tipo::ERRO," Campo NOME é obrigatório");
		$err	= 1;
	}

	if ((!empty($nome)) && (strlen($nome) > 100)) {
		$system->criaAviso(\Zage\App\Aviso\Tipo::ERRO," Campo NOME não deve conter mais de 100 caracteres");
		$err	= 1;
	}

	/******* RG *********/
	/*if (!isset($rg) || (empty($rg))) {
		$system->criaAviso(\Zage\App\Aviso\Tipo::ERRO," Campo RG é obrigatório");
		$err	= 1;
	}*/

	if ((!empty($rg)) && (strlen($rg) > 14)) {
		$system->criaAviso(\Zage\App\Aviso\Tipo::ERRO," Campo RG não deve conter mais de 14 caracteres");
		$err	= 1;
	}
	
	if (!empty($dataNascimento)) {
		if (\Zage\App\Util::validaData($dataNascimento, $system->config["data"]["dateFormat"]) == false) {
			$system->criaAviso(\Zage\App\Aviso\Tipo::ERRO,$tr->trans("Campo Data de Nascimento inválido"));
			$err	= 1;
		}
	}
	
}




if (isset($ativo) && (!empty($ativo))) {
	$ativo	= 1;
}else{
	$ativo	= 0;
}

if (isset($indEstrangeiro) && (!empty($indEstrangeiro))) {
	$indEstrangeiro	= 1;
}else{
	$indEstrangeiro	= 0;
}

/******** CPF / CGC (obrigatórios para pessoas não estrangeiras) ***********/
if ($indEstrangeiro == 0) {
	if ($tipo	== 'F') {
		$nomeCampoMen	= "CPF";
		$valCgc			= new \Zage\App\Validador\Cpf(); 
	}else{
		$nomeCampoMen	= "CNPJ";
		$valCgc			= new \Zage\App\Validador\Cnpj();
	}
	
	if (empty($cgc)) {
		$system->criaAviso(\Zage\App\Aviso\Tipo::ERRO,$tr->trans("Campo %s deve ser preenchido",array('%s' => $nomeCampoMen)));
		$err	= 1;
	}else{
		if ($valCgc->isValid($cgc) == false) {
			$system->criaAviso(\Zage\App\Aviso\Tipo::ERRO,$tr->trans("Campo %s inválido",array('%s' => $nomeCampoMen)));
			$err	= 1;
		}
	}
}


/** Fonte de Recurso (CONTA) **/
if (  (isset($codBanco) && !empty($codBanco)) || (isset($agencia) && !empty($agencia)) || (isset($ccorrente) && !empty($ccorrente))) {
	if (empty($codBanco)) {
		$system->criaAviso(\Zage\App\Aviso\Tipo::ERRO,$tr->trans("Campo %s deve ser preenchido",array('%s' => 'BANCO')));
		$err	= 1;
	}

	if (empty($agencia)) {
		$system->criaAviso(\Zage\App\Aviso\Tipo::ERRO,$tr->trans("Campo %s deve ser preenchido",array('%s' => 'AGÊNCIA')));
		$err	= 1;
	}
	
	if (empty($ccorrente)) {
		$system->criaAviso(\Zage\App\Aviso\Tipo::ERRO,$tr->trans("Campo %s deve ser preenchido",array('%s' => 'CONTA CORRENTE')));
		$err	= 1;
	}
	
	
	$oBanco		= $em->getRepository('Entidades\ZgfinBanco')->findOneBy(array('codigo' => $codBanco));
	
	if (!$oBanco) {
		$system->criaAviso(\Zage\App\Aviso\Tipo::ERRO,$tr->trans("Campo %s inválido, selecione um banco válido",array('%s' => 'BANCO')));
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
	if (isset($codPessoa) && (!empty($codPessoa))) {
 		$oPessoa	= $em->getRepository('Entidades\ZgfinPessoa')->findOneBy(array('codigo' => $codPessoa));
 		if (!$oPessoa) {
 			$oPessoa	= new \Entidades\ZgfinPessoa();
 			$oPessoa->setDataCadastro(new \DateTime("now"));
 			$oPessoa->setIndCliente(0);
 			$oPessoa->setIndFornecedor(0);
 			$oPessoa->setIndTransportadora(0);
 		}
 	}else{
 		$oPessoa	= new \Entidades\ZgfinPessoa();
 		$oPessoa->setDataCadastro(new \DateTime("now"));
 		$oPessoa->setIndCliente(0);
 		$oPessoa->setIndFornecedor(0);
 		$oPessoa->setIndTransportadora(0);
 	}
 	
 	$oOrg		= $em->getRepository('Entidades\ZgadmOrganizacao')->findOneBy(array('codigo' => $system->getCodOrganizacao()));
 	$oSexo		= $em->getRepository('Entidades\ZgsegSexoTipo')->findOneBy(array('codigo' => $sexo));
 	$oTipo		= $em->getRepository('Entidades\ZgfinPessoaTipo')->findOneBy(array('codigo' => $tipo));
 	
 	if (!empty($dataNascimento)) {
 		$dtNasc		= DateTime::createFromFormat($system->config["data"]["dateFormat"], $dataNascimento);
 	}else{
 		$dtNasc		= null;
 	}
 	
 	
 	//$log->debug("Tipo Cad Pessoa: ".$tipoCadPessoa);
 	
 	if ($tipoCadPessoa == "C") {
 		$oPessoa->setIndCliente(1);
 	}elseif ($tipoCadPessoa == "F") {
 		$oPessoa->setIndFornecedor(1);
	}elseif ($tipoCadPessoa == "T") {
		$oPessoa->setIndTransportadora(1);
 	}
 	
 	$oPessoa->setCodOrganizacao($oOrg);
 	$oPessoa->setNome($nome);
 	$oPessoa->setFantasia($fantasia);
 	$oPessoa->setCgc($cgc);
 	$oPessoa->setRg($rg);
 	$oPessoa->setInscEstadual($inscEstadual);
 	$oPessoa->setInscMunicipal($inscMunicipal);
 	$oPessoa->setCodTipoPessoa($oTipo);
 	$oPessoa->setEmail($email);
 	$oPessoa->setIndAtivo($ativo);
 	$oPessoa->setIndContribuinte(0);
 	$oPessoa->setDataNascimento($dtNasc);
 	$oPessoa->setCodSexo($oSexo);
 	$oPessoa->setLink($link);
 	$oPessoa->setIndEstrangeiro($indEstrangeiro);
 	
 	$em->persist($oPessoa);
 	$em->flush();
 	//$em->detach($oPessoa);
 	
 	
 	
 	#################################################################################
 	## Contato
 	#################################################################################
 	$telefones		= $em->getRepository('Entidades\ZgfinPessoaTelefone')->findBy(array('codProprietario' => $codPessoa));
 	
 	#################################################################################
 	## Exclusão
 	#################################################################################
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
 	
 	#################################################################################
 	## Criação / Alteração 
 	#################################################################################
 	for ($i = 0; $i < sizeof($codTelefone); $i++) {
 		$infoTel		= $em->getRepository('Entidades\ZgfinPessoaTelefone')->findOneBy(array('codigo' => $codTelefone[$i] , 'codProprietario' => $oPessoa->getCodigo()));
 	
 		if (!$infoTel) {
 			$infoTel		= new \Entidades\ZgfinPessoaTelefone();
 		}
 		
 		if ($infoTel->getCodTipoTelefone() !== $codTipoTel[$i] || $infoTel->getTelefone() !== $telefone[$i]) {
 			
 			$oTipoTel	= $em->getRepository('Entidades\ZgappTelefoneTipo')->findOneBy(array('codigo' => $codTipoTel[$i]));
 			
 			$infoTel->setCodProprietario($oPessoa);
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
 	 	

 	
 	if (!isset($codTipoEnd))				$codTipoEnd			= array();
 	if (!isset($cep))						$cep				= array();
 	if (!isset($bairro))					$bairro				= array();
 	if (!isset($endereco))					$endereco			= array();
 	if (!isset($numero))					$numero				= array();
 	if (!isset($complemento))				$complemento		= array();
 	if (!isset($codCidade))					$codCidade			= array();
 	if (!isset($codLogradouro))				$codLogradouro		= array();
 	if (!isset($codEndereco))				$codEndereco		= array();
 	
 	
 	#################################################################################
 	## Endereço
 	#################################################################################
 	$enderecos		= $em->getRepository('Entidades\ZgfinPessoaEndereco')->findBy(array('codPessoa' => $codPessoa));
 	
 	#################################################################################
 	## Exclusão
 	#################################################################################
 	for ($i = 0; $i < sizeof($enderecos); $i++) {
 		if (!in_array($enderecos[$i]->getCodigo(), $endereco)) {
 			try {
 				$em->remove($enderecos[$i]);
 				$em->flush();
 			} catch (\Exception $e) {
 				$system->criaAviso(\Zage\App\Aviso\Tipo::ERRO,"Não foi possível excluir o endereco: ".$enderecos[$i]->getEndereco()." Erro: ".$e->getMessage());
 				echo '1'.\Zage\App\Util::encodeUrl('||'.htmlentities($e->getMessage()));
 				exit;
 			}
 		}
 	}
 	
 	
 	#################################################################################
 	## Criação / Alteração
 	#################################################################################
 	for ($i = 0; $i < sizeof($codEndereco); $i++) {
 		$infoEnd		= $em->getRepository('Entidades\ZgfinPessoaEndereco')->findOneBy(array('codigo' => $codEndereco[$i] , 'codPessoa' => $oPessoa->getCodigo()));
 		if (!$infoEnd) 	$infoEnd		= new \Entidades\ZgfinPessoaEndereco();

 		//$oCidade		= $em->getRepository('Entidades\ZgadmCidade')->findOneBy(array('codigo' => $codCidade[$i]));
		$oTipoEnd		= $em->getRepository('Entidades\ZgfinEnderecoTipo')->findOneBy(array('codigo' => $codTipoEnd[$i]));
		$oLogradouro	= $em->getRepository('Entidades\ZgadmLogradouro')->findOneBy(array('codigo' => $codLogradouro[$i]));
 		
 		$infoEnd->setCodPessoa($oPessoa);
 		$infoEnd->setCodTipoEndereco($oTipoEnd);
 		$infoEnd->setEndereco($endereco[$i]);
 		$infoEnd->setNumero($numero[$i]);
 		$infoEnd->setBairro($bairro[$i]);
 		$infoEnd->setComplemento($complemento[$i]);
 		$infoEnd->setCep($cep[$i]);
 		//$infoEnd->setCodCidade($oCidade);
 		$infoEnd->setCodLogradouro($oLogradouro);
 				
 		try {
 			$em->persist($infoEnd);
 			$em->flush();
 			$em->detach($infoEnd);
 		} catch (\Exception $e) {
 			$system->criaAviso(\Zage\App\Aviso\Tipo::ERRO,"Não foi possível cadastrar o endereço: ".$endereco[$i]." Erro: ".$e->getMessage());
 			echo '1'.\Zage\App\Util::encodeUrl('||'.htmlentities($e->getMessage()));
 			exit;
 		}
 		
 	}
 	
 	#################################################################################
 	## Segmentos
 	#################################################################################
 	if (!empty($aSegs)) {
 		$arraySeg	= explode(",", $aSegs);
 	}else{
 		$arraySeg = array();
 	}
 	
 	//$log->debug("ArraySeg:".serialize($arraySeg));
 		
 	#################################################################################
 	## Lista de segmentos já associados
 	#################################################################################
 	$segAss		= \Zage\Fin\Pessoa::listaSegmentos($codPessoa);
 	$aSegAss	= array();
 		
 	#################################################################################
 	## Exclusão
 	#################################################################################
	for ($i = 0; $i < sizeof($segAss); $i++) {
 		//$log->debug("I: $i (Segmento = ".$segAss[$i]->getCodSegmento()->getCodigo().")");
		$aSegAss[]	= $segAss[$i]->getCodSegmento()->getCodigo();
		
		if (!in_array($segAss[$i]->getCodSegmento()->getCodigo(), $arraySeg)) {
			try {
				$em->remove($segAss[$i]);
				$em->flush();
			} catch (\Exception $e) {
				$system->criaAviso(\Zage\App\Aviso\Tipo::ERRO,"Não foi possível excluir o segmento: ".$segAss[$i]->getCodSegmento()->getDescricao()." Erro: ".$e->getMessage());
				echo '1'.\Zage\App\Util::encodeUrl('||'.htmlentities($e->getMessage()));
				exit;
			}
 		}
	}
 	
	#################################################################################
	## Inclusão
	#################################################################################
	for ($i = 0; $i < sizeof($arraySeg); $i++) {
		if (!in_array($arraySeg[$i], $aSegAss)) {
			try {
				$oSeg			= $em->getRepository('Entidades\ZgfinSegmentoMercado')->findOneBy(array('codigo' => $arraySeg[$i]));
 	
				if (!$oSeg){
					$system->criaAviso(\Zage\App\Aviso\Tipo::ERRO,"Não foi possível encontrar o segmento: ".$arraySeg[$i]);
					echo '1'.\Zage\App\Util::encodeUrl('||');
					exit;
				}
 	
				$oPesSegMer		= new \Entidades\ZgfinPessoaSegmento();
				$oPesSegMer->setCodPessoa($oPessoa);
				$oPesSegMer->setCodSegmento($oSeg);
 	
				$em->persist($oPesSegMer);
				$em->flush();
				$em->detach($oPesSegMer);
			} catch (\Exception $e) {
				$system->criaAviso(\Zage\App\Aviso\Tipo::ERRO,"Não foi possível excluir o segmento: ".$segAss[$i]->getCodSegmento()->getDescricao()." Erro: ".$e->getMessage());
				echo '1'.\Zage\App\Util::encodeUrl('||'.htmlentities($e->getMessage()));
				exit;
			}
		}
	}

	
	#################################################################################
	## Fonte de Recurso
	#################################################################################
	$contas		= $em->getRepository('Entidades\ZgfinPessoaConta')->findBy(array('codPessoa' => $codPessoa));
	
	#################################################################################
	## Exclusão
	#################################################################################
	for ($i = 0; $i < sizeof($contas); $i++) {
		if (!in_array($contas[$i]->getCodigo(), $codConta)) {
			try {
				$em->remove($contas[$i]);
				$em->flush();
			} catch (\Exception $e) {
				$system->criaAviso(\Zage\App\Aviso\Tipo::ERRO,"Não foi possível excluir a conta: ".$contas[$i]->getCcorrente()." Erro: ".$e->getMessage());
				echo '1'.\Zage\App\Util::encodeUrl('||'.htmlentities($e->getMessage()));
				exit;
			}
		}
	}
	
	#################################################################################
	## Criação / Alteração
	#################################################################################
	for ($i = 0; $i < sizeof($codConta); $i++) {
		$infoConta		= $em->getRepository('Entidades\ZgfinPessoaConta')->findOneBy(array('codigo' => $codConta[$i] , 'codPessoa' => $oPessoa->getCodigo()));
		if (!$infoConta) {
			$infoConta		= new \Entidades\ZgfinPessoaConta();
		}
		
		$oBanco	= $em->getRepository('Entidades\ZgfinBanco')->findOneBy(array('codigo' => $codBanco[$i]));
	
		$infoConta->setCodPessoa($oPessoa);
		$infoConta->setCodBanco($oBanco);
		$infoConta->setAgencia($agencia[$i]);
		$infoConta->setCcorrente($ccorrente[$i]);
				
		try {
			$em->persist($infoConta);
			$em->flush();
			$em->detach($infoConta);
		} catch (\Exception $e) {
			$system->criaAviso(\Zage\App\Aviso\Tipo::ERRO,"Não foi possível cadastrar o telefone: ".$telefone[$i]." Erro: ".$e->getMessage());
			echo '1'.\Zage\App\Util::encodeUrl('||'.htmlentities($e->getMessage()));
 			exit;
		}
	}
	
	 	
} catch (\Exception $e) {
 	$system->criaAviso(\Zage\App\Aviso\Tipo::ERRO,$e->getMessage());
 	echo '1'.\Zage\App\Util::encodeUrl('||'.htmlentities($e->getMessage()));
 	exit;
}
 
$system->criaAviso(\Zage\App\Aviso\Tipo::INFO,"Informações salvas com sucesso");
echo '0'.\Zage\App\Util::encodeUrl('|'.$oPessoa->getCodigo());