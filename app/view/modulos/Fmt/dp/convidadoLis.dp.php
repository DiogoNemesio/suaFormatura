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
## Resgata os parâmetros passados pelo formulário
#################################################################################
if (isset($_POST['codConvidado']))			$codConvidado		= \Zage\App\Util::antiInjection($_POST['codConvidado']);
if (isset($_POST['codGrupo']))				$codGrupo			= \Zage\App\Util::antiInjection($_POST['codGrupo']);
if (isset($_POST['nome']))					$nome				= \Zage\App\Util::antiInjection($_POST['nome']);
if (isset($_POST['telefone']))				$telefone			= \Zage\App\Util::antiInjection($_POST['telefone']);
if (isset($_POST['sexo']))					$sexo				= \Zage\App\Util::antiInjection($_POST['sexo']);
if (isset($_POST['codFaixaEtaria']))		$codFaixaEtaria		= \Zage\App\Util::antiInjection($_POST['codFaixaEtaria']);
if (isset($_POST['email']))					$email				= \Zage\App\Util::antiInjection($_POST['email']);

#################################################################################
## Caso não venha as variáveis (ARRAY) inicializar eles
#################################################################################
if (!isset($nome))				$nome				= array();
if (!isset($telefone))			$telefone			= array();
if (!isset($sexo))				$sexo				= array();
if (!isset($codFaixaEtaria))	$codFaixaEtaria		= array();
if (!isset($email))				$email				= array();

#################################################################################
## Limpar a variável de erro
#################################################################################
$err	= false;

#################################################################################
## Fazer validação dos campos
#################################################################################
/** Nome **/
if ((empty($nome))) {
	$system->criaAviso(\Zage\App\Aviso\Tipo::ERRO,$tr->trans("Campo NOME é obrigatório"));
	$err	= 1;
}

if ((strlen($nome) > 100)) {
	$system->criaAviso(\Zage\App\Aviso\Tipo::ERRO,"Campo NOME não deve conter mais de 100 caracteres");
	$err	= 1;
}

/** Grupo **/
if (!isset($codGrupo) || (empty($codGrupo))) {
	$system->criaAviso(\Zage\App\Aviso\Tipo::ERRO,$tr->trans("Campo GRUPO é obrigatório"));
	$err	= 1;
}

/** Nome **/
if (!is_array($nome)) {
	$system->criaAviso(\Zage\App\Aviso\Tipo::ERRO,$tr->trans("Campo NOME inválido !!!"));
	$err 	= 1;
}

/** Telefone **/
if (!is_array($telefone)) {
	$system->criaAviso(\Zage\App\Aviso\Tipo::ERRO,$tr->trans("Campo TELEFONE inválido !!!"));
	$err 	= 1;
}

/** Sexo **/
if (!is_array($sexo)) {
	$system->criaAviso(\Zage\App\Aviso\Tipo::ERRO,$tr->trans("Campo SEXO inválido !!!"));
	$err 	= 1;
}

/** CodFaixaEtaria **/
if (!is_array($codFaixaEtaria)) {
	$system->criaAviso(\Zage\App\Aviso\Tipo::ERRO,$tr->trans("Campo COD_FAIXA_ETARIA inválido !!!"));
	$err 	= 1;
}

/** EMAIL **/
if (!is_array($email)) {
	$system->criaAviso(\Zage\App\Aviso\Tipo::ERRO,$tr->trans("Campo EMAIL inválido !!!"));
	$err 	= 1;
}

/** CodConvidado **/
if (!is_array($codConvidado)) {
	$system->criaAviso(\Zage\App\Aviso\Tipo::ERRO,$tr->trans("Campo COD_CONVIDADO inválido !!!"));
	$err 	= 1;
}

#################################################################################
## Validar o tamanho dos arrays
#################################################################################
$numCon	= sizeof($codConvidado);

/** Nome **/
if (sizeof($nome) != $numCon) {
	$system->criaAviso(\Zage\App\Aviso\Tipo::ERRO,$tr->trans("Campo NOME com tamanho inválido !!!"));
	$err 	= 1;
}

