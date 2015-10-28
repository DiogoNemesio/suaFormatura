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
## Resgata os parâmetros passados pelo formulário
#################################################################################
if (isset($_POST['codConviteConf']))		$codConviteConf			= \Zage\App\Util::antiInjection($_POST['codConviteConf']);
if (isset($_POST['codTipoEvento']))			$codTipoEvento			= \Zage\App\Util::antiInjection($_POST['codTipoEvento']);
if (isset($_POST['custoBoletoPadrao']))		$custoBoletoPadrao		= \Zage\App\Util::antiInjection($_POST['custoBoletoPadrao']);
if (isset($_POST['valor']))					$valor					= \Zage\App\Util::antiInjection($_POST['valor']);
if (isset($_POST['qtdeMax']))				$qtdeMax				= \Zage\App\Util::antiInjection($_POST['qtdeMax']);
if (isset($_POST['dataInicioInternet']))	$dataInicioInternet		= \Zage\App\Util::antiInjection($_POST['dataInicioInternet']);
if (isset($_POST['dataFimInternet']))		$dataFimInternet		= \Zage\App\Util::antiInjection($_POST['dataFimInternet']);
if (isset($_POST['custoBoleto']))			$custoBoleto			= \Zage\App\Util::antiInjection($_POST['custoBoleto']);
if (isset($_POST['codContaRec']))			$codContaRec			= \Zage\App\Util::antiInjection($_POST['codContaRec']);
if (isset($_POST['dataInicioPresencial']))	$dataInicioPresencial	= \Zage\App\Util::antiInjection($_POST['dataInicioPresencial']);
if (isset($_POST['dataFimPresencial']))		$dataFimPresencial		= \Zage\App\Util::antiInjection($_POST['dataFimPresencial']);

#################################################################################
## Limpar a variável de erro
#################################################################################
$err	= false;
#################################################################################
## Fazer validação dos campos
#################################################################################
/** VALOR **/
if (!isset($valor) || empty($valor)) {
	$system->criaAviso(\Zage\App\Aviso\Tipo::ERRO,$tr->trans("Informe o valor unitário do convite extra."));
	$err	= 1;
}else{
	$valor 		= \Zage\App\Util::to_float($valor);
	if ($valor < 0){
		$system->criaAviso(\Zage\App\Aviso\Tipo::ERRO,$tr->trans("O valor unitário do convite extra deve ser maior que 0."));
		$err	= 1;
	}
}

/** QUANTIDADE MAXIMA **/
if (!isset($qtdeMax) || empty($qtdeMax)) {
	$qtdeMax = null;
}

/** DATA INICIO INTERNET **/
if (!empty($dataInicioInternet)) {
	$dataInicioInternet = DateTime::createFromFormat($system->config["data"]["dateFormat"], $dataInicioInternet);
	if ($dataInicioInternet < new \DateTime("now")) {
		$system->criaAviso(\Zage\App\Aviso\Tipo::ERRO,"A data do início da venda na internet não pode ser inferior a data de hoje.");
		$err	= 1;
	}elseif (empty($codContaRec)){
		$system->criaAviso(\Zage\App\Aviso\Tipo::ERRO,"Selecione uma conta bancária para receber os pagamentos.");
		$err	= 1;
	}
}else{
	$dataInicioInternet = null;
}

/** DATA FIM INTERNET **/
if (!empty($dataFimInternet)) {
	$dataFimInternet = DateTime::createFromFormat($system->config["data"]["dateFormat"], $dataFimInternet);
	if ($dataFimInternet < new \DateTime("now")) {
		$system->criaAviso(\Zage\App\Aviso\Tipo::ERRO,"A data do encerramento da venda na internet não pode ser inferior a data de hoje.");
		$err	= 1;
	}elseif ($dataFimInternet < $dataInicioInternet){
		$system->criaAviso(\Zage\App\Aviso\Tipo::ERRO,"A data do encerramento da venda na internet não pode ser inferior a data de inicio.");
		$err	= 1;
	}elseif ($dataFimInternet && empty($dataInicioInternet)){
		$system->criaAviso(\Zage\App\Aviso\Tipo::ERRO,"Para iniciar a venda na internet é necessário preencher uma data de início.");
		$err	= 1;
	}
}else{
	$dataFimInternet = null;
}

/** CONTA CORRENTE **/
if ($codContaRec && empty($dataInicioInternet)) {
	$system->criaAviso(\Zage\App\Aviso\Tipo::ERRO,"Para iniciar a venda na internet é necessário preencher uma data de início.");
	$err	= 1;
}

/** CUSTO BOLETO **/
if (!empty($custoBoleto)) {
	if (empty($codContaRec)) {
		$system->criaAviso(\Zage\App\Aviso\Tipo::ERRO,"Selecione a conta corrente.");
		$err	= 1;
	}elseif ($custoBoleto > $custoBoletoPadrao){
		$system->criaAviso(\Zage\App\Aviso\Tipo::ERRO,"O custo do boleto não deve ser maior que R$ ".$custoBoletoPadrao);
		$err	= 1;
	}
	$custoBoleto 		= \Zage\App\Util::to_float($custoBoleto);
}


/** DATA INICIO PRESENCIAL **/
if (!empty($dataInicioPresencial)) {
	$dataInicioPresencial = DateTime::createFromFormat($system->config["data"]["dateFormat"], $dataInicioPresencial);
	if ($dataInicioPresencial < new \DateTime("now")) {
		$system->criaAviso(\Zage\App\Aviso\Tipo::ERRO,"A data do início da venda presencial não pode ser inferior a data de hoje.");
		$err	= 1;
	}
}else{
	$system->criaAviso(\Zage\App\Aviso\Tipo::ERRO,"É necessário preencher a data do início da venda presencial.");
	$err	= 1;
}

