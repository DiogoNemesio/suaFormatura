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
global $em,$log,$system,$tr;

//$log->info("Post UsuarioFormandoContrato: ".serialize($_POST));

#################################################################################
## Resgata os parâmetros passados pelo formulario
#################################################################################
if (isset($_POST['id']))					$id						= \Zage\App\Util::antiInjection($_POST['id']);
if (isset($_POST['codFormaPag']))			$codFormaPag			= \Zage\App\Util::antiInjection($_POST['codFormaPag']);
if (isset($_POST['aValor']))				$aValor					= \Zage\App\Util::antiInjection($_POST['aValor']);
if (isset($_POST['aData']))					$aData					= \Zage\App\Util::antiInjection($_POST['aData']);
//if (isset($_POST['dataVenc']))				$dataVenc				= \Zage\App\Util::antiInjection($_POST['dataVenc']);

#################################################################################
## Descompacta o ID
#################################################################################
\Zage\App\Util::descompactaId($id);

#################################################################################
## Verifica se o usuário tem permissão no menu
#################################################################################
$system->checaPermissao($_codMenu_);

#################################################################################
## Validações
#################################################################################
#################################################################################
## Formando
#################################################################################
if (!isset($codUsuario)) 	die('1'.\Zage\App\Util::encodeUrl('||'.htmlentities($tr->trans('Falta de Parâmetros').' (COD_USUARIO)')));
$oFormando 					= $em->getRepository('Entidades\ZgsegUsuario')->findOneBy(array('codigo' => $codUsuario));
if (!$oFormando)			die('1'.\Zage\App\Util::encodeUrl('||'.htmlentities($tr->trans('Formando não encontrado'))));

#################################################################################
## Resgata o registro da Pessoa associada ao Formando
#################################################################################
$oPessoa			= \Zage\Fin\Pessoa::getPessoaUsuario($system->getCodOrganizacao(),$codUsuario);
if (!$oPessoa) 		die('1'.\Zage\App\Util::encodeUrl('||'.htmlentities('Violação de acesso, 0x912FB, Pessoa não encontrada')));

#################################################################################
## Verificar se o usuário pode alterar o contrato, só pode altera caso não
## tenha mensalidade gerada
#################################################################################
$temMensalidade	= \Zage\Fmt\Financeiro::temMensalidadeGerada($system->getCodOrganizacao(),$oPessoa->getCodigo());
$podeAlterar	= ($temMensalidade) ? false : true;
if ($podeAlterar	== false)			die('1'.\Zage\App\Util::encodeUrl('||'.htmlentities($tr->trans('Contrato não pode ser alterado, pois já foi gerado mensalidade'))));

#################################################################################
## Forma de Pagamento
## Deve vir preenchida e ser uma forma de pagamento cadastrada
#################################################################################
if (!$codFormaPag)			die('1'.\Zage\App\Util::encodeUrl('||'.htmlentities($tr->trans('Forman de pagamento deve ser selecionada'))));
$oFormaPag					= $em->getRepository('Entidades\ZgfinFormaPagamento')->findOneBy(array('codigo' => $codFormaPag));
if (!$oFormaPag)			die('1'.\Zage\App\Util::encodeUrl('||'.htmlentities($tr->trans('Forma de pagamento não encontrada'))));

#################################################################################
## Array de valores e datas
## Verificar se o tamanho dos arrays são iguais, e se são realmente arrays
#################################################################################
if (!is_array($aValor))		die('1'.\Zage\App\Util::encodeUrl('||'.htmlentities($tr->trans('Valores inconsistentes'))));
if (!is_array($aData))		die('1'.\Zage\App\Util::encodeUrl('||'.htmlentities($tr->trans('Datas inconsistentes'))));

if (sizeof($aData) != sizeof($aValor))	die('1'.\Zage\App\Util::encodeUrl('||'.htmlentities($tr->trans('Array inconsistente'))));

#################################################################################
## Validação das datas de vencimento
#################################################################################
for ($i = 0; $i < sizeof($aData); $i++) {
	if (\Zage\App\Util::validaData($aData[$i], $system->config["data"]["dateFormat"]) == false) {
		die('1'.\Zage\App\Util::encodeUrl('||'.htmlentities($tr->trans('Data inválida na parcela: '.($i+1)))));
	}
}


#################################################################################
## Resgata as informações do contrato
#################################################################################
$oContrato 			= $em->getRepository('Entidades\ZgfmtContratoFormando')->findOneBy(array('codOrganizacao' => $system->getCodOrganizacao() , 'codFormando' => $codUsuario));
if (!$oContrato)	{
	$oContrato		= new \Entidades\ZgfmtContratoFormando();
}

#################################################################################
## Resgata as informações da Organização
#################################################################################
$oOrg				= $em->getRepository('Entidades\ZgadmOrganizacao')->findOneBy(array('codigo' => $system->getCodOrganizacao()));


#################################################################################
## Resgata o tipo de contrato
#################################################################################
$oTipoContrato				= $em->getRepository('Entidades\ZgfmtContratoFormandoTipo')->findOneBy(array('codigo' => "T"));

$log->info("cheguei aqui 1.0.1");
#################################################################################
## Salvar no banco
#################################################################################
try {
	
	$oContrato->setCodFormando($oFormando);
	$oContrato->setCodFormaPagamento($oFormaPag);
	$oContrato->setCodOrganizacao($oOrg);
	$oContrato->setNumMeses(sizeof($aData));
	$oContrato->setCodTipoContrato($oTipoContrato);
	$em->persist($oContrato);

	$log->info("cheguei aqui 1.0.2");
	#################################################################################
	## Excluir as parcelas existentes
	#################################################################################
	if ($oContrato->getCodigo()) {
		$oParcelas		= $em->getRepository('Entidades\ZgfmtContratoFormandoParcela')->findBy(array('codContrato' => $oContrato->getCodigo()));
		for ($i = 0; $i < sizeof($oParcelas); $i++) {
			$em->remove($oParcelas[$i]);
		}
	}
	
	$log->info("cheguei aqui 1.0.3");
	
	#################################################################################
	## Salvar as parcelas
	#################################################################################
	for ($i = 0; $i < sizeof($aValor); $i++) {
		$oParcela		= new \Entidades\ZgfmtContratoFormandoParcela();
		$oParcela->setCodContrato($oContrato);
		$oParcela->setDataVencimento(\DateTime::createFromFormat($system->config["data"]["dateFormat"], $aData[$i]));
		$oParcela->setParcela(($i+1));
		$oParcela->setValor(\Zage\App\Util::to_float($aValor[$i]));
		$em->persist($oParcela);
	}
	$log->info("cheguei aqui 1.0.4");
	
	$em->flush();
	$em->clear();
	
	$mensagem	= $tr->trans("Contrato salvo com sucesso");
	
} catch (\Exception $e) {
	//$em->getConnection()->rollback();
 	echo '1'.\Zage\App\Util::encodeUrl('||'.htmlentities($e->getMessage()));
 	exit;
}
 
echo '0'.\Zage\App\Util::encodeUrl('||'.htmlentities($mensagem));