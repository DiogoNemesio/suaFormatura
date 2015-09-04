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
if (isset($_POST['codSubgrupo'])) 	$codSubgrupo	= \Zage\App\Util::antiInjection($_POST['codSubgrupo']);
if (isset($_POST['codGrupo'])) 		$codGrupo		= \Zage\App\Util::antiInjection($_POST['codGrupo']);
if (isset($_POST['descricao'])) 	$descricao		= \Zage\App\Util::antiInjection($_POST['descricao']);

if (isset($_POST['codTipoOrg']))	$codTipoOrg			= $_POST['codTipoOrg'];
if (!isset($codTipoOrg))			$codTipoOrg			= array();
#################################################################################
## Limpar a variável de erro
#################################################################################
$err	= false;
//$log->debug(serialize($_POST));exit;
#################################################################################
## Fazer validação dos campos
#################################################################################
/** Descricao **/
if (!isset($descricao) || (empty($descricao)) || (strlen($descricao) > 60)) {
	$err	= $tr->trans("O campo Descricao deve ter no máximo 60 caracteres");
}

if (!$codSubgrupo) $codSubgrupo = null;

/** Verifica se o nome já existe no mesmo nível **/
$existeGrupo	= $em->getRepository('Entidades\ZgestSubgrupo')->findOneBy(array('codGrupo' => $codGrupo,'descricao' => $descricao));

if (is_object($existeGrupo) && $existeGrupo->getCodigo() != $codSubgrupo) {
	$err	= $tr->trans("Descrição já utilizada, escolha outra descrição de grupo");
}

if ($err != null) {
	echo '1'.\Zage\App\Util::encodeUrl('||'.htmlentities($err));
	exit;
}

#################################################################################
## Salvar no banco
#################################################################################
$em->getConnection()->beginTransaction();
try {

	if (isset($codSubgrupo) && (!empty($codSubgrupo))) {
		$oSubgrupo	= $em->getRepository('Entidades\ZgestSubgrupo')->findOneBy(array('codigo' => $codGrupo));
		
		if (!$oSubgrupo){
			$oSubgrupo	= new \Entidades\ZgestSubgrupo();
		}else{
			$codGrupo = $oSubgrupo->getCodGrupo()->getCodigo();
		}
	}else{
		$oSubgrupo		= new \Entidades\ZgestSubgrupo();
	}
	
	if ($codGrupo != null) {
		$oGrupo	= $em->getRepository('Entidades\ZgestGrupo')->findOneBy(array('codigo' => $codGrupo));
	}else{
		$oGrupo	= null;
	}
	
	$oSubgrupo->setDescricao($descricao);
	$oSubgrupo->setCodGrupo($oGrupo);
	
	$em->persist($oSubgrupo);
	$em->flush();
	//$em->detach($oSubgrupo);

	#################################################################################
	## Dependentes
	#################################################################################
	$subgrupos		= $em->getRepository('Entidades\ZgestSubgrupo')->findBy(array('codigo' => $codSubgrupo));
	
	#################################################################################
	## Exclusão
	#################################################################################
	for($i = 0; $i < sizeof ( $subgrupos ); $i ++) {
		if (! in_array ( $subgrupos [$i]->getCodigo (), $codSubgrupo )) {
			try {
				$em->remove ( $subgrupos [$i] );
				$em->flush ();
			} catch ( \Exception $e ) {
				$system->criaAviso ( \Zage\App\Aviso\Tipo::ERRO, "Não foi possível excluir o subgrupo: " . $subgrupos [$i]->getDescricao () . " Erro: " . $e->getMessage () );
				echo '1' . \Zage\App\Util::encodeUrl ( '||' . htmlentities ( $e->getMessage () ) );
				exit ();
			}
		}
	}
	
	### Tipos Organizacao ###
	for($i = 0; $i < sizeof ( $codTipoOrg ); $i ++) {
		$oSubgrupoOrg = $em->getRepository ( 'Entidades\ZgestSubgrupoOrg' )->findOneBy ( array (
				'codigo' => $codTipoOrg[$i],
				'codSubgrupo' => $oSubgrupo->getCodigo ()
		) );
	
		if (! $oSubgrupoOrg) {
			$oSubgrupoOrg = new \Entidades\ZgestSubgrupoOrg ();
		}
	
		$oSubgrupoTipo	= $em->getRepository('Entidades\ZgadmOrganizacaoTipo')->findOneBy(array(
				'codigo' => $codTipoOrg[$i]
		));
		
		$log->debug($codTipoOrg[$i]);
			
		$oSubgrupoOrg->setCodSubGrupo( $oSubgrupo );
		$oSubgrupoOrg->setCodTipoOrganizacao($oSubgrupoTipo);
			
		try {
			$em->persist ( $oSubgrupoOrg );
			$em->flush ();
			$em->detach ( $oSubgrupoOrg );
		} catch ( \Exception $e ) {
			$system->criaAviso ( \Zage\App\Aviso\Tipo::ERRO, "Não foi possível cadastrar o tipo organização: " . $codTipoOrg [$i] . " Erro: " . $e->getMessage () );
			echo '1' . \Zage\App\Util::encodeUrl ( '||' . htmlentities ( $e->getMessage () ) );
			exit ();
		}
	}
	$em->getConnection()->commit();
	
} catch (\Exception $e) {
	$em->getConnection()->rollback();
	echo '1'.\Zage\App\Util::encodeUrl('||'.htmlentities($e->getMessage()));
	exit;
}

echo '0'.\Zage\App\Util::encodeUrl('|'.$oSubgrupo->getCodigo().'|'.htmlentities($tr->trans("Informações salvas com sucesso")));
