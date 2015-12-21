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
global $em,$system,$tr,$log;

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
## Resgata as informações do Relatório
#################################################################################
$info	= $em->getRepository('Entidades\ZgappMenu')->findOneBy(array('codigo' => $_codMenu_));

#################################################################################
## Criar o relatório
#################################################################################
$rel	= new \Zage\App\Relatorio(''	,'A4-L',20,'',15,15,16,16,9,9,'P');

#################################################################################
## Criação do cabeçalho
#################################################################################
//$rel->adicionaCabecalho($info->getNome());
$rel->NaoExibeFiltrosNulo();

#################################################################################
## Criação do Rodapé
#################################################################################
$rel->adicionaRodape();

#################################################################################
## Ajustar o timezone
#################################################################################
date_default_timezone_set($system->config["data"]["timezone"]);
setlocale (LC_ALL, 'ptb');

#################################################################################
## Verificar se o mês de referência foi informado
#################################################################################
if (!isset($dataRef) || !$dataRef) $dataRef = date('d/m/Y');
list ($dia, $mes, $ano) = split ('[/.-]', $dataRef);

#################################################################################
## Ajustar a data de referência com base no offset
#################################################################################
$dataRef				= date('d/m/Y', mktime (0,0,0,$mes,($dia + $offset),$ano));
list ($dia, $mes, $ano) = split ('[/.-]', $dataRef);

$_dtVenc				= \DateTime::createFromFormat("d/m/Y", $dataRef);
$dtVenc					= $_dtVenc->format("Y-m-d");

#################################################################################
## Resgata as informações da organização
#################################################################################
$oOrg			= $em->getRepository('Entidades\ZgadmOrganizacao')->findOneBy(array('codigo' => $system->getCodOrganizacao()));

#################################################################################
## Resgata os parâmetros passados pelo formulario
#################################################################################
if (isset($_POST['indFiltrado']))		$indFiltrado		= $_POST['indFiltrado'];
if (isset($_POST['geraPdf']))			$geraPdf		= $_POST['geraPdf'];
if (isset($_POST['codStatus']))			$codStatus			= $_POST['codStatus'];
//if (isset($_POST['codUsuarioCad']))		$codUsuarioCad		= $_POST['codUsuarioCad'];
if (isset($_POST['dataCadIni']))		$dataCadIni			= $_POST['dataCadIni'];
if (isset($_POST['dataCadFim']))		$dataCadFim			= $_POST['dataCadFim'];
if (isset($_POST['dataPrevIni']))		$dataPrevIni		= $_POST['dataPrevIni'];
if (isset($_POST['dataPrevFim']))		$dataPrevFim		= $_POST['dataPrevFim'];
if (isset($_POST['instituicao']))		$instituicao		= $_POST['instituicao'];
if (isset($_POST['curso']))				$curso				= $_POST['curso'];
if (isset($_POST['cidade']))			$cidade				= $_POST['cidade'];
$codUsuarioCad = [$system->getCodUsuario()];
#################################################################################
## Ajustar valores dos arrays
#################################################################################
$codStatus			= (isset($codStatus)) 		? $codStatus		: array();
//$codUsuarioCad		= (isset($codUsuarioCad)) 	? $codUsuarioCad	: array();

#################################################################################
## Select do Status
#################################################################################
try {
	$aStatus	= $em->getRepository('Entidades\ZgadmOrganizacaoStatusTipo')->findAll(array('descricao' => 'ASC'));
	$oStatus	= $system->geraHtmlCombo($aStatus,	'CODIGO', 'DESCRICAO',	$codStatus , null);
} catch (\Exception $e) {
	\Zage\App\Erro::halt($e->getMessage(),__FILE__,__LINE__);
}

#################################################################################
## Url de geração do PDF
#################################################################################
$urlRel			= ROOT_URL . "/Fmt/formaturaResultadoVendedor.php?id=".$id;

