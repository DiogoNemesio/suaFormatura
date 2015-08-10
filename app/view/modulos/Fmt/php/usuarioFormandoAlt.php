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
## Verificar parâmetros obrigatórios
#################################################################################
$codOrganizacao = $system->getCodOrganizacao();
if (!isset($codOrganizacao)) \Zage\App\Erro::halt('Falta de Parâmetros : COD_ORGANIZACAO');

#################################################################################
## Resgata as informações do banco
#################################################################################
if ($codUsuario) {
	
	$podeAlterar = 'readonly';
	
	try {
		$info			= $em->getRepository('Entidades\ZgsegUsuario')->findOneBy(array('codigo' => $codUsuario));
		$oPerfil		= $em->getRepository('Entidades\ZgsegUsuarioOrganizacao')->findOneBy(array('codUsuario' => $codUsuario, 'codOrganizacao'=> $codOrganizacao));
	} catch (\Exception $e) {
		\Zage\App\Erro::halt($e->getMessage());
	}
	$usuario		= $info->getUsuario();
	$nome			= $info->getNome();
	$apelido		= $info->getApelido();
	$cpf			= $info->getCpf();
	$rg				= ($info->getRg()) ? $info->getRg() : null;
	$dataNasc		= $info->getDataNascimento()->format($system->config["data"]["dateFormat"]);
	$perfil			= $oPerfil->getCodPerfil()->getCodigo();
	$codStatus		= $info->getCodStatus()->getCodigo();
	$sexo			= ($info->getSexo()) ? $info->getSexo()->getCodigo() : null;

	/** Endereco **/
	$codLogradouro   = ($info->getCodLogradouro()) ? $info->getCodLogradouro()->getCodigo() : null;
	$cep 		     = ($info->getCep()) ? $info->getCep() : null;
	$complemento     = ($info->getComplemento()) ? $info->getComplemento() : null;
	$numero		     = ($info->getNumero()) ? $info->getNumero() : null;
	$endCorreto		 = ($info->getIndEndCorreto() == 1) ? "checked" : null;
	
	if($codLogradouro != null){
	
		$infoLogradouro = $em->getRepository('Entidades\ZgadmLogradouro')->findOneBy(array('codigo' => $codLogradouro));
	
		if($info->getIndEndCorreto() == 0){
			if($infoLogradouro->getDescricao() == $info->getEndereco()){
				$logradouro	  = $infoLogradouro->getDescricao();
				$readOnlyEnd 	  = 'readonly';
			}else{
				$logradouro	  = $info->getEndereco();
				$readOnlyEnd 	  = '';
			}
			
			if($infoLogradouro->getCodBairro()->getDescricao() == $info->getBairro()){
				$bairro = $infoLogradouro->getCodBairro()->getDescricao();
				$readOnlyBairro 	  = 'readonly';
			}else{
				$bairro = $info->getBairro();
				$readOnlyBairro 	  = '';
			}
		
		}else{
			$logradouro 	= $infoLogradouro->getDescricao();
			$bairro 		= $infoLogradouro->getCodBairro()->getDescricao();
			$readOnlyBairro = 'readonly';
			$readOnlyEnd 	= 'readonly';
		}
		
		$cidade	  		 = $infoLogradouro->getCodBairro()->getCodLocalidade()->getCodCidade()->getNome();
		$estado    		 = $infoLogradouro->getCodBairro()->getCodLocalidade()->getCodCidade()->getCodUF()->getNome();
	}else{
		$readOnlyBairro = 'readonly';
		$readOnlyEnd 	= 'readonly';
	}

}else{
	
	$podeAlterar	= null;
	$usuario		= null;
	$nome			= null;
	$dataNasc		= null;
	$rg				= null;
	$apelido		= null;
	$cpf			= null;
	$perfil			= null;
	$codStatus		= null;
	$sexo			= null;
	
	$codLogradouro	= null;
	$cep			= null;
	$complemento	= null;
	$numero			= null;
	$endCorreto		= null;
	$cidade			= null;
	$estado			= null;
	$readOnlyBairro = 'readonly';
	$readOnlyEnd 	= 'readonly';
	
}

