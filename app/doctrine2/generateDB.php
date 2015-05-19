<?php

require_once("db-config.php");

$tool = new \Doctrine\ORM\Tools\SchemaTool($em);
$classes = array(
	$em->getClassMetadata('Entidades\ZgappLoadHtml')
);
$tool->createSchema($classes);

//$tool->createSchema();