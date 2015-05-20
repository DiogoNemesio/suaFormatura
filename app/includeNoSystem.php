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
