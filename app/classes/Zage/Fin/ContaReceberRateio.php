<?php

namespace Zage\Fin;

/**
 * Gerenciar os Rateios do Contas a Receber
 * 
 * @package: ContaReceberRateio
 * @Author: Daniel Henrique Cassela
 * @version: 1.0.1
 * 
 */

class ContaReceberRateio extends \Entidades\ZgfinContaReceberRateio {

	/**
     * Construtor
     *
	 * @return void
	 */
	public function __construct() {
		global $log;
		$log->debug(__CLASS__.": nova Instância");
	}
	
	/**
	 * Código
	 * @var unknown
	 */
	private $_codigo;
	
	/**
	 * Array com os códigos de Rateio
	 * @var array
	 */
	private $_codigosRateio;
	
	/**
	 * Array com as categorias
	 * @var array
	 */
	private $_categoriasRateio;
	
	/**
	 * Array com as Centros de Custo
	 * @var array
	 */
	private $_centroCustosRateio;
	
	/**
	 * Array com os valores de rateio
	 * @var array
	 */
	private $_valoresRateio;
	
	/**
	 * Array com os percentuais de rateio
	 * @var array
	 */
	private $_pctRateio;
	
	/**
	 * Código da Conta
	 * @var unknown
	 */
	private $_codConta;
	
	/**
	 * Valor total da conta
	 * @var float
	 */
	private $_valorTotal;
	
	/**
	 *
	 * Lista
	 */
	public static function lista ($codConta) {
		global $em,$system;
		
		$qb 	= $em->createQueryBuilder();
		try {
			$qb->select('crr')
			->from('\Entidades\ZgfinContaReceberRateio','crr')
			->leftJoin('\Entidades\ZgfinContaReceber', 'cp', \Doctrine\ORM\Query\Expr\Join::WITH, 'crr.codContaRec = cp.codigo')
			->where($qb->expr()->andX(
				$qb->expr()->eq('cp.codOrganizacao'	, ':codOrganizacao'),
				$qb->expr()->eq('crr.codContaRec'	, ':codConta')
			))
			->orderBy('cp.codigo','ASC')
			->setParameter('codConta', $codConta)
			->setParameter('codOrganizacao', $system->getCodOrganizacao());
			
			$query 		= $qb->getQuery();
			return($query->getResult());
		} catch (\Exception $e) {
			\Zage\App\Erro::halt($e->getMessage());
		}
	}
	
