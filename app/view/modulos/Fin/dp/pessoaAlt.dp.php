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
	if (isset($_POST['nomeComercial']))	$fantasia			= \Zage\App\Util::antiInjection($_POST['nomeComercial']);
	if (isset($_POST['cpf']))	 		$cgc				= \Zage\App\Util::antiInjection($_POST['cpf']);
	if (isset($_POST['rg']))	 		$rg					= \Zage\App\Util::antiInjection($_POST['rg']);
	if (isset($_POST['dataNas'])) 		$dataNascimento		= \Zage\App\Util::antiInjection($_POST['dataNas']);
	if (isset($_POST['sexo']))	 		$sexo				= \Zage\App\Util::antiInjection($_POST['sexo']);
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

if (isset($_POST['codLogradouro']))		$codLogradouro	= \Zage\App\Util::antiInjection($_POST['codLogradouro']);
if (isset($_POST['endCorreto']))		$endCorreto		= \Zage\App\Util::antiInjection($_POST['endCorreto']);
if (isset($_POST['descLogradouro']))	$descLogradouro	= \Zage\App\Util::antiInjection($_POST['descLogradouro']);
if (isset($_POST['bairro']))			$bairro			= \Zage\App\Util::antiInjection($_POST['bairro']);
if (isset($_POST['complemento']))		$complemento	= \Zage\App\Util::antiInjection($_POST['complemento']);
if (isset($_POST['numero']))			$numero			= \Zage\App\Util::antiInjection($_POST['numero']);
if (isset($_POST['cep']))				$cep			= \Zage\App\Util::antiInjection($_POST['cep']);

if (!isset($codTipoTel))				$codTipoTel			= array();
if (!isset($codTelefone))				$codTelefone		= array();
if (!isset($telefone))					$telefone			= array();

if (!isset($codConta))					$codConta			= array();
if (!isset($codBanco))					$codBanco			= array();
if (!isset($agencia))					$agencia			= array();
if (!isset($ccorrente))					$ccorrente			= array();

#################################################################################
## Limpar a variável de erro
#################################################################################
$err	= false;

#################################################################################
## Fazer validação dos campos
#################################################################################
if (!isset($cep) || (empty($cep))) {
	$system->criaAviso(\Zage\App\Aviso\Tipo::ERRO," O campo CEP deve ser preenchido");
	$err	= 1;
}elseif ((!empty($cep)) && (strlen($cep) > 8)) {
	$system->criaAviso(\Zage\App\Aviso\Tipo::ERRO," O CEP não deve conter mais de 8 caracteres.");
	$err	= 1;
}

if ($tipo == 'J'){
	/******* Nome *********/
	if (!isset($nome) || (empty($nome))) {
		$system->criaAviso(\Zage\App\Aviso\Tipo::ERRO," Informe a razão social da pessoa jurídica.");
		$err	= 1;
	}elseif ((!empty($nome)) && (strlen($nome) > 100)) {
		$system->criaAviso(\Zage\App\Aviso\Tipo::ERRO," A razão social não deve conter mais de 100 caracteres.");
		$err	= 1;
	}
	
	/******* Fantasia *********/
	if (!isset($fantasia) || (empty($fantasia))) {
		$system->criaAviso(\Zage\App\Aviso\Tipo::ERRO,"Informe o nome fantasia da pessoa jurídica.");
		$err	= 1;
	}elseif ((!empty($fantasia)) && (strlen($fantasia) > 100)) {
		$system->criaAviso(\Zage\App\Aviso\Tipo::ERRO,"O nome fantasia não deve conter mais de 100 caracteres.");
		$err	= 1;
	}
	
	/******** Início de Atividade ***********/
	if (!empty($dataNascimento)) {
		if (\Zage\App\Util::validaData($dataNascimento, $system->config["data"]["dateFormat"]) == false) {
			$system->criaAviso(\Zage\App\Aviso\Tipo::ERRO,$tr->trans("A data de abertura está com formato inválido."));
			$err	= 1;
		}
	}
}

