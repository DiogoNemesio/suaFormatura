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
# Resgata as informações do banco
################################################################################
if ($codConvidado) {
	try {
		$info = $em->getRepository ( 'Entidades\ZgfmtListaConvidado' )->findOneBy (array ('codigo' => $codConvidado));
	} catch ( \Exception $e ) {
		\Zage\App\Erro::halt ( $e->getMessage () );
	}
	
	$codGrupo		 = ($info->getCodGrupo()) ? $info->getCodGrupo()->getCodigo() : null;
	$nome			 = ($info->getNome()) ? $info->getNome() : null;
	$telefone		 = ($info->getTelefone()) ? $info->getTelefone() : null;
	$sexo			 = ($info->getSexo()) ? $info->getSexo()->getCodigo() : null;
	$codFaixaEtaria	 = ($info->getCodFaixaEtaria()) ? $info->getCodFaixaEtaria()->getCodigo() : null;
	$email			 = ($info->getEmail()) ? $info->getEmail() : null;
} else {
	$codGrupo 	 	 = null;
	$nome			 = null;
	$telefone		 = null;
	$sexo			 = null;
	$codFaixaEtaria	 = null;
	$email			 = null;
}

#################################################################################
## Resgatas os grupos de convidados
#################################################################################
$grupos		= $em->getRepository('Entidades\ZgfmtConvidadoGrupo')->findBy(array(),array('descricao' => "ASC"));

$htmlBotoes			= "";
if (sizeof($grupos) > 0) {
	if (!isset($codGrupo)) $codGrupo	 = $grupos[0]->getCodigo();

	for ($i = 0; $i < sizeof($grupos); $i++) {
		if ($grupos[$i]->getCodigo() == $codGrupo) {
			$class		= "btn-info";
		}else{
			$class		= "btn-white";
		}
		$bid			= \Zage\App\Util::encodeUrl('_codMenu_='.$_codMenu_.'&_icone_='.$_icone_.'&codGrupo='.$grupos[$i]->getCodigo());
		$urlBotao		= ROOT_URL."/Fmt/". basename(__FILE__)."?id=".$bid;
		$htmlBotoes 	.= '<button type="button" onclick="javascript:zgLoadUrlSeSalvouConv(\''.$urlBotao.'\');" class="btn '.$class.' btn-sm btn-bold">'.$grupos[$i]->getDescricao().'</button>';
	}
}else{
	if (!isset($codGrupo)) $codGrupo	 = null;
}

#################################################################################
## Select da Faixa Etaria
#################################################################################
try {
	$aSexo	= $em->getRepository('Entidades\ZgsegSexoTipo')->findBy(array(),array('descricao' => 'ASC'));
	$oSexo	= $system->geraHtmlCombo($aSexo,	'CODIGO', 'DESCRICAO',	$sexo, null);
} catch (\Exception $e) {
	\Zage\App\Erro::halt($e->getMessage(),__FILE__,__LINE__);
}

#################################################################################
## Select da Faixa Etaria
#################################################################################
try {
	$aFaixaEtaria	= $em->getRepository('Entidades\ZgfmtConvidadoFaixaEtaria')->findBy(array(),array('descricao' => 'ASC'));
	$oFaixaEtaria	= $system->geraHtmlCombo($aFaixaEtaria,	'CODIGO', 'DESCRICAO',	$codFaixaEtaria, null);
} catch (\Exception $e) {
	\Zage\App\Erro::halt($e->getMessage(),__FILE__,__LINE__);
}

#################################################################################
## Select do Grupo
#################################################################################
try {
	$aGrupo	= $em->getRepository('Entidades\ZgfmtConvidadoGrupo')->findBy(array(),array('descricao' => 'ASC'));
	$oGrupo	= $system->geraHtmlCombo($aGrupo,	'CODIGO', 'DESCRICAO',	$codGrupo, null);
} catch (\Exception $e) {
	\Zage\App\Erro::halt($e->getMessage(),__FILE__,__LINE__);
}

#################################################################################
## Buscar os convidados
#################################################################################
$convidados			= $em->getRepository('Entidades\ZgfmtListaConvidado')->findBy(array('codUsuario' => $system->getCodUsuario(), 'codGrupo' => $codGrupo));

