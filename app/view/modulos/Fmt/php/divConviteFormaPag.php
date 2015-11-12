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
## Verifica se o usuário está autenticado
#################################################################################
include_once(BIN_PATH . 'auth.php');
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
	\Zage\App\Erro::halt($tr->trans('Falta de Parâmetros'));
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
## Resgata as variáveis postadas
#################################################################################
if (isset($_GET['codVendaTipo'])) 		$codVendaTipo		= \Zage\App\Util::antiInjection($_GET['codVendaTipo']);
$oVendaTipo	= $em->getRepository('Entidades\ZgfmtConviteExtraVendaTipo')->findOneBy(array('codigo' => $codVendaTipo));
#################################################################################
## Verificar se já existe configuração de venda
#################################################################################
$oVendaConf	= $em->getRepository('Entidades\ZgfmtConviteExtraVendaConf')->findOneBy(array('codFormatura' => $system->getCodOrganizacao() , 'codVendaTipo' => $codVendaTipo));

if ($oVendaConf){
	$taxaAdm 			= ($oVendaConf->getTaxaAdministracao() != 0) ? \Zage\App\Util::formataDinheiro($oVendaConf->getTaxaAdministracao()) : null;
	$indAddTaxaBoleto	= ($oVendaConf->getIndAdicionarTaxaBoleto()	== 1) ? "checked" : null;
	$codConta			= ($oVendaConf->getCodContaBoleto()	!= null) ? $oVendaConf->getCodContaBoleto()->getCodigo() : null;
	
}else{
	$taxaAdm 			= null;
	$indAddTaxaBoleto	= "checked";
	$codConta 			= null;
}

#################################################################################
## Select da Forma de Pagamento
#################################################################################
try {
	
	
	
	$oFormaSel	= $em->getRepository('Entidades\ZgfmtConviteExtraVendaForma')->findBy(array('codOrganizacao' => $system->getCodOrganizacao() , 'codVendaTipo' => $codVendaTipo));
	$aFormaSel  = array();
	foreach($oFormaSel as $codTipo){
		$aFormaSel[] = $codTipo->getCodFormaPagamento()->getCodigo();
	}
	
	if ($codVendaTipo == 'I'){
		$aFormaPag	= $em->getRepository('Entidades\ZgfinFormaPagamento')->findBy(array('codigo' => 'BOL'),array('descricao' => 'ASC'));
	}else{
		$aFormaPag	= $em->getRepository('Entidades\ZgfinFormaPagamento')->findBy(array(),array('descricao' => 'ASC'));
	}
	
	$oFormaPag	= $system->geraHtmlCombo($aFormaPag,	'CODIGO', 'DESCRICAO',	$aFormaSel , null);
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
			
			if ($aCntCer[$i]->getCodigo() == $codConta){
				$selected = 'selected';
			}else{
				$selected = '';
			}
			
			$valBol		= ($aCntCer[$i]->getCodTipo()->getCodigo() == "CC") ? \Zage\Fmt\Financeiro::getValorBoleto($aCntCer[$i]->getCodigo()) : 0;
			$oConta	.= "<option value='".$aCntCer[$i]->getCodigo()."' ".$selected." zg-val-boleto='".$valBol."'>".$aCntCer[$i]->getNome()."</option>";
		}
		$oConta		.= '</optgroup>';
		if ($aConta) {
			$oConta		.= "<optgroup label='Contas da Formatura'>";
			for ($i = 0; $i < sizeof($aConta); $i++) {
				
				if ($aConta[$i]->getCodigo() == $codConta){
					$selected = 'selected';
				}else{
					$selected = '';
				}
				
				$valBol		= ($aConta[$i]->getCodTipo()->getCodigo() == "CC") ? \Zage\Fmt\Financeiro::getValorBoleto($aConta[$i]->getCodigo()) : 0;
				$oConta	.= "<option value='".$aConta[$i]->getCodigo()."' ".$selected." zg-val-boleto='".$valBol."'>".$aConta[$i]->getNome()."</option>";
			}
			$oConta		.= '</optgroup>';
		}
	}else{
		$oConta		= $system->geraHtmlCombo($aConta,	'CODIGO', 'NOME', $codConta ,'');
	}


} catch (\Exception $e) {
	\Zage\App\Erro::halt($e->getMessage(),__FILE__,__LINE__);
}

################################################################################
# Resgata as informações do banco
################################################################################
try {
	$info = $em->getRepository('Entidades\ZgfmtConviteExtraVendaConf')->findOneBy(array('codVendaTipo' => $codVendaTipo , 'codFormatura' => $system->getCodOrganizacao()));
} catch ( \Exception $e ) {
	\Zage\App\Erro::halt ( $e->getMessage () );
}


$html .= '<div class="col-sm-6">';
$html .= '<h5 class="header blue bolder smaller" align="center">Pagamento</h5>';
$html .= '<div class="form-group">';
$html .= '<label class="col-sm-3 control-label" for="codFormaPagID">Forma de Pag.</label>';
$html .= '<div class="input-group col-sm-5">';
$html .= '<span class="input-group-addon"><i class="ace-icon fa fa-question-circle" data-rel="popover" data-placement="top" data-trigger="hover" data-original-title="<i class=\'ace-icon fa fa-question-circle red\'></i> Ajuda" data-content="Selecione as formas de pagamento aceitas na venda do convite extra no tipo '.$oVendaTipo->getDescricao().'."></i></span>';
$html .= '<select class="multiselect" multiple="multiple" onchange="mostrarConf();" name="codFormaPag[]" id="codFormaPagID">';
$html .= $oFormaPag;
$html .= '</select>';
$html .= '</div>';
$html .= '</div>';

