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
if ($codEvento) {
	try {
		$info 			= $em->getRepository ('Entidades\ZgfmtEvento')->findOneBy(array('codigo' => $codEvento));
	} catch (\Exception $e) {
		\Zage\App\Erro::halt ($e->getMessage());
	}
	
	$codTipo		 = ($info->getCodTipoEvento()) ? $info->getCodTipoEvento()->getCodigo() : null;
	$tipoEventoDesc	 = ($info->getCodTipoEvento()) ? $info->getCodTipoEvento()->getDescricao() : null;
	$codFornecedor	 = ($info->getCodPessoa()) ? $info->getCodPessoa()->getCodigo() : null;
	$dataEvento		 = ($info->getData() != null) ? $info->getData()->format($system->config["data"]["datetimeSimplesFormat"]) : null;
	$qtdeConvite	 = ($info->getQtdeConvite()) ? $info->getQtdeConvite() : null;
	$valorAvulso	 = ($info->getValorAvulso()) ? \Zage\App\Util::formataDinheiro($info->getValorAvulso()) : null;
	
}else {
	$info 		= $em->getRepository ('Entidades\ZgfmtEventoTipo')->findOneBy(array('codigo' => $codTipo));
	
	//Resgatar a quantidade de convites do orçamento
	if ($info->getCodigo() == '8'){
		$oOrgFmt 	= $em->getRepository ('Entidades\ZgfmtOrganizacaoFormatura')->findOneBy(array('codOrganizacao' => $system->getCodOrganizacao()));
		if ($oOrgFmt->getQtdePrevistaConvidados()){
			$qtdeConvite	= $oOrgFmt->getQtdePrevistaConvidados();
			$readOnlyConv	= 'readonly';
		}else{
			$qtdeConvite	= null;
			$readOnlyConv	= '';
		}
	}else{
		$qtdeConvite 	 = null;
		$readOnlyConv	= '';
	}

	$tipoEventoDesc	 = $info->getDescricao();
	$dataEvento		 = null;
	$codFornecedor	 = null;
	$valorAvulso 	 = null;
}

################################################################################
# Resgatar informações da formatura
################################################################################
$oOrfFmt = $em->getRepository ('Entidades\ZgfmtOrganizacaoFormatura')->findOneBy(array('codOrganizacao' => $system->getCodOrganizacao()));

$instituicao = $oOrfFmt->getCodInstituicao()->getNome();

################################################################################
# Parceiros
################################################################################
/**$oFornecedor	= \Zage\Fin\Pessoa::lista($system->getCodOrganizacao(),array("F","J"),"indFornecedor");
$vFornecedor	= array();
for ($i = 0; $i < sizeof($oFornecedor); $i++) {
	//$vFornecedor .= "'".$oFornecedor[$i]->getNome()."',";
	$vFornecedor[$i]['FANTASIA'] = $oFornecedor[$i]->getFantasia();
	$vFornecedor[$i]['CODIGO'] = $oFornecedor[$i]->getCodigo();
}

$vFornecedor = json_encode($vFornecedor);
**/

################################################################################
# Select de Local
################################################################################
$arraySeg = array();
$arrayCat = array();

switch ($codTipo) {
	case 1 : //Missa (igreja católica)
		$arrayCat[] = "31";
		break;
	case 2 : // Igreja evangélica(culto)
		$arrayCat[] = "46";
		break;
	case 3 : // Culto espírita
		$arrayCat[] = "47"; 
		break;
	case 4 : //Cerimonia ecumenica
		$arrayCat[] = ["31","46","47"];
		break;
	case 6 : // Aula da saudade
		$arrayCat[] = "29";
		break;
	case 7 : // Colação de grau (treatro ou autidório)
		$arrayCat[] = "30";
		break;
	case 8 :
		$arrayCat[] = "29";
		break;
}

try {
	$aFornecedor	= \Zage\Fin\Pessoa::lista($system->getCodOrganizacao(),array("F","J"),"indFornecedor",$arraySeg,$arrayCat,null,null,1);
	$oFornecedor 	= $system->geraHtmlCombo($aFornecedor, 'CODIGO', 'FANTASIA', $codFornecedor, '');
} catch (\Exception $e) {
	\Zage\App\Erro::halt($e->getMessage(),__FILE__,__LINE__);
}

################################################################################
# Url Voltar
################################################################################
$urlVoltar = ROOT_URL . "/Fmt/eventoAgendarLis.php?id=" . $id;

################################################################################
# Url Novo
################################################################################
$uid = \Zage\App\Util::encodeUrl ( '_codMenu_=' . $_codMenu_ . '&_icone_=' . $_icone_ );
$urlNovo = ROOT_URL . "/Fmt/eventoAgendarLis.php?id=" . $uid;

################################################################################
# Carregando o template html
################################################################################
$tpl = new \Zage\App\Template ();
$tpl->load ( \Zage\App\Util::getCaminhoCorrespondente ( __FILE__, \Zage\App\ZWS::EXT_HTML ) );

################################################################################
# Define os valores das variáveis
################################################################################
$tpl->set ( 'URL_FORM'			  , $_SERVER ['SCRIPT_NAME'] );
$tpl->set ( 'URLVOLTAR'			  , $urlVoltar );
$tpl->set ( 'URLNOVO'		 	  , $urlNovo );
$tpl->set ( 'ID'				  , $id );
$tpl->set ( 'COD_EVENTO'		  , $codEvento);
$tpl->set ( 'COD_TIPO'		   	  , $codTipo);
$tpl->set ( 'DESCRICAO_EVENTO' 	  , $tipoEventoDesc);
$tpl->set ( 'INSTITUICAO' 	   	  , $instituicao);
$tpl->set ( 'QTDE_CONVITE' 	   	  , $qtdeConvite);
$tpl->set ( 'VALOR_AVULSO' 	   	  , $valorAvulso);

$tpl->set ( 'COD_LOCAL'		   	  , $codLocal);
$tpl->set ( 'DATA_EVENTO'	   	  , $dataEvento);

$tpl->set ( 'READONLY_CONV'		  , $readOnlyConv);
$tpl->set ( 'READONLY'			  , $readonly);
$tpl->set ( 'FORNECEDOR' 		  , $oFornecedor);

$tpl->set ( 'DP', \Zage\App\Util::getCaminhoCorrespondente ( __FILE__, \Zage\App\ZWS::EXT_DP, \Zage\App\ZWS::CAMINHO_RELATIVO ) );

################################################################################
# Por fim exibir a página HTML
################################################################################
$tpl->show ();

