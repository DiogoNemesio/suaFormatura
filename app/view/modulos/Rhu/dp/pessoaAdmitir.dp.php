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
if (isset($_POST['codPessoa']))					$codPessoa				= \Zage\App\Util::antiInjection($_POST['codPessoa']);
if (isset($_POST['codFuncionario']))			$codFuncionario			= \Zage\App\Util::antiInjection($_POST['codFuncionario']);
if (isset($_POST['chapa']))						$chapa					= \Zage\App\Util::antiInjection($_POST['chapa']);
if (isset($_POST['jornada']))					$jornada				= \Zage\App\Util::antiInjection($_POST['jornada']);
if (isset($_POST['dataAdmissao']))				$dataAdmissao			= \Zage\App\Util::antiInjection($_POST['dataAdmissao']);
if (isset($_POST['codTipo']))					$codTipo				= \Zage\App\Util::antiInjection($_POST['codTipo']);
if (isset($_POST['codTipoAdmissao']))			$codTipoAdmissao		= \Zage\App\Util::antiInjection($_POST['codTipoAdmissao']);
if (isset($_POST['codMotivoAdmissao']))			$codMotivoAdmissao		= \Zage\App\Util::antiInjection($_POST['codMotivoAdmissao']);
if (isset($_POST['codFuncao']))					$codFuncao				= \Zage\App\Util::antiInjection($_POST['codFuncao']);
if (isset($_POST['codSituacao']))				$codSituacao			= \Zage\App\Util::antiInjection($_POST['codSituacao']);
if (isset($_POST['dataPrazoContr']))			$dataPrazoContr			= \Zage\App\Util::antiInjection($_POST['dataPrazoContr']);
if (isset($_POST['prazoExp']))					$prazoExp				= \Zage\App\Util::antiInjection($_POST['prazoExp']);
if (isset($_POST['salario']))					$salario				= \Zage\App\Util::antiInjection($_POST['salario']);
if (isset($_POST['dataDemissao']))				$dataDemissao			= \Zage\App\Util::antiInjection($_POST['dataDemissao']);
#################################################################################
## Limpar a variável de erro
#################################################################################
$err	= false;

#################################################################################
## Fazer validação dos campos
#################################################################################
/** Pessoa **/
if ((empty($codPessoa))) {
	$system->criaAviso(\Zage\App\Aviso\Tipo::ERRO,$tr->trans("É obrigatório ter selecionado uma PESSOA"));
	$err	= 1;
}

/** Chapa **/
if ((empty($chapa))) {
	$system->criaAviso(\Zage\App\Aviso\Tipo::ERRO,$tr->trans("Campo CHAPA é obrigatório"));
	$err	= 1;
}

/** Jornada **/
if ((empty($jornada))) {
	$system->criaAviso(\Zage\App\Aviso\Tipo::ERRO,$tr->trans("Campo JORNADA é obrigatório"));
	$err	= 1;
}

/** DataAdmissao  **/
if ((empty($dataAdmissao))) {
	$system->criaAviso(\Zage\App\Aviso\Tipo::ERRO,$tr->trans("Campo DATA ADMISSÃO é obrigatório"));
	$err	= 1;
}

/** CodTipo  **/
if ((empty($codTipo))) {
	$system->criaAviso(\Zage\App\Aviso\Tipo::ERRO,$tr->trans("Campo TIPO é obrigatório"));
	$err	= 1;
}

/** TipoAdmissao  **/
if ((empty($codTipoAdmissao))) {
	$system->criaAviso(\Zage\App\Aviso\Tipo::ERRO,$tr->trans("Campo TIPO ADMISSÃO é obrigatório"));
	$err	= 1;
}

/** MotivoAdmissao  **/
if ((empty($codMotivoAdmissao))) {
	$system->criaAviso(\Zage\App\Aviso\Tipo::ERRO,$tr->trans("Campo MOTIVO ADMISSÂO é obrigatório"));
	$err	= 1;
}

/** Funcao  **/
if ((empty($codFuncao))) {
	$system->criaAviso(\Zage\App\Aviso\Tipo::ERRO,$tr->trans("Campo FUNÇÂO é obrigatório"));
	$err	= 1;
}

/** Situacao  **/
if ((empty($codSituacao))) {
	$system->criaAviso(\Zage\App\Aviso\Tipo::ERRO,$tr->trans("Campo SITUAÇÂO é obrigatório"));
	$err	= 1;
}

/** Salario  **/
if ((empty($salario))) {
	$system->criaAviso(\Zage\App\Aviso\Tipo::ERRO,$tr->trans("Campo SALARIO é obrigatório"));
	$err	= 1;
}

if (!empty($dataAdmissao) && !empty($dataPrazoContr)) {
	$dataPrazoContr = date('d/m/Y', strtotime("+".$dataPrazoContr." days"));
}

