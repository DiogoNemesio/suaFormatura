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
//$system->checaPermissao($_codMenu_);


if (isset($_GET['codVenda'])){
	$codVenda		= \Zage\App\Util::antiInjection($_GET['codVenda']);
}

#################################################################################
## Resgatar informações da da venda
#################################################################################
$infoVenda = $em->getRepository('Entidades\ZgfmtRifaNumero')->findBy(array('codVenda' => $codVenda));

$total = 0;
for ($i = 0; $i < sizeof($infoVenda); $i++) {
	$total = $infoVenda[$i]->getCodRifa()->getValorUnitario() + $total;
	$linha = $i + 1;
	
	$html .= '<tr>';
	$html .= '<td class="center">'.$linha.'</td>';
	$html .= '<td class="center">'.$infoVenda[$i]->getCodRifa()->getNome().'</td>';
	$html .= '<td class="center">'.$infoVenda[$i]->getNumero().'</td>';
	$html .= '<td class="center">'.$infoVenda[$i]->getCodRifa()->getValorUnitario().'</td>';
	$html .= '</tr>';
}

#################################################################################
## Resgata a url desse script
#################################################################################
$pid			= \Zage\App\Util::encodeUrl('_codMenu_='.$_codMenu_.'&_icone_='.$_icone_.'&codModulo='.$codRifa);
$url			= ROOT_URL."/Fmt/".basename(__FILE__)."?id=".$id;
$menuPerfilUrl	= ROOT_URL."/Seg/menuPerfilLis.php?id=".$pid;

#################################################################################
## Carregando o template html
#################################################################################
$tpl	= new \Zage\App\Template();
$tpl->load(\Zage\App\Util::getCaminhoCorrespondente(__FILE__, \Zage\App\ZWS::EXT_HTML));

#################################################################################
## Define os valores das variáveis
#################################################################################
$tpl->set('ID'					,$id);
$tpl->set('IC'					,$_icone_);

$tpl->set('URL'					,$url);

$tpl->set('COD_RIFA'			,$codRifa);
$tpl->set('NOME_RIFA'			,$infoVenda[0]->getCodRifa()->getNome());
$tpl->set('PREMIO'				,$infoVenda[0]->getCodRifa()->getPremio());
$tpl->set('DATA_SORTEIO'		,$infoVenda[0]->getCodRifa()->getDataSorteio()->format($system->config["data"]["datetimeSimplesFormat"]));
$tpl->set('LOCAL_SORTEIO'		,$infoVenda[0]->getCodRifa()->getLocalSorteio());
$tpl->set('ORGANIZACAO'			,$infoVenda[0]->getCodRifa()->getCodOrganizacao()->getNome());
$tpl->set('COD_VENDA'			,$codVenda);
$tpl->set('TOTAL'				,$total);
$tpl->set('DATA_VENDA'			,$infoVenda[0]->getData()->format($system->config["data"]["datetimeSimplesFormat"]));
$tpl->set('NOME'				,$infoVenda[0]->getNome());
$tpl->set('EMAIL'				,$infoVenda[0]->getEmail());
$tpl->set('TELEFONE'			,$infoVenda[0]->getTelefone());
$tpl->set('HTML_TABLE'			,$html);

#################################################################################
## Por fim exibir a página HTML
#################################################################################
$tpl->show();

