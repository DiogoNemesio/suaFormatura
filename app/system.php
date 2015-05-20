<?php
if ( (!isset($system)) || (!is_object($system)) || $system->estaIniciado() !== true ) {
	
	/** 
	 * Instancia o sistema 
	 **/
	$system = \Zage\App\suaFormatura::getInstance ();
	
	/** 
	 * Inicializa o sistema 
	 **/
	$system->inicializaSistema();

}else{

	/**
	 * Inicia os recursos (DB, LOG)
	 */
	$system->iniciaRecursos();
}

/**
 * Checar os parâmetros obrigatórios
 */
if (!isset($system->config["data"]["datetimeFormat"])) {
	die ("Parâmetro datetimeFormat não configurado");
}
if (!isset($system->config["data"]["dateFormat"])) {
	die ("Parâmetro dateFormat não configurado");
}
if (!isset($system->config["data"]["maskDateFormat"])) {
	die ("Parâmetro maskDateFormat não configurado");
}


/**
 * Definir a Organização
 */
if ($system->getCodOrganizacao()) {
	$db->setOrganizacao($system->getCodOrganizacao());
}

$tr	= new \Symfony\Component\Translation\Translator('pt_BR', new \Symfony\Component\Translation\MessageSelector());
$tr->addLoader('php', new \Symfony\Component\Translation\Loader\PhpFileLoader());
$tr->addResource('php', MOD_PATH . '/App/lang/mensagens.en_US.php', 'en_US');