<?php
#################################################################################
## Includes
#################################################################################
if (defined('DOC_ROOT')) {
	include_once(DOC_ROOT . 'include.php');
}else{
	include_once('./include.php');
}

#################################################################################
## Variáveis globais
#################################################################################
global $system,$em,$tr;


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
## Resgata a url desse script
#################################################################################
$url		= ROOT_URL . "/Fin/". basename(__FILE__)."?id=".$id;

#################################################################################
## Resgata a variável FID com a lista de formandos selecionados
#################################################################################
if (isset($_GET['fid']))	$fid = \Zage\App\Util::antiInjection($_GET["fid"]);
if (!isset($fid))			\Zage\App\Erro::halt('Falta de Parâmetros 2');

#################################################################################
## Descompacta o FID
#################################################################################
\Zage\App\Util::descompactaId($fid);

if (!isset($aSelFormandos))			\Zage\App\Erro::halt('Falta de Parâmetros 3');

#################################################################################
## Gera o array de formandos selecionados a partir da string
#################################################################################
$aSelFormandos				= explode(",",$aSelFormandos);

#################################################################################
## Resgatar os dados dos formandos selecionados
#################################################################################
try {
	$formandos				= $em->getRepository('Entidades\ZgsegUsuario')->findBy(array('codigo' => $aSelFormandos));
} catch (\Exception $e) {
	\Zage\App\Erro::halt($e->getMessage());
}

for ($i = 0; $i < sizeof($formandos); $i++) {

	#################################################################################
	## Verificar se esse usuário é formando na organização atual
	#################################################################################
	if (\Zage\Seg\Usuario::ehFormando($system->getCodOrganizacao(), $formandos[$i]->getCodigo()) != true) {
		\Zage\App\Erro::halt('Violação de acesso, 0x3748FF');
	}
	
	#################################################################################
	## Resgata o registro da Pessoa associada ao Formando
	#################################################################################
	$oPessoa			= \Zage\Fin\Pessoa::getPessoaUsuario($system->getCodOrganizacao(),$formandos[$i]->getCodigo());
	if (!$oPessoa) 		die('1'.\Zage\App\Util::encodeUrl('||'.htmlentities('Violação de acesso, 0x871FB, Pessoa não encontrada')));
	
	
	#################################################################################
	## Verifica se o formando já tem contrato
	#################################################################################
	$temContrato 		= $em->getRepository('Entidades\ZgfmtContratoFormando')->findOneBy(array('codOrganizacao' => $system->getCodOrganizacao() , 'codFormando' => $formandos[$i]->getCodigo()));
	
	#################################################################################
	## Verificar se já foi gerada alguma mensalidade para algum formando
	#################################################################################
	$temMensalidade				= \Zage\Fmt\Financeiro::temMensalidadeGerada($system->getCodOrganizacao(), $oPessoa->getCodigo());

	#################################################################################
	## Verificar se esse formando está apto a gerar as mensalidades
	#################################################################################
	if (($temMensalidade) || (!$temContrato))	\Zage\App\Erro::halt('Violação de acesso, 0x3748FE');
	
}


#################################################################################
## Select da Conta de Crédito
#################################################################################
try {

	#################################################################################
	## Verifica se a formatura está sendo administrada por um Cerimonial,
	## para resgatar as contas do cerimonial tb
	#################################################################################
	$oFmtAdm		= \Zage\Fmt\Formatura::getCerimonalAdm($system->getCodOrganizacao());

	if ($oFmtAdm)	{
		$aCntCer	= $em->getRepository('Entidades\ZgfinConta')->findBy(array('codOrganizacao' => $oFmtAdm->getCodigo()),array('nome' => 'ASC'));
	}else{
		$aCntCer	= null;
	}

	$aConta		= $em->getRepository('Entidades\ZgfinConta')->findBy(array('codOrganizacao' => $system->getCodOrganizacao()),array('nome' => 'ASC'));

	if ($aCntCer) {
		$oConta		= "<optgroup label='Contas do Cerimonial'>";
		for ($i = 0; $i < sizeof($aCntCer); $i++) {
			$valBol		= ($aCntCer[$i]->getCodTipo()->getCodigo() == "CC") ? \Zage\Fmt\Financeiro::getValorBoleto($aCntCer[$i]->getCodigo()) : 0;
			$oConta	.= "<option value='".$aCntCer[$i]->getCodigo()."' zg-val-boleto='".$valBol."'>".$aCntCer[$i]->getNome()."</option>";
		}
		$oConta		.= '</optgroup>';
	}

	if ($aConta) {
		$oConta		.= ($aCntCer) ? "<optgroup label='Contas da Formatura'>" : '';
		for ($i = 0; $i < sizeof($aConta); $i++) {
			$valBol		= ($aConta[$i]->getCodTipo()->getCodigo() == "CC") ? \Zage\Fmt\Financeiro::getValorBoleto($aConta[$i]->getCodigo()) : 0;
			$oConta	.= "<option value='".$aConta[$i]->getCodigo()."' zg-val-boleto='".$valBol."'>".$aConta[$i]->getNome()."</option>";
		}
		$oConta		.= ($aCntCer) ? '</optgroup>' : '';
	}


} catch (\Exception $e) {
	\Zage\App\Erro::halt($e->getMessage(),__FILE__,__LINE__);
}

 
#################################################################################
## Urls
#################################################################################
$urlVoltar			= ROOT_URL . "/Fmt/mensalFormandoLis.php?id=".$id;


#################################################################################
## Carregando o template html
#################################################################################
$tpl	= new \Zage\App\Template();
$tpl->load(\Zage\App\Util::getCaminhoCorrespondente(__FILE__, \Zage\App\ZWS::EXT_HTML));

#################################################################################
## Define os valores das variáveis
#################################################################################
$tpl->set('IC'						,$_icone_);
$tpl->set('ID'						,$id);
$tpl->set('FID'						,$fid);
$tpl->set('CONTAS'					,$oConta);
$tpl->set('TITULO'					,"Geração de contas com base no contrato");
$tpl->set('DP_MODAL'				,\Zage\App\Util::getCaminhoCorrespondente(__FILE__,\Zage\App\ZWS::EXT_DP,\Zage\App\ZWS::CAMINHO_RELATIVO));
$tpl->set('URL_VOLTAR'				,$urlVoltar);


//$tpl->set('JSON_CODIGOS'			,json_encode($aCodigos));

#################################################################################
## Por fim exibir a página HTML
#################################################################################
$tpl->show();


//$log->info("GET: ".serialize($_GET));
//$log->info("aSelFormandos: ".serialize($aSelFormandos));
//var_dump($aSelFormandos);

