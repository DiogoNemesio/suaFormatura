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
## Resgata os parâmetros passados pelo formulario
#################################################################################
if (isset($_POST['codEnquete']))		$codEnquete			= \Zage\App\Util::antiInjection($_POST['codEnquete']);
if (isset($_POST['pergunta'])) 			$pergunta			= \Zage\App\Util::antiInjection($_POST['pergunta']);
if (isset($_POST['descricao'])) 		$descricao			= \Zage\App\Util::antiInjection($_POST['descricao']);
if (isset($_POST['codTipo'])) 			$codTipo			= \Zage\App\Util::antiInjection($_POST['codTipo']);
if (isset($_POST['dataPrazo']))			$dataPrazo			= \Zage\App\Util::antiInjection($_POST['dataPrazo']);
if (isset($_POST['dataPrazo']))			$data				= \Zage\App\Util::antiInjection($_POST['dataPrazo']);
if (isset($_POST['tamanho']))			$tamanho			= \Zage\App\Util::antiInjection($_POST['tamanho']);
if (isset($_POST['valores'])) 			$valores			= \Zage\App\Util::antiInjection($_POST['valores']);

#################################################################################
## Limpar a variável de erro
#################################################################################
$err	= false;

#################################################################################
## Fazer validação dos campos
#################################################################################
/** Verificar se existe formando **/
$formandos		= \Zage\Fmt\Formatura::listaFormandosAtivos($system->getCodOrganizacao());
if (sizeof($formandos) == 0)	{
	die ('1'.\Zage\App\Util::encodeUrl('||'.htmlentities($tr->trans("Esta formatura não possui formandos ativos!"))));
	$err	= 1;
}

/** Validações para a edição **/
if ($codEnquete){
	$oEnquete	= $em->getRepository('Entidades\ZgappEnquetePergunta')->findOneBy(array('codOrganizacao' => $system->getCodOrganizacao(), 'codigo' => $codEnquete));
	$oResposta	= $em->getRepository('Entidades\ZgappEnqueteResposta')->findBy(array('codPergunta' => $codEnquete));
	if ($oEnquete->getDataPrazo() < new \DateTime("now")){
		die ('1'.\Zage\App\Util::encodeUrl('||'.htmlentities($tr->trans("Para garantir a integridade da enquete não é possível alterar uma pergunta que já teve seu prazo expirado!"))));
		$err	= 1;
	}else{
		if ($oResposta){
			die ('1'.\Zage\App\Util::encodeUrl('||'.htmlentities($tr->trans("Para garantir a integridade da enquete não é possível alterar uma pergunta que já possui resposta!"))));
			$err	= 1;
		}
	}
}

/** Pergunta **/
if (!isset($pergunta) || (empty($pergunta))) {
	die ('1'.\Zage\App\Util::encodeUrl('||'.htmlentities($tr->trans("A pergunta deve ser preencida!"))));
	$err	= 1;
}elseif ((!empty($pergunta)) && (strlen($pergunta) > 200)) {
	$system->criaAviso(\Zage\App\Aviso\Tipo::ERRO,$tr->trans("A pergunta não deve conter mais de 200 caracteres!"));
	$err	= 1;
}

/** Descrição **/
if ((!empty($descricao)) && (strlen($descricao) > 100)) {
	die ('1'.\Zage\App\Util::encodeUrl('||'.htmlentities($tr->trans("O objetivo não deve conter mais de 100 caracteres!"))));
	$err	= 1;
}

/** Tipo da pergunta **/
if (!isset($codTipo) || (empty($codTipo))) {
	die ('1'.\Zage\App\Util::encodeUrl('||'.htmlentities($tr->trans("O tipo da pergunta deve ser preenchido!"))));
	$err	= 1;
}

/** Data **/
if (!empty($dataPrazo)) {
	$dataPrazo		= DateTime::createFromFormat($system->config["data"]["datetimeSimplesFormat"], $dataPrazo);
	
	if ($dataPrazo < new \DateTime("now")) {
		die ('1'.\Zage\App\Util::encodeUrl('||'.htmlentities($tr->trans("A data de finalização da enquete não deve ser menor que a data atual!"))));
		$err	= 1;
	}
}

/** Tamanho **/
if ((empty($tamanho))) {
	$tamanho = null;
}else{
	$val = is_numeric($tamanho);
	if ($val == true){
		if($tamanho == 0){
			$tamanho = null;
		}
	}else{
		die ('1'.\Zage\App\Util::encodeUrl('||'.htmlentities($tr->trans("O tamanho deve conter apenas números!"))));
		$err	= 1;
	}
}


if ($err != null) {
	echo '1'.\Zage\App\Util::encodeUrl('||'.htmlentities($err));
 	exit;
}
 
