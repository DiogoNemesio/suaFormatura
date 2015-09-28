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
global $em,$tr,$system;

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

if ($info->getIndRifaEletronica() == 1){
	$nomeQtde 		= 'QTDE VENDIDA';
	$nomeVendido	= "VENDIDO (R$)";
}else{
	$nomeQtde 		= 'QTDE VENDIDA/GERADA';
	$nomeVendido	= "VENDIDO/GERADO (R$)";
}

#################################################################################
## Criar o objeto da data do sorteio
#################################################################################
$dataSorteio		= $info->getDataSorteio();
$hoje				= new \DateTime();


#################################################################################
## Verificar se pode receber a rifa
#################################################################################
if ($info->getNumeroVencedor() || $hoje > $dataSorteio) {
	$podeReceber		= true;
}else{
	$podeReceber		= false;
}


#################################################################################
## Resgata os dados do grid
#################################################################################
try {
	$rifas		= \Zage\Fmt\Rifa::listaNumRifasPorFormando($system->getCodOrganizacao(),$codRifa);
} catch (\Exception $e) {
	\Zage\App\Erro::halt($e->getMessage());
}

#################################################################################
## Cria o objeto do Grid (bootstrap)
#################################################################################
$grid			= \Zage\App\Grid::criar(\Zage\App\Grid\Tipo::TP_BOOTSTRAP,"GFin");
$grid->adicionaTexto($tr->trans('FORMANDO'),			20, $grid::CENTER	,'NOME');
$grid->adicionaTexto($tr->trans('QTDE OBRIGATÓRIA'),	10, $grid::CENTER	,'');
$grid->adicionaMoeda($tr->trans('VALOR DA RIFA (R$)'),	10, $grid::CENTER	,'');
$grid->adicionaTexto($tr->trans($nomeQtde),				10, $grid::CENTER	,'NUM');
$grid->adicionaMoeda($tr->trans($nomeVendido),			10, $grid::CENTER	,'');
$grid->adicionaMoeda($tr->trans('TOTAL A PAGAR (R$)'),	10, $grid::CENTER	,'');
$grid->adicionaMoeda($tr->trans('JÁ PAGO (R$)'),		10, $grid::CENTER	,'');
$grid->adicionaIcone(null,'fa fa-money green',$tr->trans('Receber'));
//$grid->importaDadosDoctrine($rifas);
$grid->importaDadosArray($rifas);

#################################################################################
## Popula os valores dos botões
#################################################################################
for ($i = 0; $i < sizeof($rifas); $i++) {
	$uid	= \Zage\App\Util::encodeUrl('_codMenu_='.$_codMenu_.'&_icone_='.$_icone_.'&codRifa='.$codRifa.'&codFormando='.$rifas[$i]["CODIGO"].'&url='.$url);
	$grid->setValorCelula($i,1,$info->getQtdeObrigatorio());
	$grid->setValorCelula($i,2,$info->getValorUnitario());
	
	#################################################################################
	## Calcula o valor total que o formando deve pagar
	#################################################################################
	if ($rifas[$i]["NUM"] >= $info->getQtdeObrigatorio()){
		$total = $rifas[$i]["NUM"] * $info->getValorUnitario();
	}else{
		$total = $info->getQtdeObrigatorio() * $info->getValorUnitario();
	}

	
	#################################################################################
	## Resgatar a quantidade vendida caso a rifa não seja eletrônica
	#################################################################################
	if ($info->getIndRifaEletronica() != 1) {
		$infoVenda		= $em->getRepository('Entidades\ZgfmtRifaFormando')->findOneBy(array('codRifa' => $codRifa, 'codFormando' => $rifas[$i]["CODIGO"]));
		if ($infoVenda)		{
			$qtdeVendida		= $infoVenda->getQtdeVendida();
			$grid->setValorCelula($i,3,"(".$qtdeVendida."/".$rifas[$i]["NUM"].")");
		}
	}
	
	
	#################################################################################
	## Calcula o valor que o formando conseguiu arrecadar
	#################################################################################
	$grid->setValorCelula($i,4,($rifas[$i]["NUM"] * $info->getValorUnitario()));
	
	#################################################################################
	## Grupo de Associação da rifa com a conta
	#################################################################################
	$codGrpAssociacao	= "RIFA_".$info->getCodigo(). "_".$rifas[$i]["CODIGO"];
	
	#################################################################################
	## Verificar se a conta já foi gerada
	#################################################################################
	$oConta				= $em->getRepository('Entidades\ZgfinContaReceber')->findOneBy(array('codOrganizacao' => $system->getCodOrganizacao(),'codGrupoAssociacao' => $codGrpAssociacao));
	
	if (!$oConta)		{
		$totalPago		= 0;
		$valAPagar		= $total;
	}else{
		$totalPago		= \Zage\Fin\ContaReceber::getValorJaRecebido($oConta->getCodigo());
		$valAPagar		= $total - $totalPago;
	}
	
	$grid->setValorCelula($i,5,$total);
	$grid->setValorCelula($i,6,$totalPago);
	
	if ($valAPagar > 0 && $podeReceber == true)	{
		$grid->setUrlCelula($i,7,"javascript:zgAbreModal('".ROOT_URL."/Fmt/rifaFinRec.php?id=".$uid."');");
	}else{
		$grid->desabilitaCelula($i, 7);
	}
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
