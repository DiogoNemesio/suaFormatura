<?php
################################################################################
# Includes
################################################################################
if (defined ( 'DOC_ROOT' )) {
	include_once (DOC_ROOT . 'include.php');
} else {
	include_once ('../include.php');
}

################################################################################
# Resgata a variável ID que está criptografada
################################################################################
if (isset ( $_GET ['id'] )) {
	$id = \Zage\App\Util::antiInjection ( $_GET ["id"] );
} elseif (isset ( $_POST ['id'] )) {
	$id = \Zage\App\Util::antiInjection ( $_POST ["id"] );
} elseif (isset ( $id )) {
	$id = \Zage\App\Util::antiInjection ( $id );
} else {
	\Zage\App\Erro::halt ( 'Falta de Parâmetros' );
}

################################################################################
# Descompacta o ID
################################################################################
\Zage\App\Util::descompactaId ( $id );

#################################################################################
## Verifica se os parâmetros foram passados
#################################################################################
if (!isset($codUsuario)) exit;
if (!isset($codNotificacao)) exit;
if (!isset($codTemplate)) exit;

#################################################################################
## Verifica o usuário
#################################################################################
if ($codUsuario != $system->getCodUsuario()) exit;

#################################################################################
## Verifica se a notificação existe
#################################################################################
$notificacao		= $em->getRepository('\Entidades\ZgappNotificacao')->findOneBy(array('codigo' => $codNotificacao));
if (!$notificacao)	throw new \Exception('Notificação não encontrada !!!');

#################################################################################
## Resgata as informações do template
#################################################################################
$template		= $em->getRepository('\Entidades\ZgappNotificacaoTemplate')->findOneBy(array('codigo' => $codTemplate));
if (!$template)	throw new \Exception('Template não encontrado !!!');

#################################################################################
## Verificar se o template existe
#################################################################################
if (!file_exists(TPL_PATH . '/' . $template->getCaminho())) throw new \Exception('Template não encontrado !!!');
	
#################################################################################
## Carregando o template html
#################################################################################
$tpl	= new \Zage\App\Template();
$tpl->load(TPL_PATH . '/' . $template->getCaminho());

#################################################################################
## Atribui as variáveis do template
#################################################################################
$variaveis		= $em->getRepository('\Entidades\ZgappNotificacaoVariavel')->findBy(array('codNotificacao' => $codNotificacao));
for ($i = 0; $i < sizeof($variaveis); $i++) {
	$tpl->set($variaveis[$i]->getVariavel(), $variaveis[$i]->getValor());
}
	
#################################################################################
## Por fim exibir a página HTML
#################################################################################
$tpl->show();


