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
## Resgata as informações do banco
#################################################################################
if ($codProduto) {
	try {
		$info = $em->getRepository('Entidades\ZgestProduto')->findOneBy(array('codOrganizacao' => $system->getCodOrganizacao(), 'codigo' => $codProduto));

	} catch (\Exception $e) {
		\Zage\App\Erro::halt($e->getMessage());
	}

	$nome			= $info->getNome();
	$descricao		= $info->getDescricao();
	$preReserva		= $info->getQtdeDiasPreReserva();
	$diasIndis		= $info->getNumDiasIndisponivel();
	$ativo			= ($info->getIndAtivo()	== 1) ? "checked" : null;
	$indReservaOnline	= ($info->getIndReservaOnline()	== 1) ? "checked" : null;
	$codTipoMaterial= ($info->getCodTipoMaterial() != null) ? $info->getCodTipoMaterial()->getCodigo() : null;
	$codSubgrupo	= ($info->getCodSubgrupo() != null) ? $info->getCodSubgrupo()->getCodigo() : null;
	
	//$codUniMed		= ($info->getCodUnidadeMedida() != null) ? $info->getCodUnidadeMedida()->getCodigo() : null;
	//$referencia		= $info->getReferencia();
	//$descricaoCom	= $info->getDescricaoCompleta();
	//$codNcm			= $info->getCodNcm();
	//$margemLucro	= $info->getPctMargemLucro();
	//$codTipoPreço	= ($info->getCodTipoPrecoVenda() != null) ? $info->getCodTipoPrecoVenda()->getCodigo() : null;
	//$valorVenda		= $info->getValorVenda();
	//$desconto		= $info->getPctMaxDesconto();
	//$observacao		= $info->getObservacao();

}else{
	$nome			= '';
	$descricao		= '';
	$preReserva		= '';
	$diasIndis		= '';
	$codTipoMaterial= '';
	$codSubgrupo	= '';
	$ativo			= 'checked';
	$indReservaOnline   = 'checked';
	
	$codUniMed		= '';
	$referencia		= '';
	$descricaoCom	= '';
	$codNcm			= '';
	$margemLucro	= '';
	$codTipoPreço	= '';
	$valorVenda		= '';
	$desconto		= '';
	$observacao		= '';
	
}

#################################################################################
## Select das unidades de medida
#################################################################################
/*try {
	$aUnidades		= $em->getRepository('Entidades\ZgestUnidadeMedida')->findBy(array('codOrganizacao' => $system->getCodOrganizacao()));
	$oUnidades		= $system->geraHtmlCombo($aUnidades,	'CODIGO', 'DESCRICAO',	$codUniMed, 		null);
} catch (\Exception $e) {
	\Zage\App\Erro::halt($e->getMessage(),__FILE__,__LINE__);
}
*/
################################################################################
# Select de tipo de material
################################################################################
try {
	$aMaterial = $em->getRepository('Entidades\ZgestTipoProduto')->findBy(array(),array('descricao' => 'ASC'));
	$oMaterial = $system->geraHtmlCombo($aMaterial, 'CODIGO', 'DESCRICAO', $codTipoMaterial, '');
} catch (\Exception $e) {
	\Zage\App\Erro::halt($e->getMessage(),__FILE__,__LINE__);
}

################################################################################
# Select de Subgrupo
################################################################################
try {
	$aSubGrupo = $em->getRepository('Entidades\ZgestSubgrupo')->findBy(array(),array('descricao' => 'ASC'));
	$oSubGrupo = $system->geraHtmlCombo($aSubGrupo, 'CODIGO', 'DESCRICAO', $codSubgrupo, '');
} catch (\Exception $e) {
	\Zage\App\Erro::halt($e->getMessage(),__FILE__,__LINE__);
}

