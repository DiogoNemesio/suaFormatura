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
## Resgata os parâmetros passados pelo formulario
#################################################################################
if (isset($_POST['codEnquete'])) 		$codEnquete		= \Zage\App\Util::antiInjection($_POST['codEnquete']);

#################################################################################
## Limpar a variável de erro
#################################################################################
$err	= false;

#################################################################################
## Excluir pergunta
#################################################################################
try {
	
	/** Validações de exclusão **/
	if (!isset($codEnquete) || (!$codEnquete)) {
		$system->criaAviso(\Zage\App\Aviso\Tipo::ERRO,$tr->trans('PARÂMENTRO NÃO INFORMADO : COD_ENQUETE'));
		die('1'.\Zage\App\Util::encodeUrl('||'));
	}
	
	$oEnquete 	 = $em->getRepository('Entidades\ZgappEnquetePergunta')->findOneBy(array('codOrganizacao' => $system->getCodOrganizacao(), 'codigo' => $codEnquete));
	$oEnqueteRes = $em->getRepository('Entidades\ZgappEnqueteResposta')->findOneBy(array('codPergunta' => $codEnquete));
	
	if (!$oEnquete) {
		die ('1'.\Zage\App\Util::encodeUrl('||'.htmlentities($tr->trans("Pergunta não encontrada!"))));
		$err = 1;
	}elseif ($oEnquete->getDataPrazo() < new \DateTime("now")){
		die ('1'.\Zage\App\Util::encodeUrl('||'.htmlentities($tr->trans("Para garantir a integridade da enquete não é possível excluir uma pergunta que já teve seu prazo expirado."))));
		$err = 1;
	}elseif ($oEnqueteRes){
		die ('1'.\Zage\App\Util::encodeUrl('||'.htmlentities($tr->trans("Para garantir a integridade da enquete não é possível excluir uma pergunta que já possui resposta!"))));
		$err = 1;
	}
	
	/** Excluir valores resposta caso seja do tipo lista **/
	if ($oEnquete->getCodTipo()->getCodigo() == LI){
		$oEnqueteValor = $em->getRepository('Entidades\ZgappEnquetePerguntaValor')->findBy(array('codPergunta' => $codEnquete)); 
		
		if ($oEnqueteValor){
			for ($i = 0; $i < sizeof($oEnqueteValor); $i++) {
				$em->remove($oEnqueteValor[$i]);
			}
		}
	}
	
	/** Excluir pergunta **/
	$em->remove($oEnquete);
	
	
	/** Flush **/
	try {
		$em->flush();
		$em->clear();
	} catch (Exception $e) {
		$log->debug("Erro ao excluir o pergunta (enquete):". $e->getTraceAsString());
		throw new \Exception("Erro excluir a pergunta (enquete). Uma mensagem de depuração foi salva em log, entre em contato com os administradores do sistema !!!");
	}	

} catch (\Exception $e) {
	$system->criaAviso(\Zage\App\Aviso\Tipo::ERRO,$e->getMessage());
	die('1'.\Zage\App\Util::encodeUrl('||'));
	exit;
}

echo '0'.\Zage\App\Util::encodeUrl('||'.htmlentities($tr->trans("Pergunta excluída com sucesso!")));