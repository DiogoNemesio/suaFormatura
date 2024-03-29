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
$url		= ROOT_URL . "/Fin/". basename(__FILE__)."?id=".$id;

#################################################################################
## Resgata os dados do grid
#################################################################################
try {
	$segmentos	= $em->getRepository('Entidades\ZgfinSegmentoMercado')->findBy(array(), array('descricao' => 'ASC'));
} catch (\Exception $e) {
	\Zage\App\Erro::halt($e->getMessage());
}
	

#################################################################################
## Cria o objeto do Grid (bootstrap)
#################################################################################
$grid			= \Zage\App\Grid::criar(\Zage\App\Grid\Tipo::TP_BOOTSTRAP,"GSeg");
$grid->adicionaTexto($tr->trans('CÓDIGO'),				10, $grid::CENTER	,'codigo');
$grid->adicionaTexto($tr->trans('DESCRIÇÃO'),			30, $grid::CENTER	,'descricao');
$grid->adicionaTexto($tr->trans('CATEGORIAS'),			10, $grid::CENTER	,'');
$grid->adicionaIcone(null,'fa fa-crosshairs orange',$tr->trans('Vincular a uma categoria'));
$grid->adicionaBotao(\Zage\App\Grid\Coluna\Botao::MOD_EDIT);
$grid->adicionaBotao(\Zage\App\Grid\Coluna\Botao::MOD_REMOVE);
$grid->importaDadosDoctrine($segmentos);


#################################################################################
## Popula os valores dos botões
#################################################################################
for ($i = 0; $i < sizeof($segmentos); $i++) {
	$uid		= \Zage\App\Util::encodeUrl('_codMenu_='.$_codMenu_.'&_icone_='.$_icone_.'&codSegmento='.$segmentos[$i]->getCodigo().'&url='.$url);

	//CATEGORIA ASSOCIADA
	$oCats		= $em->getRepository('Entidades\ZgfinSegmentoCategoria')->findBy(array('codSegmento' => $segmentos[$i]->getCodigo()));
	if (sizeof($oCats) == 1) {
		foreach ($oCats as $cat) {
			$htmlCat = $cat->getCodCategoria()->getDescricao();
		}
	}elseif (sizeof($oCats) > 1) {
		$cats = '';
		foreach ($oCats as $cat) {
			$cats	.= '<li><a href="#">'.$cat->getCodCategoria()->getDescricao().'</a></li>';
		}
	
		$htmlCat	= '<div class="inline dropdown dropup"><a href="#" data-toggle="dropdown"><i class="fa fa-ellipsis-h bigger-150"></i></a>
		<ul class="dropdown-menu dropdown-menu-right dropdown-125 dropdown-lighter dropdown-close dropdown-caret">
			'.$cats.'
		</ul>
		</div>';
	}else{
		$htmlCat 	= null;
	}
	
	$grid->setValorCelula($i,2,$htmlCat);
		
	$grid->setUrlCelula($i,3,ROOT_URL.'/Fin/segmentoMercadoCat.php?id='.$uid);
	$grid->setUrlCelula($i,4,ROOT_URL.'/Fin/segmentoMercadoAlt.php?id='.$uid);
	$grid->setUrlCelula($i,5,ROOT_URL.'/Fin/segmentoMercadoExc.php?id='.$uid);
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
$urlAdd			= ROOT_URL.'/Fin/segmentoMercadoAlt.php?id='.\Zage\App\Util::encodeUrl('_codMenu_='.$_codMenu_.'&_icone_='.$_icone_.'&codSegmento=');

#################################################################################
## Carregando o template html
#################################################################################
$tpl	= new \Zage\App\Template();
$tpl->load(HTML_PATH . 'templateLis.html');

#################################################################################
## Define os valores das variáveis
#################################################################################
$tpl->set('GRID'			,$htmlGrid);
$tpl->set('NOME'			,$tr->trans("Segmento de Mercado"));
$tpl->set('URLADD'			,$urlAdd);
$tpl->set('IC'				,$_icone_);

#################################################################################
## Por fim exibir a página HTML
#################################################################################
$tpl->show();
