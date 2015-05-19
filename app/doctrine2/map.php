<?php
require_once("cli-config.php");

$driver 	= new \Doctrine\ORM\Mapping\Driver\DatabaseDriver($em->getConnection()->getSchemaManager());
$driver->setNamespace('Entidades\\');
$em->getConfiguration()->setMetadataDriverImpl($driver);

$cmf 		= new \Doctrine\ORM\Tools\DisconnectedClassMetadataFactory();
$cmf->setEntityManager($em);
$metadata = $cmf->getAllMetadata();

$etg 		= new \Doctrine\ORM\Tools\EntityGenerator;

$etg->setGenerateAnnotations(true);
$etg->setGenerateStubMethods(true);
$etg->setRegenerateEntityIfExists(true);
$etg->setUpdateEntityIfExists(true);

$result = $etg->generate($metadata,  __DIR__.'/../classes/');
echo "Resultado: ";
print_r($result);
echo "\n";
exit;

/*
$cme 		= new \Doctrine\ORM\Tools\Export\ClassMetadataExporter();
$exporter = $cme->getExporter('annotation', __DIR__.'/../classes/');
$exporter->setOverwriteExistingFiles(true);
$exporter->setEntityGenerator($etg);
$exporter->setMetadata($metadata);
$exporter->export();
*/