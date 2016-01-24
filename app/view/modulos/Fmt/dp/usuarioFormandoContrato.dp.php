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
if (isset($_POST['codTipoContrato']))		$codTipoContrato		= \Zage\App\Util::antiInjection($_POST['codTipoContrato']);
if (isset($_POST['aValor']))				$aValor					= \Zage\App\Util::antiInjection($_POST['aValor']);
if (isset($_POST['aData']))					$aData					= \Zage\App\Util::antiInjection($_POST['aData']);
if (isset($_POST['aSelFormandos']))			$aSelFormandos			= \Zage\App\Util::antiInjection($_POST['aSelFormandos']);
if (isset($_POST['aSelEventos']))			$aSelEventos			= \Zage\App\Util::antiInjection($_POST['aSelEventos']);


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
if (!isset($aSelFormandos))	die('1'.\Zage\App\Util::encodeUrl('||'.htmlentities($tr->trans('Falta de Parâmetros').' (A_SEL_FORMANDOS)')));
$aSelFormandos				= ($aSelFormandos) 	? explode(",",$aSelFormandos)	: array();
$aSelEventos				= ($aSelEventos) 	? explode(",",$aSelEventos)		: array();

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
## Resgata o tipo de contrato
#################################################################################
$oTipoContrato				= $em->getRepository('Entidades\ZgfmtContratoFormandoTipo')->findOneBy(array('codigo' => $codTipoContrato));
if (!$oTipoContrato)		die('1'.\Zage\App\Util::encodeUrl('||'.htmlentities($tr->trans('Tipo de contrato não encontrado'))));
if (($codTipoContrato == "P") && ( (!$aSelEventos) || sizeof($aSelEventos) == 0)) {
	die('1'.\Zage\App\Util::encodeUrl('||'.htmlentities($tr->trans('Pelo menos 1 evento deve ser selecionado'))));
}

#################################################################################
## Resgata as informações da Organização
#################################################################################
$oOrg				= $em->getRepository('Entidades\ZgadmOrganizacao')->findOneBy(array('codigo' => $system->getCodOrganizacao()));


