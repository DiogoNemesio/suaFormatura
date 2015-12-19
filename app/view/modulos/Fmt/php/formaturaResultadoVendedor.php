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
## Resgata as informações da organização
#################################################################################
$oOrg			= $em->getRepository('Entidades\ZgadmOrganizacao')->findOneBy(array('codigo' => $system->getCodOrganizacao()));

#################################################################################
## Resgata os parâmetros passados pelo formulario
#################################################################################
if (isset($_POST['indFiltrado']))		$indFiltrado		= $_POST['indFiltrado'];
if (isset($_POST['codStatus']))			$codStatus			= $_POST['codStatus'];
if (isset($_POST['codUsuarioCad']))		$codUsuarioCad		= $_POST['codUsuarioCad'];
if (isset($_POST['dataCadIni']))		$dataCadIni			= $_POST['dataCadIni'];
if (isset($_POST['dataCadFim']))		$dataCadFim			= $_POST['dataCadFim'];
if (isset($_POST['dataPrevIni']))		$dataPrevIni		= $_POST['dataPrevIni'];
if (isset($_POST['dataPrevFim']))		$dataPrevFim		= $_POST['dataPrevFim'];
if (isset($_POST['instituicao']))		$instituicao		= $_POST['instituicao'];
if (isset($_POST['curso']))				$curso				= $_POST['curso'];
if (isset($_POST['cidade']))			$cidade				= $_POST['cidade'];
$log->info($codStatus);
#################################################################################
## Ajustar valores dos arrays
#################################################################################
$codStatus			= (isset($codStatus)) 		? $codStatus		: array();
$codUsuarioCad		= (isset($codUsuarioCad)) 	? $codUsuarioCad	: array();

#################################################################################
## Select do Status
#################################################################################
try {
	$aStatus	= $em->getRepository('Entidades\ZgadmOrganizacaoStatusTipo')->findAll(array('descricao' => 'ASC'));
	$oStatus	= $system->geraHtmlCombo($aStatus,	'CODIGO', 'DESCRICAO',	'', null);
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
	$table = null;
	$formaturas = null;
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
	$table .= '</table>';	

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
$tpl->set('ICONE'				,$info->getIcone());
$tpl->set('DATA_INI_FILTRO'		,$dataIniFiltro);
$tpl->set('DATA_FIM_FILTRO'		,$dataFimFiltro);
$tpl->set('CATEGORIAS'			,$oCat);
$tpl->set('STATUS'				,$oStatus);
$tpl->set('CENTRO_CUSTO'		,$oCentroCusto);
$tpl->set('FORMAS_PAG'			,$oFormaPag);
$tpl->set('TABLE'				,$table);
$tpl->set('URL_REL'				,$urlRel);
$tpl->set('TESTE'				,$teste);

#################################################################################
## Por fim exibir a página HTML
#################################################################################
$tpl->show();