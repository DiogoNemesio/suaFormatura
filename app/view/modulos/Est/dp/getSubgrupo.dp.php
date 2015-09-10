<?php
#################################################################################
## Includes
#################################################################################
if (defined('DOC_ROOT')) {
	include_once(DOC_ROOT . 'includeNoAuth.php');
}else{
	include_once('../includeNoAuth.php');
}

#################################################################################
## Resgata as variáveis postadas
#################################################################################
if (isset($_GET['q']))				$q		     = \Zage\App\Util::antiInjection($_GET["q"]);
if (isset($_GET['codSubgrupo']))	$codSubgrupo = \Zage\App\Util::antiInjection($_GET["codSubgrupo"]);

if (isset($codSubgrupo)) {
	$conf		= $em->getRepository('Entidades\ZgestSubgrupoConf')->findBy(array('codSubgrupo' => $codSubgrupo));
}else{
	//$conf		= \Zage\Adm\Organizacao::buscaOrganizacaoParceiro($q);
}

$array		= array();

#################################################################################
## Criação do formulário de índices
#################################################################################
$htmlForm	= "";
for ($i = 0; $i < sizeof($conf); $i++) {

	$idCampo	= \Zage\Est\Produto::geraIdInput($conf[$i]->getCodigo());
	$htmlForm	.= '<div class="col-sm-12">';
	$htmlForm	.= '<div class="form-group">';
	$htmlForm	.= '<label class="col-sm-5 control-label" for="'.$idCampo.'">'.$conf[$i]->getNome().'</label>';
	$htmlForm	.= '<div class="input-group col-sm-7">';
	$htmlForm	.= \Zage\Est\Produto::geraHtml($conf[$i]->getCodigo(), $codSubgrupo, ($i+1));
	$htmlForm	.= '</div>';
	$htmlForm	.= '</div>';
	$htmlForm	.= '</div>';
	
	$array[$i]["html"]  = $htmlForm;
}

echo json_encode($array);