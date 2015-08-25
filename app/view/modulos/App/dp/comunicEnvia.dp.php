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
## Variáveis globais
#################################################################################
global $em,$tr,$log,$system;

#################################################################################
## Resgata os parâmetros passados pelo formulario
#################################################################################
if (isset($_POST['assunto'])) 			$assunto		= \Zage\App\Util::antiInjection($_POST['assunto']);
if (isset($_POST['mensagem'])) 			$mensagem		= \Zage\App\Util::antiInjection($_POST['mensagem']);
if (isset($_POST['indEmail']))			$indEmail		= \Zage\App\Util::antiInjection($_POST['indEmail']);
if (isset($_POST['indWhatsApp']))		$indWhatsApp	= \Zage\App\Util::antiInjection($_POST['indWhatsApp']);

#################################################################################
## Limpar a variável de erro
#################################################################################
$err	= false;

#################################################################################
## Fazer validação dos campos
#################################################################################
$oOrg		= $em->getRepository('Entidades\ZgadmOrganizacao')->findOneBy(array('codigo' => $system->getCodOrganizacao()));

if ($oOrg->getCodTipo()->getCodigo() == FMT){
	$codTipo = 'F';
	$codTipoDesc = 'formandos';
}elseif ($oOrg->getCodTipo()->getCodigo() == CAS){
	$codTipo = 'N';
	$codTipoDesc = 'noivos';
}else{
	$codTipo = 'U';
	$codTipoDesc = 'usuários';
}

/******* Verificar se existe formandos ativos *********/
$log->debug($codTipo);
$usuarios		= \Zage\Seg\Usuario::listaUsuarioOrganizacaoAtivo($system->getCodOrganizacao(), $codTipo);
if (sizeof($usuarios) == 0)	{
	die ('1'.\Zage\App\Util::encodeUrl('||'.htmlentities($tr->trans("Não podemos enviar a mensagem, pois não existe ".$codTipoDesc." ativos!"))));
}

/******* Assunto *********/
if (!isset($assunto) || (empty($assunto))) {
	die ('1'.\Zage\App\Util::encodeUrl('||'.htmlentities($tr->trans("O assunto deve ser preenchido!"))));
}elseif ((!empty($assunto)) && (strlen($assunto) > 100)) {
	die ('1'.\Zage\App\Util::encodeUrl('||'.htmlentities($tr->trans("O assunto não deve conter mais de 100 caracteres!"))));
}

/******* Assunto *********/
if (!isset($mensagem) || (empty($mensagem))) {
	die ('1'.\Zage\App\Util::encodeUrl('||'.htmlentities($tr->trans("O conteúdo da mensagem deve ser preenchido!"))));
}elseif ((!empty($mensagem)) && (strlen($mensagem) > 500)) {
	die ('1'.\Zage\App\Util::encodeUrl('||'.htmlentities($tr->trans("A mensagem não deve conter mais de 500 caracteres!"))));
}

/******* IND EMAIL *********/
if (isset($indEmail) && (!empty($indEmail))) {
	$indEmail	= 1;
}else{
	$indEmail	= 0;
}

/******* IND EMAIL *********/
if (isset($indWhatsApp) && (!empty($indWhatsApp))) {
	$indWhatsApp	= 1;
}else{
	$indWhatsApp	= 0;
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
	
	#################################################################################
	## Gerar a notificação
	#################################################################################
	$oRemetente		= $em->getReference('\Entidades\ZgsegUsuario',$system->getCodUsuario());
	$notificacao	= new \Zage\App\Notificacao(\Zage\App\Notificacao::TIPO_MENSAGEM_TEXTO, \Zage\App\Notificacao::TIPO_DEST_USUARIO);
	$notificacao->setAssunto($assunto);
	$notificacao->setCodRemetente($oRemetente);
	
	for ($i = 0; $i < sizeof($usuarios); $i++) {
		$notificacao->associaUsuario($usuarios[$i]->getCodUsuario()->getCodigo());
	}
	
	$notificacao->setMensagem($mensagem);
	$notificacao->enviaSistema();
	if ($indEmail == 1){
		$notificacao->enviaEmail();
	}
	if ($indWhatsApp == 1){
		$notificacao->enviaWa();
	}
	$notificacao->salva();
	
	$em->flush();
	$em->clear();
	$em->getConnection()->commit();

} catch (\Exception $e) {
	$em->getConnection()->rollback();
	die ('1'.\Zage\App\Util::encodeUrl('||'.htmlentities($e->getMessage())));

	exit;
}

echo '0'.\Zage\App\Util::encodeUrl('|');