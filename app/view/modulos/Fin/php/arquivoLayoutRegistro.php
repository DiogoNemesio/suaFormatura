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
## Verificar parâmetro obrigatório
#################################################################################
if (!isset($codLayout)) \Zage\App\Erro::halt('Falta de Parâmetros 2');

#################################################################################
## Resgata as informações do banco
#################################################################################
try {
	$info = $em->getRepository('Entidades\ZgfinArquivoLayout')->findOneBy(array('codigo' => $codLayout));

	if (!$info)	\Zage\App\Erro::halt('Layout não encontrado !!!');
	
} catch (\Exception $e) {
	\Zage\App\Erro::halt($e->getMessage());
}

$codTipo		= ($info->getCodTipoLayout()) 	? $info->getCodTipoLayout()->getCodigo() 	: null; 
$codBanco		= ($info->getCodBanco()) 		? $info->getCodBanco()->getCodigo() 		: null;
$nome			= $info->getNome();
$codTipoArq		= ($info->getCodTipoLayout()) 	? $info->getCodTipoLayout()->getCodTipoArquivo()->getCodigo() 	: null;
	

#################################################################################
## Resgatas os tipos de registro desse tipo de arquivo
#################################################################################
$tiposRegistro		= $em->getRepository('Entidades\ZgfinArquivoRegistroTipo')->findBy(array('codTipoArquivo' => $codTipoArq),array('codTipoRegistro' => "ASC"));

$htmlBotoes			= "";
if (sizeof($tiposRegistro) > 0) {
	if (!isset($codTipoRegistro)) $codTipoRegistro	 = $tiposRegistro[0]->getCodigo();
	
	for ($i = 0; $i < sizeof($tiposRegistro); $i++) {
		if ($tiposRegistro[$i]->getCodigo() == $codTipoRegistro) {
			$class		= "btn-info";
		}else{
			$class		= "btn-white";
		}
		$bid			= \Zage\App\Util::encodeUrl('_codMenu_='.$_codMenu_.'&_icone_='.$_icone_.'&codLayout='.$codLayout.'&codTipoRegistro='.$tiposRegistro[$i]->getCodigo());
		$urlBotao		= ROOT_URL."/Fin/". basename(__FILE__)."?id=".$bid;
		$htmlBotoes .= '<button type="button" onclick="javascript:zgLoadUrlSeSalvouLayReg(\''.$urlBotao.'\');" class="btn '.$class.' btn-sm btn-bold">'.$tiposRegistro[$i]->getCodTipoRegistro(). ' - '.$tiposRegistro[$i]->getNome().'</button>';
	}
}else{
	if (!isset($codTipoRegistro)) $codTipoRegistro	 = null;
}


#################################################################################
## Select do Formato
#################################################################################
try {
	$aFormato	= $em->getRepository('Entidades\ZgfinArquivoCampoFormato')->findBy(array(),array('nome' => 'ASC'));
	$oFormato	= $system->geraHtmlCombo($aFormato,	'CODIGO', 'NOME',	null, null);
} catch (\Exception $e) {
	\Zage\App\Erro::halt($e->getMessage(),__FILE__,__LINE__);
}

#################################################################################
## Select da Variável
#################################################################################
try {
	$aVariavel	= $em->getRepository('Entidades\ZgfinArquivoVariavel')->findBy(array(),array('variavel' => 'ASC'));
	$oVariavel	= $system->geraHtmlCombo($aVariavel,	'CODIGO', 'VARIAVEL',	'', '');
} catch (\Exception $e) {
	\Zage\App\Erro::halt($e->getMessage(),__FILE__,__LINE__);
}


#################################################################################
## Buscar os registros
#################################################################################
$registros			= $em->getRepository('Entidades\ZgfinArquivoLayoutRegistro')->findBy(array('codLayout' => $codLayout,'codTipoRegistro' => $codTipoRegistro),array('ordem' => "ASC"));

