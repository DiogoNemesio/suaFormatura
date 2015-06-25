<?php
#################################################################################
## Includes
#################################################################################
if (defined('DOC_ROOT')) {
	include_once(DOC_ROOT . 'includeNoAuth.php');
}else{
	include_once('../includeNoAuth.php');
}

#################################################################################
## Resgata as variáveis postadas
#################################################################################
if (isset($_GET['q']))					$q			    = \Zage\App\Util::antiInjection($_GET["q"]);
if (isset($_GET['codCurso']))			$codCurso		= \Zage\App\Util::antiInjection($_GET["codCurso"]);

if (isset($codCurso)) {
	$cursos		= $em->getRepository('Entidades\ZgfmtCurso')->findBy(array('codigo' => $codCurso));
}else{
	$cursos		= \Zage\Fmt\Curso::busca($q);
}


$array		= array();
//$numItens	= \Zage\Adm\Parametro::getValor('APP_BS_TA_ITENS');

for ($i = 0; $i < sizeof($cursos); $i++) {
	$array[$i]["id"]		= $cursos[$i]->getCodigo();
	$array[$i]["text"]		= '(' . $cursos[$i]->getCodOcde() . ') '.$cursos[$i]->getNome();
}

echo json_encode($array);