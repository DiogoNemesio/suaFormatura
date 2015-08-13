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
if (isset($_POST['codRifa'])) 		$codRifa			= \Zage\App\Util::antiInjection($_POST['codRifa']);

#################################################################################
## Limpar a variável de erro
#################################################################################
$err	= false;

#################################################################################
## Fazer validação dos campos
#################################################################################
/******* Rifa *********/
if (!isset($codRifa) || (empty($codRifa))) {
	die ('1'.\Zage\App\Util::encodeUrl('||'.htmlentities($tr->trans("Você não possui nenhuma rifa para realizar sorteio!"))));
}

$oRifa			= $em->getRepository('Entidades\ZgfmtRifa')->findOneBy(array('codigo' => $codRifa));
if (!$oRifa){
	die ('1'.\Zage\App\Util::encodeUrl('||'.htmlentities($tr->trans("O sistema não encontrou a rifa seleciona, por favor tente novamente em instantes!"))));
}

/******* Validar data/hora do sorteio *********/
if ($oRifa->getDataSorteio() > new \DateTime("now")){
	//die ('1'.\Zage\App\Util::encodeUrl('||'.htmlentities($tr->trans("Ainda não chegou a hora de realizar o sorteio desta rifa, confirme a data cadastrada e aguarde!!"))));
}

/******* Verificar se já existe um vencedor para a rifa *********/
if ($oRifa->getNumeroVencedor()){
	die ('1'.\Zage\App\Util::encodeUrl('||'.htmlentities($tr->trans("Está rifa já foi sorteada, atualize a tela!"))));
}

if ($err != null) {
	echo '1'.\Zage\App\Util::encodeUrl('||'.htmlentities($err));
	exit;
}

#################################################################################
## Salvar no banco
#################################################################################
try {
	
	$em->getConnection()->beginTransaction();
	/***********************
	 * Sortear
	 ***********************/
	$oNumero 	= $em->getRepository('Entidades\ZgfmtRifaNumero')->findBy(array('codRifa' => $codRifa));
	
	if (!$oNumero){
		die ('1'.\Zage\App\Util::encodeUrl('||'.htmlentities($tr->trans("Não foi vendido nenhum bilhete para esta rifa!"))));
	}
	
	$vencedor = array_rand($oNumero,1);
	
	/***********************
	* Salvar o vencedor no banco
	***********************/	
	$oRifa->setNumeroVencedor($oNumero[$vencedor]);
	$em->persist($oRifa);

	/********** Salvar as informações *********/
	try {
		$em->flush();
		$em->clear();	
	} catch (Exception $e) {
		$log->debug("Erro ao gerar o vencedor:". $e->getTraceAsString());
		throw new \Exception("Ops!! Não conseguimos processar sua solicitação. Por favor, tente novamente em instantes!! Caso o problema persista entre em contato com o nosso suporte especializado.");
	}
	
	/********** Criar notificação *********/	
	if (!empty($oRifa->getNumeroVencedor()->getEmail())) {
		### Gerar notificacao enviar email ###
		$oRemetente		= $em->getReference('\Entidades\ZgsegUsuario',$system->getCodUsuario());
		$template		= $em->getRepository('\Entidades\ZgappNotificacaoTemplate')->findOneBy(array('template' => 'RIFA_SORTEIO'));
		$notificacao	= new \Zage\App\Notificacao(\Zage\App\Notificacao::TIPO_MENSAGEM_TEMPLATE, \Zage\App\Notificacao::TIPO_DEST_ANONIMO);
		$notificacao->setAssunto("Sorteio da rifa ".$oRifa->getNome());
		$notificacao->setCodRemetente($oRemetente);
			
		//$notificacao->associaUsuario($oHistEmail->getCodUsuario()->getCodigo());
		//$notificacao->enviaSistema();
		$notificacao->enviaEmail();
		$notificacao->setEmail($oRifa->getNumeroVencedor()->getEmail());
		$notificacao->setCodTemplate($template);
			
		### Gerar confirmacao rifa ###
		$html .= '<tr style="background-color:#f9f9f9;padding:0; border:1px solid #ddd;text-align: center;">';
		$html .= '<td style="padding: 10px;">'.$oRifa->getNumeroVencedor()->getNome().'</td>';
		$html .= '<td style="padding: 10px;">'.$oRifa->getNumeroVencedor()->getCodigo().'</td>';
		$html .= '<td style="padding: 10px;">'.$oRifa->getPremio().'</td>';
		$html .= '</tr>';
			
		$notificacao->adicionaVariavel('NOME_RIFA'		,$oRifa->getNome());
		$notificacao->adicionaVariavel('PREMIO'			,$oRifa->getPremio());
		$notificacao->adicionaVariavel('NOME'			,$oRifa->getNumeroVencedor()->getNome());
		$notificacao->adicionaVariavel('EMAIL'			,$oRifa->getNumeroVencedor()->getEmail());
		$notificacao->adicionaVariavel('TELEFONE'		,$oRifa->getNumeroVencedor()->getTelefone());
		$notificacao->adicionaVariavel('HTML_TABLE'		,$html);
	
		$notificacao->salva();
	}
	
	/********** Salvar notificação *********/
	try {
		$em->flush();
		$em->getConnection()->commit();
		$em->clear();
	} catch (Exception $e) {
		$log->debug("Erro ao gerar o vencedor:". $e->getTraceAsString());
		throw new \Exception("Ops!! Não conseguimos processar sua solicitação. Por favor, tente novamente em instantes!! Caso o problema persista entre em contato com o nosso suporte especializado.");
	}
	
} catch (\Exception $e) {
	echo '1'.\Zage\App\Util::encodeUrl('||'.htmlentities($e->getMessage()));
	exit;
}

echo '0'.\Zage\App\Util::encodeUrl('|'.$oRifa->getNumeroVencedor()->getCodigo());