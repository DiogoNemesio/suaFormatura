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
## Resgata os parâmetros passados pelo formulario de pesquisa
#################################################################################
if (isset($_GET['codRifa'])) 		$codRifa	= \Zage\App\Util::antiInjection($_GET['codRifa']);

#################################################################################
## Select dos rifas
#################################################################################
try {
	$rifas	= \Zage\Fmt\Rifa::listaRifaAptaSorteio();
	$oRifas	= $system->geraHtmlCombo($rifas,	'CODIGO', 'NOME', $codRifa, null);

} catch (\Exception $e) {
	\Zage\App\Erro::halt($e->getMessage(),__FILE__,__LINE__);
}

#################################################################################
## Resgatar informações da RIFA 
#################################################################################
$msg = null;
if ($rifas){
	if ($codRifa != null){
		$infoRifa = $em->getRepository('Entidades\ZgfmtRifa')->findOneBy(array('codOrganizacao' => $system->getCodOrganizacao(), 'codigo' => $codRifa));
	}else{
		if($rifas){
			$infoRifa = $em->getRepository('Entidades\ZgfmtRifa')->findOneBy(array('codOrganizacao' => $system->getCodOrganizacao(), 'codigo' => $rifas[0]->getCodigo()));
			$codRifa  = $rifas[0]->getCodigo(); 
		}
	}
	//Resgatar informações da rifa
	if ($infoRifa){
		//Informações 
		$nome 	= $infoRifa->getNome();
		$premio	= $infoRifa->getPremio();
		$valor 	= $infoRifa->getValorUnitario();
		$data 	= $infoRifa->getDataSorteio()->format($system->config["data"]["datetimeSimplesFormat"]);
		
		//Verificar se já foi sorteado
		if ($infoRifa->getNumeroVencedor() != null){
			$numVencedor = $infoRifa->getNumeroVencedor()->getNumero();
			
			$vencedorNome		= $infoRifa->getNumeroVencedor()->getNome();
			$vencedorEmail		= $infoRifa->getNumeroVencedor()->getEmail();
			$vencedorTelefone	= $infoRifa->getNumeroVencedor()->getTelefone();
			$dataCompra			= $infoRifa->getNumeroVencedor()->getData()->format($system->config["data"]["datetimeSimplesFormat"]);
			$vendedorNome		= $infoRifa->getNumeroVencedor()->getCodFormando()->getNome();
			$vendedorEmail		= $infoRifa->getNumeroVencedor()->getCodFormando()->getUsuario();
			
		}else{
			$numVencedor = null;
		}
	}

	
	
}else{
	
	$codRifa = null;
	
	$msg .= '<div class="alert alert-warning">';
	$msg .= '<i class="fa fa-exclamation-triangle bigger-125"></i> No momento não existe nenhuma rifa para realizar o sorteio. Observe a data e hora de sorteio das rifas eletrônicas!';
	$msg .= '</div>';
}

#################################################################################
## Resgata a url desse script
#################################################################################
$pid			= \Zage\App\Util::encodeUrl('_codMenu_='.$_codMenu_.'&_icone_='.$_icone_.'&codRifa='.$codRifa);
$url			= ROOT_URL."/Fmt/".basename(__FILE__)."?id=".$id;

#################################################################################
## Carregando o template html
#################################################################################
$tpl	= new \Zage\App\Template();
$tpl->load(\Zage\App\Util::getCaminhoCorrespondente(__FILE__, \Zage\App\ZWS::EXT_HTML));

#################################################################################
## Define os valores das variáveis
#################################################################################
$tpl->set('ID'				,$id);
$tpl->set('IC'				,$_icone_);
$tpl->set('URL'				,$url);
$tpl->set('COD_RIFA'		,$codRifa);
$tpl->set('MSG'				,$msg);

$tpl->set('RIFAS'			,$oRifas);
$tpl->set('NOME_RIFA'		,$nome);
$tpl->set('PREMIO_RIFA'		,$premio);
$tpl->set('VALOR_RIFA'		,$valor);
$tpl->set('DATA_RIFA'		,$data);
$tpl->set('NUM_VENCEDOR'	,$numVencedor);

$tpl->set('VENCEDOR_NOME'		,$vencedorNome);
$tpl->set('VENCEDOR_EMAIL'		,$vencedorEmail);
$tpl->set('VENCEDOR_TELEFONE'	,$vencedorTelefone);
$tpl->set('DATA_COMPRA'			,$dataCompra);
$tpl->set('VENDEDOR_NOME'		,$vendedorNome);
$tpl->set('VENDEDOR_EMAIL'		,$vendedorEmail);

$tpl->set('DP'				,\Zage\App\Util::getCaminhoCorrespondente(__FILE__,\Zage\App\ZWS::EXT_DP,\Zage\App\ZWS::CAMINHO_RELATIVO));

#################################################################################
## Por fim exibir a página HTML
#################################################################################
$tpl->show();