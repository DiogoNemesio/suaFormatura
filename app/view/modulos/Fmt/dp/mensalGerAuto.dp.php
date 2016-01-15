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
if (isset($_POST['fid']))	$fid = \Zage\App\Util::antiInjection($_POST["fid"]);
if (!isset($fid))			die('1'.\Zage\App\Util::encodeUrl('||'.htmlentities($tr->trans('Falta de parâmetros 2'))));

#################################################################################
## Descompacta o FID
#################################################################################
\Zage\App\Util::descompactaId($fid);

if (!isset($aSelFormandos))			die('1'.\Zage\App\Util::encodeUrl('||'.htmlentities($tr->trans('Falta de parâmetros 3'))));

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


#################################################################################
## Montar o array de retorno de parcelas geradas
#################################################################################
$aContrato		= array();
for ($i = 0; $i < sizeof($formandos); $i++) {

	#################################################################################
	## Verificar se esse usuário é formando na organização atual
	#################################################################################
	if (\Zage\Seg\Usuario::ehFormando($system->getCodOrganizacao(), $formandos[$i]->getCodigo()) != true) {
		die('1'.\Zage\App\Util::encodeUrl('||'.htmlentities('Violação de acesso, 0x3748FF')));
	}
	
	#################################################################################
	## Verificar se já foi gerada alguma mensalidade para algum formando
	#################################################################################
	$temMensalidade				= \Zage\Fmt\Financeiro::temMensalidadeGerada($system->getCodOrganizacao(), $formandos[$i]->getCodigo());
	if ($temMensalidade)		die('1'.\Zage\App\Util::encodeUrl('||'.htmlentities('Violação de acesso, 0x3748FE')));

	#################################################################################
	## Resgatar as informações do contrato
	#################################################################################
	$aContrato[$i]				= $em->getRepository('Entidades\ZgfmtContratoFormando')->findOneBy(array('codOrganizacao' => $system->getCodOrganizacao() , 'codFormando' => $formandos[$i]->getCodigo()));
	if (!$aContrato[$i])		die('1'.\Zage\App\Util::encodeUrl('||'.htmlentities('Violação de acesso, 0x3748FD')));
}


#################################################################################
## Gerar as mensalidades
#################################################################################
$aParcGer		= array();
for ($i = 0; $i < sizeof($formandos); $i++) {

	#################################################################################
	## Resgatar as parcelas do contrato
	#################################################################################
	$valorTotal		= 0;
	$parcelas		= $em->getRepository('Entidades\ZgfmtContratoFormandoParcela')->findBy(array('codContrato' => $aContrato[$i]->getCodigo()));
	$numParcelas	= sizeof($parcelas);
	for ($p = 0; $p < sizeof($parcelas); $p++) {
		$valorParcela	= \Zage\App\Util::to_float($parcelas[$p]->getValor());
		$valorTotal		+= $valorParcela; 
	}
	
	
	#################################################################################
	## Montar o array JSON de retorno
	#################################################################################
	$aParcGer[$formandos[$i]->getCodigo()]["NOME"]			= $formandos[$i]->getNome();
	$aParcGer[$formandos[$i]->getCodigo()]["NUM_PARCELAS"]	= $numParcelas;
	$aParcGer[$formandos[$i]->getCodigo()]["VALOR_TOTAL"]	= $valorTotal;
	$aParcGer[$formandos[$i]->getCodigo()]["FORMA_PAG"]		= $aContrato[$i]->getCodFormaPagamento()->getDescricao();
	
}

echo '0'.\Zage\App\Util::encodeUrl('||'.json_encode($aParcGer));