<?php
#################################################################################
## Includes
#################################################################################
if (defined('DOC_ROOT')) {
	include_once(DOC_ROOT . 'include.php');
}else{
	include_once('./include.php');
}

#################################################################################
## Variáveis globais
#################################################################################
global $system,$em,$tr;


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
## Resgata os dados do grid
#################################################################################
try {
	$formandos	= \Zage\Fmt\Formatura::listaFormandosAtivos($system->getCodOrganizacao());
} catch (\Exception $e) {
	\Zage\App\Erro::halt($e->getMessage());
}

#################################################################################
## Cria o objeto do Grid (bootstrap)
#################################################################################
$grid			= \Zage\App\Grid::criar(\Zage\App\Grid\Tipo::TP_BOOTSTRAP,"GContaLis");
$checkboxName	= "selItemGeracaoConta";
$grid->adicionaCheckBox($checkboxName);
$grid->adicionaTexto($tr->trans('USUÁRIO'),				15	,$grid::CENTER	,'usuario');
$grid->adicionaTexto($tr->trans('NOME'),				25	,$grid::CENTER	,'nome');
$grid->adicionaTexto($tr->trans('CPF'),					12	,$grid::CENTER	,'cpf','cpf');
$grid->adicionaTexto($tr->trans('RG'),					12	,$grid::CENTER	,'rg');
$grid->adicionaData($tr->trans('NASCIMENTO'),			12	,$grid::CENTER	,'dataNascimento');
$grid->adicionaTexto($tr->trans('STATUS'),				10	,$grid::CENTER	,'codStatus:descricao');
$grid->adicionaTexto($tr->trans('SEXO'),				10	,$grid::CENTER	,'sexo:descricao');
$grid->importaDadosDoctrine($formandos);

#################################################################################
## Popula os valores dos botões
#################################################################################
for ($i = 0; $i < sizeof($formandos); $i++) {

	#################################################################################
	## Definir o valor da Checkbox
	#################################################################################
	$grid->setValorCelula($i,0,$formandos[$i]->getCodigo());

}

#################################################################################
## Gerar o código html do grid
#################################################################################
try {
	$htmlGrid	= $grid->getHtmlCode();
} catch (\Exception $e) {
	\Zage\App\Erro::halt($e->getMessage());
}

#################################################################################
## Gerar a url de geração de Contas
#################################################################################
$urlGeracao				= ROOT_URL."/Fin/geraContaMassa.php?id=".$id;

#################################################################################
## Carregando o template html
#################################################################################
$tpl	= new \Zage\App\Template();
$tpl->load(\Zage\App\Util::getCaminhoCorrespondente(__FILE__, \Zage\App\ZWS::EXT_HTML));

#################################################################################
## Define os valores das variáveis
#################################################################################
$tpl->set('GRID'			,$htmlGrid);
$tpl->set('NOME'			,$tr->trans("Geração de Contas"));
$tpl->set('IC'				,$_icone_);
$tpl->set('FILTER_URL'		,$url);
$tpl->set('DIVCENTRAL'		,$system->getDivCentral());
$tpl->set('CHECK_NAME'		,$checkboxName);
$tpl->set('URL_GERACAO'		,$urlGeracao);

#################################################################################
## Por fim exibir a página HTML
#################################################################################
$tpl->show();
