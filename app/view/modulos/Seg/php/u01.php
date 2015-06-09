<?php
#################################################################################
## Includes
#################################################################################
if (defined('DOC_ROOT')) {
	include_once(DOC_ROOT . 'includeNoAuth.php');
}else{
	include_once('includeNoAuth.php');
}

#################################################################################
## Resgata a variável CID que está criptografada
#################################################################################
if (isset($_GET['cid'])) $cid = \Zage\App\Util::antiInjection($_GET["cid"]);
if (!isset($cid))		\Zage\App\Erro::externalHalt('Script só pode ser usado por pessoas autorizadas');

#################################################################################
## Descompacta o ID
#################################################################################
\Zage\App\Util::descompactaId($cid);

if (!isset($_cdu01))	\Zage\App\Erro::externalHalt('Script só pode ser usado por pessoas autorizadas, COD_ERRO: 01');
if (!isset($_cdu02))	\Zage\App\Erro::externalHalt('Script só pode ser usado por pessoas autorizadas, COD_ERRO: 02');
if (!isset($_cdu03))	\Zage\App\Erro::externalHalt('Script só pode ser usado por pessoas autorizadas, COD_ERRO: 03');
if (!isset($_cdu04))	\Zage\App\Erro::externalHalt('Script só pode ser usado por pessoas autorizadas, COD_ERRO: 04');
if (!isset($_cdsenha))	\Zage\App\Erro::externalHalt('Script só pode ser usado por pessoas autorizadas, COD_ERRO: 05');

#################################################################################
## Ajusta os nomes das variáveis
#################################################################################
$codAssoc		= $_cdu01;
$codUsuario		= $_cdu02;
$codOrganizacao	= $_cdu03;
$codConvite		= $_cdu04;
$senha			= $_cdsenha;

#################################################################################
## Verificar se os usuário já existe e se já está ativo
#################################################################################
$oUsuario	= $em->getRepository('Entidades\ZgsegUsuario')->findOneBy(array('codigo' => $codUsuario));
if (!$oUsuario) 											\Zage\App\Erro::externalHalt('Convite não está mais disponível, COD_ERRO: 06');
if ($oUsuario->getCodStatus()->getCodigo() != "P")			\Zage\App\Erro::externalHalt('Convite não está mais disponível, COD_ERRO: 07');

#################################################################################
## Verificar a associação do usuário a Organização
#################################################################################
$oUsuOrg		= $em->getRepository('Entidades\ZgsegUsuarioOrganizacao')->findOneBy(array('codigo' => $codAssoc));
if (!$oUsuOrg) 										\Zage\App\Erro::externalHalt('Convite não está mais disponível, COD_ERRO: 08');
if ($oUsuOrg->getCodStatus()->getCodigo() != "P")	\Zage\App\Erro::externalHalt('Convite não está mais disponível, COD_ERRO: 09');

#################################################################################
## Verificar a senha do convite
#################################################################################
$convite		= $em->getRepository('Entidades\ZgsegConvite')->findOneBy(array('codigo' => $codConvite));
if (!$convite) 								\Zage\App\Erro::externalHalt('Convite não está mais disponível, COD_ERRO: 10');
if ($convite->getSenha() != $senha)			\Zage\App\Erro::externalHalt('Convite não está mais disponível, COD_ERRO: 11');
if ($convite->getIndUtilizado() != 0)		\Zage\App\Erro::externalHalt('Convite não está mais disponível, COD_ERRO: 12');

#################################################################################
## Select de Sexo
#################################################################################
try {
	$aSexo		= $em->getRepository('Entidades\ZgsegSexoTipo')->findAll();
	$oSexo		= $system->geraHtmlCombo($aSexo,	'CODIGO', 'DESCRICAO',	$sexo, null);
} catch (\Exception $e) {
	\Zage\App\Erro::externalHalt($e->getMessage(),__FILE__,__LINE__);
}

#################################################################################
## Gera o código javascript das máscaras
#################################################################################
$mascaras	= $em->getRepository('Entidades\ZgappMascara')->findAll();
$htmlMask		= "";
for ($i = 0; $i < sizeof($mascaras); $i++) {
	if ($mascaras[$i]->getIndReversa() == 1) {
		$reverse	= ",reverse: true";
	}else{
		$reverse	= "";
	}

	if ($mascaras[$i]->getIndTamanhoFixo() === 0) {
		$maxLen	= ",maxlength: false";
	}else{
		$maxLen	= "";
	}

	$htmlMask	.= "'".strtolower($mascaras[$i]->getNome())."': { mascara: '".$mascaras[$i]->getMascara()."' $reverse $maxLen},";
}
$htmlMask = substr($htmlMask, 0 , -1);


#################################################################################
## Carregando o template html
#################################################################################
$tpl	= new \Zage\App\Template();
$tpl->load(\Zage\App\Util::getCaminhoCorrespondente(__FILE__, \Zage\App\ZWS::EXT_HTML));


#################################################################################
## Define os valores das variáveis
#################################################################################
$tpl->set('URL_FORM'			,$_SERVER['SCRIPT_NAME']);
$tpl->set('USUARIO'				,$oUsuario->getUsuario());
$tpl->set('NOME'				,$oUsuario->getNome());
$tpl->set('SEXO'				,$oSexo);
$tpl->set('MASCARAS'			,$htmlMask);
$tpl->set('CD01'				,$_cdu01);
$tpl->set('CD02'				,$_cdu02);
$tpl->set('CD03'				,$_cdu03);
$tpl->set('CD04'				,$_cdu04);
$tpl->set('DP'					,\Zage\App\Util::getCaminhoCorrespondente(__FILE__,\Zage\App\ZWS::EXT_DP,\Zage\App\ZWS::CAMINHO_RELATIVO));

#################################################################################
## Por fim exibir a página HTML
#################################################################################
$tpl->show();