/** Telefone **/
if (sizeof($telefone) != $numCon) {
	$system->criaAviso(\Zage\App\Aviso\Tipo::ERRO,$tr->trans("Campo TELEFONE com tamanho inválido !!!"));
	$err 	= 1;
}

/** Sexo **/
if (sizeof($sexo) != $numCon) {
	$system->criaAviso(\Zage\App\Aviso\Tipo::ERRO,$tr->trans("Campo SEXO com tamanho inválido !!!"));
	$err 	= 1;
}

/** CodFaixaEtaria **/
if (sizeof($codFaixaEtaria) != $numCon) {
	$system->criaAviso(\Zage\App\Aviso\Tipo::ERRO,$tr->trans("Campo COD_FAIXA_ETARIA com tamanho inválido !!!"));
	$err 	= 1;
}

/** Email **/
if (sizeof($email) != $numCon) {
	$system->criaAviso(\Zage\App\Aviso\Tipo::ERRO,$tr->trans("Campo EMAIL com tamanho inválido !!!"));
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
	#################################################################################
	## Apagar os convidados
	#################################################################################
	$convidados			= $em->getRepository('Entidades\ZgfmtListaConvidado')->findBy(array('codigo' => $codConvidado));

	for ($i = 0; $i < sizeof($convidados); $i++) {
		if (!in_array($convidados[$i]->getCodigo(), $codConvidado)) {
			try {
				$em->remove($convidados[$i]);
			} catch (\Exception $e) {
				$system->criaAviso(\Zage\App\Aviso\Tipo::ERRO,"Não foi possível excluir o Convidado de nome: ".$convidados[$i]->getNome()." Erro: ".$e->getMessage());
				echo '1'.\Zage\App\Util::encodeUrl('||'.htmlentities($e->getMessage()));
				exit;
			}
		}
	}

	#################################################################################
	## Criar / Alterar
	#################################################################################
	for ($i = 0; $i < $numCon; $i++) {

		#################################################################################
		## Verifica se o registro já existe no banco
		#################################################################################
		if (!empty($codConvidado[$i])) {
			$oConvidado		= $em->getRepository('Entidades\ZgfmtListaConvidado')->findOneBy(array('codigo' => $codConvidado[$i]));
			if (!$oConvidado)	$oConvidado	= new \Entidades\ZgfmtListaConvidado();
		}else{
			$oConvidado	= new \Entidades\ZgfmtListaConvidado();
		}
	
		#################################################################################
		## Constroi os objetos
		#################################################################################
		$oUsuario		= $em->getRepository('Entidades\ZgsegUsuario')->findOneBy(array('codigo' => $system->getCodUsuario()));
		$oGrupo			= $em->getRepository('Entidades\ZgfmtConvidadoGrupo')->findOneBy(array('codigo' => $codGrupo));
		$oSexo			= $em->getRepository('Entidades\ZgsegSexoTipo')->findOneBy(array('codigo' => $sexo[$i]));
		$oFaixaEtaria	= $em->getRepository('Entidades\ZgfmtConvidadoFaixaEtaria')->findOneBy(array('codigo' => $codFaixaEtaria[$i]));

		$oConvidado->setCodUsuario($oUsuario);
		$oConvidado->setCodGrupo($oGrupo);
		$oConvidado->setNome($nome[$i]);
		$oConvidado->setTelefone($telefone[$i]);
		$oConvidado->setSexo($oSexo);
		$oConvidado->setCodFaixaEtaria($oFaixaEtaria);
		$oConvidado->setEmail($email[$i]);

		$em->persist($oConvidado);
	}

	$em->flush();
	$em->clear();

} catch (\Exception $e) {
	$system->criaAviso(\Zage\App\Aviso\Tipo::ERRO,$e->getMessage());
	echo '1'.\Zage\App\Util::encodeUrl('||'.htmlentities($e->getMessage()));
	exit;
}

$system->criaAviso(\Zage\App\Aviso\Tipo::INFO,"Informações salvas com sucesso");
echo '0'.\Zage\App\Util::encodeUrl('||');
