<?php
if (!defined('DOC_ROOT')) {
	define('DOC_ROOT', dirname( __FILE__ ) . '/' );
}

if (!defined('SITE_ROOT')) {
	$info   = pathinfo(DOC_ROOT);
	define('SITE_ROOT',$info['dirname'].'/site/');
}