#################################################################################
## Montar a tabela de convidados
#################################################################################
$htmlReg			= "";
for ($i = 0; $i < sizeof($convidados); $i++) {
	#################################################################################
	## Monta a combo do Sexo
	#################################################################################
	$codSexo	= ($convidados[$i]->getSexo()) ? $convidados[$i]->getSexo()->getCodigo() : null;
	$oSexoInt	= $system->geraHtmlCombo($aSexo,	'CODIGO', 'DESCRICAO',	$codSexo, null);
	
	#################################################################################
	## Monta a combo da Faixa Etaria
	#################################################################################
	$faixaEtaria		= ($convidados[$i]->getCodFaixaEtaria()) ? $convidados[$i]->getCodFaixaEtaria()->getCodigo() : null;
	$oFaixaEtariaInt	= $system->geraHtmlCombo($aFaixaEtaria,	'CODIGO', 'DESCRICAO',	$faixaEtaria, null);

	#################################################################################
	## Monta a combo de Grupo
	#################################################################################
	$codGrupo		= ($convidados[$i]->getCodGrupo()) ? $convidados[$i]->getCodGrupo()->getCodigo() : null;
	$oGrupoInt		= $system->geraHtmlCombo($aGrupo,	'CODIGO', 'DESCRICAO',	$codGrupo, null);

	$htmlReg	.= '
	<tr>
			<td class="col-sm-2 center"><input type="text" name="nome[]" value="'.$convidados[$i]->getNome().'" maxlength="100" style="width:100%;" autocomplete="off"></td>
			<td class="col-sm-1 center"><input type="text" name="telefone[]" value="'.$convidados[$i]->getTelefone().'" maxlength="15" autocomplete="off" zg-data-toggle="mask" zg-data-mask="fone" zg-data-mask-retira="1"></td>
			<td class="col-sm-2 center"><select class="select2" style="width:100%;" name="codFaixaEtaria[]" data-rel="select2">'.$oFaixaEtariaInt.'</select></td>
			<td class="col-sm-2 center"><select class="select2" style="width:100%;" name="sexo[]" data-rel="select2">'.$oSexoInt.'</select></td>
			<td class="col-sm-1 center"><input type="text" name="email[]" value="'.$convidados[$i]->getEmail().'" maxlength="100" autocomplete="off"></td>
			<td class="col-sm-1 center">
				<div data-toggle="buttons" class="btn-group btn-overlap btn-corner">
					<span class="btn btn-sm btn-white btn-info center zgdelete" onclick="delRowConvidadoLayReg($(this));"><i class="fa fa-trash bigger-150 red"></i></span>
				</div>
				<input type="hidden" name="codConvidado[]" value="'.$convidados[$i]->getCodigo().'">
				</td>
		</tr>
	';
}

################################################################################
# Url Voltar
################################################################################
$urlVoltar = ROOT_URL . "/Fmt/convidadoLis.php?id=" . $id;

################################################################################
# Url Novo
################################################################################
$uid = \Zage\App\Util::encodeUrl ( '_codMenu_=' . $_codMenu_ . '&_icone_=' . $_icone_ . '&codConvidado=' );
$urlNovo = ROOT_URL . "/Fmt/convidadoAlt.php?id=" . $uid;

################################################################################
# Carregando o template html
################################################################################
$tpl = new \Zage\App\Template ();
$tpl->load ( \Zage\App\Util::getCaminhoCorrespondente ( __FILE__, \Zage\App\ZWS::EXT_HTML ) );

################################################################################
# Define os valores das variáveis
################################################################################
$tpl->set ( 'URL_FORM'			   , $_SERVER ['SCRIPT_NAME'] );
$tpl->set ( 'URLVOLTAR'			   , $urlVoltar );
$tpl->set ( 'URLNOVO'		 	   , $urlNovo );
$tpl->set ( 'ID'				   , $id );
$tpl->set ( 'COD_CONVIDADO'	  	   , $codConvidado);
$tpl->set ( 'COD_GRUPO'	  		   , $oGrupo);
$tpl->set ( 'NOME'				   , $nome);
$tpl->set ( 'TELEFONE'			   , $telefone);
$tpl->set ( 'SEXO'				   , $oSexo);
$tpl->set ( 'COD_FAIXA_ETARIA'	   , $oFaixaEtaria);
$tpl->set ( 'EMAIL'				   , $email);
$tpl->set ( 'CONVIDADOS'		   , $htmlReg);
$tpl->set ( 'BOTOES'			   , $htmlBotoes);

$tpl->set ( 'DP', \Zage\App\Util::getCaminhoCorrespondente ( __FILE__, \Zage\App\ZWS::EXT_DP, \Zage\App\ZWS::CAMINHO_RELATIVO ) );

################################################################################
# Por fim exibir a página HTML
################################################################################
$tpl->show ();

