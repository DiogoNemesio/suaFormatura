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
if (isset($_POST['codSindicato']))	 	$codSindicato		= \Zage\App\Util::antiInjection($_POST['codSindicato']);
if (isset($_POST['nome'])) 				$nome				= \Zage\App\Util::antiInjection($_POST['nome']);
if (isset($_POST['fantasia']))			$fantasia			= \Zage\App\Util::antiInjection($_POST['fantasia']);
if (isset($_POST['apelido']))			$apelido			= \Zage\App\Util::antiInjection($_POST['apelido']);
if (isset($_POST['cnpj']))				$cnpj				= \Zage\App\Util::antiInjection($_POST['cnpj']);
if (isset($_POST['email']))	 			$email				= \Zage\App\Util::antiInjection($_POST['email']);

if (isset($_POST['codLogradouro']))	 	$codLogradouro		= \Zage\App\Util::antiInjection($_POST['codLogradouro']);
if (isset($_POST['descLogradouro']))	$endereco			= \Zage\App\Util::antiInjection($_POST['descLogradouro']);
if (isset($_POST['cep']))				$cep				= \Zage\App\Util::antiInjection($_POST['cep']);
if (isset($_POST['complemento']))		$complemento		= \Zage\App\Util::antiInjection($_POST['complemento']);
if (isset($_POST['numero']))			$numero				= \Zage\App\Util::antiInjection($_POST['numero']);
if (isset($_POST['bairro']))			$bairro				= \Zage\App\Util::antiInjection($_POST['bairro']);

/** Contato **/
if (isset($_POST['codTipoTel']))		$codTipoTel			= $_POST['codTipoTel'];
if (isset($_POST['codTelefone']))		$codTelefone		= $_POST['codTelefone'];
if (isset($_POST['telefone']))			$telefone			= $_POST['telefone'];

if (!isset($codTipoTel))				$codTipoTel			= array();
if (!isset($codTelefone))				$codTelefone		= array();
if (!isset($telefone))					$telefone			= array();
#################################################################################
## Limpar a variável de erro
#################################################################################
$err	= false;

#################################################################################
## Fazer validação dos campos
#################################################################################
/** Nome **/
if (!isset($nome) || (empty($nome))) {
 	$system->criaAviso(\Zage\App\Aviso\Tipo::ERRO,"Campo RAZÃO é obrigatório");
	$err	= 1;
}

if ((!empty($nome)) && (strlen($nome) > 100)) {
	$system->criaAviso(\Zage\App\Aviso\Tipo::ERRO,"Campo NOME não deve conter mais de 100 caracteres");
	$err	= 1;
}

/** Fantasia **/
if (!isset($fantasia) || (empty($fantasia))) {
	$system->criaAviso(\Zage\App\Aviso\Tipo::ERRO,"Campo FANTASIA é obrigatório");
	$err	= 1;
}

if ((!empty($fantasia)) && (strlen($fantasia) > 60)) {
	$system->criaAviso(\Zage\App\Aviso\Tipo::ERRO,"Campo FANTASIA não deve conter mais de 60 caracteres");
	$err	= 1;
}

/** Fantasia **/
if (!isset($apelido) || (empty($apelido))) {
	$system->criaAviso(\Zage\App\Aviso\Tipo::ERRO,"Campo APELIDO é obrigatório");
	$err	= 1;
}

if ((!empty($apelido)) && (strlen($apelido) > 45)) {
	$system->criaAviso(\Zage\App\Aviso\Tipo::ERRO,"Campo APELIDO não deve conter mais de 45 caracteres");
	$err	= 1;
}

/** CNPJ **/
$valCnpj	= new \Zage\App\Validador\Cnpj();

