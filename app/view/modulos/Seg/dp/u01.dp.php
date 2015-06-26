<?php
#################################################################################
## Includes
#################################################################################
if (defined('DOC_ROOT')) {
	include_once(DOC_ROOT . 'includeNoAuth.php');
}else{
 	include_once('../includeNoAuth.php');
}

#################################################################################
## Resgata os parâmetros passados pelo formulario
#################################################################################
if (isset($_POST['_cdu01'])) 			$codAssoc			= \Zage\App\Util::antiInjection($_POST['_cdu01']);
if (isset($_POST['_cdu02'])) 			$codUsuario			= \Zage\App\Util::antiInjection($_POST['_cdu02']);
if (isset($_POST['_cdu03'])) 			$codOrganizacao		= \Zage\App\Util::antiInjection($_POST['_cdu03']);
if (isset($_POST['_cdu04'])) 			$codConvite			= \Zage\App\Util::antiInjection($_POST['_cdu04']);

if (isset($_POST['nome'])) 				$nome				= \Zage\App\Util::antiInjection($_POST['nome']);
if (isset($_POST['apelido']))			$apelido			= \Zage\App\Util::antiInjection($_POST['apelido']);
if (isset($_POST['cpf'])) 				$cpf				= \Zage\App\Util::antiInjection($_POST['cpf']);
if (isset($_POST['sexo'])) 				$sexo				= \Zage\App\Util::antiInjection($_POST['sexo']);
if (isset($_POST['senhaCad'])) 			$senha				= \Zage\App\Util::antiInjection($_POST['senhaCad']);
if (isset($_POST['confSenhaCad'])) 		$confSenha			= \Zage\App\Util::antiInjection($_POST['confSenhaCad']);

if (isset($_POST['codLogradouro'])) 	$codLogradouro		= \Zage\App\Util::antiInjection($_POST['codLogradouro']);
if (isset($_POST['cep'])) 				$cep				= \Zage\App\Util::antiInjection($_POST['cep']);
if (isset($_POST['descLogradouro'])) 	$descLogradouro		= \Zage\App\Util::antiInjection($_POST['descLogradouro']);
if (isset($_POST['bairro'])) 			$bairro				= \Zage\App\Util::antiInjection($_POST['bairro']);
if (isset($_POST['numero'])) 			$numero				= \Zage\App\Util::antiInjection($_POST['numero']);
if (isset($_POST['complemento'])) 		$complemento		= \Zage\App\Util::antiInjection($_POST['complemento']);
if (isset($_POST['endCorreto']))		$endCorreto		= \Zage\App\Util::antiInjection($_POST['endCorreto']);

if (isset($_POST['codTipoTel']))		$codTipoTel			= $_POST['codTipoTel'];
if (isset($_POST['codTelefone']))		$codTelefone		= $_POST['codTelefone'];
if (isset($_POST['telefone']))			$telefone			= $_POST['telefone'];

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

/** Nome **/
if (!isset($nome) || empty($nome)) {
	die ('1'.\Zage\App\Util::encodeUrl('||'.htmlentities($tr->trans("O nome deve ser preenchido!"))));
	$err	= 1;
}elseif (strlen($nome) < 5){
	die ('1'.\Zage\App\Util::encodeUrl('||'.htmlentities($tr->trans("Nome muito pequeno, informe o nome completo!"))));
	$err	= 1;
}elseif (strlen($nome) > 100){
	die ('1'.\Zage\App\Util::encodeUrl('||'.htmlentities($tr->trans("O nome não deve conter mais de 100 caracteres!"))));
	$err	= 1;
}

/** Apelido **/
if (!isset($apelido) || empty($apelido)) {
	die ('1'.\Zage\App\Util::encodeUrl('||'.htmlentities($tr->trans("O apelido deve ser preenchido!"))));
	$err	= 1;
}elseif (strlen($apelido) > 60){
	die ('1'.\Zage\App\Util::encodeUrl('||'.htmlentities($tr->trans("O apelido não deve conter mais de 60 caracteres!"))));
	$err	= 1;
}

/** Sexo **/
if (!isset($sexo) || empty($sexo)) {
	die ('1'.\Zage\App\Util::encodeUrl('||'.htmlentities($tr->trans("O sexo deve ser preenchido!"))));
	$err	= 1;
}

