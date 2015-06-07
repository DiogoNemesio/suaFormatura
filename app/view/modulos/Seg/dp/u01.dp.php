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
if (isset($_POST['_cdu01'])) 			$codAssoc			= \Zage\App\Util::antiInjection($_POST['_cdu01']);
if (isset($_POST['_cdu02'])) 			$codUsuario			= \Zage\App\Util::antiInjection($_POST['_cdu02']);
if (isset($_POST['_cdu03'])) 			$codOrganizacao		= \Zage\App\Util::antiInjection($_POST['_cdu03']);
if (isset($_POST['_cdu04'])) 			$codConvite			= \Zage\App\Util::antiInjection($_POST['_cdu04']);

if (isset($_POST['nome'])) 				$nome				= \Zage\App\Util::antiInjection($_POST['nome']);
if (isset($_POST['apelido']))			$apelido			= \Zage\App\Util::antiInjection($_POST['apelido']);
if (isset($_POST['cpf'])) 				$cpf				= \Zage\App\Util::antiInjection($_POST['cpf']);
if (isset($_POST['sexo'])) 				$sexo				= \Zage\App\Util::antiInjection($_POST['sexo']);
if (isset($_POST['senhaCad'])) 			$senha				= \Zage\App\Util::antiInjection($_POST['senhaCad']);
if (isset($_POST['confSenhaCad'])) 		$confSenha			= \Zage\App\Util::antiInjection($_POST['confSenhaCad']);
if (isset($_POST['codLogradouro'])) 	$codLogradouro		= \Zage\App\Util::antiInjection($_POST['codLogradouro']);
if (isset($_POST['cep'])) 				$cep				= \Zage\App\Util::antiInjection($_POST['cep']);
if (isset($_POST['descLogradouro'])) 	$logradouro			= \Zage\App\Util::antiInjection($_POST['descLogradouro']);
if (isset($_POST['bairro'])) 			$bairro				= \Zage\App\Util::antiInjection($_POST['bairro']);
if (isset($_POST['numero'])) 			$numero				= \Zage\App\Util::antiInjection($_POST['numero']);
if (isset($_POST['complemento'])) 		$complemento		= \Zage\App\Util::antiInjection($_POST['complemento']);

#################################################################################
## Limpar a variável de erro
#################################################################################
$err	= false;

#################################################################################
## Fazer validação dos campos
#################################################################################

/** Senha **/
if (!empty($senha) && strlen($senha) < 4) {
	$system->criaAviso(\Zage\App\Aviso\Tipo::ERRO,$tr->trans("Insira uma senha com mais caracteres !!"));
	$err	= 1;
}

if (!empty($senha) && ($senha !== $confSenha)) {
	$system->criaAviso(\Zage\App\Aviso\Tipo::ERRO,$tr->trans("A senha está diferente da confirmação !!"));
	$err	= 1;
}

/** Nome **/
if (isset($nome) || !empty($nome)) {
	if (strlen($nome) < 4){
		if(strlen($nome) == 0){
			$system->criaAviso(\Zage\App\Aviso\Tipo::ERRO,$tr->trans("O Nome deve ser preenchido !!"));
			$err	= 1;
		}else{
			$system->criaAviso(\Zage\App\Aviso\Tipo::ERRO,$tr->trans("Nome muito pequeno, informe o nome completo !!"));
			$err	= 1;
		}
	}elseif (strlen($nome) > 60){
		$system->criaAviso(\Zage\App\Aviso\Tipo::ERRO,$tr->trans("Nome tem limite de 60 caracteres !!"));
		$err	= 1;
	}
}

/** Telefone **/
/*if (isset($telefone) && (!empty($telefone)) && (!is_numeric($telefone))) {
	$system->criaAviso(\Zage\App\Aviso\Tipo::ERRO,$tr->trans("Telefone inválido"));
	$err	= 1;
}*/

/** Celular **/
/*if (isset($celular) && (!empty($celular)) && (!is_numeric($celular))) {
	$system->criaAviso(\Zage\App\Aviso\Tipo::ERRO,$tr->trans("Celular inválido"));
	$err	= 1;
}*/

if (!isset($codUsuario) || (empty($codUsuario))) {
	$system->criaAviso(\Zage\App\Aviso\Tipo::ERRO,$tr->trans("Usuário inválido"));
	$err	= 1;
}

