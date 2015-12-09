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
## Variáveis globais
#################################################################################
global $system,$em,$tr,$log;

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
## Verificar parâmetro obrigatório
#################################################################################
if (!isset($codFormando)) \Zage\App\Erro::halt('Falta de Parâmetros 2');

#################################################################################
## Verificar se o usuário existe
#################################################################################
$oUsuario	= $em->getRepository('Entidades\ZgsegUsuario')->findOneBy(array('codigo' => $codFormando));
if (!$oUsuario) \Zage\App\Erro::halt('Formando não existe');

#################################################################################
## Resgatar o status da associação com a Formatura
#################################################################################
$oStatus	= $em->getRepository('Entidades\ZgsegUsuarioOrganizacao')->findOneBy(array('codUsuario' => $codFormando,'codOrganizacao' => $system->getCodOrganizacao()));
$codStatus	= ($oStatus->getCodStatus()) ? $oStatus->getCodStatus()->getCodigo() : null;
$codPerfil	= ($oStatus->getCodPerfil()) ? $oStatus->getCodPerfil(): null;
if (!$codPerfil) \Zage\App\Erro::halt('Perfil inválido para o Formando');

#################################################################################
## Verificar o status da associação a Formatura, para definir se poderá ou não
## Gerar mensalidade para o Formando
#################################################################################
switch ($codStatus) {
	case "A":
	case "P":
	case "B":
		$podeDesistir	= true;
		break;
	default:
		$podeDesistir	= false;
		break;
}

if (!$podeDesistir)	\Zage\App\Erro::halt('Tentativa indevida de desistência: 0x6a31');

#################################################################################
## Verificar se o usuário tem perfil de formando nessa organização
#################################################################################
if ($codPerfil->getCodTipoUsuario()->getCodigo() != "F") {
	\Zage\App\Erro::halt('Esse usuário não é um formando');
}

#################################################################################
## Resgatar os valores ja pago por esse formando
#################################################################################
$aPago				= \Zage\Fmt\Financeiro::getValorPagoFormando($system->getCodOrganizacao(),$oUsuario->getCpf());
$aProvisionado		= \Zage\Fmt\Financeiro::getValorProvisionadoUnicoFormando($system->getCodOrganizacao(),$oUsuario->getCpf());
$valPagoMensalidade	= \Zage\App\Util::to_float($aPago["mensalidade"]);
$valProvMensalidade	= \Zage\App\Util::to_float($aProvisionado["mensalidade"]);
$valPagoSistema		= \Zage\App\Util::to_float($aPago["sistema"]);
$valProvSistema		= \Zage\App\Util::to_float($aProvisionado["sistema"]);

$saldoCancelar		= round($valProvMensalidade - $valPagoMensalidade,2);
$saldoSistema		= round($valProvSistema - $valPagoSistema,2);

#################################################################################
## Resgatar os eventos
#################################################################################
$eventos		= $em->getRepository('Entidades\ZgfmtEvento')->findBy(array('codFormatura' => $system->getCodOrganizacao()));

if ($eventos){
	$htmlEventos 	= '';
	for ($i = 0; $i < sizeof($eventos); $i++){
		$htmlEventos .= '<div class="checkbox">';
		$htmlEventos .= '<label class="block">';
		$htmlEventos .= '<input name="form-field-checkbox" type="checkbox" class="ace input-lg" />';
		$htmlEventos .= '<span class="lbl bigger-120"> '.$eventos[$i]->getCodTipoEvento()->getDescricao().'</span>';
		$htmlEventos .= '</label>';
		$htmlEventos .= '</div>';	
	}  
}else{
	$htmlEventos 	= 'Nenhum evento';
}



$log->info("Valor ja pago de Mensalidade do Formando: ".$valPagoMensalidade);
$log->info("Valor ja pago de Sistema do Formando: ".$valPagoSistema);
$log->info("Saldo a Cancelar: ".$saldoCancelar);
$log->info("Saldo de Sistema: ".$saldoSistema);

#################################################################################
## Gerenciar as URls
#################################################################################
if (!isset($urlVoltar) || (!$urlVoltar)) {
	$urlVoltar			= ROOT_URL . "/Fin/contaReceberLis.php?id=".$id;
	//$urlVoltar			= ROOT_URL . "/Fin/contaReceberRecLis.php?id=".$id;
}else{
	$urlVoltar			= $urlVoltar . "&id=".$id;
}




#################################################################################
## Carregando o template html
#################################################################################
$tpl	= new \Zage\App\Template();
$tpl->load(\Zage\App\Util::getCaminhoCorrespondente(__FILE__, \Zage\App\ZWS::EXT_HTML));

#################################################################################
## Define os valores das variáveis
#################################################################################
$tpl->set('ID'					,$id);
$tpl->set('TITULO'				,'Desistência');
$tpl->set('HTML_EVENTOS'		,$htmlEventos);



$tpl->set('URL_VOLTAR'		,$urlVoltar);
$tpl->set('DP'				,\Zage\App\Util::getCaminhoCorrespondente(__FILE__,\Zage\App\ZWS::EXT_DP,\Zage\App\ZWS::CAMINHO_RELATIVO));


#################################################################################
## Por fim exibir a página HTML
#################################################################################
$tpl->show();