<?php
#################################################################################
## Includes
#################################################################################
if (defined('DOC_ROOT')) {
	include_once(DOC_ROOT . 'include.php');
}else{
	include_once('../include.php');
}

use \H2P\Converter\PhantomJS;
use \H2P\TempFile;
use \H2P\Request;
use \H2P\Request\Cookie;

#################################################################################
## Variáveis globais
#################################################################################
global $system,$log,$_user,$em,$tr;

#################################################################################
## Resgata a variável ID que está criptografada
#################################################################################
if (isset($_GET['id'])) {
	$id = \Zage\App\Util::antiInjection($_GET["id"]);
}elseif (isset($_POST['id'])) {
	$id = \Zage\App\Util::antiInjection($_POST["id"]);
}elseif (isset($id)) 	{
	$id = \Zage\App\Util::antiInjection($id);
}else{
	\Zage\App\Erro::halt('Falta de Parâmetros');
}

#################################################################################
## Descompacta o ID
#################################################################################
\Zage\App\Util::descompactaId($id);

#################################################################################
## Verifica se o usuário tem permissão no menu
#################################################################################
$system->checaPermissao($_codMenu_);

$log->info("GET ORC: ".serialize($_GET));

#################################################################################
## Resgata os parâmetros passados pelo formulario
#################################################################################
if (isset($_POST['aValor'])) 			$aValor				= \Zage\App\Util::antiInjection($_POST['aValor']);
if (isset($_POST['aQtde'])) 			$aQtde				= \Zage\App\Util::antiInjection($_POST['aQtde']);
if (isset($_POST['numMeses'])) 			$numMeses			= \Zage\App\Util::antiInjection($_POST['numMeses']);
if (isset($_POST['numFormando'])) 		$numFormando		= \Zage\App\Util::antiInjection($_POST['numFormando']);
if (isset($_POST['numConvidado'])) 		$numConvidado		= \Zage\App\Util::antiInjection($_POST['numConvidado']);
if (isset($_POST['dataConclusao'])) 	$dataConclusao		= \Zage\App\Util::antiInjection($_POST['dataConclusao']);
if (isset($_POST['codVersaoOrc'])) 		$codVersaoOrc		= \Zage\App\Util::antiInjection($_POST['codVersaoOrc']);

#################################################################################
## Verificar parâmetro obrigatório
#################################################################################
if (!isset($numFormando)	|| (!$numFormando))		\Zage\App\Erro::halt('Falta de Parâmetros 2');
if (!isset($numConvidado)	|| (!$numConvidado))	\Zage\App\Erro::halt('Falta de Parâmetros 3');
if (!isset($dataConclusao)	|| (!$dataConclusao))	\Zage\App\Erro::halt('Falta de Parâmetros 4');
if (!isset($aValor)			|| (!$aValor))			\Zage\App\Erro::halt('Falta de Parâmetros 5');
if (!isset($aQtde)			|| (!$aQtde))			\Zage\App\Erro::halt('Falta de Parâmetros 6');

#################################################################################
## Verificar parâmetros
#################################################################################
if (!is_array($aValor) 			&& sizeof($aValor) 		< 1)  \Zage\App\Erro::halt('Parâmetro 5 incorreto');
if (!is_array($aQtde) 			&& sizeof($aQtde) 		< 1)  \Zage\App\Erro::halt('Parâmetro 6 incorreto');

#################################################################################
## Inicializa o html
#################################################################################
$html		= '';

#################################################################################
## Calcula a data de hoje
#################################################################################
$hoje		= date($system->config["data"]["dateFormat"]);

#################################################################################
## Resgata as informações da organização
#################################################################################
$oOrg 		= $em->getRepository('Entidades\ZgadmOrganizacao')->findOneBy(array('codigo' => $system->getCodOrganizacao()));

#################################################################################
## Buscar o cerimonial que está administrando
#################################################################################
$oFmtAdm		= \Zage\Fmt\Formatura::getCerimonalAdm($system->getCodOrganizacao());
if (!$oFmtAdm)	\Zage\App\Erro::halt('Formatura não administrada por um cerimonial');

