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
## Variáveis globais
#################################################################################
global $em,$system,$tr;

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
## Verificar parâmetro obrigatório
#################################################################################
if (!isset($codChip) || empty($codChip)) \Zage\App\Erro::halt('Falta de Parâmetros 2');

#################################################################################
## Resgata as informações do banco
#################################################################################
try {
	$info = $em->getRepository('\Entidades\ZgwapChip')->findOneBy(array('codigo' => $codChip));
} catch (\Exception $e) {
	\Zage\App\Erro::halt($e->getMessage());
}

$identificacao	= $info->getIdentificacao();
$numero			= $info->getDdd() . $info->getNumero();
$status			= $info->getCodStatus()->getCodigo();
$codPais		= ($info->getCodPais()) ? $info->getCodPais()->getCodigo() : null;
	

if ($status == "R") {
	$submit			= null;
	$titulo			= 'Bloqueio de chip';
	$icone			= '<i class="fa fa-lock red"></i>';
	$mensagem		= $tr->trans('Chip não registrado, não precisa ser cancelado').': <b>'.$numero.'</b> ?';
	$observacao		= $tr->trans('Caso não precise mais do chip, uso o botão excluir');
	$classe			= "text-warning";
	$botao			= '<i class="fa fa-lock bigger-110"></i> Bloquear ';
	$botaoClasse	= 'btn btn-danger';
}elseif ($status == "A") {
	$submit			= null;
	$titulo			= 'Bloqueio de chip';
	$icone			= '<i class="fa fa-lock red"></i>';
	$mensagem		= $tr->trans('Chip está ATIVO, Deseja realmente bloquear o número').': <b>'.$numero.'</b> ?';
	$observacao		= $tr->trans('Não será possível enviar mensagens via Whatsapp, caso esse seja o único chip ativo');
	$classe			= "text-warning";
	$botao			= '<i class="fa fa-lock bigger-110"></i> Bloquear ';
	$botaoClasse	= 'btn btn-danger';
}elseif ($status == "B") {
	$submit			= null;
	$titulo			= 'Desbloqueio de chip';
	$icone			= '<i class="fa fa-unlock green"></i>';
	$mensagem		= $tr->trans('Chip está BLOQUEADO, Deseja realmente desbloquear o número').': <b>'.$numero.'</b> ?';
	$observacao		= $tr->trans('Você poderá enviar mensagens após o desbloqueio');
	$classe			= "text-success";
	$botao			= '<i class="fa fa-unlock bigger-110"></i> Desbloquear ';
	$botaoClasse	= 'btn btn-success';
}elseif ($status == "C") {
	$submit			= null;
	$titulo			= 'Desbloqueio de chip';
	$icone			= '<i class="fa fa-unlock green"></i>';
	$mensagem		= $tr->trans('Chip está CANCELADO, e não pode ser desbloqueado').': <b>'.$numero.'</b> ?';
	$observacao		= $tr->trans('Entre em contato com os administradores através do e-mail: '.$system->config["mail"]["admin"]);
	$classe			= "text-danger";
	$botao			= '<i class="fa fa-lock bigger-110"></i>';
	$botaoClasse	= 'btn btn-danger';
}else{
	$submit			= null;
	$titulo			= 'Desbloqueio de chip';
	$icone			= '<i class="fa fa-ban green"></i>';
	$mensagem		= $tr->trans('Status do chip não suportado').': <b>'.$status.'</b> ?';
	$observacao		= $tr->trans('Entre em contato com os administradores através do e-mail: '.$system->config["mail"]["admin"]);
	$classe			= "text-danger";
	$botao			= '<i class="fa fa-ban bigger-110"></i>';
	$botaoClasse	= 'btn btn-danger';
}


#################################################################################
## Url Voltar
#################################################################################
$urlVoltar			= ROOT_URL."/Wap/chipLis.php?id=".$id;

#################################################################################
## Carregando o template html
#################################################################################
$tpl	= new \Zage\App\Template();
//$tpl->load(\Zage\App\Util::getCaminhoCorrespondente(__FILE__, \Zage\App\ZWS::EXT_HTML));
$tpl->load(HTML_PATH . '/templateModal.html');

#################################################################################
## Define os valores das variáveis
#################################################################################
$tpl->set('ID'					,$id);
$tpl->set('URLVOLTAR'			,$urlVoltar);
$tpl->set('DP'					,\Zage\App\Util::getCaminhoCorrespondente(__FILE__,\Zage\App\ZWS::EXT_DP,\Zage\App\ZWS::CAMINHO_RELATIVO));
$tpl->set('COD_CHIP'			,$codChip);
$tpl->set('IDENTIFICACAO'		,$identificacao);
$tpl->set('NUMERO'				,$numero);
$tpl->set('TEXTO'				,$mensagem);
$tpl->set('OBSERVACAO'			,$observacao);
$tpl->set('CLASSE'				,$classe);
$tpl->set('BOTAO'				,$botao);
$tpl->set('BOTAO_CLASSE'		,$botaoClasse);
$tpl->set('SUBMIT'				,$submit);
$tpl->set('TITULO'				,$titulo);
$tpl->set('ICONE'				,$icone);
$tpl->set('VAR'					,'codChip');
$tpl->set('VAR_VALUE'			,$codChip);
$tpl->set('NOME'				,$numero);

#################################################################################
## Por fim exibir a página HTML
#################################################################################
$tpl->show();
