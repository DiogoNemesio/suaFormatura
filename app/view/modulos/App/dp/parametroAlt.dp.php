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
if (isset($_POST['codParametro'])) 		$codParametro	= \Zage\App\Util::antiInjection($_POST['codParametro']);
if (isset($_POST['modulo']))	 		$codModulo		= \Zage\App\Util::antiInjection($_POST['modulo']);
if (isset($_POST['parametro'])) 		$parametro		= \Zage\App\Util::antiInjection($_POST['parametro']);
if (isset($_POST['descricao']))			$descricao		= \Zage\App\Util::antiInjection($_POST['descricao']);
if (isset($_POST['secao']))				$secao			= \Zage\App\Util::antiInjection($_POST['secao']);
if (isset($_POST['tipo'])) 				$tipo			= \Zage\App\Util::antiInjection($_POST['tipo']);
if (isset($_POST['uso'])) 				$uso			= \Zage\App\Util::antiInjection($_POST['uso']);
if (isset($_POST['valores'])) 			$valores		= \Zage\App\Util::antiInjection($_POST['valores']);
if (isset($_POST['valorPadrao'])) 		$valorPadrao	= \Zage\App\Util::antiInjection($_POST['valorPadrao']);
if (isset($_POST['obrigatorio'])) 		$obrigatorio	= \Zage\App\Util::antiInjection($_POST['obrigatorio']);
if (isset($_POST['tamanho'])) 			$tamanho		= \Zage\App\Util::antiInjection($_POST['tamanho']);

#################################################################################
## Limpar a variável de erro
#################################################################################
$err	= false;

#################################################################################
## Fazer validação dos campos
#################################################################################
 
/** Parametro **/
if (!isset($parametro) || (empty($parametro))) {
	$system->criaAviso(\Zage\App\Aviso\Tipo::ERRO,$tr->trans("Parametro deve ser preenchido !!!"));
	$err	= 1;
}elseif ((!empty($parametro)) && (strlen($parametro) > 60)) {
	$system->criaAviso(\Zage\App\Aviso\Tipo::ERRO,$tr->trans("Campo parametro não deve ter mais de 60 caracteres !!!"));
	$err	= 1;
}

/** modulo **/
if (!isset($codModulo) || (empty($codModulo))) {
	$system->criaAviso(\Zage\App\Aviso\Tipo::ERRO,$tr->trans("Módulo deve ser preenchido !!!"));
	$err	= 1;
}

/** Uso **/
if (!isset($uso) || (empty($uso))) {
	$system->criaAviso(\Zage\App\Aviso\Tipo::ERRO,$tr->trans("Uso deve ser selecionado !!!"));
	$err	= 1;
}

/** Tipo **/
if (!isset($tipo) || (empty($tipo))) {
	$system->criaAviso(\Zage\App\Aviso\Tipo::ERRO,$tr->trans("Tipo deve ser selecionado !!!"));
	$err	= 1;
}elseif ($tipo == 'LIS'){
	if (!isset($valores) || (empty($valores))){
		$system->criaAviso(\Zage\App\Aviso\Tipo::ERRO,$tr->trans("Para o tipo LISTA PREDEFINIDA o campo VALORES não pode estar vazio !!!"));
		$err	= 1;
	}
}

/** Tamanho **/
if (isset($tamanho) && (!empty($tamanho))){
	if (!is_numeric($tamanho)) {
		$system->criaAviso(\Zage\App\Aviso\Tipo::ERRO,$tr->trans("Tamanho deve conter apenas números !!!"));
		$err	= 1;
	}
}else{
	$tamanho = null;
}

/** Obrigatorio **/
if (isset($obrigatorio) && (!empty($obrigatorio))) {
	$obrigatorio	= 1;
}else{
	$obrigatorio	= 0;
}

/** Parametro **/
$oParametro	= $em->getRepository('Entidades\ZgappParametro')->findOneBy(array('parametro' => $parametro, 'codModulo' => $codModulo));

if (($oParametro != null) && ($oParametro->getCodigo() != $codParametro)){
	$system->criaAviso(\Zage\App\Aviso\Tipo::ERRO,$tr->trans("Este parâmetro já existe no módulo selecionado !!!"));
	$err 	= 1;
}
 
if ($err != null) {
	echo '1'.\Zage\App\Util::encodeUrl('||'.htmlentities($err));
 	exit;
}
 
