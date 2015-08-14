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
if (isset($_POST['rifa'])) 				$rifa			= \Zage\App\Util::antiInjection($_POST['rifa']);
if (isset($_POST['nome'])) 				$nome			= \Zage\App\Util::antiInjection($_POST['nome']);
if (isset($_POST['email']))				$email			= \Zage\App\Util::antiInjection($_POST['email']);
if (isset($_POST['telefone']))			$telefone		= \Zage\App\Util::antiInjection($_POST['telefone']);
if (isset($_POST['quantidade'])) 		$quantidade		= \Zage\App\Util::antiInjection($_POST['quantidade']);

#################################################################################
## Limpar a variável de erro
#################################################################################
$err	= false;

#################################################################################
## Fazer validação dos campos
#################################################################################
/******* Nome *********/
if (!isset($rifa) || (empty($rifa))) {
	die ('1'.\Zage\App\Util::encodeUrl('||'.htmlentities($tr->trans("Você não possui nenhuma rifa para efetuar venda!"))));
}

/******* Nome *********/
if (!isset($nome) || (empty($nome))) {
	die ('1'.\Zage\App\Util::encodeUrl('||'.htmlentities($tr->trans("O nome do comprador deve ser preenchido!"))));
}elseif ((!empty($nome)) && (strlen($nome) > 100)) {
	die ('1'.\Zage\App\Util::encodeUrl('||'.htmlentities($tr->trans("O nome do comprador não deve conter mais de 100 caracteres!"))));
}

/******* Email *********/
if (!empty($email)) {
	if ((!empty($email)) && (strlen($email) > 100)) {
		die ('1'.\Zage\App\Util::encodeUrl('||'.htmlentities($tr->trans("O email do comprador não deve conter mais de 200 caracteres!"))));
	}elseif(\Zage\App\Util::validarEMail($email) == false){
		die ('1'.\Zage\App\Util::encodeUrl('||'.htmlentities($tr->trans("O email do comprador é inválido! Por favor, verifique."))));
	}
}

/******* Telefone *********/
if (!empty($telefone)) {
	if ((!empty($telefone)) && (strlen($telefone) > 11)) {
		die ('1'.\Zage\App\Util::encodeUrl('||'.htmlentities($tr->trans("O número do telefone do comprador é inválido! Por favor, verifique."))));
	}elseif ((!empty($telefone)) && (strlen($telefone) < 10)) {
		die ('1'.\Zage\App\Util::encodeUrl('||'.htmlentities($tr->trans("O número do telefone do comprador é inválido! Por favor, verifique."))));
	}
}