for ($i = 0; $i < sizeof($aSelFormandos); $i++) {

	#################################################################################
	## Formando
	#################################################################################
	$oFormando 					= $em->getRepository('Entidades\ZgsegUsuario')->findOneBy(array('codigo' => $aSelFormandos[$i]));
	if (!$oFormando)			{
		$log->err('0x912FB8nM: violação de acesso, "Formando ('.$aSelFormandos[$i].') não encontrado", Usuário: '.$system->getCodUsuario().' Organização: '.$system->getCodOrganizacao());
		die('1'.\Zage\App\Util::encodeUrl('||'.htmlentities($tr->trans('0x912FB8nM: violação de acesso'))));
	}
	
	#################################################################################
	## Resgata o registro da Pessoa associada ao Formando
	#################################################################################
	$oPessoa			= \Zage\Fin\Pessoa::getPessoaUsuario($system->getCodOrganizacao(),$aSelFormandos[$i]);
	if (!$oPessoa) 		{
		$log->err('0x912FB8nL: violação de acesso, "Formando ('.$aSelFormandos[$i].') não encontrado no Financeiro", Usuário: '.$system->getCodUsuario().' Organização: '.$system->getCodOrganizacao());
		die('1'.\Zage\App\Util::encodeUrl('||'.htmlentities('Violação de acesso, 0x912FB8nL, Pessoa não encontrada')));
	}
	
	#################################################################################
	## Verificar se o usuário pode alterar o contrato, só pode altera caso não
	## tenha mensalidade gerada
	#################################################################################
	$temMensalidade	= \Zage\Fmt\Financeiro::temMensalidadeGerada($system->getCodOrganizacao(),$oPessoa->getCodigo());
	$podeAlterar	= ($temMensalidade) ? false : true;
	if ($podeAlterar	== false)			{
		$log->err('0xhy1kil01: violação de acesso, "Contrato não pode ser alterado, pois já foi gerado mensalidade", Usuário: '.$system->getCodUsuario().' Organização: '.$system->getCodOrganizacao());
		die('1'.\Zage\App\Util::encodeUrl('||'.htmlentities($tr->trans('Contrato não pode ser alterado, pois já foi gerado mensalidade'))));
	}

	#################################################################################
	## Resgata as informações do contrato
	#################################################################################
	$oContrato 			= $em->getRepository('Entidades\ZgfmtContratoFormando')->findOneBy(array('codOrganizacao' => $system->getCodOrganizacao() , 'codFormando' => $aSelFormandos[$i]));
	if (!$oContrato)	{
		$oContrato		= new \Entidades\ZgfmtContratoFormando();
	}else{
		#################################################################################
		## Verificar se o contrato está ativo, para poder alterar, caso não esteja emitir um erro
		#################################################################################
		if ($oContrato->getCodStatus()->getCodigo() != "A") {
			die('1'.\Zage\App\Util::encodeUrl('||'.htmlentities($tr->trans('Contrato do formando: "'.$oFormando->getNome().'" não pode ser alterado, pois não está mais ativo'))));
		}
	}

	#################################################################################
	## Salvar no banco
	#################################################################################
	try {
		$codStatus				= "A";
		$oStatusContrato		= $em->getRepository('Entidades\ZgfmtContratoStatusTipo')->findOneBy(array('codigo' => $codStatus));
	
		$oContrato->setCodFormando($oFormando);
		$oContrato->setCodFormaPagamento($oFormaPag);
		$oContrato->setCodOrganizacao($oOrg);
		$oContrato->setNumMeses(sizeof($aData));
		$oContrato->setCodTipoContrato($oTipoContrato);
		$oContrato->setCodStatus($oStatusContrato);
		$em->persist($oContrato);
	
		#################################################################################
		## Excluir as parcelas existentes
		#################################################################################
		if ($oContrato->getCodigo()) {
			$oParcelas		= $em->getRepository('Entidades\ZgfmtContratoFormandoParcela')->findBy(array('codContrato' => $oContrato->getCodigo()));
			for ($j = 0; $j < sizeof($oParcelas); $j++) {
				$em->remove($oParcelas[$j]);
			}
		}
	
		#################################################################################
		## Excluir as participações nos eventos caso haja
		#################################################################################
		if ($oContrato->getCodigo()) {
			$oEventos		= $em->getRepository('Entidades\ZgfmtEventoParticipacao')->findBy(array('codOrganizacao' => $system->getCodOrganizacao(),'codFormando' => $oFormando->getCodigo()));
			for ($j = 0; $j < sizeof($oEventos); $j++) {
				$em->remove($oEventos[$j]);
			}
		}
		
		#################################################################################
		## Salvar as parcelas
		#################################################################################
		for ($j = 0; $j < sizeof($aValor); $j++) {
			$oParcela		= new \Entidades\ZgfmtContratoFormandoParcela();
			$oParcela->setCodContrato($oContrato);
			$oParcela->setDataVencimento(\DateTime::createFromFormat($system->config["data"]["dateFormat"], $aData[$j]));
			$oParcela->setParcela(($j+1));
			$oParcela->setValor(\Zage\App\Util::to_float($aValor[$j]));
			$em->persist($oParcela);
		}
		
		#################################################################################
		## Salvar a desistência e a participação nos eventos caso o tipo de participação seja parcial
		#################################################################################
		if ($codTipoContrato == "P") {

			#################################################################################
			## Calcular o tipo de desistência
			#################################################################################
			$codTipoDesistencia	= "P";
			$codTipoBaseCalculo	= "O";
			
			#################################################################################
			## Resgata os objetos (chave estrangeiras)
			#################################################################################
			$oTipoBase		= $em->getReference('Entidades\ZgfmtBaseCalculoTipo' 			,$codTipoBaseCalculo);
			$oTipoDes		= $em->getRepository('Entidades\ZgfmtDesistenciaTipo')->findOneBy(array('codigo' => $codTipoDesistencia));
			
			#################################################################################
			## Verificar se a desistência já está cadastrada, se não tiver gerar a desistência
			#################################################################################
			$desistencia		= $em->getRepository('Entidades\ZgfmtDesistencia')->findOneBy(array('codOrganizacao' => $system->getCodOrganizacao(),'codFormando' => $oFormando->getCodigo()));
			if (!$desistencia)	{
				$desistencia		= new \Entidades\ZgfmtDesistencia();
				$desistencia->setDataDesistencia(new \DateTime());
				$desistencia->setPctMulta(0);
				$desistencia->setValorMulta(0);
				$desistencia->setCodTransacao(null);
			}
			
			#################################################################################
			## Coloca na fila do doctrine
			#################################################################################
			$desistencia->setCodFormando($oFormando);
			$desistencia->setCodOrganizacao($oOrg);
			$desistencia->setCodTipoBaseCalculo($oTipoBase);
			$desistencia->setCodTipoDesistencia($oTipoDes);
			$em->persist($desistencia);
				
			for ($j = 0; $j < sizeof($aSelEventos); $j++) {
				#################################################################################
				## Resgata os objetos (chave estrangeiras)
				#################################################################################
				$oEvento		= $em->getReference('Entidades\ZgfmtEvento'				,$aSelEventos[$j]);

				$eventoPart		= new \Entidades\ZgfmtEventoParticipacao();
				$eventoPart->setCodEvento($oEvento);
				$eventoPart->setCodFormando($oFormando);
				$eventoPart->setCodOrganizacao($oOrg);
				$eventoPart->setDataCadastro(new \DateTime());
				$em->persist($eventoPart);
			}
		}
		
	} catch (\Exception $e) {
		//$em->getConnection()->rollback();
		echo '1'.\Zage\App\Util::encodeUrl('||'.htmlentities($e->getMessage()));
		exit;
	}
}

#################################################################################
## Salvar no banco
#################################################################################
try {

	$em->flush();
	$em->clear();
} catch (\Exception $e) {
	echo '1'.\Zage\App\Util::encodeUrl('||'.htmlentities($e->getMessage()));
	exit;
}
	
$mensagem	= $tr->trans("Contrato salvo com sucesso");
echo '0'.\Zage\App\Util::encodeUrl('||'.htmlentities($mensagem));