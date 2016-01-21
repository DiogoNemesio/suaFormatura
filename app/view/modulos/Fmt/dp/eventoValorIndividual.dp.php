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
if (isset($_POST['valor']))				$aValor			= $_POST['valor'];
if (isset($_POST['pct']))				$aPct			= $_POST['pct'];
if (isset($_POST['codTipoPreco']))		$codTipoPreco	= $_POST['codTipoPreco'];
if (isset($_POST['codEvento']))			$codEvento		= $_POST['codEvento'];

if (!isset($aValor))					$aValor			= array();
if (!isset($codTipoPreco))				$codTipoPreco	= array();
if (!isset($aPct))						$aPct			= array();
if (!isset($codEvento))					$codEvento		= array();

#################################################################################
## Limpar a variável de erro
#################################################################################
$err	= false;

#################################################################################
## Fazer validação dos campos
#################################################################################


if ($err != null) {
	echo '1'.\Zage\App\Util::encodeUrl('||'.htmlentities($err));
	exit;
}

#################################################################################
## Salvar no banco
#################################################################################
try {
	
	for ($i = 0; $i < sizeof($codEvento); $i++) {
		
		//Resgatar objeto
		$oEvento = $em->getRepository('Entidades\ZgfmtEvento')->findOneBy(array('codigo' => $codEvento[$i]));
		
		if ($codTipoPreco[$i] == 'V'){
			if ($aValor[$i]){
				$valor	= \Zage\App\Util::to_float($aValor[$i]);
			}else{
				$valor = 0;
			}
			$pct = 0;
		}elseif ($codTipoPreco[$i] == 'P'){
			if ($aPct[$i]){
				$pct		= \Zage\App\Util::to_float(str_replace("%", "", $aPct[$i]));
			}else{
				$pct = 0;
			}
			$valor = 0;
		}else{
			$system->criaAviso(\Zage\App\Aviso\Tipo::ERRO,$tr->trans("Ops! Não conseguimos identificar a forma de calculo para o valor. Tente novamente em instantes e caso o problema continue entre em contato com o nosso suporte."));
			exit;
		}
		
		//Setar valores
		$oEventoTipoPreco = $em->getRepository('Entidades\ZgfmtEventoPrecoTipo')->findOneBy(array('codigo' => $codTipoPreco[$i]));
		
		$oEvento->setCodTipoPreco($oEventoTipoPreco);
		$oEvento->setValorAvulso($valor);
		$oEvento->setPctValorOrcamento($pct);
		
		$em->persist($oEvento);
	}
	
	/*********************** 
	 * Commit (salvar)
	 ***********************/
	try {
		$em->flush();
		$em->clear();
	} catch (Exception $e) {
		$log->debug("Erro ao salvar o usuário:". $e->getTraceAsString());
		throw new \Exception("Ops!! Não conseguimos realizar a operação. Tente novamente em instantes e caso o problema continue entre em contato com o nosso suporte.");
	}

	
} catch (\Exception $e) {
	$system->criaAviso(\Zage\App\Aviso\Tipo::ERRO,$e->getMessage());
	echo '1'.\Zage\App\Util::encodeUrl('||'.htmlentities($e->getMessage()));
	exit;
}

$system->criaAviso(\Zage\App\Aviso\Tipo::INFO,$tr->trans("Formando salvo com sucesso."));
echo '0'.\Zage\App\Util::encodeUrl('|'.$oUsuario->_getCodigo());
