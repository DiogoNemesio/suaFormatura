<?php

use Doctrine\ORM\Tools\Setup;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\Mapping\Driver\AnnotationDriver;
use Doctrine\Common\Annotations\AnnotationReader;
use Doctrine\Common\Annotations\AnnotationRegistry;
use Doctrine\Common\Annotations\FileCacheReader;

require_once "vendor/autoload.php";
require_once "../root.php";
require_once "../include.php";

//require_once ENTITY_PATH . '/ZgappLoadHtml.php';

/** Variáveis de Ambiente **/
putenv("ORACLE_HOME=/u01/app/oracle/product/11.2.0/xe");
putenv("LD_LIBRARY_PATH=/usr/lib64/mpi/gcc/openmpi/lib64:/u01/app/oracle/product/11.2.0/xe/lib");

$isDevMode = true;

$classLoader = new \Doctrine\Common\ClassLoader('Entidades', ENTITY_PATH);
$classLoader->register();

$classLoader = new \Doctrine\Common\ClassLoader('Proxy', PROXY_PATH);
$classLoader->register();

$config = Setup::createConfiguration($isDevMode);

$reader = new FileCacheReader(
	new AnnotationReader(),
	ENTITY_PATH,
	$debug = true
);
//$reader->setDefaultAnnotationNamespace('Entidades\\');

$driver = new AnnotationDriver($reader, array(ENTITY_PATH));


// registering noop annotation autoloader - allow all annotations by default
//AnnotationRegistry::registerLoader('ComposerAutoloaderInit2995769f846b7bf61f619902245cea47::getLoader');
//AnnotationRegistry::registerLoader('\Zage\App\Loader::autoload');
$config->setMetadataDriverImpl($driver);

/*$config = new \Doctrine\ORM\Configuration();
$driverImpl 	= $config->newDefaultAnnotationDriver(ENTITY_PATH);
$config->setMetadataDriverImpl($driverImpl);
$config->setMetadataCacheImpl(new \Doctrine\Common\Cache\ArrayCache);
$config->setProxyDir(PROXY_PATH);
$config->setProxyNamespace('Proxy');
$config->setAutoGenerateProxyClasses(true);
*/

// database configuration parameters
$connectionOptions = array(
	'dbname' 	=> 'XE',
	'user' 		=> 'NCLEW',
	'password' 	=> 'NCLEW',
	'host' 		=> 'localhost',
	'driver' 	=> 'oci8',
	'charset' 	=> 'UTF-8',
);

$em = \Doctrine\ORM\EntityManager::create($connectionOptions, $config);

$driverDB 	= new \Doctrine\ORM\Mapping\Driver\DatabaseDriver($em->getConnection()->getSchemaManager());
$driverDB->setNamespace('Entidades\\');
//$em->getConfiguration()->setMetadataDriverImpl($driverDB);
//$em->getConfiguration()->setMetadataDriverImpl($driver);


$helperSet = new \Symfony\Component\Console\Helper\HelperSet(array(
	'db' => new \Doctrine\DBAL\Tools\Console\Helper\ConnectionHelper($em->getConnection()),
	'em' => new \Doctrine\ORM\Tools\Console\Helper\EntityManagerHelper($em)
));