	/**
	 * Salva a conta no banco
	 */
	public function salva () {
		global $em,$system,$log,$tr;
		
		#################################################################################
		## Validações de campos 
		#################################################################################
		/*if (!$this->getCodContaRec()) {
			return $tr->trans('Código da conta não informado !!!');
		}*/
		
		#################################################################################
		## Validações do rateio
		#################################################################################
		if (!is_array($this->_codigosRateio)) {
			return $tr->trans('"Códigos de Rateio" deve ser um array');
		}
		
		if (!is_array($this->_categoriasRateio)) {
			return $tr->trans('"Categoria" deve ser um array');
		}
		
		if (!is_array($this->_centroCustosRateio)) {
			return $tr->trans('"Centro de Custo" deve ser um array');
		}
		
		if (sizeof($this->_centroCustosRateio) != sizeof($this->_categoriasRateio)) {
			return $tr->trans('Quantidade de Centro de Custos difere da Quantidade de Categorias');
		}
		
		if (!is_array($this->_valoresRateio)) {
			return $tr->trans('"Valor" deve ser um array');
		}
		
		if (!is_array($this->_pctRateio)) {
			return $tr->trans('"Percentual" deve ser um array');
		}
		
		if (sizeof($this->_valoresRateio) != sizeof($this->_pctRateio)) {
			return $tr->trans('Quantidade de Valores difere da Quantidade de Percentuais');
		}
		
		if (sizeof($this->_centroCustosRateio) != sizeof($this->_valoresRateio)) {
			return $tr->trans('Quantidade de Centro de Custos difere da Quantidade de Valor');
		}
		
		if (sizeof($this->_centroCustosRateio) != sizeof($this->_codigosRateio)) {
			return $tr->trans('Quantidade de Centro de Custos difere da Quantidade de Códigos de Rateio');
		}
		
		if (!$this->_getValorTotal()) {
			return $tr->trans('Valor total não informado !!!');
		}
		
		#################################################################################
		## Calcula o número de registros do rateio
		#################################################################################
		$n		= sizeof($this->_valoresRateio);
		
		$log->debug("Array de valores rateio: ".serialize($this->_valoresRateio));
		
		#################################################################################
		## Validações dos valores 
		#################################################################################
		$valores	= array();
		for ($i = 0; $i < $n; $i++) {
			if ($this->_valoresRateio[$i] == 0) {
				return $tr->trans('Array de valores tem registro com valor = 0 na posição "'.$i.'"');
			}elseif (!\Zage\App\Util::ehNumero($this->_valoresRateio[$i])) {
				return $tr->trans('Array de valores tem registro inválido na posição "'.$i.'" !!!');
			}else{
				$valores[$i]	= \Zage\App\Util::to_float($this->_valoresRateio[$i]); 
			}
		}

		#################################################################################
		## Validações dos percentuais
		#################################################################################
		$percs		= array();
		for ($i = 0; $i < $n; $i++) {
			$perc		= \Zage\App\Util::to_float(str_replace("%", "", $this->_pctRateio[$i]));
			if ($perc == 0) {
				return $tr->trans('Array de Percentuais tem registro com percentual = 0 na posição "'.$i.'" ');
			}elseif (!\Zage\App\Util::ehNumero($perc)) {
				return $tr->trans('Array de Percentuais tem registro inválido na posição "'.$i.'" !!!');
			}else{
				$percs[$i]	= round($perc,2);
			}
		}
		
		#################################################################################
		## Validações das categorias
		#################################################################################
		$cats		= array();
		for ($i = 0; $i < $n; $i++) {
			if ($this->_categoriasRateio[$i]) {
				$oCat		= $em->getRepository('Entidades\ZgfinCategoria')->findOneBy(array('codigo' => $this->_categoriasRateio[$i]));
				if (!$oCat) {
					return $tr->trans('Array de Categorias tem categoria inexistente  na posição "'.$i.'" !!!');
				}else{
					$cats[$i]	= $oCat;
				}
			}else{
				$cats[$i]		= null;
			}
		}
		
		#################################################################################
		## Validações dos Centros de Custo
		#################################################################################
		$centros	= array();
		for ($i = 0; $i < $n; $i++) {
			if ($this->_centroCustosRateio[$i]) {
				$oCentro		= $em->getRepository('Entidades\ZgfinCentroCusto')->findOneBy(array('codigo' => $this->_centroCustosRateio[$i]));
				if (!$oCentro) {
					return $tr->trans('Array de Centro de Custos tem Centro de Custo inexistente na posição "'.$i.'" !!!');
				}else{
					$centros[$i]	= $oCentro;
				}
			}else{
				$centros[$i]	= null;
			}
		}
		
		#################################################################################
		## Apaga os rateios
		#################################################################################
		$erro	= $this->exclui($this->getCodContaRec());
			
		if ($erro) {
			return $erro;
		}
		
		
		#################################################################################
		## Fazer o loop para cadastrar os rateios
		#################################################################################
		$valorTotal	= 0;
		for ($i	= 0; $i < $n; $i++) {
			
			#################################################################################
			## Calcula o total dos valores para aplicar a diferença no último rateio
			#################################################################################
			$valorTotal += $valores[$i];
			

			#################################################################################
			## Copia os valores de um objeto para o outro
			#################################################################################
			$rateio	= new \Entidades\ZgfinContaReceberRateio();
			$rateio->setCodContaRec($this->getCodContaRec());
			$rateio->setCodCategoria($cats[$i]);
			$rateio->setCodCentroCusto($centros[$i]);
			$rateio->setPctValor(\Zage\App\Util::toMysqlNumber($percs[$i]));
			
			
			$log->info("Rateio [".$i."]-> ValorTotal: ".$valorTotal. " ,_getValorTotal(): ".$this->_getValorTotal());
			
			if ($i == ($n -1)) {
				if ($valorTotal !== $this->_getValorTotal()) {
					$diff	= ($this->_getValorTotal() - $valorTotal);
					$rateio->setValor(\Zage\App\Util::toMysqlNumber($valores[$i] + $diff));
				}else{
					$rateio->setValor(\Zage\App\Util::toMysqlNumber($valores[$i]));
				}
				
			}else{
				$rateio->setValor(\Zage\App\Util::toMysqlNumber($valores[$i]));
			}
			
			try {
				$em->persist($rateio);
			} catch (\Exception $e) {
				return $e->getMessage();
			}
		}

		return null;
	}
	
	
	/**
	 * Excluir os rateios de uma conta
	 */
	public function exclui($oConta) {
		global $em,$_user,$log,$system,$tr;
	
		#################################################################################
		## Valida o status da conta
		#################################################################################
		$status 	= $oConta->getCodStatus()->getCodigo();
		
		switch ($status) {
			case "A":
			case "C":
				$podeExc	= true;
				break;
			default:
				$podeExc	= false;
				break;
		}
	
		if (!$podeExc) {
			return($tr->trans('Rateio não pode ser excluído, status não permitido (%s)',array('%s' => $oConta->getCodStatus()->getCodigo())));
		}
		
		$rateios	= $em->getRepository('Entidades\ZgfinContaReceberRateio')->findBy(array('codContaRec' => $oConta->getCodigo())); 
	
		try {
			
			for ($i = 0; $i < sizeof($rateios); $i++) {
				$em->remove($rateios[$i]);
			}
			
			return null;
		} catch (\Exception $e) {
			return $e->getMessage();
		}
	}
	
	
	public function _setCodigo($codigo) {
		$this->_codigo	= $codigo;
	}
	
	public function _getCodigo() {
		return ($this->_codigo);
	}
	
	public function _setCodConta($codConta) {
		$this->_codConta	= $codConta;
	}
	
	public function _getCodConta() {
		return ($this->_codConta);
	}
	
	public function _setArrayCodigosRateio($array) {
		$this->_codigosRateio	= $array;
	}
	
	public function _setArrayCategoriasRateio($array) {
		$this->_categoriasRateio	= $array;
	}
	
	public function _setArrayCentroCustoRateio($array) {
		$this->_centroCustosRateio	= $array;
	}
	
	public function _setArrayValoresRateio($array) {
		$this->_valoresRateio	= $array;
	}
	
	public function _setArrayPctRateio($array) {
		$this->_pctRateio	= $array;
	}
	
	public function _setValorTotal($valorTotal) {
		$this->_valorTotal	= $valorTotal;
	}
	
	public function _getValorTotal() {
		return ($this->_valorTotal);
	}
}