if (!isset($cnpj) || (empty($cnpj))) {
	$system->criaAviso(\Zage\App\Aviso\Tipo::ERRO,"Campo CNPJ é obrigatório");
	$err	= 1;
}else{
	if ($valCnpj->isValid($cnpj) == false){
		$system->criaAviso(\Zage\App\Aviso\Tipo::ERRO,$tr->trans("CNPJ inválido !! Verifique se a informação está correta"));
		$err	= 1;
	}else{
		$oSindicatoInfo	= $em->getRepository('Entidades\ZgrhuSindicato')->findOneBy(array('cnpj' => $cnpj));
		
		if($oSindicatoInfo != null && $oSindicatoInfo->getCodigo() != $codSindicato){
			$system->criaAviso(\Zage\App\Aviso\Tipo::ERRO,"Já existe um sindicato com este CNPJ !!");
			$err	= 1;
		}
	}
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
	## Resgata os objetos (chave estrangeiras)
	#################################################################################
	$oOrganizacao	= $em->getRepository('Entidades\ZgadmOrganizacao')->findOneBy(array('codigo' => $system->getCodOrganizacao()));
	$oLogradouro	= $em->getRepository('Entidades\ZgadmLogradouro')->findOneBy(array('codigo' => $codLogradouro));
	
	if (isset($codSindicato) && (!empty($codSindicato))) {
 		$oSindicato	= $em->getRepository('Entidades\ZgrhuSindicato')->findOneBy(array('codigo' => $codSindicato));
 		if (!$oSindicato) $oSindicato	= new \Entidades\ZgrhuSindicato();
 	}else{
 		$oSindicato	= new \Entidades\ZgrhuSindicato();
 	}
 	
 	$oSindicato->setCodOrganizacao($oOrganizacao);
 	$oSindicato->setNome($nome);
 	$oSindicato->setFantasia($fantasia);
 	$oSindicato->setCnpj($cnpj);
 	$oSindicato->setEmail($email);
 	$oSindicato->setApelido($apelido);
 	
 	$oSindicato->setCep($cep);
 	$oSindicato->setCodLogradouro($oLogradouro);
 	$oSindicato->setBairro($bairro);
 	$oSindicato->setEndereco($endereco);
 	$oSindicato->setNumero($numero);
 	$oSindicato->setComplemento($complemento);
 	
 	$em->persist($oSindicato);
 	$em->flush();
 	//$em->detach($oSindicato);
 	
 	#################################################################################
 	## Contato
 	#################################################################################
 	$telefones		= $em->getRepository('Entidades\ZgrhuSindicatoTelefone')->findBy(array('codSindicato' => $codSindicato));
 	
 	#################################################################################
 	## Exclusão
 	#################################################################################
 	for($i = 0; $i < sizeof ( $telefones ); $i ++) {
		if (! in_array ( $telefones [$i]->getCodigo (), $codTelefone )) {
			try {
				$em->remove ( $telefones [$i] );
				$em->flush ();
			} catch ( \Exception $e ) {
				$system->criaAviso ( \Zage\App\Aviso\Tipo::ERRO, "Não foi possível excluir o telefone: " . $telefones [$i]->getTelefone () . " Erro: " . $e->getMessage () );
				echo '1' . \Zage\App\Util::encodeUrl ( '||' . htmlentities ( $e->getMessage () ) );
				exit ();
			}
		}
	}
 	
 	#################################################################################
 	## Criação / Alteração
 	#################################################################################
 	for($i = 0; $i < sizeof ( $codTelefone ); $i ++) {
 	$infoTel = $em->getRepository ( 'Entidades\ZgrhuSindicatoTelefone' )->findOneBy ( array (
			'codigo' => $codTelefone [$i],
 			'codSindicato' => $oSindicato->getCodigo ()
 	) );
 	
 	if (! $infoTel) {
 	$infoTel = new \Entidades\ZgrhuSindicatoTelefone ();
 	}
 	
 	if ($infoTel->getCodTipoTelefone () !== $codTipoTel [$i] || $infoTel->getTelefone () !== $telefone [$i]) {
			
			$oTipoTel = $em->getRepository ( 'Entidades\ZgappTelefoneTipo' )->findOneBy ( array (
					'codigo' => $codTipoTel [$i] 
			) );
			
			$infoTel->setCodSindicato ( $oSindicato );
			$infoTel->setCodTipoTelefone ( $oTipoTel );
			$infoTel->setTelefone ( $telefone [$i] );
			
			try {
				$em->persist ( $infoTel );
				$em->flush ();
				$em->detach ( $infoTel );
			} catch ( \Exception $e ) {
				$system->criaAviso ( \Zage\App\Aviso\Tipo::ERRO, "Não foi possível cadastrar o telefone: " . $telefone [$i] . " Erro: " . $e->getMessage () );
				echo '1' . \Zage\App\Util::encodeUrl ( '||' . htmlentities ( $e->getMessage () ) );
				exit ();
			}
		}
	}
 	 	
} catch (\Exception $e) {
 	$system->criaAviso(\Zage\App\Aviso\Tipo::ERRO,$e->getMessage());
 	echo '1'.\Zage\App\Util::encodeUrl('||'.htmlentities($e->getMessage()));
 	exit;
}
 
$system->criaAviso(\Zage\App\Aviso\Tipo::INFO,"Informações salvas com sucesso");
echo '0'.\Zage\App\Util::encodeUrl('|'.$oSindicato->getCodigo());