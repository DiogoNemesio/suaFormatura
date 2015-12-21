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

#################################################################################
## Resgata os parâmetros passados pelo formulario
#################################################################################
if (isset($_GET['codVersaoOrc'])) 		$codVersaoOrc		= \Zage\App\Util::antiInjection($_GET['codVersaoOrc']);
if (isset($_GET['via'])) 				$via				= \Zage\App\Util::antiInjection($_GET['via']);
if (isset($_GET['email'])) 				$email				= \Zage\App\Util::antiInjection($_GET['email']);

#################################################################################
## Valida os parâmetros
#################################################################################
if (!isset($codVersaoOrc) || (!$codVersaoOrc)) 	\Zage\App\Erro::halt('Parâmetro incorreto');
if (!isset($via) || (!$via)) 	$via		= "PDF";


#################################################################################
## Resgata as informações do orçamento
#################################################################################
$orcamento			= $em->getRepository('Entidades\ZgfmtOrcamento')->findOneBy(array('codigo' => $codVersaoOrc));
$codPlanoOrc		= $orcamento->getCodPlanoOrc()->getCodigo();
$numFormandos		= $orcamento->getQtdeFormandos();
$numConvidados		= $orcamento->getQtdeConvidados();
$indAceite			= $orcamento->getIndAceite();


#################################################################################
## Resgata as informações da Turma
#################################################################################
try {
	$oOrgFmt	= $em->getRepository('Entidades\ZgfmtOrganizacaoFormatura')->findOneBy(array('codOrganizacao' => $system->getCodOrganizacao()));
	$oOrg		= $em->getRepository('Entidades\ZgadmOrganizacao')->findOneBy(array('codigo' => $system->getCodOrganizacao()));

} catch (\Exception $e) {
	\Zage\App\Erro::halt($e->getMessage());
}

#################################################################################
## LogoMarca
#################################################################################
$oFmtAdm		= \Zage\Fmt\Formatura::getCerimonalAdm($system->getCodOrganizacao());
$logoOrg		= ($oFmtAdm) ? $oFmtAdm : $oOrg;	

#################################################################################
## Verifica se tem logomarca
#################################################################################
$temLogo		= $em->getRepository('Entidades\ZgadmOrganizacaoLogo')->findOneBy(array('codOrganizacao' => $logoOrg->getCodigo()));
$logoUrl		= ($temLogo) ? ROOT_URL . "/Adm/mostraLogoOrganizacao.php?codOrganizacao=".$logoOrg->getCodigo() : null; 
		
#################################################################################
## Formata as informações do Orçamento
#################################################################################
$nome			= $oOrg->getNome();
$numMeses		= (int) $orcamento->getNumMeses();
$dataConclusao	= ($oOrgFmt->getDataConclusao() != null) ? $oOrgFmt->getDataConclusao()->format($system->config["data"]["dateFormat"]) : null;

#################################################################################
## Criar o relatório
#################################################################################
$rel	= new \Zage\App\Relatorio();
$rel->use_kwt = true;

#################################################################################
## Criação do cabeçalho
#################################################################################
if ($logoUrl)		{
	$rel->_logo		= $logoUrl;
	$rel->adicionaCabecalho(null);
}

#################################################################################
## Criação do Rodapé
#################################################################################
$rel->adicionaRodape();

#################################################################################
## Inicia o html
#################################################################################
$html		= "";

#################################################################################
## Monta a logo marca
#################################################################################
//if ($logoUrl) $html		.= '<h6 align="center"><img src="'.$logoUrl.'" align="center" style=""/></h6>';

#################################################################################
## Monta os dados iniciais
#################################################################################
$html		.= '<h3 align="center"><b>'.$nome.'</b></h3>';
$html		.= '<h6 align="center">Orçamento versão:&nbsp;'.$orcamento->getVersao().'</h6>';
$html		.= '<table align="center" class="table table-condensed" style="width: 80%; align: center;">';
$html		.= '<thead>';
$html		.= '<tr>
					<th style="text-align: center;"><strong>Número de Formandos</strong></th>
					<th style="text-align: center;"><strong>Convites por formando</strong></th>
					<th style="text-align: center;"><strong>Número de Pessoas</strong></th>
				</tr>';
