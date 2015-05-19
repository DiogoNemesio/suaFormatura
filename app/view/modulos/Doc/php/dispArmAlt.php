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
## Resgata as informações do banco
#################################################################################
if ($codDisp) {
	try {
		$info = $em->getRepository('Entidades\ZgdocDispositivoArm')->findOneBy(array('codEmpresa' => $system->getCodEmpresa(), 'codigo' => $codDisp));
		
	} catch (\Exception $e) {
		\Zage\App\Erro::halt($e->getMessage());
	}

	$ident			= $info->getIdentificacao();
	$codTipo		= ($info->getCodTipo() != null) 			? $info->getCodTipo()->getCodigo() 			: null;
	$status			= ($info->getCodStatus() != null) 			? $info->getCodStatus()->getCodigo()		: null;
	$dtElim			= $info->getDataEliminacao();
	$codLocal		= ($info->getCodLocalAtual() != null) 		? $info->getCodLocalAtual()->getCodigo()	: null;
	$codEndereco	= ($info->getCodEnderecoAtual() != null) 	? $info->getCodEnderecoAtual()->getCodigo()	: null;
	$disabled		= 'disabled';
	
}else{
	$ident			= \Zage\Adm\Semaforo::valorAtual($system->getCodEmpresa(), 'DOC_DISP_ARM_IDENTIFICACAO') + 1;
	$codTipo		= '';
	$status			= '';
	$dtElim			= '';
	$codLocal		= '';
	$codEndereco	= '';
	$disabled		= '';
	
}


#################################################################################
## Select de Tipos
#################################################################################
try {
	$aTipo	= $em->getRepository('Entidades\ZgdocDispositivoArmTipo')->findBy(array('codOrganizacao' => $system->getCodOrganizacao()));
	$oTipo	= $system->geraHtmlCombo($aTipo, 'CODIGO', 'NOME', $codTipo, null);

} catch (\Exception $e) {
	\Zage\App\Erro::halt($e->getMessage(),__FILE__,__LINE__);
}

#################################################################################
## Select do Status
#################################################################################
try {
	$aStatus	= $em->getRepository('Entidades\ZgdocDispositivoArmStatusTipo')->findAll();
	$oStatus	= $system->geraHtmlCombo($aStatus, 'CODIGO', 'NOME', $status, null);

} catch (\Exception $e) {
	\Zage\App\Erro::halt($e->getMessage(),__FILE__,__LINE__);
}


#################################################################################
## Select de Endereços
#################################################################################
try {
	$aEnd	= \Zage\Doc\Endereco::listaAtivo();
	$oEnd	= "<option value=''></option>";
	if (!$codEndereco && $aEnd) $codEndereco 	= $aEnd[0]->getCodigo();
	for ($i = 0; $i < sizeof($aEnd); $i++) {
		($codEndereco == $aEnd[$i]->getCodigo()) ? $selected = "selected=\"true\"" : $selected = "";
		$oEnd .= "<option value=\"".$aEnd[$i]->getCodigo()."\" $selected>".$aEnd[$i]->getCodLocal()->getCodDepartamento()->getNome().' - '.$aEnd[$i]->getNome().'</option>';
	}

} catch (\Exception $e) {
	\Zage\App\Erro::halt($e->getMessage(),__FILE__,__LINE__);
}

#################################################################################
## Select do Local
#################################################################################
try {
	$aLocal	= \Zage\Doc\Local::listaAtivo();
	$oLocal	= "<option value=''></option>";
	if (!$codLocal && $aLocal) $codLocal 	= $aLocal[0]->getCodigo();
	for ($i = 0; $i < sizeof($aLocal); $i++) {
		($codLocal == $aLocal[$i]->getCodigo()) ? $selected = "selected=\"true\"" : $selected = "";
		$oLocal .= "<option value=\"".$aLocal[$i]->getCodigo()."\" $selected>".$aLocal[$i]->getCodDepartamento()->getNome().' - '.$aLocal[$i]->getNome().'</option>';
	}

} catch (\Exception $e) {
	\Zage\App\Erro::halt($e->getMessage(),__FILE__,__LINE__);
}


#################################################################################
## Url Voltar
#################################################################################
$urlVoltar			= ROOT_URL."/Doc/dispArmLis.php?id=".$id;

#################################################################################
## Url Novo
#################################################################################
$uid 				= \Zage\App\Util::encodeUrl('_codMenu_='.$_codMenu_.'&_icone_='.$_icone_.'&codDisp=');
$urlNovo			= ROOT_URL."/Doc/dispArmAlt.php?id=".$uid;
$urlGerar			= ROOT_URL."/Doc/dispArmGer.php?id=".$uid;


#################################################################################
## Carregando o template html
#################################################################################
$tpl	= new \Zage\App\Template();
$tpl->load(\Zage\App\Util::getCaminhoCorrespondente(__FILE__, \Zage\App\ZWS::EXT_HTML));

#################################################################################
## Define os valores das variáveis
#################################################################################
$tpl->set('URL_FORM'			,$_SERVER['SCRIPT_NAME']);
$tpl->set('URLVOLTAR'			,$urlVoltar);
$tpl->set('URLNOVO'				,$urlNovo);
$tpl->set('URLGERAR'			,$urlGerar);
$tpl->set('ID'					,$id);
$tpl->set('COD_DISP'			,$codDisp);
$tpl->set('IDENTIFICACAO'		,$ident);
$tpl->set('TIPO'				,$oTipo);
$tpl->set('LOCAL'				,$oLocal);
$tpl->set('ENDERECO'			,$oEnd);
$tpl->set('STATUS'				,$oStatus);
$tpl->set('DATA_ELIM'			,$dtElim);
$tpl->set('DISABLED'			,$disabled);
$tpl->set('DP'					,\Zage\App\Util::getCaminhoCorrespondente(__FILE__,\Zage\App\ZWS::EXT_DP,\Zage\App\ZWS::CAMINHO_RELATIVO));


#################################################################################
## Por fim exibir a página HTML
#################################################################################
$tpl->show();

