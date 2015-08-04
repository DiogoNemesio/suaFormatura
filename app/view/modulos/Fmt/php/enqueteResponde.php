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
$url		= ROOT_URL . "/App/". basename(__FILE__)."?id=".$id;

#################################################################################
## Resgata os dados do grid
#################################################################################
try {
	//$info	= $em->getRepository('Entidades\ZgappEnquetePergunta')->findBy(array('codStatus' => 'A'));
	$info	= \Zage\App\Enquete::listaEnqueteAtivo();
	if(empty($info)){
		$texto 	  = '<p class="alert alert-success">Não existem perguntas cadastradas para sua formatura.</p>';
		$disabled = "disabled";
	}else{
		for ($i = 0; $i < sizeof($info); $i++) {
			$infoR  = $em->getRepository('Entidades\ZgappEnqueteResposta')->findOneBy(array('codPergunta' => $info[$i]->getCodigo(), 'codUsuario' => $system->getCodUsuario()));
			if (empty($infoR)){
				$info = $info[$i];
				
				/** Valores **/
				$infoVal = $em->getRepository('Entidades\ZgappEnquetePerguntaValor')->findBy(array('codPergunta' => $info->getCodigo()));
				/** Pergunta **/
				$codPergunta	= ($info->getCodigo()) ? $info->getCodigo() : null;
				$descricao		= ($info->getDescricao()) ? "(".$info->getDescricao().")" : null;
				$dataPrazo		= ($info->getDataPrazo() != null) ? $info->getDataPrazo()->format($system->config["data"]["datetimeSimplesFormat"]) : null;
				$codTipo		= ($info->getCodTipo()) ? $info->getCodTipo()->getCodigo() : null;
				$pergunta		= ($info->getPergunta()) ? $info->getPergunta() : null;
				$tamanho		= ($info->getTamanho()) ? $info->getTamanho() : null;
				
				if ($codTipo == 'DT'){
					$reposta = '<div class="input-group col-sm-3 pull-left"><span class="input-group-addon"><i class="ace-icon fa fa-question-circle" data-rel="popover" data-placement="top" data-trigger="hover" data-original-title="<i class=\'ace-icon fa fa-question-circle red\'></i> Ajuda" data-content="Responder enquente."></i></span>
									<input class="form-control datepicker" id="date-timepicker" type="text" name="data" placeholder="Data" maxlength="16" autocomplete="off" zg-data-toggle="mask" zg-data-mask="data"></div>';
				}elseif ($codTipo == 'L'){
					$reposta = '<div class="input-group col-sm-8 pull-left"><span class="input-group-addon"><i class="ace-icon fa fa-question-circle" data-rel="popover" data-placement="top" data-trigger="hover" data-original-title="<i class=\'ace-icon fa fa-question-circle red\'></i> Ajuda" data-content="Responder enquente."></i></span>
								<input class="form-control" id="livreID" type="text" name="livre" placeholder="Resposta" maxlength="200" required autocomplete="off"></div>';
				}elseif ($codTipo == 'LI'){
						
					if ($infoVal) {
						$reposta = null;
						foreach ($infoVal as $val) {
							$reposta		.= '<div class="input-group col-sm-12 pull-left"><input id="listaID" name="lista" type="radio" class="ace" value="'.$val->getValor().'"><span class="lbl">&nbsp;</span>'.$val->getValor().'</div>';
						}
						$reposta	= substr($reposta,0,-1);
					}else{
						$reposta = null;
					}
						
				}elseif ($codTipo == 'N'){
					$reposta = '<div class="input-group col-sm-3 pull-left"><span class="input-group-addon"><i class="ace-icon fa fa-question-circle" data-rel="popover" data-placement="top" data-trigger="hover" data-original-title="<i class=\'ace-icon fa fa-question-circle red\'></i> Ajuda" data-content="Responder enquente."></i></span>
						<input class="form-control" id="numeroID" type="text" name="numero" placeholder="Resposta" maxlength="200" required autocomplete="off" zg-data-toggle="mask" zg-data-mask="numero"></div>';
				}elseif ($codTipo == 'SN'){
					$reposta = '<div class="input-group col-sm-12 pull-left">
						<input id="simNaoID" name="simNao" type="radio" class="ace" value="sim"><span class="lbl">&nbsp;</span>Sim
						</div><div class="input-group col-sm-12 pull-left">
						<input id="simNaoID" name="simNao" type="radio" class="ace" value="nao"><span class="lbl">&nbsp;</span>Não
						</div>';
				}
				break;
			}else{
				$ii = $i + 1;
				if($ii >= sizeof($info)) {
					$texto = '<p class="alert alert-success">Não existem perguntas pendentes no momento.</p>';
					$disabled = "disabled";
				}
				continue;
			}
		}
	}

} catch (\Exception $e) {
	\Zage\App\Erro::halt($e->getMessage());
}

################################################################################
# Url Novo
################################################################################
$uid = \Zage\App\Util::encodeUrl ( '_codMenu_=' . $_codMenu_ . '&_icone_=' . $_icone_ . '&codPergunta=' );
$urlNovo = ROOT_URL . "/Fmt/enqueteResponde.php?id=" . $uid;

################################################################################
# Url Resultado
################################################################################
$uidRes	   = \Zage\App\Util::encodeUrl ( '_codMenu_=' . $_codMenu_ . '&_icone_=' . $_icone_ . '&codEnquete='.$codPergunta. '&url='.$urlNovo );
$urlResult = ROOT_URL . "/App/enqueteRes.php?id=" . $uidRes;

#################################################################################
## Carregando o template html
#################################################################################
$tpl	= new \Zage\App\Template();
$tpl->load ( \Zage\App\Util::getCaminhoCorrespondente ( __FILE__, \Zage\App\ZWS::EXT_HTML ) );

#################################################################################
## Define os valores das variáveis
#################################################################################
$tpl->set('NOME'			,$tr->trans("Responda as perguntas"));
$tpl->set('URLNOVO'			,$urlNovo);
$tpl->set('URLRESULT'		,$urlResult);
$tpl->set('COD_PERGUNTA'	,$codPergunta);
$tpl->set('TEXTO'			,$texto);
$tpl->set('PERGUNTA'		,$pergunta);
$tpl->set('DESCRICAO'		,$descricao);
$tpl->set('RESPOSTA'		,$reposta);
$tpl->set('DISABLED'		,$disabled);
$tpl->set('IC'				,$_icone_);

$tpl->set ( 'DP', \Zage\App\Util::getCaminhoCorrespondente ( __FILE__, \Zage\App\ZWS::EXT_DP, \Zage\App\ZWS::CAMINHO_RELATIVO ) );
#################################################################################
## Por fim exibir a página HTML
#################################################################################
$tpl->show();
