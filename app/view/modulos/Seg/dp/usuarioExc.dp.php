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
if (isset($_POST['codUsuario'])) 		$codUsuario			= \Zage\App\Util::antiInjection($_POST['codUsuario']);
if (isset($_POST['codOrganizacao']))	$codOrganizacao		= \Zage\App\Util::antiInjection($_POST['codOrganizacao']);
#################################################################################
## Limpar a variável de erro
#################################################################################
$err	= false;

#################################################################################
## Verificar se a pasta existe e excluir
#################################################################################

try {

	if (!isset($codUsuario) || (!$codUsuario)) {
		$system->criaAviso(\Zage\App\Aviso\Tipo::ERRO,$tr->trans('Parâmetro não informado'));
		die('1'.\Zage\App\Util::encodeUrl('||'));
	}
	
	$oUsuario	= $em->getRepository('Entidades\ZgsegUsuario')->findOneBy(array('codigo' => $codUsuario));

	if (!$oUsuario) {
		$system->criaAviso(\Zage\App\Aviso\Tipo::ERRO,$tr->trans('Usuário não encontrado'));
		die('1'.\Zage\App\Util::encodeUrl('||'));
	}
	
	#################################################################################
	## Remover os acessos as empresas
	#################################################################################
	
	$oUsuAdm		= $em->getRepository('Entidades\ZgsegUsuarioOrganizacao')->findBy(array('codUsuario' => $codUsuario));

	if ($oUsuario->getCodStatus()->getCodigo() == P){
		if (sizeof($oUsuAdm) < 2){
			
			/*** Exclusão dos telefone ***/
			$oTel		= $em->getRepository('Entidades\ZgsegUsuarioTelefone')->findBy(array('codUsuario' => $codUsuario));
			for ($i = 0; $i < sizeof($oTel); $i++) {
				$em->remove($oTel[$i]);
			}
			
			/*** Exclusão da associação ***/
			$em->remove($oUsuAdm[0]);
			
			/*** Exclusão do convite ***/
			$oConvite = $em->getRepository('Entidades\ZgsegConvite')->findBy(array('codUsuarioDestino' => $codUsuario));
			for ($i = 0; $i < sizeof($oConvite); $i++) {
				$em->remove($oConvite[$i]);
			}
			
			/*** Exclusão do usuário ***/
			$em->remove($oUsuario);
		}
	}else{
		
		$oStatus			= $em->getRepository('Entidades\ZgsegUsuarioOrganizacaoStatus')->findOneBy(array('codigo' => C));
		
		/*** Status C (cancelado) para o usuario organizacao ***/
		for ($i = 0; $i < sizeof($oUsuAdm); $i++) {
			if($oUsuAdm[$i]->getCodOrganizacao()->getCodigo() == $codOrganizacao){
				if ($oUsuAdm[$i]->getCodStatus()->getCodigo() == P){
					
					/*** Status C (cancelado) para o convite ***/
					$oConvite		 = $em->getRepository('Entidades\ZgsegConvite')->findOneBy(array('codUsuarioDestino' => $codUsuario , 'codOrganizacaoOrigem' => $codOrganizacao));
					$oConviteStatus  = $em->getRepository('Entidades\ZgsegConviteStatus')->findOneBy(array('codigo' => C));
					$oConvite->setCodStatus($oConviteStatus);
				}

				/*** Status C (cancelado) para a associação ***/
				$oUsuAdm[$i]->setCodStatus($oStatus);
				$em->persist($oUsuAdm[$i]);
			}
		}
		
	}	
	
	$em->flush();

} catch (\Exception $e) {
	$system->criaAviso(\Zage\App\Aviso\Tipo::ERRO,$e->getMessage());
	die('1'.\Zage\App\Util::encodeUrl('||'));
	exit;
}


$system->criaAviso(\Zage\App\Aviso\Tipo::INFO,$tr->trans("Usuário excluído com sucesso"));
echo '0'.\Zage\App\Util::encodeUrl('|'.$oUsuario->getCodigo().'|');