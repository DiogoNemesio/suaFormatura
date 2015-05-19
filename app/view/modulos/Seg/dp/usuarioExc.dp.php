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
if (isset($_POST['codUsuario'])) 		$codUsuario		= \Zage\App\Util::antiInjection($_POST['codUsuario']);

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
	
	$oUsuario	= $em->getRepository('Entidades\ZgsegUsuario')->findOneBy(array('codOrganizacao' => $system->getCodOrganizacao(), 'codigo' => $codUsuario));

	if (!$oUsuario) {
		$system->criaAviso(\Zage\App\Aviso\Tipo::ERRO,$tr->trans('Usuário não encontrado'));
		die('1'.\Zage\App\Util::encodeUrl('||'));
	}
	
	#################################################################################
	## Remover os acessos as empresas
	#################################################################################
	$aEmps		= $em->getRepository('Entidades\ZgsegUsuarioEmpresa')->findBy(array('codUsuario' => $codUsuario));
	
	for ($i = 0; $i < sizeof($aEmps); $i++) {
		$em->remove($aEmps[$i]);
	}
	
	#################################################################################
	## Remover o Histórico de acesso aos menus
	#################################################################################
	$qb = $em->createQueryBuilder();
	$qb->delete('Entidades\ZgappMenuHistAcesso', 'mh');
	$qb->andWhere($qb->expr()->eq('mh.codUsuario', ':codUsuario'));
	$qb->setParameter('codUsuario', $codUsuario);
	$query 		= $qb->getQuery();
	$query->execute();
	
	
	$em->remove($oUsuario);
	$em->flush();

} catch (\Exception $e) {
	$system->criaAviso(\Zage\App\Aviso\Tipo::ERRO,$e->getMessage());
	die('1'.\Zage\App\Util::encodeUrl('||'));
	exit;
}


$system->criaAviso(\Zage\App\Aviso\Tipo::INFO,$tr->trans("Usuário excluído com sucesso"));
echo '0'.\Zage\App\Util::encodeUrl('|'.$oAgencia->getCodigo().'|');