/******* Quantidade *********/
if (!isset($quantidade) || (empty($quantidade))) {
	die ('1'.\Zage\App\Util::encodeUrl('||'.htmlentities($tr->trans("A quantidade de rifas deve ser preenchida!"))));
}elseif (is_numeric ($quantidade) == false){
	die ('1'.\Zage\App\Util::encodeUrl('||'.htmlentities($tr->trans("A quantidade de rifas deve conter apenas números!"))));
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
	 * Salvar no banco
	 ***********************/
	//RESGATAR SEQUENCIAL DA VENDA
	$codVenda = \Zage\Adm\Sequencial::proximoValor(ZgfmtRifaVendaSequencial);
	
	$oUsuario		= $em->getRepository('Entidades\ZgsegUsuario')->findOneBy(array('codigo' => $system->getCodUsuario()));
	$oRifa			= $em->getRepository('Entidades\ZgfmtRifa')->findOneBy(array('codigo' => $rifa));
	$oCodVenda		= $em->getRepository('Entidades\ZgfmtRifaVendaSequencial')->findOneBy(array('codigo' => $codVenda));
	
	//Validar data
	if ($oRifa->getDataSorteio() <  new \DateTime("now")){
		die ('1'.\Zage\App\Util::encodeUrl('||'.htmlentities($tr->trans("O período de venda desta rifa já foi encerrado."))));
	}
	
	
	for ($i = 0; $i < $quantidade; $i++) {
		$oRifaNum		= new \Entidades\ZgfmtRifaNumero();
		$rifaSemaforo	= \Zage\Adm\Semaforo::proximoValor($system->getCodOrganizacao(), "RIFA_".$rifa);
		$log->debug('entrei1');
		$oRifaNum->setCodFormando($oUsuario);
		$oRifaNum->setCodRifa($oRifa);
		$oRifaNum->setCodVenda($oCodVenda);
		$oRifaNum->setData(new \DateTime("now"));
		$oRifaNum->setNome($nome);
		$oRifaNum->setEmail($email);
		$oRifaNum->setTelefone($telefone);
		$oRifaNum->setNumero($rifaSemaforo);
		
		$em->persist($oRifaNum);
	}
		
	/********** Salvar as informações *********/
	try {		
		$em->flush();
		$em->clear();
		
	} catch (Exception $e) {
		$log->debug("Erro ao salvar o usuário:". $e->getTraceAsString());
		throw new \Exception("Ops!! Não conseguimos processar sua solicitação. Por favor, tente novamente em instantes!! Caso o problema persista entre em contato com o nosso suporte especializado.");
	}

	/********** Enviar notificação *********/
	if (!empty($email)) {
		### Gerar notificacao enviar email ###
		$oRemetente		= $em->getReference('\Entidades\ZgsegUsuario',$system->getCodUsuario());
		$template		= $em->getRepository('\Entidades\ZgappNotificacaoTemplate')->findOneBy(array('template' => 'RIFA_CONF_COMPRA'));
		$notificacao	= new \Zage\App\Notificacao(\Zage\App\Notificacao::TIPO_MENSAGEM_TEMPLATE, \Zage\App\Notificacao::TIPO_DEST_ANONIMO);
		$notificacao->setAssunto("Compra da Rifa com sucesso.");
		$notificacao->setCodRemetente($oRemetente);
			
		//$notificacao->associaUsuario($oHistEmail->getCodUsuario()->getCodigo());
		//$notificacao->enviaSistema();
		$notificacao->enviaEmail();
		$notificacao->setEmail($email);
		$notificacao->setCodTemplate($template);
			
		### Gerar confirmacao rifa ###
		$infoVenda = $em->getRepository('Entidades\ZgfmtRifaNumero')->findBy(array('codVenda' => $codVenda));
		$total = 0;
		for ($i = 0; $i < sizeof($infoVenda); $i++) {
			$total = $infoVenda[$i]->getCodRifa()->getValorUnitario() + $total;
			$linha = $i + 1;
				
			$html .= '<tr style="background-color:#f9f9f9;padding:0; border:1px solid #ddd;text-align: center;">';
			$html .= '<td style="padding: 10px;">'.$linha.'</td>';
			$html .= '<td style="padding: 10px;">'.$infoVenda[$i]->getCodRifa()->getNome().'</td>';
			$html .= '<td style="padding: 10px;">'.$infoVenda[$i]->getNumero().'</td>';
			$html .= '<td style="padding: 10px;">'.$infoVenda[$i]->getCodRifa()->getValorUnitario().'</td>';
			$html .= '</tr>';
		}
			
		$notificacao->adicionaVariavel('COD_RIFA'		,$rifa);
		$notificacao->adicionaVariavel('NOME_RIFA'		,$infoVenda[0]->getCodRifa()->getNome());
		$notificacao->adicionaVariavel('PREMIO'			,$infoVenda[0]->getCodRifa()->getPremio());
		$notificacao->adicionaVariavel('DATA_SORTEIO'	,$infoVenda[0]->getCodRifa()->getDataSorteio()->format($system->config["data"]["datetimeSimplesFormat"]));
		$notificacao->adicionaVariavel('LOCAL_SORTEIO'	,$infoVenda[0]->getCodRifa()->getLocalSorteio());
		$notificacao->adicionaVariavel('ORGANIZACAO'	,$infoVenda[0]->getCodRifa()->getCodOrganizacao()->getNome());
		$notificacao->adicionaVariavel('COD_VENDA'		,$codVenda);
		$notificacao->adicionaVariavel('TOTAL'			,$total);
		$notificacao->adicionaVariavel('DATA_VENDA'		,$infoVenda[0]->getData()->format($system->config["data"]["datetimeSimplesFormat"]));
		$notificacao->adicionaVariavel('NOME'			,$infoVenda[0]->getNome());
		$notificacao->adicionaVariavel('EMAIL'			,$infoVenda[0]->getEmail());
		$notificacao->adicionaVariavel('TELEFONE'		,$infoVenda[0]->getTelefone());
		$notificacao->adicionaVariavel('HTML_TABLE'		,$html);
			
		$notificacao->salva();
	}
	
	$em->flush();
	$em->getConnection()->commit();
	
	/********** Salvar as informações *******
	try {
		$em->flush();
		$em->getConnection()->commit();
	
	} catch (Exception $e) {
		$log->debug("Erro ao salvar o usuário:". $e->getTraceAsString());
		throw new \Exception("Ops!! Não conseguimos processar sua solicitação. Por favor, tente novamente em instantes!! Caso o problema persista entre em contato com o nosso suporte especializado.");
	}
**/
} catch (\Exception $e) {
	$system->criaAviso(\Zage\App\Aviso\Tipo::ERRO,$e->getMessage());
	echo '1'.\Zage\App\Util::encodeUrl('||'.htmlentities($e->getMessage()));
	exit;
}

echo '0'.\Zage\App\Util::encodeUrl('|'.$oRifaNum->getCodVenda()->getCodigo());
