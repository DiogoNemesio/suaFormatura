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


#################################################################################
## Solicitar o código SMS
#################################################################################
/*$chip		= new \Zage\Wap\Chip();
$chip->_setCodigo(3);
$return		= $chip->solicitaCodigoPorSms();
print_r($return);
exit;

$chip 	= new \Zage\Wap\Chip();
$chip->_setCodigo(1);
$chip->conectar();

exit;
*/


$daniel			= $em->getReference('\Entidades\ZgsegUsuario',1);
$diogo			= $em->getRepository('\Entidades\ZgsegUsuario')->findOneBy(array('codigo' => 2));
$template		= $em->getRepository('\Entidades\ZgappNotificacaoTemplate')->findOneBy(array('template' => 'ASSINATURA_VENCIDA'));
$notificacao	= new \Zage\App\Notificacao(\Zage\App\Notificacao::TIPO_MENSAGEM_TEMPLATE, \Zage\App\Notificacao::TIPO_DEST_USUARIO);
$notificacao->setAssunto("Teste de notificação ");
$notificacao->setCodRemetente($daniel);
$notificacao->associaUsuario(1);
$notificacao->enviaEmail();
$notificacao->enviaSistema();
//$notificacao->setEmail("daniel.cassela@usinacaete.com");
$notificacao->setCodTemplate($template);
//$notificacao->anexarArquivo("cpd40192.pdf", \Zage\App\Util::getConteudoArquivo("/home/cassela/cpd40192.pdf"));

//$notificacao->enviaEmail();
//$notificacao->setCodTemplate($template);
//$notificacao->adicionaVariavel("DATA_VENCIMENTO", "13/06/2015");
//$notificacao->adicionaVariavel("VALOR_ASSINATURA", "R$ 1.000,00");
$notificacao->salva();

/*$notificacao	= new \Zage\App\Notificacao(\Zage\App\Notificacao::TIPO_MENSAGEM_TEXTO, \Zage\App\Notificacao::TIPO_DEST_ORGANIZACAO);
$notificacao->setAssunto("Teste de notificação");
$notificacao->setMensagem("Teste de notificação de organização");
$notificacao->associaOrganizacao(1);
$notificacao->enviaWa();
$notificacao->salva();
$notificacao->adicionaVariavel("NOME", "Daniel");
*/



$diogo			= $em->getReference('\Entidades\ZgsegUsuario',2);
$notificacao	= new \Zage\App\Notificacao(\Zage\App\Notificacao::TIPO_MENSAGEM_HTML, \Zage\App\Notificacao::TIPO_DEST_USUARIO);
$notificacao->setAssunto("Teste de notificação com anexo");
$notificacao->setCodRemetente($diogo);
$notificacao->associaUsuario(1);
$notificacao->enviaSistema();
$notificacao->anexarArquivo("cpd40192.pdf", \Zage\App\Util::getConteudoArquivo("/home/cassela/cpd40192.pdf"));
$notificacao->setMensagem("Segue em anexo o PDF para visualização");
$notificacao->salva();

