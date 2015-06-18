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
$url		= ROOT_URL . "/App/". basename(__FILE__)."?id=".$id;

#################################################################################
if (!isset($codEnquete)) \Zage\App\Erro::halt('Falta de Parâmetros');

#################################################################################
## Resgata os dados do grid
#################################################################################
try {
	$respostas	= $em->getRepository('Entidades\ZgappEnqueteResposta')->findBy(array('codPergunta' => $codEnquete));
} catch (\Exception $e) {
	\Zage\App\Erro::halt($e->getMessage());
}

## Cria o objeto do Grid (bootstrap)
#################################################################################
$grid			= \Zage\App\Grid::criar(\Zage\App\Grid\Tipo::TP_BOOTSTRAP,"GRes");
$grid->adicionaTexto($tr->trans('USUARIO'),		15, $grid::CENTER	,'codUsuario:nome');
$grid->adicionaTexto($tr->trans('PERGUNTA'),	30, $grid::CENTER	,'codPergunta:pergunta');
$grid->adicionaTexto($tr->trans('RESPOSTA'),	30, $grid::CENTER	,'resposta');
$grid->adicionaDataHora($tr->trans('DATA'),		15, $grid::CENTER	,'dataResposta');
$grid->adicionaBotao(\Zage\App\Grid\Coluna\Botao::MOD_EDIT);
$grid->adicionaBotao(\Zage\App\Grid\Coluna\Botao::MOD_REMOVE);
$grid->importaDadosDoctrine($respostas);

#################################################################################
## Popula os valores dos botões
#################################################################################
for ($i = 0; $i < sizeof($respostas); $i++) {
	$uid		= \Zage\App\Util::encodeUrl('_codMenu_='.$_codMenu_.'&_icone_='.$_icone_.'&codResposta='.$respostas[$i]->getCodigo().'&url='.$url.'&codEnquete='.$codEnquete);
	$grid->setUrlCelula($i,4,ROOT_URL.'/App/respostaAlt.php?id='.$uid);
	$grid->setUrlCelula($i,5,ROOT_URL.'/App/respostaExc.php?id='.$uid);
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
$urlAdd			= ROOT_URL.'/App/respostaAlt.php?id='.\Zage\App\Util::encodeUrl('_codMenu_='.$_codMenu_.'&_icone_='.$_icone_.'&codEnquete='.$codEnquete.'&codResposta=');

#################################################################################
## Gerar a url voltar
#################################################################################
$urlVoltar			= ROOT_URL.'/App/enqueteLis.php?id='.\Zage\App\Util::encodeUrl('_codMenu_='.$_codMenu_.'&_icone_='.$_icone_.'&codEnquete='.$codEnquete);

#################################################################################
## Gerar a url atualizar
#################################################################################
$urlAtualizar			= ROOT_URL.'/App/respostaLis.php?id='.\Zage\App\Util::encodeUrl('_codMenu_='.$_codMenu_.'&_icone_='.$_icone_.'&codEnquete='.$codEnquete);

#################################################################################
## Carregando o template html
#################################################################################
$tpl	= new \Zage\App\Template();
//$tpl->load(HTML_PATH . 'templateLis.html');
$tpl->load(\Zage\App\Util::getCaminhoCorrespondente(__FILE__, \Zage\App\ZWS::EXT_HTML));

#################################################################################
## Define os valores das variáveis
#################################################################################
$tpl->set('GRID'			,$htmlGrid);
$tpl->set('NOME'			,$tr->trans("Resultados Enquete"));
$tpl->set('URLADD'			,$urlAdd);
$tpl->set('URL_VOLTAR'		,$urlVoltar);
$tpl->set('URL_ATUALIZAR'	,$urlAtualizar);
$tpl->set('IC'				,$_icone_);

#################################################################################
## Por fim exibir a página HTML
#################################################################################
$tpl->show();