if (!empty($dataAdmissao) && !empty($prazoExp)) {
	$prazoExp = date('d/m/Y', strtotime("+".$prazoExp." days"));
}


exit;
/*
$salario = 1.10;
/** Ajustando o valor para o formato do banco **
$salario	= \Zage\App\Util::toMysqlNumber($salario);
if (!$salario)	$salario	= 0;
*/
if ($err != null) {
	echo '1'.\Zage\App\Util::encodeUrl('||'.htmlentities($err));
 	exit;
}
 
#################################################################################
## Salvar no banco
#################################################################################
try {
	#################################################################################
	## Resgata os objetos (chave estrangeiras)
	#################################################################################
	$oEmpresa		 = $em->getRepository('Entidades\ZgadmEmpresa')->findOneBy(array('codigo' => $system->getCodEmpresa()));
	$oPessoa		 = $em->getRepository('Entidades\ZgrhuPessoa')->findOneBy(array('codigo' => $codPessoa));
	$oFuncao		 = $em->getRepository('Entidades\ZgrhuFuncionarioFuncao')->findOneBy(array('codigo' => $codFuncao));
	$oSituacao		 = $em->getRepository('Entidades\ZgrhuFuncionarioSituacao')->findOneBy(array('codigo' => $codSituacao));
	$oTipo			 = $em->getRepository('Entidades\ZgrhuFuncionarioTipo')->findOneBy(array('codigo' => $codTipo));
	$oTipoAdmissao	 = $em->getRepository('Entidades\ZgrhuFuncionarioAdmissaoTipo')->findOneBy(array('codigo' => $codTipoAdmissao));
	$oMotivoAdmissao = $em->getRepository('Entidades\ZgrhuFuncionarioAdmissaoMotivo')->findOneBy(array('codigo' => $codMotivoAdmissao));
	
	#################################################################################
	## Configurações da data
	#################################################################################
	if (!empty($dataAdmissao)) {
		$dataAdmissao		= DateTime::createFromFormat($system->config["data"]["dateFormat"], $dataAdmissao);
	}else{
		$dataAdmissao		= null;
	}
	
	if (!empty($dataDemissao)) {
		$dataDemissao		= DateTime::createFromFormat($system->config["data"]["dateFormat"], $dataDemissao);
	}else{
		$dataDemissao		= null;
	}
	
	if (!empty($dataPrazoContr)) {
		$dataPrazoContr		= DateTime::createFromFormat($system->config["data"]["dateFormat"], $dataPrazoContr);
	}else{
		$dataPrazoContr		= null;
	}
	
	if (!empty($prazoExp)) {
		$prazoExp			= DateTime::createFromFormat($system->config["data"]["dateFormat"], $prazoExp);
	}else{
		$prazoExp			= null;
	}
	#################################################################################
	
	if (isset($codFuncionario) && (!empty($codFuncionario))) {
 		$oAdmitir	= $em->getRepository('Entidades\ZgrhuFuncionario')->findOneBy(array('codigo' => $codFuncionario));
 		if (!$oAdmitir) $oAdmitir	= new \Entidades\ZgrhuFuncionario();
 	}else{
 		$oAdmitir	= new \Entidades\ZgrhuFuncionario();
 	}
 	
 	$oAdmitir->setCodEmpresa($oEmpresa);
 	$oAdmitir->setCodPessoa($oPessoa);
 	$oAdmitir->setCodFuncao($oFuncao);
 	$oAdmitir->setCodSituacao($oSituacao);
 	$oAdmitir->setCodTipo($oTipo);
 	$oAdmitir->setChapa($chapa);
 	$oAdmitir->setJornada($jornada);
 	$oAdmitir->setDataAdmissao($dataAdmissao);
 	$oAdmitir->setCodTipoAdmissao($oTipoAdmissao);
 	$oAdmitir->setCodMotivoAdmissao($oMotivoAdmissao);
 	$oAdmitir->setPrazoExperiencia($prazoExp);
 	$oAdmitir->setPrazoContraDeterminado($dataPrazoContr);
 	$oAdmitir->setSalario($salario);
 	$oAdmitir->setDataDemissao($dataDemissao);
 	
 	$em->persist($oAdmitir);
 	$em->flush();
 	$em->detach($oAdmitir);
 		
} catch (\Exception $e) {
 	$system->criaAviso(\Zage\App\Aviso\Tipo::ERRO,$e->getMessage());
 	echo '1'.\Zage\App\Util::encodeUrl('||'.htmlentities($e->getMessage()));
 	exit;
}
 
$system->criaAviso(\Zage\App\Aviso\Tipo::INFO,"Informações salvas com sucesso");
echo '0'.\Zage\App\Util::encodeUrl('|'.$oAdmitir->getCodigo());