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
	private $itens;
	
	/**
	 * Inidicador de agrupamento de itens
	 */
	private $indAgruparItens;
	
	#################################################################################
	## Construtor
	#################################################################################
	public function __construct() {
		global $system;
		
		#################################################################################
		## Inicializa o array de itens
		#################################################################################
		$this->itens	= array();
		
		#################################################################################
		## Verificar o parâmetro de agrupamento de itens
		#################################################################################
		$indAgrupar	= \Zage\Adm\Parametro::getValorOrganizacao('AGRUPA_MENSAGENS_ERROS_IGUAIS',$system->getCodOrganizacao(),true);
		if (!$indAgrupar)	{
			$this->naoAgruparItens();
		}else{
			$this->agruparItens();
		}
		
	}
	
	#################################################################################
	## Adiciona um Item
	#################################################################################
	public function adicionaItem($tipo,$posicao,$linha,$tipoRegistro,$mensagem) {

		#################################################################################
		## Verifica se o tipo de item é válido
		#################################################################################
		switch ($tipo) {
			case \Zage\App\FilaImportacao\ResumoPDF\Item::TIPO_AVISO:
			case \Zage\App\FilaImportacao\ResumoPDF\Item::TIPO_ERRO	:
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
		if ($this->getIndAgruparItens()) {
			
			#################################################################################
			## o índice é a própria mensagem
			#################################################################################
			$i		= $mensagem;
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
			$linhas		=	$this->itens[$i]->getLinha() . ",".$linha; 
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
		$this->indAgruparItens = true;
	}
	
	/**
	 * Gerar o PDF do Resumo
	 */
	public function geraPDF() {

		#################################################################################
		## Inicializa o PDF
		#################################################################################
		$pdf		= new \Zage\App\FilaImportacao\ResumoPDF\PDF();
	
		#################################################################################
		## Monta as linhas 
		#################################################################################
		if (sizeof($this->itens) == 0)	return;
		
		foreach ($this->itens as $item) {
			
			if ($this->getIndAgruparItens()) {
				$pdf->addRow(
					array(
						array($item->getTipo(),80,'C'),
						array($item->getPosicao(),80,'C'),
						array($item->getTipoRegistro(),80,'C'),
						array($item->getLinha(),200,'C'),
						array($item->getQuantidade(),80,'C')
					),
					'N',
					'L',
					0,
					null
				);
			
			}else{
				$pdf->addRow(
					array(
						array($item->getTipo(),100,'C'),
						array($item->getPosicao(),100,'C'),
						array($item->getTipoRegistro(),100,'C'),
						array($item->getLinha(),100,'C'),
					),
					'B',
					'L',
					0,
					null
				);
			}
			$pdf->addText($item->getMensagem(),"L","N");
			$pdf->addLine();
		}
	
		$conteudo = $pdf->render();
		return $conteudo;
	}
	
	/**
	 * Resgatar o Pdf de erros
	 */
	public function getPdf() {
		$conteudo 		= $this->geraPDF();
		return $conteudo;
	}
	
}