$html .= '<div class="form-group col-sm-12" id="divValorPorFormandoID">
		 <label for="identID" class="col-sm-3 control-label">Taxa comodidade</label>
		 <div class="input-group col-sm-5 pull-left">
		 <span class="input-group-addon"><i class="ace-icon fa fa-question-circle" data-rel="popover" data-placement="top" data-trigger="hover" data-original-title="<i class=\'ace-icon fa fa-question-circle red\'></i> Ajuda" data-content="A taxa de comodidade é o valor fixo que será adicionado a uma venda de convite extra. Caso não seja preenchido, este valor será considerado como 0."></i></span>
		 <input class="form-control" id="taxaAdministracaoID" type="text" name="taxaAdministracao" maxlength="60" value="'.$taxaAdm.'" placeholder="Taxa de administração" autocomplete="off" zg-data-toggle="mask" zg-data-mask="dinheiro">
		 </div>
		 <div class="col-sm-1 pull-left" id="divHelpValorPorFormandoID"></div>
		 </div>';

$html .= '</div>';
$html .= '</div>';

$html .= '<div class="col-sm-6">';
$html .= '<h5 class="header blue bolder smaller" align="center">Configuração do boleto</h5>';

$html .=	'<div id="divBoletoConfID">';
$html .=	'<div class="form-group col-sm-12" id="divContaRecID">
			<label class="col-sm-3 control-label">Conta Corrente</label>
			<div class="input-group col-sm-6 pull-left">
			<span tabindex="99012" class="input-group-addon"><i class="ace-icon fa fa-question-circle" data-rel="popover" data-placement="top" data-trigger="hover" data-original-title="<i class=\'ace-icon fa fa-question-circle red\'></i> Ajuda" data-content="Selecione a conta corrente que será utilizada para gerar os boletos da venda do convite extra."></i></span>
			<select class="select2" style="width:100%;" onchange="mostrarTaxaBoleto();" id="codContaRecID" name="codContaRec" data-rel="select2">
			'.$oConta.'
			</select>
			</div>
			<div class="help-block col-sm-1 inline" id="divHelpContaRecID"></div>
			</div>';

$html .=	'<div class="form-group col-sm-12" id="divValorPorFormandoID">
			<label for="identID" class="col-sm-3 control-label">Taxa boleto</label>
			<div class="input-group col-sm-5 pull-left">
			<span class="input-group-addon"><i class="ace-icon fa fa-question-circle" data-rel="popover" data-placement="top" data-trigger="hover" data-original-title="<i class=\'ace-icon fa fa-question-circle red\'></i> Ajuda" data-content="Custo do boleto para a conta selecionada."></i></span>
			<input class="form-control" id="taxaBoletoID" readonly type="text" name="taxaBoleto" maxlength="60" value="" placeholder="Custo do boleto" autocomplete="off" zg-data-toggle="mask" zg-data-mask="dinheiro">
			</div>
			<div class="col-sm-1 pull-left" id="divHelpValorPorFormandoID"></div>
			</div>';

$html .=	'<div class="form-group col-sm-12" >
			<label class="col-sm-3 control-label">
			<input name="indAddTaxaBoleto" id="indAddTaxaBoletoID" class="ace ace-switch " value="1" '.$indAddTaxaBoleto.' type="checkbox" onchange="montarResumo();"/>
			<span class="lbl" data-lbl="SIM&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;NÃO">&nbsp;&nbsp;</span>
			</label>
			<div class="input-group col-sm-8 pull-left">
			<span class="control-label pull-left">Adicionar o custo do boleto na taxa de comodidade <i class="ace-icon fa fa-question-circle blue" data-rel="popover" data-placement="top" data-trigger="hover" data-original-title="<i class=\'ace-icon fa fa-question-circle red\'></i> Ajuda" data-content="Marque sim caso queira adicionar o custo do boleto na taxa de comodidade."></i></span>
			</div>
			</div>';

$html .= '</div>';
$html .= '</div>';


$html	.= '<script type="text/javascript" charset="%CHARSET%">';

$html	.= 'mostrarTaxaBoleto();';

$html	.= "$('#codFormaPagID').multiselect({
			enableFiltering: false,
			includeSelectAllOption: true,
			disableIfEmpty: true,
			buttonClass: 'btn btn-white btn-primary btn-sm',
			nonSelectedText: 'Selecione as formas de pagamento',
			allSelectedText: 'Todas as formas selecionadas',
			nSelectedText: 'selecionadas',
			selectAllText: 'Marcar todas',
			numberDisplayed: 3,
		});";

$html	.= "$('[data-rel=popover]').popover({html:true});";
$html	.= "$('[data-rel=select2]').css('width','100%').select2({allowClear:true});";
$html	.= "$('[zg-data-toggle="."mask"."]').each(function( index ) {";
$html	.= "zgMask($( this ), $( this ).attr('zg-data-mask'));";
$html	.= "});";


$html	.= '</script>';




echo $html;