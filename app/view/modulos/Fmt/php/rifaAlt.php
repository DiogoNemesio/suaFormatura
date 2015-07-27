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
if ((isset($codRifa) && ($codRifa))) {

	try {
		
		$info 		= $em->getRepository('Entidades\ZgadmOrganizacao')->findOneBy(array('codigo' => $codRifa));
		
	} catch (\Exception $e) {
		\Zage\App\Erro::halt($e->getMessage());
	}
	
	$tipo			= $info->getCodTipoPessoa()->getCodigo();
	$segmento		= $info->getCodTipo()->getCodigo();
	$email			= $info->getEmail();
	$ident			= $info->getIdentificacao();
	$disabled		= 'disabled';
	$readOnly		= 'readonly';
	
	if ($tipo == 'J') {

		$nome			= $info->getNome();
		$cnpj			= $info->getCgc();
		$razao			= $info->getRazao();
		$inscEstadual	= $info->getInscEstadual();
		$inscMunicipal	= $info->getInscMunicipal();
		$datInicio 		= ($info->getDataNascimento() != null) ? $info->getDataNascimento()->format($system->config["data"]["dateFormat"]) : null;	
		$cpf			= '';
		$rg				= '';
		$sexo			= '';
		$dataNascimento = '';
	
	}elseif($tipo == 'F') {
		
		$nome			= '';
		$cnpj			= '';
		$fantasia		= '';
		$inscEstadual	= '';
		$inscMunicipal	= '';
		$datInicio 		= '';
		$nome			= $info->getNome();
		$cpf			= $info->getCgc();
		$rg				= $info->getRg();
		$sexo			= ($info->getCodSexo() != null) ? $info->getCodSexo()->getCodigo() : null;
		$dataNascimento	= ($info->getDataNascimento() != null) ? $info->getDataNascimento()->format($system->config["data"]["dateFormat"]) : null;

	}
	
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
	
	$codRifa			= null;
	$nome				= null;
	$premio				= null;
	$custo				= null;
	$dataSorteio		= null;
	$localSorteio 		= null;
	$qtdeObrigatorio	= null;
	$valor				= null;
	
	

}

#################################################################################
## Url Voltar
#################################################################################
$urlVoltar			= ROOT_URL."/Fmt/rifaLis.php?id=".$id;

#################################################################################
## Url Novo
#################################################################################
$uid = \Zage\App\Util::encodeUrl('_codMenu_='.$_codMenu_.'&_icone_='.$_icone_.'&codRifa=');
$urlNovo			= ROOT_URL."/Fmt/rifaAlt.php?id=".$uid;

#################################################################################
## Resgata as informaões do tipo PF/PJ
#################################################################################
try {
	$aTipo	= $em->getRepository('Entidades\ZgadmOrganizacaoPessoaTipo')->findAll();
	$oTipo	= $system->geraHtmlCombo($aTipo,'CODIGO', 'DESCRICAO', $tipo, null);

} catch (\Exception $e) {
	\Zage\App\Erro::halt($e->getMessage(),__FILE__,__LINE__);
}

#################################################################################
## Select de Sexo
#################################################################################
try {
	$aSexo		= $em->getRepository('Entidades\ZgsegSexoTipo')->findAll();
	$oSexo		= $system->geraHtmlCombo($aSexo,	'CODIGO', 'DESCRICAO',	$sexo, 		null);
} catch (\Exception $e) {
	\Zage\App\Erro::halt($e->getMessage(),__FILE__,__LINE__);
}

#################################################################################
## Select de Segmento (tipo de organização)
#################################################################################
try {
	$aSegmento	= \Zage\Adm\Organizacao::listaTipoOrganizacaoParceiro();
	
	$oSegmento	= $system->geraHtmlCombo($aSegmento,	'CODIGO', 'DESCRICAO',	$segmento, 		null);
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
$aTelefones		= $em->getRepository('Entidades\ZgadmOrganizacaoTelefone')->findBy(array('codOrganizacao' => $codRifa));
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
## Lista de segmentos de mercado
#################################################################################
$usuAtivo		= \Zage\Seg\Usuario::listaUsuarioOrganizacaoAtivo($system->getCodOrganizacao(), F);
$numUsuAtivo	= sizeof($usuAtivo);

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
$tpl->set('NUM_USUARIO'				,$numUsuAtivo);

$tpl->set('COD_RIFA'				,$codRifa);
$tpl->set('NOME'					,$nome);
$tpl->set('PREMIO'					,$premio);
$tpl->set('CUSTO'					,$custo);
$tpl->set('DATA_SORTEIO'			,$dataSorteio);
$tpl->set('LOCAL_SORTEIO'			,$localSorteio);
$tpl->set('QTDE_OBRIGATORIO'		,$qtdeObrigatorio);
$tpl->set('VALOR'					,$valor);

$tpl->set('DP'						,\Zage\App\Util::getCaminhoCorrespondente(__FILE__,\Zage\App\ZWS::EXT_DP,\Zage\App\ZWS::CAMINHO_RELATIVO));
$tpl->set('IC'						,$_icone_);
$tpl->set('COD_MENU'				,$_codMenu_);

#################################################################################
## Por fim exibir a página HTML
#################################################################################
$tpl->show();
