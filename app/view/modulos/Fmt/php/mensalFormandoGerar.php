<?php
#################################################################################
## Includes
#################################################################################
if (defined('DOC_ROOT')) {
	include_once(DOC_ROOT . 'include.php');
}else{
	include_once('./include.php');
}

#################################################################################
## Variáveis globais
#################################################################################
global $system,$em,$tr;

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
## Resgata a url desse script
#################################################################################
$url		= ROOT_URL . "/Fmt/". basename(__FILE__)."?id=".$id;

#################################################################################
## Resgata informações do formando e da formatura
#################################################################################
if (!isset($codFormando)) \Zage\App\Erro::halt('FALTA PARÂMENTRO : COD_FORMANDO!');

$formando 		= $em->getRepository('Entidades\ZgsegUsuario')->findOneBy(array('codigo' => $codFormando));

if (!$formando){
	\Zage\App\Erro::halt($tr->trans('Ops! Não encontramos o formando selecionado. Por favor, tente novamente!').' (COD_FORMANDO)');
}

$oOrgFmt	= $em->getRepository('Entidades\ZgfmtOrganizacaoFormatura')->findOneBy(array('codOrganizacao' => $system->getCodOrganizacao()));

#################################################################################
## Variáveis usadas no cálculo das mensalidades
#################################################################################
$dataConclusao			= $oOrgFmt->getDataConclusao();
if (!$dataConclusao)	\Zage\App\Erro::halt("Data de Conclusão não informada");
$hoje					= new DateTime('now');
$interval				= $dataConclusao->diff($hoje);
$numMesesConc			= (($interval->format('%y') * 12) + $interval->format('%m'));
$diaVencimento			= ($oOrgFmt->getDiaVencimento()) ? $oOrgFmt->getDiaVencimento() : 5;
$dataVenc				= date($system->config["data"]["dateFormat"],mktime(0, 0, 0, date('m') + 1, $diaVencimento , date('Y')));

#################################################################################
## Taxas / Configurações
#################################################################################
$taxaAdmin				= \Zage\App\Util::to_float($oOrgFmt->getTaxaAdministracao());

#################################################################################
## Formatar as taxas
#################################################################################
if ($taxaAdmin		< 0)		$taxaAdmin		= 0;

#################################################################################
## Buscar o orçamento aceite, caso exista um, pois ele será usado como base
## Para calcular o valor pendente a ser gerado
## Se não existir, apenas não sugerir os valores a serem gerados
#################################################################################
$orcamento				= \Zage\Fmt\Orcamento::getVersaoAceita($system->getCodOrganizacao());
if (!$orcamento)		\Zage\App\Erro::halt("Nenhum orçamento aceito");
$valorOrcado			= \Zage\App\Util::to_float($oOrgFmt->getValorPrevistoTotal());
$qtdFormandosBase		= (int) $oOrgFmt->getQtdePrevistaFormandos();
$valorOrcadoFormando	= round($valorOrcado/$qtdFormandosBase,2);

#################################################################################
## Calcular o valor já provisionado por formando
#################################################################################
$valorProv				= \Zage\Fmt\Financeiro::getValorProvisionadoUnicoFormando($system->getCodOrganizacao(), $formando->getCpf());
$totalProv				= \Zage\App\Util::to_float($valorProv["mensalidade"]) + \Zage\App\Util::to_float($valorProv["sistema"]);

#################################################################################
## Calcular os valores totais e saldos
#################################################################################
if ($totalProv < $valorOrcadoFormando){
	$saldoAProvisionar = $valorOrcadoFormando - $totalProv;
}else{
	$saldoAProvisionar = 0;
}

#################################################################################
## Select da Forma de Pagamento
#################################################################################
try {
	$aFormaPag	= $em->getRepository('Entidades\ZgfinFormaPagamento')->findBy(array(),array('descricao' => 'ASC'));
	$oFormaPag	= $system->geraHtmlCombo($aFormaPag,	'CODIGO', 'DESCRICAO',	'BOL' , '');
} catch (\Exception $e) {
	\Zage\App\Erro::halt($e->getMessage(),__FILE__,__LINE__);
}

