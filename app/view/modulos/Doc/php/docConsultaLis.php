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
## Resgata os parâmetros passados pelo formulario
#################################################################################
if (isset($_POST['codTipoDoc'])) 		{
	$codTipoDoc		= \Zage\App\Util::antiInjection($_POST['codTipoDoc']);
}else{
	\Zage\App\Erro::halt($tr->trans('Falta de Parâmetros (%s) ',array('%s' => 'codTipoDoc')));
}

#################################################################################
## Resgata a url desse script
#################################################################################
$url		= ROOT_URL . '/Doc/'. basename(__FILE__)."?id=".$id;
$urlVoltar	= ROOT_URL . '/Doc/docConsulta.php?codTipoDoc='.$codTipoDoc."&id=".$id;
$urlAdd		= null;

#################################################################################
## Cria o objeto do Grid (bootstrap)
#################################################################################
$grid			= \Zage\App\Grid::criar(\Zage\App\Grid\Tipo::TP_BOOTSTRAP,"GDocs");

#################################################################################
## Resgata os dados do grid
#################################################################################
try {
	
	$indices		= \Zage\Doc\Indice::lista($codTipoDoc);
	
	if (!$indices) {
		\Zage\App\Erro::halt($tr->trans('Tipo de Documento não encontrado (%s) ',array('%s' => $codTipoDoc)));
	}
	
	#############################################################################################
	## Verificar se o array de campos foi passado pelo formulário
	#############################################################################################
	if (isset($_POST["_zgIndice"])) {
		$_zgIndice = $_POST["_zgIndice"];
	}else{
		\Zage\App\Erro::halt($tr->trans('Variável POST mal formada'). ', file: '.__FILE__);
	}
	
	#############################################################################################
	## Resgata os parâmetros passados pelo formulario novamente, agora os campos dos índices
	#############################################################################################
	$aIndiceValor	= array();
	$aIndiceGrid	= array();
	$indiceGrid		= 0;
	for ($i = 0; $i < sizeof($indices); $i++) {
		$tipo			= $indices[$i]->getCodTipo()->getCodigo();
		
		if (isset($_zgIndice[$indices[$i]->getCodigo()])) {
			
			$valorIndice	= \Zage\App\Util::antiInjection($_zgIndice[$indices[$i]->getCodigo()]);

			if ($valorIndice != null) {
				switch ($tipo) {
					case "T":
						$tipoComparacao	= "LIKE";
						break;
					case "DIN":
						$valorIndice	= str_replace('.', '', $valorIndice);
						$tipoComparacao	= "EQUAL";
						break;
					case "P":
						$valorIndice	= str_replace('%', '', $valorIndice);
						$tipoComparacao	= "EQUAL";
						break;
					default: 
						$tipoComparacao	= "EQUAL";
						break;
				}
				
				$aIndiceValor[$indices[$i]->getCodigo()]["VALOR"]	= $valorIndice;
				$aIndiceValor[$indices[$i]->getCodigo()]["COMP"]	= $tipoComparacao;
			}
			
		}
		
		if ($indices[$i]->getIndVisivel() == 1) {
			#################################################################################
			## Cria as colunas do grid
			#################################################################################
			switch ($tipo) {
				case "DIN":
					$grid->adicionaMoeda(strtoupper($indices[$i]->getNome()), 	10, $grid::CENTER	, null);
					break;
				default :
					$grid->adicionaTexto(strtoupper($indices[$i]->getNome()), 	15, $grid::CENTER	,null);
					break;
			}
			
			$aIndiceGrid[$indices[$i]->getCodigo()]		= $indiceGrid;
			$indiceGrid++;
		}
		
	}

	$documentos		= \Zage\Doc\Documento::busca($codTipoDoc,$aIndiceValor);
	
} catch (\Exception $e) {
	\Zage\App\Erro::halt($e->getMessage());
}

