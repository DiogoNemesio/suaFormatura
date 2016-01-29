<?php
namespace Zage\App\FilaImportacao;

/**
 * @package: \Zage\App\FilaImportacao\ResumoPDF
 * @Author: Daniel Henrique Cassela
 * @version: 1.0.1
 * 
 * Gerar um resumo da importação
 */

class ResumoPDF {

	/**
	 * Itens
	 * @var array
	 */
	public $itens;
	
	/**
	 * Inidicador de agrupamento de itens
	 */
	private $indAgruparItens;
	
	/**
	 * Objeto do relatório
	 * @var unknown
	 */
	private $rel;
	
	#################################################################################
	## Construtor
	#################################################################################
	public function __construct() {
		global $system,$log;
		
		#################################################################################
		## Inicializa o array de itens
		#################################################################################
		$this->itens	= array();
		
		#################################################################################
		## Verificar o parâmetro de agrupamento de itens
		#################################################################################
		$indAgrupar	= \Zage\Adm\Parametro::getValorOrganizacao('AGRUPA_MENSAGENS_ERROS_IGUAIS',$system->getCodOrganizacao(),true);
		if (!$indAgrupar || $indAgrupar == 0)	{
			$log->info("Parâmetro: indAgrupar: ".$indAgrupar." Não irei agrupar");
			$this->naoAgruparItens();
		}else{
			$this->agruparItens();
		}
		
	}
	
	#################################################################################
	## Adiciona um Item
	#################################################################################
	public function adicionaItem($tipo,$posicao,$linha,$tipoRegistro,$mensagem) {
		global $system,$log;
		
		#################################################################################
		## Verifica se o tipo de item é válido
		#################################################################################
		switch ($tipo) {
			case \Zage\App\FilaImportacao\ResumoPDF\Item::TIPO_AVISO:
			case \Zage\App\FilaImportacao\ResumoPDF\Item::TIPO_ERRO	:
			case \Zage\App\FilaImportacao\ResumoPDF\Item::TIPO_MENSAGEM	:
				break;
			default:
				throw new \Exception('Tipo de Item de Resumo desconhecido!!! ');
		}
		
		#################################################################################
		## Descobre o nome da classe do Item
		#################################################################################
		$classe	= "\\Zage\\App\\FilaImportacao\\ResumoPDF\\Item\\".$tipo;
		
		#################################################################################
		## Verificar se é para agrupar os não as mensagens iguais
		#################################################################################
		if ($this->getIndAgruparItens() !== false) {
			
			#################################################################################
			## o índice é a própria mensagem
			#################################################################################
			if (is_object($mensagem)) {
				$i	= $mensagem->getMensagem();
			}else{
				$i	= $mensagem;
			}
			
			
		}else{
			
			#################################################################################
			## Calcula o número de itens atuais, para definir o próximo índice do array
			#################################################################################
			$i 		= sizeof($this->itens);
		}
		
		
		#################################################################################
		## Verifica se o item existe
		#################################################################################
		if (!array_key_exists($i, $this->itens)) {
			$this->itens[$i]		= new $classe;
			$this->itens[$i]->setPosicao($posicao);
			$this->itens[$i]->setLinha($linha);
			$this->itens[$i]->setTipoRegistro($tipoRegistro);
			$this->itens[$i]->setMensagem($mensagem);
				
		}elseif (is_object($this->itens[$i])) {
			$qtde		= $this->itens[$i]->getQuantidade();
			if ($qtde == 10) {
				$linhas		= $this->itens[$i]->getLinha() . " ...";
			}elseif ( $qtde < 10) {
				$linhas		= $this->itens[$i]->getLinha() . ",".$linha;
			}else{
				$linhas		= $this->itens[$i]->getLinha();
			}
			$this->itens[$i]->aumentaQuantidade();
			$this->itens[$i]->setLinha($linhas);
		}else{
			throw new \Exception('Item incompatível de índice : '.$i.' !!! ');
		}
	}

