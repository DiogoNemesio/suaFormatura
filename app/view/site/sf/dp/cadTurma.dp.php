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
global $em,$system,$tr,$log;

#################################################################################
## Resgata os parâmetros passados pelo formulario
#################################################################################
if (isset($_POST['termo']))				$termo				= \Zage\App\Util::antiInjection($_POST['termo']);
if (isset($_POST['ident']))				$ident				= \Zage\App\Util::antiInjection($_POST['ident']);
if (isset($_POST['nome'])) 				$nome				= \Zage\App\Util::antiInjection($_POST['nome']);
if (isset($_POST['instituicao']))		$instituicao		= \Zage\App\Util::antiInjection($_POST['instituicao']);
if (isset($_POST['curso']))				$curso				= \Zage\App\Util::antiInjection($_POST['curso']);
if (isset($_POST['cidade']))			$cidade				= \Zage\App\Util::antiInjection($_POST['cidade']);
if (isset($_POST['dataConclusao']))		$dataConclusao		= \Zage\App\Util::antiInjection($_POST['dataConclusao']);

if (isset($_POST['indUsuario']))		$indUsuario			= \Zage\App\Util::antiInjection($_POST['indUsuario']);
if (isset($_POST['nomeUsu']))			$nomeUsu			= \Zage\App\Util::antiInjection($_POST['nomeUsu']);
if (isset($_POST['email1']))			$email1				= \Zage\App\Util::antiInjection($_POST['email1']);
if (isset($_POST['email2']))			$email2				= \Zage\App\Util::antiInjection($_POST['email2']);
if (isset($_POST['cpf']))				$cpf				= \Zage\App\Util::antiInjection($_POST['cpf']);

$codOrganizacao = null;
#################################################################################
## Limpar a variável de erro
#################################################################################
$err	= false;

#################################################################################
## Fazer validação dos campos
#################################################################################
/******* TERMO *********/
if (isset($termo) && (!empty($termo))) {
	$termo	= 1;
}else{
	die ('1'.\Zage\App\Util::encodeUrl('||'.htmlentities($tr->trans("É necessário que você aceite o termo de utilização!"))));
}

/******* IDENTIFICAÇÃO *********/
if (!isset($ident) || (empty($ident))) {
	die ('1'.\Zage\App\Util::encodeUrl('||'.htmlentities($tr->trans("Informe uma identificação para sua turma!"))));
}

if ((!empty($ident)) && (strlen($ident) > 100)) {
	die ('1'.\Zage\App\Util::encodeUrl('||'.htmlentities($tr->trans("A identificação da formatura não deve conter mais de 60 caracteres!"))));
}

$oOrganizacao	= $em->getRepository('Entidades\ZgadmOrganizacao')->findOneBy(array('identificacao' => $ident));

if($oOrganizacao != null && ($oOrganizacao->getCodigo() != $codOrganizacao)){
	die ('1'.\Zage\App\Util::encodeUrl('||'.htmlentities($tr->trans("Já existe uma formatura com essa identificação, por favor informe outro nome."))));
}

/******* NOME *********/
if (!isset($nome) || (empty($nome))) {
	die ('1'.\Zage\App\Util::encodeUrl('||'.htmlentities($tr->trans("Informe um nome para sua formatura!"))));
}elseif ((!empty($nome)) && (strlen($nome) > 100)) {
	die ('1'.\Zage\App\Util::encodeUrl('||'.htmlentities($tr->trans("O nome da sua formatura não deve conter mais de 100 caracteres!"))));
}

/******* INSTITUIÇÃO *********/
if (!isset($instituicao) || (empty($instituicao))) {
	die ('1'.\Zage\App\Util::encodeUrl('||'.htmlentities($tr->trans("Selecione a sua instituição de ensino!"))));
}

/******* CURSO *********/
if (!isset($curso) || (empty($curso))) {
	die ('1'.\Zage\App\Util::encodeUrl('||'.htmlentities($tr->trans("Selecione o seu curso!"))));
}

