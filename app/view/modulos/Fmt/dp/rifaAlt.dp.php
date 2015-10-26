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
global $em,$tr,$log,$system;

#################################################################################
## Resgata os parâmetros passados pelo formulario
#################################################################################
if (isset($_POST['codRifa'])) 			$codRifa		= \Zage\App\Util::antiInjection($_POST['codRifa']);
if (isset($_POST['nome'])) 				$nome			= \Zage\App\Util::antiInjection($_POST['nome']);
if (isset($_POST['premio']))			$premio			= \Zage\App\Util::antiInjection($_POST['premio']);
if (isset($_POST['custo']))				$custo			= \Zage\App\Util::antiInjection($_POST['custo']);
if (isset($_POST['valor'])) 			$valor			= \Zage\App\Util::antiInjection($_POST['valor']);
if (isset($_POST['qtdeObrigatorio']))	$qtdeObri		= \Zage\App\Util::antiInjection($_POST['qtdeObrigatorio']);
if (isset($_POST['indRifaEletronica']))	$indRifaEletronica		= \Zage\App\Util::antiInjection($_POST['indRifaEletronica']);

if (isset($_POST['localSorteio']))		$local			= \Zage\App\Util::antiInjection($_POST['localSorteio']);
if (isset($_POST['dataSorteio']))		$dataHora			= \Zage\App\Util::antiInjection($_POST['dataSorteio']);

if (isset($_POST['numUsuAtivo']))		$numUsuAtivo	= \Zage\App\Util::antiInjection($_POST['numUsuAtivo']);

#################################################################################
## Limpar a variável de erro
#################################################################################
$err	= false;

#################################################################################
## Fazer validação dos campos
#################################################################################
/******* Verificar se existe formandos ativos *********/
$formandos		= \Zage\Fmt\Formatura::listaFormandosAtivos($system->getCodOrganizacao());
if (sizeof($formandos) == 0)	{
	$system->criaAviso(\Zage\App\Aviso\Tipo::ERRO,$tr->trans("A rifa não pode ser criada pois não existe formando ativo!"));
	$err	= 1;
}

/******* Nome *********/
if (isset($codRifa) && (!empty($codRifa))){
	$oRifa	= $em->getRepository('Entidades\ZgfmtRifa')->findOneBy(array('codigo' => $codRifa));
	if ($oRifa->getIndRifaGerada() == 1){
		$system->criaAviso(\Zage\App\Aviso\Tipo::ERRO,$tr->trans("Não podemos alterar um rifa em andamento."));
		$err	= 1;
	}
}

/******* Nome *********/
if (!isset($nome) || (empty($nome))) {
	$system->criaAviso(\Zage\App\Aviso\Tipo::ERRO,$tr->trans("O nome da rifa deve ser preenchido!"));
	$err	= 1;
}elseif ((!empty($nome)) && (strlen($nome) > 100)) {
	$system->criaAviso(\Zage\App\Aviso\Tipo::ERRO,$tr->trans("O nome da rifa não deve conter mais de 100 caracteres!"));
	$err = 1;
}

/******* Data *********/
if (!isset($dataHora) || (empty($dataHora))) {
	$system->criaAviso(\Zage\App\Aviso\Tipo::ERRO,$tr->trans("A data/hora do sorteio deve ser preenchido!"));
	$err	= 1;
}elseif ((!empty($dataHora)) && (strlen($dataHora) > 16)) {
	$system->criaAviso(\Zage\App\Aviso\Tipo::ERRO,$tr->trans("A data/hora do sorteio não deve conter mais de 12 caracteres!"));
	$err	= 1;
}

$dataHora		= DateTime::createFromFormat($system->config["data"]["datetimeSimplesFormat"], $dataHora);

if ($dataHora < new \DateTime("now")) {
	$system->criaAviso(\Zage\App\Aviso\Tipo::ERRO,"A data/hora do sorteio deve ser maior que a data atual!");
	$err	= 1;
}

/******* Ind Rifa Eletronica *********/
if (isset($indRifaEletronica) && (!empty($indRifaEletronica))) {
	$indRifaEletronica	= 1;
}else{
	$indRifaEletronica	= 0;
}

/******* Custo *********/
if (!isset($custo) || (empty($custo))) {
	$custo = 0;
}elseif (!empty($custo)) {
	$custo		= \Zage\App\Util::to_float($custo);
	if (!$custo) {
		$system->criaAviso(\Zage\App\Aviso\Tipo::ERRO,$tr->trans("O custo tem em um formato inválido!"));
		$err	= 1;
	}
}

/******* Valor *********/
if (!isset($valor) || (empty($valor))) {
	$system->criaAviso(\Zage\App\Aviso\Tipo::ERRO,$tr->trans("O valor deve ser preenchido!"));
	$err	= 1;
}elseif (!empty($valor)) {
	$valor		= \Zage\App\Util::to_float($valor);
	if (!$valor) {
		$system->criaAviso(\Zage\App\Aviso\Tipo::ERRO,$tr->trans("O valor da rifa tem um formato inválido!"));
		$err	= 1;
	}
}

if ($err != null) {
	echo '1'.\Zage\App\Util::encodeUrl('||'.htmlentities($err));
	exit;
}

#################################################################################
## Salvar no banco
#################################################################################
$em->getConnection()->beginTransaction();

