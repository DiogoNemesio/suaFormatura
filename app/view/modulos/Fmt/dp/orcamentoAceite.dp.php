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
global $em,$system,$tr,$log;

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
## Resgata as variáveis postadas
#################################################################################
if (isset($_POST['codVersaoOrc'])) 		$codVersaoOrc			= \Zage\App\Util::antiInjection($_POST['codVersaoOrc']);

#################################################################################
## Verificar parâmetro obrigatório
#################################################################################
if (!isset($codVersaoOrc)) \Zage\App\Erro::halt('Falta de Parâmetros 1');

#################################################################################
## Validações, verificar se a versão do orcamento existe
#################################################################################
$orcamento			= $em->getRepository('Entidades\ZgfmtOrcamento')->findOneBy(array('codigo' => $codVersaoOrc));

if (!$orcamento) {
	die ('1'.\Zage\App\Util::encodeUrl('||'.htmlentities($tr->trans("Orçamento não encontrado!"))));
}

#################################################################################
## Buscar as configurações da formatura, onde será gravado os valores de previsão
#################################################################################
$oFmt				= $em->getRepository('Entidades\ZgfmtOrganizacaoFormatura')->findOneBy(array('codOrganizacao' => $system->getCodOrganizacao()));

if (!$oFmt) {
	die ('1'.\Zage\App\Util::encodeUrl('||'.htmlentities($tr->trans("Organização não é uma formatura, ou não está configurada!"))));
}

#################################################################################
## Calcula o valor total do orcamento
#################################################################################
$valorOrcamento		= \Zage\App\Util::to_float(\Zage\Fmt\Orcamento::calculaValorTotal($codVersaoOrc));
$valorSistema		= \Zage\App\Util::to_float($orcamento->getTaxaSistema()) * ((int) $orcamento->getQtdeFormandos()) * ( (int) $orcamento->getNumMeses());
$valorTotal			= \Zage\App\Util::to_float($valorOrcamento + $valorSistema);

#################################################################################
## Salvar no banco
#################################################################################
try {
	$oUser			= $em->getRepository('Entidades\ZgsegUsuario')->findOneBy(array('codigo' => $system->getCodUsuario()));
	
	#################################################################################
	## Retirar o aceite dos outros orcamentos
	#################################################################################
	$orcs			= $em->getRepository('Entidades\ZgfmtOrcamento')->findBy(array('codOrganizacao' => $system->getCodorganizacao(),'indAceite' => 1));
	for ($i = 0; $i < sizeof($orcs); $i++) {
		$orcs[$i]->setIndAceite(0);
		$em->persist($orcs[$i]);
	}
	
	#################################################################################
	## Atualiza o orcamento
	#################################################################################
	$orcamento->setIndAceite(1);
	$orcamento->setCodUsuarioAceite($oUser);
	$orcamento->setDataAceite(new \DateTime("now"));
 	$em->persist($orcamento);
 	
 	#################################################################################
 	## Transpor os valores do orçamento para a previsão orcamentária da formatura
 	#################################################################################
 	$oFmt->setValorPrevistoTotal($valorTotal);
 	$oFmt->setQtdePrevistaFormandos($orcamento->getQtdeFormandos());
 	$oFmt->setQtdePrevistaConvidados($orcamento->getQtdeConvidados());
 	$oFmt->setDataConclusao($orcamento->getDataConclusao());
 	$em->persist($oFmt);
 	
 	#################################################################################
 	## Salvar o histórico de aceite
 	#################################################################################
 	$oHisAceite		= new \Entidades\ZgfmtOrcamentoHistoricoAceite();
 	
 	$oHisAceite->setCodOrcamento($orcamento);
 	$oHisAceite->setCodUsuario($oUser);
 	$oHisAceite->setValorTotal($valorTotal);
 	$oHisAceite->setDataCadastro(new \DateTime("now"));
 	$em->persist($oHisAceite);
 	
	#################################################################################
 	## Salvar as informações
 	#################################################################################
	$em->flush();
	$em->clear();
 	
} catch (\Exception $e) {
 	$log->err("Erro ao salvar o Orçamento:". $e->getTraceAsString());
 	//throw new \Exception("Erro ao salvar o Orçamento. Uma mensagem de depuração foi salva em log, entre em contato com os administradores do sistema !!!");
	echo '1'.\Zage\App\Util::encodeUrl('||'.htmlentities($e->getMessage()));
 	exit;
}
 
echo '0'.\Zage\App\Util::encodeUrl('|'.$orcamento->getCodigo());