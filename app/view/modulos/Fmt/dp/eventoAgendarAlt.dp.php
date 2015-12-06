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
if (isset($_POST['codEvento']))				$codEvento			= \Zage\App\Util::antiInjection($_POST['codEvento']);
if (isset($_POST['codTipo']))				$codTipo			= \Zage\App\Util::antiInjection($_POST['codTipo']);
if (isset($_POST['codLocal']))				$codLocal			= \Zage\App\Util::antiInjection($_POST['codLocal']);
if (isset($_POST['dataEvento']))			$dataEvento			= \Zage\App\Util::antiInjection($_POST['dataEvento']);
if (isset($_POST['qtdeConvite']))			$qtdeConvite		= \Zage\App\Util::antiInjection($_POST['qtdeConvite']);
if (isset($_POST['valorAvulso']))			$valorAvulso		= \Zage\App\Util::antiInjection($_POST['valorAvulso']);
if (isset($_POST['fornecedor']))			$codLocal				= \Zage\App\Util::antiInjection($_POST['fornecedor']);

#################################################################################
## Limpar a variável de erro
#################################################################################
$err	= false;

#################################################################################
## Fazer validação dos campos
#################################################################################
/** Tipo de envento **/
if (!isset($codTipo) || empty($codTipo)) {
	$system->criaAviso(\Zage\App\Aviso\Tipo::ERRO,$tr->trans("Ops, encontramos um problema para identificar o tipo do evento. Tente novamente em instantes e caso o problema persita entre em contato com o suporte."));
	$err	= 1;
}

/** Verificar se existe formandos ativos **/
$formandos		= \Zage\Fmt\Formatura::listaFormandosAtivos($system->getCodOrganizacao());
if (sizeof($formandos) == 0) {
	$system->criaAviso(\Zage\App\Aviso\Tipo::ERRO,$tr->trans("O evento não pode ser criado pois não existe formando ativo."));
	$err	= 1;
}

/** Data do evento **/
if (!isset($dataEvento) || empty($dataEvento)) {
	$system->criaAviso(\Zage\App\Aviso\Tipo::ERRO,$tr->trans("Informe a data do evento."));
	$err	= 1;
}else{
	$dataEvento = DateTime::createFromFormat($system->config["data"]["datetimeSimplesFormat"], $dataEvento);
	
	if ($dataEvento < new \DateTime("now") && !$codEvento) {
		$system->criaAviso(\Zage\App\Aviso\Tipo::ERRO,"A data do evento deve ser maior que a data de hoje.");
		$err	= 1;
	}
}

/** Local do evento (validar apenas se o tipo não for APOSIÇÃO DE PLACA) **/
if ($codTipo != 5){
	if (!isset($codLocal) || empty($codLocal)) {
		$system->criaAviso(\Zage\App\Aviso\Tipo::ERRO,$tr->trans("Informe o local aonde será realizado o evento."));
		$err	= 1;
	}else{
		$oFornecedor	= $em->getRepository('Entidades\ZgfinPessoa')->findOneBy(array('codigo' => $codLocal));
	}
}else{
	$oFornecedor = null;
}

/** Valor Avulso **/
if (!isset($valorAvulso) || empty($valorAvulso)) {
	$valorAvulso = null;
}else{
	$valorAvulso = \Zage\App\Util::to_float($valorAvulso);
}

/** Quantidade de convidados **/
if (!isset($qtdeConvite) || empty($qtdeConvite)) {
	$qtdeConvite = null;
}

if ($err != null) {
	echo '1'.\Zage\App\Util::encodeUrl('||'.htmlentities($err));
 	exit;
}
 
#################################################################################
## Salvar no banco
#################################################################################
//$em->getConnection()->beginTransaction();
try {
	
	if (isset($codEvento) && (!empty($codEvento))) {
 		$oEvento	= $em->getRepository('Entidades\ZgfmtEvento')->findOneBy(array('codigo' => $codEvento));
 		if (!$oEvento) $oEvento	= new \Entidades\ZgfmtEvento();
 		$assunto    = "O evento ".$local." foi alterado";
 	}else{
 		$oEvento	= new \Entidades\ZgfmtEvento();
 		$assunto    = $local."foi definido";
 	}
 	
 	#################################################################################
 	## Configurações da data
 	################################################################################# 	
 	$oOrganizacao	= $em->getRepository('Entidades\ZgadmOrganizacao')->findOneBy(array('codigo' => $system->getCodOrganizacao()));
 	$oTipoEvento	= $em->getRepository('Entidades\ZgfmtEventoTipo')->findOneBy(array('codigo' => $codTipo));
 	
 	$oEvento->setCodFormatura($oOrganizacao);
 	$oEvento->setData($dataEvento);
 	$oEvento->setCodTipoEvento($oTipoEvento);
 	$oEvento->setQtdeConvite($qtdeConvite);
 	$oEvento->setValorAvulso($valorAvulso);
 	$oEvento->setCodPessoa($oFornecedor);
 	
 	$em->persist($oEvento);
	
	/**
	 * ******** Enviar notificação ********
	 **/
	$oRemetente 	= $em->getReference ('\Entidades\ZgsegUsuario', $system->getCodUsuario());
	$template 		= $em->getRepository ('\Entidades\ZgappNotificacaoTemplate' )->findOneBy (array('template' => 'EVENTO_CONF'));
	$notificacao	= new \Zage\App\Notificacao(\Zage\App\Notificacao::TIPO_MENSAGEM_TEMPLATE, \Zage\App\Notificacao::TIPO_DEST_USUARIO);
	$notificacao->setAssunto($assunto);
	$notificacao->setCodRemetente($oRemetente);
	
	for ($i = 0; $i < sizeof($formandos); $i++) {
		$notificacao->associaUsuario($formandos[$i]->getCodigo());
	}
	
	$notificacao->enviaSistema();
	$notificacao->enviaEmail ();
	//$notificacao->setEmail ( $email );
	$notificacao->setCodTemplate ( $template );

	$notificacao->adicionaVariavel("EVENTO_TIPO"	, $oTipoEvento->getDescricao());
 	//$notificacao->adicionaVariavel("NOME"			, $local);
 	///$notificacao->adicionaVariavel("DATA"			, $dataEvento);
 	//$notificacao->adicionaVariavel("LOGRADOURO"		, $descLogradouro);
 	///$notificacao->adicionaVariavel("BAIRRO"			, $bairro);
 	//$notificacao->adicionaVariavel("NUMERO"			, $numero);
 	//$notificacao->adicionaVariavel("COMPLEMENTO"	, $complemento);
 	
	$notificacao->salva ();
 	/********** Salvar as informações *******/
	$em->flush();
	$em->clear();
	//$em->getConnection()->commit();

} catch (\Exception $e) {
	//$em->getConnection()->rollback();
 	$system->criaAviso(\Zage\App\Aviso\Tipo::ERRO,$e->getMessage());
 	echo '1'.\Zage\App\Util::encodeUrl('||'.htmlentities($e->getMessage()));
 	exit;
}
 
$system->criaAviso(\Zage\App\Aviso\Tipo::INFO,"Informações salvas com sucesso");
echo '0'.\Zage\App\Util::encodeUrl('|'.$oEvento->getCodigo());
