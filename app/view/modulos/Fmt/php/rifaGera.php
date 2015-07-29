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
## Verifica os parâmetros obrigatórios
#################################################################################
if (!isset($codRifa)) \Zage\App\Erro::halt('Falta de Parâmetros 2');
	
#################################################################################
## Resgata as informações do banco
#################################################################################
try {
	
	$info 		= $em->getRepository('Entidades\ZgfmtRifa')->findOneBy(array('codigo' => $codRifa));
	
} catch (\Exception $e) {
	\Zage\App\Erro::halt($e->getMessage());
}
	
$nome 				= $info->getNome();
$premio				= $info->getPremio();
$custo				= \Zage\App\Util::formataDinheiro($info->getCusto());
$dataSorteio		= $info->getDataSorteio()->format($system->config["data"]["datetimeSimplesFormat"]);
$localSorteio 		= $info->getLocalSorteio();
$qtdeObrigatorio	= $info->getQtdeObrigatorio();
$valor				= \Zage\App\Util::formataDinheiro($info->getValorUnitario());
$indRifaEletronica	= ($info->getIndRifaEletronica()	== 1) ? "checked" : null;
	
#################################################################################
## Quantidade de formandos ativos
#################################################################################
$formandos		= \Zage\Fmt\Formatura::listaFormandosAtivos($system->getCodOrganizacao());
$numAtivo		= sizeof($formandos);

#################################################################################
## Número inicial e final
#################################################################################
$numeroInicial	= 1;
$numeroFinal	= $numAtivo * $qtdeObrigatorio;

#################################################################################
## Url Voltar
#################################################################################
$urlVoltar			= ROOT_URL."/Fmt/rifaLis.php?id=".$id;

#################################################################################
## Carregando o template html
#################################################################################
$tpl	= new \Zage\App\Template();
$tpl->load(\Zage\App\Util::getCaminhoCorrespondente(__FILE__, \Zage\App\ZWS::EXT_HTML));

#################################################################################
## Define os valores das variáveis
#################################################################################
$tpl->set('TITULO'					,"Geração dos números da Rifa");
$tpl->set('URL_FORM'				,$_SERVER['SCRIPT_NAME']);
$tpl->set('URL_VOLTAR'				,$urlVoltar);
$tpl->set('ID'						,$id);
$tpl->set('NUM_FORMANDO'			,$numAtivo);
$tpl->set('NUM_RIFAS'				,$numeroFinal);
$tpl->set('COD_RIFA'				,$codRifa);
$tpl->set('NOME'					,$nome);
$tpl->set('PREMIO'					,$premio);
$tpl->set('CUSTO'					,$custo);
$tpl->set('LOCAL_SORTEIO'			,$localSorteio);
$tpl->set('QTDE_OBRIGATORIO'		,$qtdeObrigatorio);
$tpl->set('VALOR'					,$valor);
$tpl->set('IND_RIFA_ELETRONICA'		,$indRifaEletronica);
$tpl->set('DP'						,\Zage\App\Util::getCaminhoCorrespondente(__FILE__,\Zage\App\ZWS::EXT_DP,\Zage\App\ZWS::CAMINHO_RELATIVO));
$tpl->set('IC'						,$_icone_);
$tpl->set('COD_MENU'				,$_codMenu_);

#################################################################################
## Por fim exibir a página HTML
#################################################################################
$tpl->show();