$html		.= '</thead><tbody>';
$html		.= '<tr>
					<th style="text-align: center;">'.$numFormandos.'</th>
					<th style="text-align: center;">'.$numConvidados.'</th>
					<th style="text-align: center;">'.($numFormandos * $numConvidados).'</th>
				</tr>
				</tbody></table>';

#################################################################################
## Carrega o orçamento salvo
#################################################################################
$orcItens		= $em->getRepository('Entidades\ZgfmtOrcamentoItem')->findBy(array('codOrcamento' => $codVersaoOrc));

#################################################################################
## Monta um array com os itens salvos
#################################################################################
$aItens			= array();
for ($i = 0; $i < sizeof($orcItens); $i++) {
	$item		= $orcItens[$i]->getCodItem();
	$codTipo	= $item->getCodGrupoItem()->getCodigo();
	$codigo		= $item->getCodigo();
	$aItens[$codTipo]["DESCRICAO"]						= $item->getCodGrupoItem()->getDescricao();
	$aItens[$codTipo]["ITENS"][$codigo]["CODIGO"] 		= $item->getCodigo();
	$aItens[$codTipo]["ITENS"][$codigo]["TIPO"] 		= $item->getCodTipoItem()->getCodigo();
	$aItens[$codTipo]["ITENS"][$codigo]["ITEM"] 		= $item->getItem();
	$aItens[$codTipo]["ITENS"][$codigo]["QTDE"] 		= $orcItens[$i]->getQuantidade();
	$aItens[$codTipo]["ITENS"][$codigo]["VALOR"] 		= \Zage\App\Util::to_float($orcItens[$i]->getValorUnitario());
	$aItens[$codTipo]["ITENS"][$codigo]["OBS"] 			= $orcItens[$i]->getTextoDescritivo();
	$aItens[$codTipo]["ITENS"][$codigo]["TOTAL"]		= \Zage\App\Util::to_float($orcItens[$i]->getQuantidade() * \Zage\App\Util::to_float($orcItens[$i]->getValorUnitario()));
	$aItens[$codTipo]["ITENS"][$codigo]["CORTESIA"] 	= ($orcItens[$i]->getCodTipoCortesia()) ? $orcItens[$i]->getCodTipoCortesia()->getTexto() : null;
	
}

#################################################################################
## Cria o html dinâmico
#################################################################################
$htmlForm	= '';
$htmlForm	.= '<h5 align="center"><b>Detalhes dos eventos</b></h5>';
$htmlForm	.= '<center>';

$w1			= "width: 30%;";
$w2			= "width: 20%;";
$w3			= "width: 20%;";
$w4			= "width: 20%;";

$aTotal		= array();
$valorTotal	= 0;

