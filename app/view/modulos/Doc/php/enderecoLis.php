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
## Resgata os parâmetros passados pelo formulario
#################################################################################
if (isset($_GET['codLocal']))		$codLocal		= \Zage\App\Util::antiInjection($_GET['codLocal']);

if (isset($codLocal)) {
	$_SESSION['DOC']['codLocal']	= $codLocal;
}else if (isset($_SESSION['DOC']['codLocal'])) {
	$codLocal						= $_SESSION['DOC']['codLocal'];
}

#################################################################################
## Resgata a url desse script
#################################################################################
$url		= ROOT_URL . "/Doc/". basename(__FILE__)."?id=".$id;


#################################################################################
## Select do Local
#################################################################################
try {
	$aLocal	= \Zage\Doc\Local::listaAtivo();
	$oLocal	= "";
	if (!isset($codLocal) && $aLocal) {
		$codLocal 						= $aLocal[0]->getCodigo();
		$_SESSION['DOC']['codLocal']	= $codLocal;
	}
	for ($i = 0; $i < sizeof($aLocal); $i++) {
		($codLocal == $aLocal[$i]->getCodigo()) ? $selected = "selected=\"true\"" : $selected = "";
		$oLocal .= "<option value=\"".$aLocal[$i]->getCodigo()."\" $selected>".$aLocal[$i]->getCodDepartamento()->getNome().' - '.$aLocal[$i]->getNome().'</option>';
	}

} catch (\Exception $e) {
	\Zage\App\Erro::halt($e->getMessage(),__FILE__,__LINE__);
}

#################################################################################
## Resgata as informações do Local
#################################################################################
	
if (isset($codLocal)) {

	try {

		$infoLocal		= $em->getRepository('Entidades\ZgdocLocal')->findOneBy(array('codigo' => $codLocal));
		
		#####################################################################
		# Verificar se o local pertence a empresa atual						#
		#####################################################################
		
		if (($infoLocal) && ($infoLocal->getCodDepartamento()->getCodEmpresa()->getCodigo() != $system->getCodEmpresa()) ) {
			if (!isset($codLocal) && $aLocal) {
				$codLocal 						= $aLocal[0]->getCodigo();
				$_SESSION['DOC']['codLocal']	= $codLocal;
			}else{
				$codLocal						= null;
				$_SESSION['DOC']['codLocal']	= $codLocal;
			}
		}

	} catch (\Exception $e) {
		\Zage\App\Erro::halt($e->getMessage());
	}
}

//$log->debug("CODLOCAL: $codLocal");

#################################################################################
## Resgata os dados do grid
#################################################################################
try {
	$enderecos		= $em->getRepository('Entidades\ZgdocEndereco')->findBy(array('codLocal' => $codLocal), array('nome' => 'ASC'));
	
} catch (\Exception $e) {
	\Zage\App\Erro::halt($e->getMessage());
}
	

#################################################################################
## Cria o objeto do Grid (bootstrap)
#################################################################################
$grid			= \Zage\App\Grid::criar(\Zage\App\Grid\Tipo::TP_BOOTSTRAP,"GDep");
$grid->adicionaTexto($tr->trans('NOME'),		70, $grid::CENTER	,'nome');
$grid->adicionaTexto($tr->trans('TIPO'), 		20, $grid::CENTER	,'codTipo:descricao');
$grid->adicionaBotao(\Zage\App\Grid\Coluna\Botao::MOD_EDIT);
$grid->adicionaBotao(\Zage\App\Grid\Coluna\Botao::MOD_REMOVE);
$grid->importaDadosDoctrine($enderecos);


#################################################################################
## Popula os valores dos botões
#################################################################################
for ($i = 0; $i < sizeof($enderecos); $i++) {
	$uid		= \Zage\App\Util::encodeUrl('_codMenu_='.$_codMenu_.'&_icone_='.$_icone_.'&codEndereco='.$enderecos[$i]->getCodigo().'&codLocal='.$codLocal.'&url='.$url);
	$grid->setUrlCelula($i,2,ROOT_URL.'/Doc/enderecoAlt.php?id='.$uid);
	$grid->setUrlCelula($i,3,ROOT_URL.'/Doc/enderecoExc.php?id='.$uid);
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
$urlAdd			= ROOT_URL.'/Doc/enderecoAlt.php?id='.\Zage\App\Util::encodeUrl('_codMenu_='.$_codMenu_.'&_icone_='.$_icone_.'&codEndereco=&codLocal='.$codLocal);

#################################################################################
## Carregando o template html
#################################################################################
$tpl	= new \Zage\App\Template();
$tpl->load(\Zage\App\Util::getCaminhoCorrespondente(__FILE__, \Zage\App\ZWS::EXT_HTML));

#################################################################################
## Define os valores das variáveis
#################################################################################
$tpl->set('GRID'			,$htmlGrid);
$tpl->set('NOME'			,$tr->trans('Endereços'));
$tpl->set('URLADD'			,$urlAdd);
$tpl->set('LOCAL'			,$oLocal);
$tpl->set('IC'				,$_icone_);
$tpl->set('URL'				,$url);

#################################################################################
## Por fim exibir a página HTML
#################################################################################
$tpl->show();
