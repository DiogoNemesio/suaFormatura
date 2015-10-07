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
global $em,$tr,$system,$log;

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
if (!isset($codRifa)) \Zage\App\Erro::halt('Falta de Parâmetros : COD_RIFA');


#################################################################################
## Resgata as informações da rifa
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
$qtdeObrigatorioInfo= $info->getQtdeObrigatorio();
$valor				= \Zage\App\Util::formataDinheiro($info->getValorUnitario());
$indRifaEletronica	= ($info->getIndRifaEletronica()	== 1) ? "checked" : null;

#################################################################################
## Resgatar formandos ativos
#################################################################################
try {
	$formandos	= \Zage\Seg\Usuario::listaUsuarioOrganizacaoAtivo($system->getCodOrganizacao(), 'F');
} catch (\Exception $e) {
	\Zage\App\Erro::halt($e->getMessage());
}

// Número de formandos ativos
$numAtivo		= sizeof($formandos);

// Gerar select dos formandos
for ($i = 0; $i < $numAtivo; $i++){
	$usuCombo .= '<option value='.$formandos[$i]->getCodUsuario()->getCodigo().'>'.$formandos[$i]->getCodUsuario()->getNome().'</option>';
}

#################################################################################
## Verificar se já foi gerado bilhete para a rifa
################################################################################
if ($info->getIndRifaGerada() == null){
	$disabled = 'disabled';
}else{
	$qtdeObrigatorio = null;
}

#################################################################################
## Número inicial e final
#################################################################################
$numeroFinal	= $numAtivo * $qtdeObrigatorio;


#################################################################################
## Resgata os dados do grid
#################################################################################
try {
	$rifaGera	= $em->getRepository('Entidades\ZgfmtRifaGeracao')->findBy(array('codRifa' => $codRifa),array('data' => 'DESC'));
} catch (\Exception $e) {
	\Zage\App\Erro::halt($e->getMessage());
}

#################################################################################
## Cria o objeto do Grid (bootstrap)
#################################################################################
$grid			= \Zage\App\Grid::criar(\Zage\App\Grid\Tipo::TP_BOOTSTRAP,"GCargo");
$grid->adicionaTexto($tr->trans('CÓDIGO'),			10, $grid::CENTER	,'codGeracao:codigo');
$grid->adicionaTexto($tr->trans('USUÁRIO'),			20, $grid::CENTER	,'codUsuario:nome');
$grid->adicionaTexto($tr->trans('# GERADAS'),		10, $grid::CENTER	,'');
$grid->adicionaTexto($tr->trans('NÚMERO INICIAL'),	10, $grid::CENTER	,'');
$grid->adicionaTexto($tr->trans('NÚMERO FINAL'),	10, $grid::CENTER	,'');
$grid->adicionaDataHora($tr->trans('DATA'),	 		20, $grid::CENTER	,'data');
$grid->adicionaIcone(null,'fa fa-file-pdf-o red',$tr->trans('Arquivo para download'));

$grid->importaDadosDoctrine($rifaGera);

#################################################################################
## Popula os valores dos botões
#################################################################################
for ($i = 0; $i < sizeof($rifaGera); $i++) {
	$rid		= \Zage\App\Util::encodeUrl('_codMenu_='.$_codMenu_.'&_icone_='.$_icone_.'&codRifa='.$codRifa.'&codGeracao='.$rifaGera[$i]->getCodigo().'&url='.$url);
	
	$infoGer	= \Zage\Fmt\Rifa::getInfoGeracao($codRifa, $rifaGera[$i]->getCodigo());
	
	$grid->setValorCelula($i, 2, $infoGer[0]["num"]);
	$grid->setValorCelula($i, 3, $infoGer[0]["num_ini"]);
	$grid->setValorCelula($i, 4, $infoGer[0]["num_fim"]);
	
	$grid->setUrlCelula($i,6,"javascript:zgDownloadUrl('".ROOT_URL."/Fmt/rifaPDF.php?id=".$rid."');");

}

#################################################################################
## Gerar o código html do grid
#################################################################################
try {
	$htmlGrid	= $grid->getHtmlCode();
} catch (\Exception $e) {
	\Zage\App\Erro::halt($e->getMessage());
}

#################################################################################
## Url Voltar
#################################################################################
$urlVoltar			= ROOT_URL."/Fmt/rifaLis.php?id=".$id;
$urlAtualizar		= ROOT_URL."/Fmt/rifaGera.php?id=".$id."&codRifa=".$codRifa;

echo $_;

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
$tpl->set('GRID'					,$htmlGrid);
$tpl->set('URL_VOLTAR'				,$urlVoltar);
$tpl->set('URL_ATUALIZAR'			,$urlAtualizar);
$tpl->set('ID'						,$id);
$tpl->set('FORMANDOS'				,$usuCombo);
$tpl->set('DISABLED'				,$disabled);
$tpl->set('NUM_FORMANDO'			,$numAtivo);
$tpl->set('NUM_RIFAS'				,$numeroFinal);
$tpl->set('COD_RIFA'				,$codRifa);
$tpl->set('NOME'					,$nome);
$tpl->set('PREMIO'					,$premio);
$tpl->set('CUSTO'					,$custo);
$tpl->set('LOCAL_SORTEIO'			,$localSorteio);
$tpl->set('DATA_SORTEIO'			,$dataSorteio);
$tpl->set('QTDE_OBRIGATORIO'		,$qtdeObrigatorio);
$tpl->set('QTDE_OBRIGATORIO_INFO'	,$qtdeObrigatorioInfo);
$tpl->set('VALOR'					,$valor);
$tpl->set('IND_RIFA_ELETRONICA'		,$indRifaEletronica);
$tpl->set('DP'						,\Zage\App\Util::getCaminhoCorrespondente(__FILE__,\Zage\App\ZWS::EXT_DP,\Zage\App\ZWS::CAMINHO_RELATIVO));
$tpl->set('IC'						,$_icone_);
$tpl->set('COD_MENU'				,$_codMenu_);

#################################################################################
## Por fim exibir a página HTML
#################################################################################
$tpl->show();
