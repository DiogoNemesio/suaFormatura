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
if ((isset($codParceiro) && ($codParceiro))) {

	try {
		
		$info 		= $em->getRepository('Entidades\ZgadmOrganizacao')->findOneBy(array('codigo' => $codParceiro));
		
	} catch (\Exception $e) {
		\Zage\App\Erro::halt($e->getMessage());
	}
	
	$tipo			= $info->getCodTipoPessoa()->getCodigo();
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
		
		$razao			= '';
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
	
	$codParceiro	= null;
	$tipo			= 'J';
	$ident			= '';
	$nome			= '';
	$cpf			= '';
	$rg				= '';
	$sexo			= '';
	$dataNascimento = '';
	$razao			= '';
	$cnpj			= '';
	$fantasia		= '';
	$inscEstadual	= '';
	$inscMunicipal	= '';
	$email			= '';
	$link			= '';
	$disabled		= '';
	$segmento		= '';
	$readOnly		= '';
	
	$codLogradouro  = '';
	$logradouro		= '';
	$bairro			= '';
	$cidade			= '';
	$estado			= '';
	$complemento   	= '';
	$numero		    = '';
	$readOnlyBairro	= 'readonly';
	$readOnlyEnd	= 'readonly';
	$endCorreto  = '';

}

#################################################################################
## Url Voltar
#################################################################################
$urlVoltar			= ROOT_URL."/Fmt/parceiroLis.php?id=".$id;

#################################################################################
## Url Novo
#################################################################################
$uid = \Zage\App\Util::encodeUrl('_codMenu_='.$_codMenu_.'&_icone_='.$_icone_.'&codParceiro=');
$urlNovo			= ROOT_URL."/Fmt/parceiroAlt.php?id=".$uid;

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
$aTelefones		= $em->getRepository('Entidades\ZgadmOrganizacaoTelefone')->findBy(array('codOrganizacao' => $codParceiro));
$tabTel			= "";
for ($i = 0; $i < sizeof($aTelefones); $i++) {

	#################################################################################
	## Monta a combo de Tipo
	#################################################################################
	$codTipoTel		= ($aTelefones[$i]->getCodTipoTelefone()) ? $aTelefones[$i]->getCodTipoTelefone()->getCodigo() : null;
	$oTipoInt		= $system->geraHtmlCombo($aTipoTel,	'CODIGO', 'DESCRICAO',	$codTipoTel, '');

	$tabTel			.= '<tr><td class="center" style="width: 20px;"><div class="inline" zg-type="zg-div-msg"></div></td><td><select class="select2" style="width:100%;" name="codTipoTel[]" data-rel="select2">'.$oTipoInt.'</select></td><td><input type="text" name="telefone[]" value="'.$aTelefones[$i]->getTelefone().'" maxlength="15" autocomplete="off" zg-data-toggle="mask" zg-data-mask="fone" zg-data-mask-retira="1"></td><td class="center"><span class="center" zgdelete onclick="delRowTelefonePessoaAlt($(this));"><i class="fa fa-trash bigger-150 red"></i></span><input type="hidden" name="codTelefone[]" value="'.$aTelefones[$i]->getCodigo().'"></td></tr>';
}

/**
#################################################################################
## Lista de segmentos de mercado
#################################################################################
$segMer			= $em->getRepository('Entidades\ZgfmtSegmentoMercado')->findBy(array(),array('nome' => ASC));
$segAss			= $em->getRepository('Entidades\ZgadmOrganizacaoSegmento')->findBy(array('codOrganizacao' => $codParceiro));
$arraySegAss 	= array(); 

for ($i = 0; $i < sizeof($segAss); $i++) {
	$arraySegAss[$i] = $segAss[$i]->getCodSegmento()->getCodigo();
}

$htmlSeg		= "";

for ($i = 0; $i < sizeof($segMer); $i++) {
	
	if (in_array($segMer[$i]->getCodigo(), $arraySegAss)){
		$selected = 'selected'; 
	}else{
		$selected = '';
	}
		
	$htmlSeg .= '<option value="'.$segMer[$i]->getCodigo().'" '.$selected.'>'.$segMer[$i]->getNome().'</option>';
}
**/
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
$tpl->set('TIPO_CAD_PESSOA'			,$tipoCadPessoa);
$tpl->set('NOME_TIPO_PESSOA'		,$nomeTipoPessoa);
$tpl->set('COD_PARCEIRO'			,$codParceiro);
$tpl->set('IDENT'					,$ident);
$tpl->set('SEXO'					,$oSexo);
$tpl->set('TIPO'					,$oTipo);
$tpl->set('RAZAO'					,$razao);
$tpl->set('CNPJ'					,$cnpj);
$tpl->set('DISABLED'				,$disabled);
$tpl->set('READONLY'				,$readOnly);
$tpl->set('FANTASIA'				,$nome);
$tpl->set('INSCR_EST'				,$inscEstadual);
$tpl->set('INSCR_MUN'				,$inscMunicipal);
$tpl->set('DATA_INICIO'				,$datInicio);
$tpl->set('NOME'					,$nome);
$tpl->set('CPF'						,$cpf);
$tpl->set('RG'						,$rg);
$tpl->set('NOME'					,$nome);
$tpl->set('DATA_NAS'				,$dataNascimento);
$tpl->set('EMAIL'					,$email);
$tpl->set('LINK'					,$link);
$tpl->set('TIPO_TEL'				,$oTipoTel);
$tpl->set('SEGMENTO'				,$oSegmento);
$tpl->set('TAB_TELEFONE'			,$tabTel);

$tpl->set ( 'COD_LOGRADOURO' , $codLogradouro);
$tpl->set ( 'CEP' 			 , $cep);
$tpl->set ( 'LOGRADOURO'	 , $logradouro);
$tpl->set ( 'BAIRRO'		 , $bairro);
$tpl->set ( 'CIDADE'		 , $cidade);
$tpl->set ( 'ESTADO'		 , $estado);
$tpl->set ( 'COMPLEMENTO' 	 , $complemento);
$tpl->set ( 'NUMERO' 		 , $numero);
$tpl->set ( 'READONLY_BAIRRO', $readOnlyBairro);
$tpl->set ( 'READONLY_END' 	 , $readOnlyEnd);
$tpl->set ( 'IND_END_CORRETO', $endCorreto);

$tpl->set('APP_BS_TA_MINLENGTH'		,\Zage\Adm\Parametro::getValor('APP_BS_TA_MINLENGTH'));
$tpl->set('APP_BS_TA_ITENS'			,\Zage\Adm\Parametro::getValor('APP_BS_TA_ITENS'));
$tpl->set('APP_BS_TA_TIMEOUT'		,\Zage\Adm\Parametro::getValor('APP_BS_TA_TIMEOUT'));
$tpl->set('DP'						,\Zage\App\Util::getCaminhoCorrespondente(__FILE__,\Zage\App\ZWS::EXT_DP,\Zage\App\ZWS::CAMINHO_RELATIVO));
$tpl->set('IC'						,$_icone_);
$tpl->set('COD_MENU'				,$_codMenu_);

#################################################################################
## Por fim exibir a página HTML
#################################################################################
$tpl->show();
