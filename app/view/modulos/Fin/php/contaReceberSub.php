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

if (sizeof($contas) == 0) {
	\Zage\App\Erro::halt($tr->trans('Conta[s] não encontrada !!!'));
}

$indPodeSub		= true;
$valorSub		= 0;

for ($i = 0; $i < sizeof($contas); $i++) {

	#################################################################################
	## Valida o status das contas
	#################################################################################
	switch ($contas[$i]->getCodStatus()->getCodigo()) {
		case "A":
		case "P":
			break;
		default:
			$indPodeSub	= false;
			break;
	}
	
	#################################################################################
	## Definir o valor a ser substituído
	#################################################################################
	$valPend	= \Zage\Fin\ContaReceber::getSaldoAReceber($contas[$i]->getCodigo());
	$valorSub	+= $valPend;
	
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
## Select da Conta de Débito
#################################################################################
try {
	$aConta		= $em->getRepository('Entidades\ZgfinConta')->findBy(array('codOrganizacao' => $system->getCodOrganizacao()),array('nome' => 'ASC'));
	$oConta		= $system->geraHtmlCombo($aConta,	'CODIGO', 'NOME',	'', '');
} catch (\Exception $e) {
	\Zage\App\Erro::halt($e->getMessage(),__FILE__,__LINE__);
}

#################################################################################
## Select da Categoria
#################################################################################
try {
	$aCat	= \Zage\Fin\Categoria::listaCombo("D");
	$oCat    	= "<option value=\"\"></option>";
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
	}
} catch (\Exception $e) {
	\Zage\App\Erro::halt($e->getMessage(),__FILE__,__LINE__);
}

#################################################################################
## Select do Centro de Custo
#################################################################################
try {
	$aCentroCusto	= $em->getRepository('Entidades\ZgfinCentroCusto')->findBy(array('codOrganizacao' => $system->getCodOrganizacao(),'indDebito' => 1),array('descricao' => 'ASC'));
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
$tpl->set('VALOR_TOTAL'				,number_format($valorSub,2,',',''));
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