foreach ($aItens as $codTipo => $aItem)	{
	$htmlForm	.= '<h5 align="left"><b>'.$aItem["DESCRICAO"].'</b></h5>';

	#################################################################################
	## Montar a tabela de itens
	#################################################################################
	$tipoItens	= $aItem["ITENS"];
	if (sizeof($tipoItens) > 0) {
		$htmlForm	.= '<div align="center">';
		$htmlForm	.= '<table class="table table-bordered1"><thead>';
		$htmlForm	.= '<tr>';
		$htmlForm	.= '<th style="text-align: left; '.$w1.' border-left: 1px solid #000000; border-bottom: 1px solid #000000; border-top: 1px solid #000000;">ITEM</th>';
		$htmlForm	.= '<th style="text-align: center; '.$w2.' border-bottom: 1px solid #000000; border-top: 1px solid #000000;">Quantidade</th>';
		$htmlForm	.= '<th style="text-align: right; '.$w3.' border-bottom: 1px solid #000000; border-top: 1px solid #000000;">VALOR</th>';
		$htmlForm	.= '<th style="text-align: right; '.$w4.' border-right: 1px solid #000000; border-bottom: 1px solid #000000; border-top: 1px solid #000000;">TOTAL</th>';
		$htmlForm	.= '</tr>';
		$htmlForm	.= '</thead><tbody>';
		
		$totalTipo	= 0;

		foreach ($tipoItens as $codItem => $item) {
			
			if ($item["OBS"]) {
				$bdBottom	= null;
			}else{
				$bdBottom	= "border-bottom: 1px solid #000000;";
			}
			
			$valItem	= ($item["VALOR"]) ? \Zage\App\Util::to_money($item["VALOR"]) : $item["CORTESIA"];
			$totItem	= ($item["TOTAL"]) ? \Zage\App\Util::to_money($item["TOTAL"]) : $item["CORTESIA"];
			
			$htmlForm	.= '<tr>';
			$htmlForm	.= '<td style="text-align: left; '.$w1.' border-left: 1px solid #000000; '.$bdBottom.' border-top: 1px solid #000000;">'.$item["ITEM"].'</td>';
			$htmlForm	.= '<td style="text-align: center; '.$w2.' '.$bdBottom.' border-top: 1px solid #000000;">'.$item["QTDE"].' </td>';
			$htmlForm	.= '<td style="text-align: right; '.$w3.' '.$bdBottom.' border-top: 1px solid #000000;">'.$valItem.'</td>';
			$htmlForm	.= '<td style="text-align: right; '.$w4.' border-right: 1px solid #000000; '.$bdBottom.' border-top: 1px solid #000000;">'.$totItem.'</td>';
			$htmlForm	.= '</tr>';
			
			if ($item["OBS"]) {
				$htmlForm	.= '<tr>';
				$htmlForm	.= '<td style="border-bottom: 1px solid #000000; border-right: 1px solid #000000; border-left: 1px solid #000000;" colspan="4">'.$item["OBS"].'</td>';
				$htmlForm	.= '</tr>';
			}
			$totalTipo	+= $item["TOTAL"];
			$valorTotal	+= $item["TOTAL"];
		}
		$htmlForm	.= '</tbody>';
		//$htmlForm	.= '<tfoot>';
		$htmlForm	.= '<tr><th style="text-align: right;" colspan="3">Total: </th>';
		$htmlForm	.= '<th style="text-align: right; '.$w4.'">'.\Zage\App\Util::to_money($totalTipo).'</th></tr>';
		//$htmlForm	.= '</tfoot>';
		$htmlForm	.= '</tbody>';
		$htmlForm	.= '</table></div>';
		$aTotal[$aItem["DESCRICAO"]]["VALOR"]	= $totalTipo;
		$aTotal[$aItem["DESCRICAO"]]["EVENTO"]	= $aItem["DESCRICAO"];
	}
}

#################################################################################
## Calculo final do custo
#################################################################################
$taxaSistema	= \Zage\App\Util::to_float($orcamento->getTaxaSistema());
$totalSistema	= ($numMeses * $taxaSistema);
$textoSistema	= ($totalSistema) ? \Zage\App\Util::to_money($totalSistema).' por formando' : "cortesia";
$valorFinal		= ($valorTotal + ($totalSistema * $numFormandos)); 
$totalFormando	= round(($valorFinal / $numFormandos),2);
$mensalidade	= $totalFormando / $numMeses;

$htmlForm	.= '<div style="width: 50%; page-break-inside: avoid;">';
//$htmlForm	.= '<div style="float: left; width: 48%; page-break-inside: avoid;">';
$htmlForm	.= '<table class="table" style="width: 100%; page-break-inside: avoid;"><thead>';
$htmlForm	.= '<tr><th style="text-align: left; border-left: 1px solid #000000; border-bottom: 1px solid #000000; border-top: 1px solid #000000;">Evento</th><th style="text-align: right; border-bottom: 1px solid #000000; border-top: 1px solid #000000; border-right: 1px solid #000000;">Valor do Evento</th></tr>';
$htmlForm	.= '</thead>';
$htmlForm	.= '<tbody>';
foreach ($aTotal as $evento) {
	$htmlForm	.= '<tr><td style="text-align: left; border-left: 1px solid #000000; border-bottom: 1px solid #000000; border-top: 1px solid #000000;">'.$evento["EVENTO"].'</td><td style="text-align: right; border-bottom: 1px solid #000000; border-top: 1px solid #000000; border-right: 1px solid #000000;">'.\Zage\App\Util::to_money($evento["VALOR"]).'</td></tr>';	
}
$htmlForm	.= '</tbody>';
$htmlForm	.= '<tfoot>';
$htmlForm	.= '<tr><th style="text-align: left; border-left: 1px solid #000000; border-bottom: 1px solid #000000; border-top: 1px solid #000000;">Total dos Eventos</th><th style="text-align: right; border-bottom: 1px solid #000000; border-top: 1px solid #000000; border-right: 1px solid #000000;">'.\Zage\App\Util::to_money($valorTotal).'</th></tr>';	
$htmlForm	.= '</tfoot>';
$htmlForm	.= '</table>';
$htmlForm	.= '</div>';


