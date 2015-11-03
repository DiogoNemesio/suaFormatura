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
## Variáveis globais
#################################################################################
global $em,$system,$tr,$log;

#################################################################################
## Resgata os parâmetros passados pelo formulario
#################################################################################
if (isset($_POST['codItemSel']))		$codItemSel			= \Zage\App\Util::antiInjection($_POST['codItemSel']);
if (isset($_POST['vencimento'])) 		$aVenc				= \Zage\App\Util::antiInjection($_POST['vencimento']);
if (isset($_POST['valor'])) 			$aValor				= \Zage\App\Util::antiInjection($_POST['valor']);
if (isset($_POST['valorJuros'])) 		$aValorJuros		= \Zage\App\Util::antiInjection($_POST['valorJuros']);
if (isset($_POST['valorMora'])) 		$aValorMora			= \Zage\App\Util::antiInjection($_POST['valorMora']);
if (isset($_POST['valorDesconto'])) 	$aValorDesconto		= \Zage\App\Util::antiInjection($_POST['valorDesconto']);
if (isset($_POST['tipoMidia'])) 		$tipoMidia			= \Zage\App\Util::antiInjection($_POST['tipoMidia']);
if (isset($_POST['instrucao'])) 		$instrucao			= \Zage\App\Util::antiInjection($_POST['instrucao']);
if (isset($_POST['email'])) 			$email				= \Zage\App\Util::antiInjection($_POST['email']);

#################################################################################
## Verificar parâmetro obrigatório
#################################################################################
if (!isset($codContaSel)) \Zage\App\Erro::halt('Falta de Parâmetros 2');
if (!is_array($codContaSel)) \Zage\App\Erro::halt('Parâmetros incorretos');

#################################################################################
## Verificar parâmetros
#################################################################################
if (!is_array($aVenc) 			&& sizeof($codContaSel) > 1)  \Zage\App\Erro::halt('Parâmetro 2 incorreto');
if (!is_array($aValor) 			&& sizeof($codContaSel) > 1)  \Zage\App\Erro::halt('Parâmetro 3 incorreto');
if (!is_array($aValorJuros)		&& sizeof($codContaSel) > 1)  \Zage\App\Erro::halt('Parâmetro 4 incorreto');
if (!is_array($aValorMora) 		&& sizeof($codContaSel) > 1)  \Zage\App\Erro::halt('Parâmetro 5 incorreto');
if (!is_array($aValorDesconto) 	&& sizeof($codContaSel) > 1)  \Zage\App\Erro::halt('Parâmetro 6 incorreto');

#################################################################################
## Corrige os campos que não são arrays
#################################################################################
if (!is_array($aVenc) 			)  $aVenc			= array($codContaSel[0] => $aVenc);
if (!is_array($aValor) 			)  $aValor			= array($codContaSel[0] => $aValor);
if (!is_array($aValorJuros)		)  $aValorJuros		= array($codContaSel[0] => $aValorJuros);
if (!is_array($aValorMora) 		)  $aValorMora		= array($codContaSel[0] => $aValorMora);
if (!is_array($aValorDesconto) 	)  $aValorDesconto	= array($codContaSel[0] => $aValorDesconto);

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
}elseif ((!empty($nome)) && (strlen($nome) > 100)) {
	$system->criaAviso(\Zage\App\Aviso\Tipo::ERRO,$tr->trans("O Nome não deve conter mais de 100 caracteres!"));
	$err	= 1;
}

/******* INSTITUIÇÃO *********/
if (!isset($instituicao) || (empty($instituicao))) {
	$system->criaAviso(\Zage\App\Aviso\Tipo::ERRO,$tr->trans("A instituição de ensino deve ser preenchida!"));
	$err	= 1;
}

/******* CURSO *********/
if (!isset($curso) || (empty($curso))) {
	$system->criaAviso(\Zage\App\Aviso\Tipo::ERRO,$tr->trans("O curso deve ser preenchido!"));
	$err	= 1;
}

/******* CIDADE *********/
if (!isset($cidade) || (empty($cidade))) {
	$system->criaAviso(\Zage\App\Aviso\Tipo::ERRO,$tr->trans("A cidade de realizacão da formatura deve ser preenchida!"));
	$err	= 1;
}

/******* DATA DE CONCLUSAO *********/
if (!isset($dataConclusao) || (empty($dataConclusao))) {
	$system->criaAviso(\Zage\App\Aviso\Tipo::ERRO,$tr->trans("A data prevista de conclusão deve ser preenchida!"));
	$err	= 1;
}

/******* CONTRATO *********/
if (!isset($codPlano) || (empty($codPlano))) {
	$system->criaAviso(\Zage\App\Aviso\Tipo::ERRO,$tr->trans("O Plano deve ser selecionado!"));
	$err	= 1;
}else{
	$oPlano		= $em->getRepository('\Entidades\ZgadmPlano')->findOneBy(array('codigo' => $codPlano));
	if (!$oPlano) {
		$system->criaAviso(\Zage\App\Aviso\Tipo::ERRO,$tr->trans("Plano não encontrado!"));
		$err	= 1;
	}
}