#################################################################################
## Cria as outras colunas do grid
#################################################################################
$indexDown	= $indiceGrid;
//$indexView	= $indiceGrid + 1;
$grid->adicionaImagem(null, null);
//$grid->adicionaIcone(null,'fa fa-tag',$tr->trans('Indexar'));
//$grid->adicionaBotao(\Zage\App\Grid\Coluna\Botao::MOD_REMOVE);
$grid->importaDadosDoctrine($documentos);

//\Doctrine\Common\Util\Debug::dump($documentos);
#################################################################################
## Popula os valores do grid
#################################################################################
for ($i = 0; $i < sizeof($documentos); $i++) {
	$codDocumento = $documentos[$i]["codigo"];
	
	for ($j = 0; $j < sizeof($indices); $j++) {
		if ($indices[$j]->getIndVisivel() == 1) {
			
			#################################################################################
			## Resgata o valor do índice do documento
			#################################################################################
			//$log->debug ("Buscando Documento: ".$codDocumento. ' Índice: '.$indices[$j]->getCodigo());
			$valor	= $em->getRepository('Entidades\ZgdocIndiceValor')->findOneBy(array('codDocumento' => $codDocumento,'codIndice' => $indices[$j]->getCodigo()));
						
			if ($valor) {
				//\Doctrine\Common\Util\Debug::dump($valor);
				
				#################################################################################
				## Resgata as informações do arquivo,se existir
				#################################################################################
				$file		= $em->getRepository('Entidades\ZgdocArquivoInfo')->findBy(array('codDocumento' => $codDocumento));
				$numFiles	= sizeof($file);
				 
				if ($numFiles == 0) {
					$grid->desabilitaCelula($i, $indexDown);
				}elseif ($numFiles == 1) {
					$downid		= \Zage\App\Util::encodeUrl('_codMenu_='.$_codMenu_.'&_icone_='.$_icone_.'&codArquivo='.$file[0]->getCodigo());
					$urlDown	= ROOT_URL . '/Doc/Down/'.$downid;
					$grid->setUrlCelula($i,$indexDown,$urlDown);
					
					if ($file[0]->getCodTipoArquivo() && $file[0]->getCodTipoArquivo()->getIcone() ) {
						$grid->setEnderecoImagemCelula($i,$indexDown,ICON_URL . '/' .$file[0]->getCodTipoArquivo()->getIcone());
					}else{
						$grid->setEnderecoImagemCelula($i,$indexDown,ICON_URL . '/DOC_EXT_OUTROS.png');
					}
					
				}else {
					$downid		= \Zage\App\Util::encodeUrl('_codMenu_='.$_codMenu_.'&_icone_='.$_icone_.'&codArquivo='.$file[0]->getCodigo());
					$urlDown	= ROOT_URL . '/Doc/Down/'.$downid;
					$grid->setUrlCelula($i,$indexDown,$urlDown);
					
					if ($file[0]->getCodTipoArquivo() && $file[0]->getCodTipoArquivo()->getIcone() ) {
						$grid->setEnderecoImagemCelula($i,$indexDown,ICON_URL . '/' .$file[0]->getCodTipoArquivo()->getIcone());
					}else{
						$grid->setEnderecoImagemCelula($i,$indexDown,ICON_URL . '/DOC_EXT_OUTROS.png');
					}
				}
				
				$grid->setValorCelula($i,$aIndiceGrid[$indices[$j]->getCodigo()],$valor->getValor());
				
			}
		}
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
## Carregando o template html
#################################################################################
$tpl	= new \Zage\App\Template();
$tpl->load(HTML_PATH . 'templateLis.html');

#################################################################################
## Define os valores das variáveis
#################################################################################
$tpl->set('GRID'			,$htmlGrid);
$tpl->set('NOME'			,$tr->trans('Documentos'));
$tpl->set('IC'				,'fa fa-file-text');
$tpl->set('URLADD'			,$urlAdd);

#################################################################################
## Por fim exibir a página HTML
#################################################################################
$tpl->show();
