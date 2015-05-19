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
	
	$cliente	= $em->getRepository('Entidades\ZgfinPessoa')->findBy(array('codOrganizacao' => $system->getCodOrganizacao(), $indTipo => 1), array('nome' => 'ASC'));
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
$grid->adicionaStatus($tr->trans('STATUS'),'indAtivo');
$grid->adicionaBotao(\Zage\App\Grid\Coluna\Botao::MOD_EDIT);
$grid->adicionaBotao(\Zage\App\Grid\Coluna\Botao::MOD_REMOVE);
$grid->importaDadosDoctrine($cliente);


#################################################################################
## Popula os valores dos botões
#################################################################################
for ($i = 0; $i < sizeof($cliente); $i++) {
	$uid		= \Zage\App\Util::encodeUrl('_codMenu_='.$_codMenu_.'&_icone_='.$_icone_.'&codPessoa='.$cliente[$i]->getCodigo().'&tipoCadPessoa='.$tipoCadPessoa.'&url='.$url);
	if (strlen($cliente[$i]->getCgc()) == 14) {
		$valor	= \Zage\App\Mascara::tipo(\Zage\App\Mascara\Tipo::TP_CNPJ)->aplicaMascara($cliente[$i]->getCgc());
		$grid->setValorCelula($i,2,$valor);
	}elseif (strlen($cliente[$i]->getCgc()) == 11) {
		$valor	= \Zage\App\Mascara::tipo(\Zage\App\Mascara\Tipo::TP_CPF)->aplicaMascara($cliente[$i]->getCgc());
		$grid->setValorCelula($i,2,$valor);
	}
	$grid->setUrlCelula($i,5,ROOT_URL.'/Fin/pessoaAlt.php?id='.$uid);
	$grid->setUrlCelula($i,6,ROOT_URL.'/Fin/pessoaExc.php?id='.$uid);
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