	#################################################################################
	## Adiciona um Item do Tipo Erro
	#################################################################################
	public function adicionaErro($posicao,$linha,$tipoRegistro,$mensagem) {
		$this->adicionaItem(\Zage\App\FilaImportacao\ResumoPDF\Item::TIPO_ERRO, $posicao, $linha, $tipoRegistro, $mensagem);
	}
	
	#################################################################################
	## Adiciona um Item do Tipo Aviso
	#################################################################################
	public function adicionaAviso($posicao,$linha,$tipoRegistro,$mensagem) {
		$this->adicionaItem(\Zage\App\FilaImportacao\ResumoPDF\Item::TIPO_AVISO, $posicao, $linha, $tipoRegistro, $mensagem);
	}
	
	#################################################################################
	## Adiciona um Item do Tipo Mensagem
	#################################################################################
	public function adicionaMensagem($posicao,$linha,$tipoRegistro,$mensagem) {
		$this->adicionaItem(\Zage\App\FilaImportacao\ResumoPDF\Item::TIPO_MENSAGEM, $posicao, $linha, $tipoRegistro, $mensagem);
	}
	
	/**
	 *
	 * @return the boolean
	 */
	public function getIndAgruparItens() {
		return $this->indAgruparItens;
	}
	
	/**
	 * Definir para true o parâmetro de agrupar os itens
	 */
	public function agruparItens() {
		$this->indAgruparItens = true;
	}
	
	/**
	 * Definir para false o parâmetro de agrupar os itens
	 */
	public function naoAgruparItens() {
		$this->indAgruparItens = false;
	}
	