#################################################################################
## Salvar no banco															#####
#################################################################################
try {
	
	if (isset($codEnquete) && (!empty($codEnquete))) {
 		if (!$oEnquete) $oEnquete	= new \Entidades\ZgappEnquetePergunta();
 		$novaEnquete = false;
 	}else{
 		$oEnquete	= new \Entidades\ZgappEnquetePergunta();
 		$novaEnquete = true;
 	}

 	$oOrganizacao	= $em->getRepository('Entidades\ZgadmOrganizacao')->findOneBy(array('codigo' => $system->getCodOrganizacao()));
 	$oTipo			= $em->getRepository('Entidades\ZgappEnquetePerguntaTipo')->findOneBy(array('codigo' => $codTipo));
 	
 	$oEnquete->setCodOrganizacao($oOrganizacao);
 	$oEnquete->setCodTipo($oTipo);
 	$oEnquete->setPergunta($pergunta);
 	$oEnquete->setDescricao($descricao);
 	$oEnquete->setDataCadastro(new \DateTime("now"));
 	$oEnquete->setDataPrazo($dataPrazo);
 	$oEnquete->setTamanho($tamanho);
 	
 	$em->persist($oEnquete);
 	
 	/**
 	 * Salvar os valores (Lista de valores )
 	 */
 	if ($valores) {
 		
 		$log->debug($valores);
 		$aValores	= explode(', ', $valores);
 		$log->debug($aValores);
 		/** Excluir **/
 		$infoValores		= $em->getRepository('Entidades\ZgappEnquetePerguntaValor')->findBy(array('codPergunta' => $codEnquete));
 		
 		for ($i = 0; $i < sizeof($infoValores); $i++) {
 			$log->debug('entrei');
 			if (!in_array($infoValores[$i]->getValor(), $aValores)) {
 				$log->debug($infoValores[$i]->getValor());
 				
 				
 				
 				try {
 					$em->remove($infoValores[$i]);
 				} catch (\Exception $e) {
 					$system->criaAviso(\Zage\App\Aviso\Tipo::ERRO,"Não foi possível excluir da lista de valores o valor: ".$infoValores[$i]->getValor()." Erro: ".$e->getMessage());
 					echo '1'.\Zage\App\Util::encodeUrl('||'.htmlentities($e->getMessage()));
 					exit;
 				}
 			}
 	
 		}
 	
 		/** Criar **/
 		for ($i = 0; $i < sizeof($aValores); $i++) {
 	
 			$infoValor		= $em->getRepository('Entidades\ZgappEnquetePerguntaValor')->findBy(array('codPergunta' => $codEnquete , 'valor' => $aValores[$i]));
 	
 			if (!$infoValor) {
 				$oValor		= new \Entidades\ZgappEnquetePerguntaValor();
 				$oValor->setcodPergunta($oEnquete);
 				$oValor->setValor($aValores[$i]);
 					
 				try {
 					$em->persist($oValor);
 				} catch (\Exception $e) {
 					$system->criaAviso(\Zage\App\Aviso\Tipo::ERRO,"Não foi possível cadastrar o valor: ".$aValores[$i]." Erro: ".$e->getMessage());
 					echo '1'.\Zage\App\Util::encodeUrl('||'.htmlentities($e->getMessage()));
 					exit;
 				}
 			}
 		}
 	}
 	
 	#################################################################################
 	## Gerar a notificação
 	#################################################################################
 	if ($novaEnquete == true){
		
 		$oRemetente		= $em->getReference('\Entidades\ZgsegUsuario',$system->getCodUsuario());
	 	$template		= $em->getRepository('\Entidades\ZgappNotificacaoTemplate')->findOneBy(array('template' => 'ENQUETE_CADASTRO'));
	 	$notificacao	= new \Zage\App\Notificacao(\Zage\App\Notificacao::TIPO_MENSAGEM_TEMPLATE, \Zage\App\Notificacao::TIPO_DEST_USUARIO);
	 	$notificacao->setAssunto("Responda a nova enquete!");
	 	$notificacao->setCodRemetente($oRemetente);
	 	
	 	for ($i = 0; $i < sizeof($formandos); $i++) {
	 		$notificacao->associaUsuario($formandos[$i]->getCodigo());
	 	}
	 	
	 	$notificacao->enviaEmail();
	 	$notificacao->enviaSistema();
	 	//$notificacao->setEmail("daniel.cassela@usinacaete.com"); # Se quiser mandar com cópia
	 	$notificacao->setCodTemplate($template);
	 	$notificacao->adicionaVariavel("PERGUNTA", $pergunta);
	 	$notificacao->adicionaVariavel("PRAZO", $data);
	
	 	$notificacao->salva();
 	}
 	 	
 	$em->flush();
 	$em->clear();
 	 	
} catch (\Exception $e) {
 	$system->criaAviso(\Zage\App\Aviso\Tipo::ERRO,$e->getMessage());
 	echo '1'.\Zage\App\Util::encodeUrl('||'.htmlentities($e->getMessage()));
 	exit;
}
 
//$system->criaAviso(\Zage\App\Aviso\Tipo::INFO,"Informações salvas com sucesso");
echo '0'.\Zage\App\Util::encodeUrl('|'.$oEnquete->getCodigo());