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
	\Zage\App\Erro::halt('FALTA PARÂMENTRO : ID');
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
## Resgata informações da rifa
#################################################################################
if (!isset($codRifa)) \Zage\App\Erro::halt('FALTA PARÂMENTRO : COD_RIFA');

$info 		= $em->getRepository('Entidades\ZgfmtRifa')->findOneBy(array('codigo' => $codRifa));

if (!$info){
	\Zage\App\Erro::halt($tr->trans('Rifa não encontrada').' (COD_RIFA)');
}


#################################################################################
## Resgata os dados do grid
#################################################################################
try {
	$rifas	= \Zage\Fmt\Rifa::listaNumRifasPorFormando($system->getCodOrganizacao(),$codRifa);
} catch (\Exception $e) {
	\Zage\App\Erro::halt($e->getMessage());
}

#################################################################################
## Cria o objeto do Grid (bootstrap)
#################################################################################
$grid			= \Zage\App\Grid::criar(\Zage\App\Grid\Tipo::TP_BOOTSTRAP,"GFin");
$grid->adicionaTexto($tr->trans('FORMANDO'),			20, $grid::CENTER	,'');
$grid->adicionaTexto($tr->trans('QTDE OBRIGATÓRIA'),	10, $grid::CENTER	,'');
$grid->adicionaMoeda($tr->trans('VALOR DA RIFA (R$)'),	10, $grid::CENTER	,'');
$grid->adicionaTexto($tr->trans('QTDE VENDIDA'),		10, $grid::CENTER	,'');
$grid->adicionaMoeda($tr->trans('A PAGAR (R$)'),		10, $grid::CENTER	,'');
$grid->adicionaMoeda($tr->trans('TOTAL PAGO(R$)'),		10, $grid::CENTER	,'');
$grid->adicionaIcone(null,'fa fa-money green',$tr->trans('Receber'));
$grid->importaDadosDoctrine($rifas);

#################################################################################
## Popula os valores dos botões
#################################################################################
for ($i = 0; $i < sizeof($rifas); $i++) {
	$uid	= \Zage\App\Util::encodeUrl('_codMenu_='.$_codMenu_.'&_icone_='.$_icone_.'&codRifa='.$codRifa.'&codUsuario='.$rifas[$i]["codigo"].'&url='.$url);
	$grid->setValorCelula($i,0,$rifas[$i]["nome"]);
	$grid->setValorCelula($i,1,$rifas[$i]["qtdeObrigatorio"]);
	$grid->setValorCelula($i,2,$rifas[$i]["valorUnitario"]);
	$grid->setValorCelula($i,3,$rifas[$i]["num"]);
	
	if ($rifas[$i]["num"] >= $rifas[$i]["qtdeObrigatorio"]){
		$total = $rifas[$i]["num"] * $rifas[$i]["valorUnitario"];
	}else{
		$total = $rifas[$i]["qtdeObrigatorio"] * $rifas[$i]["valorUnitario"];
	}
	
	$grid->setValorCelula($i,4,$total);
	$grid->setUrlCelula($i,6,"javascript:zgAbreModal('".ROOT_URL."/Fmt/rifaFinRec.php?id=".$uid."');");
	
	
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
## Gerar a url de adicão
#################################################################################
$urlVoltar			= ROOT_URL.'/Fmt/rifaLis.php?id='.\Zage\App\Util::encodeUrl('_codMenu_='.$_codMenu_.'&_icone_='.$_icone_);
$urlAtualizar		= ROOT_URL.'/Fmt/rifaFin.php?id='.\Zage\App\Util::encodeUrl('_codMenu_='.$_codMenu_.'&_icone_='.$_icone_.'&codRifa='.$codRifa);
#################################################################################
## Carregando o template html
#################################################################################
$tpl	= new \Zage\App\Template();
$tpl->load(\Zage\App\Util::getCaminhoCorrespondente(__FILE__, \Zage\App\ZWS::EXT_HTML));

#################################################################################
## Define os valores das variáveis
#################################################################################
$tpl->set('GRID'			,$htmlGrid);
$tpl->set('NOME'			,$tr->trans("Receber pagamentos"));
$tpl->set('URLVOLTAR'		,$urlVoltar);
$tpl->set('URLATUALIZAR'	,$urlAtualizar);
$tpl->set('NOME_RIFA'		,$info->getNome());
$tpl->set('IC'				,$_icone_);

#################################################################################
## Por fim exibir a página HTML
#################################################################################
$tpl->show();