if ($tipo == 'F'){
	/******* Nome *********/
	if (!isset($nome) || (empty($nome))) {
		$system->criaAviso(\Zage\App\Aviso\Tipo::ERRO," Informe o nome completo da pessoa física.");
		$err	= 1;
	}elseif ((!empty($nome)) && (strlen($nome) > 100)){
		$system->criaAviso(\Zage\App\Aviso\Tipo::ERRO," O nome completo não deve conter mais de 100 caracteres.");
		$err	= 1;
	}
	
	/******* Nome Comercial *********/
	if (!isset($fantasia) || (empty($fantasia))) {
		$system->criaAviso(\Zage\App\Aviso\Tipo::ERRO," Informe o nome comercial da pessoa física.");
		$err	= 1;
	}elseif ((!empty($fantasia)) && (strlen($fantasia) > 100)){
		$system->criaAviso(\Zage\App\Aviso\Tipo::ERRO," O nome comercial não deve ter mais que 100 caracteres.");
		$err	= 1;
	}

	/******* RG *********/
	if ((!empty($rg)) && (strlen($rg) > 14)) {
		$system->criaAviso(\Zage\App\Aviso\Tipo::ERRO," O RG não deve conter mais de 14 caracteres.");
		$err	= 1;
	}
	
	/******* Data de nascimento *********/
	if (!empty($dataNascimento)) {
		if (\Zage\App\Util::validaData($dataNascimento, $system->config["data"]["dateFormat"]) == false) {
			$system->criaAviso(\Zage\App\Aviso\Tipo::ERRO,$tr->trans("A data de nascimento está inválido."));
			$err	= 1;
		}
	}
}

/******* Status *********/
if (isset($ativo) && (!empty($ativo))) {
	$ativo	= 1;
}else{
	$ativo	= 0;
}