/******* CIDADE *********/
if (!isset($cidade) || (empty($cidade))) {
	die ('1'.\Zage\App\Util::encodeUrl('||'.htmlentities($tr->trans("Informe a cidade onde será realizado a formatura!"))));
}

/******* DATA DE CONCLUSAO *********/
if (!isset($dataConclusao) || (empty($dataConclusao))) {
	die ('1'.\Zage\App\Util::encodeUrl('||'.htmlentities($tr->trans("Informe a data prevista para a conclusão do curso!"))));
}

/******* NOME USUARIO *********/
if (!isset($nomeUsu) || (empty($nomeUsu))) {
	die ('1'.\Zage\App\Util::encodeUrl('||'.htmlentities($tr->trans("Informe um nome para sua formatura!"))));
}elseif ((!empty($nomeUsu)) && (strlen($nomeUsu) > 100)) {
	die ('1'.\Zage\App\Util::encodeUrl('||'.htmlentities($tr->trans("O nome da sua formatura não deve conter mais de 100 caracteres!"))));
}elseif ((!empty($nomeUsu)) && (strlen($nomeUsu) < 5)){
	die ('1'.\Zage\App\Util::encodeUrl('||'.htmlentities($tr->trans("Seu nome está muito perqueno, por favor informe seu nome completo!"))));
}

/******* EMAIL 1 *********/
if (!isset($email1) || (empty($email1))) {
	die ('1'.\Zage\App\Util::encodeUrl('||'.htmlentities($tr->trans("Informe seu email corretamente!"))));
}elseif (strlen($email1) > 200){
	die ('1'.\Zage\App\Util::encodeUrl('||'.htmlentities($tr->trans("O seu email não deve conter mais de 200 caracteres!"))));
}elseif(\Zage\App\Util::validarEMail($email1) == false){
	die ('1'.\Zage\App\Util::encodeUrl('||'.htmlentities($tr->trans("O formato do seu email está inválido. Por favor informe corretamente!"))));
}else{
	$oUsuario = $em->getRepository('Entidades\ZgsegUsuario')->findOneBy(array('usuario' => $email1));
		
	if($oUsuario != null && $indUsuario != 1){
		die ('1'.\Zage\App\Util::encodeUrl('||'.htmlentities($tr->trans("Já existe um usuário cadastrado com este EMAIL! Por favor, verifique os dados informados."))));
	}
}

/******* EMAIL 2 *********/
if (!isset($email2) || (empty($email2))) {
	die ('1'.\Zage\App\Util::encodeUrl('||'.htmlentities($tr->trans("Informe a confirmação do seu email corretamente!"))));
}elseif (strlen($email1) > 200){
	die ('1'.\Zage\App\Util::encodeUrl('||'.htmlentities($tr->trans("A confirmação do seu email não deve conter mais de 200 caracteres!"))));
}elseif($email1 != $email2){
	die ('1'.\Zage\App\Util::encodeUrl('||'.htmlentities($tr->trans("A confirmação do email está diferente do email preenchid!."))));
}