#################################################################################
## Resgatar informações do relatório
#################################################################################
$table = '';
if ($indFiltrado){	
	
	$formaturas	= \Zage\Fmt\Organizacao::listaFormaturaOrganizacao($system->getCodOrganizacao(),$codStatus,$codUsuarioCad,$dataCadIni,$dataCadFim,$dataPrevIni,$dataPrevFim,$instituicao,$curso,$cidade);
	
	#################################################################################
	## Montar o relatório
	#################################################################################
	//$teste	= '<table class="table table-condensed">';
	$table	= '<table class="table table-condensed">';
	$table .= '<thead><tr style="background-color:#EEEEEE">
					<th style="text-align: center; width: 30%;"><strong>TURMA</strong></th>
					<th style="text-align: center; width: 10%;"><strong>INSTITUIÇÃO</strong></th>
					<th style="text-align: center; width: 20%;"><strong>CURSO</strong></th>
					<th style="text-align: center; width: 20%;"><strong>STATUS</strong></th>
					<th style="text-align: center; width: 10%;"><strong>CADASTRO</strong></th>
					<th style="text-align: center; width: 10%;"><strong>ATIVAÇÃO</strong></th>
				</tr>';
	$table .= '</thead><tbody>';
	
	if ($formaturas){
		
		#################################################################################
		## Botão do PDF
		#################################################################################
		$btnPdf = '<button class="btn btn-white btn-default btn-round" onclick="zgRelConviteResumoVendaImprimir();" data-rel="tooltip" data-placement="top" title="Gerar PDF">
					<i class="ace-icon fa fa-file-pdf-o red2"></i>
					PDF
				</button>';
		
		foreach ($formaturas as $dados) {
			$dataAtivacao = ($dados->getCodOrganizacao()->getDataAtivacao() != null) ? $dados->getCodOrganizacao()->getDataAtivacao()->format($system->config["data"]["dateFormat"]) : null;
			$table .= '<tr>
						<td style="text-align: center; width: 30%;">'.$dados->getCodOrganizacao()->getNome().'</td>
						<td style="text-align: center; width: 10%;">'.$dados->getCodInstituicao()->getSigla().'</td>
						<td style="text-align: center; width: 20%;">'.$dados->getCodCurso()->getNome().'</td>
						<td style="text-align: center; width: 20%;">'.$dados->getCodOrganizacao()->getCodStatus()->getDescricao().'</td>
						<td style="text-align: center; width: 10%;">'.$dados->getCodOrganizacao()->getDataCadastro()->format($system->config["data"]["dateFormat"]).'</td>
						<td style="text-align: center; width: 10%;">'.$dataAtivacao.'</td>
						</tr>';
		
		}
	}else{
		$table .= '<tr>
						<td style="text-align: center; width: 100%;" colspan="6">Nenhum resultado encontrado</td>
					</tr>';
	}
	
	$table .= '</table>';	
}
#################################################################################
## Carregando o template html
#################################################################################
$tpl	= new \Zage\App\Template();
$tpl->load(\Zage\App\Util::getCaminhoCorrespondente(__FILE__, \Zage\App\ZWS::EXT_HTML));

#################################################################################
## Define os valores das variáveis
#################################################################################
$tpl->set('ID'					,$id);
$tpl->set('TITULO'				,$info->getNome());
$tpl->set('DIVCENTRAL'			,$system->getDivCentral());
$tpl->set('ICONE'				,$info->getIcone());
$tpl->set('DATA_INI_FILTRO'		,$dataIniFiltro);
$tpl->set('DATA_FIM_FILTRO'		,$dataFimFiltro);
$tpl->set('CATEGORIAS'			,$oCat);
$tpl->set('STATUS'				,$oStatus);
$tpl->set('CENTRO_CUSTO'		,$oCentroCusto);
$tpl->set('FORMAS_PAG'			,$oFormaPag);
$tpl->set('TABLE'				,$table);
$tpl->set('URL_REL'				,$urlRel);
$tpl->set('BTN_PDF'				,$btnPdf);

$tpl->set('dataCadIni'			,$dataCadIni);
$tpl->set('dataCadFim'			,$dataCadFim);
$tpl->set('dataPrevIni'			,$dataPrevIni);
$tpl->set('dataPrevFim'			,$dataPrevFim);
$tpl->set('instituicao'			,$instituicao);
$tpl->set('curso'				,$curso);
$tpl->set('cidade'				,$cidade);

#################################################################################
## Por fim exibir a página HTML
#################################################################################
$relName	= "Cadastro_Formaturas".str_replace("/", "_", $dataRef).".pdf";

$htmlTable	= '
<div class="row">
	<div class="col-sm-12 widget-container-span">
		<div class="widget-body">
			<div class="box-content">'.$table.'</div><!--/span-->
		</div>
	</div>
</div>
</body>';


if ($geraPdf == 1) {
	$log->info("entrei");
	$rel->WriteHTML($htmlTable);
	$rel->Output($relName,'D');
}else{
	$tpl->show();
}
