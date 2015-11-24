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
## Resgata a variável ID que está criptografada
#################################################################################
if (isset($_GET['id'])) {
	$id = \Zage\App\Util::antiInjection($_GET["id"]);
}elseif (isset($_POST['id'])) {
	$id = \Zage\App\Util::antiInjection($_POST["id"]);
}elseif (isset($id)) 	{
	$id = \Zage\App\Util::antiInjection($id);
}else{
	\Zage\App\Erro::halt('Falta de Parâmetros');
}

#################################################################################
## Descompacta o ID
#################################################################################
\Zage\App\Util::descompactaId($id);

#################################################################################
## Verifica se o usuário tem permissão no menu
#################################################################################
//$system->checaPermissao($_codMenu_);

if (isset($_GET['codTrans'])){
	$codTrans		= \Zage\App\Util::antiInjection($_GET['codTrans']);
}

#################################################################################
## Resgatar informações da da venda
#################################################################################
$infoTrans = $em->getRepository('Entidades\ZgfmtConviteExtraTransf')->findBy(array('codigo' => $codTrans));

for ($i = 0; $i < sizeof($infoTrans); $i++) {
	
	$html .= '<tr>';
	$html .= '<td class="center">'.$infoTrans[$i]->getCodEvento()->getCodTipoEvento()->getDescricao().'</td>';
	$html .= '<td class="center">'.$infoTrans[$i]->getQuantidade().'</td>';
	$html .= '</tr>';
}

#################################################################################
## Carregando o template html
#################################################################################
$tpl	= new \Zage\App\Template();
$tpl->load(\Zage\App\Util::getCaminhoCorrespondente(__FILE__, \Zage\App\ZWS::EXT_HTML));

#################################################################################
## Define os valores das variáveis
#################################################################################
$tpl->set('ID'					,$id);
$tpl->set('IC'					,$_icone_);
$tpl->set('COD_TRANS'			,$infoTrans[0]->getCodigo());
$tpl->set('DATA_TRANS'			,$infoTrans[0]->getDataCadastro()->format($system->config["data"]["datetimeSimplesFormat"]));
$tpl->set('FORMANDO_ORIGEM'		,$infoTrans[0]->getCodFormandoOrigem()->getNome());
$tpl->set('EMAIL_ORIGEM'		,$infoTrans[0]->getCodFormandoOrigem()->getEmail());
$tpl->set('FORMANDO_DESTINO'	,$infoTrans[0]->getCodFormandoDestino()->getNome());
$tpl->set('EMAIL_DESTINO'		,$infoTrans[0]->getCodFormandoOrigem()->getEmail());

$tpl->set('HTML_TABLE'			,$html);

#################################################################################
## Por fim exibir a página HTML
#################################################################################
$tpl->show();

