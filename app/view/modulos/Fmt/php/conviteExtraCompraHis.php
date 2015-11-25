<?php
#################################################################################
## Includes
#################################################################################
if (defined('DOC_ROOT')) {
	include_once(DOC_ROOT . 'include.php');
}else{
	include_once('./include.php');
}

#################################################################################
## Variáveis globais
#################################################################################
global $system,$em,$tr,$_user;


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
$system->checaPermissao($_codMenu_);

#################################################################################
## Resgata a url desse script
#################################################################################
$url		= ROOT_URL . "/Fmt/". basename(__FILE__)."?id=".$id;

#################################################################################
## Resgata os dados do grid
#################################################################################
$hidden = null;
$msnCom = null;

try {
	$oCompras	= $em->getRepository('Entidades\ZgfmtConviteExtraVenda')->findBy(array('codFormando' => \Zage\Fmt\Convite::getCodigoUsuarioPessoa(), 'codOrganizacao' => $system->getCodOrganizacao() ), array('dataCadastro' => 'ASC'));
} catch (\Exception $e) {
	\Zage\App\Erro::halt($e->getMessage());
}

if(!$oCompras) {
	$hidden = "hidden";
	$msnCom .= '<div align="center" class="alert alert-info">';
	$msnCom .= '<i class="fa fa-exclamation-triangle bigger-125"></i> Nenhuma compra realizada!';
	$msnCom .= '</div>';
}else{
	for ($i = 0; $i < sizeof($oCompras); $i++) {
		$tabCompra	.= '<tr>
			<td style="text-align: center;">'.$oCompras[$i]->getCodTransacao().'</td>
			<td style="text-align: center;">'.$oCompras[$i]->getCodFormaPagamento()->getDescricao().'</td>
			<td style="text-align: center;">R$ '.\Zage\App\Util::formataDinheiro($oCompras[$i]->getValorTotal()).'</td>
			<td class="hidden-480" style="text-align: center;">'.$oCompras[$i]->getDataCadastro()->format($system->config["data"]["datetimeSimplesFormat"]).'</td>
			</tr>';
	}
}

#################################################################################
## Gerar a url de histórico de pagamentos
#################################################################################
$urlVoltar				= ROOT_URL."/Fmt/conviteExtraCompra.php?id=".$id;

#################################################################################
## Carregando o template html
#################################################################################
$tpl	= new \Zage\App\Template();
$tpl->load(\Zage\App\Util::getCaminhoCorrespondente(__FILE__, \Zage\App\ZWS::EXT_HTML));

#################################################################################
## Define os valores das variáveis
#################################################################################
$tpl->set('IC'				,$_icone_);
$tpl->set('ID'				,$id);
$tpl->set('URL_VOLTAR'		,$urlVoltar);
$tpl->set('HIDDEN'			,$hidden);
$tpl->set('TAB_COMPRA'		,$tabCompra);

$tpl->set('MSG_COM'			,$msnCom);
#################################################################################
## Por fim exibir a página HTML
#################################################################################
$tpl->show();