/******* CPF *********/
$valCgc			= new \Zage\App\Validador\Cpf();
if (!isset($cpf) || (empty($cpf))) {
	return $tr->trans('Informe o seu CPF!');
}else{
	if ($valCgc->isValid($cpf) == false) {
		return $tr->trans('O seu CPF está inválido!');
	}else{
		$oUsuario	= $em->getRepository('Entidades\ZgsegUsuario')->findOneBy(array('cpf' => $cpf));
		if($oUsuario != null && $indUsuario != 1){
			return $tr->trans('Já existe um usuário cadastrado com este CPF! Por favor,  verifique os dados informados.');
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
	## ORGANIZAÇÃO
	#################################################################################
 	$oOrganizacao	= new \Entidades\ZgadmOrganizacao();
 	
 	$oTipoOrganizacao	= $em->getRepository('Entidades\ZgadmOrganizacaoTipo')->findOneBy(array('codigo' => FMT));
 	$oCodStatus			= $em->getRepository('Entidades\ZgadmOrganizacaoStatusTipo')->findOneBy(array('codigo' => A));
 	
 	$oOrganizacao->setIdentificacao($ident);
 	$oOrganizacao->setNome($nome);
 	$oOrganizacao->setCodTipo($oTipoOrganizacao);
 	$oOrganizacao->setCodStatus($oCodStatus);
 	$oOrganizacao->setDataCadastro(new \DateTime("now"));
 	
 	$em->persist($oOrganizacao);
 	
 	#################################################################################
 	## ORGANIZAÇÃO FORMATURA
 	#################################################################################
 	$oOrgFmt			= new \Entidades\ZgfmtOrganizacaoFormatura();
 	
 	$oInstituicao		= $em->getRepository('Entidades\ZgfmtInstituicao')->findOneBy(array('codigo' => $instituicao));
 	$oCurso				= $em->getRepository('Entidades\ZgfmtCurso')->findOneBy(array('codigo' => $curso));
 	$oCidade			= $em->getRepository('Entidades\ZgadmCidade')->findOneBy(array('codigo' => $cidade));
 	
 	if (!empty($dataConclusao)) {
 		$dtCon		= DateTime::createFromFormat($system->config["data"]["dateFormat"], $dataConclusao);
 	}else{
 		$dtCon		= null;
 	}
 	
 	$oOrgFmt->setCodOrganizacao($oOrganizacao);
 	$oOrgFmt->setCodInstituicao($oInstituicao);
 	$oOrgFmt->setCodCurso($oCurso);
 	$oOrgFmt->setCodCidade($oCidade);
 	$oOrgFmt->setDataConclusao($dtCon);
 	
 	$em->persist($oOrgFmt);
	
	#################################################################################
	## Contrato
	#################################################################################
	$oStatusContrato	= $em->getReference('\Entidades\ZgadmContratoStatusTipo','A');
	$oPlano				= $em->getReference('\Entidades\ZgadmPlano','1');
	
	$oContrato			= new \Entidades\ZgadmContrato();
	
	$oContrato->setDataCadastro(new \DateTime());
	$oContrato->setDataInicio(new \DateTime());
	$oContrato->setCodStatus($oStatusContrato);
	$oContrato->setCodOrganizacao($oOrganizacao);
	$oContrato->setCodPlano($oPlano);
	$oContrato->setPctDesconto(0);
	$oContrato->setValorDesconto(0);
	
	$em->persist($oContrato);
	
	#################################################################################
	## USUÁRIO
	#################################################################################
	if(!$oUsuario){
		$oUsuario		= new \Entidades\ZgsegUsuario();
		$oStatus			= $em->getRepository('Entidades\ZgsegUsuarioStatusTipo')->findOneBy(array('codigo' => 'P'));
		$novoUsuario 		= true;
		$enviarEmail 		= true;
		
		$oUsuario->setUsuario($email1);
		$oUsuario->setCodStatus($oStatus);
		$oUsuario->setNome($nomeUsu);
		$oUsuario->setCpf($cpf);
		
		$em->persist($oUsuario);
	}
	
	#################################################################################
	## USUARIO - ORG
	#################################################################################
	$oUsuarioOrg		= new \Entidades\ZgsegUsuarioOrganizacao();
	$oUsuarioOrgStatus  = $em->getRepository('Entidades\ZgsegUsuarioOrganizacaoStatus')->findOneBy(array('codigo' => 'P'));
	$oPerfil			= $em->getRepository('Entidades\ZgsegPerfil')->findOneBy(array('codigo' => 5));// Perfil de formando comissão
	
	$oUsuarioOrg->setCodUsuario($oUsuario);
	$oUsuarioOrg->setCodOrganizacao($oOrganizacao);
	$oUsuarioOrg->setCodPerfil($oPerfil);
	$oUsuarioOrg->setCodStatus($oUsuarioOrgStatus);
	
	$em->persist($oUsuarioOrg);
	
	#################################################################################
	## Cria o convite
	#################################################################################
	$oConviteStatus = $em->getRepository('Entidades\ZgsegConviteStatus')->findOneBy(array('codigo' => A));
	$convite		= new \Zage\Seg\Convite();
	$log->debug($oOrganizacao->getCodigo());
	$convite->setCodOrganizacaoOrigem($oOrganizacao);
	$convite->setCodUsuarioDestino($oUsuario);
	$convite->setCodStatus($oConviteStatus);
	$convite->salvar();

	#################################################################################
 	## Salvar as informações
 	#################################################################################
 	
 	try {
 		$em->flush();
 		$em->clear();
 	} catch (Exception $e) {
 		$log->debug("Erro ao salvar o Organização:". $e->getTraceAsString());
 		throw new \Exception("Erro ao salvar a Organização. Uma mensagem de depuração foi salva em log, entre em contato com os administradores do sistema !!!");
 	}

 	
 	#################################################################################
 	## Enviar notificação
 	#################################################################################
 	$cid 		= \Zage\App\Util::encodeUrl('_cdu01='.$oUsuarioOrg->getCodigo().'&_cdu02='.$oUsuario->getCodigo().'&_cdu03='.$oOrganizacao->getCodigo().'&_cdu04='.$convite->_getCodigo().'&_cdsenha='.$convite->getSenha());
 	if ($oUsuario->getCodStatus()->getCodigo() == P) {
 		$assunto			= "Confirmação de cadastro";
 		$nome				= $oUsuario->getNome();
 		$texto = 'Obrigado por utilizaro nosso portal! Para concluir o seu cadastro e acessar a formatura <b>'.$oOrganizacao->getNome().'</b> é necessário confimar seus dados.';
 		$confirmUrl			= ROOT_URL . "/Seg/u01.php?cid=".$cid;
 	}else{
 		$assunto			= "Associação a uma nova formatura";
 		$template			= 'USUARIO_CADASTRO';
 		$confirmUrl			= ROOT_URL . "/Seg/u02.php?cid=".$cid;
 		$texto = 'Identificamos que você já é usuário do portal SUAFORMATURA. Confirme seu cadastro para acessar tudo sobre sua nova formatura <b>'.$oOrganizacao->getNome().'</b>.';
 	}
 	
 	$template		= $em->getRepository('\Entidades\ZgappNotificacaoTemplate')->findOneBy(array('template' => 'USUARIO_CADASTRO'));
 	$notificacao	= new \Zage\App\Notificacao(\Zage\App\Notificacao::TIPO_MENSAGEM_TEMPLATE, \Zage\App\Notificacao::TIPO_DEST_USUARIO);
 	$notificacao->setAssunto($assunto);
 	
 	$notificacao->associaUsuario($oUsuario->getCodigo());
 	
 	$notificacao->enviaEmail();
 	$notificacao->setCodTemplate($template);
 	$notificacao->adicionaVariavel('ID', $id);
 	$notificacao->adicionaVariavel("CONFIRM_URL", $confirmUrl);
 	$notificacao->adicionaVariavel("ASSUNTO", $assunto);
 	$notificacao->adicionaVariavel("NOME", $nome);
 	$notificacao->adicionaVariavel("TEXTO", $texto);
 	$notificacao->salva();
 	
 	$em->flush();
 	$em->clear();
 	
} catch (\Exception $e) {
 
 	echo '1'.\Zage\App\Util::encodeUrl('||'.htmlentities($e->getMessage()));
 	exit;
}
 
//$system->criaAviso(\Zage\App\Aviso\Tipo::INFO,"Informações salvas com sucesso");
echo '0'.\Zage\App\Util::encodeUrl('|'.$oUsuarioOrg->getCodigo());