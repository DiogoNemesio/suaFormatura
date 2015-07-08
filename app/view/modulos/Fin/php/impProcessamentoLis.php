<?php
#################################################################################
## Includes
#################################################################################
if (defined('DOC_ROOT')) {
	include_once(DOC_ROOT . 'include.php');
}else{
	include_once('./include.php');
}

global $em;

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
## Resgata a url desse script
#################################################################################
$url		= ROOT_URL . "/Fin/". basename(__FILE__)."?id=".$id;

#################################################################################
## Executar a acao dos botões
#################################################################################
if (isset($acao) && isset($codigo)) {
	if ($acao == "reprocessar") {
		\Zage\App\Fila::reprocessar($codigo);
		\Zage\App\Fila::alteraLinhaAtual($codigo,0);
	}elseif ($acao == "remover") {
		\Zage\App\Fila::excluir($codigo);
	}elseif ($acao == "cancelar") {
		\Zage\App\Fila::cancelar($codigo);
		\Zage\App\Fila::alteraLinhaAtual($codigo,0);
	}elseif ($acao == "baixar") {
		$nomeArquivo	= "RESUMO.pdf";
		$resumo			= $em->getRepository('Entidades\ZgappFilaImportacaoResumo')->findOneBy(array('codFila' => $codigo));
		if ((!$resumo)) 	exit;
		$conteudo 		= $resumo->getResumo();
		\Zage\App\Util::sendHeaderDownload($nomeArquivo,'pdf');
		echo stream_get_contents($conteudo);
		exit;
	}
}


#################################################################################
## Resgata os dados do grid
#################################################################################
$codAtividade	= \Zage\Utl\Atividade::buscaPorIdentificacao("IMP_RET_BANCARIO");

try {
	$fila	= $em->getRepository('Entidades\ZgappFilaImportacao')->findBy(array('codAtividade' => $codAtividade), array('dataImportacao' => 'ASC')); 
} catch (\Exception $e) {
	\Zage\App\Erro::halt($e->getMessage());
}

$icReproc	= "fa fa-retweet";
$icLog		= "fa fa-file-text";
$icRemove	= "fa fa-trash-o red";
$icCancel	= "fa fa-ban";
$icProc		= "fa fa-refresh fa-spin";

#################################################################################
## Cria o objeto do Grid (bootstrap)
#################################################################################
$grid			= \Zage\App\Grid::criar(\Zage\App\Grid\Tipo::TP_BOOTSTRAP,"GFilaImp");
$grid->adicionaTexto('#'					, 4	,$grid::CENTER	,'codigo');
$grid->adicionaTexto('Nome'					, 9	,$grid::CENTER	,'nome');
$grid->adicionaTexto('Tamanho'				, 7	,$grid::CENTER	,'');
$grid->adicionaTexto('Tipo'					, 6	,$grid::CENTER	,'codTipoArquivo:nome');
$grid->adicionaDataHora('Data'				,10	,$grid::CENTER	,'dataImportacao');
$grid->adicionaTexto('Usuário'				,10	,$grid::CENTER	,'codUsuario:nome');
$grid->adicionaTexto('Status'				,16	,$grid::CENTER	,'codStatus:nome');
$grid->adicionaTexto('Variavel'				,10	,$grid::CENTER	,'variavel');
$grid->adicionaTexto('% Processado'			,10	,$grid::CENTER	,'');
$grid->adicionaIcone("#", $icReproc		,'Reprocessar');
$grid->adicionaIcone("#", $icLog		,'Baixar');
$grid->adicionaIcone("#", $icRemove		,'Remover');
$grid->adicionaIcone("#", $icCancel		,'Cancelar');
$grid->adicionaIcone("#", $icProc		,'Processando');
$grid->importaDadosDoctrine($fila);

