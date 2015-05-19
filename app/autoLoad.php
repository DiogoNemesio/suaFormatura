<?php
set_include_path ( get_include_path () . PATH_SEPARATOR . CLASS_PATH );
spl_autoload_register ( '\Zage\App\Loader::autoload' );

/** Registro do Twig **/
//Twig_Autoloader::register();

?>