	public function geraPdf() {
		#################################################################################
		## Variáveis globais
		#################################################################################
		global $log;
		
		#################################################################################
		## Monta as linhas
		#################################################################################
		$numItens	= sizeof($this->itens);
		if ($numItens == 0)	return;
		
		#################################################################################
		## Criar o relatório
		#################################################################################
		$this->rel	= new \Zage\App\Relatorio(''	,'A4',20,'',15,15,16,16,9,9,'P');
		
		#################################################################################
		## Criação do cabeçalho
		#################################################################################
		//$this->rel->adicionaCabecalho($info->getNome());
		$this->rel->NaoExibeFiltrosNulo();
		
		#################################################################################
		## Criação do Rodapé
		#################################################################################
		$this->rel->adicionaRodape();
		
		#################################################################################
		## Ajustar o timezone
		#################################################################################
		date_default_timezone_set($system->config["data"]["timezone"]);
		setlocale (LC_ALL, 'ptb');
		
		$html	= '<body class="no-skin">';
		$html	.= '<h4 align="center"><strong>Resumo de importação</strong></h4>';
		$html	.= '<br>';
		$table	= '<table style="width: 100%;" class="table table-condensed"><thead>';
		if ($this->getIndAgruparItens()) {
			$table	.= '<tr style="background-color:#FBF8EF; border: 1px solid #000000;">
					<th style="text-align: center; width: 20%;">Tipo da Mensagem</th>
					<th style="text-align: center; width: 10%;">Posição</th>
					<th style="text-align: center; width: 20%;">Tipo do Registro</th>
					<th style="text-align: center; width: 40%;">Sequencial</th>
					<th style="text-align: center; width: 10%;">Qtde</th>
				</tr>';
			$colspan	= 5;
		}else{
			$table	.= '<tr style="background-color:#FBF8EF; border: 1px solid #000000;">
					<th style="text-align: center; width: 20%;">Tipo da Mensagem</th>
					<th style="text-align: center; width: 20%;">Posição</th>
					<th style="text-align: center; width: 30%;">Tipo do Registro</th>
					<th style="text-align: center; width: 30%;">Sequencial</th>
				</tr>';
			$colspan	= 4;
		}
		
		$table	.= "</thead><tbody>";
		

		foreach ($this->itens as $item) {
		
			$mensagem	= $item->getMensagem();
		
			if (is_object($mensagem)) 	{
				$_message	= $mensagem->getMensagem();
			}else{
				$_message	= $mensagem;
			}
			
			if (is_object($item->getTipo())) {
				$_item		= $item->getTipo();
				$tipo		= $_item->getTipo();
				$posicao	= $_item->getPosicao();
				$tipoReg	= $_item->getTipoRegistro();
				$linha		= $_item->getLinha();
				$qtde		= $_item->getQuantidade();
			}else{
				$tipo		= $item->getTipo();
				$posicao	= $item->getPosicao();
				$tipoReg	= $item->getTipoRegistro();
				$linha		= $item->getLinha();
				$qtde		= $item->getQuantidade();
			}
			if ($tipo == "Erro") {
				$icone		= "fa fa-exclamation-circle red";
				$cor		= "#ff0000";
			}elseif ($tipo == "Aviso") {
				$icone		= "fa fa-exclamation-triangle orange";
				$cor		= "#F3E2A9";
			}else{
				$icone		= "fa fa-check-circle green";
				$cor		= "#A9F5D0";
			}
			
			/*$log->info("Tipo: ".serialize($tipo));
			$log->info("Posição: ".serialize($posicao));
			$log->info("TipoReg: ".serialize($tipoReg));
			$log->info("Linha: ".serialize($linha));
			$log->info("Qtde: ".serialize($qtde));
			*/
			$tipoIcon		= '<i class="'.$icone.'"></i>&nbsp;<span style="color: '.$cor.';">'.$tipo."</span>";
			
			if ($this->getIndAgruparItens()) {
				$table	.= '<tr style="background-color:#EFEFEF;">
					<td style="text-align: center; border-left: 1px solid #000000; border-top: 1px solid #000000;">'.$tipoIcon.'</td>
					<td style="text-align: center; border-top: 1px solid #000000;">'.$posicao.'</td>
					<td style="text-align: center; border-top: 1px solid #000000;">'.$tipoReg.'</td>
					<td style="text-align: center; border-top: 1px solid #000000;">'.$linha.'</td>
					<td style="text-align: center; border-right: 1px solid #000000; border-top: 1px solid #000000;">'.$qtde.'</td>
				</tr>';
			}else{
				$table	.= '<tr style="background-color:#EFEFEF;">
					<td style="text-align: center; border-left: 1px solid #000000; border-top: 1px solid #000000;">'.$tipoIcon.'</td>
					<td style="text-align: center; border-top: 1px solid #000000;">'.$posicao.'</td>
					<td style="text-align: center; border-top: 1px solid #000000;">'.$tipoReg.'</td>
					<td style="text-align: center; border-right: 1px solid #000000; border-top: 1px solid #000000;">'.$linha.'</td>
				</tr>';
				

			}
			
			$table .= '<tr><td colspan="'.$colspan.'" style="text-align: center; border-right: 1px solid #000000; border-left: 1px solid #000000; border-bottom: 1px solid #000000;">'.$_message.'</td></tr>';			
		}
		
		$table	.= "</tbody></table>";
		
		$htmlTable	= '
<div class="row">
	<div class="col-sm-12 widget-container-span">
		<div class="widget-body">
			<div class="box-content">'.$table.'</div><!--/span-->
		</div>
	</div>
</div>
</body>';
		
		$html		.= $htmlTable;
		$this->rel->WriteHTML($html);
		
		//\Zage\App\Util::sendHeaderDownload("boleto.pdf","PDF");
		//echo $output->getContent();
		
	}
	
	/**
	 * Resgatar o Pdf de erros
	 */
	public function getPdf() {
		$this->geraPdf();
		$conteudo	= $this->rel->Output('','S');
		return $conteudo;
	}
	
	/**
	 * Resgatar o Pdf de erros
	 */
	public function getPdf1() {
		$conteudo 		= $this->geraPDF();
		return $conteudo;
	}
	
}
