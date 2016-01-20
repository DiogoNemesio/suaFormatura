<?php
global $log,$system,$db,$em,$_user,$_org,$_mod;

#################################################################################
## Includes
#################################################################################
if (defined('DOC_ROOT')) {
	include_once(DOC_ROOT . 'includeNoAuth.php');
}else{
	include_once('../includeNoAuth.php');
}

#################################################################################
## Verificar se o usuário e senha foram passados através do form
#################################################################################
if ((isset($_POST['zgUsuario'])) && (isset($_POST['zgSenha']))) {
	$_usuario		= \Zage\App\Util::antiInjection($_POST['zgUsuario']);
	$_senha			= \Zage\App\Util::antiInjection($_POST['zgSenha']);
	
	if (isset($_org) && ($_org instanceof \Entidades\ZgadmOrganizacao)) {
		$_senhaCrip		= \Zage\App\Crypt::crypt($_usuario, $_senha);
	}else{
		die("Organização não definida !!!");
	}
	
}else{
	$_usuario		= '';
	$_senha			= '';
}


if ((isset($_org) && (isset($_SESSION['_codOrg'])) && ($_org instanceof \Entidades\ZgadmOrganizacao) && ($_org->getCodigo() != $_SESSION['_codOrg']) ) ) {
	$system->desautentica();
}

#################################################################################
## Limpando a variável da mensagem
#################################################################################
$mensagem		= '';

#################################################################################
## Verifica se o usuário já está conectado
#################################################################################
if (!$system->estaAutenticado()) {

	if (($_usuario) && ($_senha) && ($_org)) {

		$valUsuario	= new \Zage\Seg\Auth\validaUsuario();
		$valSenha	= new \Zage\Seg\Auth\validaSenha();

		if (!$valUsuario->isValid($_usuario)) {
    		$r			= \Zend\Authentication\Result::FAILURE_CREDENTIAL_INVALID;
    		$result		= new \Zend\Authentication\Result($r,$_usuario,$valUsuario->getMessages());
		}elseif (!$valSenha->isValid($_senha)) {
    		$r			= \Zend\Authentication\Result::FAILURE_CREDENTIAL_INVALID;
    		$result		= new \Zend\Authentication\Result($r,$_usuario,$valSenha->getMessages());
		}else{
			$authAdap	= new \Zage\Seg\Auth($_usuario,$_senhaCrip,$_org->getCodigo());
			$result		= $authAdap->authenticate();
		}
		
		if (!$result->isValid()) {

			$m			= $result->getMessages();
			if (isset($m[0])) {
				$mensagem	= $m[0];
			}else{
				$mensagem	= "Usuário / senha inconsistentes !!!";
			}
			
			$log->err('0x00000001: Tentativa de acesso do usuário "'.$_usuario.'" sem sucesso !! ');
			
			#################################################################################
			## Não chamar a tela de login se a variável $_doNotLogin estiver setada para 1
			## Será útil quando as telas que rodam em background (notificacao.php e mensagem.php)
			## forem chamadas e o tempo da sessão estiver expirado
			#################################################################################
			if (isset($_doNotLogin) && ($_doNotLogin == 1)) exit;
			
			include_once(MOD_PATH . '/Seg/php/login.php');
			exit;
		} else {

			/** Autenticação OK **/
			$log->info('1x00000001: Usuário "'.$_usuario.'" autenticado com sucesso !!! ');
			
			try {
				
				/** Resgata os dados do usuário **/
				$_user 		= $em->getRepository('Entidades\ZgsegUsuario')->findOneBy(array ('usuario' => $_usuario));
				$system->setCodUsuario($_user->getCodigo());
				$system->setAutenticado();
				$system->setDataUltAcesso($_user->getDataUltAcesso());
				$_SESSION['_codOrg']		= $_org->getCodigo();
				
				/** Gerar o histório de acesso **/
				\Zage\Adm\Organizacao::geraHistoricoAcesso($_org->getCodigo(),$_user->getCodigo());
				
			} catch (\Exception $e) {
				\Zage\App\Erro::halt($e->getMessage(),__FILE__,__LINE__);
			}
		}
	}else{
		include_once(MOD_PATH . '/Seg/php/login.php');
		exit;
	}
}else{
	/** Resgata os dados do usuário **/
	$_user 		= $em->getRepository('Entidades\ZgsegUsuario')->findOneBy(array('codigo' => $system->getCodUsuario()));
	
}

#################################################################################
## Define o usuário que está logado no banco
#################################################################################
$db->setLoggedUser($_user->getCodigo());

#################################################################################
## Resgata os parâmetros passados pelo formulario de pesquisa do módulo
#################################################################################
//if (isset($_POST['modApelido']))	{
//	$_modApelido	= \Zage\App\Util::antiInjection($_POST['modApelido']);
//	$_modApelido	= substr($_modApelido,0,3);
//
//	/** Verifica se o módulo existe **/
//	$_mod	= $em->getRepository('Entidades\ZgappModulo')->findOneBy(array('apelido' => $_modApelido));
//
//	if (($_mod) && $system->temPermissaoNoModulo($_mod->getCodigo()) == true) {
//		$_codModulo_	= $_mod->getCodigo();
//	}
//}elseif (is_object($_user) && $_user->getUltModuloAcesso()) {
//	$_mod	= $em->getRepository('Entidades\ZgappModulo')->findOneBy(array('codigo' => $_user->getUltModuloAcesso()->getCodigo()));
//}

?>