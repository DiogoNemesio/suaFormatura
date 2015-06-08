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
if (isset($_POST['codOrganizacao']))	$codOrganizacao		= \Zage\App\Util::antiInjection($_POST['codOrganizacao']);
if (isset($_POST['ident']))				$ident				= \Zage\App\Util::antiInjection($_POST['ident']);
if (isset($_POST['nome'])) 				$nome				= \Zage\App\Util::antiInjection($_POST['nome']);
if (isset($_POST['instituicao']))		$instituicao		= \Zage\App\Util::antiInjection($_POST['instituicao']);

#################################################################################
## Limpar a variável de erro
#################################################################################
$err	= false;

#################################################################################
## Fazer validação dos campos
#################################################################################
/******* IDENTIFICAÇÃO *********/
if (!isset($ident) || (empty($ident))) {
	$system->criaAviso(\Zage\App\Aviso\Tipo::ERRO,$tr->trans("A Identificação deve ser preenchida!"));
	$err	= 1;
}

if ((!empty($ident)) && (strlen($ident) > 100)) {
	$system->criaAviso(\Zage\App\Aviso\Tipo::ERRO,$tr->trans("A Identificação não deve conter mais de 60 caracteres!"));
	$err	= 1;
}

$oOrganizacao	= $em->getRepository('Entidades\ZgadmOrganizacao')->findOneBy(array('identificacao' => $ident));

if($oOrganizacao != null && ($oOrganizacao->getCodigo() != $codOrganizacao)){
	$system->criaAviso(\Zage\App\Aviso\Tipo::ERRO,$tr->trans("Está identificação já existe!"));
	$err	= 1;
}

/******* NOME *********/
if (!isset($nome) || (empty($nome))) {
	$system->criaAviso(\Zage\App\Aviso\Tipo::ERRO,$tr->trans("O Nome Completo deve ser preenchido!"));
	$err	= 1;
}

if ((!empty($nome)) && (strlen($nome) > 100)) {
	$system->criaAviso(\Zage\App\Aviso\Tipo::ERRO,$tr->trans("O Nome não deve conter mais de 100 caracteres!"));
	$err	= 1;
}

/******* INSTITUIÇÃO *********/
if (!isset($instituicao) || (empty($instituicao))) {
	$system->criaAviso(\Zage\App\Aviso\Tipo::ERRO,$tr->trans("A instituição deve ser preenchida!"));
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
	if (isset($codOrganizacao) && (!empty($codOrganizacao))){
 		$oOrganizacao	= $em->getRepository('Entidades\ZgadmOrganizacao')->findOneBy(array('codigo' => $codOrganizacao));
 		
 		if (!$oOrganizacao) {
 			$oOrganizacao	= new \Entidades\ZgadmOrganizacao();
 			$oOrganizacao->setDataCadastro(new \DateTime("now"));
 		}
 	}else{
 		$oOrganizacao	= new \Entidades\ZgadmOrganizacao();
 		$oOrganizacao->setDataCadastro(new \DateTime("now"));
 	}
 	 	
 	$oTipoOrganizacao	= $em->getRepository('Entidades\ZgadmOrganizacaoTipo')->findOneBy(array('codigo' => 1));
 	$oCodStatus			= $em->getRepository('Entidades\ZgadmOrganizacaoStatusTipo')->findOneBy(array('codigo' => 1));
 	$oInstituicao		= $em->getRepository('Entidades\ZgfmtInstituicao')->findOneBy(array('codigo' => $instituicao));
 	
 	$oOrganizacao->setIdentificacao($ident);
 	$oOrganizacao->setNome($nome);
 	$oOrganizacao->setCodTipo($oTipoOrganizacao);
 	$oOrganizacao->setCodStatus($oCodStatus);
 	$oOrganizacao->setCodInstituicao($oInstituicao);
 	
 	$em->persist($oOrganizacao);
 	
 	#################################################################################
 	## ASSOCIACAO ORGANIZAÇÃO ADMINISTRADOR
 	#################################################################################
 	$orgAdm		= $em->getRepository('Entidades\ZgadmOrganizacaoAdm')->findOneBy(array('codOrganizacao' => $oOrganizacao->getCodigo(), 'codOrganizacaoPai' => $system->getCodOrganizacao()));
 	
 	if (!$codOrganizacao){
 		$oOrgAdm = new \Entidades\ZgadmOrganizacaoAdm();
 		
 		$oOrg 	 = $em->getRepository('Entidades\ZgadmOrganizacao')->findOneBy(array('codigo' => $system->getCodOrganizacao()));
 		
 		$oOrgAdm->setCodOrganizacao($oOrganizacao);
 		$oOrgAdm->setCodOrganizacaoPai($oOrg);
 		
 		try {
 				$em->persist($oOrgAdm);
 			} catch (\Exception $e) {
 				$system->criaAviso(\Zage\App\Aviso\Tipo::ERRO,"Não foi possível associar as organizações"." Erro: ".$e->getMessage());
 				echo '1'.\Zage\App\Util::encodeUrl('||'.htmlentities($e->getMessage()));
 				exit;
 			}
 	}
	
 	#################################################################################
 	## Salvar as informações
 	#################################################################################
 	try {
 		$em->flush();
 		$em->clear();
 	} catch (Exception $e) {
 		$log->debug("Erro ao salvar o Organização:". $e->getTraceAsString());
 		throw new \Exception("Erro ao salvar a Organização. Uma mensagem de depuração foi salva em log, entre em contato com os administradores do sistema !!!");
 	}
 	
 	
} catch (\Exception $e) {
 	$system->criaAviso(\Zage\App\Aviso\Tipo::ERRO,$e->getMessage());
 	echo '1'.\Zage\App\Util::encodeUrl('||'.htmlentities($e->getMessage()));
 	exit;
}
 
$system->criaAviso(\Zage\App\Aviso\Tipo::INFO,"Informações salvas com sucesso");
echo '0'.\Zage\App\Util::encodeUrl('|'.$oOrganizacao->getCodigo());