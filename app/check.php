<?php

/** Checando se a constante que define a localização raiz da aplicação foi definida **/
if (!defined('DOC_ROOT')) {
	die('Constante DOC_ROOT não definida !!!');
}

if (!extension_loaded('mcrypt')) {
	die('Extensão mcrypt não instalada !!!');
}

?>