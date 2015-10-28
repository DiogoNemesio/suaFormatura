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
$url		= ROOT_URL . '/Fmt/'. basename(__FILE__);

#################################################################################
## Resgata os dados do grid
#################################################################################
try {
	$evento			= $em->getRepository('Entidades\ZgfmtEvento')->findBy(array('codFormatura' => $system->getCodOrganizacao()) , array('codTipoEvento' => 'ASC'));
	$conviteConf	= $em->getRepository('Entidades\ZgfmtConviteExtraConf')->findBy(array('codOrganizacao' => $system->getCodOrganizacao()) , array('codigo' => 'ASC'));
} catch (\Exception $e) {
	\Zage\App\Erro::halt($e->getMessage());
}
	
#################################################################################
## Cria o objeto do Grid (bootstrap)
#################################################################################
$grid			= \Zage\App\Grid::criar(\Zage\App\Grid\Tipo::TP_BOOTSTRAP,"GConviteExtraConf");
$grid->adicionaTexto($tr->trans('EVENTO'),					30, $grid::CENTER	,'codTipoEvento:descricao');
$grid->adicionaMoeda($tr->trans('VALOR'),					15, $grid::CENTER	,'valor');
$grid->adicionaTexto($tr->trans('QTDE MÁXIMA'),				15, $grid::CENTER	,'qtdeMaxAluno');
$grid->adicionaDataHora($tr->trans('CADASTRADO EM'),		15, $grid::CENTER	,'dataCadastro');
$grid->adicionaBotao(\Zage\App\Grid\Coluna\Botao::MOD_EDIT);
$grid->importaDadosDoctrine($conviteConf);

#################################################################################
## Popula os valores dos botões
#################################################################################
for ($i = 0; $i < sizeof($conviteConf); $i++) {
	$uid		= \Zage\App\Util::encodeUrl('_codMenu_='.$_codMenu_.'&_icone_='.$_icone_.'&codConviteExtraConf='.$conviteConf[$i]->getCodigo().'&url='.$url);
	
	$grid->setUrlCelula($i,4,ROOT_URL.'/Fmt/conviteExtraConf.php?id='.$uid);

}

#################################################################################
## Gerar a url de adicão
#################################################################################
for ($i = 0; $i < sizeof($evento); $i++) {
	
	$existeConf	= $em->getRepository('Entidades\ZgfmtConviteExtraConf')->findOneBy(array('codOrganizacao' => $system->getCodOrganizacao(),'codTipoEvento' => $evento[$i]->getCodTipoEvento()->getCodigo())); 
	
	if ($existeConf) {
		$urlAdd			= "#";
		$classe			= "disabled";
	}else{
		$urlAdd		   = ROOT_URL.'/Fmt/conviteExtraConf.php?id='.\Zage\App\Util::encodeUrl('_codMenu_='.$_codMenu_.'&_icone_='.$_icone_.'&codTipoEvento='.$evento[$i]->getCodTipoEvento()->getCodigo().'&codConviteExtraConf=');
		$classe			= "";
	}
	$htmlButton	  .= "<li class='".$classe."'>
						<a href=\"javascript:zgLoadUrl('".$urlAdd."');\">".$evento[$i]->getCodTipoEvento()->getDescricao()."</a>
				  	  </li>";
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
## Carregando o template html
#################################################################################
$tpl	= new \Zage\App\Template();
$tpl->load(\Zage\App\Util::getCaminhoCorrespondente(__FILE__, \Zage\App\ZWS::EXT_HTML));

#################################################################################
## Define os valores das variáveis
#################################################################################
$tpl->set('GRID'			,$htmlGrid);
$tpl->set('NOME'			,$tr->trans('Convite Extra'));
$tpl->set('URLADD'			,$urlAdd);
$tpl->set('HTML_BUTTON'		,$htmlButton);
$tpl->set('IC'				,$_icone_);

#################################################################################
## Por fim exibir a página HTML
#################################################################################
$tpl->show();
