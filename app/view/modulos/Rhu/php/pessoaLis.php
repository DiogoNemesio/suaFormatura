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
$url		= ROOT_URL . "/Rh/". basename(__FILE__)."?id=".$id;

#################################################################################
## Resgata os dados do grid
#################################################################################
try {
	$pessoa	= $em->getRepository('Entidades\ZgrhuPessoa')->findBy(array ('codOrganizacao' => $system->getCodOrganizacao()));
} catch (\Exception $e) {
	\Zage\App\Erro::halt($e->getMessage());
}
	

#################################################################################
## Cria o objeto do Grid (bootstrap)
#################################################################################
$grid			= \Zage\App\Grid::criar(\Zage\App\Grid\Tipo::TP_BOOTSTRAP,"GUsuario");
$grid->adicionaTexto($tr->trans('NOME'), 			25, $grid::CENTER	,'nome');
$grid->adicionaTexto($tr->trans('CPF'), 			10, $grid::CENTER	,'cpf');
$grid->adicionaTexto($tr->trans('RG'), 				10, $grid::CENTER	,'rg');
$grid->adicionaData($tr->trans('DATA NASCIMENTO'), 	20, $grid::CENTER	,'dataNascimento');
$grid->adicionaTexto($tr->trans('NOME DA MÃE'), 	15, $grid::CENTER	,'nomeMae');
$grid->adicionaBotao(\Zage\App\Grid\Coluna\Botao::MOD_EDIT);
$grid->adicionaIcone(null,'fa fa-level-up',$tr->trans('Admitir'));
$grid->adicionaIcone(null,'fa fa-level-up',$tr->trans('Cadastro de Cargos'));
$grid->adicionaBotao(\Zage\App\Grid\Coluna\Botao::MOD_REMOVE);
$grid->importaDadosDoctrine($pessoa);


#################################################################################
## Popula os valores dos botões
#################################################################################
for ($i = 0; $i < sizeof($pessoa); $i++) {
	$uid		= \Zage\App\Util::encodeUrl('_codMenu_='.$_codMenu_.'&_icone_='.$_icone_.'&codPessoa='.$pessoa[$i]->getCodigo().'&url='.$url);
	
	$url    = '<a href="javascript:zgLoadUrl(\''.ROOT_URL.'/Rhu/pessoaPainel.php?id='.$uid.'\');" data-toggle="tooltip" data-trigger="click hover" data-animation="true">'.$pessoa[$i]->getNome().'<a/>';
	$grid->setValorCelula($i,0,$url);
	
	$grid->setUrlCelula($i,5,ROOT_URL.'/Rhu/pessoaAlt.php?id='.$uid);
	$grid->setUrlCelula($i,8,ROOT_URL.'/Rhu/pessoaExc.php?id='.$uid);
	
	
	$grid->setUrlCelula($i,6,ROOT_URL.'/Rhu/pessoaAdmitir.php?id='.$uid);
	$grid->setUrlCelula($i,7,ROOT_URL.'/Rhu/pessoaDemitir.php?id='.$uid);
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
## Gerar a url de adicão
#################################################################################
$urlAdd			= ROOT_URL.'/Rhu/pessoaAlt.php?id='.\Zage\App\Util::encodeUrl('_codMenu_='.$_codMenu_.'&_icone_='.$_icone_.'&codPessoa=');

#################################################################################
## Carregando o template html
#################################################################################
$tpl	= new \Zage\App\Template();
$tpl->load(HTML_PATH . 'templateLis.html');

#################################################################################
## Define os valores das variáveis
#################################################################################
$tpl->set('GRID'			,$htmlGrid);
$tpl->set('NOME'			,$tr->trans('Pessoas'));
$tpl->set('URLADD'			,$urlAdd);
$tpl->set('IC'				,$_icone_);

#################################################################################
## Por fim exibir a página HTML
#################################################################################
$tpl->show();
