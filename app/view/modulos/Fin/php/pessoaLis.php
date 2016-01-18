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
## Variáveis globais
#################################################################################
global $em,$system,$tr,$tipoCadPessoa;

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
	
	if (!isset($tipoCadPessoa) || ($tipoCadPessoa == "C")) {
		$indTipo		= "indCliente";
		$nomeTipoPessoa	= "Clientes";
		$tipoCadPessoa	= "C";
	}elseif ($tipoCadPessoa 	== "F") {
		$indTipo		= "indFornecedor";
		$nomeTipoPessoa	= "Fornecedores";
	}elseif ($tipoCadPessoa	== "T") {
		$indTipo		= "indTransportadora";
		$nomeTipoPessoa	= "Transportadoras";
	}
	
	$pessoas	= \Zage\Fin\Pessoa::lista($system->getCodOrganizacao(),array("F","J"),$indTipo,null,null,null,null,2);
	
//	$pessoas	= $em->getRepository('Entidades\ZgfinPessoa')->findBy(array('codOrganizacao' => $system->getCodOrganizacao(), $indTipo => 1,'codTipoPessoa' => array("F","J")), array('nome' => 'ASC'));
} catch (\Exception $e) {
	\Zage\App\Erro::halt($e->getMessage());
}
	
#################################################################################
## Cria o objeto do Grid (bootstrap)
#################################################################################
$grid			= \Zage\App\Grid::criar(\Zage\App\Grid\Tipo::TP_BOOTSTRAP,"GPessoas");
$grid->setMostraBarraExportacao(false);
$grid->adicionaTexto($tr->trans('NOME / RAZÃO SOCIAL'),	20, $grid::CENTER	,'nome');
$grid->adicionaTexto($tr->trans('NOME FANTASIA'),		20, $grid::CENTER	,'fantasia');
$grid->adicionaTexto($tr->trans('CNPJ / CPF'),			10, $grid::CENTER	,'cgc');
$grid->adicionaTexto($tr->trans('TIPO'), 				10, $grid::CENTER	,'codTipoPessoa:descricao');
$grid->adicionaTexto($tr->trans('STATUS'), 				10, $grid::CENTER	,'');
$grid->adicionaBotao(\Zage\App\Grid\Coluna\Botao::MOD_EDIT);
//$grid->adicionaBotao(\Zage\App\Grid\Coluna\Botao::MOD_REMOVE);
$grid->importaDadosDoctrine($pessoas);

#################################################################################
## Guarda numa variável o tipo da organização que está logada
#################################################################################
$oOrg		= $em->getRepository('Entidades\ZgadmOrganizacao')->findOneBy(array('codigo' => $system->getCodOrganizacao()), array('nome' => 'ASC')); 
$tipoOrg	= $oOrg->getCodTipo()->getCodigo();


#################################################################################
## Verifica se a organização é uma formatura e se @author cassela
## administrada por um Cerimonial, para resgatar as pessoas do cerimonial também
#################################################################################
if ($tipoOrg == "FMT") {
	$oFmtAdm		= \Zage\Fmt\Formatura::getCerimonalAdm($system->getCodOrganizacao());
}else{
	$oFmtAdm		= null;
}


#################################################################################
## Popula os valores dos botões
#################################################################################
for ($i = 0; $i < sizeof($pessoas); $i++) {

	$uid		= \Zage\App\Util::encodeUrl('_codMenu_='.$_codMenu_.'&_icone_='.$_icone_.'&codPessoa='.$pessoas[$i]->getCodigo().'&tipoCadPessoa='.$tipoCadPessoa.'&url='.$url);
	if (strlen($pessoas[$i]->getCgc()) == 14) {
		$valor	= \Zage\App\Mascara::tipo(\Zage\App\Mascara\Tipo::TP_CNPJ)->aplicaMascara($pessoas[$i]->getCgc());
		$grid->setValorCelula($i,2,$valor);
	}elseif (strlen($pessoas[$i]->getCgc()) == 11) {
		$valor	= \Zage\App\Mascara::tipo(\Zage\App\Mascara\Tipo::TP_CPF)->aplicaMascara($pessoas[$i]->getCgc());
		$grid->setValorCelula($i,2,$valor);
	}
	
	#################################################################################
	## STATUS
	#################################################################################
	if ($oFmtAdm) 		$oPessoOrg = $em->getRepository('Entidades\ZgfinPessoaOrganizacao')->findOneBy(array('codOrganizacao' => $oFmtAdm->getCodigo() , 'codPessoa' => $pessoas[$i]->getCodigo()));
	if (!$oPessoOrg)	$oPessoOrg = $em->getRepository('Entidades\ZgfinPessoaOrganizacao')->findOneBy(array('codOrganizacao' => $oOrg->getCodigo() , 'codPessoa' => $pessoas[$i]->getCodigo()));
	
	if (is_object($oPessoOrg) && $oPessoOrg->getIndAtivo() == 1){
		$grid->setValorCelula($i,4,"<span class=\"label label-success\">ATIVO</span>");
	}else{
		$grid->setValorCelula($i,4,"<span class=\"label label-danger\">INATIVO</span>");
	}
	
	#################################################################################
	## Verificar se a Pessoa pode ser alterada
	#################################################################################
	if ($tipoOrg == "ADM") {
		$podeAlt		=  true;
	}else{
		$codOrgPessoa	= ($pessoas[$i]->getCodParceiro()) ? $pessoas[$i]->getCodParceiro()->getCodigo() : false;
		$codOrgCad		= ($pessoas[$i]->getCodOrganizacaoCadastro()) ? $pessoas[$i]->getCodOrganizacaoCadastro()->getCodigo() : false;
		$podeAlt		= ($codOrgPessoa && $codOrgCad == $system->getCodOrganizacao()) ? true : false;
	}
	$podeAlt = true;
	if ($podeAlt) {
		$grid->setUrlCelula($i,5,ROOT_URL.'/Fin/pessoaAlt.php?id='.$uid);
		//$grid->setUrlCelula($i,6,ROOT_URL.'/Fin/pessoaExc.php?id='.$uid);
	}else{
		$grid->desabilitaCelula($i, 5);
		//$grid->desabilitaCelula($i, 6);
	}
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
$urlAdd			= ROOT_URL.'/Fin/pessoaAlt.php?id='.\Zage\App\Util::encodeUrl('_codMenu_='.$_codMenu_.'&_icone_='.$_icone_.'&codPessoa=&tipoCadPessoa='.$tipoCadPessoa);

#################################################################################
## Carregando o template html
#################################################################################
$tpl	= new \Zage\App\Template();
$tpl->load(HTML_PATH . 'templateLis.html');

#################################################################################
## Define os valores das variáveis
#################################################################################
$tpl->set('GRID'			,$htmlGrid);
$tpl->set('NOME'			,$tr->trans($nomeTipoPessoa));
$tpl->set('URLADD'			,$urlAdd);
$tpl->set('IC'				,$_icone_);

#################################################################################
## Por fim exibir a página HTML
#################################################################################
$tpl->show();