#################################################################################
## Verificar se os usuário já existe e se já está ativo
#################################################################################
$oUsuario	= $em->getRepository('Entidades\ZgsegUsuario')->findOneBy(array('codigo' => $codUsuario));
if (!$oUsuario) 											\Zage\App\Erro::halt('Convite não está mais disponível, COD_ERRO: 06');
if ($oUsuario->getCodStatus()->getCodigo() != "P")			\Zage\App\Erro::halt('Convite não está mais disponível, COD_ERRO: 07');

#################################################################################
## Verificar a associação do usuário a Organização
#################################################################################
$oUsuOrg		= $em->getRepository('Entidades\ZgsegUsuarioOrganizacao')->findOneBy(array('codigo' => $codAssoc));
if (!$oUsuOrg) 										\Zage\App\Erro::halt('Convite não está mais disponível, COD_ERRO: 08');
if ($oUsuOrg->getCodStatus()->getCodigo() != "P")	\Zage\App\Erro::halt('Convite não está mais disponível, COD_ERRO: 09');

#################################################################################
## Verificar a senha do convite
#################################################################################
$convite		= $em->getRepository('Entidades\ZgsegConvite')->findOneBy(array('codigo' => $codConvite));
if (!$convite) 								\Zage\App\Erro::halt('Convite não está mais disponível, COD_ERRO: 10');
if ($convite->getIndUtilizado() != 0)		\Zage\App\Erro::halt('Convite não está mais disponível, COD_ERRO: 12');


if ($err != null) {
	echo '1'.\Zage\App\Util::encodeUrl('||'.htmlentities($err));
	exit;
}

#################################################################################
## Salvar no banco
#################################################################################
try {

	#################################################################################
	## Resgatar as chaves estrangeiras
	#################################################################################
	$oOrg		= $em->getRepository('Entidades\ZgadmOrganizacao')->findOneBy(array('codigo' => $codOrganizacao));
	$oStatus	= $em->getRepository('Entidades\ZgsegUsuarioStatusTipo')->findOneBy(array('codigo' => 'A'));
	$oSexo		= $em->getRepository('Entidades\ZgsegSexoTipo')->findOneBy(array('codigo' => $sexo));
	$oLog		= $em->getRepository('Entidades\ZgadmLogradouro')->findOneBy(array('codigo' => $codLogradouro));
	$oUsuOrgSt	= $em->getRepository('Entidades\ZgsegUsuarioOrganizacaoStatus')->findOneBy(array('codigo' => 'A'));
	
	#################################################################################
	## Salvar as informações do usuário
	#################################################################################
	$oUsuario->setNome($nome);
	$oUsuario->setCpf($cpf);
	$oUsuario->setApelido($apelido);
	$oUsuario->setCodLogradouro($oLog);
	$oUsuario->setEndereco($logradouro);
	$oUsuario->setBairro($bairro);
	$oUsuario->setNumero($numero);
	$oUsuario->setCep($cep);
	$oUsuario->setComplemento($complemento);
	$oUsuario->setCodStatus($oStatus);
	$oUsuario->setSexo($oSexo);
	
	$senhaCrip	= \Zage\App\Crypt::crypt($oUsuario->getUsuario(), $senha);
	$oUsuario->setSenha($senhaCrip);
	
	#################################################################################
	## Mudar o status da associação
	#################################################################################
	$oUsuOrg->setCodStatus($oUsuOrgSt);

	#################################################################################
	## Mudar o status do convite
	#################################################################################
	$convite->setIndUtilizado(1);
	//$convite->setDataUtilizacao(new \DateTime());
	
	$em->persist($oUsuario);
	$em->persist($oUsuOrg);
	$em->persist($convite);
	
	$em->flush();
	$em->clear();
	

} catch (\Exception $e) {
	$system->criaAviso(\Zage\App\Aviso\Tipo::ERRO,$e->getMessage());
	echo '1'.\Zage\App\Util::encodeUrl('||'.htmlentities($e->getMessage()));
	exit;
}


$system->criaAviso(\Zage\App\Aviso\Tipo::INFO,$tr->trans("Informações salvas com sucesso"));
echo '0'.\Zage\App\Util::encodeUrl('|'.$oUsuario->getCodigo());
