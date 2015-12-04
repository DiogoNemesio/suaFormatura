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
if (isset($_POST['local']))					$local				= \Zage\App\Util::antiInjection($_POST['local']);
if (isset($_POST['codLogradouro']))			$codLogradouro		= \Zage\App\Util::antiInjection($_POST['codLogradouro']);
if (isset($_POST['cep']))					$cep				= \Zage\App\Util::antiInjection($_POST['cep']);
if (isset($_POST['descLogradouro']))		$descLogradouro		= \Zage\App\Util::antiInjection($_POST['descLogradouro']);
if (isset($_POST['bairro']))				$bairro				= \Zage\App\Util::antiInjection($_POST['bairro']);
if (isset($_POST['complemento']))			$complemento		= \Zage\App\Util::antiInjection($_POST['complemento']);
if (isset($_POST['numero']))				$numero				= \Zage\App\Util::antiInjection($_POST['numero']);
if (isset($_POST['latitude']))				$latitude			= \Zage\App\Util::antiInjection($_POST['latitude']);
if (isset($_POST['longitude']))				$longitude			= \Zage\App\Util::antiInjection($_POST['longitude']);

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
if (sizeof($formandos) == 0)	{
	$system->criaAviso(\Zage\App\Aviso\Tipo::ERRO,$tr->trans("O evento não pode ser criado pois não existe formando ativo."));
	$err	= 1;
}

/** Local do evento **/
if (!isset($local) || empty($local)) {
	$system->criaAviso(\Zage\App\Aviso\Tipo::ERRO,$tr->trans("Informe o local aonde será realizado o evento."));
	$err	= 1;
}

/** Analisar se o local é parceiro do sistema  **/
if (!isset($codLocal) || empty($codLocal)) {
	$codLocal = null;
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
 		$assunto    = "Evento(".$local.") alterado(a)";
 	}else{
 		$oEvento	= new \Entidades\ZgfmtEvento();
 		$assunto    = "Novo evento(".$local.") definido";
 	}
 	
 	#################################################################################
 	## Configurações da data
 	#################################################################################
 	if (!empty($dataEvento)) {
 		$dataEvento		= DateTime::createFromFormat($system->config["data"]["datetimeSimplesFormat"], $dataEvento);
 	}else{
 		$dataEvento		= null;
 	}
 	
 	$oOrganizacao	= $em->getRepository('Entidades\ZgadmOrganizacao')->findOneBy(array('codigo' => $system->getCodOrganizacao()));
 	$oTipo			= $em->getRepository('Entidades\ZgfmtEventoTipo')->findOneBy(array('codigo' => $codTipo));
 	$oLocal			= $em->getRepository('Entidades\ZgadmOrganizacao')->findOneBy(array('codigo' => $codLocal));
 	$oLogradouro	= $em->getRepository('Entidades\ZgadmLogradouro')->findOneBy(array('codigo' => $codLogradouro));
 	
 	$oEvento->setCodFormatura($oOrganizacao); 
 	$oEvento->setCodTipoEvento($oTipo);
 	$oEvento->setQtdeConvite($qtdeConvite);
 	$oEvento->setValorAvulso($valorAvulso);
 	$oEvento->setCodLocal($oLocal);
 	$oEvento->setData($dataEvento);
 	$oEvento->setLocal($local);
 	$oEvento->setCodLogradouro($oLogradouro);
 	$oEvento->setCep($cep);
 	$oEvento->setEndereco($descLogradouro);
 	$oEvento->setBairro($bairro);
 	$oEvento->setComplemento($complemento);
 	$oEvento->setNumero($numero);
 	$oEvento->setLatitude($latitude);
 	$oEvento->setLongitude($longitude);
 	
 	$em->persist($oEvento);
	
	/**
	 * ******** Enviar notificação ********
	 **/
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
