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
$url		= ROOT_URL . '/Rhu/'. basename(__FILE__);

#################################################################################
## Resgata informações de organização
#################################################################################
try {
	$org	= $em->getRepository('Entidades\ZgadmOrganizacao')->findOneBy(array('codigo' => $codParceiro));
} catch (\Exception $e) {
	\Zage\App\Erro::halt($e->getMessage());
}

#################################################################################
## Resgata os dados do grid
#################################################################################
try {
	$usuario	= \Zage\Seg\Usuario::listaUsuarioOrganizacao($codParceiro);
} catch (\Exception $e) {
	\Zage\App\Erro::halt($e->getMessage());
}
	
#################################################################################
## Cria o objeto do Grid (bootstrap)
#################################################################################
$grid			= \Zage\App\Grid::criar(\Zage\App\Grid\Tipo::TP_BOOTSTRAP,"GCargo");
$grid->adicionaTexto($tr->trans('NOME/FANTASIA'),	 	15, $grid::CENTER	,'nome');
$grid->adicionaTexto($tr->trans('NOME'),		15, $grid::CENTER	,'usuario');
$grid->adicionaTexto($tr->trans('STATUS'),		15, $grid::CENTER	,'codStatus:descricao');
$grid->adicionaBotao(\Zage\App\Grid\Coluna\Botao::MOD_EDIT);
$grid->adicionaBotao(\Zage\App\Grid\Coluna\Botao::MOD_REMOVE);
$grid->importaDadosDoctrine($usuario);


#################################################################################
## Popula os valores dos botões
#################################################################################
for ($i = 0; $i < sizeof($usuario); $i++) {
	$uid		= \Zage\App\Util::encodeUrl('_codMenu_='.$_codMenu_.'&_icone_='.$_icone_.'&codUsuario='.$usuario[$i]->getCodigo().'&url='.$url);
	
	$grid->setUrlCelula($i,3,ROOT_URL.'/Seg/usuarioAlt.php?id='.$uid);
	$grid->setUrlCelula($i,4,ROOT_URL.'/Seg/usuarioExc.php?id='.$uid);
}

#################################################################################
## Resgata os dados do grid
#################################################################################
try {
	$usuario	= \Zage\Seg\Usuario::listaUsuarioOrganizacao($codParceiro);
} catch (\Exception $e) {
	\Zage\App\Erro::halt($e->getMessage());
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
$urlAdd			= ROOT_URL.'/Fmt/parceiroUsuarioCad.php?id='.\Zage\App\Util::encodeUrl('_codMenu_='.$_codMenu_.'&_icone_='.$_icone_.'&codUsuario=');

#################################################################################
## Gerar a url voltar
#################################################################################
$urlVoltar			= ROOT_URL.'/Fmt/parceiroLis.php?id='.\Zage\App\Util::encodeUrl('_codMenu_='.$_codMenu_.'&_icone_='.$_icone_.'&codOrganizacao=');

#################################################################################
## Carregando o template html
#################################################################################
$tpl	= new \Zage\App\Template();
$tpl->load(\Zage\App\Util::getCaminhoCorrespondente(__FILE__, \Zage\App\ZWS::EXT_HTML));

#################################################################################
## Define os valores das variáveis
#################################################################################
$tpl->set('GRID'			,$htmlGrid);
$tpl->set('NOME'			,$tr->trans('Usuários'));
$tpl->set('URLADD'			,$urlAdd);
$tpl->set('URL_VOLTAR'		,$urlVoltar);
$tpl->set('IC'				,$_icone_);

$tpl->set('NOME_PARCEIRO'	,$org->getNome());

#################################################################################
## Por fim exibir a página HTML
#################################################################################
$tpl->show();
