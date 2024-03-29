<?php
/**
 * incluir o arquivo de configuração
 */
include_once ('root.php');

/**
 * Definições de constantes
 */
include_once (DOC_ROOT . '/constants.php');

/**
 * AUTO_LOAD
 *
 * Include automático das classes
 */
include_once (CLASS_PATH . '/Zage/App/Loader.php');
include_once ('autoLoad.php');

/**
 * Inclusão de Packages externos
 */
include_once (PKG_PATH . '/mpdf60/mpdf.php'); // mPDF
include_once (CLASS_PATH . '/Chat-API/whatsprot.class.php'); // CHat-APi

/**
 * Checar se a configuração do Web Server está OK
 */
if ($_SERVER ['DOCUMENT_ROOT']) {
	include_once (DOC_ROOT . '/check.php');
}

/**
 * Gerenciamento de sessão
 */
if ($_SERVER ['DOCUMENT_ROOT']) {
	include_once ('session.php');
}

/**
 * Alterar o parâmetro do php para fazer buffer
 */
ini_set ( 'output_buffer', 65535 );

/**
 * Inicializar o sistema
 */
include_once (DOC_ROOT . '/system.php');


if ($_SERVER['DOCUMENT_ROOT']) {
	/** descarregar o buffer de saída **/
	//ob_end_flush();
	/** Checa se a autenticação foi feita **/
	include_once(BIN_PATH . 'auth.php');
}
	