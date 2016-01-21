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
global $system,$em,$tr,$_user;

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
## Buscar informações do aceite e formatura
#################################################################################
$orcAceite = \Zage\Fmt\Orcamento::getVersaoAceita($system->getCodOrganizacao());

if (!$orcAceite){
	$aceite			= '<i class="ace-icon fa fa-times bigger-110 red"></i> Sem orçamento:';
	$valorTotal	 	= 'Ainda não temos esta informação';
	$numFormandos	= 'Ainda não temos esta informação';
	$valorFormando	= 'Ainda não temos esta informação';
	$valorFormandoCal = 0;
}else{
	$aceite			= '<i class="ace-icon fa fa-check bigger-110 green"></i> Orçamento aceito:';
	$oOrgFmt = $em->getRepository('Entidades\ZgfmtOrganizacaoFormatura')->findOneBy(array('codOrganizacao' => $system->getCodOrganizacao()));
	
	if($oOrgFmt){
		$valorTotal		 	= ($oOrgFmt->getValorPrevistoTotal()) ? \Zage\App\Util::to_money($oOrgFmt->getValorPrevistoTotal()) : 'Ainda não temos esta informação';
		$numFormandos	 	= ($oOrgFmt->getQtdePrevistaFormandos()) ? $oOrgFmt->getQtdePrevistaFormandos() : 'Ainda não temos esta informação';
		$valorFormando	 	= ($oOrgFmt->getValorPrevistoTotal() && $oOrgFmt->getQtdePrevistaFormandos()) ? \Zage\App\Util::to_money(round($oOrgFmt->getValorPrevistoTotal()/$oOrgFmt->getQtdePrevistaFormandos(),2)) : 'Ainda não temos esta informação';
		$valorFormandoCal	= ($oOrgFmt->getValorPrevistoTotal() && $oOrgFmt->getQtdePrevistaFormandos()) ? (round($oOrgFmt->getValorPrevistoTotal()/$oOrgFmt->getQtdePrevistaFormandos(),2)) : 0;
	}	
}

#################################################################################
## Select dos tipos de calculo do preço
#################################################################################
try {
	$aPrecoTipo		= $em->getRepository('Entidades\ZgfmtEventoPrecoTipo')->findAll();
} catch (\Exception $e) {
	\Zage\App\Erro::halt($e->getMessage(),__FILE__,__LINE__);
}

#################################################################################
## Buscar o usuário para conseguir o email
#################################################################################
$oEvento	= $em->getRepository('Entidades\ZgfmtEvento')->findAll(); 

for ($i = 0; $i < sizeof($oEvento); $i++) {
	
	$codTipoPreco = ($oEvento[$i]->getCodTipoPreco() ? $oEvento[$i]->getCodTipoPreco()->getCodigo() : null);
	
	if ($codTipoPreco == 'V'){ //preço por valor
		$preco 	= \Zage\App\Util::formataDinheiro($oEvento[$i]->getValorAvulso());
		$pct	= null;
		$total	= \Zage\App\Util::to_money($preco);
		$hiddenValor = '';
		$hiddenPct = 'hidden';
		
	}elseif ($codTipoPreco == 'P'){
		$preco 	= null;
		$pct	= $oEvento[$i]->getPctValorOrcamento();
		$total 	= \Zage\App\Util::to_money(round($valorFormandoCal * $pct / 100,2));
		$hiddenValor = 'hidden';
		$hiddenPct = '';
		
	}else{
		$preco 	= null;
		$pct	= null;
		$total	= 'Não configurado';
		$codTipoPreco = 'P';
		$hiddenValor = 'hidden';
		$hiddenPct = '';
		
	}
	
	//Gerar combo
	$oTipoPreco		= $system->geraHtmlCombo($aPrecoTipo,	'CODIGO', 'DESCRICAO',	$codTipoPreco, null);
	
	$tabCompra	.= '<tr>
		<td style="text-align: left;">'.$oEvento[$i]->getCodTipoEvento()->getDescricao().'</td>
		<td><select id="tipoPreco_'.$i.'_ID" class="select2" style="width:100%;" name="codTipoPreco[]" onchange="mudarForma('.$i.');" data-rel="select2">'.$oTipoPreco.'</select></td>
		<td class="center" style="width: 20px;"><input hidden type="text" name="codEvento[]" style="width:100%;" value="'.$oEvento[$i]->getCodigo().'"><input class="'.$hiddenValor.'" type="text" id="valor_'.$i.'_ID" name="valor[]" placeholder="Valor fixo" style="width:100%;" value="'.$preco.'" maxlength="10" autocomplete="off" zg-data-toggle="mask" zg-data-mask="dinheiro" zg-data-mask-retira="1"><input class="'.$hiddenPct.'" type="text" id="pct_'.$i.'_ID" name="pct[]" placeholder="Porcentagem" style="width:100%;" value="'.$pct.'" maxlength="10" autocomplete="off" zg-data-toggle="mask" zg-data-mask="porcentagem" zg-data-mask-retira="1"></td>
		<td style="text-align: center;">'.$total.'</td>
		</tr>';
}

#################################################################################
## Gerar a url de histórico de pagamentos
#################################################################################
$urlVoltar				= ROOT_URL."/Fmt/conviteExtraCompra.php?id=".$id;

#################################################################################
## Carregando o template html
#################################################################################
$tpl	= new \Zage\App\Template();
$tpl->load(\Zage\App\Util::getCaminhoCorrespondente(__FILE__, \Zage\App\ZWS::EXT_HTML));

#################################################################################
## Define os valores das variáveis
#################################################################################
$tpl->set('IC'				,$_icone_);
$tpl->set('ID'				,$id);
$tpl->set('URL_VOLTAR'		,$urlVoltar);
$tpl->set('HIDDEN'			,$hidden);
$tpl->set('TAB_COMPRA'		,$tabCompra);

$tpl->set('ACEITE'			,$aceite);
$tpl->set('VALOR_ORCAMENTO'	,$valorTotal);
$tpl->set('NUM_FORMANDOS'	,$numFormandos);
$tpl->set('VALOR_FORMANDO'	,$valorFormando);

$tpl->set('DP'						,\Zage\App\Util::getCaminhoCorrespondente(__FILE__,\Zage\App\ZWS::EXT_DP,\Zage\App\ZWS::CAMINHO_RELATIVO));
$tpl->set('IC'						,$_icone_);
$tpl->set('COD_MENU'				,$_codMenu_);
#################################################################################
## Por fim exibir a página HTML
#################################################################################
$tpl->show();
