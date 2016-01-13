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
global $system,$em,$tr;


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
	$formandos	= \Zage\Fmt\Formatura::listaFormandos($system->getCodOrganizacao());
	$oOrgFmt	= $em->getRepository('Entidades\ZgfmtOrganizacaoFormatura')->findOneBy(array('codOrganizacao' => $system->getCodOrganizacao()));
} catch (\Exception $e) {
	\Zage\App\Erro::halt($e->getMessage());
}

#################################################################################
## Variáveis usadas no cálculo das mensalidades
#################################################################################
$dataConclusao			= $oOrgFmt->getDataConclusao();
if (!$dataConclusao)	\Zage\App\Erro::halt("Data de Conclusão não informada");

#################################################################################
## Buscar o orçamento aceite, caso exista um, pois ele será usado como base
## Para calcular o valor pendente a ser gerado
#################################################################################
$orcamento				= \Zage\Fmt\Orcamento::getVersaoAceita($system->getCodOrganizacao());
if ($orcamento){
	$valorOrcado			= \Zage\App\Util::to_float($oOrgFmt->getValorPrevistoTotal());
	$qtdFormandosBase		= (int) $oOrgFmt->getQtdePrevistaFormandos();
	$mensalidadeFormando	= $valorOrcado / $qtdFormandosBase;
}


#################################################################################
## Calcular o valor já provisionado por formando
#################################################################################
$oValorProv				= \Zage\Fmt\Financeiro::getValorProvisionadoPorFormando($system->getCodOrganizacao());

#################################################################################
## Montar o array para facilitar a impressão no grid dos valores provisionados
## Montar um array que será enviado ao Html para validar se os formandos
## selecionados tem os mesmos valores de mensalidade e sistema
#################################################################################
$aValorProv				= array();
$aCodigos				= array();
for ($i = 0; $i < sizeof($oValorProv); $i++) {
	$total													= \Zage\App\Util::to_float($oValorProv[$i]["mensalidade"]) + \Zage\App\Util::to_float($oValorProv[$i]["sistema"]);
	$aCodigos[$oValorProv[$i][0]->getCgc()]["MENSALIDADE"]	= \Zage\App\Util::to_float($oValorProv[$i]["mensalidade"]);
	$aCodigos[$oValorProv[$i][0]->getCgc()]["SISTEMA"]		= \Zage\App\Util::to_float($oValorProv[$i]["sistema"]);
	$aCodigos[$oValorProv[$i][0]->getCgc()]["TOTAL"]		= $total;
	$aValorProv[$oValorProv[$i][0]->getCgc()]				= $total;
}

#################################################################################
## Cria o objeto do Grid (bootstrap)
#################################################################################
$grid			= \Zage\App\Grid::criar(\Zage\App\Grid\Tipo::TP_BOOTSTRAP,"MensalidadeFormando");
$grid->adicionaTexto($tr->trans('NOME'),				20	,$grid::CENTER	,'nome');
$grid->adicionaTexto($tr->trans('CPF'),					12	,$grid::CENTER	,'cpf','cpf');
$grid->adicionaMoeda($tr->trans('R$ GERADO'),			12	,$grid::CENTER	,'');
$grid->adicionaTexto($tr->trans('R$ A GERAR'),			10	,$grid::CENTER	,'');
$grid->adicionaTexto($tr->trans('TOTAL PAGO'),			10	,$grid::CENTER	,'');
$grid->adicionaTexto($tr->trans('EM ATRASO'),			10	,$grid::CENTER	,'');
$grid->adicionaTexto($tr->trans('STATUS'),				12	,$grid::CENTER	,'');
$grid->adicionaIcone(null,'fa fa-sign-out red'			,$tr->trans('Desistir'));
$grid->adicionaIcone(null,'fa fa-usd green'				,$tr->trans('Gerar conta'));
$grid->adicionaIcone(null,'fa fa-search blue'			,$tr->trans('Visualizar contas'));
$grid->importaDadosDoctrine($formandos);

