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
if (isset($_POST['codConvidado']))			$codConvidado		= $_POST['codConvidado'];
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

if ($codConvidado == null){
	$codConvidado	= array();
}

#################################################################################
## Limpar a variável de erro
#################################################################################
$err	= false;

#################################################################################
## Fazer validação dos campos
#################################################################################
/** CodGrupo **/
if (empty($codGrupo)){
	die ('1'.\Zage\App\Util::encodeUrl('||'.htmlentities($tr->trans("PARÂMETRO NÃO INFORMANDO : COD_GRUPO"))));
}

/** CodConvidado **/
if (!is_array($codConvidado)) {
	die ('1'.\Zage\App\Util::encodeUrl('||'.htmlentities($tr->trans("VARIÁVEL INVÁLIDA : COD_CONVIDADO"))));
}

/** Nome **/
if (!empty($nome)){
	for ($v = 0; $v < sizeof($nome); $v++) {
		if ($nome[$v] == null){
			die ('1'.\Zage\App\Util::encodeUrl('||'.htmlentities($tr->trans("Não pode haver uma linha sem o nome preenchido!"))));
			
		}
	}
}

/** Nome **/
if (!is_array($nome)) {
	die ('1'.\Zage\App\Util::encodeUrl('||'.htmlentities($tr->trans("djuhdsiuhdsiuh!"))));
	$err 	= 1;
}

#################################################################################
## Validar o tamanho dos arrays
#################################################################################
$numCon	= sizeof($codConvidado);

#################################################################################
## Salvar no banco
#################################################################################
try {
	#################################################################################
	## Apagar os convidados
	#################################################################################
	$convidados			= $em->getRepository('Entidades\ZgfmtListaConvidado')->findBy(array('codUsuario' => $system->getCodUsuario(), 'codGrupo' => $codGrupo));

	for ($i = 0; $i < sizeof($convidados); $i++) {
		$log->debug($convidados[$i]->getCodigo());
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
		//$log->debug($codConvidado[$i]);
		#################################################################################
		## Verifica se o registro já existe no banco
		#################################################################################
		$oConvidado		= $em->getRepository('Entidades\ZgfmtListaConvidado')->findOneBy(array('codigo' => $codConvidado[$i]));
		
		if (!$oConvidado) {
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

echo '0'.\Zage\App\Util::encodeUrl('||'.htmlentities($tr->trans('Lista de convidados atualizada com sucesso!')));