#################################################################################
## Montar a tabela de registros
#################################################################################
$htmlReg			= "";
for ($i = 0; $i < sizeof($registros); $i++) {
	
	
	#################################################################################
	## Monta a combo do Formato
	#################################################################################
	$codFormato		= ($registros[$i]->getCodFormato()) ? $registros[$i]->getCodFormato()->getCodigo() : null;
	$oFormatoInt	= $system->geraHtmlCombo($aFormato,	'CODIGO', 'NOME',	$codFormato, '');
	
	#################################################################################
	## Monta a combo da Variável
	#################################################################################
	$codVariavel	= ($registros[$i]->getCodVariavel()) ? $registros[$i]->getCodVariavel()->getCodigo() : null;
	$oVariavelInt		= $system->geraHtmlCombo($aVariavel,	'CODIGO', 'VARIAVEL',	$codVariavel, '');
	
	$htmlReg	.= '
		<tr>
			<td class="col-sm-1 center"><input type="text" name="ordem[]" readonly value="'.$registros[$i]->getOrdem().'" maxlength="3" autocomplete="off" zg-data-toggle="mask" zg-data-mask="numero"></td>
			<td class="col-sm-1 center"><input type="text" readonly name="posicao[]" value="'.$registros[$i]->getPosicaoInicial().'" maxlength="3" autocomplete="off" zg-data-toggle="mask" zg-data-mask="numero"></td>
			<td class="col-sm-1 center"><input type="text" name="tamanho[]" onchange="alteraTamanhoRegistroLayReg($(this));" value="'.$registros[$i]->getTamanho().'" maxlength="3" autocomplete="off" zg-data-toggle="mask" zg-data-mask="numero"></td>
			<td class="col-sm-2 center"><select class="select2" style="width:100%;" name="codFormato[]" data-rel="select2">'.$oFormatoInt.'</select></td>
			<td class="col-sm-2 center"><select class="select2" style="width:100%;" name="codVariavel[]" data-rel="select2">'.$oVariavelInt.'</select></td>
			<td class="col-sm-1 center"><input type="text" name="valorFixo[]" value="'.$registros[$i]->getValorFixo().'" maxlength="400" autocomplete="off"></td>
			<td class="col-sm-1 center">
				<div data-toggle="buttons" class="btn-group btn-overlap btn-corner">
					<span class="btn btn-sm btn-white btn-info center" onclick="moveUpRegistroLayReg($(this));"><i class="fa fa-arrow-circle-up bigger-150"></i></span>
					<span class="btn btn-sm btn-white btn-info center" onclick="moveDownRegistroLayReg($(this));"><i class="fa fa-arrow-circle-down bigger-150"></i></span>
					<span class="btn btn-sm btn-white btn-info center zgdelete" onclick="delRowRegistroLayReg($(this));"><i class="fa fa-trash bigger-150 red"></i></span>
				</div>
				<input type="hidden" name="codRegistro[]" value="'.$registros[$i]->getCodigo().'">
			</td>
		</tr>
	';
}

#################################################################################
## Url Voltar
#################################################################################
$urlVoltar			= ROOT_URL."/Fin/arquivoLayoutLis.php?id=".$id;
$urlAtualizar		= ROOT_URL."/Fin/". basename(__FILE__)."?id=".$id;

#################################################################################
## Carregando o template html
#################################################################################
$tpl	= new \Zage\App\Template();
$tpl->load(\Zage\App\Util::getCaminhoCorrespondente(__FILE__, \Zage\App\ZWS::EXT_HTML));

#################################################################################
## Define os valores das variáveis
#################################################################################
$tpl->set('URL_FORM'			,$_SERVER['SCRIPT_NAME']);
$tpl->set('URLVOLTAR'			,$urlVoltar);
$tpl->set('URLATUALIZAR'		,$urlAtualizar);
$tpl->set('ID'					,$id);
$tpl->set('COD_LAYOUT'			,$codLayout);
$tpl->set('COD_BANCO'			,$codBanco);
$tpl->set('NOME'				,$nome);
$tpl->set('BOTOES'				,$htmlBotoes);
$tpl->set('REGISTROS'			,$htmlReg);
$tpl->set('FORMATOS'			,$oFormato);
$tpl->set('VARIAVEIS'			,$oVariavel);
$tpl->set('APP_BS_TA_MINLENGTH'	,\Zage\Adm\Parametro::getValor('APP_BS_TA_MINLENGTH'));
$tpl->set('APP_BS_TA_ITENS'		,\Zage\Adm\Parametro::getValor('APP_BS_TA_ITENS'));
$tpl->set('APP_BS_TA_TIMEOUT'	,\Zage\Adm\Parametro::getValor('APP_BS_TA_TIMEOUT'));
$tpl->set('DP'					,\Zage\App\Util::getCaminhoCorrespondente(__FILE__,\Zage\App\ZWS::EXT_DP,\Zage\App\ZWS::CAMINHO_RELATIVO));


#################################################################################
## Por fim exibir a página HTML
#################################################################################
$tpl->show();

