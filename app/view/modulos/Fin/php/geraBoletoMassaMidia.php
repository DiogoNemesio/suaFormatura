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
use \Zend\Mail;
use \Zend\Mail\Message;
use \Zend\Mime\Message as MimeMessage;
use \Zend\Mime\Part as MimePart;
Use \Zend\Mime;

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


#################################################################################
## Resgata os parâmetros passados pelo formulario
#################################################################################
if (isset($_POST['codContaSel']))		$codContaSel		= \Zage\App\Util::antiInjection($_POST['codContaSel']);
if (isset($_POST['vencimento'])) 		$aVenc				= \Zage\App\Util::antiInjection($_POST['vencimento']);
if (isset($_POST['valor'])) 			$aValor				= \Zage\App\Util::antiInjection($_POST['valor']);
if (isset($_POST['valorJuros'])) 		$aValorJuros		= \Zage\App\Util::antiInjection($_POST['valorJuros']);
if (isset($_POST['valorMora'])) 		$aValorMora			= \Zage\App\Util::antiInjection($_POST['valorMora']);
if (isset($_POST['valorDesconto'])) 	$aValorDesconto		= \Zage\App\Util::antiInjection($_POST['valorDesconto']);
if (isset($_POST['valorOutros'])) 		$aValorOutros		= \Zage\App\Util::antiInjection($_POST['valorOutros']);
if (isset($_POST['tipoMidia'])) 		$tipoMidia			= \Zage\App\Util::antiInjection($_POST['tipoMidia']);
if (isset($_POST['instrucao'])) 		$instrucao			= \Zage\App\Util::antiInjection($_POST['instrucao']);
if (isset($_POST['email'])) 			$email				= \Zage\App\Util::antiInjection($_POST['email']);

#################################################################################
## Verificar parâmetro obrigatório
#################################################################################
if (!isset($codContaSel)) \Zage\App\Erro::halt('Falta de Parâmetros 2');

if (!is_array($codContaSel)) \Zage\App\Erro::halt('Parâmetros incorretos');

#################################################################################
## Inicializa o html
#################################################################################
$htmlBol	= '';

#################################################################################
## Instancia o objeto do contas a receber
#################################################################################
$contaRec	= new \Zage\Fin\ContaReceber();

