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
global $em,$log,$system;


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
## Resgata as variáveis postadas
#################################################################################
if (isset($_GET['codVersao'])) 		$codVersao			= \Zage\App\Util::antiInjection($_GET['codVersao']);

################################################################################
# Resgata as informações do banco
################################################################################
if ($codVersao) {
	try {
		$info = $em->getRepository ( 'Entidades\ZgfmtPlanoOrcamentario' )->findOneBy (array ('codigo' => $codVersao));
	} catch ( \Exception $e ) {
		\Zage\App\Erro::halt ( $e->getMessage () );
	}

	$versao		 = ($info->getVersao()) ? $info->getVersao() : null;
	$indVersao	 = ($info->getIndAtivo() == 1) ? "checked" : null;
} else {
	$versao 	  = null;
	$indVersao	 = "checked";
}

#################################################################################
## Resgatas os Eventos
#################################################################################
$eventos		= $em->getRepository('Entidades\ZgfmtPlanoOrcGrupoItem')->findBy(array(),array('codigo' => "ASC"));

$htmlBotoes			= "";
if (sizeof($eventos) > 0) {
	if (!isset($codEvento)) $codEvento	 = $eventos[0]->getCodigo();

	for ($i = 0; $i < sizeof($eventos); $i++) {
		if ($eventos[$i]->getCodigo() == $codEvento) {
			$class		= "btn-info";
		}else{
			$class		= "btn-white";
		}
		$bid			= \Zage\App\Util::encodeUrl('_codMenu_='.$_codMenu_.'&_icone_='.$_icone_.'&codEvento='.$eventos[$i]->getCodigo());
		$urlBotao		= ROOT_URL."/Fmt/". basename(__FILE__)."?id=".$bid;
		$htmlBotoes 	.= '<button type="button" onclick="javascript:zgLoadUrlSeSalvouOrc(\''.$urlBotao.'\');" class="btn '.$class.' btn-sm btn-bold">'.$eventos[$i]->getDescricao().'</button>';
	}
}else{
	if (!isset($codEvento)) $codEvento	 = null;
}

#################################################################################
## Select de Tipo Item
#################################################################################
try {
	$aTipoItem		= $em->getRepository('Entidades\ZgfmtPlanoOrcItemTipo')->findAll();
	$oTipoItem		= $system->geraHtmlCombo($aTipoItem,	'CODIGO', 'DESCRICAO',	null, 	null);
} catch (\Exception $e) {
	\Zage\App\Erro::halt($e->getMessage(),__FILE__,__LINE__);
}

#################################################################################
## Select de Categoria
#################################################################################
$dadosCat		= array();
try {
	$aCategoria		= $em->getRepository('Entidades\ZgfinCategoria')->findBy(array('codTipo' => 'D' , 'indAtiva' => 1 , 'codOrganizacao' => null ,  'codTipoOrganizacao' => 'FMT'), array('descricao' => 'ASC'));
	//$oCategoria		= $system->geraHtmlCombo($aCategoria,	'CODIGO', 'DESCRICAO',	null, 	null);
	
	//Formatar dados
	$dadosCat = array();
	for ($i = 0; $i < sizeof($aCategoria); $i++) {
		if ($aCategoria[$i]->getCodCategoriaPai() != null) {
			$descPai	= $aCategoria[$i]->getCodCategoriaPai()->getDescricao();
			$dadosCat["SUB"][$descPai][$aCategoria[$i]->getCodigo()]	= $aCategoria[$i]->getDescricao(); 
		}else{	
			$dadosCat["CAT"][$aCategoria[$i]->getCodigo()]				= $aCategoria[$i]->getDescricao();
		}
	}
	
	$oCategoria = '';
	foreach ($dadosCat["SUB"] as $desc => $aCatItens ){
		$oCategoria		.= '<optgroup label="'.$desc.'">';
		
		foreach ($aCatItens as $codCat => $descCat) {
			$oCategoria		.= '<option value="'.$codCat.'">'.$descCat.'</option>';
		}
		
		$oCategoria		.= '</optgroup>';
	}
		
} catch (\Exception $e) {
	\Zage\App\Erro::halt($e->getMessage(),__FILE__,__LINE__);
}

$log->debug(serialize($dadosCat));

#################################################################################
## Resgatar os dados dos valores
#################################################################################
$aOrcItem		= $em->getRepository('Entidades\ZgfmtPlanoOrcItem')->findBy(array('codPlano' => $codVersao, 'codGrupoItem' => $codEvento),array('ordem' => "ASC"));
$tabOrcamento	= "";

