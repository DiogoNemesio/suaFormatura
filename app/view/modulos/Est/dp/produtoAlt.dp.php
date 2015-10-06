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
if (isset($_POST['codProduto']))		$codProduto			= \Zage\App\Util::antiInjection($_POST['codProduto']);
if (isset($_POST['codTipo']))			$codTipo			= \Zage\App\Util::antiInjection($_POST['codTipo']);
if (isset($_POST['nome']))				$nome				= \Zage\App\Util::antiInjection($_POST['nome']);
if (isset($_POST['descricao']))			$descricao			= \Zage\App\Util::antiInjection($_POST['descricao']);
if (isset($_POST['codSubgrupo']))	 	$codSubgrupo		= \Zage\App\Util::antiInjection($_POST['codSubgrupo']);
if (isset($_POST['indAtivo']))	 		$ativo				= \Zage\App\Util::antiInjection($_POST['indAtivo']);

if ($codTipo == P){
	$indReservaOnline = null;
	$qtdeDiasPreReserva	  = null;
	$qtdeDiasIndis	  = null;
	$quantidade = null;
	
}elseif ($codTipo == S){
	if (isset($_POST['indReservaOnline']))	 	$indReservaOnline		= \Zage\App\Util::antiInjection($_POST['indReservaOnline']);
	if (isset($_POST['qtdeDiasPreReserva']))	$qtdeDiasPreReserva		= \Zage\App\Util::antiInjection($_POST['qtdeDiasPreReserva']);
	if (isset($_POST['qtdeDiasIndis']))	 		$qtdeDiasIndis			= \Zage\App\Util::antiInjection($_POST['qtdeDiasIndis']);
	if (isset($_POST['qtdeServico']))	 		$quantidade				= \Zage\App\Util::antiInjection($_POST['qtdeServico']);
}

/** Valores **/
if (isset($_POST['codValor']))			$codValor			= $_POST['codValor'];
if (isset($_POST['valor']))				$valor				= $_POST['valor'];
if (isset($_POST['dataBase']))			$dataBase			= $_POST['dataBase'];
if (isset($_POST['desconPorcMax']))		$desconPorcMax		= $_POST['desconPorcMax'];

if (!isset($codValor))					$codValor			= array();
if (!isset($valor))						$valor				= array();
if (!isset($dataBase))					$dataBase			= array();
if (!isset($desconPorcMax))				$desconPorcMax		= array();

#################################################################################
## Resgata os valores das configurações
#################################################################################
if (isset($_POST["_zgConf"])) {
	$_zgConf = $_POST["_zgConf"];
}else{
	//$system->criaAviso(\Zage\App\Aviso\Tipo::ERRO,$tr->trans('Variável POST mal formada'). ', file: '.__FILE__);
	//echo '1'.\Zage\App\Util::encodeUrl('||');
	//exit;
}

#################################################################################
## Limpar a variável de erro
#################################################################################
$err	= false;

#################################################################################
## Fazer validação dos campos
#################################################################################
/** Descrição**/
if ((!empty($descricao)) && (strlen($descricao) > 500)) {
 	$system->criaAviso(\Zage\App\Aviso\Tipo::ERRO,$tr->trans("A descrição do produto não deve conter mais de 500 caracteres!"));
	$err	= 1;
}

/** Nome**/
if ((!empty($nome)) && (strlen($nome) > 100)) {
	$system->criaAviso(\Zage\App\Aviso\Tipo::ERRO,$tr->trans("O nome do produto não deve conter mais de 100 caracteres!"));
	$err	= 1;
}

if ((empty($nome))) {
	$system->criaAviso(\Zage\App\Aviso\Tipo::ERRO,$tr->trans("O nome do produto deve ser preenchido!"));
	$err	= 1;
}

/** Tipo**/
if ((empty($codTipo))) {
	$system->criaAviso(\Zage\App\Aviso\Tipo::ERRO,$tr->trans("Selecione um tipo para seu produto!"));
	$err	= 1;
}

/** SubGrupo**/
if ((empty($codSubgrupo))) {
	$system->criaAviso(\Zage\App\Aviso\Tipo::ERRO,$tr->trans("Selecione um subgrupo para o seu produto!"));
	$err	= 1;
}

/** Ativo **/
if (isset($ativo) && (!empty($ativo))) {
	$ativo	= 1;
}else{
	$ativo	= 0;
}

/** indReservaOnline **/
if (isset($indReservaOnline) && (!empty($indReservaOnline))) {
	$indReservaOnline	= 1;
}else{
	$indReservaOnline	= 0;
}

/** Quantidade **/
if (isset($quantidade) && (empty($quantidade))) {
	$quantidade = null;
}

