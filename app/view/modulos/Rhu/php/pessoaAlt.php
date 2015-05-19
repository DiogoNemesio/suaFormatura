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
if ($codPessoa) {
	try {
		$info = $em->getRepository ( 'Entidades\ZgrhuPessoa' )->findOneBy (array ('codigo' => $codPessoa));
	} catch ( \Exception $e ) {
		\Zage\App\Erro::halt ( $e->getMessage () );
	}
	$nome			 = ($info->getnome()) ? $info->getnome() : null;
	$nomeMae		 = ($info->getnomeMae()) ? $info->getnomeMae() : null;
	$nomePai		 = ($info->getnomePai()) ? $info->getnomePai() : null;
	$sexo			 = ($info->getSexo()) ? $info->getSexo()->getCodigo() : null;
	$dataNascimento	 = ($info->getDataNascimento() != null) ? $info->getDataNascimento()->format($system->config["data"]["dateFormat"]) : null;
	$email		 	 = ($info->getemail()) ? $info->getemail() : null;
	$codEstadoCivil	 = ($info->getCodTipoEstadoCivil()) ? $info->getCodTipoEstadoCivil()->getCodigo() : null;
	$codNaturalidade = ($info->getCodNaturalidade()) ? $info->getCodNaturalidade()->getCodigo() : null;
	$codNacionalidade= ($info->getCodNacionalidade()) ? $info->getCodNacionalidade()->getCodigo() : null;
	$codInstrucao	 = ($info->getCodInstrucao()) ? $info->getCodInstrucao()->getCodigo() : null;
	$indEstrangeiro  = ($info->getIndEstrangeiro() == 1) ? "checked" : null;
	/** CPF,Rg **/
	$cpf		     = ($info->getcpf()) ? $info->getcpf() : null;
	$rg				 = ($info->getrg()) ? $info->getrg() : null;
	$rgDataEmissao   = ($info->getrgDataEmissao() != null) ? $info->getrgDataEmissao()->format($system->config["data"]["dateFormat"]) : null;
	$rgUf			 = ($info->getCodUfRg()) ? $info->getCodUfRg()->getCodUf() : null;
	$orgaoExpedidor  = ($info->getRgOrgaoExpedidor()) ? $info->getRgOrgaoExpedidor() : null;
	/** Endereco **/
	$codLogradouro   = ($info->getCodLogradouro()) ? $info->getCodLogradouro()->getCodigo() : null;
	$endereco	 	 = ($info->getEndereco()) ? $info->getEndereco() : null;
	$bairro 	 	 = ($info->getBairro()) ? $info->getBairro() : null;
	$complemento     = ($info->getcomplemento()) ? $info->getcomplemento() : null;
	$numero		     = ($info->getnumero()) ? $info->getnumero() : null;
	/** Carteira Habilitação **/
	$foto			 = ($info->getFoto()) ? $info->getFoto() : null;
	$numCnh			 = ($info->getCnhNumero()) ? $info->getCnhNumero() : null;
	$catHabilitacao  = ($info->getCodCnhCategoria()) ? $info->getCodCnhCategoria()->getCodigo() : null;
	$cnhEmissao      = ($info->getCnhEmissao() != null) ? $info->getCnhEmissao()->format($system->config["data"]["dateFormat"]) : null;
	$cnhVencimento   = ($info->getCnhVencimento() != null) ? $info->getCnhVencimento()->format($system->config["data"]["dateFormat"]) : null;
	/** Titulo eleitor **/
	$titEleitor		 = ($info->getTituloEleitor()) ? $info->getTituloEleitor() : null;
	$titEleitorZona  = ($info->getTituloEleitorZona()) ? $info->getTituloEleitorZona() : null;
	$titEleitorSecao = ($info->getTituloEleitorSecao()) ? $info->getTituloEleitorSecao() : null;
	/** RNE **/
	$rne			 = ($info->getRne()) ? $info->getRne() : null;
	$rneOrgaoEmissor = ($info->getRneOrgaoEmissor()) ? $info->getRneOrgaoEmissor() : null;
	$rneDataEmissao  = ($info->getRneDataEmissao() != null) ? $info->getRneDataEmissao()->format($system->config["data"]["dateFormat"]) : null;
	$indNaturalizado = ($info->getIndNaturalizado() == 1) ? "checked" : null;
	/** Passaporte **/
	$passaporteNro	 = ($info->getPassaporteNro()) ? $info->getPassaporteNro() : null;
	$passPaisOrigem	 = ($info->getPassaportePaisOrigem()) ? $info->getPassaportePaisOrigem()->getCodigo() : null;
	$passDataEmissao = ($info->getPassaporteDataEmissao() != null) ? $info->getPassaporteDataEmissao()->format($system->config["data"]["dateFormat"]) : null;
	$passDataValidade= ($info->getPassaporteDataValidade() != null) ? $info->getPassaporteDataValidade()->format($system->config["data"]["dateFormat"]) : null;
	/** Carteira de trabalho **/
	$cartTrabalho    = ($info->getCarteiraTrabalho()) ? $info->getCarteiraTrabalho() : null;
	$cartTrabalhoSerie  = ($info->getCarteiraTrabalhoSerie()) ? $info->getCarteiraTrabalhoSerie() : null;
	$cartTrabalhoUf  = ($info->getCodCarteiraTrabalhoUf()) ? $info->getCodCarteiraTrabalhoUf()->getCodUf() : null;
	$cartTrabalhoData= ($info->getCarteiraTrabalhoData() != null) ? $info->getCarteiraTrabalhoData()->format($system->config["data"]["dateFormat"]) : null;
	$cartTrabalhoVenc= ($info->getCarteiraTrabalhoVencimento() != null) ? $info->getCarteiraTrabalhoVencimento()->format($system->config["data"]["dateFormat"]) : null;
	$nit			 = ($info->getNit()) ? $info->getNit() : null;
	/** Reservista **/
	$certReservista  = ($info->getCertificadoReservista()) ? $info->getCertificadoReservista() : null;
	$reservistaCat   = ($info->getCodReservistaCategoria()) ? $info->getCodReservistaCategoria()->getCodigo() : null;
	$certReservistaVenc= ($info->getCertificadoReservistaVencimento() != null) ? $info->getCertificadoReservistaVencimento()->format($system->config["data"]["dateFormat"]) : null;
	/** Deficiencia **/
	$indDeficienteF  = ($info->getIndDeficienteFisico()  == 1) ? "checked" : null;
	$indDeficienteA  = ($info->getIndDeficienteAuditivo() == 1) ? "checked" : null;
	$indDeficienteFa = ($info->getIndDeficienteFala() == 1) ? "checked" : null;
	$indDeficienteV  = ($info->getIndDeficienteVisual() == 1) ? "checked" : null;
	$indDeficienteM  = ($info->getIndDeficienteMental() == 1) ? "checked" : null;
	$indDeficienteMob= ($info->getIndDeficienteMobilidade() == 1) ? "checked" : null;
	
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
	
} else {
	$nome			 		= null;
	$nomeMae		 		= null;
	$nomePai				= null;
	$sexo			 		= null;
	$dataNascimento			= null;
	$cpf				 	= null;
	$rg				 		= null;
	$rgDataEmissao			= null;
	$rgUf					= null;
	$orgaoExpedidor  		= null;
	$email		    		= null;
	$codEstadoCivil	 		= null;
	$codNaturalidade	 	= null;
	$codNacionalidade		= null;
	$codInstrucao			= null;
	$indEstrangeiro			= '';
	
	$codLogradouro   		= null;
	$endereco				= null;
	$bairro					= null;
	$complemento   			= null;
	$numero		     		= null;
	
	$foto		     		= null;
	$numCnh		    		= null;
	$catHabilitacao   		= null;
	$cnhEmissao				= null;
	$cnhVencimento			= null;
	
	$cep	 	 	= '';
	$endPadrao 	 	= '';
	$bairroPadrao 	= '';
	$cidade 		= '';
	$estado 		= '';
	$readonly		= 'readonly';
	
	$titEleitor		 = null;
	$titEleitorZona  = null;
	$titEleitorSecao = null;
	
	$rne			 = null;
	$rneOrgaoEmissor = null;
	$rneDataEmissao	 = null;
	$indNaturalizado = null;
	
	$passaporteNro	 = null;
	$passDataEmissao = null;
	$passDataValidade= null;
	$passPaisOrigem  = null;
	
	$cartTrabalho   		 = null;
	$cartTrabalhoSerie 		 = null;
	$cartTrabalhoUf  		 = null;
	$cartTrabalhoData 		 = null;
	$cartTrabalhoVenc		 = null;
	$nit					 = null;
	
	$certReservista   		 = null;
	$reservistaCat   		 = null;
	$certReservistaVenc		 = null;
	$indDeficienteF 		 = null;
	$indDeficienteA 		 = null;
	$indDeficienteFa		 = null;
	$indDeficienteV  		 = null;
	$indDeficienteM 		 = null;
	$indDeficienteMob		 = null;
	//$indDeficiencia          = '';
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
$aTelefones		= $em->getRepository('Entidades\ZgrhuPessoaTelefone')->findBy(array('codPessoa' => $codPessoa));
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
## Select de Sexo Dependente
#################################################################################
try {
	$aSexoD		= $em->getRepository('Entidades\ZgsegSexoTipo')->findAll();
	$oSexoD		= $system->geraHtmlCombo($aSexoD,	'CODIGO', 'DESCRICAO', null, null);
} catch (\Exception $e) {
	\Zage\App\Erro::halt($e->getMessage(),__FILE__,__LINE__);
}

#################################################################################
## Resgatar os dados de contato
#################################################################################
$aDependentes		= $em->getRepository('Entidades\ZgrhuPessoaDependente')->findBy(array('codPessoa' => $codPessoa));
$tabDepen			= "";
for ($i = 0; $i < sizeof($aDependentes); $i++) {

	#################################################################################
	## Monta a combo de Tipo
	#################################################################################
	$indDeficiente  = ($aDependentes[$i]->getIndDeficiente() == 1) ? "checked" : null;
	$sexoDependente	= ($aDependentes[$i]->getSexo()) ? $aDependentes[$i]->getSexo()->getCodigo() : null;
	$oTipoInt		= $system->geraHtmlCombo($aSexoD,	'CODIGO', 'DESCRICAO',	$sexoDependente, '');
	
	$dataNascDep = $aDependentes[$i]->getDataNascimento()->format($system->config["data"]["dateFormat"]);
	
	$tabDepen    	.= '<tr><td class="center" style="width: 20px;"><div class="inline" zg-type="zg-div-msg"></div></td><td><input type="text" class="width-100" name="nomeDependente[]" value="'.$aDependentes[$i]->getNome().'" maxlength="60" autocomplete="off""></td><td><select class="select2" style="width:100%;" name="sexoDependente[]" data-rel="select2">'.$oSexoD.'</select></td><td><input type="text" class="width-100" class="select2" name="dataNascimentoD[]" value="'.$dataNascDep.'" maxlength="15" autocomplete="off" zg-data-toggle="mask" zg-data-mask="data" zg-data-mask-retira="1"></td><td align="center"><label><input name="indDeficiente" id="indDeficienteID" '.$indDeficiente.' class="ace ace-switch ace-switch-6" type="checkbox" /><span class="lbl"></span></label></td><td class="center"><span class="center" zgdelete onclick="delRowDependentePessoaAlt($(this));"><i class="fa fa-trash bigger-150 red"></i></span><input type="hidden" name="codDepedente[]"></td></tr>';
						
}
################################################################################
# Select de Logradouro
################################################################################
try {
	$aLogradouro = $em->getRepository('Entidades\ZgadmLogradouro')->findBy(array(),array('descricao' => 'ASC'));
	$oLogradouro= $system->geraHtmlCombo($aLogradouro, 'CODIGO', 'DESCRICAO', $codLogradouro, '');
} catch (\Exception $e) {
	\Zage\App\Erro::halt($e->getMessage(),__FILE__,__LINE__);
}
################################################################################
# Select de Sexo
################################################################################
try {
	$aSexo = $em->getRepository('Entidades\ZgsegSexoTipo')->findBy(array(),array('descricao' => 'ASC'));
	$oSexo = $system->geraHtmlCombo($aSexo, 'CODIGO', 'DESCRICAO', $sexo, '');
} catch (\Exception $e) {
	\Zage\App\Erro::halt($e->getMessage(),__FILE__,__LINE__);
}
################################################################################
# Select de Tipo Habilitacao
################################################################################
try {
	$aCatHabilitacao = $em->getRepository('Entidades\ZgrhuCnhCategoria')->findBy(array(),array('codigo' => 'ASC'));
	$oCatHabilitacao = $system->geraHtmlCombo($aCatHabilitacao, 'CODIGO', 'CODIGO', $catHabilitacao, '');
} catch (\Exception $e) {
	\Zage\App\Erro::halt($e->getMessage(),__FILE__,__LINE__);
}
################################################################################
# Select de Estado Civil
################################################################################
try {
	$aEstadoCivil = $em->getRepository('Entidades\ZgrhuPessoaTipoEstadoCivil')->findBy(array(),array('descricao' => 'ASC'));
	$oEstadoCivil = $system->geraHtmlCombo($aEstadoCivil, 'CODIGO', 'DESCRICAO', $codEstadoCivil, '');
} catch (\Exception $e) {
	\Zage\App\Erro::halt($e->getMessage(),__FILE__,__LINE__);
}
################################################################################
# Select de Tipo Instrucao
################################################################################
try {
	$aInstrucao = $em->getRepository('Entidades\ZgrhuPessoaInstrucaoTipo')->findBy(array(),array('descricao' => 'ASC'));
	$oInstrucao = $system->geraHtmlCombo($aInstrucao, 'CODIGO', 'DESCRICAO', $codInstrucao, '');
} catch (\Exception $e) {
	\Zage\App\Erro::halt($e->getMessage(),__FILE__,__LINE__);
}
################################################################################
# Select de Naturalidade
################################################################################
try {
	$aNaturalidade = $em->getRepository('Entidades\ZgadmCidade')->findBy(array(),array('codigo' => 'ASC'));
	$oNaturalidade = $system->geraHtmlCombo($aNaturalidade, 'CODIGO', 'NOME', $codNaturalidade, '');
} catch (\Exception $e) {
	\Zage\App\Erro::halt($e->getMessage(),__FILE__,__LINE__);
}
################################################################################
# Select de reservista
################################################################################
try {
	$aReservistaCat = $em->getRepository('Entidades\ZgrhuReservistaCategoria')->findBy(array(),array('codigo' => 'ASC'));
	$oReservistaCat = $system->geraHtmlCombo($aReservistaCat, 'CODIGO', 'DESCRICAO', $reservistaCat, '');
} catch (\Exception $e) {
	\Zage\App\Erro::halt($e->getMessage(),__FILE__,__LINE__);
}
################################################################################
# Select de Carteira de trabalho Estado
################################################################################
try {
	$aCartTrabalhoUf = $em->getRepository('Entidades\ZgadmEstado')->findBy(array(),array('codUf' => 'ASC'));
	$oCartTrabalhoUf = $system->geraHtmlCombo($aCartTrabalhoUf, 'COD_UF', 'NOME', $cartTrabalhoUf, '');
} catch (\Exception $e) {
	\Zage\App\Erro::halt($e->getMessage(),__FILE__,__LINE__);
}
################################################################################
# Select UF RG
################################################################################
try {
	$aRgUf = $em->getRepository('Entidades\ZgadmEstado')->findBy(array(),array('codUf' => 'ASC'));
	$oRgUf = $system->geraHtmlCombo($aRgUf, 'COD_UF', 'NOME', $rgUf, '');
} catch (\Exception $e) {
	\Zage\App\Erro::halt($e->getMessage(),__FILE__,__LINE__);
}
################################################################################
# Url Voltar
################################################################################
$urlVoltar = ROOT_URL . "/Rhu/pessoaLis.php?id=" . $id;

################################################################################
# Url Novo
################################################################################
$uid = \Zage\App\Util::encodeUrl ( '_codMenu_=' . $_codMenu_ . '&_icone_=' . $_icone_ . '&codPessoa=' );
$urlNovo = ROOT_URL . "/Rhu/pessoaAlt.php?id=" . $uid;

################################################################################
# Carregando o template html
################################################################################
$tpl = new \Zage\App\Template ();
$tpl->load ( \Zage\App\Util::getCaminhoCorrespondente ( __FILE__, \Zage\App\ZWS::EXT_HTML ) );

################################################################################
# Define os valores das variáveis
################################################################################
$tpl->set ( 'URL_FORM'		 , $_SERVER ['SCRIPT_NAME'] );
$tpl->set ( 'URLVOLTAR'		 , $urlVoltar );
$tpl->set ( 'URLNOVO'		 , $urlNovo );
$tpl->set ( 'ID'			 , $id );
$tpl->set ( 'COD_PESSOA'	 , $codPessoa);
$tpl->set ( 'NOME'			 , $nome);
$tpl->set ( 'NOME_MAE'		 , $nomeMae);
$tpl->set ( 'NOME_PAI'		 , $nomePai);
$tpl->set ( 'SEXO'		 	 , $oSexo);
$tpl->set ( 'EMAIL'		 	 , $email);
$tpl->set ( 'COD_ESTADO_CIVIL', $oEstadoCivil);
$tpl->set ( 'COD_NATURALIDADE', $codNaturalidade);
$tpl->set ( 'COD_NACIONALIDADE', $codNacionalidade);
$tpl->set ( 'COD_INSTRUCAO'	 , $oInstrucao);
$tpl->set ( 'IND_ESTRANGEIRO', $indEstrangeiro);

$tpl->set ( 'DATA_NASCIMENTO', $dataNascimento);
$tpl->set ( 'CPF'		 	 , $cpf);
$tpl->set ( 'RG'	 		 , $rg);
$tpl->set ( 'RG_DATA_EMISSAO', $rgDataEmissao);
$tpl->set ( 'COD_RG_UF'	 	 , $oRgUf);
$tpl->set ( 'ORGAO_EXPEDIDOR', $orgaoExpedidor);
$tpl->set ( 'COD_LOGRADOURO' , $codLogradouro);
$tpl->set ( 'LOGRADOURO'	 , $endPadrao);
$tpl->set ( 'BAIRRO'		 , $bairroPadrao);
$tpl->set ( 'ENDERECO' 		 , $endereco);
$tpl->set ( 'CIDADE'		 , $cidade);
$tpl->set ( 'ESTADO'		 , $estado);
$tpl->set ( 'COMPLEMENTO' 	 , $complemento);
$tpl->set ( 'NUMERO' 		 , $numero);
$tpl->set ( 'CEP'			 , $cep);
$tpl->set ( 'READONLY'		 , $readonly);
$tpl->set ( 'TAB_TELEFONE'	 , $tabTel);
$tpl->set ( 'TIPO_TEL'		 , $oTipoTel);
$tpl->set ( 'TAB_DEPENDENTE' , $tabDepen);
$tpl->set ( 'SEXO_DEPEN'	 , $oSexoD);
$tpl->set ( 'FOTO'		 	 , $foto);
$tpl->set ( 'NUM_HABILITACAO', $numCnh);
$tpl->set ( 'TIP_HABILITACAO', $oCatHabilitacao);
$tpl->set ( 'CNH_EMISSAO'	 , $cnhEmissao);
$tpl->set ( 'CNH_VENCIMENTO' , $cnhVencimento);

$tpl->set ( 'TIT_ELEITOR'		 , $titEleitor);
$tpl->set ( 'TIT_ELEITOR_ZONA'	 , $titEleitorZona);
$tpl->set ( 'TIT_ELEITOR_SECAO'	 , $titEleitorSecao);
$tpl->set ( 'RNE'				 , $rne);
$tpl->set ( 'RNE_ORGAO_EMISSOR'	 , $rneOrgaoEmissor);
$tpl->set ( 'RNE_DATA_EMISSAO'	 , $rneDataEmissao);
$tpl->set ( 'IND_NATURALIZADO'	 , $indNaturalizado);
$tpl->set ( 'PASSAPORTE_NRO'	 , $passaporteNro);
$tpl->set ( 'PASS_PAIS_ORIGEM'	 , $passPaisOrigem);
$tpl->set ( 'PASS_DATA_EMISSAO'	 , $passDataEmissao);
$tpl->set ( 'PASS_DATA_VALIDADE' , $passDataValidade);
$tpl->set ( 'CART_TRABALHO'		 , $cartTrabalho);
$tpl->set ( 'CART_TRABALHO_SERIE', $cartTrabalhoSerie);
$tpl->set ( 'CART_TRABALHO_UF'	 , $oCartTrabalhoUf);
$tpl->set ( 'CART_TRABALHO_DATA' , $cartTrabalhoData);
$tpl->set ( 'CART_TRABALHO_VENC' , $cartTrabalhoVenc);
$tpl->set ( 'NIT' 				 , $nit);
$tpl->set ( 'CART_RESERVISTA' 	 , $certReservista);
$tpl->set ( 'RESERVISTA_CAT' 	 , $oReservistaCat);
$tpl->set ( 'RESERVISTA_VENC' 	 , $certReservistaVenc);
//$tpl->set ( 'IND_DEFICIENCIA' 	 , $indDeficiencia);
$tpl->set ( 'IND_DEFICIENTE_F' 	 , $indDeficienteF);
$tpl->set ( 'IND_DEFICIENTE_A' 	 , $indDeficienteA);
$tpl->set ( 'IND_DEFICIENTE_FA'  , $indDeficienteFa);
$tpl->set ( 'IND_DEFICIENTE_V'   , $indDeficienteV);
$tpl->set ( 'IND_DEFICIENTE_M'   , $indDeficienteM);
$tpl->set ( 'IND_DEFICIENTE_MOB' , $indDeficienteMob);

$tpl->set ( 'DP', \Zage\App\Util::getCaminhoCorrespondente ( __FILE__, \Zage\App\ZWS::EXT_DP, \Zage\App\ZWS::CAMINHO_RELATIVO ) );

################################################################################
# Por fim exibir a página HTML
################################################################################
$tpl->show ();

