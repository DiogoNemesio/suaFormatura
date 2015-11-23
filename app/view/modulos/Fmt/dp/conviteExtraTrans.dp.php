<?php
use Entidades\ZgfinSeqCodTransacao;
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
if (isset($_POST['codFormando']))		$codFormando		= \Zage\App\Util::antiInjection($_POST['codFormando']);

if (isset($_POST['codEvento']))			$codEvento			= \Zage\App\Util::antiInjection($_POST['codEvento']);
if (isset($_POST['quantConv']))			$quantConv			= \Zage\App\Util::antiInjection($_POST['quantConv']);

#################################################################################
## Limpar a variável de erro
#################################################################################
$err	= false;
#################################################################################
## Fazer validação dos campos
#################################################################################
/** FORMANDO **/
if (!isset($codFormando) || empty($codFormando)) {
	$system->criaAviso(\Zage\App\Aviso\Tipo::ERRO,"Selecione o formando.");
	$err	= 1;
}

/** VALIDAR SE AS QUANTIDADES ESTÃO DE ACORDO COM O LIMITE **/
if(isset($codEvento) && !empty($codEvento)) {
	//Resgatar as configurações do tipo de evento
	$oEventoConf = $em->getRepository('Entidades\ZgfmtConviteExtraEventoConf')->findOneBy(array('codOrganizacao' => $system->getCodOrganizacao() , 'codEvento' => $codEvento ));
	
	if (!$oEventoConf){
		$system->criaAviso(\Zage\App\Aviso\Tipo::ERRO,"Ops!! Não encontramos as configurações do convite extra. Caso o problema persista entre em contato com o nosso suporte.");
		$err	= 1;
	}else{
		$quantConv	= (int) $quantConv;
		//Resgatar a quantidade de convites disponíveis para esse evento
		$qtdeConvDis	= 10; /* \Zage\Fmt\Convite::qtdeConviteDispFormando($codFormando, $oEventoConf->getCodEvento());*/
		if ($qtdeConvDis < $quantConv){
			$system->criaAviso(\Zage\App\Aviso\Tipo::ERRO,"A quantidade para evento ".$oEventoConf->getcodEvento()->getCodTipoEvento()->getDescricao()." está maior que o disponível.");
			$err	= 1;
		}
	}
}else{
	$system->criaAviso(\Zage\App\Aviso\Tipo::ERRO,"Nenhum evento foi selecionado.");
	$err	= 1;
}

if ($err != null) {
	echo '1'.\Zage\App\Util::encodeUrl('||'.htmlentities($err));
 	exit;
}
 
#################################################################################
## Salvar no banco
#################################################################################
try {
	//$em->getConnection()->beginTransaction();
	#################################################################################
	## RESGATAR OBJETOS
	###################################################s##############################
	$oOrigem		= $em->getRepository('Entidades\ZgfinPessoa')->findOneBy(array('codigo' => \Zage\Fmt\Convite::getCodigoUsuarioPessoa()));
	$oFormando		= $em->getRepository('Entidades\ZgfinPessoa')->findOneBy(array('codigo' => $codFormando));
	//$oOrg			= $em->getRepository('Entidades\ZgadmOrganizacao')->findOneBy(array('codigo' => $system->getcodOrganizacao()));
	
 	//Resgatar as configurações do tipo de evento
	$oEventoConf = $em->getRepository('Entidades\ZgfmtConviteExtraEventoConf')->findOneBy(array('codOrganizacao' => $system->getCodOrganizacao() , 'codEvento' => $codEvento));
	
	#################################################################################
 	## SETAR VALORES
 	#################################################################################
	$oConviteTrans	= new \Entidades\ZgfmtConviteExtraTransf();
	
 	$oConviteTrans->setCodEvento($oEventoConf->getCodEvento());
 	$oConviteTrans->setCodFormandoOrigem($oOrigem);
 	$oConviteTrans->setCodFormandoDestino($oFormando);
 	$oConviteTrans->setQuantidade($quantConv);
 	$oConviteTrans->setDataCadastro(new DateTime(now));
 	
 	$em->persist($oConviteTrans);
 	$em->flush();
 	$em->clear();
	
} catch (\Exception $e) {
 	$system->criaAviso(\Zage\App\Aviso\Tipo::ERRO,$e->getMessage());
 	echo '1'.\Zage\App\Util::encodeUrl('||'.htmlentities($e->getMessage()));
 	exit;
}
 

echo '0'.\Zage\App\Util::encodeUrl('|'.$oConviteTrans->getCodigo());
