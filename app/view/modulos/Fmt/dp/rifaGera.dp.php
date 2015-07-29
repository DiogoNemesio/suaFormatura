<?php
#################################################################################
## Includes
#################################################################################
if (defined('DOC_ROOT')) {
	include_once(DOC_ROOT . 'include.php');
}else{
 	include_once('../include.php');
}

#################################################################################
## Variáveis globais
#################################################################################
global $em,$system,$log,$tr;

#################################################################################
## Resgata os parâmetros passados pelo formulario
#################################################################################
if (isset($_POST['codRifa'])) 			$codRifa		= \Zage\App\Util::antiInjection($_POST['codRifa']);

#################################################################################
## Limpar a variável de erro
#################################################################################
$err	= false;

#################################################################################
## Fazer validação dos campos
#################################################################################
if (!isset($codRifa) || (empty($codRifa))) {
	$err	= $tr->trans("Falta de parâmetros!");
}else{
	$oRifa	= $em->getRepository('\Entidades\ZgfmtRifa')->findOneBy(array('codigo' => $codRifa));
	if (!$oRifa) {
		$system->criaAviso(\Zage\App\Aviso\Tipo::ERRO,$tr->trans("Rifa não encontrada!"));
		$err	= 1;
	}
}

#################################################################################
## Resgatar os formandos ativos dessa organização
#################################################################################
$formandos		= \Zage\Fmt\Formatura::listaFormandosAtivos($system->getCodOrganizacao());
if (sizeof($formandos) == 0)	{
	$system->criaAviso(\Zage\App\Aviso\Tipo::ERRO,$tr->trans("Não existes formandos ativos nessa formatura !!!"));
	$err	= 1;
}

if ($err != null) {
	echo '1'.\Zage\App\Util::encodeUrl('||'.htmlentities($err));
	exit;
}

#################################################################################
## Calcular os parâmetro de número de rifas
#################################################################################
$qtdePorFormando	= $oRifa->getQtdeObrigatorio();


#################################################################################
## Gerar as rifas
#################################################################################
$em->getConnection()->beginTransaction();

try {
	#################################################################################
	## Faz o loop nos formandos para gerar as rifas
	#################################################################################
	for ($i = 0; $i < sizeof($formandos); $i++) {
		for ($j = 0; $j < $qtdePorFormando; $j++) {
			$log->info("Loop gera: J = ".$j." I = ".$i);
			$rifaAtual	= \Zage\Adm\Semaforo::proximoValor($system->getCodOrganizacao(), "RIFA_".$codRifa);
			$log->info("Rifa Atual: ".$rifaAtual);
			$oNumero	= new \Entidades\ZgfmtRifaNumero();
			$oNumero->setCodRifa($oRifa);
			$oNumero->setCodFormando($formandos[$i]);
			$oNumero->setData(new \DateTime());
			$oNumero->setNumero($rifaAtual);
			$em->persist($oNumero);
		}
	}
	
	$em->flush();
	$em->clear();
	$em->getConnection()->commit();

} catch (\Exception $e) {
	$em->getConnection()->rollback();
	echo '1'.\Zage\App\Util::encodeUrl('||'.htmlentities($e->getMessage()));
	exit;
}

echo '0'.\Zage\App\Util::encodeUrl('||'.$tr->trans($rifaAtual ." Rifas geradas com sucesso"));