for ($i = 0; $i < sizeof($aOrcItem); $i++) {
	
	$indAtivo		= ($aOrcItem[$i]->getIndAtivo()	== 1) ? "checked" : null;
	$codTipoItem	= ($aOrcItem[$i]->getCodTipoItem()) ? $aOrcItem[$i]->getCodTipoItem()->getCodigo() : null;
	$oTipoItem		= $system->geraHtmlCombo($aTipoItem,	'CODIGO', 'DESCRICAO',	$codTipoItem, '');
	$codCategoria	= ($aOrcItem[$i]->getCodCategoria()) ? $aOrcItem[$i]->getCodCategoria()->getCodigo() : null;
	//$oCategoria		= $system->geraHtmlCombo($aCategoria,	'CODIGO', 'DESCRICAO',	$codCategoria, '');
	// Gera combo
	foreach ($dadosCat["SUB"] as $desc => $aCatItens ){
		$oCatExiste		.= '<optgroup label="'.$desc.'">';
		foreach ($aCatItens as $codCat => $descCat) {
			if ($codCat == $codCategoria){
				$selected = 'selected';
			}else{
				$selected = '';
			}
			$oCatExiste		.= '<option value="'.$codCat.'" '.$selected.'>'.$descCat.'</option>';
		}
		$oCatExiste		.= '</optgroup>';
	}
	
	if ($aOrcItem[$i]->getTextoDescritivo()) {
		$hidDesc		= "";
	}else{
		$hidDesc		= "hidden";
	}
	
	$tabOrcamento	.= '<tr class="_registroOrc"><td class="center" style="width: 20px;"><div class="inline" zg-type="zg-div-msg"></div></td>
				<td><input type="text" class="width-100" name="item[]" zg-name="item" value="'.$aOrcItem[$i]->getItem().'" autocomplete="off" onchange="verificaAlteracaoOrcAlt($(this));"></td>
				<td><select class="select2" style="width:100%;" name="codTipoItem[]" data-rel="select2" onchange="verificaAlteracaoOrcAlt($(this));">'.$oTipoItem.'</select></td>
				<td><select class="select2" style="width:100%;" name="codCategoria[]" data-rel="select2" onchange="verificaAlteracaoOrcAlt($(this));">'.$oCatExiste.'</select></td>
				<td align="center"><label><input name="indAtivo[]" id="indAtivoID" '.$indAtivo.' class="ace ace-switch ace-switch-6" type="checkbox" onchange="verificaAlteracaoOrcAlt($(this));" /><span class="lbl"></span></label></td>
				<td class="center">
						<div data-toggle="buttons" class="btn-group btn-overlap btn-corner">
							<span class="btn btn-sm btn-white btn-info center" onclick="moveUpOrcamentoOrcAlt($(this));"><i class="fa fa-arrow-circle-up bigger-150"></i></span>
							<span class="btn btn-sm btn-white btn-info center" onclick="moveDownOrcamentoOrcAlt($(this));"><i class="fa fa-arrow-circle-down bigger-150"></i></span>
							<span class="btn btn-sm btn-white btn-info center zgdelete" onclick="delRowOrcamentoOrcAlt($(this));"><i class="fa fa-trash bigger-150 red"></i></span>
							<span class="btn btn-sm btn-white btn-info center" onclick="habilitaTextoDescritivoOrcAlt($(this));"><i class="fa fa-commenting-o bigger-150"></i></span>
						</div>
						<input type="hidden" name="codOrcamento[]" value="'.$aOrcItem[$i]->getCodigo().'">
				</td></tr>
				<tr class="'.$hidDesc.'">
					<td colspan="6"><textarea maxlength="800" rows="3" class="col-sm-6 pull-right" name="aObs[]" onchange="alteraTextoDescritivoOrcAlt();">'.$aOrcItem[$i]->getTextoDescritivo().'</textarea></td>
				</tr>
				';
}

#################################################################################
## Url Voltar
#################################################################################
$urlVoltar			= ROOT_URL."/Fmt/planoOrcLis.php?id=".$id;

#################################################################################
## Url Novo
#################################################################################
$uid = \Zage\App\Util::encodeUrl('_codMenu_='.$_codMenu_.'&_icone_='.$_icone_.'&codVersao=');
$urlNovo			= ROOT_URL."/Fmt/planoOrcAlt.php?id=".$uid;

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
$tpl->set('COD_VERSAO'				,$codVersao);
$tpl->set('COD_EVENTO'				,$codEvento);
$tpl->set('COD_ITEM'				,$oTipoItem);
$tpl->set('COD_CATEGORIA'			,$oCategoria);
$tpl->set('TAB_ORCAMENTO'			,$tabOrcamento);
$tpl->set('BOTOES'			  		,$htmlBotoes);
$tpl->set('URL_BOTOES'			  	,$urlBotao);

$tpl->set('VERSAO'					,$versao);
$tpl->set('IND_VERSAO'				,$indVersao);

$tpl->set('COD_CONTA'				,$oC);

$tpl->set('DP'						,\Zage\App\Util::getCaminhoCorrespondente(__FILE__,\Zage\App\ZWS::EXT_DP,\Zage\App\ZWS::CAMINHO_RELATIVO));
$tpl->set('IC'						,$_icone_);
$tpl->set('COD_MENU'				,$_codMenu_);

#################################################################################
## Por fim exibir a página HTML
#################################################################################
$tpl->show();