for ($i = 0; $i < sizeof($fila); $i++) {

	#################################################################################
	## Calcular os ícones atraves do status
	#################################################################################
	$urlReproc	= ROOT_URL . "/Fin/".'/impProcessamentoLis.php'."?id=". \Zage\App\Util::encodeUrl('_codMenu_='.$_codMenu_.'&_icone_='.$_icone_.'&acao=reprocessar&codigo='.$fila[$i]->getCodigo());
	$urlLog		= 'javascript:zgDownloadUrl(\''.ROOT_URL . "/Fin/".'/impProcessamentoLis.php'."?id=".\Zage\App\Util::encodeUrl('_codMenu_='.$_codMenu_.'&_icone_='.$_icone_.'&acao=baixar&codigo='.$fila[$i]->getCodigo()).'\');';
	$urlRemove	= ROOT_URL . "/Fin/".'/impProcessamentoLis.php'."?id=".\Zage\App\Util::encodeUrl('_codMenu_='.$_codMenu_.'&_icone_='.$_icone_.'&acao=remover&codigo='.$fila[$i]->getCodigo());
	$urlCancel	= ROOT_URL . "/Fin/".'/impProcessamentoLis.php'."?id=".\Zage\App\Util::encodeUrl('_codMenu_='.$_codMenu_.'&_icone_='.$_icone_.'&acao=cancelar&codigo='.$fila[$i]->getCodigo());
	$urlProc	= "#";
	
	if ($fila[$i]->getCodStatus()->getCodigo() == "A") {
		$ic1	= " ";
		$ic2	= " ";
		$ic3	= $icRemove;
		$ic4	= $icCancel;
		$ic5	= " ";
		$url1	= "#";
		$url2	= "#";
		$url3	= $urlRemove;
		$url4	= $urlCancel;
		$url5	= "#";
	}elseif ($fila[$i]->getCodStatus()->getCodigo() == "E") {
		$ic1	= $icReproc;
		$ic2	= $icLog;
		$ic3	= $icRemove;
		$ic4	= " ";
		$ic5	= " ";
		$url1	= $urlReproc;
		$url2	= $urlLog;
		$url3	= $urlRemove;
		$url4	= "#";
		$url5	= "#";
	}elseif ($fila[$i]->getCodStatus()->getCodigo() == "C") {
		$ic1	= $icReproc;
		$ic2	= " ";
		$ic3	= $icRemove;
		$ic4	= " ";
		$ic5	= " ";
		$url1	= $urlReproc;
		$url2	= "#";
		$url3	= $urlRemove;
		$url4	= "#";
		$url5	= "#";
	}elseif ($fila[$i]->getCodStatus()->getCodigo() == "OK") {
		$ic1	= $icReproc;
		$ic2	= " ";
		$ic3	= $icRemove;
		$ic4	= " ";
		$ic5	= " ";
		$url1	= $urlReproc;
		$url2	= "#";
		$url3	= $urlRemove;
		$url4	= "#";
		$url5	= "#";
	}elseif ($fila[$i]->getCodStatus()->getCodigo() == "V") {
		$ic1	= " ";
		$ic2	= " ";
		$ic3	= " ";
		$ic4	= $icCancel;
		$ic5	= $icProc;
		$url1	= "#";
		$url2	= "#";
		$url3	= "#";
		$url4	= $urlCancel;
		$url5	= $urlProc;
	}elseif ($fila[$i]->getCodStatus()->getCodigo() == "AN") {
		$ic1	= " ";
		$ic2	= " ";
		$ic3	= " ";
		$ic4	= $icCancel;
		$ic5	= " ";
		$url1	= "#";
		$url2	= "#";
		$url3	= "#";
		$url4	= $urlCancel;
		$url5	= "#";
	}else{
		$ic1	= " ";
		$ic2	= " ";
		$ic3	= " ";
		$ic4	= " ";
		$ic5	= " ";
		$url1	= "#";
		$url2	= "#";
		$url3	= "#";
		$url4	= "#";
		$url5	= "#";
	}
	
	$perc 		= round($fila[$i]->getLinhaAtual()/$fila[$i]->getNumLinhas()*100);
	$percHtml 	= '<div class="progress progress-striped"><div class="progress-bar progress-bar-success" style="width: '.$perc.'%;"><b>'.$perc.'%</b></div></div>';

	/* Define o Tamanho do arquivo **/
	$grid->setValorCelula($i,2,\Zage\App\Util::mostraTamanhoLegivel($fila[$i]->getBytes(),2));
	
	/* Define o percentual **/
	$grid->setValorCelula($i,8,$percHtml);
	
	/* Define os ícones de cada registro */
	$grid->setIconeCelula($i,9,$ic1);
	$grid->setIconeCelula($i,10,$ic2);
	$grid->setIconeCelula($i,11,$ic3);
	$grid->setIconeCelula($i,12,$ic4);
	$grid->setIconeCelula($i,13,$ic5);

	/* Define as Urls de cada registro */
	$grid->setUrlCelula($i,9,$url1);
	$grid->setUrlCelula($i,10,$url2);
	$grid->setUrlCelula($i,11,$url3);
	$grid->setUrlCelula($i,12,$url4);
	$grid->setUrlCelula($i,13,$url5);
	
}


#################################################################################
## Gera Id de reload
#################################################################################
$rid		= \Zage\App\Util::encodeUrl('_codMenu_='.$_codMenu_.'&_icone_='.$_icone_);

#################################################################################
## Carregando o template html
#################################################################################
$tpl	= new \Zage\App\Template();
$tpl->load(\Zage\App\Util::getCaminhoCorrespondente(__FILE__, \Zage\App\ZWS::EXT_HTML));

#################################################################################
## Define os valores das variáveis
#################################################################################
$tpl->set('GRID'				,$grid->getHtmlCode());
$tpl->set('NOME'				,'Fila de Importação<small><i class="fa fa-angle-double-right"></i>Lista de arquivos transferidos</small><a href="javascript:atualizaDiv();" data-toggle="tooltip" title="Atualizar página"><i class="fa fa-repeat"></i></a>');
$tpl->set('URLADD'				,null);
$tpl->set('IC'					,$_icone_);
$tpl->set('URL_RELOAD'			,ROOT_URL . "/Fin/".'/impProcessamentoLis.php?id='.$rid);
//$tpl->set('IFRAMECENTRAL'		,$system->getIframeCentral());


#################################################################################
## Por fim exibir a página HTML
#################################################################################
$tpl->show();
