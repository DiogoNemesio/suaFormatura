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
$oOrganizacao	= $em->getRepository('Entidades\ZgadmOrganizacao')->findOneBy(array('codigo' => $system->getCodOrganizacao()));
if ($oOrganizacao->getCodStatus()->getCodigo() != 'A'){
	$system->criaAviso(\Zage\App\Aviso\Tipo::ERRO,$tr->trans("Não é possivel realizar a operação pois a formatura ainda não está ativa."));
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
if ($codTipo != 5 && $codTipo != 9 && $codTipo != 10){
	$emailComEnd = true;
	if (!isset($codLocal) || empty($codLocal)) {
		$system->criaAviso(\Zage\App\Aviso\Tipo::ERRO,$tr->trans("Informe o local aonde será realizado o evento."));
		$err	= 1;
	}else{
		$oFornecedor	= $em->getRepository('Entidades\ZgfinPessoa')->findOneBy(array('codigo' => $codLocal));
	}
}else{
	$emailComEnd = false;
	$oFornecedor = null;
}

/** Valor Avulso **/
if (!isset($valorAvulso) || empty($valorAvulso)) {
	$valorAvulso = 0;
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
	
	#################################################################################
	## Resgatar objetos
	#################################################################################
	$oTipoEvento	= $em->getRepository('Entidades\ZgfmtEventoTipo')->findOneBy(array('codigo' => $codTipo));
	
	if (isset($codEvento) && (!empty($codEvento))) {
 		$oEvento	= $em->getRepository('Entidades\ZgfmtEvento')->findOneBy(array('codigo' => $codEvento));
 		if (!$oEvento) $oEvento	= new \Entidades\ZgfmtEvento();
 		
 	}else{
 		$oEvento	= new \Entidades\ZgfmtEvento();
 	}
 	
 	//Verificar qual tipo de email enviar
 	if ($oEvento->getData() == null){
 		$novo = true;
 	}else{
 		$novo = false;
 		$alteracao = false;
 		if ($oEvento->getData() != $dataEvento){
 			$log->info('entrei data');
 			$alteracao = true;
 		}elseif ($oEvento->getCodPessoa()->getCodigo() != $codLocal){
 			$log->info('entrei local');
 			$alteracao = true;
 		}elseif ($oEvento->getQtdeConvite() != $qtdeConvite){
 			$log->info('entrei convite');
 			$alteracao = true;
 		}
 	}
 	
 	$oEvento->setCodFormatura($oOrganizacao);
 	$oEvento->setData($dataEvento);
 	$oEvento->setCodTipoEvento($oTipoEvento);
 	$oEvento->setQtdeConvite($qtdeConvite);
 	//$oEvento->setValorAvulso($valorAvulso);
 	$oEvento->setCodPessoa($oFornecedor);
 	
 	$em->persist($oEvento);
	
	#################################################################################
	## Enviar notificação
	#################################################################################
 	if ($novo == true){
 		$notifica	= true;
 		$assunto    = "Definição de um novo evento";
 		$texto 		= 'Sua turma acabou de definir um novo evento. Fique atento à programação da sua formatura.';
 	}elseif ($alteracao == true){
 		$notifica	= true;
 		$assunto    = "Evento alterado";
 		$texto 		= 'Sua turma acabou de alterar um evento. Fique atento à programação da sua formatura.';
 	}else{
 		$notifica	= false;
 	}
 	
 	if ($notifica == true){
	 	$oRemetente 	= $em->getReference('\Entidades\ZgsegUsuario', $system->getCodUsuario());
		$template 		= $em->getRepository('\Entidades\ZgappNotificacaoTemplate')->findOneBy(array('template' => 'EVENTO_CONF'));
		$notificacao	= new \Zage\App\Notificacao(\Zage\App\Notificacao::TIPO_MENSAGEM_TEMPLATE, \Zage\App\Notificacao::TIPO_DEST_USUARIO);
		$notificacao->setAssunto($assunto);
		$notificacao->setCodRemetente($oRemetente);
		
		$formandos		= \Zage\Fmt\Formatura::listaFormandos($system->getCodOrganizacao());
		
		for ($i = 0; $i < sizeof($formandos); $i++) {
			$notificacao->associaUsuario($formandos[$i]->getCodigo());
		}
		
		$notificacao->enviaSistema();
		$notificacao->enviaEmail ();
		$notificacao->setCodTemplate($template);
		
		//Analisar se precisa enviar o endereço
		if ($emailComEnd == false){
			$oOrfFmt 		= $em->getRepository('\Entidades\ZgfmtOrganizacaoFormatura')->findOneBy(array('codOrganizacao' => $system->getCodOrganizacao()));
			
			$notificacao->adicionaVariavel("EVENTO_TIPO"	, $oTipoEvento->getDescricao());
			$notificacao->adicionaVariavel("TEXTO"			, $texto);
			$notificacao->adicionaVariavel("LOCAL"			, $oOrfFmt->getCodInstituicao()->getNome());
			$notificacao->adicionaVariavel("DATA"			, $dataEvento->format($system->config["data"]["datetimeSimplesFormat"]));
			$notificacao->adicionaVariavel("TAG_END"		, "");
			$notificacao->adicionaVariavel("LOGRADOURO"		, "");
			$notificacao->adicionaVariavel("BAIRRO"			, "");
			$notificacao->adicionaVariavel("NUMERO"			, "");
			$notificacao->adicionaVariavel("COMPLEMENTO"	, "");
			
			//Qtde de convite
			if ($qtdeConvite == null){
				$notificacao->adicionaVariavel("QTDE_CONVITE"	, "Sem limite");
			}else{
				$notificacao->adicionaVariavel("QTDE_CONVITE"	, $qtdeConvite." (incluindo o formando)");
			}
			
		}else{
			
			$oForEnd 		= $em->getRepository('\Entidades\ZgfinPessoaEndereco')->findOneBy(array('codPessoa' => $oFornecedor->getCodigo(), 'codTipoEndereco' => "F"));
				
			$notificacao->adicionaVariavel("EVENTO_TIPO"	, $oTipoEvento->getDescricao());
			$notificacao->adicionaVariavel("TEXTO"			, $texto);
			$notificacao->adicionaVariavel("LOCAL"			, $oFornecedor->getFantasia());
			$notificacao->adicionaVariavel("DATA"			, $dataEvento->format($system->config["data"]["datetimeSimplesFormat"]));
			
			//Qtde de convite
			if ($qtdeConvite == null){
				$notificacao->adicionaVariavel("QTDE_CONVITE"	, "Sem limite");
			}else{
				$notificacao->adicionaVariavel("QTDE_CONVITE"	, $qtdeConvite." (incluindo o formando)");
			}
			
			//Verificar se tem endereço
			if ($oForEnd){
				$notificacao->adicionaVariavel("TAG_END"		, "ENDEREÇO:");
				$notificacao->adicionaVariavel("LOGRADOURO"		, "<b>Rua:</b> ".$oForEnd->getEndereco());
				$notificacao->adicionaVariavel("BAIRRO"			, "<b>Bairro:</b> ".$oForEnd->getBairro());
				$notificacao->adicionaVariavel("NUMERO"			, "<b>Número:</b> ".$oForEnd->getNumero());
				$notificacao->adicionaVariavel("COMPLEMENTO"	, "<b>Complemento:</b> ".$oForEnd->getComplemento());
			}else{
				$notificacao->adicionaVariavel("TAG_END"		, "");
				$notificacao->adicionaVariavel("LOGRADOURO"		, "");
				$notificacao->adicionaVariavel("BAIRRO"			, "");
				$notificacao->adicionaVariavel("NUMERO"			, "");
				$notificacao->adicionaVariavel("COMPLEMENTO"	, "");
			}
			
		}
	 	
		$notificacao->salva ();
 	}
 	
 	#################################################################################
	## Salvar informações
	#################################################################################
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