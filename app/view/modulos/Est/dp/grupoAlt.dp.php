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
if (isset($_POST['codGrupoPai'])) 	$codGrupoPai	= \Zage\App\Util::antiInjection($_POST['codGrupoPai']);
if (isset($_POST['codGrupo'])) 		$codGrupo		= \Zage\App\Util::antiInjection($_POST['codGrupo']);
if (isset($_POST['nome'])) 			$nome			= \Zage\App\Util::antiInjection($_POST['nome']);
if (isset($_POST['descricao'])) 	$descricao		= \Zage\App\Util::antiInjection($_POST['descricao']);

#################################################################################
## Limpar a variável de erro
#################################################################################
$err	= false;

#################################################################################
## Fazer validação dos campos
#################################################################################
/** Descricao **/
if (!isset($descricao) || (empty($descricao)) || (strlen($descricao) > 60)) {
	$err	= $tr->trans("O campo Descricao deve ter no máximo 60 caracteres");
}

if (!$codGrupoPai) $codGrupoPai = null;

/** Verifica se o nome já existe no mesmo nível **/
$existeGrupo	= $em->getRepository('Entidades\ZgestGrupo')->findOneBy(array('codGrupoPai' => $codGrupoPai,'descricao' => $descricao));

if (is_object($existeGrupo) && $existeGrupo->getCodigo() != $codGrupo) {
	$err	= $tr->trans("Descrição já utilizada, escolha outra descrição de grupo");
}

if ($err != null) {
	echo '1'.\Zage\App\Util::encodeUrl('||'.htmlentities($err));
	exit;
}

#################################################################################
## Salvar no banco
#################################################################################
try {

	if (isset($codGrupo) && (!empty($codGrupo))) {
		$oGrupo	= $em->getRepository('Entidades\ZgestGrupo')->findOneBy(array('codigo' => $codGrupo));
		
		if (!$oGrupo){
			$oGrupo	= new \Entidades\ZgestGrupo();
		}else{
			$codGrupoPai = $oGrupo->getCodGrupoPai()->getCodigo();
		}
	}else{
		$oGrupo		= new \Entidades\ZgestGrupo();
	}
	
	if ($codGrupoPai != null) {
		$oGrupoPai	= $em->getRepository('Entidades\ZgestGrupo')->findOneBy(array('codigo' => $codGrupoPai));
	}else{
		$oGrupoPai	= null;
	}
	
	$oGrupo->setDescricao($descricao);
	$oGrupo->setCodGrupoPai($oGrupoPai);
	
	$em->persist($oGrupo);
	$em->flush();
	$em->detach($oGrupo);

} catch (\Exception $e) {
	echo '1'.\Zage\App\Util::encodeUrl('||'.htmlentities($e->getMessage()));
	exit;
}

echo '0'.\Zage\App\Util::encodeUrl('|'.$oGrupo->getCodigo().'|'.htmlentities($tr->trans("Informações salvas com sucesso")));
