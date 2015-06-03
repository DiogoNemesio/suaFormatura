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
if ( (isset($codPessoa) && ($codPessoa)) || ((isset($loadCgc) && ($loadCgc))) ) {
	
	try {
		
		if (isset($loadCgc) && !empty($loadCgc)) {
			$info 		= $em->getRepository('Entidades\ZgfinPessoa')->findOneBy(array('codOrganizacao' => $system->getCodOrganizacao(), 'cgc' => $loadCgc,));
			$codPessoa	= $info->getCodigo();
		}else{
			$info 		= $em->getRepository('Entidades\ZgfinPessoa')->findOneBy(array('codOrganizacao' => $system->getCodOrganizacao(), 'codigo' => $codPessoa));
		}
		
	//	\Doctrine\Common\Util\Debug::dump($info);
	} catch (\Exception $e) {
		\Zage\App\Erro::halt($e->getMessage());
	}
	
	$tipo			= $info->getCodTipoPessoa()->getCodigo();

	
	
	$ativo			= ($info->getIndAtivo()			== 1) ? "checked" : null;
	$indEst			= ($info->getIndEstrangeiro()	== 1) ? "checked" : null;
	$email			= $info->getEmail();
	$link			= $info->getLink();
	$disabled		= 'disabled';
	$readOnly		= 'readonly';
	
	/** Fonte de Recurso (Conta) **/
	$oConta			= $em->getRepository('Entidades\ZgfinPessoaConta')->findOneBy(array('codPessoa' => $codPessoa));
	
	if ($oConta) {
		$codBanco	= ($oConta->getCodBanco() != null) ? $oConta->getCodBanco()->getCodigo() : null;
		$banco		= ($oConta->getCodBanco() != null) ? $oConta->getCodBanco()->getCodBanco() . " / ".$oConta->getCodBanco()->getNome(): null;
		$agencia	= $oConta->getAgencia();
		$ccorrente	= $oConta->getCcorrente();
	}else{
		$codBanco	= null;
		$banco		= null;
		$agencia	= null;
		$ccorrente	= null;
	}
	
	
	if ($tipo == 'J') {

		$razao			= $info->getNome();
		$cnpj			= $info->getCgc();
		$fantasia		= $info->getFantasia();
		$inscEstadual	= $info->getInscEstadual();
		$inscMunicipal	= $info->getInscMunicipal();
		$datInicio 		= ($info->getDataNascimento() != null) ? $info->getDataNascimento()->format($system->config["data"]["dateFormat"]) : null;	
		$nome			= '';
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
		
}else{
	
	$tipo			= 'J';
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
	$datInicio 		= '';
	$email			= '';
	$link			= '';
	$disabled		= '';
	$ativo			= "checked";
	$indEst			= '';
	$readOnly		= '';

	/** Fonte de Recurso (Conta) **/
	$codBanco	= null;
	$banco		= null;
	$agencia	= null;
	$ccorrente	= null;
}


if (!isset($tipoCadPessoa) || ($tipoCadPessoa == "C")) {
	$nomeTipoPessoa	= "Clientes";
}elseif ($tipoCadPessoa 	== "F") {
	$nomeTipoPessoa	= "Fornecedores";
}elseif ($tipoCadPessoa	== "T") {
	$nomeTipoPessoa	= "Transportadoras";
}


#################################################################################
## Url Voltar
#################################################################################
$urlVoltar			= ROOT_URL."/Fin/pessoaLis.php?id=".$id;

#################################################################################
## Url Novo
#################################################################################
$uid = \Zage\App\Util::encodeUrl('_codMenu_='.$_codMenu_.'&_icone_='.$_icone_.'&codPessoa=&tipoCadPessoa='.$tipoCadPessoa);
$urlNovo			= ROOT_URL."/Fin/pessoaAlt.php?id=".$uid;

#################################################################################
## Resgata as informações do banco
#################################################################################
try {
	$aTipo	= $em->getRepository('Entidades\ZgfmtOrganizacaoPessoaTipo')->findAll();
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
## Select de Tipo de Telefone
#################################################################################
try {
	$aTipoTel		= $em->getRepository('Entidades\ZgappTelefoneTipo')->findAll();
	$oTipoTel		= $system->geraHtmlCombo($aTipoTel,	'CODIGO', 'DESCRICAO',	null, 		null);
} catch (\Exception $e) {
	\Zage\App\Erro::halt($e->getMessage(),__FILE__,__LINE__);
}


#################################################################################
## Select de Tipo de Endereço
#################################################################################
try {
	$aTipoEnd		= $em->getRepository('Entidades\ZgfinEnderecoTipo')->findAll();
	$oTipoEnd		= $system->geraHtmlCombo($aTipoEnd,	'CODIGO', 'DESCRICAO',	null, 		null);
} catch (\Exception $e) {
	\Zage\App\Erro::halt($e->getMessage(),__FILE__,__LINE__);
}


#################################################################################
## Resgatar os dados de contato
#################################################################################
$aTelefones		= $em->getRepository('Entidades\ZgfinPessoaTelefone')->findBy(array('codPessoa' => $codPessoa));
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
## Resgatar os dados de endereço
#################################################################################
$aEnd		= $em->getRepository('Entidades\ZgfinPessoaEndereco')->findBy(array('codPessoa' => $codPessoa));
$tabEnd		= "";
for ($i = 0; $i < sizeof($aEnd); $i++) {


	#################################################################################
	## Monta a combo de Tipo
	#################################################################################
	$codTipoEnd		= ($aEnd[$i]->getCodTipoEndereco() 	!= null) 	? $aEnd[$i]->getCodTipoEndereco()->getCodigo() 	: null;
	$oTipoEnd		= $system->geraHtmlCombo($aTipoEnd,	'CODIGO', 'DESCRICAO',	$codTipoEnd, '');
	
	$codCidade		= ($aEnd[$i]->getCodCidade()		!= null) 	? $aEnd[$i]->getCodCidade()->getCodigo() 		: null;
	$descCidade		= ($aEnd[$i]->getCodCidade()		!= null) 	? $aEnd[$i]->getCodCidade()->getCodUf()->getCodUf() . ' / '.$aEnd[$i]->getCodCidade()->getNome() : null;
	
	$codLogradouro	= ($aEnd[$i]->getCodLogradouro()	!= null) 	? $aEnd[$i]->getCodLogradouro()->getCodigo() 	: null;

	//$oCidade	= "<option value='".$codCidade."' selected>".$descCidade."</option>";

	$tabEnd		.= '<tr><td class="center" style="width: 20px;"><div class="inline" zg-type="zg-div-msg"></div></td><td><select class="select2" style="width: 120px;" name="codTipoEnd[]" data-rel="select2">'.$oTipoEnd.'</select></td><td><input type="text" name="cep[]" value="'.$aEnd[$i]->getCep().'" style="width: 90px;" onchange="pessoaAltCarregaEndereco($(this));" maxlength="9" autocomplete="off" zg-data-toggle="mask" zg-data-mask="cep" zg-data-mask-retira="1"></td><td><input type="text" name="bairro[]" value="'.$aEnd[$i]->getBairro().'" style="width: 120px;" maxlength="60" autocomplete="off"></td><td><input type="text" name="endereco[]" value="'.$aEnd[$i]->getEndereco().'" maxlength="100" autocomplete="off"></td><td><input type="text" name="numero[]" value="'.$aEnd[$i]->getNumero().'" style="width: 80px;" maxlength="20" autocomplete="off"></td><td><input type="text" name="complemento[]" value="'.$aEnd[$i]->getComplemento().'" maxlength="120" autocomplete="off"></td><td><input type="hidden" name="codCidade[]" value="'.$codCidade.'" style="width:180px;" class="select2Cidade" data-rel="select2Cidade"></td><td class="center"><span class="center" zgdelete onclick="delRowEnderecoPessoaAlt($(this));"><i class="fa fa-trash bigger-150 red"></i></span><input type="hidden" name="codEndereco[]" value="'.$aEnd[$i]->getCodigo().'"><input type="hidden" name="codLogradouro[]" value="'.$codLogradouro.'"></td></tr>';
}

#################################################################################
## Resgatar os dados de conta
#################################################################################
$aContas		= $em->getRepository('Entidades\ZgfinPessoaConta')->findBy(array('codPessoa' => $codPessoa));
$tabConta		= "";
for ($i = 0; $i < sizeof($aContas); $i++) {

	$codBanco	= ($aContas[$i]->getCodBanco() != null) ? $aContas[$i]->getCodBanco()->getCodigo() : null;
	$banco		= ($aContas[$i]->getCodBanco() != null) ? $aContas[$i]->getCodBanco()->getCodBanco() . " / ".$aContas[$i]->getCodBanco()->getNome(): null;
	
	$tabConta		.= '<tr><td class="center" style="width: 20px;"><div class="inline" zg-type="zg-div-msg"></div></td><td><input type="hidden" name="codBanco[]" value="'.$codBanco.'" style="width:400px;" data-rel="select2Banco"></td><td><input class="form-control" type="text" name="agencia[]" value="'.$aContas[$i]->getAgencia().'" placeholder="Agência"  maxlength="8" autocomplete="off"></td><td><input class="form-control" type="text" name="ccorrente[]" value="'.$aContas[$i]->getCcorrente().'" placeholder="Conta Corrente" maxlength="20" autocomplete="off"/></td><td class="center"><span class="center" zgdelete onclick="delRowContaPessoaAlt($(this));"><i class="fa fa-trash bigger-150 red"></i></span><input type="hidden" name="codConta[]" value="'.$aContas[$i]->getCodigo().'"></td></tr>';
}


#################################################################################
## Lista de segmentos de mercado
#################################################################################
$segAss		= \Zage\Fin\Pessoa::listaSegmentos($codPessoa);
$segDis		= \Zage\Fin\Pessoa::listaSegmentosNaoAssociados($codPessoa);

$liAss			= "";
$liDis			= "";

for ($i = 0; $i < sizeof($segAss); $i++) {
	$classe		= "fa fa-building-o";
	$liAss		.= '<li id="zgId_"'.$segAss[$i]->getCodSegmento()->getCodigo().'" class="ui-state-default" zg-data-id="'.$segAss[$i]->getCodSegmento()->getCodigo().'"><i class="ace-icon bigger-120 green '.$classe.'"></i>&nbsp;'.$segAss[$i]->getCodSegmento()->getDescricao().'</li>';
}
for ($i = 0; $i < sizeof($segDis); $i++) {
	$classe		= "fa fa-building-o";
	$liDis		.= '<li id="zgDis_"'.$segDis[$i]->getCodigo().'" class="ui-state-default" zg-data-id="'.$segDis[$i]->getCodigo().'"><i class="ace-icon bigger-120 green '.$classe.'"></i>&nbsp;'.$segDis[$i]->getDescricao().'</li>';
}



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
$tpl->set('COD_PESSOA'				,$codPessoa);
$tpl->set('ATIVO'					,$ativo);
$tpl->set('IND_ESTRANGEIRO'			,$indEst);
$tpl->set('NOME'					,$nome);
$tpl->set('SEXO'					,$oSexo);
$tpl->set('TIPO'					,$oTipo);
$tpl->set('RAZAO'					,$razao);
$tpl->set('CNPJ'					,$cnpj);
$tpl->set('DISABLED'				,$disabled);
$tpl->set('READONLY'				,$readOnly);
$tpl->set('FANTASIA'				,$fantasia);
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
$tpl->set('TIPO_END'				,$oTipoEnd);
$tpl->set('LISTA_ASS'				,$liAss);
$tpl->set('LISTA_DIS'				,$liDis);
$tpl->set('COD_BANCO'				,$codBanco);
$tpl->set('BANCO'					,$banco);
$tpl->set('AGENCIA'					,$agencia);
$tpl->set('CCORRENTE'				,$ccorrente);
$tpl->set('TAB_TELEFONE'			,$tabTel);
$tpl->set('TAB_CONTA'				,$tabConta);
$tpl->set('TAB_ENDERECO'			,$tabEnd);

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
