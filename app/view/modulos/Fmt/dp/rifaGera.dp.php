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
if (isset($_POST['codRifa'])) 				$codRifa			= \Zage\App\Util::antiInjection($_POST['codRifa']);
if (isset($_POST['qtdePorFormando'])) 		$qtdePorFormando	= \Zage\App\Util::antiInjection($_POST['qtdePorFormando']);
if (isset($_POST['indTodosFormandos'])) 	$indTodosFormandos	= \Zage\App\Util::antiInjection($_POST['indTodosFormandos']);
if (isset($_POST['formandos'])) 			$listaFormandos		= \Zage\App\Util::antiInjection($_POST['formandos']);

#################################################################################
## Limpar a variável de erro
#################################################################################
$err	= false;

#################################################################################
## Fazer validação dos campos
#################################################################################
// COD RIFA
if (!isset($codRifa) || (empty($codRifa))) {
	die ('1'.\Zage\App\Util::encodeUrl('||'.htmlentities($tr->trans("Falta de parâmetros : COD_RIFA!"))));
}else{
	$oRifa	= $em->getRepository('\Entidades\ZgfmtRifa')->findOneBy(array('codigo' => $codRifa));
	if (!$oRifa) {
		die ('1'.\Zage\App\Util::encodeUrl('||'.htmlentities($tr->trans("Rifa não encontrada!"))));
	}
}

// VERIFICAR SE A RIFA AINDA ESTÁ VÁLIDA
if (new \DateTime("now") > $oRifa->getDataSorteio()){
	die ('1'.\Zage\App\Util::encodeUrl('||'.htmlentities($tr->trans("Você não pode gerar bilhetes para esta rifa pois já chegou a data do sorteio!"))));
}

// QUANTIDADE DE BILHETES
if (!isset($qtdePorFormando) || (empty($qtdePorFormando))) {
	die ('1'.\Zage\App\Util::encodeUrl('||'.htmlentities($tr->trans("Você deve informar o números de bilhetes a ser gerado para cada formando!"))));
}

// INDICADOR DE FORMANDOS
if (isset($indTodosFormandos) && (!empty($indTodosFormandos))) {
	$indTodosFormandos = 1;
}elseif ($oRifa->getIndRifaGerada() == null){
	$indTodosFormandos = 1;
}else{
	$indTodosFormandos = 0;
}

// LISTA DE FORMANDOS
if (($indTodosFormandos == 0 && empty($listaFormandos))) {
	die ('1'.\Zage\App\Util::encodeUrl('||'.htmlentities($tr->trans("Selecione pelo menos um formando!"))));
}

#################################################################################
## Resgatar os formandos ativos dessa organização
#################################################################################
if ($indTodosFormandos){
	$formandos		= \Zage\Fmt\Formatura::listaFormandosAtivos($system->getCodOrganizacao());
	if (sizeof($formandos) == 0)	{
		die ('1'.\Zage\App\Util::encodeUrl('||'.htmlentities($tr->trans("Não existes formandos ativos nessa formatura!"))));
	}
}else {
	$formandos = array();
	for ($i = 0; $i < sizeof($listaFormandos); $i++){
		$formandos[$i] = $em->getRepository('\Entidades\ZgsegUsuario')->findOneBy(array('codigo' => $listaFormandos[$i]));
	}
}
#################################################################################
## Gerar as rifas
#################################################################################
$em->getConnection()->beginTransaction();

try {
	
	#################################################################################
	## Gerar sequencial
	#################################################################################
	$codGera = \Zage\Adm\Sequencial::proximoValor(ZgfmtRifaGeracaoSequencial);

	#################################################################################
	## Salvar na tabela de geração
	#################################################################################
	$oUsu 			= $em->getRepository('\Entidades\ZgsegUsuario')->findOneBy(array('codigo' => $system->getCodUsuario()));
	$oCodGeracao	= $em->getRepository('Entidades\ZgfmtRifaGeracaoSequencial')->findOneBy(array('codigo' => $codGera));
	
	$oGeracao = new \Entidades\ZgfmtRifaGeracao();
	$oGeracao->setCodRifa($oRifa);
	$oGeracao->setCodGeracao($oCodGeracao);
	$oGeracao->setCodUsuario($oUsu);
	$oGeracao->setData(new \DateTime());
	$em->persist($oGeracao);
	
	#################################################################################
	## Alterar o status de gerado da rifa
	#################################################################################
	$oRifa->setIndRifaGerada('1');
	$em->persist($oRifa);
	
	#################################################################################
	## Faz o loop nos formandos para gerar as rifas
	#################################################################################
	for ($i = 0; $i < sizeof($formandos); $i++) {
		for ($j = 0; $j < $qtdePorFormando; $j++) {
			$rifaAtual	= \Zage\Adm\Semaforo::proximoValor($system->getCodOrganizacao(), "RIFA_".$codRifa);
			$oNumero	= new \Entidades\ZgfmtRifaNumero();
			$oNumero->setCodRifa($oRifa);
			$oNumero->setCodFormando($formandos[$i]);
			$oNumero->setData(new \DateTime());
			$oNumero->setNumero($rifaAtual);
			$oNumero->setCodGeracao($oCodGeracao);
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

$system->criaAviso(\Zage\App\Aviso\Tipo::INFO,$tr->trans("Bilhetes gerados com sucesso! Você já pode baixar o arquivo pdf."));
echo '0'.\Zage\App\Util::encodeUrl('|');