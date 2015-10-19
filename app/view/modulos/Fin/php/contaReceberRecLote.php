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
if (!isset($aSelContas)) \Zage\App\Erro::halt('Falta de Parâmetros 3');
$aSelContas		= explode(",",$aSelContas);


#################################################################################
## Resgata as informações do banco
#################################################################################
$contas		= $em->getRepository('Entidades\ZgfinContaReceber')->findBy(array('codOrganizacao' => $system->getCodOrganizacao(), 'codigo' => $aSelContas));

if (sizeof($contas) == 0) {
	\Zage\App\Erro::halt($tr->trans('Conta[s] não encontrada !!!'));
}


#################################################################################
## Cria o objeto do Grid (bootstrap)
#################################################################################
$grid			= \Zage\App\Grid::criar(\Zage\App\Grid\Tipo::TP_BOOTSTRAP,"GContaCon");
$grid->setPagingType(\Zage\App\Grid\Tipo::PG_NONE);
$grid->setFiltro(0);
$grid->setMostraInfo(0);
$grid->adicionaTexto($tr->trans('NUMERO'), 				10, $grid::CENTER	,'');
$grid->adicionaTexto($tr->trans('DESCRICAO'),			20, $grid::CENTER	,'descricao');
$grid->adicionaTexto($tr->trans('CLIENTE'),				20, $grid::CENTER	,'codPessoa:nome');
$grid->adicionaTexto($tr->trans('PARC'),				8, $grid::CENTER	,'');
$grid->adicionaMoeda($tr->trans('VALOR TOTAL'),			20, $grid::CENTER	,'');
$grid->adicionaMoeda($tr->trans('SALDO A RECEBER'),		20, $grid::CENTER	,'');
$grid->importaDadosDoctrine($contas);


$aNaoPode	= array();

for ($i = 0; $i < sizeof($contas); $i++) {

	#################################################################################
	## Valida o status das contas
	#################################################################################
	switch ($contas[$i]->getCodStatus()->getCodigo()) {
		case "A":
		case "P":
			$podeRec	= true;
			break;
		default:
			$aNaoPode[]	= $contas[$i]->getNumero();
			$podeRec	= false;
			break;
	}
	

	#################################################################################
	## Definir o valor do campo Número
	#################################################################################
	if ($podeRec)	{
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
	$valTotal	= \Zage\Fin\ContaReceber::calculaValorTotal($contas[$i]);
	$grid->setValorCelula($i,4,$valTotal);

	#################################################################################
	## Definir o valor a cancelar
	#################################################################################
	$valCanc	= \Zage\Fin\ContaReceber::getSaldoAReceber($contas[$i]->getCodigo());
	$grid->setValorCelula($i,5,$valCanc);

}

if (sizeof($aNaoPode) > 0) {
	$podeRec	= "disabled";
	if (sizeof($aNaoPode) > 1) {
		$mensagem = $tr->trans('Contas não podem ser recebidas, status não permitido');
	}else{
		$mensagem = $tr->trans('Conta não pode ser recebida, status não permitido');
	}
}else{
	
	if ($contas[0]->getCodStatus()->getCodigo() == "P") {
		$inicioMensagem		= "Deseja realmente efetivar o recebimento do saldo ";
	}else{
		$inicioMensagem		= "Deseja realmente efetivar o recebimento ";
	}
	
	if (sizeof($contas) > 1) {
		$mensagem	= $inicioMensagem . "das ".sizeof($contas)." contas abaixo ?";
	}else{
		$mensagem	= $inicioMensagem . "da conta abaixo ?";
	}

	$podeRec	= "";
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
	$urlVoltar			= ROOT_URL . "/Fin/contaReceberLis.php?id=".$id;
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
## Select da Conta de Débito
#################################################################################
try {
	$aContaCre	= $em->getRepository('Entidades\ZgfinConta')->findBy(array('codOrganizacao' => $system->getCodOrganizacao()),array('nome' => 'ASC'));
	$oContaCre	= $system->geraHtmlCombo($aContaCre,	'CODIGO', 'NOME',	'', '');
} catch (\Exception $e) {
	\Zage\App\Erro::halt($e->getMessage(),__FILE__,__LINE__);
}

#################################################################################
## Definir os valores padrões de alguns campos
#################################################################################
$dataRec	= date($system->config["data"]["dateFormat"]);


#################################################################################
## Carregando o template html
#################################################################################
$tpl	= new \Zage\App\Template();
$tpl->load(\Zage\App\Util::getCaminhoCorrespondente(__FILE__, \Zage\App\ZWS::EXT_HTML));

#################################################################################
## Define os valores das variáveis
#################################################################################
$tpl->set('ID'					,$id);
$tpl->set('TITULO'				,'Recebimento de Contas em Lote');
$tpl->set('COD_CONTA'			,implode(",",$aSelContas));
$tpl->set('GRID'				,$htmlGrid);
$tpl->set('DATA_REC'			,$dataRec);
$tpl->set('MENSAGEM'			,$mensagem);
$tpl->set('FORMAS_PAG'			,$oFormaPag);
$tpl->set('CONTAS_CRE'			,$oContaCre);
$tpl->set('PODE_REC'			,$podeRec);
$tpl->set('URL_VOLTAR'			,$urlVoltar);
$tpl->set('DP_MODAL'			,\Zage\App\Util::getCaminhoCorrespondente(__FILE__,\Zage\App\ZWS::EXT_DP,\Zage\App\ZWS::CAMINHO_RELATIVO));

#################################################################################
## Por fim exibir a página HTML
#################################################################################
$tpl->show();

