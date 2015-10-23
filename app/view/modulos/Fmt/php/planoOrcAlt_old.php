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
		$info = $em->getRepository('Entidades\ZgfmtPlanoOrcItem')->findBy(array('codVersao' => $codVersao));

	} catch (\Exception $e) {
		\Zage\App\Erro::halt($e->getMessage());
	}

	$nome			= $info->getNome();
	$codVersao		= ($info->getCodVersao() != null) ? $info->getCodVersao()->getCodigo() : null;
	$ativo			= ($info->getIndAtivo()	== 1) ? "checked" : null;
}else{
	$nome			= '';
	$descricao		= '';
	$codSubgrupo	= '';
	$ativo			= 'checked';
}

################################################################################
# Select de tipo de material
################################################################################
try {
	$aVersao = $em->getRepository('Entidades\ZgfmtPlanoOrcamentario')->findBy(array('indAtivo' => 1),array('versao' => 'ASC'));
	$oVersao = $system->geraHtmlCombo($aVersao, 'CODIGO', 'VERSAO', $codVersao, '');
} catch (\Exception $e) {
	\Zage\App\Erro::halt($e->getMessage(),__FILE__,__LINE__);
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
try {
	$aCategoria		= $em->getRepository('Entidades\ZgfinCategoria')->findAll();
	$oCategoria		= $system->geraHtmlCombo($aCategoria,	'CODIGO', 'DESCRICAO',	null, 	null);
} catch (\Exception $e) {
	\Zage\App\Erro::halt($e->getMessage(),__FILE__,__LINE__);
}

#################################################################################
## Resgatar os dados dos valores
#################################################################################
$aOrcItem		= $em->getRepository('Entidades\ZgfmtPlanoOrcItem')->findBy(array('codVersao' => $codVersao));
$tabMissa		= "";
for ($i = 0; $i < sizeof($aOrcItem); $i++) {

	if( $aOrcItem[$i]->getCodTipoEvento()->getCodigo() == 1 ){ ##Missa
		$indAtivo		= ($aOrcItem[$i]->getIndAtivo()	== 1) ? "checked" : null;
		$codTipoItem	= ($aOrcItem[$i]->getCodTipoItem()) ? $aOrcItem[$i]->getCodTipoItem()->getCodigo() : null;
		$oTipoItem		= $system->geraHtmlCombo($aTipoItem,	'CODIGO', 'DESCRICAO',	$codTipoItem, '');
		$codCategoria	= ($aOrcItem[$i]->getCodCategoria()) ? $aOrcItem[$i]->getCodCategoria()->getCodigo() : null;
		$oCategoria		= $system->geraHtmlCombo($aCategoria,	'CODIGO', 'DESCRICAO',	$codCategoria, '');
		
		$tabMissa	.= '<tr><td class="center" style="width: 20px;"><div class="inline" zg-type="zg-div-msg"></div></td>
					<td><input type="text" class="width-100" name="itemMissa[]" value="'.$aOrcItem[$i]->getItem().'" autocomplete="off"></td>
					<td><select class="select2" style="width:100%;" name="codTipoItemMissa[]" data-rel="select2">'.$oTipoItem.'</select></td>
					<td><select class="select2" style="width:100%;" name="codCategoriaMissa[]" data-rel="select2">'.$oCategoria.'</select></td>
					<td align="center"><label><input name="indAtivoMissa" id="indAtivoMissaID" '.$indAtivo.' class="ace ace-switch ace-switch-6" type="checkbox" /><span class="lbl"></span></label></rd>
					<td class="center"><span class="center" zgdelete onclick="delRowMissaOrcAlt($(this));"><i class="fa fa-trash bigger-150 red"></i></span><input type="hidden" name="codOrcMissa[]" value="'.$aOrcItem[$i]->getCodigo().'"></td></tr>';
	}else if( $aOrcItem[$i]->getCodTipoEvento()->getCodigo() == 2 ){##CULTO EVANGÉLICO
		$indAtivo		= ($aOrcItem[$i]->getIndAtivo()	== 1) ? "checked" : null;
		$codTipoItem	= ($aOrcItem[$i]->getCodTipoItem()) ? $aOrcItem[$i]->getCodTipoItem()->getCodigo() : null;
		$oTipoItem		= $system->geraHtmlCombo($aTipoItem,	'CODIGO', 'DESCRICAO',	$codTipoItem, '');
		$codCategoria	= ($aOrcItem[$i]->getCodCategoria()) ? $aOrcItem[$i]->getCodCategoria()->getCodigo() : null;
		$oCategoria		= $system->geraHtmlCombo($aCategoria,	'CODIGO', 'DESCRICAO',	$codCategoria, '');
		
		$tabCulEvan	.= '<tr><td class="center" style="width: 20px;"><div class="inline" zg-type="zg-div-msg"></div></td>
					<td><input type="text" class="width-100" name="itemCulEvan[]" value="'.$aOrcItem[$i]->getItem().'" autocomplete="off"></td>
					<td><select class="select2" style="width:100%;" name="codTipoItemCulEvan[]" data-rel="select2">'.$oTipoItem.'</select></td>
					<td><select class="select2" style="width:100%;" name="codCategoriaCulEvan[]" data-rel="select2">'.$oCategoria.'</select></td>
					<td align="center"><label><input name="indAtivoCulEvan" id="indAtivoCulEvanID" '.$indAtivo.' class="ace ace-switch ace-switch-6" type="checkbox" /><span class="lbl"></span></label></rd>
					<td class="center"><span class="center" zgdelete onclick="delRowCulEvanOrcAlt($(this));"><i class="fa fa-trash bigger-150 red"></i></span><input type="hidden" name="codOrcCulEvan[]" value="'.$aOrcItem[$i]->getCodigo().'"></td></tr>';
	}else if( $aOrcItem[$i]->getCodTipoEvento()->getCodigo() == 3 ){##CULTO ESPÍRITA
		$indAtivo		= ($aOrcItem[$i]->getIndAtivo()	== 1) ? "checked" : null;
		$codTipoItem	= ($aOrcItem[$i]->getCodTipoItem()) ? $aOrcItem[$i]->getCodTipoItem()->getCodigo() : null;
		$oTipoItem		= $system->geraHtmlCombo($aTipoItem,	'CODIGO', 'DESCRICAO',	$codTipoItem, '');
		$codCategoria	= ($aOrcItem[$i]->getCodCategoria()) ? $aOrcItem[$i]->getCodCategoria()->getCodigo() : null;
		$oCategoria		= $system->geraHtmlCombo($aCategoria,	'CODIGO', 'DESCRICAO',	$codCategoria, '');
		
		$tabCulEsp	.= '<tr><td class="center" style="width: 20px;"><div class="inline" zg-type="zg-div-msg"></div></td>
					<td><input type="text" class="width-100" name="itemCulEsp[]" value="'.$aOrcItem[$i]->getItem().'" autocomplete="off"></td>
					<td><select class="select2" style="width:100%;" name="codTipoItemCulEsp[]" data-rel="select2">'.$oTipoItem.'</select></td>
					<td><select class="select2" style="width:100%;" name="codCategoriaCulEsp[]" data-rel="select2">'.$oCategoria.'</select></td>
					<td align="center"><label><input name="indAtivoCulEsp" id="indAtivoCulEspID" '.$indAtivo.' class="ace ace-switch ace-switch-6" type="checkbox" /><span class="lbl"></span></label></rd>
					<td class="center"><span class="center" zgdelete onclick="delRowCulEspOrcAlt($(this));"><i class="fa fa-trash bigger-150 red"></i></span><input type="hidden" name="codOrcCulEsp[]" value="'.$aOrcItem[$i]->getCodigo().'"></td></tr>';
	}else if( $aOrcItem[$i]->getCodTipoEvento()->getCodigo() == 4 ){##APOSIÇÃO DA PLACA
		$indAtivo		= ($aOrcItem[$i]->getIndAtivo()	== 1) ? "checked" : null;
		$codTipoItem	= ($aOrcItem[$i]->getCodTipoItem()) ? $aOrcItem[$i]->getCodTipoItem()->getCodigo() : null;
		$oTipoItem		= $system->geraHtmlCombo($aTipoItem,	'CODIGO', 'DESCRICAO',	$codTipoItem, '');
		$codCategoria	= ($aOrcItem[$i]->getCodCategoria()) ? $aOrcItem[$i]->getCodCategoria()->getCodigo() : null;
		$oCategoria		= $system->geraHtmlCombo($aCategoria,	'CODIGO', 'DESCRICAO',	$codCategoria, '');
		
		$tabApocPla	.= '<tr><td class="center" style="width: 20px;"><div class="inline" zg-type="zg-div-msg"></div></td>
					<td><input type="text" class="width-100" name="itemApocPla[]" value="'.$aOrcItem[$i]->getItem().'" autocomplete="off"></td>
					<td><select class="select2" style="width:100%;" name="codTipoItemApocPla[]" data-rel="select2">'.$oTipoItem.'</select></td>
					<td><select class="select2" style="width:100%;" name="codCategoriaApocPla[]" data-rel="select2">'.$oCategoria.'</select></td>
					<td align="center"><label><input name="indAtivoApocPla" id="indAtivoApocPlaID" '.$indAtivo.' class="ace ace-switch ace-switch-6" type="checkbox" /><span class="lbl"></span></label></rd>
					<td class="center"><span class="center" zgdelete onclick="delRowApocPlaOrcAlt($(this));"><i class="fa fa-trash bigger-150 red"></i></span><input type="hidden" name="codOrcApocPla[]" value="'.$aOrcItem[$i]->getCodigo().'"></td></tr>';
	}else if( $aOrcItem[$i]->getCodTipoEvento()->getCodigo() == 5 ){##AULA DA SAUDADE
		$indAtivo		= ($aOrcItem[$i]->getIndAtivo()	== 1) ? "checked" : null;
		$codTipoItem	= ($aOrcItem[$i]->getCodTipoItem()) ? $aOrcItem[$i]->getCodTipoItem()->getCodigo() : null;
		$oTipoItem		= $system->geraHtmlCombo($aTipoItem,	'CODIGO', 'DESCRICAO',	$codTipoItem, '');
		$codCategoria	= ($aOrcItem[$i]->getCodCategoria()) ? $aOrcItem[$i]->getCodCategoria()->getCodigo() : null;
		$oCategoria		= $system->geraHtmlCombo($aCategoria,	'CODIGO', 'DESCRICAO',	$codCategoria, '');
		
		$tabAulaSaud	.= '<tr><td class="center" style="width: 20px;"><div class="inline" zg-type="zg-div-msg"></div></td>
					<td><input type="text" class="width-100" name="itemAulaSaud[]" value="'.$aOrcItem[$i]->getItem().'" autocomplete="off"></td>
					<td><select class="select2" style="width:100%;" name="codTipoItemAulaSaud[]" data-rel="select2">'.$oTipoItem.'</select></td>
					<td><select class="select2" style="width:100%;" name="codCategoriaAulaSaud[]" data-rel="select2">'.$oCategoria.'</select></td>
					<td align="center"><label><input name="indAtivoAulaSaud" id="indAtivoAulaSaudID" '.$indAtivo.' class="ace ace-switch ace-switch-6" type="checkbox" /><span class="lbl"></span></label></rd>
					<td class="center"><span class="center" zgdelete onclick="delRowAulaSaudOrcAlt($(this));"><i class="fa fa-trash bigger-150 red"></i></span><input type="hidden" name="codOrcAulaSaud[]" value="'.$aOrcItem[$i]->getCodigo().'"></td></tr>';
	}else if( $aOrcItem[$i]->getCodTipoEvento()->getCodigo() == 6 ){##COLAÇÃO DE GRAU
		$indAtivo		= ($aOrcItem[$i]->getIndAtivo()	== 1) ? "checked" : null;
		$codTipoItem	= ($aOrcItem[$i]->getCodTipoItem()) ? $aOrcItem[$i]->getCodTipoItem()->getCodigo() : null;
		$oTipoItem		= $system->geraHtmlCombo($aTipoItem,	'CODIGO', 'DESCRICAO',	$codTipoItem, '');
		$codCategoria	= ($aOrcItem[$i]->getCodCategoria()) ? $aOrcItem[$i]->getCodCategoria()->getCodigo() : null;
		$oCategoria		= $system->geraHtmlCombo($aCategoria,	'CODIGO', 'DESCRICAO',	$codCategoria, '');
		
		$tabColGrau	.= '<tr><td class="center" style="width: 20px;"><div class="inline" zg-type="zg-div-msg"></div></td>
					<td><input type="text" class="width-100" name="itemColGrau[]" value="'.$aOrcItem[$i]->getItem().'" autocomplete="off"></td>
					<td><select class="select2" style="width:100%;" name="codTipoItemColGrau[]" data-rel="select2">'.$oTipoItem.'</select></td>
					<td><select class="select2" style="width:100%;" name="codCategoriaColGrau[]" data-rel="select2">'.$oCategoria.'</select></td>
					<td align="center"><label><input name="indAtivoColGrau" id="indAtivoColGrauID" '.$indAtivo.' class="ace ace-switch ace-switch-6" type="checkbox" /><span class="lbl"></span></label></rd>
					<td class="center"><span class="center" zgdelete onclick="delRowColGrauOrcAlt($(this));"><i class="fa fa-trash bigger-150 red"></i></span><input type="hidden" name="codOrcColGrau[]" value="'.$aOrcItem[$i]->getCodigo().'"></td></tr>';
	}else if( $aOrcItem[$i]->getCodTipoEvento()->getCodigo() == 7 ){##BAILE
		$indAtivo		= ($aOrcItem[$i]->getIndAtivo()	== 1) ? "checked" : null;
		$codTipoItem	= ($aOrcItem[$i]->getCodTipoItem()) ? $aOrcItem[$i]->getCodTipoItem()->getCodigo() : null;
		$oTipoItem		= $system->geraHtmlCombo($aTipoItem,	'CODIGO', 'DESCRICAO',	$codTipoItem, '');
		$codCategoria	= ($aOrcItem[$i]->getCodCategoria()) ? $aOrcItem[$i]->getCodCategoria()->getCodigo() : null;
		$oCategoria		= $system->geraHtmlCombo($aCategoria,	'CODIGO', 'DESCRICAO',	$codCategoria, '');
		
		$tabBaile	.= '<tr><td class="center" style="width: 20px;"><div class="inline" zg-type="zg-div-msg"></div></td>
					<td><input type="text" class="width-100" name="itemBaile[]" value="'.$aOrcItem[$i]->getItem().'" autocomplete="off"></td>
					<td><select class="select2" style="width:100%;" name="codTipoItemBaile[]" data-rel="select2">'.$oTipoItem.'</select></td>
					<td><select class="select2" style="width:100%;" name="codCategoriaBaile[]" data-rel="select2">'.$oCategoria.'</select></td>
					<td align="center"><label><input name="indAtivoBaile" id="indAtivoBaileID" '.$indAtivo.' class="ace ace-switch ace-switch-6" type="checkbox" /><span class="lbl"></span></label></rd>
					<td class="center"><span class="center" zgdelete onclick="delRowBaileOrcAlt($(this));"><i class="fa fa-trash bigger-150 red"></i></span><input type="hidden" name="codOrcBaile[]" value="'.$aOrcItem[$i]->getCodigo().'"></td></tr>';
	}
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
$tpl->set('COD_ITEM'				,$oTipoItem);
$tpl->set('COD_CATEGORIA'			,$oCategoria);
$tpl->set('TAB_MISSA'				,$tabMissa);
$tpl->set('TAB_CUL_EVAN'			,$tabCulEvan);
$tpl->set('TAB_CUL_ESP'				,$tabCulEsp);
$tpl->set('TAB_APOC_PLA'			,$tabApocPla);
$tpl->set('TAB_AULA_SAUD'			,$tabAulaSaud);
$tpl->set('TAB_COL_GRAU'			,$tabColGrau);
$tpl->set('TAB_BAILE'				,$tabBaile);

$tpl->set('DP'						,\Zage\App\Util::getCaminhoCorrespondente(__FILE__,\Zage\App\ZWS::EXT_DP,\Zage\App\ZWS::CAMINHO_RELATIVO));
$tpl->set('IC'						,$_icone_);
$tpl->set('COD_MENU'				,$_codMenu_);

#################################################################################
## Por fim exibir a página HTML
#################################################################################
$tpl->show();
