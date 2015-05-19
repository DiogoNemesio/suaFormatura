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
}else{
	\Zage\App\Erro::halt('Falta de Parâmetros');
}

#################################################################################
## Descompacta o ID
#################################################################################
\Zage\App\Util::descompactaId($id);

#################################################################################
## Verifica se o módulo foi passado
#################################################################################
/*if (isset($_codModulo_)) {
	$menus	= \Zage\Seg\Usuario::listaMenusAcesso($system->getCodUsuario(),$system->getCodEmpresa(),$_codModulo_);
}*/

#################################################################################
## Resgata os módulos que o usuário tem acesso
#################################################################################
$modulos	= \Zage\Seg\Usuario::listaModulosAcesso($system->getCodUsuario(),$system->getCodEmpresa());

$html		= "";
$n			= 1;
$maxRows	= 4;
for ($i = 0; $i < sizeof($modulos); $i++) {
	if ($n == 1) $html	.= '<div class="row">';
	$mId	= \Zage\App\Util::encodeUrl('_codModulo_='.$modulos[$i]->getCodigo());
	//$mId	= \Zage\App\Util::encodeUrl('_codMenu_='.$_codMenu_.'&_icone_='.$_icone_.'&_codModulo_='.$modulos[$i]->getCodigo());
	$html	.= '
		<div class="col-sm-2 pricing-box">
			<div class="widget-box">
				<div class="widget-header widget-header-small">
					<h5 class="bigger lighter"><a class="pull-left" href="javascript:zgLoadUrl(\''.ROOT_URL.'/App/menus.php?id='.$mId.'\');"><img  src="'.ICON_URL.'/'.$modulos[$i]->getIcone().'"/></a>'.$modulos[$i]->getNome().'</h5>
				</div>
				<div class="widget-body">
					<div class="widget-main">
						<ul class="list-unstyled center">
							<li>['.$modulos[$i]->getApelido().']&nbsp;</li>
							<li><p>'.$modulos[$i]->getDescricao().'</p></li>
						</ul>
					</div>
					<div>
						<a href="'.ROOT_URL.'index.php?id='.$mId.'" target="_top" class="btn btn-xs btn-block '.$modulos[$i]->getClasseCss().'">
							<i class="fa fa-sign-in fa-1x"></i><span>Entrar</span>
						</a>
					</div>
				</div>
			</div>
		</div>';
	
	
	if ($n == $maxRows) {
		$n = 1;
		$html	.= '</div>';
	}
	$n++;
}

if ($n !== 1) {
	$html	.= '</div>';
}

#################################################################################
## Carregando o template html
#################################################################################
$tpl	= new \Zage\App\Template();
$tpl->load(\Zage\App\Util::getCaminhoCorrespondente(__FILE__, \Zage\App\ZWS::EXT_HTML));

#################################################################################
## Define os valores das variáveis
#################################################################################
$tpl->set('HTML'	,$html);


#################################################################################
## Por fim exibir a página HTML
#################################################################################
$tpl->show();
