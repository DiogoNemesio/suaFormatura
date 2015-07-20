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
## Resgata as informações do banco
#################################################################################
$codUsuario		= $_user->getCodigo();
$usuario		= $_user->getUsuario();
$nome			= $_user->getNome();
$apelido		= $_user->getApelido();
$cpf			= $_user->getCpf();
$codStatus		= ($_user->getCodStatus() != null) ? $_user->getCodStatus()->getCodigo() : null;
$sexo			= ($_user->getSexo() != null) ? $_user->getSexo()->getCodigo() : null;
$codLogradouro  = ($_user->getCodLogradouro() != null) ? $_user->getCodLogradouro()->getCodigo() : null;
$endereco		= $_user->getEndereco();
$bairro 		= $_user->getBairro();
$complemento    = $_user->getcomplemento();
$numero		    = $_user->getnumero();
$avatar			= ($_user->getAvatar() != null) ? $_user->getAvatar()->getCodigo() : null;
$avatarLink		= ($_user->getAvatar() != null) ? $_user->getAvatar()->getLink() : null;
if (empty($avatarLink)) $avatarLink		= IMG_URL.'/avatars/usuarioGenerico.png';
if (!empty($cpf)) $cpfReadonly = 'readonly';

if($codLogradouro != null){

	$infoLogradouro = $em->getRepository('Entidades\ZgadmLogradouro')->findOneBy(array('codigo' => $codLogradouro));

	if($infoLogradouro->getDescricao() == $endereco){
		$endPadrao 	  = $infoLogradouro->getDescricao();
		$bairroPadrao = $infoLogradouro->getCodBairro()->getDescricao();
		$readonly 	  = 'readonly';
	}else{
		$endPadrao 	  = $endereco;
		$bairroPadrao = $bairro;
		$readonly 	  = '';
	}

	$cep	= $infoLogradouro->getCep();
	$cidade = $infoLogradouro->getCodBairro()->getCodLocalidade()->getDescricao();
	$estado = $infoLogradouro->getCodBairro()->getCodLocalidade()->getCodUF()->getNome();

}else{
	$endPadrao 		= '';
	$bairroPadrao 	= '';
	$cidade	 		= '';
	$estado 		= '';
	$readonly 	  	= 'readonly';

}

$infoEmail = $em->getRepository('Entidades\ZgsegUsuarioHistEmail')->findOneBy(array('codUsuario' => $codUsuario, 'codStatus' => 'A'));

if(!isset($infoEmail) && empty($infoEmail)){
	$readonlyEmail = '';
	$indMudaEmail  = 0; #Pode mudar
}elseif ($infoEmail->getIndConfirmadoAnterior() == 1){
	$readonlyEmail = 'readonly'; 
	$indMudaEmail  = 1; #Nao pode mudar
	$usuario	   = $infoEmail->getEmailNovo();
}elseif ($infoEmail->getIndConfirmadoAnterior() == 0){
	$readonlyEmail = 'readonly';
	$indMudaEmail  = 1; #Nao pode mudar
}


#################################################################################
## Select de Tipo de Telefone
#################################################################################
try {
	$aTipoTel		= $em->getRepository('Entidades\ZgappTelefoneTipo')->findAll();
	$oTipoTel		= $system->geraHtmlCombo($aTipoTel,	'CODIGO', 'DESCRICAO',	null, 	null);
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

	$tabTel			.= '<tr><td class="center" style="width: 20px;"><div class="inline" zg-type="zg-div-msg"></div></td><td><select class="select2" style="width:100%;" name="codTipoTel[]" data-rel="select2">'.$oTipoInt.'</select></td><td><input type="text" name="telefone[]" value="'.$aTelefones[$i]->getTelefone().'" maxlength="15" autocomplete="off" zg-data-toggle="mask" zg-data-mask="fone" zg-data-mask-retira="1"></td><td class="center"><span class="center" zgdelete onclick="delRowTelefonePessoaAlt($(this));"><i class="fa fa-trash bigger-150 red"></i></span><input type="hidden" name="codTelefone[]" value="'.$aTelefones[$i]->getCodigo().'"></td></tr>';
}

