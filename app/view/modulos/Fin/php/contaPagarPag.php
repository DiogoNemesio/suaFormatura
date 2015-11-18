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
global $em,$system,$tr;

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
if (!isset($codConta)) \Zage\App\Erro::halt('Falta de Parâmetros 2');

#################################################################################
## Resgata as informações do banco
#################################################################################
$oConta		= $em->getRepository('Entidades\ZgfinContaPagar')->findOneBy(array('codOrganizacao' => $system->getCodOrganizacao(), 'codigo' => $codConta));

if (!$oConta) {
	\Zage\App\Erro::halt('Conta não encontrada');
}

#################################################################################
## Indicador de somente visualização
#################################################################################
$indSomenteVis		= $oConta->getIndSomenteVisualizar();

if ($indSomenteVis)	\Zage\App\Erro::halt($tr->trans('Conta não pode ser confirmada, pois é somente de visualização (%s)',array('%s' => $oConta->getCodStatus()->getCodigo())));

#################################################################################
## Valida o status da conta
#################################################################################
switch ($oConta->getCodStatus()->getCodigo()) {
	case "A":
	case "P":
		$podePag	= ($indSomenteVis) ? false : true;
		break;
	default:
		$podePag	= false;
		break;
}

if (!$podePag) {
	\Zage\App\Erro::halt($tr->trans('Conta não pode ser confirmada, status não permitido (%s)',array('%s' => $oConta->getCodStatus()->getCodigo())));
}

$codFormaPag		= ($oConta->getCodFormaPagamento() != null) ? $oConta->getCodFormaPagamento()->getCodigo() : null;
$codContaPag		= ($oConta->getCodConta() != null) ? $oConta->getCodConta()->getCodigo() : null;

$dataPag			= ($oConta->getDataVencimento() != null) 	? $oConta->getDataVencimento()->format($system->config["data"]["dateFormat"]) : null;

$contaPag			= new \Zage\Fin\ContaPagar();

if (!$contaPag->getValorJaPago($codConta)) {
	$valor				= \Zage\App\Util::to_float($oConta->getValor());
	$valorJuros			= \Zage\App\Util::to_float($oConta->getValorJuros());
	$valorMora			= \Zage\App\Util::to_float($oConta->getValorMora());
	$valorDesconto		= \Zage\App\Util::to_float($oConta->getValorDesconto());
	$valorOutros		= \Zage\App\Util::to_float($oConta->getValorOutros());
	
}else{
	$valor				= \Zage\App\Util::to_float($contaPag->getSaldoAPagar($codConta));
	$valorJuros			= 0;
	$valorMora			= 0;
	$valorDesconto		= 0;
	$valorOutros		= 0;
}

$documento				= "";


if (!isset($urlVoltar) || (!$urlVoltar)) {
	$urlVoltar			= ROOT_URL . "/Fin/contaPagarLis.php?id=".$id;
}else{
	$urlVoltar			= $urlVoltar . "&id=".$id;
}


#################################################################################
## Select da Forma de Pagamento
#################################################################################
try {
	$aFormaPag	= $em->getRepository('Entidades\ZgfinFormaPagamento')->findBy(array(),array('descricao' => 'ASC'));
	$oFormaPag	= $system->geraHtmlCombo($aFormaPag,	'CODIGO', 'DESCRICAO',	$codFormaPag, '');
} catch (\Exception $e) {
	\Zage\App\Erro::halt($e->getMessage(),__FILE__,__LINE__);
}

#################################################################################
## Select da Conta de Débito
#################################################################################
try {

	#################################################################################
	## Verifica se a formatura está sendo administrada por um Cerimonial, para resgatar as contas do cerimonial tb
	#################################################################################
	$oFmtAdm		= \Zage\Fmt\Formatura::getCerimonalAdm($system->getCodOrganizacao());

	if ($oFmtAdm)	{
		$aCntCer	= $em->getRepository('Entidades\ZgfinConta')->findBy(array('codOrganizacao' => $oFmtAdm->getCodigo()),array('nome' => 'ASC'));
	}else{
		$aCntCer	= null;
	}

	$aConta		= $em->getRepository('Entidades\ZgfinConta')->findBy(array('codOrganizacao' => $system->getCodOrganizacao()),array('nome' => 'ASC'));

	if ($aCntCer) {
		$oConta		= "<optgroup label='Contas do Cerimonial'>";
		for ($i = 0; $i < sizeof($aCntCer); $i++) {
			$selected = ($aCntCer[$i]->getCodigo() == $codContaPag) ? "selected" : "";
			$oConta	.= "<option value='".$aCntCer[$i]->getCodigo()."' $selected>".$aCntCer[$i]->getNome()."</option>";
		}
		$oConta		.= '</optgroup>';
		if ($aConta) {
			$oConta		.= "<optgroup label='Contas da Formatura'>";
			for ($i = 0; $i < sizeof($aConta); $i++) {
				$selected = ($aConta[$i]->getCodigo() == $codContaPag) ? "selected" : "";
				$oConta	.= "<option value='".$aConta[$i]->getCodigo()."' $selected>".$aConta[$i]->getNome()."</option>";
			}
			$oConta		.= '</optgroup>';
		}
	}else{
		$oConta		= $system->geraHtmlCombo($aConta,	'CODIGO', 'NOME',	$codContaPag, '');
	}


} catch (\Exception $e) {
	\Zage\App\Erro::halt($e->getMessage(),__FILE__,__LINE__);
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
$tpl->set('TITULO'				,'Pagamento de Conta');
$tpl->set('COD_CONTA'			,$codConta);
$tpl->set('FORMAS_PAG'			,$oFormaPag);
$tpl->set('CONTAS_DEB'			,$oConta);
$tpl->set('DATA_PAG'			,$dataPag);
$tpl->set('VALOR'				,\Zage\App\Util::formataDinheiro($valor));
$tpl->set('VALOR_JUROS'			,\Zage\App\Util::formataDinheiro($valorJuros));
$tpl->set('VALOR_MORA'			,\Zage\App\Util::formataDinheiro($valorMora));
$tpl->set('VALOR_DESCONTO'		,\Zage\App\Util::formataDinheiro($valorDesconto));
$tpl->set('VALOR_OUTROS'		,\Zage\App\Util::formataDinheiro($valorOutros));
$tpl->set('DOCUMENTO'			,$documento);
$tpl->set('URL_VOLTAR'			,$urlVoltar);
$tpl->set('DP_MODAL'			,\Zage\App\Util::getCaminhoCorrespondente(__FILE__,\Zage\App\ZWS::EXT_DP,\Zage\App\ZWS::CAMINHO_RELATIVO));

#################################################################################
## Por fim exibir a página HTML
#################################################################################
$tpl->show();