$htmlForm	.= '<div style="width: 50%; page-break-inside: avoid;" align="center">';
//$htmlForm	.= '<div style="float: right; width: 48%; page-break-inside: avoid;">';
$htmlForm	.= '<table class="table" align="center" style="width: 100%; page-break-inside: avoid;"><thead>';
$htmlForm	.= '<tr><th colspan="2" style="text-align: center; border-left: 1px solid #000000; border-bottom: 1px solid #000000; border-top: 1px solid #000000; border-right: 1px solid #000000;">Resumo Geral</th></tr>';
$htmlForm	.= '</thead>'; 
$htmlForm	.= '<tbody>';
$htmlForm	.= '<tr><td style="text-align: left; border-left: 1px solid #000000; border-bottom: 1px solid #000000; border-top: 1px solid #000000;"><b>Data de conclusão<b>	</td><td style="text-align: right; border-bottom: 1px solid #000000; border-top: 1px solid #000000; border-right: 1px solid #000000; border-left: 1px solid #000000;">'.$dataConclusao.' ('.$numMeses.' meses previstos)&nbsp;<i>&#10004</i></td></tr>';
$htmlForm	.= '<tr><td style="text-align: left; border-left: 1px solid #000000; border-bottom: 1px solid #000000; border-top: 1px solid #000000;"><b>Portal SuaFormatura.com<b>	</td><td style="text-align: right; border-bottom: 1px solid #000000; border-top: 1px solid #000000; border-right: 1px solid #000000; border-left: 1px solid #000000;">'.$textoSistema.'&nbsp;<i>&#10004</i></td></tr>';
$htmlForm	.= '<tr><td style="text-align: left; border-left: 1px solid #000000; border-bottom: 1px solid #000000; border-top: 1px solid #000000;"><b>Total por formando<b>	</td><td style="text-align: right; border-bottom: 1px solid #000000; border-top: 1px solid #000000; border-right: 1px solid #000000; border-left: 1px solid #000000;">'.\Zage\App\Util::to_money($totalFormando).' por formando&nbsp;<i>&#10004</i></td></tr>';
$htmlForm	.= '<tr><td style="text-align: left; border-left: 1px solid #000000; border-bottom: 1px solid #000000; border-top: 1px solid #000000;"><b>Sugestão de mensalidade<b> </td><td style="text-align: right; border-bottom: 1px solid #000000; border-top: 1px solid #000000; border-right: 1px solid #000000; border-left: 1px solid #000000;">'.\Zage\App\Util::to_money($mensalidade).' mensais por formando&nbsp;<i>&#10004</i></td></tr>';
$htmlForm	.= '</tbody>';
$htmlForm	.= '<tfoot>';
$htmlForm	.= '<tr><th style="text-align: right; border-left: 1px solid #000000; border-bottom: 1px solid #000000; border-top: 1px solid #000000;">Custo Final Total: </th><th style="text-align: right; border-bottom: 1px solid #000000; border-top: 1px solid #000000; border-right: 1px solid #000000;">'.\Zage\App\Util::to_money($valorFinal).'</th></tr>';
$htmlForm	.= '</tfoot>';
$htmlForm	.= '</table>';
$htmlForm	.= '</div>';

$html		.= $htmlForm;

$rel->WriteHTML($html);


if ($via == "PDF") {
	$rel->Output("Orçamento_Versao_".$orcamento->getVersao().".pdf",'D');
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
	//$tpl->set('TEXTO_PARCELA'		,$textoParcela);
	//$tpl->set('DESC_CONTA'			,$descConta);
	$tpl->set('URL_ORG'				,ROOT_URL);

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
	$fileContent 				= $rel->Output("Orçamento_Versao_".$orcamento->getVersao()."_".$system->getCodOrganizacao().".pdf",'S');
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
	$mail->setSubject("<SF> Orçamento versão ".$orcamento->getVersao());

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
		//throw new \Exception("Erro ao enviar o email, a mensagem foi para o log dos administradores, entre em contato para mais detalhes !!!");
		die ('1'.\Zage\App\Util::encodeUrl('||'.htmlentities($e->getTraceAsString())));
	}

	echo '0'.\Zage\App\Util::encodeUrl('||');
	//$system->criaAviso(\Zage\App\Aviso\Tipo::INFO,$tr->trans("Email enviado com sucesso !!!"));
}


