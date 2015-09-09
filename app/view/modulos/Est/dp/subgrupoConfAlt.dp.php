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
if (isset($_POST['codConf']))			$codConf			= \Zage\App\Util::antiInjection($_POST['codConf']);
if (isset($_POST['codSubgrupo']))		$codSubgrupo		= \Zage\App\Util::antiInjection($_POST['codSubgrupo']);
if (isset($_POST['nome'])) 				$nome				= \Zage\App\Util::antiInjection($_POST['nome']);
if (isset($_POST['descricao'])) 		$descricao			= \Zage\App\Util::antiInjection($_POST['descricao']);
if (isset($_POST['codTipo'])) 			$codTipo			= \Zage\App\Util::antiInjection($_POST['codTipo']);
if (isset($_POST['tamanho']))			$tamanho			= \Zage\App\Util::antiInjection($_POST['tamanho']);
if (isset($_POST['valores'])) 			$valores			= \Zage\App\Util::antiInjection($_POST['valores']);
if (isset($_POST['indAtivo'])) 			$indAtivo			= \Zage\App\Util::antiInjection($_POST['indAtivo']);
if (isset($_POST['indObrigatorio'])) 	$indObrigatorio		= \Zage\App\Util::antiInjection($_POST['indObrigatorio']);

#################################################################################
## Limpar a variável de erro
#################################################################################
$err	= false;

#################################################################################
## Fazer validação dos campos
#################################################################################
/** Nome **/
if (!isset($nome) || (empty($nome))) {
	die ('1'.\Zage\App\Util::encodeUrl('||'.htmlentities($tr->trans("O nome da configuração deve ser preencido!"))));
	$err	= 1;
}elseif ((!empty($nome)) && (strlen($nome) > 60)) {
	$system->criaAviso(\Zage\App\Aviso\Tipo::ERRO,$tr->trans("O nome da configuração não deve conter mais de 60 caracteres!"));
	$err	= 1;
}

/** Descrição **/
if (!isset($descricao) || (empty($descricao))) {
	die ('1'.\Zage\App\Util::encodeUrl('||'.htmlentities($tr->trans("A descrição da configuração deve ser preencida!"))));
	$err	= 1;
}elseif ((!empty($descricao)) && (strlen($descricao) > 60)) {
	$system->criaAviso(\Zage\App\Aviso\Tipo::ERRO,$tr->trans("A descrição da configuração não deve conter mais de 60 caracteres!"));
	$err	= 1;
}

/** Tipo **/
if (!isset($codTipo) || (empty($codTipo))) {
	die ('1'.\Zage\App\Util::encodeUrl('||'.htmlentities($tr->trans("O tipo da configuração deve ser preenchido!"))));
	$err	= 1;
}

/** Tamanho **/
if ((empty($tamanho))) {
	$tamanho = null;
}else{
	$val = is_numeric($tamanho);
	if ($val == true){
		if($tamanho == 0){
			$tamanho = null;
		}
	}else{
		die ('1'.\Zage\App\Util::encodeUrl('||'.htmlentities($tr->trans("O tamanho deve conter apenas números!"))));
		$err	= 1;
	}
}

/** Ativo **/
if (isset($indAtivo) && (!empty($indAtivo))) {
	$indAtivo	= 1;
}else{
	$indAtivo	= 0;
}

/** Obrigatório **/
if (isset($indObrigatorio) && (!empty($indObrigatorio))) {
	$indObrigatorio	= 1;
}else{
	$indObrigatorio	= 0;
}


if ($err != null) {
	echo '1'.\Zage\App\Util::encodeUrl('||'.htmlentities($err));
 	exit;
}

#################################################################################
## Salvar no banco															
#################################################################################
try {

	/**
	 * Salvar configurações
	 */
	if (isset($codConf) && (!empty($codConf))) {
		$oConf = $em->getRepository('Entidades\ZgestSubgrupoConf')->findOneBy(array('codigo' => $codConf));
 		if (!$oConf) $oConf	= new \Entidades\ZgestSubgrupoConf();
 	}else{
 		$oConf	= new \Entidades\ZgestSubgrupoConf();
 	}
 
 	$oSubgrupo		= $em->getRepository('Entidades\ZgestSubgrupo')->findOneBy(array('codigo' => $codSubgrupo));
 	$oTipo			= $em->getRepository('Entidades\ZgestSubgrupoConfTipo')->findOneBy(array('codigo' => $codTipo));
 	
 	$oConf->setCodSubgrupo($oSubgrupo);
 	$oConf->setCodTipo($oTipo);
 	$oConf->setNome($nome);
 	$oConf->setDescricao($descricao);
 	$oConf->setIndAtivo($indAtivo);
 	$oConf->setIndObrigatorio($indObrigatorio);
 	$oConf->setTamanho($tamanho);
 	
 	$em->persist($oConf);
 	
 	/**
 	 * Salvar os valores (Lista de valores)
 	 */
 	if ($valores) {
 		
 		$aValores	= explode(', ', $valores);
 		/** Excluir **/
 		$infoValores		= $em->getRepository('Entidades\ZgestSubgrupoConfValor')->findBy(array('codSubgrupoConf' => $codConf));
 		
 		for ($i = 0; $i < sizeof($infoValores); $i++) {
 			
 			if (!in_array($infoValores[$i]->getValor(), $aValores)) {
 					
 				try {
 					$em->remove($infoValores[$i]);
 				} catch (\Exception $e) {
 					$system->criaAviso(\Zage\App\Aviso\Tipo::ERRO,"Não foi possível excluir da lista de valores o valor: ".$infoValores[$i]->getValor()." Erro: ".$e->getMessage());
 					echo '1'.\Zage\App\Util::encodeUrl('||'.htmlentities($e->getMessage()));
 					exit;
 				}
 			}
 	
 		}
 	
 		/** Criar **/
 		for ($i = 0; $i < sizeof($aValores); $i++) {
 	
 			$infoValor		= $em->getRepository('Entidades\ZgestSubgrupoConfValor')->findBy(array('codSubgrupoConf' => $codConf , 'valor' => $aValores[$i]));
 	
 			if (!$infoValor) {
 				$oValor		= new \Entidades\ZgestSubgrupoConfValor();
 				$oValor->setCodSubgrupoConf($oConf);
 				$oValor->setValor($aValores[$i]);
 					
 				try {
 					$em->persist($oValor);
 				} catch (\Exception $e) {
 					$system->criaAviso(\Zage\App\Aviso\Tipo::ERRO,"Não foi possível cadastrar o valor: ".$aValores[$i]." Erro: ".$e->getMessage());
 					echo '1'.\Zage\App\Util::encodeUrl('||'.htmlentities($e->getMessage()));
 					exit;
 				}
 			}
 		}
 	}
 	
 	/**
 	 * Flush
 	 */
 	$em->flush();
 	$em->clear();
 	 	
} catch (\Exception $e) {
 	$system->criaAviso(\Zage\App\Aviso\Tipo::ERRO,$e->getMessage());
 	echo '1'.\Zage\App\Util::encodeUrl('||'.htmlentities($e->getMessage()));
 	exit;
}
 
$system->criaAviso(\Zage\App\Aviso\Tipo::INFO,"Informações salvas com sucesso");
echo '0'.\Zage\App\Util::encodeUrl('|'.$oConf->getCodigo());