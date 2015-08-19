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
## Verifica os parâmetros obrigatórios
#################################################################################
if (!isset($codRifa)) \Zage\App\Erro::halt('Falta de Parâmetros : COD_RIFA');

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
$grid->adicionaTexto($tr->trans('QTDE DE BILHETES'),10, $grid::CENTER	,'codGeracao:codigo');
$grid->adicionaTexto($tr->trans('USUÁRIO'),			20, $grid::CENTER	,'codUsuario:nome');
$grid->adicionaDataHora($tr->trans('DATA'),	 		20, $grid::CENTER	,'data');
$grid->adicionaIcone(arquvo,'fa fa-cog red',$tr->trans('Arquivo para download'));

$grid->importaDadosDoctrine($rifaGera);

#################################################################################
## Popula os valores dos botões
#################################################################################
for ($i = 0; $i < sizeof($rifaGera); $i++) {
	$rid		= \Zage\App\Util::encodeUrl('_codMenu_='.$_codMenu_.'&_icone_='.$_icone_.'&codRifa='.$rifaGera[$i]->getCodigo().'&url='.$url);
	
	$grid->setUrlCelula($i,3,ROOT_URL.'/Fmt/rifaGera.php?id='.$rid);

}

#################################################################################
## Gerar o código html do grid
#################################################################################
try {
	$htmlGrid	= $grid->getHtmlCode();
} catch (\Exception $e) {
	\Zage\App\Erro::halt($e->getMessage());
}

echo $htmlGrid;