#################################################################################
## Salvar no banco
#################################################################################
try {
 
	if (isset($codParametro) && (!empty($codParametro))) {
 		$oParametro	= $em->getRepository('Entidades\ZgappParametro')->findOneBy(array('codigo' => $codParametro));
 		if (!$oParametro) $oParametro	= new \Entidades\ZgappParametro();
 	}else{
 		$oParametro	= new \Entidades\ZgappParametro();
 	}
 	
 	$oCodModulo	= $em->getRepository('Entidades\ZgappModulo')->findOneBy(array('codigo' => $codModulo));
 	$oTipo		= $em->getRepository('Entidades\ZgappParametroTipo')->findOneBy(array('codigo' => $tipo));
 	$oSecao		= $em->getRepository('Entidades\ZgappParametroSecao')->findOneBy(array('codigo' => $secao));
 	$oUso		= $em->getRepository('Entidades\ZgappParametroUso')->findOneBy(array('codigo' => $uso));
 	
 	
 	if (!$oCodModulo) {
 		$system->criaAviso(\Zage\App\Aviso\Tipo::ERRO,$tr->trans("Módulo não encontrado !!!"));
		echo '1'.\Zage\App\Util::encodeUrl('||');
 		exit;
 	}
 	
 	if (!$oTipo) {
 		$system->criaAviso(\Zage\App\Aviso\Tipo::ERRO,$tr->trans("Tipo não encontrado !!!"));
		echo '1'.\Zage\App\Util::encodeUrl('||');
 		exit;
 	}
 	
 	if (!$oUso) {
 		$system->criaAviso(\Zage\App\Aviso\Tipo::ERRO,$tr->trans("Uso não encontrado !!!"));
 		echo '1'.\Zage\App\Util::encodeUrl('||');
 		exit;
 	}
 	
 	$oParametro->setCodModulo($oCodModulo);
 	$oParametro->setParametro($parametro);
 	$oParametro->setDescricao($descricao);
 	$oParametro->setCodSecao($oSecao);
 	$oParametro->setCodTipo($oTipo);
 	$oParametro->setCodUso($oUso);
 	$oParametro->setValorPadrao($valorPadrao);
 	$oParametro->setIndObrigatorio($obrigatorio);
 	$oParametro->setTamanho($tamanho);
 	
 	$em->persist($oParametro);
 	$em->flush();
 	
 	
 	/**
 	 * Salvar os valores (Lista de valores )
 	 */
 	if ($valores) {
 		
 		$aValores	= explode(",", $valores);
 		
 		/** Excluir **/
 		$infoValores		= $em->getRepository('Entidades\ZgappParametroTipoValor')->findBy(array('codParametro' => $codParametro));

 		for ($i = 0; $i < sizeof($infoValores); $i++) {
 			if (!in_array($infoValores[$i]->getValor(), $aValores)) {
 				try {
 					$em->remove($infoValores[$i]);
 					$em->flush();
 				} catch (\Exception $e) {
 					$system->criaAviso(\Zage\App\Aviso\Tipo::ERRO,"Não foi possível excluir da lista de valores o valor: ".$infoValores[$i]->getValor()." Erro: ".$e->getMessage());
 					echo '1'.\Zage\App\Util::encodeUrl('||'.htmlentities($e->getMessage()));
 					exit;
				}
 			}
 			
 		}
 			
 		/** Criar **/
 		for ($i = 0; $i < sizeof($aValores); $i++) {
 			
 			$infoValor		= $em->getRepository('Entidades\ZgappParametroTipoValor')->findBy(array('codParametro' => $codParametro , 'valor' => $aValores[$i]));
 			
 			if (!$infoValor) {
	 			$oValor		= new \Entidades\ZgappParametroTipoValor();
	 			$oValor->setcodParametro($oParametro);
	 			$oValor->setValor($aValores[$i]);
	 			
	 		 	try {
		 			$em->persist($oValor);
	 				$em->flush();
	 				$em->detach($oValor);
	 			} catch (\Exception $e) {
	 				$system->criaAviso(\Zage\App\Aviso\Tipo::ERRO,"Não foi possível cadastrar o valor: ".$aValores[$i]." Erro: ".$e->getMessage());
	 				echo '1'.\Zage\App\Util::encodeUrl('||'.htmlentities($e->getMessage()));
	 				exit;
				}
 			}
 			
 		}
 	
 	}
 	
 	$em->detach($oParametro);
 	
 	
} catch (\Exception $e) {
 	$system->criaAviso(\Zage\App\Aviso\Tipo::ERRO,$e->getMessage());
 	echo '1'.\Zage\App\Util::encodeUrl('||'.htmlentities($e->getMessage()));
 	exit;
}
 
$system->criaAviso(\Zage\App\Aviso\Tipo::INFO,"Informações salvas com sucesso");
echo '0'.\Zage\App\Util::encodeUrl('|'.$oParametro->getCodigo());