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
if (!isset($_GET["cid"])) \Zage\App\Erro::halt('Falta de Parâmetros 2');


#################################################################################
## Descompacta o CID
#################################################################################
$cid			= \Zage\App\Util::antiInjection($_GET["cid"]);
\Zage\App\Util::descompactaId($cid);


#################################################################################
## Monta o array
#################################################################################
if (!isset($aSelTransfs)) \Zage\App\Erro::halt('Falta de Parâmetros 3');
$aSelTransfs		= explode(",",$aSelTransfs);


#################################################################################
## Resgata as informações do banco
#################################################################################
$transferencias		= $em->getRepository('Entidades\ZgfinTransferencia')->findBy(array('codFilial' => $system->getcodOrganizacao(), 'codigo' => $aSelTransfs));

if (sizeof($transferencias) == 0) {
	\Zage\App\Erro::halt($tr->trans('Transferência[s] não encontrada !!!'));
}


#################################################################################
## Cria o objeto do Grid (bootstrap)
#################################################################################
$grid			= \Zage\App\Grid::criar(\Zage\App\Grid\Tipo::TP_BOOTSTRAP,"GTransfCon");
$grid->setPagingType(\Zage\App\Grid\Tipo::PG_NONE);
$grid->setFiltro(0);
$grid->setMostraInfo(0);
$grid->adicionaTexto($tr->trans('NUMERO'), 				10, $grid::CENTER	,'');
$grid->adicionaTexto($tr->trans('DESCRICAO'),			20, $grid::CENTER	,'descricao');
$grid->adicionaTexto($tr->trans('PARC'),				8, $grid::CENTER	,'');
$grid->adicionaTexto($tr->trans('ORIGEM'),				15, $grid::CENTER	,'codContaOrigem:nome');
$grid->adicionaTexto($tr->trans('DESTINO'),				15, $grid::CENTER	,'codContaDestino:nome');
$grid->adicionaMoeda($tr->trans('VALOR TOTAL'),			15, $grid::CENTER	,'');
$grid->adicionaMoeda($tr->trans('SALDO A TRANSFERIR'),	15, $grid::CENTER	,'');
$grid->importaDadosDoctrine($transferencias);


$aNaoPode	= array();

for ($i = 0; $i < sizeof($transferencias); $i++) {

	#################################################################################
	## Valida o status das contas
	#################################################################################
	switch ($transferencias[$i]->getCodStatus()->getCodigo()) {
		case "P":
		case "PA":
			$podeTransf	= true;
			break;
		default:
			$aNaoPode[]	= $transferencias[$i]->getNumero();
			$podeTransf	= false;
			break;
	}
	

	#################################################################################
	## Definir o valor do campo Número
	#################################################################################
	if ($podeTransf)	{
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
	$valTotal	= ( floatval($transferencias[$i]->getValor()) - floatval($transferencias[$i]->getValorCancelado())  );
	$grid->setValorCelula($i,5,$valTotal);

	#################################################################################
	## Definir o valor a Transferir
	#################################################################################
	$saldo	= \Zage\Fin\Transferencia::getSaldoATransferir($transferencias[$i]->getCodigo());
	$grid->setValorCelula($i,6,$saldo);

}

if (sizeof($aNaoPode) > 0) {
	$podeTransf	= "disabled";
	if (sizeof($aNaoPode) > 1) {
		$mensagem = $tr->trans('Transferências não podem ser recebidas, status não permitido');
	}else{
		$mensagem = $tr->trans('Transferência não pode ser recebida, status não permitido');
	}
}else{
	
	if (sizeof($transferencias) > 1) {
		$inicioMensagem 	= "Deseja realmente realizar as transferências ";
	}else{
		$inicioMensagem 	= "Deseja realmente realizar a transferência ";
	}
	
	if ($transferencias[0]->getCodStatus()->getCodigo() == "PA") {
		$mensagem			= $inicioMensagem . "dos saldos abaixo ?";
	}else{
		$mensagem			= $inicioMensagem . "abaixo ? ";
	}
	
	$podeTransf	= "";
}

#################################################################################
## Gerar o código html do grid
#################################################################################
try {
	$htmlGrid	= $grid->getHtmlCode();
} catch (\Exception $e) {
	\Zage\App\Erro::halt($e->getMessage());
}

if (!isset($urlVoltar) || (!$urlVoltar)) {
	$urlVoltar			= ROOT_URL . "/Fin/transferenciaLis.php?id=".$id;
}else{
	$urlVoltar			= $urlVoltar . "&id=".$id;
}


#################################################################################
## Select da Forma de Pagamento
#################################################################################
try {
	$aFormaPag	= $em->getRepository('Entidades\ZgfinFormaPagamento')->findBy(array(),array('descricao' => 'ASC'));
	$oFormaPag	= $system->geraHtmlCombo($aFormaPag,	'CODIGO', 'DESCRICAO',	'', '');
} catch (\Exception $e) {
	\Zage\App\Erro::halt($e->getMessage(),__FILE__,__LINE__);
}

#################################################################################
## Select da Conta de Origem
#################################################################################
try {
	$aConta			= $em->getRepository('Entidades\ZgfinConta')->findBy(array('codFilial' => $system->getcodOrganizacao()),array('nome' => 'ASC'));
	$oContaOrig		= $system->geraHtmlCombo($aConta,	'CODIGO', 'NOME',	'', '');
} catch (\Exception $e) {
	\Zage\App\Erro::halt($e->getMessage(),__FILE__,__LINE__);
}

#################################################################################
## Select da Conta de Destino
#################################################################################
try {
	$oContaDest		= $system->geraHtmlCombo($aConta,	'CODIGO', 'NOME',	'', '');
} catch (\Exception $e) {
	\Zage\App\Erro::halt($e->getMessage(),__FILE__,__LINE__);
}

#################################################################################
## Definir os valores padrões de alguns campos
#################################################################################
$dataTransf	= date($system->config["data"]["dateFormat"]);


#################################################################################
## Carregando o template html
#################################################################################
$tpl	= new \Zage\App\Template();
$tpl->load(\Zage\App\Util::getCaminhoCorrespondente(__FILE__, \Zage\App\ZWS::EXT_HTML));

#################################################################################
## Define os valores das variáveis
#################################################################################
$tpl->set('ID'					,$id);
$tpl->set('TITULO'				,'Realização de Transferência em Lote');
$tpl->set('COD_TRANSF'			,implode(",",$aSelTransfs));
$tpl->set('GRID'				,$htmlGrid);
$tpl->set('DATA_TRANSF'			,$dataTransf);
$tpl->set('MENSAGEM'			,$mensagem);
$tpl->set('FORMAS_PAG'			,$oFormaPag);
//$tpl->set('CONTAS_ORIG'			,$oContaOrig);
//$tpl->set('CONTAS_DEST'			,$oContaDest);
$tpl->set('PODE_TRANSF'			,$podeTransf);
$tpl->set('URL_VOLTAR'			,$urlVoltar);
$tpl->set('DP_MODAL'			,\Zage\App\Util::getCaminhoCorrespondente(__FILE__,\Zage\App\ZWS::EXT_DP,\Zage\App\ZWS::CAMINHO_RELATIVO));

#################################################################################
## Por fim exibir a página HTML
#################################################################################
$tpl->show();

