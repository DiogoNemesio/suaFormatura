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
if (isset($_GET['codVersaoOrc'])) 		$codVersaoOrc			= \Zage\App\Util::antiInjection($_GET['codVersaoOrc']);

if (!isset($codVersaoOrc)) exit;

#################################################################################
## Resgatar os dados
#################################################################################
$oItem		= $em->getRepository('Entidades\ZgfmtPlanoOrcItem')->findBy(array('codVersao' => $codVersaoOrc));

//for ($i = 0; $i < sizeof($oItem); $i++) {
//	$arrayItem[$oItem[$i]->getCodTipoEvento()] = $oItem[$i]->getCodigo();
//}

#################################################################################
## Cria o objeto do Grid (bootstrap) 
#################################################################################
$htmlForm	  = "";

$htmlForm	.= '<h4 align="center"><b>Detalhes do evento</b></h4>';
$htmlForm	.= '<br>';
$htmlForm	.= '<div class="col-sm-10" align="center">';
$htmlForm	.= '<table id="dynamic-table" class="table table-hover">';



for ($i = 0; $i < sizeof($oItem); $i++) {
	
	$htmlForm	.= '<tr>';
	$htmlForm	.= '<td class="center">';
	$htmlForm	.= '<label class="pos-rel">';
	$htmlForm	.= '<input type="checkbox" class="ace" />';
	$htmlForm	.= '<span class="lbl"></span>';
	$htmlForm	.= '</label>';
	$htmlForm	.= '</td>';
	$htmlForm	.= '<td><input class="form-control" id="numConvidadoID" placeholder="Quantidade" type="text" name="nome" placeholder="" maxlength="100" value="" autocomplete="off" zg-data-toggle="mask" zg-data-mask="numero"></td>';
	$htmlForm	.= '<td>'.$oItem[$i]->getItem().'</td>';
	$htmlForm	.= '<td><input class="form-control" id="numConvidadoID" type="text" name="nome" placeholder="Valor unitário" maxlength="100" value="" autocomplete="off" zg-data-toggle="mask" zg-data-mask="dinheiro"></td>';
	$htmlForm	.= '<td>TOTAL</td>';
	$htmlForm	.= '</tr>';

}

$htmlForm	.= '</table>';
$htmlForm	.= '</div>';

echo $htmlForm;