<?php
################################################################################
# Includes
################################################################################
if (defined ( 'DOC_ROOT' )) {
	include_once (DOC_ROOT . 'include.php');
} else {
	include_once ('../include.php');
}

################################################################################
# Resgata a variável ID que está criptografada
################################################################################
if (isset ( $_GET ['id'] )) {
	$id = \Zage\App\Util::antiInjection ( $_GET ["id"] );
} elseif (isset ( $_POST ['id'] )) {
	$id = \Zage\App\Util::antiInjection ( $_POST ["id"] );
} elseif (isset ( $id )) {
	$id = \Zage\App\Util::antiInjection ( $id );
} else {
	\Zage\App\Erro::halt ( 'Falta de Parâmetros' );
}

################################################################################
# Descompacta o ID
################################################################################
\Zage\App\Util::descompactaId ( $id );

################################################################################
# Verifica se o usuário tem permissão no menu
################################################################################
$system->checaPermissao ( $_codMenu_ );

################################################################################
# Resgata as informações do banco
################################################################################
if ($codCargo) {
	try {
		$info = $em->getRepository ( 'Entidades\ZgrhuFuncionarioCargo' )->findOneBy (array ('codigo' => $codCargo));
	} catch ( \Exception $e ) {
		\Zage\App\Erro::halt ( $e->getMessage () );
	}
	
	$cargo			 = ($info->getDescricao()) ? $info->getDescricao() : null;
	$codCbo			 = ($info->getCodCbo()) ? $info->getCodCbo()->getCodigo() : null;
	$intAtivo		 = ($info->getIndAtivo() == 1) ? "checked" : null;
} else {
	
	$cargo  	 	 = null;
	$codCbo			 = null;
	$intAtivo		 = "checked";
}

#################################################################################
## Resgatar os dados das funções
#################################################################################
$aFuncoes		= $em->getRepository('Entidades\ZgrhuFuncionarioFuncao')->findBy(array('codCargo' => $codCargo));
$tabFuncao			= "";
for ($i = 0; $i < sizeof($aFuncoes); $i++) {
	
	$salarioMin			= \Zage\App\Util::toPHPNumber($aFuncoes[$i]->getSalarioInicial());
	$salarioMax			= \Zage\App\Util::toPHPNumber($aFuncoes[$i]->getSalarioFinal());

	$indAtivoF		 = ($aFuncoes[$i]->getIndAtivo() == 1) ? "checked" : null;
	#################################################################################
	## Monta a combo de Tipo
	#################################################################################
	//$tabFuncao		.= '<tr><td class="center" style="width: 20px;"><div class="inline" zg-type="zg-div-msg"></div></td><td><input type="text" name="funcao[]" value="'.$aFuncoes[$i]->getDescricao().'" maxlength="15" autocomplete="off"></td><td class="center"><span class="center" zgdelete onclick="delRowFuncaoCargoAlt($(this));"><i class="fa fa-trash bigger-150 red"></i></span><input type="hidden" name="codFuncao[]" value="'.$aFuncoes[$i]->getCodigo().'"></td></tr>';
	$tabFuncao		.= '<tr><td class="center" style="width: 30px;"><div class="inline" zg-type="zg-div-msg"></div></td><td><input type="text" class="width-100" name="funcao[]" value="'.$aFuncoes[$i]->getDescricao().'" maxlength="60" autocomplete="off"></td><td><input type="text" class="width-100" name="salarioInicial[]" value="'.$salarioMin.'" maxlength="25" autocomplete="off" zg-data-toggle="mask" zg-data-mask="dinheiro"></td><td><input type="text" class="width-100" name="salarioFinal[]" value="'.$salarioMax.'" maxlength="25" autocomplete="off" zg-data-toggle="mask" zg-data-mask="dinheiro"></td><td class="center"><label><input name="indAtivoF" id="indAtivoFID" class="ace ace-switch ace-switch-6" type="checkbox" '.$indAtivoF.' /><span class="lbl"></span></label>	</td><td class="center"><span class="center" zgdelete onclick="delRowFuncaoCargoAlt($(this));"><i class="fa fa-trash bigger-150 red"></i></span><input type="hidden" name="codFuncao[]" value="'.$aFuncoes[$i]->getCodigo().'"></td></tr>';
	
}
################################################################################
# Url Voltar
################################################################################
$urlVoltar = ROOT_URL . "/Rhu/cargoLis.php?id=" . $id;

################################################################################
# Url Novo
################################################################################
$uid = \Zage\App\Util::encodeUrl ( '_codMenu_=' . $_codMenu_ . '&_icone_=' . $_icone_ . '&codCargo=' );
$urlNovo = ROOT_URL . "/Rhu/cargoAlt.php?id=" . $uid;

################################################################################
# Carregando o template html
################################################################################
$tpl = new \Zage\App\Template ();
$tpl->load ( \Zage\App\Util::getCaminhoCorrespondente ( __FILE__, \Zage\App\ZWS::EXT_HTML ) );

################################################################################
# Define os valores das variáveis
################################################################################
$tpl->set ( 'URL_FORM'			   , $_SERVER ['SCRIPT_NAME'] );
$tpl->set ( 'URLVOLTAR'			   , $urlVoltar );
$tpl->set ( 'URLNOVO'		 	   , $urlNovo );
$tpl->set ( 'ID'				   , $id );
$tpl->set ( 'COD_CARGO'			   , $codCargo);
$tpl->set ( 'CARGO'				   , $cargo);
$tpl->set ( 'COD_CBO'			   , $codCbo);
$tpl->set ( 'INT_ATIVO'			   , $intAtivo);

$tpl->set ( 'TAB_FUNCAO'		   , $tabFuncao);

$tpl->set ( 'DP', \Zage\App\Util::getCaminhoCorrespondente ( __FILE__, \Zage\App\ZWS::EXT_DP, \Zage\App\ZWS::CAMINHO_RELATIVO ) );

################################################################################
# Por fim exibir a página HTML
################################################################################
$tpl->show ();

