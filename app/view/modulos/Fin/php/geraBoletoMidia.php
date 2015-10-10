<?php
#################################################################################
## Includes
#################################################################################
if (defined('DOC_ROOT')) {
	include_once(DOC_ROOT . 'include.php');
}else{
	include_once('../include.php');
}

//use \OpenBoleto\Banco\Itau;
//use \OpenBoleto\Agente;
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
## Verificar parâmetros
#################################################################################
if (!is_array($aVenc) 			&& sizeof($codContaSel) > 1)  \Zage\App\Erro::halt('Parâmetro 2 incorreto');
if (!is_array($aValor) 			&& sizeof($codContaSel) > 1)  \Zage\App\Erro::halt('Parâmetro 3 incorreto');
if (!is_array($aValorJuros)		&& sizeof($codContaSel) > 1)  \Zage\App\Erro::halt('Parâmetro 4 incorreto');
if (!is_array($aValorMora) 		&& sizeof($codContaSel) > 1)  \Zage\App\Erro::halt('Parâmetro 5 incorreto');
if (!is_array($aValorDesconto) 	&& sizeof($codContaSel) > 1)  \Zage\App\Erro::halt('Parâmetro 6 incorreto');
if (!is_array($aValorOutros) 	&& sizeof($codContaSel) > 1)  \Zage\App\Erro::halt('Parâmetro 7 incorreto');

#################################################################################
## Corrige os campos que não são arrays
#################################################################################
if (!is_array($aVenc) 			)  $aVenc			= array($codContaSel[0] => $aVenc);
if (!is_array($aValor) 			)  $aValor			= array($codContaSel[0] => $aValor);
if (!is_array($aValorJuros)		)  $aValorJuros		= array($codContaSel[0] => $aValorJuros);
if (!is_array($aValorMora) 		)  $aValorMora		= array($codContaSel[0] => $aValorMora);
if (!is_array($aValorDesconto) 	)  $aValorDesconto	= array($codContaSel[0] => $aValorDesconto);
if (!is_array($aValorOutros) 	)  $aValorOutros	= array($codContaSel[0] => $aValorOutros);

#################################################################################
## Inicializa o html
#################################################################################
$htmlBol	= '';
$parcelas	= '';

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
	
		if ($codContaRec->getCodTipo()->getCodigo() !== "CC") {
			\Zage\App\Erro::halt('Conta não é do tipo conta corrente!!!');
		}
	
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
	## Formata as informações de vencimento / Valor
	#################################################################################
	$vencimento				= $aVenc[$codConta];
	$valor					= \Zage\App\Util::to_float($aValor[$codConta]);
	$juros					= \Zage\App\Util::to_float($aValorJuros[$codConta]);
	$mora					= \Zage\App\Util::to_float($aValorMora[$codConta]);
	$desconto				= \Zage\App\Util::to_float($aValorDesconto[$codConta]);
	$outros					= \Zage\App\Util::to_float($aValorOutros[$codConta]);
	$especie				= ($oConta->getCodMoeda()) ? $oConta->getCodMoeda()->getCodInternacional() : null;
	//$especieDoc				= ($oConta->getCodMoeda()) ? $oConta->getCodMoeda()->getSimbolo() 	: null;
	$especieDoc				= "DM"; # Duplicata Mercantil
	
	if (!$juros)			$juros		= 0;
	if (!$mora)				$mora		= 0;
	if (!$desconto)			$desconto	= 0;
	if (!$outros)			$outros		= 0;
	
	#################################################################################
	## Verifica se a conta já gerou o Sequencial do nosso número
	#################################################################################
	$sequencial			= $oConta->getSequencialNossoNumero();
	if (!$sequencial)		{
		$sequencial 		= \Zage\Fin\ContaReceber::geraNossoNumero($codConta);
		$oConta->setSequencialNossoNumero($sequencial);
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
		$textoJuros		= \Zage\App\Util::to_money($valJuros);
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