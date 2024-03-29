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
$oOrg		= $em->getRepository('Entidades\ZgadmOrganizacao')->findOneBy(array('codigo' => $system->getCodOrganizacao()));


if (sizeof($contas) == 0) {
	\Zage\App\Erro::halt($tr->trans('Conta[s] não encontrada !!!'));
}

$indPodeSub		= true;
$valorSub		= 0;
$outrosSub		= 0;
$totalSub		= 0;

for ($i = 0; $i < sizeof($contas); $i++) {

	#################################################################################
	## Resgata o perfil da conta
	#################################################################################
	$codPerfil	= ($contas[$i]->getCodContaPerfil()) ? $contas[$i]->getCodContaPerfil()->getCodigo() : 0;
	
	if (!\Zage\Fin\ContaAcao::verificaAcaoPermitida($codPerfil, $contas[$i]->getCodStatus()->getCodigo(), "SUB")) {
		$indPodeSub	= false;
	}
	
	#################################################################################
	## Definir o valor a ser substituído
	#################################################################################
	$saldoPend	= \Zage\Fin\ContaReceber::getSaldoAReceberDetalhado($contas[$i]->getCodigo());
	//$valPend	= \Zage\Fin\ContaReceber::getSaldoAReceber($contas[$i]->getCodigo());
	$valPend	= $saldoPend["PRINCIPAL"] + $saldoPend["JUROS"] + $saldoPend["MORA"]; 
	$outrosSub	+= $saldoPend["OUTROS"];
	$valorSub	+= $valPend;
	$totalSub	+= $valPend + $saldoPend["OUTROS"];
	
	#################################################################################
	## Salvar o Cliente / Fornecedor
	#################################################################################
	$codPessoa	= ($contas[$i]->getCodPessoa()) ? $contas[$i]->getCodPessoa()->getCodigo() : null;
	

}

#################################################################################
## Verifica se as contas podem ser substituídas
#################################################################################
if ($indPodeSub	== false) \Zage\App\Erro::halt($tr->trans('Existe alguma conta com o status que não permite substituição !!!'));


#################################################################################
## Url anterior
#################################################################################
if (!isset($urlVoltar) || (!$urlVoltar)) {
	$urlVoltar			= ROOT_URL . "/Fin/contaReceberLis.php?id=".$id;
}else{
	$urlVoltar			= $urlVoltar . "&id=".$id;
}

#################################################################################
## Select da Moeda
#################################################################################
try {
	$aMoeda		= $em->getRepository('Entidades\ZgfinMoeda')->findBy(array(),array('descricao' => 'ASC'));
	$oMoeda		= $system->geraHtmlCombo($aMoeda,	'CODIGO', 'DESCRICAO',	null, 		null);
} catch (\Exception $e) {
	\Zage\App\Erro::halt($e->getMessage(),__FILE__,__LINE__);
}

