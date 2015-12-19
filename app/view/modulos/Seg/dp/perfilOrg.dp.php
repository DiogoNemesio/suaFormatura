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

if (isset($_POST['codPerfil'])) 		$codPerfil		= \Zage\App\Util::antiInjection($_POST['codPerfil']);
if (isset($_POST['associacao'])) 		$associacao		= \Zage\App\Util::antiInjection($_POST['associacao']);
if (!isset($associacao))				$associacao		= array();

#################################################################################
## Limpar a variável de erro
#################################################################################
$err	= false;

#################################################################################
## Fazer validação dos campos
#################################################################################
if (!isset($codPerfil) || empty($codPerfil)) {
	$system->criaAviso(\Zage\App\Aviso\Tipo::ERRO,"Parâmentro PERFIL não encontrado.");
	$err	= 1;
}else{
	$oPerfil = $em->getRepository('Entidades\ZgsegPerfil')->findOneBy(array('codigo' => $codPerfil));
	if (!$oPerfil){
		$system->criaAviso(\Zage\App\Aviso\Tipo::ERRO,"Ops!! Não encontrar o perfil selecionado. Caso o problema continue entre em contato com o suporte do portal SUAFORMATURA.COM");
		$err	= 1;
	}
}

if ($err != null) {
	echo '1'.\Zage\App\Util::encodeUrl('||'.htmlentities($err));
	exit;
}

#################################################################################
## Salvar no banco
#################################################################################
try {
	
	#################################################################################
	## Salvar a associação entre segmento de mercado e categoria
	#################################################################################	
	//Retirar tipo organizacao
	$segCatAssociado	= $em->getRepository('Entidades\ZgsegPerfilOrganizacaoTipo')->findBy(array('codPerfil' => $codPerfil));
	for ($i = 0; $i < sizeof($segCatAssociado); $i++) {
		if (!in_array($segCatAssociado[$i]->getCodTipoOrganizacao()->getCodigo(), $associacao)) {
			try {
				$em->remove($segCatAssociado[$i]);
			} catch (\Exception $e) {
				echo '1'.\Zage\App\Util::encodeUrl('||'.htmlentities("Não foi possível excluir a categoria: ".$segCatAssociado[$i]->getCodCategoria()->getDescricao()." Erro: ".$e->getMessage()));
				exit;
			}
		}
	}
	//Atribuir categoria
	for ($i = 0; $i < sizeof($associacao); $i++) {
		$oAssociacao		= $em->getRepository('Entidades\ZgsegPerfilOrganizacaoTipo')->findOneBy(array('codPerfil' => $codPerfil, 'codTipoOrganizacao' => $associacao[$i]));
		if (!$oAssociacao) {
			$oAssociacao		= new \Entidades\ZgsegPerfilOrganizacaoTipo();
		}	
		
		$oOrgTipo		= $em->getRepository('Entidades\ZgadmOrganizacaoTipo')->findOneBy(array('codigo' => $associacao[$i]));
		
		$oAssociacao->setCodPerfil($oPerfil);
		$oAssociacao->setCodTipoOrganizacao($oOrgTipo);
		
		try {
			$em->persist($oAssociacao);
		} catch (\Exception $e) {
			echo '1'.\Zage\App\Util::encodeUrl('||'.htmlentities("Não foi possível associar o tipo organização: ".$associacao[$i]." Erro: ".$e->getMessage()));
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
		$log->debug("Erro ao salvar o usuário:". $e->getTraceAsString());
		throw new \Exception("Ops!! Não conseguimos realizar a operação. Caso o problema continue entre em contato com o suporte do portal SUAFORMATURA.COM");
	}
		
} catch (\Exception $e) {
	$system->criaAviso(\Zage\App\Aviso\Tipo::ERRO,$e->getMessage());
	echo '1'.\Zage\App\Util::encodeUrl('||'.htmlentities($e->getMessage()));
	exit;
}


$system->criaAviso(\Zage\App\Aviso\Tipo::INFO,$tr->trans("Informações salvas com sucesso"));
echo '0'.\Zage\App\Util::encodeUrl('|');
