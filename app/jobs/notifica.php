<?php
#################################################################################
## Includes
#################################################################################
if (defined('DOC_ROOT')) {
	include_once(DOC_ROOT . 'includeNoAuth.php');
}else{
	define('DOC_ROOT', realpath(dirname( __FILE__ ) . '/../') . "/" );
	include_once(DOC_ROOT . 'includeNoAuth.php');
}

#################################################################################
## Variáveis globais
#################################################################################
global $em,$system,$tr,$log,$db;


$chip 	= new \Zage\Wap\Chip();
$chip->_setCodigo(1);
$chip->conectar();

exit;



$daniel			= $em->getRepository('\Entidades\ZgsegUsuario')->findOneBy(array('codigo' => 1));
$template		= $em->getRepository('\Entidades\ZgappNotificacaoTemplate')->findOneBy(array('template' => 'ASSINATURA_VENCIDA'));
$notificacao	= new \Zage\App\Notificacao(\Zage\App\Notificacao::TIPO_MENSAGEM_TEMPLATE, \Zage\App\Notificacao::TIPO_DEST_USUARIO);
$notificacao->setAssunto("Assinatura vencida (".date('d/m/Y h:i:s').")");
$notificacao->setCodUsuario($daniel);
$notificacao->associaUsuario(1);
//$notificacao->enviaWa();
$notificacao->enviaEmail();
$notificacao->setCodTemplate($template);
$notificacao->adicionaVariavel("DATA_VENCIMENTO", "13/06/2015");
$notificacao->adicionaVariavel("VALOR_ASSINATURA", "R$ 1.000,00");
$notificacao->salva();

/*$notificacao	= new \Zage\App\Notificacao(\Zage\App\Notificacao::TIPO_MENSAGEM_TEXTO, \Zage\App\Notificacao::TIPO_DEST_ORGANIZACAO);
$notificacao->setAssunto("Teste de notificação");
$notificacao->setMensagem("Teste de notificação de organização");
$notificacao->associaOrganizacao(1);
$notificacao->enviaWa();
$notificacao->salva();
$notificacao->adicionaVariavel("NOME", "Daniel");
*/