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
if (isset($_POST['codPlano'])) 		$codPlano	= \Zage\App\Util::antiInjection($_POST['codPlano']);

#################################################################################
## Limpar a variável de erro
#################################################################################
$err	= false;

#################################################################################
## Verificaçãoes e excluir
#################################################################################
try {

	if (!isset($codPlano) || (!$codPlano)) {
		die ('1'.\Zage\App\Util::encodeUrl('||'.htmlentities($tr->trans("Parâmetro não informado : COD_PLANO"))));
		$err = 1;
	}
	
	$oPlano  	= $em->getRepository('Entidades\ZgfmtPlanoOrcamentario')->findOneBy(array('codigo' => $codPlano));
	$oOrc	 	= $em->getRepository('Entidades\ZgfmtOrcamento')->findBy(array('codPlanoVersao' => $codPlano));
	
	if (!$oPlano) {
		die ('1'.\Zage\App\Util::encodeUrl('||'.htmlentities($tr->trans("Ops!! Não conseguimos realizar a operação. Caso o problema continue entre em contato com o suporte do portal SUAFORMATURA.COM"))));
		$err = 1;
	}elseif ($oPlanoNum){
		die ('1'.\Zage\App\Util::encodeUrl('||'.htmlentities($tr->trans("Não podemos excluir pois já existe um orçamento cadastrado com este modelo."))));
		$err = 1;
	}
	
	// Apagar itens de orçamento
	$qb = $em->createQueryBuilder();
	$qb->delete('Entidades\ZgfmtPlanoOrcItem','i');
	$qb->andWhere($qb->expr()->eq('i.codPlano', ':codPlano'));
	$qb->setParameter(':codPlano',$codPlano);
	$query 		= $qb->getQuery();
	$numDeleted = $query->execute();
	
	// Deletar Plano
	$em->remove($oPlano);
	$em->flush();

} catch (\Exception $e) {
	die ('1'.\Zage\App\Util::encodeUrl('||'.htmlentities($e->getMessage())));
	exit;
}

echo '0'.\Zage\App\Util::encodeUrl('||'.htmlentities($tr->trans("Versão excluída com sucesso.")));