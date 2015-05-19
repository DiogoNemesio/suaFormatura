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
$url		= ROOT_URL . "/Rhu/". basename(__FILE__)."?id=".$id;


#################################################################################
## Verificar parâmetros
#################################################################################
if (!isset($codPessoa)) {
	\Zage\App\Erro::halt('Falta de parâmetros');
}

#################################################################################
## Resgata as informações da pessoa
#################################################################################
try {
	$pessoa	= $em->getRepository('Entidades\ZgrhuPessoa')->findOneBy(array ('codigo' => $codPessoa));
} catch (\Exception $e) {
	\Zage\App\Erro::halt($e->getMessage());
}


#################################################################################
## Resgata os dados do grid
#################################################################################
try {
	$funcionario	= $em->getRepository('Entidades\ZgrhuFuncionario')->findBy(array ('codPessoa' => $codPessoa));
} catch (\Exception $e) {
	\Zage\App\Erro::halt($e->getMessage());
}

#################################################################################
## Cria o objeto do Grid (bootstrap)
#################################################################################
$grid			= \Zage\App\Grid::criar(\Zage\App\Grid\Tipo::TP_BOOTSTRAP,"GBloco");
$grid->setMostraBarraExportacao(false);
$grid->adicionaTexto($tr->trans('CHAPA'), 			10, $grid::CENTER	,'chapa');
$grid->adicionaTexto($tr->trans('FUNÇÃO'),	 		20, $grid::CENTER	,'codFuncao:descricao');
$grid->adicionaTexto($tr->trans('SITUAÇÃO'),	 	15, $grid::CENTER	,'codSituacao:descricao');
$grid->adicionaTexto($tr->trans('JORNADA'),		 	15, $grid::CENTER	,'jornada');
$grid->adicionaMoeda($tr->trans('SALÁRIO'),		 	15, $grid::CENTER	,'salario');
$grid->adicionaData($tr->trans('DATA ADMISSÃO'), 	20, $grid::CENTER	,'dataAdmissao');
$grid->adicionaBotao(\Zage\App\Grid\Coluna\Botao::MOD_EDIT);
$grid->importaDadosDoctrine($funcionario);


#################################################################################
## Popula os valores dos botões
#################################################################################
for ($i = 0; $i < sizeof($funcionario); $i++) {
	$bid		= \Zage\App\Util::encodeUrl('_codMenu_='.$_codMenu_.'&_icone_='.$_icone_.'&codFuncionario='.$funcionario[$i]->getCodigo().'&url='.$url);

	#################################################################################
	## Botões de acões
	#################################################################################
	$grid->setUrlCelula($i,6,ROOT_URL.'/Rhu/pessoaAdmitir.php?id='.$bid);
	
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
$urlAdmitir		= ROOT_URL.'/Rhu/pessoaAdmitir.php?id='.\Zage\App\Util::encodeUrl('_codMenu_='.$_codMenu_.'&_icone_='.$_icone_.'&codPessoa='.$codPessoa);
$urlAdmitirJS  .= "'$urlAdmitir'";
$urlVoltar		= ROOT_URL.'/Rhu/pessoaLis.php?id='.\Zage\App\Util::encodeUrl('_codMenu_='.$_codMenu_.'&_icone_='.$_icone_.'&codPessoa=');

#################################################################################
## Gerar os botões de acões
#################################################################################
if($funcionario != null){
	$botao .= '<a  href="javascript:zgLoadUrl('.$urlAdmitirJS.');" class="btn btn-app btn-danger btn-sm">
				<i class="ace-icon fa fa-level-down bigger-200"></i>
					Demitir
				</a>';
}else{	
	$botao .= '<a  href="javascript:zgLoadUrl('.$urlAdmitirJS.');" class="btn btn-app btn-success btn-sm">
				<i class="ace-icon fa fa-level-up bigger-200"></i>
					Admitir
				</a>';
}

#################################################################################
## Carregando o template html
#################################################################################
$tpl	= new \Zage\App\Template();
$tpl->load(\Zage\App\Util::getCaminhoCorrespondente(__FILE__, \Zage\App\ZWS::EXT_HTML));

#################################################################################
## Define os valores das variáveis
#################################################################################
$tpl->set('GRID'			,$htmlGrid);
$tpl->set('NOME'			,$tr->trans('Painel do Funcionário'));
$tpl->set('URLADD'			,$urlAdd);
$tpl->set('URL_VOLTAR'		,$urlVoltar);
$tpl->set('IC'				,$_icone_);
$tpl->set('BOTAO'			,$botao);

$tpl->set('NOME_PESSOA'			,$pessoa->getNome());
$tpl->set('CPF_PESSOA'			,$pessoa->getCpf());
$tpl->set('NASCIMENTO_PESSOA'	,$pessoa->getDataNascimento()->format($system->config["data"]["dateFormat"]));
$tpl->set('RG_PESSOA'			,$pessoa->getRg());
$tpl->set('ORGAO_RG_PESSOA'		,$pessoa->getRgOrgaoExpedidor());
$tpl->set('UF_RG_PESSOA'		,$pessoa->getCodUfRg()->getCodUf());

#################################################################################
## Por fim exibir a página HTML
#################################################################################
$tpl->show();
