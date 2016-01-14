<?php
################################################################################
# Includes
################################################################################
if (defined ( 'DOC_ROOT' )) {
	include_once (DOC_ROOT . 'include.php');
} else {
	include_once ('../include.php');
}

################################################################################
# Resgata a variável ID que está criptografada
################################################################################
if (isset ( $_GET ['id'] )) {
	$id = \Zage\App\Util::antiInjection ( $_GET ["id"] );
} elseif (isset ( $_POST ['id'] )) {
	$id = \Zage\App\Util::antiInjection ( $_POST ["id"] );
} elseif (isset ( $id )) {
	$id = \Zage\App\Util::antiInjection ( $id );
} else {
	\Zage\App\Erro::halt ( 'Falta de Parâmetros' );
}

################################################################################
# Descompacta o ID
################################################################################
\Zage\App\Util::descompactaId ( $id );

################################################################################
# Verifica se o usuário tem permissão no menu
################################################################################
$system->checaPermissao ( $_codMenu_ );

#################################################################################
## Resgata a url desse script
#################################################################################
$url		= ROOT_URL . '/Fmt/'. basename(__FILE__);

#################################################################################
## Validar parâmentros
#################################################################################
if (!$codOrganizacao){
	\Zage\App\Erro::halt($tr->trans('Parâmentro COD_ORGANIZAÇÃO não encontrado'));
}

#################################################################################
## Restagar o objeto da organizacao
#################################################################################
$oOrg	= $em->getRepository('Entidades\ZgadmOrganizacao')->findOneBy(array('codigo' => $codOrganizacao));

#################################################################################
## Resgata os dados do grid
#################################################################################
try {
	$vendaPlano		= $em->getRepository('Entidades\ZgadmOrganizacaoVendaPlano')->findBy(array('codOrganizacao' => $codOrganizacao));
	$planos			= $em->getRepository('Entidades\ZgadmPlano')->findBy(array('indAtivo' => '1'),array('nome' => 'ASC'));
} catch (\Exception $e) {
	\Zage\App\Erro::halt($e->getMessage());
}

#################################################################################
## Cria o objeto do Grid (bootstrap)
#################################################################################
$grid			= \Zage\App\Grid::criar(\Zage\App\Grid\Tipo::TP_BOOTSTRAP,"GVenda");
$grid->adicionaTexto($tr->trans('PLANO'),				20, $grid::CENTER	,'codPlano:nome');
$grid->adicionaDataHora($tr->trans('DATA CADASTRO'),	20, $grid::CENTER	,'dataCadastro');
$grid->adicionaDataHora($tr->trans('ÚLTIMA ALTERAÇÃO'),	20, $grid::CENTER	,'dataUltimaAlteracao');
$grid->adicionaTexto($tr->trans('STATUS'),				20, $grid::CENTER	,'');
$grid->adicionaBotao(\Zage\App\Grid\Coluna\Botao::MOD_EDIT);
$grid->adicionaIcone(null,'fa fa-ban green',$tr->trans('Habilitar/Desabilitar'));
$grid->importaDadosDoctrine($vendaPlano);

for ($i = 0; $i < sizeof($vendaPlano); $i++) {
	$uid		= \Zage\App\Util::encodeUrl('_codMenu_='.$_codMenu_.'&_icone_='.$_icone_.'&codPlano='.$planos[$i]->getCodigo().'&codOrganizacao='.$codOrganizacao.'&codVendaPlano='.$vendaPlano[$i]->getCodigo());
	
	#################################################################################
	## IND HABILIDATO
	#################################################################################
	if ($vendaPlano[$i]->getIndHabilitado() == 1) {
		$grid->setValorCelula($i, 3, "<span class=\"label label-success\">HABILITADO</span>");
		$grid->setIconeCelula($i, 5, "fa fa-ban red");
	}else{
		$grid->setValorCelula($i, 3, "<span class=\"label label-danger\">NÃO HABILITADO</span>");
		$grid->setIconeCelula($i, 5, "fa fa-check green");
	}
	
	$grid->setUrlCelula($i,4,ROOT_URL.'/Adm/parceiroComissaoAlt.php?id='.$uid);
	$grid->setUrlCelula($i,5,"javascript:zgAbreModal('".ROOT_URL."/Adm/parceiroComissaoHabilita.php?id=".$uid."');");
	
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
for ($i = 0; $i < sizeof($planos); $i++) {
	
	$existeVendaPlano	= $em->getRepository('Entidades\ZgadmOrganizacaoVendaPlano')->findOneBy(array('codOrganizacao' => $codOrganizacao,'codPlano' => $planos[$i]->getCodigo()));

	if ($existeVendaPlano) {
		$urlAdd			= "#";
		$classe			= "disabled";
	}else{
		$urlAdd		   = ROOT_URL.'/Adm/parceiroComissaoAlt.php?id='.\Zage\App\Util::encodeUrl('_codMenu_='.$_codMenu_.'&_icone_='.$_icone_.'&codPlano='.$planos[$i]->getCodigo().'&codOrganizacao='.$codOrganizacao.'&codVendaPlano=');
		$classe			= "";
	}
	$htmlButton	  .= "<li class='".$classe."'>
						<a href=\"javascript:zgLoadUrl('".$urlAdd."');\">".$planos[$i]->getNome()."</a>
				  	  </li>";
}

#################################################################################
## Gerar a url voltar
#################################################################################
$urlVoltar			= ROOT_URL.'/Adm/parceiroLis.php?id='.\Zage\App\Util::encodeUrl('_codMenu_='.$_codMenu_.'&_icone_='.$_icone_.'&codOrganizacao=');

#################################################################################
## Gerar a url atualizar
#################################################################################
$urlAtualizar			= ROOT_URL.'/Adm/parceiroComissaoLis.php?id='.\Zage\App\Util::encodeUrl('_codMenu_='.$_codMenu_.'&_icone_='.$_icone_.'&codOrganizacao='.$codOrganizacao);

#################################################################################
## Carregando o template html
#################################################################################
$tpl	= new \Zage\App\Template();
$tpl->load(\Zage\App\Util::getCaminhoCorrespondente(__FILE__, \Zage\App\ZWS::EXT_HTML));

#################################################################################
## Define os valores das variáveis
#################################################################################
$tpl->set('GRID'			,$htmlGrid);
$tpl->set('NOME'			,$tr->trans('Comissão'));
$tpl->set('URLADD'			,$urlAdd);
$tpl->set('URL_VOLTAR'		,$urlVoltar);
$tpl->set('URL_ATUALIZAR'	,$urlAtualizar);
$tpl->set('NOME_PARCEIRO'	,$oOrg->getFantasia());
$tpl->set('HTML_BUTTON'		,$htmlButton);
$tpl->set('IC'				,$_icone_);

#################################################################################
## Por fim exibir a página HTML
#################################################################################
$tpl->show();
