<?php
// bootstrap.php
use Doctrine\ORM\Tools\Setup;
use Doctrine\ORM\EntityManager;

require_once "vendor/autoload.php";

// Create a simple "default" Doctrine ORM configuration for Annotations
$isDevMode = true;
$config = Setup::createAnnotationMetadataConfiguration(array("/srv/www/htdocs/DBApp/app/classes/Entidades/"), $isDevMode);

// database configuration parameters
$conn = array(
	'dbname' => 'suaFormatura',
	'user' => 'suaFormaturaUser',
	'password' => 'zageSenha',
	'host' => 'localhost',
	'driver' => 'mysqli',
	'charset' => 'UTF-8',
);

// obtaining the entity manager
$em = EntityManager::create($conn, $config);

