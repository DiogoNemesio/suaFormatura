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
global $em,$system,$tr;

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
## Verificar parâmetro obrigatório
#################################################################################
if (!isset($codPessoa)) \Zage\App\Erro::halt('Falta de Parâmetros 2');

#################################################################################
## Resgata os dados da Pessoa
#################################################################################
$oPessoa		= $em->getRepository('Entidades\ZgfinPessoa')->findOneBy(array('codigo' => $codPessoa));
if (!$oPessoa)	\Zage\App\Erro::halt('Pessoa não encontrada');

#################################################################################
## Resgata o saldo da pessoa em questão
#################################################################################
$saldo			= \Zage\Fin\Adiantamento::getSaldo($system->getCodOrganizacao(), $codPessoa);


#################################################################################
## Resgata as movimentações de adiantamento
#################################################################################
$aMovAd		= $em->getRepository('Entidades\ZgfinMovAdiantamento')->findBy(array('codOrganizacao' => $system->getCodOrganizacao(),'codPessoa' => $codPessoa),array('dataTransacao' => 'ASC'));

#################################################################################
## Cria o objeto do Grid (bootstrap)
#################################################################################
$grid			= \Zage\App\Grid::criar(\Zage\App\Grid\Tipo::TP_BOOTSTRAP,"GContaHis");
$grid->setPagingType(\Zage\App\Grid\Tipo::PG_NONE);
$grid->setFiltro(0);
$grid->setMostraInfo(0);
$grid->adicionaTexto($tr->trans('ORIGEM'),				15, $grid::CENTER	,'codOrigem:descricao');
$grid->adicionaTexto($tr->trans('OPERAÇÃO'),			8, $grid::CENTER	,'codTipoOperacao:descricao');
$grid->adicionaTexto($tr->trans('CONTA'),				10, $grid::CENTER	,'');
$grid->adicionaData($tr->trans('DATA ADIANTAMENTO'),	15, $grid::CENTER	,'dataAdiantamento');
$grid->adicionaData($tr->trans('DATA TRANSACAO'),		15, $grid::CENTER	,'dataTransacao');
$grid->adicionaMoeda($tr->trans('VALOR'),				10, $grid::CENTER	,'valor');
//$grid->adicionaIcone("#", "fa fa-trash red", "Excluir o recebimento");
$grid->importaDadosDoctrine($aMovAd);

#################################################################################
## Ajustar algumas informações
#################################################################################
for ($i = 0; $i < sizeof($aMovAd); $i++) {
	
	#################################################################################
	## Verificar se o adiantamento é oriundo de alguma conta a pagar ou receber
	#################################################################################
	if ($aMovAd[$i]->getCodContaRec()) {
		$conta		= $aMovAd[$i]->getCodContaRec()->getNumero();
	}elseif ($aMovAd[$i]->getCodContaPag()) {
		$conta		= $aMovAd[$i]->getCodContaPag()->getNumero();
	}else{
		$conta		= null;
	}
	$grid->setValorCelula($i,2,$conta);
}

$gHtml	= $grid->getHtmlCode();

if (!isset($urlVoltar) || (!$urlVoltar)) {
	$urlVoltar			= ROOT_URL . "/Fin/adiantamentoLis.php?id=".$id;
}

#################################################################################
## Carregando o template html
#################################################################################
$tpl	= new \Zage\App\Template();
$tpl->load(\Zage\App\Util::getCaminhoCorrespondente(__FILE__, \Zage\App\ZWS::EXT_HTML));

#################################################################################
## Define os valores das variáveis
#################################################################################
$tpl->set('ID'					,$id);
$tpl->set('TITULO'				,$tr->trans('Detalhamento dos adiantamentos de: '.$oPessoa->getNome()));
$tpl->set('COD_PESSOA'			,$codPessoa);
$tpl->set('GRID'				,$gHtml);
$tpl->set('GRID'				,$gHtml);
$tpl->set('URL_VOLTAR'			,$urlVoltar);
$tpl->set('SALDO'				,\Zage\App\Util::formataDinheiro($saldo));


#################################################################################
## Por fim exibir a página HTML
#################################################################################
$tpl->show();

