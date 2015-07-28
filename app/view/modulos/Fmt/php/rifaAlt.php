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
## Resgata as informações do banco
#################################################################################
if ((isset($codRifa) && ($codRifa))) {

	try {
		
		$info 		= $em->getRepository('Entidades\ZgfmtRifa')->findOneBy(array('codigo' => $codRifa));
		
	} catch (\Exception $e) {
		\Zage\App\Erro::halt($e->getMessage());
	}
	
	$nome 				= $info->getNome();
	$premio				= $info->getPremio();
	$custo				= \Zage\App\Util::toPHPNumber($info->getCusto());
	$dataSorteio		= $info->getDataSorteio()->format($system->config["data"]["datetimeSimplesFormat"]);
	$localSorteio 		= $info->getLocalSorteio();
	$qtdeObrigatorio	= $info->getQtdeObrigatorio();
	$valor				= \Zage\App\Util::toPHPNumber($info->getValorUnitario());
	$indRifaEletronica	= ($info->getIndRifaEletronica()	== 1) ? "checked" : null;
	
	if ($info->getIndRifaGerada() == 1){
		$readonly = 'readonly';
	}else{
		$readonly = null;
	}
	
	$usuCadastro		= $info->getUsuarioCadastro()->getNome();
	$dataCadastro		= $info->getDataCadastro()->format($system->config["data"]["datetimeSimplesFormat"]);
	$usuAlteracao		= ($info->getUsuarioAlteracao() != null) ? $info->getUsuarioAlteracao()->getNome() : null;
	$dataAlteracao		= ($info->getDataAlteracao() != null) ? $info->getDataAlteracao()->format($system->config["data"]["datetimeSimplesFormat"]) : null;
	
}else{
	
	$codRifa			= null;
	$nome				= null;
	$premio				= null;
	$custo				= null;
	$dataSorteio		= null;
	$horaSorteio		= null;
	$localSorteio 		= null;
	$qtdeObrigatorio	= null;
	$valor				= null;
	$readonly			= null;
	$indRifaEletronica	= "checked";
	
}

#################################################################################
## Url Voltar
#################################################################################
$urlVoltar			= ROOT_URL."/Fmt/rifaLis.php?id=".$id;

#################################################################################
## Url Novo
#################################################################################
$uid = \Zage\App\Util::encodeUrl('_codMenu_='.$_codMenu_.'&_icone_='.$_icone_.'&codRifa=');
$urlNovo			= ROOT_URL."/Fmt/rifaAlt.php?id=".$uid;

#################################################################################
## Quantidade de formandos ativos
#################################################################################
$usuAtivo		= \Zage\Seg\Usuario::listaUsuarioOrganizacaoAtivo($system->getCodOrganizacao(), F);
$numUsuAtivo	= sizeof($usuAtivo);

#################################################################################
## Carregando o template html
#################################################################################
$tpl	= new \Zage\App\Template();
$tpl->load(\Zage\App\Util::getCaminhoCorrespondente(__FILE__, \Zage\App\ZWS::EXT_HTML));

#################################################################################
## Define os valores das variáveis
#################################################################################
$tpl->set('URL_FORM'				,$_SERVER['SCRIPT_NAME']);
$tpl->set('URLVOLTAR'				,$urlVoltar);
$tpl->set('URLNOVO'					,$urlNovo);
$tpl->set('ID'						,$id);
$tpl->set('NUM_USUARIO'				,$numUsuAtivo);

$tpl->set('COD_RIFA'				,$codRifa);
$tpl->set('NOME'					,$nome);
$tpl->set('PREMIO'					,$premio);
$tpl->set('CUSTO'					,$custo);
$tpl->set('DATA_SORTEIO'			,$dataSorteio);
$tpl->set('HORA_SORTEIO'			,$horaSorteio);
$tpl->set('LOCAL_SORTEIO'			,$localSorteio);
$tpl->set('QTDE_OBRIGATORIO'		,$qtdeObrigatorio);
$tpl->set('VALOR'					,$valor);
$tpl->set('IND_RIFA_ELETRONICA'		,$indRifaEletronica);

$tpl->set('READONLY'				,$readonly);

$tpl->set('USUARIO_CADASTRO'		,$usuCadastro);
$tpl->set('DATA_CADASTRO'			,$dataCadastro);
$tpl->set('USUARIO_ALTERACAO'		,$usuAlteracao);
$tpl->set('DATA_ALTERACAO'			,$dataAlteracao);

$tpl->set('DP'						,\Zage\App\Util::getCaminhoCorrespondente(__FILE__,\Zage\App\ZWS::EXT_DP,\Zage\App\ZWS::CAMINHO_RELATIVO));
$tpl->set('IC'						,$_icone_);
$tpl->set('COD_MENU'				,$_codMenu_);

#################################################################################
## Por fim exibir a página HTML
#################################################################################
$tpl->show();
