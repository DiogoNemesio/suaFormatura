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
if (isset($_POST['codEvento']))				$codEvento				= \Zage\App\Util::antiInjection($_POST['codEvento']);
if (isset($_POST['valor']))					$valor					= \Zage\App\Util::antiInjection($_POST['valor']);
if (isset($_POST['qtdeMax']))				$qtdeMax				= \Zage\App\Util::antiInjection($_POST['qtdeMax']);
if (isset($_POST['dataInicioInternet']))	$dataInicioInternet		= \Zage\App\Util::antiInjection($_POST['dataInicioInternet']);
if (isset($_POST['dataFimInternet']))		$dataFimInternet		= \Zage\App\Util::antiInjection($_POST['dataFimInternet']);
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
		$system->criaAviso(\Zage\App\Aviso\Tipo::ERRO,$tr->trans("O valor do convite extra deve ser maior que 0."));
		$err	= 1;
	}
}

/** QUANTIDADE MAXIMA **/
if (!isset($qtdeMax) || empty($qtdeMax)) {
	$system->criaAviso(\Zage\App\Aviso\Tipo::ERRO,$tr->trans("Informe a quantidade máxima de convites por formando."));
	$err	= 1;
}else{
	if ($qtdeMax < 0){
		$system->criaAviso(\Zage\App\Aviso\Tipo::ERRO,$tr->trans("A quantidade de convite por aluno deve ser maior que 0."));
		$err	= 1;
	}
}

/** DATA INICIO INTERNET **/
if (!empty($dataInicioInternet)) {
	$dataInicioInternet = DateTime::createFromFormat($system->config["data"]["dateFormat"], $dataInicioInternet);
	if ($dataInicioInternet < new \DateTime("now") && !$codConviteConf) {
		$system->criaAviso(\Zage\App\Aviso\Tipo::ERRO,"A data do início da venda na internet não pode ser inferior a data de hoje.");
		$err	= 1;
	}
}else{
	if ($dataFimInternet){
		$system->criaAviso(\Zage\App\Aviso\Tipo::ERRO,"Para iniciar a venda na internet é necessário preencher uma data de início.");
		$err	= 1;
	}else{
		$dataInicioInternet = null;
	}	
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
	}
}else{
	$dataFimInternet = null;
}

/** DATA INICIO PRESENCIAL **/
if (!empty($dataInicioPresencial)) {
	$dataInicioPresencial = DateTime::createFromFormat($system->config["data"]["dateFormat"], $dataInicioPresencial);
	if ($dataInicioPresencial < new \DateTime("now") && !$codConviteConf) {
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
 		$oConviteEventoConf	= $em->getRepository('Entidades\ZgfmtConviteExtraEventoConf')->findOneBy(array('codigo' => $codConviteConf));
 		if (!$oConviteEventoConf) $oConviteEventoConf	= new \Entidades\ZgfmtConviteExtraEventoConf();
 		//$assunto    = "Evento(".$local.") alterado(a)";
 	}else{
 		$oConviteEventoConf	= new \Entidades\ZgfmtConviteExtraEventoConf();
 		$oConviteEventoConf->setDataCadastro(new DateTime(now));
 		//$assunto    = "Novo evento(".$local.") definido";
 	}
 	
 	#################################################################################
 	## RESGATAR OBJETOS
 	#################################################################################
 	
 	$oOrganizacao	= $em->getRepository('Entidades\ZgadmOrganizacao')->findOneBy(array('codigo' => $system->getCodOrganizacao()));
 	$oEvento		= $em->getRepository('Entidades\ZgfmtEvento')->findOneBy(array('codigo' => $codEvento));
 	$oConta			= $em->getRepository('Entidades\ZgfinConta')->findOneBy(array('codigo' => $codContaRec));
 	
 	#################################################################################
 	## SETAR VALORES
 	#################################################################################
 	
 	$oConviteEventoConf->setCodOrganizacao($oOrganizacao); 
 	$oConviteEventoConf->setCodEvento($oEvento);
 	$oConviteEventoConf->setDataInicioInternet($dataInicioInternet);
 	$oConviteEventoConf->setDataFimInternet($dataFimInternet);
 	$oConviteEventoConf->setDataInicioPresencial($dataInicioPresencial);
 	$oConviteEventoConf->setDataFimPresencial($dataFimPresencial);
 	$oConviteEventoConf->setQtdeMaxAluno($qtdeMax);
 	$oConviteEventoConf->setValor($valor);
 	
 	$em->persist($oConviteEventoConf);
	
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

	$notificacao->adicionaVariavel("EVENTO_TIPO"	, $oEvento->getDescricao());
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
echo '0'.\Zage\App\Util::encodeUrl('|'.$oConviteEventoConf->getCodigo());