#################################################################################
## Select de Status / Sexo
#################################################################################
try {
	$aSexo		= $em->getRepository('Entidades\ZgsegSexoTipo')->findAll();
	$oSexo		= $system->geraHtmlCombo($aSexo,	'CODIGO', 'DESCRICAO',	$sexo, 		null);
} catch (\Exception $e) {
	\Zage\App\Erro::halt($e->getMessage(),__FILE__,__LINE__);
}

#################################################################################
## Avatars
#################################################################################
$aAvatar		= $em->getRepository('Entidades\ZgsegAvatar')->findBy(array('sexo' => $sexo));
$hAvatar		= "";
if ($aAvatar) {
	foreach ($aAvatar as $av) {
		$hAvatar	.= '<li>';
		$hAvatar	.= '<a href="'.$av->getLink().'" title="'.$av->getNome().'" data-rel="colorbox">';
		$hAvatar	.= '<img height="50" width="50" src="'.$av->getLink().'" />';
		$hAvatar	.= '<div class="tags"></div>';
		$hAvatar	.= '</a>';
		$hAvatar	.= '<div class="tools tools-bottom">';
		$hAvatar	.= '<a href="javascript:zgAlteraAvatar(\''.$av->getCodigo().'\',\''.$av->getLink().'\');"><i class="fa fa-check"></i></a>';
		$hAvatar	.= '</div>';
		$hAvatar	.= '</li>';
	}
}


#################################################################################
## Carregando o template html
#################################################################################
$tpl	= new \Zage\App\Template();
$tpl->load(\Zage\App\Util::getCaminhoCorrespondente(__FILE__, \Zage\App\ZWS::EXT_HTML));

#################################################################################
## Define os valores das variáveis
#################################################################################
$tpl->set('URL_FORM'		,$_SERVER['SCRIPT_NAME']);
$tpl->set('URLVOLTAR'		,null);
$tpl->set('ID'				,$id);
$tpl->set('COD_USUARIO'		,$codUsuario);
$tpl->set('USUARIO'			,$usuario);
$tpl->set('NOME'			,$nome);
$tpl->set('TELEFONE'		,$telefone);
$tpl->set('CELULAR'			,$celular);
$tpl->set('SEXO'			,$oSexo);
$tpl->set('APELIDO'			,$apelido);
$tpl->set('CPF'				,$cpf);
$tpl->set('COD_STATUS'		,$codStatus);
$tpl->set('COD_AVATAR'		,$avatar);
$tpl->set('AVATAR_LINK'		,$avatarLink);
$tpl->set('AVATARS'			,$hAvatar);
$tpl->set('TAB_TELEFONE'	,$tabTel);
$tpl->set('TIPO_TEL'		,$oTipoTel);
$tpl->set('COD_LOGRADOURO'  ,$codLogradouro);
$tpl->set('LOGRADOURO'	 	,$endPadrao);
$tpl->set('BAIRRO'			,$bairroPadrao);
$tpl->set('ENDERECO' 		,$endereco);
$tpl->set('CIDADE'		 	,$cidade);
$tpl->set('ESTADO'			,$estado);
$tpl->set('COMPLEMENTO' 	,$complemento);
$tpl->set('NUMERO' 			,$numero);
$tpl->set('CEP'				,$cep);
$tpl->set('READONLY'		,$readonly);
$tpl->set('CPF_READONLY'	,$cpfReadonly);
$tpl->set('EMAIL_READONLY'	,$readonlyEmail);
$tpl->set('IND_MUDAUSER'	,$indMudaEmail);

$tpl->set('DP'				,\Zage\App\Util::getCaminhoCorrespondente(__FILE__,\Zage\App\ZWS::EXT_DP,\Zage\App\ZWS::CAMINHO_RELATIVO));
$tpl->set('IC'				,$_icone_);

#################################################################################
## Por fim exibir a página HTML
#################################################################################
$tpl->show();
