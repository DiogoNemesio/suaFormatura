<?php

/**
 * Constantes do Sistema
 */

/**
 * Checa se a constante DOC_ROOT está definida
 */
if (! defined ( 'DOC_ROOT' )) {
	die ( 'Constante DOC_ROOT não definida !!! (constants)' );
}

/**
 * URL Raiz
 */
if (isset($_SERVER ['DOCUMENT_ROOT']) && $_SERVER ['DOCUMENT_ROOT']) {
	define ( 'PROTO', strtolower ( substr ( $_SERVER ["SERVER_PROTOCOL"], 0, strpos ( $_SERVER ["SERVER_PROTOCOL"], '/' ) ) ) . "://" );
	define ( 'ROOT_URL', PROTO . $_SERVER ["SERVER_NAME"] . '/' );
	$pos = stripos($_SERVER ['DOCUMENT_ROOT'], 'suaformatura');
	if ($pos === false) {
		define ( 'SITE_URL'	, PROTO . $_SERVER ["SERVER_NAME"] . '/site/sc/');
		define ( 'SITE_PATH', DOC_ROOT . '/site/sc/' );
	}else{
		define ( 'SITE_URL', PROTO . $_SERVER ["SERVER_NAME"] . '/site/sf/');
		define ( 'SITE_PATH', DOC_ROOT . '/site/sf/' );
	}
}else{
	define ( 'ROOT_URL', null );
}

/**
 * Caminho onde ficam as classes
 */
define ( 'CLASS_PATH', DOC_ROOT . '/classes/' );

/**
 * Caminho onde ficam as Entidades (Objetos do Doctrine)
 */
define ( 'ENTITY_PATH', CLASS_PATH . '/Entidades/' );
define ( 'PROXY_PATH', CLASS_PATH . '/Proxy/' );


/**
 * Caminho onde ficam instalados os módulos
 */
define ( 'MOD_PATH', DOC_ROOT . '/view/modulos/' );
define ( 'MOD_URL', ROOT_URL . '/modulos/' );

/**
 * Caminho onde ficam as packages
 */
define ( 'PKG_PATH', DOC_ROOT . '/view/packages/' );
define ( 'PKG_URL', ROOT_URL . 'packages/' );

/**
 * Caminho onde ficam os arquivos PHP executáveis
 */
define ( 'BIN_PATH', DOC_ROOT . '/view/bin/' );
define ( 'BIN_URL', ROOT_URL . 'bin/' );


/**
 * Caminho onde ficam os arquivos de configuração
 */
define ( 'CONFIG_PATH', DOC_ROOT . '/etc/' );

/**
 * Caminho onde ficam os arquivos html
 */
define ( 'HTML_PATH', DOC_ROOT . '/view/html/' );

/**
 * Caminho onde ficam os arquivos de log
 */
define ( 'LOG_PATH', DOC_ROOT . '/log/' );

/**
 * Caminho onde ficam as imagens
 */
define ( 'IMG_PATH', DOC_ROOT . '/view/imgs/' );
define ( 'IMG_URL', ROOT_URL . 'imgs/' );
define ( 'ICON_URL', IMG_URL . 'Icones/' );
define ( 'HTMLX_IMG_URL',PKG_URL . 'dhtmlx/%SKIN%/imgs/');

/**
 * Caminho onde ficam os CSS
 */
define ( 'CSS_PATH', DOC_ROOT . '/view/css/' );
define ( 'CSS_URL', ROOT_URL . 'css/' );
define ( 'SITE_CSS_PATH', DOC_ROOT . '/view/css/' );
define ( 'SITE_CSS_URL', ROOT_URL . 'css/' );

/**
 * Caminho onde ficam os Javascripts
 */
define ( 'JS_PATH', DOC_ROOT . '/view/js/' );
define ( 'JS_URL', ROOT_URL . 'js/' );

/**
 * Caminho do dataProcessor
 */
define ( 'DP_PATH', DOC_ROOT . '/view/dp/' );
define ( 'DP_URL', ROOT_URL . 'dp/' );

/**
 * Caminho dos XMLS
 */
define ( 'XML_PATH', DOC_ROOT . '/view/xml/' );
define ( 'XML_URL', ROOT_URL . 'xml/' );


/**
 * Caminho dos Jobs
 */
define ( 'JOB_PATH', DOC_ROOT . '/jobs/' );

/**
 * Caminho dos templates
 */
define ( 'TPL_PATH', DOC_ROOT . '/templates/' );


/**
 * Caminho dos executáveis do sistema
 */
define ( 'EXE_PATH', DOC_ROOT . '/bin/' );