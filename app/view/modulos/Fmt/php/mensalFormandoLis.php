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
global $system,$em,$tr,$log;


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
$url		= ROOT_URL . "/Fmt/". basename(__FILE__);

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
	$totalPorFormando		= $valorOrcado / $qtdFormandosBase;
}else{
	\Zage\App\Erro::halt("Nenhum orçamento aceito!!");
}


#################################################################################
## Calcular o valor já provisionado e a provisionar por formando
#################################################################################
$oValorAProvionar			= \Zage\Fmt\Financeiro::calculaTotalAProvisionarPorFormando($system->getCodOrganizacao());
$oValorProvisionado			= \Zage\Fmt\Financeiro::getValorProvisionadoPorFormando($system->getCodOrganizacao());


#################################################################################
## Montar o array para facilitar a impressão no grid dos valores a provisionar
#################################################################################
$aValorAProvisionar			= array();
for ($i = 0; $i < sizeof($oValorAProvionar); $i++) {
	$aValorAProvisionar[$oValorAProvionar[$i][0]->getCodigo()]		= \Zage\App\Util::to_float(round(\Zage\App\Util::to_float($oValorAProvionar[$i]["total"]),2));
}

#################################################################################
## Montar o array para facilitar a impressão no grid dos valores provisionados
#################################################################################
$aValorProvisionado			= array();
$aCodigos					= array();
for ($i = 0; $i < sizeof($oValorProvisionado); $i++) {
	$total													= \Zage\App\Util::to_float($oValorProvisionado[$i]["mensalidade"]) + \Zage\App\Util::to_float($oValorProvisionado[$i]["sistema"]);
	$aValorProvisionado[$oValorProvisionado[$i][0]->getCgc()]		= $total;
}

#################################################################################
## Calcular o valor já pago por formando
#################################################################################
$oValorPago					= \Zage\Fmt\Financeiro::getValorPagoPorFormando($system->getCodOrganizacao());

#################################################################################
## Montar o array para facilitar a impressão no grid dos valores pagos
#################################################################################
$aValorPago					= array();
if (sizeof($oValorPago) > 0) {
	foreach ($oValorPago as $cpf => $info) {
		$total					= \Zage\App\Util::to_float($info["mensalidade"]) + \Zage\App\Util::to_float($info["sistema"]) + \Zage\App\Util::to_float($info["juros"]) + \Zage\App\Util::to_float($info["mora"]);
		$aValorPago[$cpf]		= $total;
	}
}

#################################################################################
## Calcular o valor em aberto por formando
#################################################################################
$oValoresDevidos			= \Zage\Fmt\Financeiro::getValorInadimplenciaPorFormando($system->getCodOrganizacao());
$aValoresDevidos			= array();

#################################################################################
## Montar o array para facilitar a impressão no grid dos valores em aberto
#################################################################################
for ($i = 0; $i < sizeof($oValoresDevidos); $i++) {
	$cpf		= $oValoresDevidos[$i][0]->getCgc();
	$aValoresDevidos[$cpf]	= \Zage\App\Util::to_float($oValoresDevidos[$i]["valor"]) - \Zage\App\Util::to_float($oValoresDevidos[$i]["valor_pago"]);
}


#################################################################################
## Cria o objeto do Grid (bootstrap)
#################################################################################
$grid			= \Zage\App\Grid::criar(\Zage\App\Grid\Tipo::TP_BOOTSTRAP,"GMensalidadeFormando");
$checkboxName	= "selItemMenForLis";
$grid->adicionaCheckBox($checkboxName);
$grid->adicionaTexto($tr->trans('NOME'),				20	,$grid::CENTER	,'nome');
$grid->adicionaTexto($tr->trans('CPF'),					12	,$grid::CENTER	,'cpf','cpf');
$grid->adicionaMoeda($tr->trans('R$ GERADO'),			12	,$grid::CENTER	,'');
$grid->adicionaTexto($tr->trans('R$ A GERAR'),			10	,$grid::CENTER	,'');
$grid->adicionaMoeda($tr->trans('TOTAL PAGO'),			10	,$grid::CENTER	,'');
$grid->adicionaTexto($tr->trans('EM ATRASO'),			10	,$grid::CENTER	,'');
$grid->adicionaTexto($tr->trans('STATUS'),				12	,$grid::CENTER	,'');
$grid->adicionaIcone(null,'fa fa-file-text-o green'		,$tr->trans('Contrato'));
$grid->adicionaIcone(null,'fa fa-sign-out red'			,$tr->trans('Desistir'));
$grid->adicionaIcone(null,'fa fa-search blue'			,$tr->trans('Visualizar contas'));
$grid->importaDadosDoctrine($formandos);

