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
## Verifica se o usuário está autenticado
#################################################################################
include_once(BIN_PATH . 'auth.php');
#################################################################################
## Resgata a variável ID que está criptografada
#################################################################################
if (isset($_GET['id'])) {
	$id = \Zage\App\Util::antiInjection($_GET["id"]);
}elseif (isset($_POST['id'])) {
	$id = \Zage\App\Util::antiInjection($_POST["id"]);
}elseif (isset($id)) 	{
	$id = \Zage\App\Util::antiInjection($id);
}else{
	\Zage\App\Erro::halt($tr->trans('Falta de Parâmetros'));
}

#################################################################################
## Descompacta o ID
#################################################################################
\Zage\App\Util::descompactaId($id);

#################################################################################
## Verifica se o usuário tem permissão no menu
#################################################################################
$system->checaPermissao($_codMenu_);

#################################################################################
## Resgata as variáveis postadas
#################################################################################
if (isset($_GET['codSubgrupo'])) 		$codSubgrupo			= \Zage\App\Util::antiInjection($_GET['codSubgrupo']);
if (isset($_GET['codProduto'])) 		$codProduto				= \Zage\App\Util::antiInjection($_GET['codProduto']);
if (!isset($codSubgrupo) || !$codSubgrupo) exit;

#################################################################################
## Resgatar os dados
#################################################################################
$conf		= $em->getRepository('Entidades\ZgestSubgrupoConf')->findBy(array('codSubgrupo' => $codSubgrupo));

#################################################################################
## Cria o objeto do Grid (bootstrap) 
#################################################################################
$htmlForm	  = "";

for ($i = 0; $i < sizeof($conf); $i++) {
	//echo 'DIV:' . $conf[$i]->getCodigo();
	$idCampo	 = \Zage\Est\Produto::geraIdInput($conf[$i]->getCodigo());
	
	$htmlForm	.= '<div class="col-sm-12" id="div'.$idCampo.'">';
	$htmlForm	.= '<div class="form-group">';
	$htmlForm	.= '<input type="hidden" id="indObrig'.$idCampo.'" value="'.$conf[$i]->getIndObrigatorio().'" >';
	$htmlForm	.= '<label class="col-sm-3 control-label" for="'.$idCampo.'">'.$conf[$i]->getNome().'</label>';
	$htmlForm	.= '<div class="input-group col-sm-8 pull-left">';
	$htmlForm	.= \Zage\Est\Produto::geraHtml($conf[$i]->getCodigo(), $codProduto, ($i+1));
	$htmlForm	.= '</div>';
	$htmlForm	.= '<div class="col-sm-1 pull-left" id="divHelp'.$idCampo.'"></div>';
	$htmlForm	.= '</div>';
	$htmlForm	.= '</div>';

}

if ( sizeof($conf) >= 1 ){
	$htmlForm	.= '<input type="hidden" id="quantConfID" value="'.sizeof($conf).'" >';
}

echo $htmlForm;