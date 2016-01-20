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
## Verificar parâmetro obrigatório
#################################################################################
if (!isset($codConta)) \Zage\App\Erro::halt('Falta de Parâmetros 2');

#################################################################################
## Resgata os parâmetros passados pelo id
#################################################################################
if (!isset($tipoMidia)) 		$tipoMidia		= "PDF";
if (!isset($instrucao)) 		$instrucao		= null;
if (!isset($email)) 			$email			= null;

#################################################################################
## Inicializa o html
#################################################################################
$htmlBol	= '';
$parcelas	= '';

#################################################################################
## Instancia o objeto do contas a receber
#################################################################################
$contaRec	= new \Zage\Fin\ContaReceber();

#################################################################################
## Calcula a data de hoje
#################################################################################
$hoje		= date($system->config["data"]["dateFormat"]);

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
$vencimento				= ($oConta->getDataVencimento() != null) 		? $oConta->getDataVencimento()->format($system->config["data"]["dateFormat"]) : null;
	
#################################################################################
## Verificar se a conta está atrasada e calcular o júros e mora caso existam
#################################################################################
$saldoDet			= $contaRec->getSaldoAReceberDetalhado($oConta->getCodigo());
if (\Zage\Fin\ContaReceber::estaAtrasada($oConta->getCodigo(), $hoje) == true) {
	
	#################################################################################
	## Calcula os valor através da data de referência
	#################################################################################
	$valorJuros		= \Zage\Fin\ContaReceber::calculaJurosPorAtraso($oConta->getCodigo(), $hoje);
	$valorMora		= \Zage\Fin\ContaReceber::calculaMoraPorAtraso($oConta->getCodigo(), $hoje);

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
$juros					= $valorJuros;
$mora					= $valorMora;
$desconto				= 0;
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
	$sequencial 		= \Zage\Fin\ContaReceber::geraNossoNumero($oConta->getCodigo());
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
$codCedente			= ($codContaRec->getCodigoCedente()) ? $codContaRec->getCodigoCedente() : null;
	
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
$boleto->setCodigoCedente($codCedente);
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
$htmlBol	.= $boleto->getHtml();

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

$parcelas		= substr($parcelas,0 ,-1);
$textoParcela	= "";
$textoParcela .= $parcelas;

$textoParcela	.= " de ".$oConta->getNumParcelas()."";
$oOrg			= $em->getRepository('Entidades\ZgadmOrganizacao')->findOneBy(array('codigo' => $system->getCodOrganizacao()));
$urlOrg			= ROOT_URL . $oOrg->getIdentificacao();

$output		 	= new TempFile();
$input 			= new TempFile($htmlBol, 'html');
$converter 		= new PhantomJS();
$converter->addSearchPath(CLASS_PATH . "/H2P/bin/phantomjs");
$converter->convert($input, $output);

if ($tipoMidia == "PDF") {
	\Zage\App\Util::sendHeaderDownload("boleto.pdf","PDF");
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
	$tpl->set('NOME'				,$sacadoNome);
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
	$mail->setSubject("<SF> Boleto referente a fatura: ".$demonstrativo1);
	
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