#################################################################################
## Resgata o plano orcamentario
#################################################################################
$planoOrc		= $em->getRepository('Entidades\ZgfmtPlanoOrcamentario')->findOneBy(array('codigo' => $codVersaoOrc));
if (!$planoOrc)	\Zage\App\Erro::halt('Plano de orçamento não encontrado');

#################################################################################
## Resgatar os itens do plano orcamentario
#################################################################################
$planoItens		= $em->getRepository('Entidades\ZgfmtPlanoOrcItem')->findBy(array('codVersao' => $codVersaoOrc,'indAtivo' => 1));
if ((!$planoItens) || (sizeof($planoItens) == 0))	\Zage\App\Erro::halt('Não existem itens ou nenhum item de orçamento está ativo');

#################################################################################
## Resgata as informações do banco
#################################################################################
try {

	$oOrgFmt	= $em->getRepository('Entidades\ZgfmtOrganizacaoFormatura')->findOneBy(array('codOrganizacao' => $system->getCodOrganizacao()));
	$oContrato	= $em->getRepository('\Entidades\ZgadmContrato')->findOneBy(array('codOrganizacao' => $system->getCodOrganizacao()));

} catch (\Exception $e) {
	\Zage\App\Erro::halt($e->getMessage());
}

$ident			= $oOrg->getIdentificacao();
$nome			= $oOrg->getNome();
$instituicao	= $oOrgFmt->getCodInstituicao()->getCodigo();
$curso			= $oOrgFmt->getCodCurso()->getCodigo();
$cidade			= $oOrgFmt->getCodCidade()->getCodigo();
$dataConclusao	= ($oOrgFmt->getDataConclusao() != null) ? $oOrgFmt->getDataConclusao()->format($system->config["data"]["dateFormat"]) : null;

if ($oContrato) {
	$codPlano			= ($oContrato->getCodPlano()) ? $oContrato->getCodPlano()->getCodigo() : null;
	$valorDesconto		= \Zage\App\Util::formataDinheiro($oContrato->getValorDesconto());
	$pctDesconto		= \Zage\App\Util::formataDinheiro($oContrato->getPctDesconto());
	$formaDesc			= ($valorDesconto > 0) ? "V" : "P";
}else{
	$codPlano			= null;
	$formaDesc			= "V";
	$valorDesconto		= 0;
	$pctDesconto		= 0;
}

#################################################################################
## Taxas
#################################################################################
$taxaAdmin				= \Zage\App\Util::to_float($oOrgFmt->getValorPorFormando());
$taxaBoleto				= \Zage\App\Util::to_float($oOrgFmt->getValorPorBoleto());
$taxaUso				= \Zage\App\Util::to_float(\Zage\Adm\Contrato::getValorLicenca($system->getCodOrganizacao()));


#################################################################################
## Montar o array com as informações do Orçamento
#################################################################################
$aItens		= array();
for ($i = 0; $i < sizeof($planoItens); $i++) {
	$codTipo		= $planoItens[$i]->getCodTipoEvento()->getCodigo();
	$codigo			= $planoItens[$i]->getCodigo();
	
	#################################################################################
	## Verificar se foi informado valor nesse item
	#################################################################################
	if (isset($aValor[$codigo]) && $aValor[$codigo] > 0) {
		$aItens[$codTipo]["DESCRICAO"]					= $planoItens[$i]->getCodTipoEvento()->getDescricao();
		$aItens[$codTipo]["ITENS"][$codigo]["CODIGO"] 	= $planoItens[$i]->getCodigo();
		$aItens[$codTipo]["ITENS"][$codigo]["TIPO"] 	= $planoItens[$i]->getCodTipoItem()->getCodigo();
		$aItens[$codTipo]["ITENS"][$codigo]["ITEM"] 	= $planoItens[$i]->getItem();
		$aItens[$codTipo]["ITENS"][$codigo]["QTDE"] 	= \Zage\App\Util::to_float($aQtde[$codigo]);
		$aItens[$codTipo]["ITENS"][$codigo]["VALOR"] 	= \Zage\App\Util::to_float($aValor[$codigo]);
	}
}