#################################################################################
## Popula os valores dos botões
#################################################################################
for ($i = 0; $i < sizeof($formandos); $i++) {

	$id		= \Zage\App\Util::encodeUrl('_codMenu_='.$_codMenu_.'&_icone_='.$_icone_.'&codFormando='.$formandos[$i]->getCodigo().'&url='.$url);
	
	#################################################################################
	## Link no nome
	#################################################################################
	$linkNome = 'javascript:zgLoadUrl(\''.ROOT_URL.'/Fmt/mensalFormandoContaLis.php?id='.$id.'\');';
	$grid->setValorCelula($i,0,'<a href="'.$linkNome.'">'.$formandos[$i]->getNome().'</a>');
	
	#################################################################################
	## Atualizar a coluna status com o status da associação do formando a Organização (Formatura)
	#################################################################################
	$oStatus	= $em->getRepository('Entidades\ZgsegUsuarioOrganizacao')->findOneBy(array('codUsuario' => $formandos[$i]->getCodigo(),'codOrganizacao' => $system->getCodOrganizacao()));
	$codStatus	= ($oStatus->getCodStatus()) ? $oStatus->getCodStatus()->getCodigo() : null;
	$status		= ($oStatus->getCodStatus()) ? $oStatus->getCodStatus()->getDescricao() : null;
	$grid->setValorCelula($i,6,$status);
	
	#################################################################################
	## Verificar o status da associação a Formatura, para definir se poderá ou não
	## Gerar mensalidade para o Formando
	#################################################################################
	switch ($codStatus) {
		case "A":
		case "P":
		case "B":
			$podeGerar		= true;
			$podeDesistir	= true;
			break;
		case "D":
			$podeGerar		= true;
			$podeDesistir	= false;
			break;
		case "T":
			$podeGerar		= false;
			$podeDesistir	= false;
			break;
		default:
			$podeGerar		= false;
			$podeDesistir	= false;
			break;
	
	}
	
	#################################################################################
	## Saldo gerado
	#################################################################################
	$valProvisionado			= (isset($aValorProv[$formandos[$i]->getCpf()])) ? $aValorProv[$formandos[$i]->getCpf()] : 0;
	$saldo						= round($mensalidadeFormando - $valProvisionado,2);
	$aCodigos[$formandos[$i]->getCpf()]["SALDO"]	= $saldo;
	$grid->setValorCelula($i,2,$valProvisionado);
	
	#################################################################################
	## Déficit de geração
	#################################################################################
	if ($podeDesistir	== true) {
		if ($saldo > 0){
			$grid->setValorCelula($i, 3, "<span style='color:red'><i class='fa fa-arrow-down red'></i> ".\Zage\App\Util::to_money($saldo)."</span>");
		}else if ($saldo == 0) {
				$grid->setValorCelula($i, 3, "<span style='color:green'><i class='fa fa-check-circle green'></i> ".\Zage\App\Util::to_money($saldo)."</span>");
		}else{
			$grid->setValorCelula($i, 3, "<span style='color:green'><i class='fa fa-arrow-up green'></i> ".\Zage\App\Util::to_money(abs($saldo))."</span>");
		}
	}else{
		$grid->setValorCelula($i, 3, "<span style='color:green'><i class='fa fa-check-circle green'></i>".\Zage\App\Util::to_money(0)."</span>");
	}
	
	#################################################################################
	## Valor pago
	#################################################################################
	$valorPago = \Zage\Fmt\Formando::listaPagamentosRealizados($system->getCodOrganizacao(), $formandos[$i]->getCpf());
	
	$grid->setValorCelula($i, 4, ($valorPago));
	#################################################################################
	## Valor pago
	#################################################################################
	
	#################################################################################
	## Definir a ação do botão de desistência
	#################################################################################
	if ($podeDesistir	== true) {
	
		#################################################################################
		## Definir o link do botão de geração de conta
		#################################################################################
		$grid->setUrlCelula($i,7,ROOT_URL.'/Fmt/desistenciaCad.php?id='.$id);
	
	}else{
		$grid->desabilitaCelula($i, 7);
	}
	
	#################################################################################
	## Definir a ação do botão de geração de conta
	#################################################################################
	if ($podeGerar	== true) {

		#################################################################################
		## Definir o link do botão de geração de conta
		#################################################################################
		$grid->setUrlCelula($i,8,ROOT_URL.'/Fmt/mensalFormandoGerar.php?id='.$id);
		
	}else{
		$grid->desabilitaCelula($i, 8);
	}
	

	#################################################################################
	## Definir o link do botão de visualização das contas
	#################################################################################
	$grid->setUrlCelula($i,9,ROOT_URL.'/Fmt/mensalFormandoContaLis.php?id='.$id);
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
$tpl->set('GRID'					,$htmlGrid);
$tpl->set('IC'						,$_icone_);
$tpl->set('ID'						,$id);
$tpl->set('TITULO'					,'Gerenciar Mensalidades');

#################################################################################
## Por fim exibir a página HTML
#################################################################################
$tpl->show();