/** Senha **/
if (!isset($senha) || empty($senha)) {
	die ('1'.\Zage\App\Util::encodeUrl('||'.htmlentities($tr->trans("A senha deve ser preenchida!"))));
	$err	= 1;
}elseif (strlen($senha) < 4){
	die ('1'.\Zage\App\Util::encodeUrl('||'.htmlentities($tr->trans("Para sua segurança a senha deve ser no mínimo 4 caracteres!"))));
	$err	= 1;
}elseif ($senha !== $confSenha){
	die ('1'.\Zage\App\Util::encodeUrl('||'.htmlentities($tr->trans("A senha está diferente da confirmação!"))));
	$err	= 1;
}

/** ENDEREÇO **/
if (isset($codLogradouro) && (!empty($codLogradouro))){

	/******* CEP *********/
	if (!isset($cep) || (empty($cep))) {
		die ('1'.\Zage\App\Util::encodeUrl('||'.htmlentities($tr->trans("O CEP deve ser preenchido!"))));
		$err	= 1;
	}elseif ((!empty($cep)) && (strlen($cep) > 8)) {
		die ('1'.\Zage\App\Util::encodeUrl('||'.htmlentities($tr->trans("O CEP não deve conter mais de 8 caracteres!"))));
		$err	= 1;
	}

	/******* LOGRADOURO *********/
	if (!isset($descLogradouro) || (empty($descLogradouro))) {
		die ('1'.\Zage\App\Util::encodeUrl('||'.htmlentities($tr->trans("O logradouro deve ser preenchido!"))));
		$err	= 1;
	}elseif ((!empty($descLogradouro)) && (strlen($descLogradouro) > 100)) {
		die ('1'.\Zage\App\Util::encodeUrl('||'.htmlentities($tr->trans("O logradouro não deve conter mais de 100 caracteres!"))));
		$err	= 1;
	}

	/******* BAIRRO *********/
	if (!isset($bairro) || (empty($bairro))) {
		die ('1'.\Zage\App\Util::encodeUrl('||'.htmlentities($tr->trans("O bairro deve ser preenchido!"))));
		$err	= 1;
	}elseif ((!empty($bairro)) && (strlen($bairro) > 60)) {
		die ('1'.\Zage\App\Util::encodeUrl('||'.htmlentities($tr->trans("O bairro não deve conter mais de 60 caracteres!"))));
		$err	= 1;
	}

	/******* NÚMERO *********/
	if ((!empty($numero)) && (strlen($numero) > 10)) {
		die ('1'.\Zage\App\Util::encodeUrl('||'.htmlentities($tr->trans("O número não deve conter mais de 10 caracteres!"))));
		$err	= 1;
	}

	/******* COMPLEMENTO *********/
	if ((!empty($complemento)) && (strlen($complemento) > 100)) {
		die ('1'.\Zage\App\Util::encodeUrl('||'.htmlentities($tr->trans("O complemento do endereço não deve conter mais de 100 caracteres!"))));
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
	die ('1'.\Zage\App\Util::encodeUrl('||'.htmlentities($tr->trans("O endereço deve ser preenchido com um CEP válido!"))));
	$err	= 1;
	
	$endCorreto = null; //Se não houver o codLogradouro o indicador deve ser nulo
}

#################################################################################
## Verificar se os usuário já existe e se já está ativo
#################################################################################
$oUsuario	= $em->getRepository('Entidades\ZgsegUsuario')->findOneBy(array('codigo' => $codUsuario));
if (!$oUsuario) 											\Zage\App\Erro::halt('Convite não está mais disponível, COD_ERRO: 06');
if (!$oUsuario) 											\Zage\App\Erro::halt('Convite não está mais disponível, COD_ERRO: 06');
if ($oUsuario->getCodStatus()->getCodigo() != "P")			\Zage\App\Erro::halt('Convite não está mais disponível, COD_ERRO: 07');

#################################################################################
## Verificar a associação do usuário a Organização
#################################################################################
$oUsuOrg		= $em->getRepository('Entidades\ZgsegUsuarioOrganizacao')->findOneBy(array('codigo' => $codAssoc));
if (!$oUsuOrg) 										\Zage\App\Erro::halt('Convite não está mais disponível, COD_ERRO: 08');
if ($oUsuOrg->getCodStatus()->getCodigo() != "P")	\Zage\App\Erro::halt('Convite não está mais disponível, COD_ERRO: 09');

#################################################################################
## Verificar a senha do convite
#################################################################################
$convite		= $em->getRepository('Entidades\ZgsegConvite')->findOneBy(array('codigo' => $codConvite));
if (!$convite) 								\Zage\App\Erro::halt('Convite não está mais disponível, COD_ERRO: 10');
if ($convite->getIndUtilizado() != 0)		\Zage\App\Erro::halt('Convite não está mais disponível, COD_ERRO: 12');


if ($err != null) {
	echo '1'.\Zage\App\Util::encodeUrl('||'.htmlentities($err));
	exit;
}

#################################################################################
## Salvar no banco
#################################################################################
try {

	#################################################################################
	## Resgatar as chaves estrangeiras
	#################################################################################
	$oOrg		= $em->getRepository('Entidades\ZgadmOrganizacao')->findOneBy(array('codigo' => $codOrganizacao));
	$oStatus	= $em->getRepository('Entidades\ZgsegUsuarioStatusTipo')->findOneBy(array('codigo' => 'A'));
	$oSexo		= $em->getRepository('Entidades\ZgsegSexoTipo')->findOneBy(array('codigo' => $sexo));
	$oLog		= $em->getRepository('Entidades\ZgadmLogradouro')->findOneBy(array('codigo' => $codLogradouro));
	$oUsuOrgSt	= $em->getRepository('Entidades\ZgsegUsuarioOrganizacaoStatus')->findOneBy(array('codigo' => 'A'));
	
	#################################################################################
	## Salvar as informações do usuário
	#################################################################################
	$oUsuario->setNome($nome);
	$oUsuario->setCpf($cpf);
	$oUsuario->setApelido($apelido);
	$oUsuario->setCodLogradouro($oLog);
	$oUsuario->setEndereco($descLogradouro);
	$oUsuario->setBairro($bairro);
	$oUsuario->setNumero($numero);
	$oUsuario->setCep($cep);
	$oUsuario->setComplemento($complemento);
	$oUsuario->setIndEndCorreto($endCorreto);
	$oUsuario->setCodStatus($oStatus);
	$oUsuario->setSexo($oSexo);
	
	$senhaCrip	= \Zage\App\Crypt::crypt($oUsuario->getUsuario(), $senha);
	$oUsuario->setSenha($senhaCrip);
	
	#################################################################################
	## Telefones / Contato
	#################################################################################
	$telefones		= $em->getRepository('Entidades\ZgsegUsuarioTelefone')->findBy(array('codUsuario' => $oUsuario->getCodigo()));
	
	/***** EXCLUSÃO *****/
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
	/***** CRIAÇÃO E ALTERAÇÃO *****/
	for ($i = 0; $i < sizeof($codTelefone); $i++) {
		$infoTel		= $em->getRepository('Entidades\ZgsegUsuarioTelefone')->findOneBy(array('codigo' => $codTelefone[$i] , 'codUsuario' => $oUsuario->getCodigo()));
		
			if (!$infoTel) {
				$infoTel		= new \Entidades\ZgsegUsuarioTelefone();
		}
		
		if ($infoTel->getCodTipoTelefone() !== $codTipoTel[$i] || $infoTel->getTelefone() !== $telefone[$i]) {
		
			$oTipoTel	= $em->getRepository('Entidades\ZgappTelefoneTipo')->findOneBy(array('codigo' => $codTipoTel[$i]));
		
			$infoTel->setCodUsuario($oUsuario);
			$infoTel->setCodTipoTelefone($oTipoTel);
			$infoTel->setTelefone($telefone[$i]);
		
			$em->persist($infoTel);
		}
	}
	
	#################################################################################
	## Mudar o status da associação
	#################################################################################
	$oUsuOrg->setCodStatus($oUsuOrgSt);
	
	#################################################################################
	## Mudar o status da associação com as formaturas
	#################################################################################
	$fmtUsuOrg		= \Zage\Fmt\Organizacao::listaFmtUsuOrg($codUsuario);
	
	for ($i = 0; $i < sizeof($fmtUsuOrg); $i++) {
		if ($fmtUsuOrg[$i]->getCodStatus()->getCodigo() == P){
			$fmtUsuOrg[$i]->setCodStatus($oUsuOrgSt);
			
			$em->persist($fmtUsuOrg[$i]);
		}
	}
	
	#################################################################################
	## Mudar o status do convite
	#################################################################################
	$convite->setIndUtilizado(1);
	//$convite->setDataUtilizacao(new \DateTime());
	
	$em->persist($oUsuario);
	$em->persist($oUsuOrg);
	$em->persist($convite);
	
	$em->flush();
	$em->clear();
	

} catch (\Exception $e) {
	$system->criaAviso(\Zage\App\Aviso\Tipo::ERRO,$e->getMessage());
	echo '1'.\Zage\App\Util::encodeUrl('||'.htmlentities($e->getMessage()));
	exit;
}


echo '0'.\Zage\App\Util::encodeUrl('||'. htmlentities('Seu usuário foi ativado!! Você vai ser redirecionado em 4 segundos'));