/******* IND Estrangeiro *********/
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
		$system->criaAviso(\Zage\App\Aviso\Tipo::ERRO,$tr->trans("O %s deve ser preenchido",array('%s' => $nomeCampoMen)));
		$err	= 1;
	}else{
		if ($valCgc->isValid($cgc) == false) {
			$system->criaAviso(\Zage\App\Aviso\Tipo::ERRO,$tr->trans("O %s inválido",array('%s' => $nomeCampoMen)));
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
$em->getConnection()->beginTransaction();
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
 	$oTipoEnd	= $em->getRepository('Entidades\ZgfinEnderecoTipo')->findOneBy(array('codigo' => "F"));
 	$oCodLogradouro		= $em->getRepository('Entidades\ZgadmLogradouro')->findOneBy(array('codigo' => $codLogradouro));
 	
 	if (!empty($dataNascimento)) {
 		$dtNasc		= DateTime::createFromFormat($system->config["data"]["dateFormat"], $dataNascimento);
 	}else{
 		$dtNasc		= null;
 	}
 	
 	if ($tipoCadPessoa == "C") {
 		$oPessoa->setIndCliente(1);
 	}elseif ($tipoCadPessoa == "F") {
 		$oPessoa->setIndFornecedor(1);
 	}elseif ($tipoCadPessoa == "T") {
 		$oPessoa->setIndTransportadora(1);
 	}
 	
 	//$oPessoa->setCodParceiro($oOrg);
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
 	
 	/**** Pessoa - organização ****/
 	$oPessoaOrg	= $em->getRepository('Entidades\ZgfinPessoaOrganizacao')->findOneBy(array('codigo' => $oPessoa->getCodigo()));
 	if (!$oPessoaOrg) {
 		$oPessoaOrg	= new \Entidades\ZgfinPessoaOrganizacao();
 		//$oPessoaOrg->setDataCadastro(new \DateTime("now"));$oPessoa->setIndCliente(0);
 		$oEndereco	= new \Entidades\ZgfinPessoaEnderecoOrganizacao();
 		$oPessoaOrg->setIndCliente(0);
 		$oPessoaOrg->setIndFornecedor(0);
 		$oPessoaOrg->setIndTransportadora(0);
 	}else{
 		$oEndereco	= $em->getRepository('Entidades\ZgfinPessoaEnderecoOrganizacao')->findOneBy(array('codPessoa' => $codPessoa));
 	}
 	
 	if ($tipoCadPessoa == "C") {
 		$oPessoaOrg->setIndCliente(1);
 	}elseif ($tipoCadPessoa == "F") {
 		$oPessoaOrg->setIndFornecedor(1);
 	}elseif ($tipoCadPessoa == "T") {
 		$oPessoaOrg->setIndTransportadora(1);
 	}
 	
 	$oPessoaOrg->setCodPessoa($oPessoa);
 	$oPessoaOrg->setCodOrganizacao($oOrg);
 	$oPessoaOrg->setIndContribuinte(0);
 	
 	$em->persist($oPessoaOrg);
 	
 	/**** Pessoa - endereço - organização ****/
 	$oEndereco->setCodPessoa($oPessoa);
 	$oEndereco->setCodOrganizacao($oOrg);
 	$oEndereco->setCodTipoEndereco($oTipoEnd);
 	$oEndereco->setCodLogradouro($oCodLogradouro);
 	$oEndereco->setEndereco($descLogradouro);
 	$oEndereco->setNumero($numero);
 	$oEndereco->setCep($cep);
 	$oEndereco->setBairro($bairro);
 	$oEndereco->setComplemento($complemento);
 	$oEndereco->setIndEndCorreto($endCorreto == "on" ? 1 : 0);
 	
 	$em->persist($oEndereco);
 	
 	#################################################################################
 	## Contato
 	#################################################################################
 	$telefones		= $em->getRepository('Entidades\ZgfinPessoaTelefoneOrganizacao')->findBy(array('codPessoa' => $codPessoa , 'codOrganizacao' => $oOrg->getCodigo() , 'codOrganizacao' => $oOrg->getCodigo()));
 	
 	#################################################################################
 	## Exclusão
 	#################################################################################
 	for ($i = 0; $i < sizeof($telefones); $i++) {
 		if (!in_array($telefones[$i]->getCodigo(), $codTelefone)) {
 			try {
 				$em->remove($telefones[$i]);
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
 		$infoTel		= $em->getRepository('Entidades\ZgfinPessoaTelefoneOrganizacao')->findOneBy(array('codigo' => $codTelefone[$i] , 'codPessoa' => $oPessoa->getCodigo() , 'codOrganizacao' => $oOrg->getCodigo()));
 	
 		if (!$infoTel) {
 			$infoTel		= new \Entidades\ZgfinPessoaTelefoneOrganizacao();
 		}
 		
 		if ($infoTel->getCodTipoTelefone() !== $codTipoTel[$i] || $infoTel->getTelefone() !== $telefone[$i]) {
 			
 			$oTipoTel	= $em->getRepository('Entidades\ZgappTelefoneTipo')->findOneBy(array('codigo' => $codTipoTel[$i]));
 			
 			$infoTel->setCodPessoa($oPessoa);
 			$infoTel->setCodOrganizacao($oOrg);
 			$infoTel->setCodTipoTelefone($oTipoTel);
 			$infoTel->setTelefone($telefone[$i]);
 		 	
 			try {
 				$em->persist($infoTel);
 			} catch (\Exception $e) {
 				$system->criaAviso(\Zage\App\Aviso\Tipo::ERRO,"Não foi possível cadastrar o telefone: ".$telefone[$i]." Erro: ".$e->getMessage());
 				echo '1'.\Zage\App\Util::encodeUrl('||'.htmlentities($e->getMessage()));
 				exit;
 			}
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
	$contas		= $em->getRepository('Entidades\ZgfinPessoaContaOrganizacao')->findBy(array('codPessoa' => $codPessoa , 'codOrganizacao' => $oOrg->getCodigo()));
	
	#################################################################################
	## Exclusão
	#################################################################################
	for ($i = 0; $i < sizeof($contas); $i++) {
		if (!in_array($contas[$i]->getCodigo(), $codConta)) {
			try {
				$em->remove($contas[$i]);
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
		$infoConta		= $em->getRepository('Entidades\ZgfinPessoaContaOrganizacao')->findOneBy(array('codigo' => $codConta[$i] , 'codPessoa' => $oPessoa->getCodigo() , 'codOrganizacao' => $oOrg->getCodigo()));
		if (!$infoConta) {
			$infoConta		= new \Entidades\ZgfinPessoaContaOrganizacao();
		}
		
		$oBanco	= $em->getRepository('Entidades\ZgfinBanco')->findOneBy(array('codigo' => $codBanco[$i]));
	
		$infoConta->setCodPessoa($oPessoa);
		$infoConta->setCodOrganizacao($oOrg);
		$infoConta->setCodBanco($oBanco);
		$infoConta->setAgencia($agencia[$i]);
		$infoConta->setCcorrente($ccorrente[$i]);
				
		try {
			$em->persist($infoConta);
		} catch (\Exception $e) {
			$system->criaAviso(\Zage\App\Aviso\Tipo::ERRO,"Não foi possível cadastrar o telefone: ".$telefone[$i]." Erro: ".$e->getMessage());
			echo '1'.\Zage\App\Util::encodeUrl('||'.htmlentities($e->getMessage()));
 			exit;
		}
	}
	$em->flush();
	$em->clear();
	$em->getConnection()->commit();
	/**
	#################################################################################
	## Salvar no banco
	#################################################################################
	try {
		$em->flush();
		$em->clear();
		$em->getConnection()->commit();	
	} catch (\Exception $e) {
		echo '1'.\Zage\App\Util::encodeUrl('||'.htmlentities($e->getMessage()));
		exit;
	}
	**/
} catch (\Exception $e) {
	$em->getConnection()->rollback();
 	$system->criaAviso(\Zage\App\Aviso\Tipo::ERRO,$e->getMessage());
 	echo '1'.\Zage\App\Util::encodeUrl('||'.htmlentities($e->getMessage()));
 	exit;
}
 
$system->criaAviso(\Zage\App\Aviso\Tipo::INFO,"Informações salvas com sucesso");
echo '0'.\Zage\App\Util::encodeUrl('|'.$oPessoa->getCodigo());