/** DATA FIM PRESENCIAL **/
if (!empty($dataFimPresencial)) {
	$dataFimPresencial = DateTime::createFromFormat($system->config["data"]["dateFormat"], $dataFimPresencial);
	if ($dataFimPresencial < new \DateTime("now")) {
		$system->criaAviso(\Zage\App\Aviso\Tipo::ERRO,"A data do encerramento da venda presencial não pode ser inferior a data de hoje.");
		$err	= 1;
	}elseif ($dataFimPresencial < $dataInicioPresencial){
		$system->criaAviso(\Zage\App\Aviso\Tipo::ERRO,"A data do encerramento da venda presencial não pode ser inferior a data de inicio.");
		$err	= 1;
	}
}else{
	$dataFimPresencial = null;
}


if ($err != null) {
	echo '1'.\Zage\App\Util::encodeUrl('||'.htmlentities($err));
 	exit;
}
 
#################################################################################
## Salvar no banco
#################################################################################
try {
	
	#################################################################################
	## VERIFICAR SE A CONFIGURAÇÃO JÁ EXISTE
	#################################################################################
	
	if (isset($codConviteConf) && (!empty($codConviteConf))) {
 		$oConviteConf	= $em->getRepository('Entidades\ZgfmtConviteExtraConf')->findOneBy(array('codigo' => $codConviteConf));
 		if (!$oConviteConf) $oConviteConf	= new \Entidades\ZgfmtConviteExtraConf();
 		//$assunto    = "Evento(".$local.") alterado(a)";
 	}else{
 		$oConviteConf	= new \Entidades\ZgfmtConviteExtraConf();
 		$oConviteConf->setDataCadastro(new DateTime(now));
 		//$assunto    = "Novo evento(".$local.") definido";
 	}
 	
 	#################################################################################
 	## RESGATAR OBJETOS
 	#################################################################################
 	
 	$oOrganizacao	= $em->getRepository('Entidades\ZgadmOrganizacao')->findOneBy(array('codigo' => $system->getCodOrganizacao()));
 	$oTipo			= $em->getRepository('Entidades\ZgfmtEventoTipo')->findOneBy(array('codigo' => $codTipoEvento));
 	$oConta			= $em->getRepository('Entidades\ZgfinConta')->findOneBy(array('codigo' => $codContaRec));
 	
 	#################################################################################
 	## SETAR VALORES
 	#################################################################################
 	
 	$oConviteConf->setCodOrganizacao($oOrganizacao); 
 	$oConviteConf->setCodTipoEvento($oTipo);
 	$oConviteConf->setDataInicioInternet($dataInicioInternet);
 	$oConviteConf->setDataFimInternet($dataFimInternet);
 	$oConviteConf->setContaRecebimentoInternet($oConta);
 	$oConviteConf->setTaxaConveniencia($custoBoleto);
 	$oConviteConf->setDataInicioPresencial($dataInicioPresencial);
 	$oConviteConf->setDataFimPresencial($dataFimPresencial);
 	$oConviteConf->setQtdeMaxAluno($qtdeMax);
 	$oConviteConf->setValor($valor);
 	
 	$em->persist($oConviteConf);
	
	/**
	 * ******** Enviar notificação ********
	 
	$oRemetente = $em->getReference ( '\Entidades\ZgsegUsuario', $system->getCodUsuario () );
	$template = $em->getRepository ( '\Entidades\ZgappNotificacaoTemplate' )->findOneBy ( array (
			'template' => 'EVENTO_CONF' 
	) );
	$notificacao	= new \Zage\App\Notificacao(\Zage\App\Notificacao::TIPO_MENSAGEM_TEMPLATE, \Zage\App\Notificacao::TIPO_DEST_USUARIO);
	$notificacao->setAssunto ( $assunto );
	$notificacao->setCodRemetente ( $oRemetente );
	
	for ($i = 0; $i < sizeof($formandos); $i++) {
		$notificacao->associaUsuario($formandos[$i]->getCodigo());
	}
	
	if (!empty($dataEvento)) {
		$dataEvento		= $dataEvento->format($system->config["data"]["datetimeSimplesFormat"]);
	}else{
		$dataEvento		= null;
	}
	
	$notificacao->enviaSistema();
	$notificacao->enviaEmail ();
	//$notificacao->setEmail ( $email );
	$notificacao->setCodTemplate ( $template );

	$notificacao->adicionaVariavel("EVENTO_TIPO"	, $oTipo->getDescricao());
 	$notificacao->adicionaVariavel("NOME"			, $local);
 	$notificacao->adicionaVariavel("DATA"			, $dataEvento);
 	$notificacao->adicionaVariavel("LOGRADOURO"		, $descLogradouro);
 	$notificacao->adicionaVariavel("BAIRRO"			, $bairro);
 	$notificacao->adicionaVariavel("NUMERO"			, $numero);
 	$notificacao->adicionaVariavel("COMPLEMENTO"	, $complemento);
 	
	$notificacao->salva ();**/
 	
 	#################################################################################
 	## SALVAR
 	#################################################################################
	$em->flush();
	$em->clear();

} catch (\Exception $e) {
 	$system->criaAviso(\Zage\App\Aviso\Tipo::ERRO,$e->getMessage());
 	echo '1'.\Zage\App\Util::encodeUrl('||'.htmlentities($e->getMessage()));
 	exit;
}
 
$system->criaAviso(\Zage\App\Aviso\Tipo::INFO,"Informações salvas com sucesso");
echo '0'.\Zage\App\Util::encodeUrl('|'.$oConviteConf->getCodigo());