#################################################################################
## Urls
#################################################################################
$uid 				= \Zage\App\Util::encodeUrl('_codMenu_='.$_codMenu_.'&_icone_='.$_icone_.'&codUsuario=');
$urlVoltar			= ROOT_URL . "/Fmt/usuarioFormandoLis.php?id=".$uid;
$urlNovo			= ROOT_URL . "/Fmt/usuarioFormandoAlt.php?id=".$uid;

#################################################################################
## Select de Sexo
#################################################################################
try {
	$aSexo		= $em->getRepository('Entidades\ZgsegSexoTipo')->findAll();
	$oSexo		= $system->geraHtmlCombo($aSexo,	'CODIGO', 'DESCRICAO',	$sexo, null);
} catch (\Exception $e) {
	\Zage\App\Erro::halt($e->getMessage(),__FILE__,__LINE__);
}

#################################################################################
## Select de perfil
#################################################################################

try {
	$aPerfil	= \Zage\Seg\Perfil::listaPerfilOrganizacao($codOrganizacao);
	$oPerfil	= $system->geraHtmlCombo($aPerfil, 'CODIGO', 'NOME', $perfil , null);
} catch (\Exception $e) {
	\Zage\App\Erro::halt($e->getMessage(),__FILE__,__LINE__);
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
$aTelefones		= $em->getRepository('Entidades\ZgsegUsuarioTelefone')->findBy(array('codProprietario' => $codUsuario));
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
$tpl->set('ID'					,$id);
$tpl->set('PODE_ALTERAR'		,$podeAlterar);
$tpl->set('COD_USUARIO'			,$codUsuario);
$tpl->set('COD_ORGANIZACAO'		,$codOrganizacao);
$tpl->set('USUARIO'				,$usuario);
$tpl->set('NOME'				,$nome);
$tpl->set('RG'					,$rg);
$tpl->set('DATA_NASC'			,$dataNasc);
$tpl->set('APELIDO'				,$apelido);
$tpl->set('EMAIL'				,$email);
$tpl->set('CPF'					,$cpf);
$tpl->set('PERFIL'				,$oPerfil);
$tpl->set('SEXO'				,$oSexo);

$tpl->set('TIPO_TEL'			,$oTipoTel);
$tpl->set('SEGMENTO'			,$oSegmento);
$tpl->set('TAB_TELEFONE'		,$tabTel);

$tpl->set ('COD_LOGRADOURO' 	, $codLogradouro);
$tpl->set ('CEP' 				, $cep);
$tpl->set ('LOGRADOURO'			, $logradouro);
$tpl->set ('BAIRRO'				, $bairro);
$tpl->set ('CIDADE'				, $cidade);
$tpl->set ('ESTADO'		 		, $estado);
$tpl->set ('COMPLEMENTO' 		, $complemento);
$tpl->set ('NUMERO' 			, $numero);
$tpl->set ('READONLY_BAIRRO'	, $readOnlyBairro);
$tpl->set ('READONLY_END' 	 	, $readOnlyEnd);
$tpl->set ('IND_END_CORRETO'	, $endCorreto);
$tpl->set ('READONLY_BAIRRO'	, $readOnlyBairro);
$tpl->set ('READONLY_END' 	 	, $readOnlyEnd);

$tpl->set ('DUAL_LIST'	 	 	, $htmlLis);

$tpl->set('DP'					,\Zage\App\Util::getCaminhoCorrespondente(__FILE__,\Zage\App\ZWS::EXT_DP,\Zage\App\ZWS::CAMINHO_RELATIVO));


#################################################################################
## Por fim exibir a página HTML
#################################################################################
$tpl->show();