if ($err != null) {
	echo '1'.\Zage\App\Util::encodeUrl('||'.htmlentities($err));
 	exit;
}
 
#################################################################################
## Salvar no banco
#################################################################################
try {
	
	#################################################################################
	## Salvar o produto
	#################################################################################
	
	if (isset($codProduto) && (!empty($codProduto))) {
 		$oProduto	= $em->getRepository('Entidades\ZgestProduto')->findOneBy(array('codigo' => $codProduto));
 		if (!$oProduto) $oProduto	= new \Entidades\ZgestProduto();
 	}else{
 		$oProduto	= new \Entidades\ZgestProduto();
 	}
 	
 	$oOrganização		= $em->getRepository('Entidades\ZgadmOrganizacao')->findOneBy(array('codigo' => $system->getCodOrganizacao()));
 	$oSubgrupo			= $em->getRepository('Entidades\ZgestSubgrupo')->findOneBy(array('codigo' => $codSubgrupo));
 	$oTipoMaterial		= $em->getRepository('Entidades\ZgestTipoProduto')->findOneBy(array('codigo' => $codTipo));
 	
 	$oProduto->setCodOrganizacao($oOrganização);
 	$oProduto->setCodTipoMaterial($oTipoMaterial);
 	$oProduto->setCodSubgrupo($oSubgrupo);
 	$oProduto->setNome($nome);
 	$oProduto->setDescricao($descricao);
 	$oProduto->setIndAtivo($ativo);
 	$oProduto->setIndReservaOnline($indReservaOnline);
 	$oProduto->setQuantidade($quantidade);
 	$oProduto->setNumDiasIndisponivel($qtdeDiasIndis);
 	$oProduto->setQtdeDiasPreReserva($qtdeDiasPreReserva);
 	$oProduto->setDataCadastro(new \DateTime("now"));
 	
 	#################################################################################
 	## Salvar os atributos
 	#################################################################################
 	$clear	= false;
 	$oSubgrupoConf = $em->getRepository('Entidades\ZgestSubgrupoConf')->findBy(array('codigo' => $codSubgrupo));
 	
 	for ($i = 0; $i < sizeof($oSubgrupoConf); $i++) {
 		
 		if (isset($_zgConf[$oSubgrupoConf[$i]->getCodigo()])) {
 			$valorConf	= \Zage\App\Util::antiInjection($_zgConf[$oSubgrupoConf[$i]->getCodigo()]);
 		}else{
 			$system->criaAviso(\Zage\App\Aviso\Tipo::ERRO,$tr->trans('Variável POST mal formada'). ', file: '.__FILE__);
 			echo '1'.\Zage\App\Util::encodeUrl('||');
 			exit;
 		}
 	
 		#############################################################################################
 		## Validar o campo
 		#############################################################################################
 		$tipoConf			= $oSubgrupoConf[$i]->getCodTipo()->getCodigo();
 		$err			= null;
 	
 		if ($tipoConf == "N") {
 			if (!is_numeric($valorConf)) 	$err	= 1;
 		}elseif ($tipoConf == "DT") {
 			if (\Zage\App\Util::validaData($valorSubgrupo,$system->config["data"]["dateFormat"]) == false) {
 				$err	= 1;
 			}
 		}elseif ($tipoConf == "DIN") {
 			/** Retirar o dígito de milhar **/
 			$valorSubgrupo	= str_replace('.', '', $valorConf);
 		}elseif ($tipoConf == "P") {
 			/** Retirar o % da string **/
 			$valorConf	= str_replace('%', '', $valorConf);
 		}
 	
 		if ($err !== null) {
 			if ($clear	== true) {
 				$em->clear();
 			}
 			$system->criaAviso(\Zage\App\Aviso\Tipo::ERRO,$tr->trans('Campo %s inválido',array('%s' => $indices[$i]->getNome())));
 			echo '1'.\Zage\App\Util::encodeUrl('||');
 			exit;
 	
 		}
 	
 		try {
 			#################################################################################
 			## Verificar se a informação já existe
 			#################################################################################
 			$oProdValorConf	=	$em->getRepository('Entidades\ZgestProdutoSubgrupoValor')->findOneBy(array('codSubgrupoConf' => $codSubgrupo,'codProduto' => $codProduto));

 			if (!$oProdValorConf) {
 				$oProdValorConf	= new \Entidades\ZgestProdutoSubgrupoValor();
 			}
 	
 			$oProdValorConf->setCodProduto($oProduto);
 			$oProdValorConf->setCodSubgrupoConf($oSubgrupoConf[$i]);
 			$oProdValorConf->setValor($valorConf);
 		
 			$em->persist($oProdValorConf);
 			$clear	= true;
 			} catch (\Exception $e) {
 				$system->criaAviso(\Zage\App\Aviso\Tipo::ERRO,$e->getMessage());
 				echo '1'.\Zage\App\Util::encodeUrl('||'.htmlentities($e->getMessage()));
 				exit;
 			}
 	}
 	
 	#################################################################################
 	## Flush
 	#################################################################################
 	$em->persist($oProduto);
 	$em->flush();
 	//$em->detach($oProduto);
 	
 	#################################################################################
 	## Valores
 	#################################################################################
 	$valores		= $em->getRepository('Entidades\ZgestProdutoValor')->findBy(array('codProduto' => $codProduto));
 	
 	#################################################################################
 	## Exclusão
 	#################################################################################
 	for($i = 0; $i < sizeof ( $valores ); $i ++) {
		if (! in_array ( $valores [$i]->getCodigo (), $codValor )) {
			try {
				$em->remove ( $valores [$i] );
				$em->flush ();
			} catch ( \Exception $e ) {
				$system->criaAviso ( \Zage\App\Aviso\Tipo::ERRO, "Não foi possível excluir o valor: " . $valores [$i]->getValor () . " Erro: " . $e->getMessage () );
				echo '1' . \Zage\App\Util::encodeUrl ( '||' . htmlentities ( $e->getMessage () ) );
				exit ();
			}
		}
	}
 	
 	#################################################################################
 	## Criação / Alteração
 	#################################################################################
 	for($i = 0; $i < sizeof ( $codValor ); $i ++) {

 		$infoVal = $em->getRepository ( 'Entidades\ZgestProdutoValor' )->findOneBy ( array (
				'codigo' => $codValor [$i],
				'codProduto' => $oProduto->getCodigo () 
		) );
		
		if (! $infoVal) {
			$infoVal = new \Entidades\ZgestProdutoValor ();
		}
		
		/******* Valor *********/
		if (!isset($valor[$i]) || (empty($valor[$i]))) {
			$system->criaAviso(\Zage\App\Aviso\Tipo::ERRO,$tr->trans("O valor deve ser preenchido!"));
			$err	= 1;
		}elseif (!empty($valor[$i])) {
			$valor		= \Zage\App\Util::to_float($valor[$i]);
			if (!$valor) {
				$system->criaAviso(\Zage\App\Aviso\Tipo::ERRO,$tr->trans("O valor do produto tem um formato inválido!"));
				$err	= 1;
			}
		}
		
		/******* Desconto *********/
		if (!empty($desconPorcMax[$i])) {
			$desconPorcMax		= \Zage\App\Util::to_float($desconPorcMax[$i]);
			if (!$desconPorcMax) {
				$system->criaAviso(\Zage\App\Aviso\Tipo::ERRO,$tr->trans("A porcentagem de desconto tem um formato inválido!"));
				$err	= 1;
			}
		}
		
		if (! empty ( $dataBase [$i] )) {
			$dataBase = DateTime::createFromFormat ( $system->config ["data"] ["dateFormat"], $dataBase [$i] );
		} else {
			$dataBase = null;
		}
		
		// if ($infoTel->getCodTipoTelefone () !== $codTipoTel [$i] || $infoTel->getTelefone () !== $telefone [$i]) {
		
		$infoVal->setCodProduto ( $oProduto );
		$infoVal->setValor($valor);
		$infoVal->setDataBase($dataBase);
		$infoVal->setDescontoPorcentoMax($desconPorcMax);
		$infoVal->setDataCadastro(new \DateTime("now"));
		
		try {
			$em->persist ( $infoVal );
			$em->flush ();
			$em->detach ( $infoVal );
		} catch ( \Exception $e ) {
			$system->criaAviso ( \Zage\App\Aviso\Tipo::ERRO, "Não foi possível cadastrar o valor: " . $valor [$i] . " Erro: " . $e->getMessage () );
			echo '1' . \Zage\App\Util::encodeUrl ( '||' . htmlentities ( $e->getMessage () ) );
			exit ();
		}
		// }
	}
 	 	
} catch (\Exception $e) {
 	$system->criaAviso(\Zage\App\Aviso\Tipo::ERRO,$e->getMessage());
 	echo '1'.\Zage\App\Util::encodeUrl('||'.htmlentities($e->getMessage()));
 	exit;
}
 
$system->criaAviso(\Zage\App\Aviso\Tipo::INFO,"Informações salvas com sucesso");
echo '0'.\Zage\App\Util::encodeUrl('|'.$oProduto->getCodigo());