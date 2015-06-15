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


#################################################################################
## Resgatar os parâmetros de sistema, ordenando pela seção
#################################################################################
$parametros 	= $em->getRepository('Entidades\ZgappParametro')->findBy(array('codUso' => 'U'),array('codSecao' => 'ASC'));

#################################################################################
## Inicializa o array de abas
#################################################################################
$abas			= array();
for ($i = 0; $i < sizeof($parametros); $i++) {
	#################################################################################
	## Criando as abas
	#################################################################################
	$aba	= ($parametros[$i]->getCodSecao()) ? $parametros[$i]->getCodSecao()->getNome() 	: "Gerais";
	$icone	= ($parametros[$i]->getCodSecao()) ? $parametros[$i]->getCodSecao()->getIcone() : "fa fa-cog";
	$ordem	= ($parametros[$i]->getCodSecao()) ? $parametros[$i]->getCodSecao()->getOrdem() : -100;
	if (!array_key_exists($aba, $abas)) {
		$abas[$aba]["ICONE"] = $icone;
		$abas[$aba]["ORDEM"] = $ordem;
	}
}


#################################################################################
## Criando o html das abas
#################################################################################
$htmlAbas		= "";
$htmlDivIniAbas	= array();
$htmlDivFimAbas	= "</div>";
$i				= 0;
foreach ($abas as $aba => $conf) {
	$ativo		= ($i == 0) ? "active" : "";
	$htmlAbas	.= '<li class="'.$ativo.'"><a data-toggle="tab" href="#'.$aba.'">';
	$htmlAbas	.= '<i class="'.$conf["ICONE"].' bigger-125"></i> '.$aba.'';
	$htmlAbas	.= '</a></li>';
	
	if (!isset($htmlDivIniAbas[$aba])) $htmlDivIniAbas[$aba] 	= "";
	
	$htmlDivIniAbas[$aba]	.= '<div id="'.$aba.'" class="tab-pane in '.$ativo.'">';
	$htmlDivIniAbas[$aba]	.= '<h4 class="header blue bolder smaller">'.$aba.'</h4>';
	
	$i++;
}


#################################################################################
## Criando o html dos Parâmetros
#################################################################################
for ($i = 0; $i < sizeof($parametros); $i++) {
	
	#################################################################################
	## Nome da Aba
	#################################################################################
	$aba	= ($parametros[$i]->getCodSecao()) ? $parametros[$i]->getCodSecao()->getNome() 	: "Gerais";
	
	
	#################################################################################
	## Criando o html do Parâmetro
	#################################################################################
	$idCampo	= \Zage\Adm\Parametro::geraIdInput($parametros[$i]->getCodigo());
	$htmlDivIniAbas[$aba]	.= '
		<div class="row">
			<div class="form-group col-sm-9" id="div'.$idCampo.'">
				<label for="'.$idCampo.'" class="col-sm-5 control-label">'.$parametros[$i]->getParametro().'</label>
				<div class="input-group col-sm-6 pull-left">
					'.\Zage\Adm\Parametro::geraHtml($parametros[$i]->getCodigo(),$system->getCodOrganizacao(),$system->getCodUsuario()).'
				</div>
			</div>
			<div class="help-block col-sm-1 inline" id="divHelp'.$idCampo.'"></div>
		</div>		
	';
}

#################################################################################
## Ajustando o html dos Parâmetros
#################################################################################
$htmlPar	= "";
foreach ($htmlDivIniAbas as $htmlAba) {
	$htmlPar	.= $htmlAba . $htmlDivFimAbas;
}


################################################################################
# Carregando o template html
################################################################################
$tpl = new \Zage\App\Template ();
$tpl->load ( \Zage\App\Util::getCaminhoCorrespondente ( __FILE__, \Zage\App\ZWS::EXT_HTML ) );

################################################################################
# Define os valores das variáveis
################################################################################
$tpl->set('URL_FORM'	,$_SERVER ['SCRIPT_NAME'] );
$tpl->set('ID'			,$id );
$tpl->set('PARAMETROS'	,$htmlPar); 
$tpl->set('SECOES'		,$htmlAbas);
$tpl->set('DP'			,\Zage\App\Util::getCaminhoCorrespondente(__FILE__,\Zage\App\ZWS::EXT_DP,\Zage\App\ZWS::CAMINHO_RELATIVO));

################################################################################
# Por fim exibir a página HTML
################################################################################
$tpl->show ();