try {
	
	/***********************
	 * Resgatar os objetos de relacionamento
	 ***********************/
	$oCodOrg		= $em->getRepository('Entidades\ZgadmOrganizacao')->findOneBy(array('codigo' => $system->getCodOrganizacao()));
	
	/***********************
	 * Verificar se a rifa já existe
	 ***********************/
	if (isset($codRifa) && (!empty($codRifa))){
 		$oRifa	= $em->getRepository('Entidades\ZgfmtRifa')->findOneBy(array('codigo' => $codRifa));
 		
 		$oUsuario		= $em->getRepository('Entidades\ZgsegUsuario')->findOneBy(array('codigo' => $system->getCodUsuario()));
 		//$oCentroCusto	= $em->getRepository('Entidades\ZgfinCentroCusto')->findOneBy(array('codigo' => $oRifa->getCodCentroCustro()->getCodigo()));
 		$oRifa->setDataAlteracao(new \DateTime("now"));
 		$oRifa->setUsuarioAlteracao($oUsuario);
 		$novaRifa = false;
 		
 		if (!$oRifa) {
 			$oRifa	= new \Entidades\ZgfmtRifa();
 			$oRifa->setDataCadastro(new \DateTime("now"));
 			$oRifa->setUsuarioCadastro($oUsuario);
 			$novaRifa = true;
 		}
 		
 	}else{
 		// Criar novo centro de custo
 		$oCC = new \Entidades\ZgfinCentroCusto();
 		$oCCTipo	= $em->getRepository('Entidades\ZgfinCentroCustoTipo')->findOneBy(array('codigo' => R));
 		$oCC->setCodOrganizacao($oCodOrg);
 		$oCC->setDescricao('RIFA:'.$nome);
 		$oCC->setCodTipoCentroCusto($oCCTipo);
 		$oCC->setIndCredito(1);
 		$oCC->setIndDebito(1);
 			
 		$em->persist($oCC);
 		
 		//Criar nova rifa
 		$oUsuario	= $em->getRepository('Entidades\ZgsegUsuario')->findOneBy(array('codigo' => $system->getCodUsuario()));
 		$oRifa	= new \Entidades\ZgfmtRifa();
 		$oRifa->setDataCadastro(new \DateTime("now"));
 		$oRifa->setUsuarioCadastro($oUsuario);
 		$oRifa->setCodCentroCusto($oCC);
 		$novaRifa = true;
 		
 	}
	
	/*********************** 
	 * Salvar os dados da rifa
	 ***********************/
	$oRifa->setCodOrganizacao($oCodOrg);
	$oRifa->setNome($nome);
	$oRifa->setPremio($premio);
	$oRifa->setCusto($custo);
	$oRifa->setQtdeObrigatorio($qtdeObri);
	$oRifa->setValorUnitario($valor);
	$oRifa->setDataSorteio($dataHora);
	$oRifa->setLocalSorteio($local);	
	$oRifa->setIndRifaEletronica($indRifaEletronica);
	
	if ($indRifaEletronica == 1){
		$oRifa->setIndRifaGerada(1);
		$menConf 	 = 'Rifa criada com sucesso! Agora falta pouco, gere os bilhetes eletônico para inciar as vendas.';
		$tipo 		 = 'Rifa eletrônica - Suas vendas serão realizadas com o bilhete eletrônico.';
	}else{
		$menConf	 = 'Rifa criada com sucesso! Agora falta pouco, gere e imprima os bilhetes para inciar as vendas.';
		$tipo 		 = 'Rifa convêncial - Suas vendas serão realizas com os bilhetes de papel.';
	}
	
	$em->persist($oRifa);
	
	#################################################################################
	## Gerar a notificação
	#################################################################################
	if ($novaRifa == true){
		$oRemetente		= $em->getReference('\Entidades\ZgsegUsuario',$system->getCodUsuario());
		$template		= $em->getRepository('\Entidades\ZgappNotificacaoTemplate')->findOneBy(array('template' => 'RIFA_CADASTRO'));
		$notificacao	= new \Zage\App\Notificacao(\Zage\App\Notificacao::TIPO_MENSAGEM_TEMPLATE, \Zage\App\Notificacao::TIPO_DEST_USUARIO);
		$notificacao->setAssunto("Vamos vender rifas?");
		$notificacao->setCodRemetente($oRemetente);
		
		for ($i = 0; $i < sizeof($formandos); $i++) {
			$notificacao->associaUsuario($formandos[$i]->getCodigo());
		}
	
		$notificacao->enviaEmail();
		$notificacao->enviaSistema();
		//$notificacao->setEmail("daniel.cassela@usinacaete.com"); # Se quiser mandar com cópia
		$notificacao->setCodTemplate($template);
		$notificacao->adicionaVariavel("NOME", $nome);
		$notificacao->adicionaVariavel("PREMIO", $premio);
		$notificacao->adicionaVariavel("VALOR", $valor);
		$notificacao->adicionaVariavel("QTDE", $qtdeObri);
		$notificacao->adicionaVariavel("TIPO", $tipo);
		$notificacao->salva();
	}
	
	$em->flush();
	$em->clear();
	$em->getConnection()->commit();

} catch (\Exception $e) {
	$em->getConnection()->rollback();
	$system->criaAviso(\Zage\App\Aviso\Tipo::ERRO,$e->getMessage());
	echo '1'.\Zage\App\Util::encodeUrl('||'.htmlentities($e->getMessage()));
	exit;
}

$system->criaAviso(\Zage\App\Aviso\Tipo::INFO,$tr->trans($menConf));
echo '0'.\Zage\App\Util::encodeUrl('|'.$oRifa->getCodigo());