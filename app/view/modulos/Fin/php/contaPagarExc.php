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
if (!isset($codConta)) {
	if (!isset($_GET["cid"])) \Zage\App\Erro::halt('Falta de Parâmetros 2');
	$cid		= $_GET["cid"];
	\Zage\App\Util::descompactaId($cid);
	if (!isset($aSelContas)) \Zage\App\Erro::halt('Falta de Parâmetros 3');
	$aSelContas		= explode(",",$aSelContas);
}else{
	$aSelContas[]	= $codConta;
}

#################################################################################
## Resgata as informações do banco
#################################################################################
$contas		= $em->getRepository('Entidades\ZgfinContaPagar')->findBy(array('codOrganizacao' => $system->getCodOrganizacao(), 'codigo' => $aSelContas));

if (sizeof($contas) == 0) {
	\Zage\App\Erro::halt($tr->trans('Conta[s] não encontrada !!!'));
}

if (!isset($urlVoltar) || (!$urlVoltar)) {
	$urlVoltar			= ROOT_URL . "/Fin/contaPagarLis.php?id=".$id;
}else{
	$urlVoltar			= $urlVoltar . "&id=".$id;
}

#################################################################################
## Cria o objeto do Grid (bootstrap)
#################################################################################
$grid			= \Zage\App\Grid::criar(\Zage\App\Grid\Tipo::TP_BOOTSTRAP,"GContaExc");
$grid->setPagingType(\Zage\App\Grid\Tipo::PG_NONE);
$grid->setFiltro(0);
$grid->setMostraInfo(0);
$grid->adicionaTexto($tr->trans('NUMERO'), 				20, $grid::CENTER	,'');
$grid->adicionaTexto($tr->trans('DESCRICAO'),			20, $grid::CENTER	,'descricao');
$grid->adicionaTexto($tr->trans('FORNEC'),				20, $grid::CENTER	,'codPessoa:nome');
$grid->adicionaTexto($tr->trans('PARC'),				10, $grid::CENTER	,'');
$grid->adicionaMoeda($tr->trans('VALOR'),				20, $grid::CENTER	,'');
$grid->importaDadosDoctrine($contas);


#################################################################################
## Define os campos personalizados
#################################################################################
$aNaoPode	= array();
for ($i = 0; $i < sizeof($contas); $i++) {
	
	#################################################################################
	## Valida o status das contas
	#################################################################################
	switch ($contas[$i]->getCodStatus()->getCodigo()) {
		case "A":
			$podeExc	= true;
			break;
		default:
			$aNaoPode[]	= $contas[$i]->getNumero();
			$podeExc	= false;
			break;
	}
	
	#################################################################################
	## Definir o valor do campo Número
	#################################################################################
	if ($podeExc)	{
		$grid->setValorCelula($i, 0, $contas[$i]->getNumero());
	}else{
		$grid->setValorCelula($i, 0,"<label class='text-danger'>". $contas[$i]->getNumero()."</div>");
	}
	
	
	#################################################################################
	## Definir o valor do campo Parcela
	#################################################################################
	$parcela	= "(".$contas[$i]->getParcela() . "/".$contas[$i]->getNumParcelas().")";
	$grid->setValorCelula($i,3,$parcela);
	
	#################################################################################
	## Definir o valor total
	#################################################################################
	$valTotal	= ( floatval($contas[$i]->getValor()) + floatval($contas[$i]->getValorJuros()) + floatval($contas[$i]->getValorMora()) + floatval($contas[$i]->getValorOutros()) - floatval($contas[$i]->getValorDesconto()) - floatval($contas[$i]->getValorCancelado())  );
	$grid->setValorCelula($i,4,$valTotal);
	
}


if (sizeof($aNaoPode) > 0) {
	$podeExc	= "disabled";
	
	if (sizeof($aNaoPode) > 1) {
		$texto = $tr->trans('Contas não podem ser excluídas, status não permitido');
	}else{
		$texto = $tr->trans('Conta não pode ser excluída, status não permitido');
	}
}else{
	$podeExc	= "";
	
	if (sizeof($contas) > 1) {
		$texto	= "Deseja realmente remover as ".sizeof($contas)." contas abaixo ???";
	}else{
		$texto	= "Deseja realmente remover a conta abaixo ???";
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
$tpl->set('TITULO'				,$tr->trans('Exclusão de Conta'));
$tpl->set('TEXTO'				,$tr->trans($texto));
$tpl->set('PODE_EXC'			,$podeExc);
$tpl->set('URL_VOLTAR'			,$urlVoltar);
$tpl->set('COD_CONTA'			,implode(",", $aSelContas));
$tpl->set('GRID'				,$htmlGrid);
$tpl->set('DP_MODAL'			,\Zage\App\Util::getCaminhoCorrespondente(__FILE__,\Zage\App\ZWS::EXT_DP,\Zage\App\ZWS::CAMINHO_RELATIVO));

#################################################################################
## Por fim exibir a página HTML
#################################################################################
$tpl->show();