#################################################################################
## Popula os valores dos botões
#################################################################################
for ($i = 0; $i < sizeof($formandos); $i++) {
	$fid		= \Zage\App\Util::encodeUrl('_codMenu_='.$_codMenu_.'&_icone_='.$_icone_.'&codFormando='.$formandos[$i]->getCodigo());
	$cid		= \Zage\App\Util::encodeUrl('_codMenu_='.$_codMenu_.'&_icone_='.$_icone_.'&codUsuario='.$formandos[$i]->getCodigo()."&urlVoltar=".$url);
	
	#################################################################################
	## Definir o valor da Checkbox
	#################################################################################
	$grid->setValorCelula($i,0,$formandos[$i]->getCodigo());
	
	#################################################################################
	## Link no nome
	#################################################################################
	$linkNome = 'javascript:zgLoadUrl(\''.ROOT_URL.'/Fmt/mensalFormandoContaLis.php?id='.$fid.'\');';
	$grid->setValorCelula($i,1,'<a href="'.$linkNome.'">'.$formandos[$i]->getNome().'</a>');
	
	#################################################################################
	## Atualizar a coluna status com o status da associação do formando a Organização (Formatura)
	#################################################################################
	$oStatus	= $em->getRepository('Entidades\ZgsegUsuarioOrganizacao')->findOneBy(array('codUsuario' => $formandos[$i]->getCodigo(),'codOrganizacao' => $system->getCodOrganizacao()));
	$codStatus	= ($oStatus->getCodStatus()) ? $oStatus->getCodStatus()->getCodigo() : null;
	$status		= ($oStatus->getCodStatus()) ? $oStatus->getCodStatus()->getDescricao() : null;
	$grid->setValorCelula($i,7,$status);
	
	#################################################################################
	## Verificar o status da associação a Formatura, para definir se poderá ou não
	## Gerar mensalidade para o Formando
	#################################################################################
	switch ($codStatus) {
		case "A":
		case "P":
		case "B":
			$podeDesistir	= true;
			$podeMarcar		= true;
			break;
		case "D":
			$podeDesistir	= false;
			$podeMarcar		= false;
			break;
		case "T":
			$podeDesistir	= false;
			$podeMarcar		= false;
			break;
		default:
			$podeDesistir	= false;
			$podeMarcar		= false;
			break;
	
	}

	#################################################################################
	## Desabilitar o checkbox para os formandos desistentes
	#################################################################################
	if (!$podeMarcar)	$grid->desabilitaCelula($i, 0);
	
	#################################################################################
	## Resgata o registro da Pessoa associada ao Formando
	#################################################################################
	$oPessoa			= \Zage\Fin\Pessoa::getPessoaUsuario($system->getCodOrganizacao(),$formandos[$i]->getCodigo());
	if (!$oPessoa) 		die('1'.\Zage\App\Util::encodeUrl('||'.htmlentities('Violação de acesso, 0x871FB, Pessoa não encontrada')));
	
	#################################################################################
	## Saldo a provisionar
	#################################################################################
	$valProvisionado			= (isset($aValorProvisionado[$formandos[$i]->getCpf()])) ? $aValorProvisionado[$formandos[$i]->getCpf()] : 0;
	$totalAProvisionar			= (isset($aValorAProvisionar[$formandos[$i]->getCodigo()])) ? $aValorAProvisionar[$formandos[$i]->getCodigo()] : 0;
	$saldo						= round($totalAProvisionar - $valProvisionado,2);
	$grid->setValorCelula($i,3,$valProvisionado);

	#################################################################################
	## Verifica se o formando já tem contrato
	#################################################################################
	$temContrato 		= $em->getRepository('Entidades\ZgfmtContratoFormando')->findOneBy(array('codOrganizacao' => $system->getCodOrganizacao() , 'codFormando' => $formandos[$i]->getCodigo()));
	if ($temContrato) {
		$grid->setIconeCelula($i, 8, 'fa fa-file-text-o green');
	}else{
		$grid->setIconeCelula($i, 8, 'fa fa-file-text-o red');
	}
	
	$grid->setUrlCelula($i,8,"javascript:zgAbreModal('".ROOT_URL."/Fmt/usuarioFormandoContrato.php?id=".$cid."');");
	
	
	#################################################################################
	## Déficit de geração
	#################################################################################
//	if ($podeDesistir	== true) {
	if ($saldo > 0) {
		$grid->setValorCelula($i, 4, "<span style='color:red'><i class='fa fa-arrow-down red'></i> ".\Zage\App\Util::to_money($saldo)."</span>");
	}else if ($saldo == 0) {
		$grid->setValorCelula($i, 4, "<span style='color:green'><i class='fa fa-check-circle green'></i> ".\Zage\App\Util::to_money($saldo)."</span>");
	}else{
		$grid->setValorCelula($i, 4, "<span style='color:green'><i class='fa fa-arrow-up green'></i> ".\Zage\App\Util::to_money($saldo)."</span>");
	}
/*	}else{
		$grid->setValorCelula($i, 4, "<span style='color:green'><i class='fa fa-check-circle green'></i>".\Zage\App\Util::to_money(0)."</span>");
	}*/

	#################################################################################
	## Verificar se já foi gerada alguma mensalidade
	#################################################################################
	$temMensalidade				= \Zage\Fmt\Financeiro::temMensalidadeGerada($system->getCodOrganizacao(), $oPessoa->getCodigo());
	if (($temMensalidade) || (!$temContrato))		{
		$aCodigos[$formandos[$i]->getCodigo()]["PODE_GERAR"]	= 0;
	}else{
		//$log->info("Formando: (".$formandos[$i]->getCodigo().") CPF: ".$formandos[$i]->getCpf()." pode gerar, temContrato: ".(($temContrato) ? 1 : 0)." temMensalidade: ".var_dump($temMensalidade));
		$aCodigos[$formandos[$i]->getCodigo()]["PODE_GERAR"]	= 1;
	}
	
	#################################################################################
	## Verificar se pode fazer atualização de valores
	#################################################################################
	if ($temMensalidade && $temContrato && $saldo > 0) {
		$aCodigos[$formandos[$i]->getCodigo()]["PODE_ATUALIZAR"]	= 1;
	}else{
		$aCodigos[$formandos[$i]->getCodigo()]["PODE_ATUALIZAR"]	= 0;
	}

	#################################################################################
	## Verificar se pode gerar o contrato em massa
	#################################################################################
	if ($temMensalidade)	{
		$aCodigos[$formandos[$i]->getCodigo()]["PODE_CONTRATO"]	= 0;
	}else{
		$aCodigos[$formandos[$i]->getCodigo()]["PODE_CONTRATO"]	= 1;
	}
	
	
	#################################################################################
	## Valor pago
	#################################################################################
	$valPago				= \Zage\App\Util::to_float($aValorPago[$formandos[$i]->getCpf()]);
	$grid->setValorCelula($i, 5, $valPago);

	#################################################################################
	## Valor em aberto
	#################################################################################
	$valAberto				= \Zage\App\Util::to_float($aValoresDevidos[$formandos[$i]->getCpf()]);
	if ($valAberto > 0){
		$grid->setValorCelula($i, 6, "<span style='color:red'><i class='fa fa-exclamation-circle red'></i> ".\Zage\App\Util::to_money($valAberto)."</span>");
	}else{
		$grid->setValorCelula($i, 6, "<span style='color:green'><i class='fa fa-check-circle green'></i> ".\Zage\App\Util::to_money($valAberto)."</span>");
	}
	
	#################################################################################
	## Definir a ação do botão de desistência
	#################################################################################
	if ($podeDesistir	== true) {
		
		#################################################################################
		## Definir o link do botão de geração de conta
		#################################################################################
		$grid->setUrlCelula($i,9,ROOT_URL.'/Fmt/desistenciaCad.php?id='.$fid);
	
	}else{
		$grid->desabilitaCelula($i, 9);
	}
	
	#################################################################################
	## Definir o link do botão de visualização das contas
	#################################################################################
	$grid->setUrlCelula($i,10,ROOT_URL.'/Fmt/mensalFormandoContaLis.php?id='.$fid);
	
}

#################################################################################
## Urls de ações em lote
#################################################################################
$gerUrl				= ROOT_URL . "/Fmt/mensalGerAuto.php?id=".$id;
$atuUrl				= ROOT_URL . "/Fmt/mensalAtuAuto.php?id=".$id;
$conUrl				= ROOT_URL . "/Fmt/usuarioFormandoContrato.php?id=".\Zage\App\Util::encodeUrl('_codMenu_='.$_codMenu_.'&_icone_='.$_icone_."&urlVoltar=".$url);

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
$tpl->set('CHECK_NAME'				,$checkboxName);
$tpl->set('TITULO'					,'Gerenciar Mensalidades');
$tpl->set('JSON_CODIGOS'			,json_encode($aCodigos));
$tpl->set('GER_URL'					,$gerUrl);
$tpl->set('ATU_URL'					,$atuUrl);
$tpl->set('CON_URL'					,$conUrl);

#################################################################################
## Por fim exibir a página HTML
#################################################################################
$tpl->show();