#################################################################################
## Resgatar os dados dos valores
#################################################################################
$aValores		= $em->getRepository('Entidades\ZgestProdutoValor')->findBy(array('codProduto' => $codProduto));
$tabVal			= "";
for ($i = 0; $i < sizeof($aValores); $i++) {

	$dataBase 	  = $aValores[$i]->getDataBase()->format($system->config["data"]["dateFormat"]);
	$dataCadastro = $aValores[$i]->getDataCadastro()->format($system->config["data"]["datetimeSimplesFormat"]);
	
	$tabVal		.= '<tr><td class="center" style="width: 20px;"><div class="inline" zg-type="zg-div-msg"></div></td>
					<td><input type="text" class="width-100" name="valor[]" value="'.$aValores[$i]->getValor().'" autocomplete="off" zg-data-toggle="mask" zg-data-mask="dinheiro" zg-data-mask-retira="1"></td>
					<td><input type="text" class="width-100" class="form-control datepicker" name="dataBase[]" value="'.$dataBase.'" maxlength="10" autocomplete="off" zg-data-toggle="mask" zg-data-mask="data"></td>
					<td><input type="text" class="width-100" name="desconPorcMax[]" value="'.$aValores[$i]->getDescontoPorcentoMax().'" autocomplete="off" zg-data-toggle="mask" zg-data-mask="porcentagem" zg-data-mask-retira="1"></td>
					<td><input type="text" class="width-100" class="form-control datepicker" name="dataCadastro[]" readonly value="'.$dataCadastro.'" maxlength="16" autocomplete="off" zg-data-toggle="mask" zg-data-mask="datetimesimples"></td>
					<td class="center"><span class="center" zgdelete onclick="delRowValorProdutoAlt($(this));"><i class="fa fa-trash bigger-150 red"></i></span><input type="hidden" name="codValor[]" value="'.$aValores[$i]->getCodigo().'"></td></tr>';
}

#################################################################################
## Url Voltar
#################################################################################
$urlVoltar			= ROOT_URL."/Est/produtoLis.php?id=".$id;

#################################################################################
## Url Novo
#################################################################################
$uid = \Zage\App\Util::encodeUrl('_codMenu_='.$_codMenu_.'&_icone_='.$_icone_.'&codProduto=');
$urlNovo			= ROOT_URL."/Est/produtoAlt.php?id=".$uid;

#################################################################################
## Carregando o template html
#################################################################################
$tpl	= new \Zage\App\Template();
$tpl->load(\Zage\App\Util::getCaminhoCorrespondente(__FILE__, \Zage\App\ZWS::EXT_HTML));

#################################################################################
## Define os valores das variáveis
#################################################################################
$tpl->set('URL_FORM'				,$_SERVER['SCRIPT_NAME']);
$tpl->set('URLVOLTAR'				,$urlVoltar);
$tpl->set('URLNOVO'					,$urlNovo);
$tpl->set('ID'						,$id);
$tpl->set('COD_PRODUTO'				,$codProduto);
$tpl->set('ATIVO'					,$ativo);
$tpl->set('IND_EXPOSICAO'			,$indReservaOnline);
$tpl->set('NOME'					,$nome);
$tpl->set('DESCRICAO'				,$descricao);
$tpl->set('PRE_RESERVA'				,$preReserva);
$tpl->set('DIAS_INDIS'				,$diasIndis);
$tpl->set('COD_TIPO_MATERIAL'		,$oMaterial);
$tpl->set('COD_SUBGRUPO'			,$oSubGrupo);
$tpl->set('TAB_VAL'					,$tabVal);


$tpl->set('UNIDADES'				,$oUnidades);
$tpl->set('DESCRICAO_COMPLETA'		,$descricaoCom);
$tpl->set('NCM'						,$codNcm);
$tpl->set('REFERENCIA'				,$referencia);
$tpl->set('APP_BS_TA_MINLENGTH'		,\Zage\Adm\Parametro::getValorSistema('APP_BS_TA_MINLENGTH'));
$tpl->set('APP_BS_TA_ITENS'			,\Zage\Adm\Parametro::getValorSistema('APP_BS_TA_ITENS'));
$tpl->set('APP_BS_TA_TIMEOUT'		,\Zage\Adm\Parametro::getValorSistema('APP_BS_TA_TIMEOUT'));
$tpl->set('DP'						,\Zage\App\Util::getCaminhoCorrespondente(__FILE__,\Zage\App\ZWS::EXT_DP,\Zage\App\ZWS::CAMINHO_RELATIVO));
$tpl->set('IC'						,$_icone_);
$tpl->set('COD_MENU'				,$_codMenu_);

#################################################################################
## Por fim exibir a página HTML
#################################################################################
$tpl->show();
