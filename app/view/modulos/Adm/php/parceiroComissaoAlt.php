<?php
################################################################################
# Includes
################################################################################
if (defined ( 'DOC_ROOT' )) {
	include_once (DOC_ROOT . 'include.php');
} else {
	include_once ('../include.php');
}

################################################################################
# Resgata a variável ID que está criptografada
################################################################################
if (isset ( $_GET ['id'] )) {
	$id = \Zage\App\Util::antiInjection ( $_GET ["id"] );
} elseif (isset ( $_POST ['id'] )) {
	$id = \Zage\App\Util::antiInjection ( $_POST ["id"] );
} elseif (isset ( $id )) {
	$id = \Zage\App\Util::antiInjection ( $id );
} else {
	\Zage\App\Erro::halt ( 'Falta de Parâmetros' );
}

################################################################################
# Descompacta o ID
################################################################################
\Zage\App\Util::descompactaId ( $id );

################################################################################
# Verifica se o usuário tem permissão no menu
################################################################################
$system->checaPermissao ( $_codMenu_ );

################################################################################
# Validar parâmentros
################################################################################
if (!$codOrganizacao){
	\Zage\App\Erro::halt($tr->trans('Parâmentro COD_ORGANIZAÇÃO não encontrado'));
}elseif (!$codPlano){
	\Zage\App\Erro::halt($tr->trans('Parâmentro COD_ORGANIZAÇÃO não encontrado'));
}
################################################################################
# Resgatar as informações de comissao
################################################################################
if ($codVendaPlano){
	
	try {
		$vendaComissao 	= $em->getRepository ('Entidades\ZgadmOrganizacaoVendaComissao')->findBy(array('codVendaPlano' => $codVendaPlano),array('dataCadastro' => DESC));
	} catch (\Exception $e) {
		\Zage\App\Erro::halt ($e->getMessage());
	}	
	
}else{
	
	/** Resgatar variável postada **/
	if (isset($_GET['retCodVendaPlano'])) 		$codVendaPlano			= \Zage\App\Util::antiInjection($_GET['retCodVendaPlano']);
	
	if ($codVendaPlano){
		try {
			$vendaComissao 	= $em->getRepository ('Entidades\ZgadmOrganizacaoVendaComissao')->findBy(array('codVendaPlano' => $codVendaPlano),array('dataCadastro' => DESC));
		} catch (\Exception $e) {
			\Zage\App\Erro::halt ($e->getMessage());
		}
	}
}

################################################################################
# Montar tabela de histórico
################################################################################
for ($i = 0; $i < sizeof($vendaComissao); $i++) {

	$dataBase 		= $vendaComissao[$i]->getDataBase()->format($system->config["data"]["dateFormat"]);
	$dataCadastro 	= $vendaComissao[$i]->getDataCadastro()->format($system->config["data"]["datetimeSimplesFormat"]);

	$tab	.= '<tr>
				<td style="text-align: center;">'.$dataBase.'</td>
				<td style="text-align: center;">'.$vendaComissao[$i]->getPctComissao().'%'.'</td>
				<td style="text-align: center;">'.\Zage\App\Util::formataDinheiro($vendaComissao[$i]->getValorComissao()).'</td>
				<td style="text-align: center;">'.$dataCadastro.'</td>
				<td style="text-align: center;"></td>
				</tr>';
}

################################################################################
# Resgatar informações do plano
################################################################################
$oPlano 	= $em->getRepository ('Entidades\ZgadmPlano')->findOneBy(array('codigo' => $codPlano));

if ($oPlano){
	$nomePlano 		= $oPlano->getNome();
	$valorPlano 	= \Zage\Adm\Plano::getValorPlano($oPlano->getCodigo());
	$licencaPlano 	= $oPlano->getCodTipoLicenca()->getDescricao();
}else{
	$nomePlano 		= null;
	$valorPlano 	= null;
	$licencaPlano 	= null;
}

################################################################################
# Url Atualizar
################################################################################
$uid	= \Zage\App\Util::encodeUrl('_codMenu_='.$_codMenu_.'&_icone_='.$_icone_.'&codPlano='.$codPlano.'&codOrganizacao='.$codOrganizacao.'&codVendaPlano='.$codVendaPlano);
$urlAtualizar 	= ROOT_URL . "/Adm/parceiroComissaoAlt.php?id=" . $uid;

################################################################################
# Url Voltar
################################################################################
$uid			= \Zage\App\Util::encodeUrl('_codMenu_='.$_codMenu_.'&_icone_='.$_icone_.'&codPlano='.$codPlano.'&codOrganizacao='.$codOrganizacao.'&codVendaPlano='.$codVendaPlano);
$urlVoltar 		= ROOT_URL . "/Adm/parceiroComissaoLis.php?id=" . $uid;

################################################################################
# Carregando o template html
################################################################################
$tpl = new \Zage\App\Template();
$tpl->load(\Zage\App\Util::getCaminhoCorrespondente ( __FILE__, \Zage\App\ZWS::EXT_HTML));

################################################################################
# Define os valores das variáveis
################################################################################
$tpl->set ( 'URL_FORM'			  , $_SERVER ['SCRIPT_NAME'] );
$tpl->set ( 'URLVOLTAR'			  , $urlVoltar);
$tpl->set ( 'URLATUALIZAR'	 	  , $urlAtualizar);
$tpl->set ( 'ID'				  , $id );
$tpl->set ( 'COD_VENDA_PLANO'  	  , $codVendaPlano);
$tpl->set ( 'COD_ORGANIZACAO'	  , $codOrganizacao);
$tpl->set ( 'COD_PLANO'			  , $codPlano);
$tpl->set ( 'NOME_PLANO'		  , $nomePlano);
$tpl->set ( 'VALOR_PLANO'		  , \Zage\App\Util::formataDinheiro($valorPlano));
$tpl->set ( 'LICENCA_PLANO'		  , $licencaPlano);

$tpl->set ( 'TAB'				  , $tab);
$tpl->set ( 'DP', \Zage\App\Util::getCaminhoCorrespondente ( __FILE__, \Zage\App\ZWS::EXT_DP, \Zage\App\ZWS::CAMINHO_RELATIVO ) );

################################################################################
# Por fim exibir a página HTML
################################################################################
$tpl->show ();