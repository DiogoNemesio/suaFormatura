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
try {
	$transEnviada	= $em->getRepository('Entidades\ZgfmtConviteExtraTransf')->findBy(array('codFormandoOrigem' => \Zage\Fmt\Convite::getCodigoUsuarioPessoa() ), array('dataCadastro' => 'ASC'));
	$transRecebida	= $em->getRepository('Entidades\ZgfmtConviteExtraTransf')->findBy(array('codFormandoDestino' => \Zage\Fmt\Convite::getCodigoUsuarioPessoa() ), array('dataCadastro' => 'ASC'));
} catch (\Exception $e) {
	\Zage\App\Erro::halt($e->getMessage());
}

for ($i = 0; $i < sizeof($transEnviada); $i++) {
	$tabEnv	.= '<tr>
			<td style="text-align: center;">'.$transEnviada[$i]->getCodEvento()->getCodTipoEvento()->getDescricao().'</td>
			<td style="text-align: center;">'.$transEnviada[$i]->getQuantidade().'</td>
			<td style="text-align: center;">'.$transEnviada[$i]->getCodFormandoDestino()->getNome().'</td>
			<td class="hidden-480" style="text-align: center;">'.$transEnviada[$i]->getDataCadastro()->format($system->config["data"]["datetimeSimplesFormat"]).'</td>
			</tr>';
}

for ($i = 0; $i < sizeof($transRecebida); $i++) {
	$tabRec	.= '<tr>
			<td style="text-align: center;">'.$transRecebida[$i]->getCodEvento()->getCodTipoEvento()->getDescricao().'</td>
			<td style="text-align: center;">'.$transRecebida[$i]->getQuantidade().'</td>
			<td style="text-align: center;">'.$transRecebida[$i]->getCodFormandoOrigem()->getNome().'</td>
			<td class="hidden-480" style="text-align: center;">'.$transRecebida[$i]->getDataCadastro()->format($system->config["data"]["datetimeSimplesFormat"]).'</td>
			</tr>';
}
$hidEnv = null;
$hidRec = null;
$msnEnv = null;
$msnRec = null;

if(!$transEnviada) {
	$hidEnv = "hidden";
	$msnEnv .= '<div align="center" class="alert alert-info">';
	$msnEnv .= '<i class="fa fa-exclamation-triangle bigger-125"></i> Nenhuma transferência realizada!';
	$msnEnv .= '</div>';
}
if(!$transRecebida) {
	$hidRec = "hidden";
	$msnRec .= '<div align="center" class="alert alert-info">';
	$msnRec .= '<i class="fa fa-exclamation-triangle bigger-125"></i> Nenhuma transferência recebida!';
	$msnRec .= '</div>';
}

#################################################################################
## Gerar a url de histórico de pagamentos
#################################################################################
$urlVoltar				= ROOT_URL."/Fmt/conviteExtraTransf.php?id=".$id;

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
$tpl->set('HID_ENV'			,$hidEnv);
$tpl->set('HID_REC'			,$hidRec);

$tpl->set('TAB_ENV'			,$tabEnv);
$tpl->set('TAB_REC'			,$tabRec);

$tpl->set('MSG_ENV'			,$msnEnv);
$tpl->set('MSG_REC'			,$msnRec);
#################################################################################
## Por fim exibir a página HTML
#################################################################################
$tpl->show();
