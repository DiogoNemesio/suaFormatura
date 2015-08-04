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
		$em->getConnection()->commit();
		
	} catch (Exception $e) {
		$log->debug("Erro ao salvar o usuário:". $e->getTraceAsString());
		throw new \Exception("Ops!! Não conseguimos processar sua solicitação. Por favor, tente novamente em instantes!! Caso o problema persista entre em contato com o nosso suporte especializado.");
	}	

} catch (\Exception $e) {
	$system->criaAviso(\Zage\App\Aviso\Tipo::ERRO,$e->getMessage());
	echo '1'.\Zage\App\Util::encodeUrl('||'.htmlentities($e->getMessage()));
	exit;
}

echo '0'.\Zage\App\Util::encodeUrl('|'.$oRifaNum->getCodVenda()->getCodigo());