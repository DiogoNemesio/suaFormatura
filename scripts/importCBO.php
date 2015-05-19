<?php

include_once('../app/include.php');

if ($_SERVER['DOCUMENT_ROOT']) {
	die('Script não pode ser excutado através de um Browser !!!');
}

$fGrupo		= "CBO2002_Grande_Grupo.txt";
$fSubGrupo	= "CBO2002_Familia.txt";
$fCBO		= "CBO2002_Ocupacao.txt";
$fSinonimo	= "CBO2002_Sinonimo.txt";


$grupos			= file($fGrupo);
$subGrupos		= file($fSubGrupo);
$cbos			= file($fCBO);
$sinonimos		= file($fSinonimo);

for ($i = 2; $i < sizeof($grupos); $i++ ) {
	list ($codigo,$descricao) 	= split("      ",$grupos[$i]);
	
	$codigo			= trim($codigo);
	$descricao		= trim($descricao);
	
	$grupo		= new \Entidades\ZgrhuFuncionarioCboGrupo();
	$grupo->setCodGrupo($codigo);
	$grupo->setDescricao($descricao);
	
	echo "Codigo: ".$grupo->getCodGrupo().", Descrição: ".$grupo->getDescricao()."\n";
	
	$em->persist($grupo);
}

$em->flush();
$em->clear();

for ($i = 2; $i < sizeof($subGrupos); $i++ ) {
	list ($codSubGrupo,$descricao) 	= split("   ",$subGrupos[$i]);
	
	$codSubGrupo	= trim($codSubGrupo);
	$descricao		= trim($descricao);
	
	$codGrupo		= substr($codSubGrupo,0,1);
	$grupo			= $em->getRepository('Entidades\ZgrhuFuncionarioCboGrupo')->findOneBy(array('codGrupo' => $codGrupo));
	
	if (!$grupo) die("Grupo não encontrado: ".$codGrupo);

	$subGrupo		= new \Entidades\ZgrhuFuncionarioCboSubgrupo();
	$subGrupo->setCodSubGrupo($codSubGrupo);
	$subGrupo->setCodGrupo($grupo);
	$subGrupo->setDescricao($descricao);

	echo "Codigo: ".$subGrupo->getCodSubGrupo().", Descrição: ".$subGrupo->getDescricao()."\n";

	$em->persist($subGrupo);
}

$em->flush();
$em->clear();


for ($i = 2; $i < sizeof($cbos); $i++ ) {
	$codCbo		= substr($cbos[$i],0,6);
	$descricao	= trim(substr($cbos[$i],6));

	$codSubGrupo	= substr($codCbo,0,4);
	$subGrupo		= $em->getRepository('Entidades\ZgrhuFuncionarioCboSubGrupo')->findOneBy(array('codSubgrupo' => $codSubGrupo));

	if (!$subGrupo) die("SubGrupo não encontrado: ".$codSubGrupo);

	$cbo		= new \Entidades\ZgrhuFuncionarioCbo();
	$cbo->setCodCbo($codCbo);
	$cbo->setCodSubgrupo($subGrupo);
	$cbo->setDescricao($descricao);
	$cbo->setIndSinonimo(0);

	echo "Codigo: ".$cbo->getCodCbo().", Descrição: ".$cbo->getDescricao()."\n";

	$em->persist($cbo);
}

$em->flush();
$em->clear();


for ($i = 2; $i < sizeof($sinonimos); $i++ ) {
	$codCbo		= substr($sinonimos[$i],0,6);
	$descricao	= trim(substr($sinonimos[$i],6));

	$codSubGrupo	= substr($codCbo,0,4);
	$subGrupo		= $em->getRepository('Entidades\ZgrhuFuncionarioCboSubGrupo')->findOneBy(array('codSubgrupo' => $codSubGrupo));

	if (!$subGrupo) die("SubGrupo não encontrado: ".$codSubGrupo);

	$cbo		= new \Entidades\ZgrhuFuncionarioCbo();
	$cbo->setCodCbo($codCbo);
	$cbo->setCodSubgrupo($subGrupo);
	$cbo->setDescricao($descricao);
	$cbo->setIndSinonimo(1);

	echo "Codigo: ".$cbo->getCodCbo().", Descrição: ".$cbo->getDescricao()."\n";

	$em->persist($cbo);
}

$em->flush();
$em->clear();

?>

