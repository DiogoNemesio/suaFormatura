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
} catch (\Exception $e) {
	\Zage\App\Erro::halt($e->getMessage());
}

#################################################################################
## Resgata as informações da formatura
#################################################################################
$oOrgFmt	= $em->getRepository('Entidades\ZgfmtOrganizacaoFormatura')->findOneBy(array('codOrganizacao' => $system->getCodOrganizacao()));

#################################################################################
## Buscar o orçamento aceite, caso exista um, pois ele será usado como base
## Para calcular o valor pendente a ser gerado
## Se não existir, apenas não sugerir os valores a serem gerados
#################################################################################
$orcamento				= \Zage\Fmt\Orcamento::getVersaoAceita($system->getCodOrganizacao());
if ($orcamento)	{
	$valorOrcado		= \Zage\App\Util::to_float($oOrgFmt->getValorPrevistoTotal());
	$qtdFormandosBase	= (int) $oOrgFmt->getQtdePrevistaFormandos();
}else{
	$valorOrcado		= 0;
	$qtdFormandosBase	= $totalFormandos;
}

#################################################################################
## Resgata os dados de previsão orcamentária
#################################################################################
try {
	$oOrgFmt	= $em->getRepository('Entidades\ZgfmtOrganizacaoFormatura')->findOneBy(array('codOrganizacao' => $system->getCodOrganizacao()));
	$contrato	= $em->getRepository('Entidades\ZgadmContrato')->findOneBy(array('codOrganizacao' => $system->getCodOrganizacao()));

	if ($oOrgFmt)	{
		$valorOrcado			= \Zage\App\Util::to_float($oOrgFmt->getValorPrevistoTotal());
		$valorArrecadado		= \Zage\App\Util::to_float(\Zage\Fmt\Financeiro::calcValorArrecadadoFormatura($system->getCodOrganizacao()));
		$valorGasto				= \Zage\App\Util::to_float(\Zage\Fmt\Financeiro::calcValorGastoFormatura($system->getCodOrganizacao()));
		$pctArrecadado			= ($valorOrcado) ? round(($valorArrecadado * 100) / $valorOrcado,2) : 0;
		$pctGasto				= ($valorOrcado) ? round(($valorGasto * 100) / $valorOrcado,2) : 0;
		$diffPct				= round($pctArrecadado - $pctGasto,2);
	
	}else{
		$valorOrcado			= 0;
		$valorArrecadado		= 0;
		$valorGasto				= 0;
		$pctArrecadado			= 0;
		$pctGasto				= 0;
		$diffPct				= 0;
		
	}
} catch (\Exception $e) {
	\Zage\App\Erro::halt($e->getMessage());
}



#################################################################################
## Resgata valores provisionados para cada formando
#################################################################################
$oValorProv				= \Zage\Fmt\Orcamento::getValorProvisionadoPorFormando($system->getCodOrganizacao());
$totalProvisionado		= 0;
for ($i = 0; $i < sizeof($oValorProv); $i++) {
	$aValorProv[$oValorProv[$i][0]->getCgc()]		= \Zage\App\Util::to_float($oValorProv[$i]["total"]);
	$totalProvisionado								+= \Zage\App\Util::to_float($oValorProv[$i]["total"]);
}

#################################################################################
## Calcular os valores totais e saldos
#################################################################################
$saldoAProvisionar			= ($valorOrcado - $totalProvisionado);
$totalPorFormando			= ($qtdFormandosBase) ? \Zage\App\Util::to_float(($valorOrcado / $qtdFormandosBase)) : 0;

#################################################################################
## Cria o objeto do Grid (bootstrap)
#################################################################################
$grid			= \Zage\App\Grid::criar(\Zage\App\Grid\Tipo::TP_BOOTSTRAP,"MensalidadeFormando");
$grid->adicionaTexto($tr->trans('NOME'),				25	,$grid::CENTER	,'nome');
$grid->adicionaTexto($tr->trans('CPF'),					12	,$grid::CENTER	,'cpf','cpf');
$grid->adicionaMoeda($tr->trans('VALOR GERADO'),		12	,$grid::CENTER	,'');
$grid->adicionaTexto($tr->trans('SALDO GERADO'),		10	,$grid::CENTER	,'');
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
	$linkNome = 'javascript:zgLoadUrl('.ROOT_URL.'Fin/mensalFormandoContaLis.php?id='.$id.');';
	$grid->setValorCelula($i,0,'<a href="'.$linkNome.'">'.$formandos[$i]->getNome().'</a>');
	
	#################################################################################
	## Valor já gerado
	#################################################################################			
	$grid->setValorCelula($i, 2, $aValorProv[$formandos[$i]->getCpf()]);
	
	#################################################################################
	## Déficit de geração
	#################################################################################
	if ($totalPorFormando > 0){
		//Tem déficit
		if ($totalPorFormando > $aValorProv[$formandos[$i]->getCpf()]){
			$saldo	= \Zage\App\Util::to_money($totalPorFormando - $aValorProv[$formandos[$i]->getCpf()]);
			$grid->setValorCelula($i, 3, "<span style='color:red'><i class='fa fa-arrow-down red'></i> ".$saldo."</span>");
		}
		// Não déficit
		elseif ($totalPorFormando < $aValorProv[$formandos[$i]->getCpf()]){
			$saldo	= \Zage\App\Util::to_money($aValorProv[$formandos[$i]->getCpf()] - $totalPorFormando);
			$grid->setValorCelula($i, 3, "<span style='color:green'><i class='fa fa-long-arrow-up green'></i> ".$saldo."</span>");
		}
	}
	
	#################################################################################
	## Definir o valores do botão
	#################################################################################
	
	$grid->setUrlCelula($i,5,ROOT_URL.'/Fin/mensalidadeFormandoConta.php?id='.$id);
	$grid->setUrlCelula($i,6,ROOT_URL.'/Fmt/mensalFormandoGerar.php?id='.$id);
	$grid->setUrlCelula($i,7,ROOT_URL.'/Fmt/mensalFormandoContaLis.php?id='.$id);

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