#################################################################################
## Select da Forma de Pagamento
#################################################################################
try {
	$aFormaPag	= $em->getRepository('Entidades\ZgfinFormaPagamento')->findBy(array(),array('descricao' => 'ASC'));
	$oFormaPag	= $system->geraHtmlCombo($aFormaPag,	'CODIGO', 'DESCRICAO',	null, '');
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
			$oConta	.= "<option value='".$aCntCer[$i]->getCodigo()."' >".$aCntCer[$i]->getNome()."</option>";
		}
		$oConta		.= '</optgroup>';
		if ($aConta) {
			$oConta		.= "<optgroup label='Contas da Formatura'>";
			for ($i = 0; $i < sizeof($aConta); $i++) {
				$oConta	.= "<option value='".$aConta[$i]->getCodigo()."'>".$aConta[$i]->getNome()."</option>";
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
## Select da Categoria
#################################################################################
try {
	$aCat	= \Zage\Fin\Categoria::listaCombo("C",$oOrg->getCodTipo()->getCodigo());
	$oCat   = "<option value=\"\"></option>";
	if ($aCat) {
		$aCatTemp	= array();
		$i 			= 0;
				
		foreach ($aCat as $cat) {
			$tDesc 	= ($cat->getCodCategoriaPai() != null) ? $cat->getCodCategoriaPai()->getDescricao() . "/" . $cat->getDescricao() : $cat->getDescricao();
			$aCatTemp[$tDesc]	= $cat->getCodigo();

		}
		
		ksort($aCatTemp);
		
		foreach ($aCatTemp as $cDesc => $cCod) {
			$oCat .= "<option value=\"".$cCod."\">".$cDesc.'</option>';
		}
	}else{
		$aCatTemp	= array();
	}
	
} catch (\Exception $e) {
	\Zage\App\Erro::halt($e->getMessage(),__FILE__,__LINE__);
}

#################################################################################
## Select do Centro de Custo
#################################################################################
try {
	$aCentroCusto	= \Zage\Fin\CentroCusto::listaCombo($system->getCodOrganizacao(),false,1,true);
	$oCentroCusto	= $system->geraHtmlCombo($aCentroCusto,	'CODIGO', 'DESCRICAO',	null, '');
} catch (\Exception $e) {
	\Zage\App\Erro::halt($e->getMessage(),__FILE__,__LINE__);
}


#################################################################################
## Select do Período de ocorrência
#################################################################################
try {
	$aPeriodo	= $em->getRepository('Entidades\ZgfinContaRecorrenciaPeriodo')->findBy(array(),array('descricao' => 'ASC'));
	$oPeriodo	= $system->geraHtmlCombo($aPeriodo,	'CODIGO', 'DESCRICAO',	"M", null);
} catch (\Exception $e) {
	\Zage\App\Erro::halt($e->getMessage(),__FILE__,__LINE__);
}

#################################################################################
## Definir os valores padrões de alguns campos
#################################################################################
$dataPag	= date($system->config["data"]["dateFormat"]);


#################################################################################
## Carregando o template html
#################################################################################
$tpl	= new \Zage\App\Template();
$tpl->load(\Zage\App\Util::getCaminhoCorrespondente(__FILE__, \Zage\App\ZWS::EXT_HTML));

#################################################################################
## Define os valores das variáveis
#################################################################################
$tpl->set('ID'						,$id);
$tpl->set('COD_CONTA'				,implode(",",$aSelContas));
$tpl->set('URL_VOLTAR'				,$urlVoltar);
$tpl->set('VALOR_TOTAL'				,number_format($totalSub,2,',',''));
$tpl->set('VALOR'					,number_format($valorSub,2,',',''));
$tpl->set('VALOR_OUTROS'			,number_format($outrosSub,2,',',''));
$tpl->set('DATA_VENC'				,date('d/m/Y'));
$tpl->set('MOEDAS'					,$oMoeda);
$tpl->set('CENTROS_CUSTO'			,$oCentroCusto);
$tpl->set('FORMAS_PAG'				,$oFormaPag);
$tpl->set('CATEGORIAS'				,$oCat);
$tpl->set('CONTAS'					,$oConta);
$tpl->set('PERIODOS_REC'			,$oPeriodo);
$tpl->set('COD_PESSOA'				,$codPessoa);
$tpl->set('APP_BS_TA_MINLENGTH'		,\Zage\Adm\Parametro::getValorSistema('APP_BS_TA_MINLENGTH'));
$tpl->set('APP_BS_TA_ITENS'			,\Zage\Adm\Parametro::getValorSistema('APP_BS_TA_ITENS'));
$tpl->set('APP_BS_TA_TIMEOUT'		,\Zage\Adm\Parametro::getValorSistema('APP_BS_TA_TIMEOUT'));
$tpl->set('DP'						,\Zage\App\Util::getCaminhoCorrespondente(__FILE__,\Zage\App\ZWS::EXT_DP,\Zage\App\ZWS::CAMINHO_RELATIVO));
$tpl->set('FORMATO_DATA'			,$system->config["data"]["jsDateFormat"]);

#################################################################################
## Por fim exibir a página HTML
#################################################################################
$tpl->show();