print_r($aItens);
exit;

#################################################################################
## Faz o loop nas parcelas das contas
#################################################################################
for ($i = 0; $i < sizeof($codContaSel); $i++) {
	
	$codConta		= $codContaSel[$i]; 
	
	#################################################################################
	## Resgata as informações da conta
	#################################################################################
	$oConta		= $em->getRepository('Entidades\ZgfinContaReceber')->findOneBy(array('codOrganizacao' => $system->getCodOrganizacao(), 'codigo' => $codConta));
	
	if (!$oConta) {
		\Zage\App\Erro::halt('Conta não encontrada');
	}
	
	
	#################################################################################
	## Resgata as informaçoes da conta corrente
	#################################################################################
	$codContaRec		= ($oConta->getCodConta() 			!= null) ? $oConta->getCodConta() 						: null;
	if (!$codContaRec)  {
		\Zage\App\Erro::halt('Conta não possui conta corrente !!!');
	}else{
	
		$codAgencia		= $codContaRec->getCodAgencia();
		if (!$codAgencia) {
			\Zage\App\Erro::halt('Conta corrente não pertence a uma agência !!!');
		}
	
		$banco		= $codAgencia->getCodBanco()->getCodBanco();
	
		if (!$banco) {
			\Zage\App\Erro::halt('Banco da agência não encontrado !!!');
		}
	}
	
	#################################################################################
	## Instancia a classe do boleto
	#################################################################################
	$boleto		= new \Zage\Fin\Boleto($banco);
	
	#################################################################################
	## Resgata as informações do Cedente (Filial)
	#################################################################################
	$cedenteNome		= $oConta->getCodOrganizacao()->getNome();
	$cedenteCNPJ		= \Zage\App\Util::formatCGC($oConta->getCodOrganizacao()->getCgc());
	$cedenteEndereco	= \Zage\Adm\Endereco::formataEndereco($oConta->getCodOrganizacao()->getEndereco(), $oConta->getCodOrganizacao()->getNumero(), $oConta->getCodOrganizacao()->getBairro(),$oConta->getCodOrganizacao()->getComplemento());
	$cedenteCep			= $oConta->getCodOrganizacao()->getCep();
	$cedenteCidade		= ($oConta->getCodOrganizacao()->getCodLogradouro()) ? $oConta->getCodOrganizacao()->getCodLogradouro()->getCodBairro()->getCodLocalidade()->getCodCidade()->getNome() : null;
	$cedenteUF			= ($oConta->getCodOrganizacao()->getCodLogradouro()) ? $oConta->getCodOrganizacao()->getCodLogradouro()->getCodBairro()->getCodLocalidade()->getCodCidade()->getCodUf()->getCodUf() : null;
	
	#################################################################################
	## Resgata as informações do Sacado (Cliente)
	#################################################################################
	if ($oConta->getCodPessoa()) {
		$sacadoNome			= $oConta->getCodPessoa()->getNome();
		$sacadoCNPJ			= \Zage\App\Util::formatCGC($oConta->getCodPessoa()->getCgc());
	
		#################################################################################
		## Busca o Endereço
		#################################################################################
		$oEndSac			= \Zage\Fin\Pessoa::getEndereco($oConta->getCodPessoa()->getCodigo());
	
		if ($oEndSac)		{
			$sacadoEndereco		= \Zage\Adm\Endereco::formataEndereco($oEndSac->getEndereco(), $oEndSac->getNumero(), $oEndSac->getBairro(),$oEndSac->getComplemento());
			$sacadoCep			= $oEndSac->getCep();
			$sacadoCidade		= ($oEndSac->getCodLogradouro()) ? $oEndSac->getCodLogradouro()->getCodBairro()->getCodLocalidade()->getCodCidade()->getNome() : null;
			$sacadoUF			= ($oEndSac->getCodLogradouro()) ? $oEndSac->getCodLogradouro()->getCodBairro()->getCodLocalidade()->getCodCidade()->getCodUf()->getCodUf() : null;
		}else{
			$sacadoEndereco		= null;
			$sacadoCep			= null;
			$sacadoCidade		= null;
			$sacadoUF			= null;
		}
	
	}else{
		$sacadoNome			= null;
		$sacadoCNPJ			= null;
		$sacadoEndereco		= null;
		$sacadoCep			= null;
		$sacadoCidade		= null;
		$sacadoUF			= null;
	
	}
	
	#################################################################################
	## Formata as informações de vencimento 
	#################################################################################
	$vencimento				= $aVenc[$codConta];
	
	#################################################################################
	## Verificar se a conta está atrasada e calcular o júros e mora caso existam
	#################################################################################
	$saldoDet			= $contaRec->getSaldoAReceberDetalhado($codConta);
	if (\Zage\Fin\ContaReceber::estaAtrasada($oConta->getCodigo(), $hoje) == true) {
	
		#################################################################################
		## Calcula os valor através da data de referência
		#################################################################################
		$valorJuros		= \Zage\Fin\ContaReceber::calculaJurosPorAtraso($oConta->getCodigo(), $vencimento);
		$valorMora		= \Zage\Fin\ContaReceber::calculaMoraPorAtraso($oConta->getCodigo(), $vencimento);
	
	}else{
	
		#################################################################################
		## Resgata o valor de júros da conta
		#################################################################################
		$valorJuros		= \Zage\App\Util::to_float($oConta->getValorJuros());
		$valorMora		= \Zage\App\Util::to_float($oConta->getValorMora());
	}
	
	
	#################################################################################
	## Atualiza o saldo a receber
	#################################################################################
	$valorJuros			+= $saldoDet["JUROS"];
	$valorMora			+= $saldoDet["MORA"];
	
	#################################################################################
	## Validação dos valores, não pode receber valores negativos
	#################################################################################
	if ($valorJuros 	< 0)	$valorJuros		= 0;
	if ($valorMora 		< 0)	$valorMora		= 0;
	
	
	#################################################################################
	## Formata as informações de Valor
	#################################################################################
	$valor					= \Zage\App\Util::to_float($saldoDet["PRINCIPAL"]);
	$juros					= \Zage\App\Util::to_float($aValorJuros[$codConta]);
	$mora					= \Zage\App\Util::to_float($aValorMora[$codConta]);
	$desconto				= \Zage\App\Util::to_float($aValorDesconto[$codConta]);
	$outros					= \Zage\App\Util::to_float($saldoDet["OUTROS"]);
	$especie				= ($oConta->getCodMoeda()) ? $oConta->getCodMoeda()->getCodInternacional() : null;
	//$especieDoc				= ($oConta->getCodMoeda()) ? $oConta->getCodMoeda()->getSimbolo() 	: null;
	$especieDoc				= "DM"; # Duplicata Mercantil
	
	if (!$juros)			$juros		= 0;
	if (!$mora)				$mora		= 0;
	if (!$desconto)			$desconto	= 0;
	if (!$outros)			$outros		= 0;
	
	#################################################################################
	## Faz o controle de salvamento da conta
	#################################################################################
	$_salvar				= false;
	
	#################################################################################
	## Verifica se a conta já gerou o Sequencial do nosso número
	#################################################################################
	$sequencial			= $oConta->getSequencialNossoNumero();
	if (!$sequencial)		{
		$sequencial 		= \Zage\Fin\ContaReceber::geraNossoNumero($codConta);
		$oConta->setSequencialNossoNumero($sequencial);
		$_salvar			= true;
	}
	
	#################################################################################
	## Verifica se foi alterado o valor do júros
	#################################################################################
	if ($juros != $valorJuros) {
		if ($juros > $valorJuros) {
			/**
			 * Combinado de não fazer nada, deixar a diferença a maior ir para adiantamento
			#################################################################################
			## Calcula a diferença
			#################################################################################
			$_difJuros	= \Zage\App\Util::to_float($juros - $valorJuros);
					
			#################################################################################
			## Adicionar o valor cobrado a mais de júros na conta
			#################################################################################
			$oConta->setValorJuros(\Zage\App\Util::to_float($oConta->getValorJuros()) + $_difJuros);

			#################################################################################
			## Atualiza a flag para salvar a conta
			#################################################################################
			$_salvar	= true;
			*/
		}else{
			#################################################################################
			## Calcula a diferença
			#################################################################################
			$_difJuros	= \Zage\App\Util::to_float($valorJuros - $juros);
				
			#################################################################################
			## Salvar o valor de desconto de júros
			#################################################################################
			$oConta->setValorDescontoJuros(\Zage\App\Util::to_float($oConta->getValorDescontoJuros()) + $_difJuros);
			
			#################################################################################
			## Atualiza a flag para salvar a conta
			#################################################################################
			$_salvar	= true;
		}
	}
	
	#################################################################################
	## Verifica se foi alterado o valor da mora
	#################################################################################
	if ($mora != $valorMora) {
		if ($mora > $valorMora) {
			
			/**
			 * Combinado de não fazer nada, deixar a diferença a maior ir para adiantamento
			#################################################################################
			## Calcula a diferença
			#################################################################################
			$_difMora	= \Zage\App\Util::to_float($mora - $valorMora);
					
			#################################################################################
			## Adicionar o valor cobrado a mais de mora na conta
			#################################################################################
			$oConta->setValorMora(\Zage\App\Util::to_float($oConta->getValorMora()) + $_difMora);

			#################################################################################
			## Atualiza a flag para salvar a conta
			#################################################################################
			$_salvar	= true;
			**/
		}else{
			#################################################################################
			## Calcula a diferença
			#################################################################################
			$_difMora	= \Zage\App\Util::to_float($valorMora - $mora);
				
			#################################################################################
			## Salvar o valor de desconto da mora
			#################################################################################
			$oConta->setValorDescontoMora(\Zage\App\Util::to_float($oConta->getValorDescontoMora()) + $_difMora);
			
			#################################################################################
			## Atualiza a flag para salvar a conta
			#################################################################################
			$_salvar	= true;
		}
	}
	
	#################################################################################
	## Verifica se é necessário salvar
	#################################################################################
	if ($_salvar)	{
		$em->persist($oConta);
		$em->flush();
	}
	
	#################################################################################
	## Resgata as informações da conta corrente
	#################################################################################
	$agencia			= $codAgencia->getAgencia();
	$agenciaDV			= $codAgencia->getAgenciaDv();
	$ccorrente			= $codContaRec->getCcorrente();
	$ccorrenteDV		= $codContaRec->getCcorrenteDv();
	$carteira			= $codContaRec->getCodCarteira()->getCodCarteira();
	
	#################################################################################
	## Resgata as informações de acréscimos
	#################################################################################
	$valJuros			= \Zage\App\Util::to_float($codContaRec->getValorJuros());
	$valMora			= \Zage\App\Util::to_float($codContaRec->getValorMora());
	$pctJuros			= $codContaRec->getPctJuros();
	$pctMora			= $codContaRec->getPctMora();
	
	if (!$valJuros)		$valJuros	= 0;
	if (!$valMora)		$valMora	= 0;
	if (!$pctJuros)		$pctJuros	= 0;
	if (!$pctMora)		$pctMora	= 0;
	
	#################################################################################
	## Resgata as informações referente ao serviço prestado
	#################################################################################
	$parcela			= $oConta->getParcela() . '/ '.$oConta->getNumParcelas();
	$parcelas			.= $oConta->getParcela().",";
	$descConta			= $oConta->getDescricao();
	$demonstrativo1		= $descConta;
	$demonstrativo2		= "Parcela: ".$parcela;
	$numeroDoc			= $oConta->getCodigo();

	#################################################################################
	## Instruções
	## Dar Prioridada aos valores, depois aos percentuais
	#################################################################################
	if ($valJuros) {
		$textoJuros		= \Zage\App\Util::to_money(($valJuros/30));
	}elseif ($pctJuros) {
		$textoJuros		= round($pctJuros/30,2)."%";
	}
	
	if ($valMora)	{
		$textoMora		= \Zage\App\Util::to_money($valMora);
	}else{
		$textoMora		= $pctMora."%";
	}
	
	$instrucao1			= "Após o dia ".$vencimento." cobrar ".$textoMora." de Mora e ".$textoJuros." de júros ao dia";
	$instrucao2			= $codContaRec->getInstrucao();
	$instrucao3			= $instrucao;
	$instrucao4			= null;
	
	
	#################################################################################
	## Define as informações do boleto
	#################################################################################
	$boleto->setAgencia($agencia);
	$boleto->setAgenciaDigito($agenciaDV);
	$boleto->setCarteira($carteira);
	$boleto->setCedente($cedenteNome);
	$boleto->setCep($cedenteCep);
	$boleto->setCidade($cedenteCidade);
	$boleto->setCnpj($cedenteCNPJ);
	$boleto->setConta($ccorrente);
	$boleto->setContaDigito($ccorrenteDV);
	$boleto->setDemonstrativo1($demonstrativo1);
	$boleto->setDemonstrativo2($demonstrativo2);
	$boleto->setEndereco($cedenteEndereco);
	$boleto->setEspecie($especie);
	$boleto->setEspecieDocumento($especieDoc);
	$boleto->setSequencial($sequencial);
	$boleto->setQuantidade(1);
	$boleto->setNumeroDocumento($numeroDoc);
	$boleto->setSacadoCep($sacadoCep);
	$boleto->setSacadoCidade($sacadoCidade);
	$boleto->setSacadoCNPJ($sacadoCNPJ);
	$boleto->setSacadoEndereco($sacadoEndereco);
	$boleto->setSacadoNome($sacadoNome);
	$boleto->setSacadoUF($sacadoUF);
	$boleto->setUf($cedenteUF);
	$boleto->setValor($valor);
	$boleto->setJuros($juros);
	$boleto->setMora($mora);
	$boleto->setDesconto($desconto);
	$boleto->setOutrosValores($outros);
	$boleto->setVencimento($vencimento);
	$boleto->setInstrucao1($instrucao1);
	$boleto->setInstrucao2($instrucao2);
	$boleto->setInstrucao3($instrucao3);
	$boleto->setInstrucao4($instrucao4);
	
	#################################################################################
	## Emitir o boleto, ou seja gerar o código html do mesmo
	#################################################################################
	$boleto->emitir();
	$htmlBol	.= $boleto->getHtml();
	if ($i < sizeof($codContaSel) -1) $htmlBol	.= '<div style="page-break-after:always;">&nbsp;</div>';

	#################################################################################
	## Salvar a linha digitável e o nosso número
	#################################################################################
	$linhaDigitavel		= $boleto->getLinhaDigitavel();
	$nossoNumero		= $boleto->getNossoNumero();
	$nossoNumeroLimpo	= str_replace(array('.', '/', ' ', '-'), '', $nossoNumero);

	#################################################################################
	## Atualiza o nosso número
	#################################################################################
	$oConta->setNossoNumero($nossoNumeroLimpo);
	$em->persist($oConta);
	$em->flush();
	
	#################################################################################
	## Salvar o histórico do Boleto
	#################################################################################
	$hist				= new \Entidades\ZgfinBoletoHistorico();
	$hist->setCodConta($oConta);
	$hist->setCodUsuario($_user);
	$hist->setData(new \DateTime());
	$hist->setDesconto($desconto);
	$hist->setJuros($juros);
	$hist->setLinhaDigitavel($linhaDigitavel);
	$hist->setNossoNumero($nossoNumero);
	$hist->setMora($mora);
	$hist->setValor($valor);
	$hist->setOutros($outros);
	$hist->setVencimento(\DateTime::createFromFormat($system->config["data"]["dateFormat"], $vencimento));
	$hist->setMidia($tipoMidia);
	$hist->setEmail($email);
	$em->persist($hist);
	$em->flush();
}

