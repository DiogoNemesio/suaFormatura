<?php
#################################################################################
## Includes
#################################################################################
if (defined('DOC_ROOT')) {
	include_once(DOC_ROOT . 'include.php');
}else{
	include_once('./include.php');
}

#################################################################################
## Variáveis globais
#################################################################################
global $system,$em,$tr,$_user;


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
	$pagamentosHis	= \Zage\Fmt\Formando::listaPagamentosRealizados($system->getCodOrganizacao(), $_user->getCpf());
} catch (\Exception $e) {
	\Zage\App\Erro::halt($e->getMessage());
}

#################################################################################
## Verifica se precisa mostrar a tabela de pagamentos em atraso
#################################################################################
if (sizeof($pagamentosHis) == 0) {
	$tabHis	.= '<tr>
					<td style="text-align: center;" colspan="5"> Nenhum registro encontrado </td>
				</tr>
			';
}else{
	$tabHis		= '';
}

#################################################################################
## Popula a tabela de pagamentos em atraso
#################################################################################
for ($i = 0; $i < sizeof($pagamentosHis); $i++) {
	$venc		= $pagamentosHis[$i]->getDataVencimento()->format($system->config["data"]["dateFormat"]);
	$valor		= ($pagamentosHis[$i]->getValor() + $pagamentosHis[$i]->getValorJuros() + $pagamentosHis[$i]->getValorMora() + $pagamentosHis[$i]->getValorOutros() - $pagamentosHis[$i]->getValorDesconto() - $pagamentosHis[$i]->getValorCancelado());
	
	#################################################################################
	## Resgata o histórico de pagamentos
	#################################################################################
	$hist		= $em->getRepository('Entidades\ZgfinHistoricoRec')->findBy(array('codContaRec' => $pagamentosHis[$i]->getCodigo()));
	if (!$hist)	{
		$dataPag	= "??/??/????";
		
	}else{
		$dataPag	= "";
		for ($h = 0 ; $h < sizeof($hist); $h++) {
			$dataPag .= $hist[$h]->getDataRecebimento()->format($system->config["data"]["dateFormat"]);
		}
	}

	$tabHis	.= '<tr>
			<td>'.$pagamentosHis[$i]->getDescricao().'</td>
			<td class="hidden-480" style="text-align: center;">('.$pagamentosHis[$i]->getParcela().'/'.$pagamentosHis[$i]->getNumParcelas().')</td>
			<td style="text-align: center;">'.$venc.'</td>
			<td style="text-align: center;">'.\Zage\App\Util::to_money($valor).'</td>
			<td style="text-align: center;">'.$dataPag.'</td>
	';
}


#################################################################################
## Gerar a url de histórico de pagamentos
#################################################################################
$urlAbe				= ROOT_URL."/Fin/meuPagamentoLis.php?id=".$id;

#################################################################################
## Carregando o template html
#################################################################################
$tpl	= new \Zage\App\Template();
$tpl->load(\Zage\App\Util::getCaminhoCorrespondente(__FILE__, \Zage\App\ZWS::EXT_HTML));

#################################################################################
## Define os valores das variáveis
#################################################################################
$tpl->set('IC'				,$_icone_);
$tpl->set('FILTER_URL'		,$url);
$tpl->set('DIVCENTRAL'		,$system->getDivCentral());
$tpl->set('URL_ABE'			,$urlAbe);
$tpl->set('HID_HIS'			,$hidHis);
$tpl->set('TAB_HIS'			,$tabHis);

#################################################################################
## Por fim exibir a página HTML
#################################################################################
$tpl->show();
