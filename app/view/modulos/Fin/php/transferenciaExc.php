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
$system->checaPermissao($_codMenu_);

#################################################################################
## Verificar parâmetro obrigatório
#################################################################################
if (!isset($codTransf)) {
	if (!isset($_GET["cid"])) \Zage\App\Erro::halt('Falta de Parâmetros 2');
	$cid		= $_GET["cid"];
	\Zage\App\Util::descompactaId($cid);
	if (!isset($aSelTransfs)) \Zage\App\Erro::halt('Falta de Parâmetros 3');
	$aSelTransfs		= explode(",",$aSelTransfs);
}else{
	$aSelTransfs[]		= $codTransf;
}

#################################################################################
## Resgata as informações do banco
#################################################################################
$transferencias		= $em->getRepository('Entidades\ZgfinTransferencia')->findBy(array('codOrganizacao' => $system->getCodOrganizacao(), 'codigo' => $aSelTransfs));

if (sizeof($transferencias) == 0) {
	\Zage\App\Erro::halt($tr->trans('Transferência[s] não encontrada !!!'));
}

if (!isset($urlVoltar) || (!$urlVoltar)) {
	$urlVoltar			= ROOT_URL . "/Fin/transferenciaLis.php?id=".$id;
}else{
	$urlVoltar			= $urlVoltar . "&id=".$id;
}

#################################################################################
## Cria o objeto do Grid (bootstrap)
#################################################################################
$grid			= \Zage\App\Grid::criar(\Zage\App\Grid\Tipo::TP_BOOTSTRAP,"GTransfExc");
$grid->setPagingType(\Zage\App\Grid\Tipo::PG_NONE);
$grid->setFiltro(0);
$grid->setMostraInfo(0);
$grid->adicionaTexto($tr->trans('NUMERO'), 				20, $grid::CENTER	,'');
$grid->adicionaTexto($tr->trans('DESCRICAO'),			20, $grid::CENTER	,'descricao');
$grid->adicionaTexto($tr->trans('PARC'),				10, $grid::CENTER	,'');
$grid->adicionaTexto($tr->trans('ORIGEM'),				10, $grid::CENTER	,'codContaOrigem:nome');
$grid->adicionaTexto($tr->trans('DESTINO'),				10, $grid::CENTER	,'codContaDestino:nome');
$grid->adicionaMoeda($tr->trans('VALOR'),				20, $grid::CENTER	,'');
$grid->importaDadosDoctrine($transferencias);


#################################################################################
## Define os campos personalizados
#################################################################################
$aNaoPode	= array();
for ($i = 0; $i < sizeof($transferencias); $i++) {
	
	#################################################################################
	## Valida o status das contas
	#################################################################################
	switch ($transferencias[$i]->getCodStatus()->getCodigo()) {
		case "P":
			$podeExc	= true;
			break;
		default:
			$aNaoPode[]	= $transferencias[$i]->getNumero();
			$podeExc	= false;
			break;
	}
	
	#################################################################################
	## Definir o valor do campo Número
	#################################################################################
	if ($podeExc)	{
		$grid->setValorCelula($i, 0, $transferencias[$i]->getNumero());
	}else{
		$grid->setValorCelula($i, 0,"<label class='text-danger'>". $transferencias[$i]->getNumero()."</div>");
	}
	
	
	#################################################################################
	## Definir o valor do campo Parcela
	#################################################################################
	$parcela	= "(".$transferencias[$i]->getParcela() . "/".$transferencias[$i]->getNumParcelas().")";
	$grid->setValorCelula($i,2,$parcela);
	
	#################################################################################
	## Definir o valor total
	#################################################################################
	$valTotal	= ( floatval($transferencias[$i]->getValor()) - floatval($transferencias[$i]->getValorCancelado()) );
	$grid->setValorCelula($i,5,$valTotal);
	
}


if (sizeof($aNaoPode) > 0) {
	$podeExc	= "disabled";
	
	if (sizeof($aNaoPode) > 1) {
		$texto = $tr->trans('Transferências não podem ser excluídas, status não permitido');
	}else{
		$texto = $tr->trans('Transferência não pode ser excluída, status não permitido');
	}
}else{
	$podeExc	= "";
	
	if (sizeof($transferencias) > 1) {
		$texto	= "Deseja realmente remover as ".sizeof($transferencias)." transferências abaixo ???";
	}else{
		$texto	= "Deseja realmente remover a transferência abaixo ???";
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
## Carregando o template html
#################################################################################
$tpl	= new \Zage\App\Template();
$tpl->load(\Zage\App\Util::getCaminhoCorrespondente(__FILE__, \Zage\App\ZWS::EXT_HTML));

#################################################################################
## Define os valores das variáveis
#################################################################################
$tpl->set('ID'					,$id);
$tpl->set('TITULO'				,$tr->trans('Exclusão de Transferência'));
$tpl->set('TEXTO'				,$tr->trans($texto));
$tpl->set('PODE_EXC'			,$podeExc);
$tpl->set('URL_VOLTAR'			,$urlVoltar);
$tpl->set('COD_TRANSF'			,implode(",", $aSelTransfs));
$tpl->set('GRID'				,$htmlGrid);
$tpl->set('DP_MODAL'			,\Zage\App\Util::getCaminhoCorrespondente(__FILE__,\Zage\App\ZWS::EXT_DP,\Zage\App\ZWS::CAMINHO_RELATIVO));

#################################################################################
## Por fim exibir a página HTML
#################################################################################
$tpl->show();

