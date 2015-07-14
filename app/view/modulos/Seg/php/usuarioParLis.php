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
$url		= ROOT_URL . '/Seg/'. basename(__FILE__);

#################################################################################
## Resgata informações de organização
#################################################################################
try {
	$org	= $em->getRepository('Entidades\ZgadmOrganizacao')->findOneBy(array('codigo' => $system->getCodOrganizacao()));
} catch (\Exception $e) {
	\Zage\App\Erro::halt($e->getMessage());
}

#################################################################################
## Resgata os dados do grid
#################################################################################
try {
	$usuario	= \Zage\Seg\Usuario::listaUsuarioOrganizacao($system->getCodOrganizacao(), 'U');
} catch (\Exception $e) {
	\Zage\App\Erro::halt($e->getMessage());
}
	
#################################################################################
## Cria o objeto do Grid (bootstrap)
#################################################################################
$grid			= \Zage\App\Grid::criar(\Zage\App\Grid\Tipo::TP_BOOTSTRAP,"GUsuario");
$grid->adicionaTexto($tr->trans('NOME'),	 			20, $grid::CENTER	,'codUsuario:nome');
$grid->adicionaTexto($tr->trans('EMAIL'),				20, $grid::CENTER	,'codUsuario:usuario');
$grid->adicionaTexto($tr->trans('CPF'),					15, $grid::CENTER	,'codUsuario:cpf');
$grid->adicionaTexto($tr->trans('CADASTRO'),			10, $grid::CENTER	,'codUsuario:codStatus:descricao');
$grid->adicionaTexto($tr->trans('ASSOCIAÇÃO'),			10, $grid::CENTER	,'codStatus:descricao');
$grid->adicionaIcone(null,'fa fa-envelope orange',$tr->trans('Reenviar convite'));
$grid->adicionaIcone(null,'fa fa-lock red',$tr->trans('Bloquear/Desbloquear usuário'));
$grid->adicionaBotao(\Zage\App\Grid\Coluna\Botao::MOD_EDIT);
$grid->adicionaBotao(\Zage\App\Grid\Coluna\Botao::MOD_REMOVE);
$grid->importaDadosDoctrine($usuario);


#################################################################################
## Popula os valores dos botões
#################################################################################
for ($i = 0; $i < sizeof($usuario); $i++) {
	$uid		= \Zage\App\Util::encodeUrl('_codMenu_='.$_codMenu_.'&_icone_='.$_icone_.'&codOrganizacao='.$codOrganizacao.'&codUsuario='.$usuario[$i]->getCodUsuario()->getCodigo().'&url='.$url);
	
	if ($usuario[$i]->getCodUsuario()->getCpf() != null){
		$valor	= \Zage\App\Mascara::tipo(\Zage\App\Mascara\Tipo::TP_CPF)->aplicaMascara($usuario[$i]->getCodUsuario()->getCpf());
	}else{
		$valor = null;
	}
	
	$grid->setValorCelula($i,2,$valor);
	
	$grid->setUrlCelula($i,5,"javascript:zgAbreModal('".ROOT_URL."/Seg/usuarioParEnv.php?id=".$uid."');");
	$grid->setUrlCelula($i,6,"javascript:zgAbreModal('".ROOT_URL."/Seg/usuarioParBlo.php?id=".$uid."');");
	$grid->setUrlCelula($i,7,ROOT_URL.'/Seg/usuarioParAlt.php?id='.$uid);
	$grid->setUrlCelula($i,8,"javascript:zgAbreModal('".ROOT_URL."/Seg/usuarioParExc.php?id=".$uid."');");
	
	if ($usuario[$i]->getCodStatus()->getCodigo() == "B") {
		$grid->setIconeCelula($i, 6, "fa fa-unlock green");
	}
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
$urlAdd			= ROOT_URL.'/Seg/usuarioParAlt.php?id='.\Zage\App\Util::encodeUrl('_codMenu_='.$_codMenu_.'&_icone_='.$_icone_.'&codUsuario=');

#################################################################################
## Carregando o template html
#################################################################################
$tpl	= new \Zage\App\Template();
$tpl->load(HTML_PATH . 'templateLis.html');

#################################################################################
## Define os valores das variáveis
#################################################################################
$tpl->set('GRID'			,$htmlGrid);
$tpl->set('NOME'			,$tr->trans('Usuários'));
$tpl->set('URLADD'			,$urlAdd);
$tpl->set('URL_VOLTAR'		,$urlVoltar);
$tpl->set('URL_ATUALIZAR'	,$urlAtualizar);
$tpl->set('IC'				,$_icone_);

$tpl->set('NOME_PARCEIRO'	,$org->getNome());

#################################################################################
## Por fim exibir a página HTML
#################################################################################
$tpl->show();
