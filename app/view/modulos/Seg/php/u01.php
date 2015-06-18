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
## Resgatar as informações já disponíveis
#################################################################################
$apelido					= $oUsuario->getApelido();
$cpf						= $oUsuario->getCpf();
$cep						= $oUsuario->getCep();
$codLogradouro   			= ($oUsuario->getCodLogradouro()) ? $oUsuario->getCodLogradouro()->getCodigo() : null;
$logradouro					= $oUsuario->getEndereco();
$bairro						= $oUsuario->getBairro();
$descCidade					= ($oUsuario->getCodLogradouro()) ? $oUsuario->getCodLogradouro()->getCodBairro()->getCodLocalidade()->getCodCidade()->getNome() : null;
$descEstado					= ($oUsuario->getCodLogradouro()) ? $oUsuario->getCodLogradouro()->getCodBairro()->getCodLocalidade()->getCodCidade()->getCodUf()->getCodUf() : null;
$numero						= $oUsuario->getNumero();
$complemento				= $oUsuario->getComplemento();

#################################################################################
## Select de Sexo
#################################################################################
$sexo		= ($oUsuario->getSexo()) ? $oUsuario->getSexo()->getCodigo() : null;
try {
	$aSexo		= $em->getRepository('Entidades\ZgsegSexoTipo')->findAll();
	$oSexo		= $system->geraHtmlCombo($aSexo,	'CODIGO', 'DESCRICAO',	$sexo, null);
} catch (\Exception $e) {
	\Zage\App\Erro::externalHalt($e->getMessage(),__FILE__,__LINE__);
}

#################################################################################
## Select de Tipo de Telefone
#################################################################################
try {
	$aTipoTel		= $em->getRepository('Entidades\ZgappTelefoneTipo')->findAll();
	$oTipoTel		= $system->geraHtmlCombo($aTipoTel,	'CODIGO', 'DESCRICAO',	null, 		null);
} catch (\Exception $e) {
	\Zage\App\Erro::halt($e->getMessage(),__FILE__,__LINE__);
}

#################################################################################
## Resgatar os dados de contato
#################################################################################
$aTelefones		= $em->getRepository('Entidades\ZgsegUsuarioTelefone')->findBy(array('codUsuario' => $codUsuario));
$tabTel			= "";
for ($i = 0; $i < sizeof($aTelefones); $i++) {

	#################################################################################
	## Monta a combo de Tipo
	#################################################################################
	$codTipoTel		= ($aTelefones[$i]->getCodTipoTelefone()) ? $aTelefones[$i]->getCodTipoTelefone()->getCodigo() : null;
	$oTipoInt		= $system->geraHtmlCombo($aTipoTel,	'CODIGO', 'DESCRICAO',	$codTipoTel, '');

	$tabTel			.= '<tr><td class="center" style="width: 20px;"><div class="inline" zg-type="zg-div-msg"></div></td><td><select class="select2" style="width:100%;" name="codTipoTel[]" data-rel="select2">'.$oTipoInt.'</select></td><td><input type="text" name="telefone[]" style="width:100%;" value="'.$aTelefones[$i]->getTelefone().'" maxlength="15" autocomplete="off" zg-data-toggle="mask" zg-data-mask="fone" zg-data-mask-retira="1"></td><td class="center"><span class="center" zgdelete onclick="delRowTelefonePessoaAlt($(this));"><i class="fa fa-trash bigger-150 red"></i></span><input type="hidden" name="codTelefone[]" value="'.$aTelefones[$i]->getCodigo().'"></td></tr>';
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
## Urls
#################################################################################
$org = $em->getRepository('Entidades\ZgadmOrganizacao')->findOneBy(array('codigo' => $codOrganizacao));
$urlRedirecionar	= ROOT_URL . "/".$org->getIdentificacao();


#################################################################################
## Carregando o template html
#################################################################################
$tpl	= new \Zage\App\Template();
$tpl->load(\Zage\App\Util::getCaminhoCorrespondente(__FILE__, \Zage\App\ZWS::EXT_HTML));


#################################################################################
## Define os valores das variáveis
#################################################################################
$tpl->set('URL_FORM'			,$_SERVER['SCRIPT_NAME']);
$tpl->set('REDIRECIONAR'		,$urlRedirecionar);
$tpl->set('USUARIO'				,$oUsuario->getUsuario());
$tpl->set('NOME'				,$oUsuario->getNome());
$tpl->set('SEXO'				,$oSexo);
$tpl->set('MASCARAS'			,$htmlMask);
$tpl->set('CD01'				,$_cdu01);
$tpl->set('CD02'				,$_cdu02);
$tpl->set('CD03'				,$_cdu03);
$tpl->set('CD04'				,$_cdu04);
$tpl->set('APELIDO'				,$apelido);
$tpl->set('CPF'					,$cpf);
$tpl->set('CEP'					,$cep);

$tpl->set('TIPO_TEL'			,$oTipoTel);
$tpl->set('TAB_TELEFONE'		,$tabTel);

$tpl->set('COD_LOGRADOURO' 		, $codLogradouro);
$tpl->set('LOGRADOURO'			,$logradouro);
$tpl->set('BAIRRO'				,$bairro);
$tpl->set('DESC_CIDADE'			,$descCidade);
$tpl->set('DESC_ESTADO'			,$descEstado);
$tpl->set('NUMERO'				,$numero);
$tpl->set('COMPLEMENTO'			,$complemento);
$tpl->set('DP'					,\Zage\App\Util::getCaminhoCorrespondente(__FILE__,\Zage\App\ZWS::EXT_DP,\Zage\App\ZWS::CAMINHO_RELATIVO));

#################################################################################
## Por fim exibir a página HTML
#################################################################################
$tpl->show();
