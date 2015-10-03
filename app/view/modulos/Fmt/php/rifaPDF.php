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
global $em,$tr,$system;

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
	\Zage\App\Erro::halt('FALTA PARÂMENTRO : ID');
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
$url		= ROOT_URL . "/Fmt/". basename(__FILE__)."?id=".$id;

#################################################################################
## Resgata informações da rifa
#################################################################################
if (!isset($codRifa)) \Zage\App\Erro::halt('FALTA PARÂMENTRO : COD_RIFA');


try {
	$infoRifa 		= $em->getRepository('Entidades\ZgfmtRifa')->findOneBy(array('codigo' => $codRifa));
} catch (\Exception $e) {
	\Zage\App\Erro::halt($e->getMessage());
}

if (!$infoRifa)	\Zage\App\Erro::halt($tr->trans('Rifa não encontrada').' (COD_RIFA)');

#################################################################################
## Resgata as informações da geração da rifa
#################################################################################
try {
	$numeros		= $em->getRepository('Entidades\ZgfmtRifaNumero')->findBy(array('codRifa' => $codRifa,'codGeracao' => $codGeracao));

	if (!$numeros)	\Zage\App\Erro::halt($tr->trans('Números não encontrada').' (COD_GERACAO)');
	
} catch (\Exception $e) {
	\Zage\App\Erro::halt($e->getMessage());
}

#################################################################################
## Criar o relatório
#################################################################################
$rel	= new \Zage\App\Relatorio();

#################################################################################
## Criação do cabeçalho
#################################################################################
$rel->NaoExibeFiltrosNulo();

#################################################################################
## Ajustar o timezone
#################################################################################
date_default_timezone_set($system->config["data"]["timezone"]);
setlocale (LC_ALL, 'ptb');

#################################################################################
## Formatar os dados do relatório
#################################################################################
$numNumeros		= sizeof($numeros);

$table	= '<table class="table table-condensed">';

for ($i = 0; $i < $numNumeros; $i++) {
	
	if ($i%2 == 0) {
		$table .= '<tr>';
	}
	
	$table .= '<td style="height: 56px;text-align: left;">'.$numeros[$i]->getNumero().'</td>';
	
	if ($i%2 != 0) {
		$table .= '</tr>';
	}
}
$table .= '</table>';

$html	= '<body class="no-skin">';
$htmlTable	= '
<div class="row">
	<div class="col-sm-12 widget-container-span">
		<div class="widget-body">
			<div class="box-content">'.$table.'</div><!--/span-->
		</div>
	</div>
</div>
</body>';

$html		.= $htmlTable;
$relName	= "Rifa_".str_replace(" ", "_", $infoRifa->getNome()).".pdf";

$rel->WriteHTML($html);
$rel->Output($relName,'D');