$parcelas		= substr($parcelas,0 ,-1);
$textoParcela	= "Boleto referente ";
if (sizeof($codContaSel) > 1) {
	$textoParcela .= "as parcelas (".$parcelas .") ";
}else{
	$textoParcela .= "a parcela ".$parcelas;
}

$textoParcela	.= " de ".$oConta->getNumParcelas()."";
$oOrg			= $em->getRepository('Entidades\ZgadmOrganizacao')->findOneBy(array('codigo' => $system->getCodOrganizacao()));
$urlOrg			= ROOT_URL . $oOrg->getIdentificacao();

$output		 	= new TempFile();
$input 			= new TempFile($htmlBol, 'html');
$converter 		= new PhantomJS();
$converter->addSearchPath(CLASS_PATH . "/H2P/bin/phantomjs");
$converter->convert($input, $output);

if ($tipoMidia == "PDF") {
	\Zage\App\Util::sendHeaderPDF("boleto.pdf");
	echo $output->getContent();
}else{
	#################################################################################
	## Carregando o template html do email
	#################################################################################
	$tpl	= new \Zage\App\Template();
	$tpl->load(MOD_PATH . "/Fin/html/boletoMail.html");
	
	#################################################################################
	## Define os valores das variáveis
	#################################################################################
	$tpl->set('ID'					,$id);
	$tpl->set('TEXTO_PARCELA'		,$textoParcela);
	$tpl->set('DESC_CONTA'			,$descConta);
	$tpl->set('URL_ORG'				,$urlOrg);
	
	#################################################################################
	## Criar os objeto do email ,transporte e validador
	#################################################################################
	$mail 			= \Zage\App\Mail::getMail();
	$transport 		= \Zage\App\Mail::getTransport();
	$validator 		= new \Zend\Validator\EmailAddress();
	$htmlMail 		= new MimePart($tpl->getHtml());
	$htmlMail->type = "text/html";
	$body 			= new MimeMessage();

	#################################################################################
	## Anexar o PDF
	#################################################################################
	$fileContent 				= $output->getContent();
	$attachment 				= new Mime\Part($fileContent);
	$attachment->type 			= 'application/pdf';
	$attachment->filename 		= 'boleto.pdf';
	$attachment->disposition 	= Mime\Mime::DISPOSITION_ATTACHMENT;
	$attachment->encoding 		= Mime\Mime::ENCODING_BASE64;
	
	#################################################################################
	## Definir o conteúdo do e-mail
	#################################################################################
	$body->setParts(array($htmlMail, $attachment));
	$mail->setBody($body);
	$mail->setSubject("<ZageMail> Boleto referente a fatura: ".$demonstrativo1);
	
	#################################################################################
	## Definir os destinatários
	#################################################################################
	$emails		= explode(",",$email);
	for ($j = 0; $j <sizeof($emails); $j++) {
		$_to		= trim($emails[$j]);
		if ($validator->isValid($_to)) {
			$mail->addTo($_to);
		}
	}
	
	#################################################################################
	## Enviar o e-mail
	#################################################################################
	try {
		$transport->send($mail);
	} catch (Exception $e) {
		$log->debug("Erro ao enviar o e-mail:". $e->getTraceAsString());
 		throw new \Exception("Erro ao enviar o email, a mensagem foi para o log dos administradores, entre em contato para mais detalhes !!!");
 	}
	
	//$system->criaAviso(\Zage\App\Aviso\Tipo::INFO,$tr->trans("Email enviado com sucesso !!!"));
}




/*$bolHtml		= $boleto->getOutput();
$output		 	= new TempFile();
$input 			= new TempFile($bolHtml, 'html');
$converter 		= new PhantomJS();
$converter->addSearchPath(CLASS_PATH . "/H2P/bin/phantomjs");
$converter->convert($input, $output);
\Zage\App\Util::sendHeaderPDF("boleto.pdf");
echo $output->getContent();
*/