#################################################################################
## Select da Conta de Crédito
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
			$valBol		= ($aCntCer[$i]->getCodTipo()->getCodigo() == "CC") ? \Zage\Fmt\Financeiro::getValorBoleto($aCntCer[$i]->getCodigo()) : 0;
			$oConta	.= "<option value='".$aCntCer[$i]->getCodigo()."' zg-val-boleto='".$valBol."'>".$aCntCer[$i]->getNome()."</option>";
		}
		$oConta		.= '</optgroup>';
		if ($aConta) {
			$oConta		.= "<optgroup label='Contas da Formatura'>";
			for ($i = 0; $i < sizeof($aConta); $i++) {
				$valBol		= ($aConta[$i]->getCodTipo()->getCodigo() == "CC") ? \Zage\Fmt\Financeiro::getValorBoleto($aConta[$i]->getCodigo()) : 0;
				$oConta	.= "<option value='".$aConta[$i]->getCodigo()."' zg-val-boleto='".$valBol."'>".$aConta[$i]->getNome()."</option>";
			}
			$oConta		.= '</optgroup>';
		}
	}else{
		$oConta		= $system->geraHtmlCombo($aConta,	'CODIGO', 'NOME',	'', '');
	}


} catch (\Exception $e) {
	\Zage\App\Erro::halt($e->getMessage(),__FILE__,__LINE__);
}

#################################################################################
## Url Voltar
#################################################################################
$urlVoltar				= ROOT_URL."/Fin/contaReceberLis.php?id=".$id;

#################################################################################
## Carregando o template html
#################################################################################
$tpl	= new \Zage\App\Template();
$tpl->load(\Zage\App\Util::getCaminhoCorrespondente(__FILE__, \Zage\App\ZWS::EXT_HTML));

#################################################################################
## Define os valores das variáveis
#################################################################################
$tpl->set('GRID'					,$htmlGrid);
$tpl->set('IC'						,$_icone_);
$tpl->set('ID'						,$id);
$tpl->set('FILTER_URL'				,$url);
$tpl->set('DIVCENTRAL'				,$system->getDivCentral());
$tpl->set('CHECK_NAME'				,$checkboxName);
$tpl->set('NUM_MESES_MAX'			,$numMesesConc);

$tpl->set('SALDO_APROVISIONAR'		,\Zage\App\Util::formataDinheiro($saldoAProvisionar));
$tpl->set('SALDO_APROVISIONAR_FMT'	,\Zage\App\Util::to_money($saldoAProvisionar));
$tpl->set('NOME_FORMANDO'			,$formando->getNome());
$tpl->set('VALOR_ORC_FORMANDO'		,\Zage\App\Util::to_money($valorOrcadoFormando));
$tpl->set('TAXA_ADMIN'				,\Zage\App\Util::formataDinheiro($taxaAdmin));



$tpl->set('DATA_CONCLUSAO'			,$dataConclusao->format($system->config["data"]["dateFormat"]));
$tpl->set('DATA_VENC'				,$dataVenc);

$tpl->set('VALOR_TOTAL_FORMATURA'	,\Zage\App\Util::formataDinheiro($valorOrcado));
$tpl->set('TOTAL_POR_FORMANDO'		,\Zage\App\Util::formataDinheiro($totalPorFormando));
$tpl->set('TOTAL_POR_FORMANDO_FMT'	,\Zage\App\Util::to_money($totalPorFormando));
$tpl->set('VALOR_RECEBER'			,\Zage\App\Util::formataDinheiro($valorJaProvisionado));
$tpl->set('VALOR_RECEBER_FMT'		,\Zage\App\Util::to_money($valorJaProvisionado));
$tpl->set('SALDO_RECEBER'			,\Zage\App\Util::formataDinheiro($saldoAProvisionar));
$tpl->set('SALDO_RECEBER_FMT'		,\Zage\App\Util::to_money($saldoAProvisionar));
$tpl->set('SALDO_FORMANDO_FMT'		,\Zage\App\Util::to_money($saldo));
$tpl->set('TAXA_USO_FMT'			,\Zage\App\Util::to_money($taxaUsoTotalFormando));
$tpl->set('TAXA_USO'				,\Zage\App\Util::formataDinheiro($taxaUsoTotalFormando));
$tpl->set('TAXA_BOLETO_FMT'			,\Zage\App\Util::to_money($taxaBoleto));
$tpl->set('TAXA_BOLETO'				,\Zage\App\Util::formataDinheiro($taxaBoleto));

$tpl->set('TOTAL_TAXA_FMT'			,\Zage\App\Util::to_money($totalTaxa));
$tpl->set('CONTAS'					,$oConta);
$tpl->set('FORMAS_PAG'				,$oFormaPag);
$tpl->set('FORMATO_DATA'			,$system->config["data"]["jsDateFormat"]);
$tpl->set('DP'						,\Zage\App\Util::getCaminhoCorrespondente(__FILE__,\Zage\App\ZWS::EXT_DP,\Zage\App\ZWS::CAMINHO_RELATIVO));
$tpl->set('URL_VOLTAR'				,$urlVoltar);


#################################################################################
## Por fim exibir a página HTML
#################################################################################
$tpl->show();