$valorDesconto	= \Zage\App\Util::toMysqlNumber($valorDesconto);
$pctDesconto	= \Zage\App\Util::toMysqlNumber($pctDesconto/100);


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
 		$oOrgFmt		= $em->getRepository('Entidades\ZgfmtOrganizacaoFormatura')->findOneBy(array('codOrganizacao' => $codOrganizacao));
 		
 		if (!$oOrganizacao) {
 			$oOrganizacao	= new \Entidades\ZgadmOrganizacao();
 			$oOrganizacao->setDataCadastro(new \DateTime("now"));
 			$oOrgFmt		= new \Entidades\ZgfmtOrganizacaoFormatura();
 		}
 	}else{
 		$oOrganizacao	= new \Entidades\ZgadmOrganizacao();
 		$oOrganizacao->setDataCadastro(new \DateTime("now"));
 		$oOrgFmt		= new \Entidades\ZgfmtOrganizacaoFormatura();
 	}
 	 
 	#################################################################################
 	## ORGANIZAÇÃO
 	#################################################################################
 	$oTipoOrganizacao	= $em->getRepository('Entidades\ZgadmOrganizacaoTipo')->findOneBy(array('codigo' => FMT));
 	$oCodStatus			= $em->getRepository('Entidades\ZgadmOrganizacaoStatusTipo')->findOneBy(array('codigo' => A));
 	
 	$oOrganizacao->setIdentificacao($ident);
 	$oOrganizacao->setNome($nome);
 	$oOrganizacao->setCodTipo($oTipoOrganizacao);
 	$oOrganizacao->setCodStatus($oCodStatus);
 	
 	$em->persist($oOrganizacao);
 	
 	#################################################################################
 	## ORGANIZAÇÃO FORMATURA
 	#################################################################################
 	$oInstituicao		= $em->getRepository('Entidades\ZgfmtInstituicao')->findOneBy(array('codigo' => $instituicao));
 	$oCurso				= $em->getRepository('Entidades\ZgfmtCurso')->findOneBy(array('codigo' => $curso));
 	$oCidade			= $em->getRepository('Entidades\ZgadmCidade')->findOneBy(array('codigo' => $cidade));
 	
 	if (!empty($dataConclusao)) {
 		$dtCon		= DateTime::createFromFormat($system->config["data"]["dateFormat"], $dataConclusao);
 	}else{
 		$dtCon		= null;
 	}
 	
 	$oOrgFmt->setCodOrganizacao($oOrganizacao);
 	$oOrgFmt->setCodInstituicao($oInstituicao);
 	$oOrgFmt->setCodCurso($oCurso);
 	$oOrgFmt->setCodCidade($oCidade);
 	$oOrgFmt->setDataConclusao($dtCon);
 	
 	$em->persist($oOrgFmt);
 	
 	#################################################################################
 	## ORGANIZACAO - ADM
 	#################################################################################
 	$oOrgAdm		= $em->getRepository('Entidades\ZgadmOrganizacaoAdm')->findOneBy(array('codOrganizacao' => $oOrganizacao->getCodigo(), 'codOrganizacaoPai' => $system->getCodOrganizacao()));
 	
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
 	## USUÁRIO - ORGANIZACAO
 	#################################################################################
	$usuOrg 	= \Zage\Seg\Usuario::listaUsuarioOrganizacaoAtivo($system->getCodOrganizacao(), U);
 	
	for ($i = 0; $i < sizeof($usuOrg); $i++) {
		
		$oUsuOrg  = $em->getRepository('Entidades\ZgsegUsuarioOrganizacao')->findOneBy(array('codUsuario' => $usuOrg[$i]->getCodUsuario()->getCodigo() , 'codOrganizacao' => $oOrganizacao->getCodigo()));
		
		if (!$oUsuOrg){
			$oUsuOrg = new \Entidades\ZgsegUsuarioOrganizacao();
		}
		
	
		$oPerfil			= $em->getRepository('Entidades\ZgsegPerfil')->findOneBy(array('codigo' => $usuOrg[$i]->getCodPerfil()->getCodigo()));
		$oUsuarioOrgStatus  = $em->getRepository('Entidades\ZgsegUsuarioOrganizacaoStatus')->findOneBy(array('codigo' => 'A'));
		$oUsu				= $em->getRepository('Entidades\ZgsegUsuario')->findOneBy(array('codigo' => $usuOrg[$i]->getCodUsuario()->getCodigo()));
		
		$oUsuOrg->setCodOrganizacao($oOrganizacao);
		$oUsuOrg->setCodPerfil($oPerfil);
		$oUsuOrg->setCodStatus($oUsuarioOrgStatus);
		$oUsuOrg->setCodUsuario($oUsu);
		
		$em->persist($oUsuOrg);

	}

	
	#################################################################################
	## Contrato
	#################################################################################
	$oContrato		= $em->getRepository('\Entidades\ZgadmContrato')->findOneBy(array('codOrganizacao' => $oOrganizacao->getCodigo()));
	if (!$oContrato)	{
		$oStatusContrato	= $em->getReference('\Entidades\ZgadmContratoStatusTipo','A');
		$oContrato			= new \Entidades\ZgadmContrato();
		$oContrato->setDataCadastro(new \DateTime());
		$oContrato->setDataInicio(new \DateTime());
		$oContrato->setCodStatus($oStatusContrato);
	}
	
	$oContrato->setCodOrganizacao($oOrganizacao);
	$oContrato->setCodPlano($oPlano);
	$oContrato->setPctDesconto($pctDesconto);
	$oContrato->setValorDesconto($valorDesconto);
	$em->persist($oContrato);
	
	
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
 
//$system->criaAviso(\Zage\App\Aviso\Tipo::INFO,"Informações salvas com sucesso");
echo '0'.\Zage\App\Util::encodeUrl('|'.$oOrganizacao->getCodigo());