#################################################################################
## Calcula a data de hoje
#################################################################################
$hoje		= date($system->config["data"]["dateFormat"]);


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
	## Verificar se a conta está atrasada e calcular o juros e mora caso existam
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
		## Resgata o valor de juros da conta
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
	$descConta			= $oConta->getDescricao();
	$demonstrativo1		= $descConta;
	$demonstrativo2		= "Parcela: ".$parcela;
	$numeroDoc			= $oConta->getCodigo();
	$valorBoleto		= $valor + $outros;

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
	
	$instrucao1			= "Após o dia ".$vencimento." cobrar ".$textoMora." de Mora e ".$textoJuros." de juros ao dia";
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
	$boleto->setValor($valorBoleto);
	$boleto->setJuros($juros);
	$boleto->setMora($mora);
	$boleto->setDesconto($desconto);
	$boleto->setOutrosValores(0);
	$boleto->setVencimento($vencimento);
	$boleto->setInstrucao1($instrucao1);
	$boleto->setInstrucao2($instrucao2);
	$boleto->setInstrucao3($instrucao3);
	$boleto->setInstrucao4($instrucao4);

	#################################################################################
	## Emitir o boleto, ou seja gerar o código html do mesmo
	#################################################################################
	$boleto->emitir();
	if ($tipoMidia == "PDF") {
		$htmlBol	.= $boleto->getHtml();
		if ($i < sizeof($codContaSel) -1) $htmlBol	.= '<div style="page-break-after:always;">&nbsp;</div>';
	}else{
		$htmlBol	= $boleto->getHtml();
	}
	
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
	$oUser				= $em->getReference("\Entidades\ZgsegUsuario", $_user->getCodigo());
	$hist				= new \Entidades\ZgfinBoletoHistorico();
	$hist->setCodConta($oConta);
	$hist->setCodUsuario($oUser);
	$hist->setData(new \DateTime());
	$hist->setDesconto($desconto);
	$hist->setJuros($juros);
	$hist->setOutros($outros);
	$hist->setLinhaDigitavel($linhaDigitavel);
	$hist->setNossoNumero($nossoNumero);
	$hist->setMora($mora);
	$hist->setValor($valor);
	$hist->setVencimento(\DateTime::createFromFormat($system->config["data"]["dateFormat"], $vencimento));
	$hist->setMidia($tipoMidia);
	$hist->setEmail($email);
	$em->persist($hist);
	
	if ($tipoMidia != "PDF") {
		
		#################################################################################
		## Verifica se a conta tem um cliente associado
		#################################################################################
		$codPessoa		= ($oConta->getCodPessoa()) ? $oConta->getCodPessoa()->getCodigo() : null;
		if ($codPessoa) {
		
			#################################################################################
			## Monta as variáveis para o envio da notificação
			#################################################################################
			$textoParcela	= "Boleto referente a parcela (".$parcela.")";
			$oOrg			= $em->getRepository('Entidades\ZgadmOrganizacao')->findOneBy(array('codigo' => $system->getCodOrganizacao()));
			$urlOrg			= ROOT_URL . $oOrg->getIdentificacao();
			
			$output		 	= new TempFile();
			$input 			= new TempFile($htmlBol, 'html');
			$converter 		= new PhantomJS();
			$converter->addSearchPath(CLASS_PATH . "/H2P/bin/phantomjs");
			$converter->convert($input, $output);
			
			#################################################################################
			## Localiza o template do Boleto por e-mail
			#################################################################################
			$template		= $em->getRepository('\Entidades\ZgappNotificacaoTemplate')->findOneBy(array('template' => 'BOLETO_MAIL'));
				
			#################################################################################
			## Cadastrar a notificação
			#################################################################################
			$notificacao	= new \Zage\App\Notificacao(\Zage\App\Notificacao::TIPO_MENSAGEM_TEMPLATE, \Zage\App\Notificacao::TIPO_DEST_PESSOA);
			$notificacao->setAssunto("Boleto referente a fatura: ".$demonstrativo1);
			//$notificacao->setCodUsuario($_user);
			$notificacao->associaPessoa($codPessoa);
			$notificacao->enviaEmail();
			$notificacao->setCodTemplate($template);
			$notificacao->adicionaVariavel("ID", $id);
			$notificacao->adicionaVariavel("TEXTO_PARCELA", $textoParcela);
			$notificacao->adicionaVariavel("DESC_CONTA", $descConta);
			$notificacao->adicionaVariavel("URL_ORG", $urlOrg);
			
			#################################################################################
			## Anexar o PDF
			#################################################################################
			$fileContent 				= $output->getContent();
			$notificacao->anexarArquivo('boleto.pdf', $fileContent);
	
			#################################################################################
			## Colocar com cópia o e-mail, caso tenha sido informado
			#################################################################################
			if (isset($email) && $email) {
				$notificacao->setEmail($email);
			}
			
			#################################################################################
			## Salva os dados
			#################################################################################
			try {
				$notificacao->salva();
				$em->flush();
				
			} catch (Exception $e) {
				$log->err("Erro ao salvar a notificação:". $e->getMessage());
				throw new \Exception("Erro ao salvar a notificação, a mensagem foi para o log dos administradores, entre em contato para mais detalhes !!!");
			}
		}
	}
}

$em->clear();

if ($tipoMidia == "PDF") {
	$output		 	= new TempFile();
	$input 			= new TempFile($htmlBol, 'html');
	$converter 		= new PhantomJS();
	$converter->addSearchPath(CLASS_PATH . "/H2P/bin/phantomjs");
	$converter->convert($input, $output);
	
	\Zage\App\Util::sendHeaderPDF("boleto.pdf");
	echo $output->getContent();
}else{
	
	$system->criaAviso(\Zage\App\Aviso\Tipo::INFO,$tr->trans("Email enviado com sucesso !!!"));
}