<?php

namespace Zage\Fin;

/**
 * Gerenciar os Rateios do Contas a Pagar
 * 
 * @package: ContaPagarRateio
 * @Author: Daniel Henrique Cassela
 * @version: 1.0.1
 * 
 */

class ContaPagarRateio extends \Entidades\ZgfinContaPagarRateio {

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
			$qb->select('cpr')
			->from('\Entidades\ZgfinContaPagarRateio','cpr')
			->leftJoin('\Entidades\ZgfinContaPagar', 'cp', \Doctrine\ORM\Query\Expr\Join::WITH, 'cpr.codContaPag = cp.codigo')
			->where($qb->expr()->andX(
				$qb->expr()->eq('cp.codOrganizacao'	, ':codOrganizacao'),
				$qb->expr()->eq('cpr.codContaPag'	, ':codConta')
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
		/*if (!$this->getCodContaPag()) {
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
				$valores[$i]	= \Zage\App\Util::toMysqlNumber($this->_valoresRateio[$i]); 
			}
		}

		#################################################################################
		## Validações dos percentuais
		#################################################################################
		$percs		= array();
		for ($i = 0; $i < $n; $i++) {
			$perc		= \Zage\App\Util::toMysqlNumber(str_replace("%", "", $this->_pctRateio[$i]));
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
				$oCat		= $em->getRepository('Entidades\ZgfinCategoria')->findOneBy(array('codOrganizacao' => $system->getCodOrganizacao(),'codigo' => $this->_categoriasRateio[$i]));
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
				$oCentro		= $em->getRepository('Entidades\ZgfinCentroCusto')->findOneBy(array('codOrganizacao' => $system->getCodOrganizacao(),'codigo' => $this->_centroCustosRateio[$i]));
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
		$erro	= $this->exclui($this->getCodContaPag());
			
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
			$rateio	= new \Entidades\ZgfinContaPagarRateio();
			$rateio->setCodContaPag($this->getCodContaPag());
			$rateio->setCodCategoria($cats[$i]);
			$rateio->setCodCentroCusto($centros[$i]);
			$rateio->setPctValor($percs[$i]);
			
			if ($i == ($n -1)) {
				if ($valorTotal !== $this->_getValorTotal()) {
					$diff	= ($this->_getValorTotal() - $valorTotal);
					$rateio->setValor($valores[$i] + $diff);
				}else{
					$rateio->setValor($valores[$i]);
				}
				
			}else{
				$rateio->setValor($valores[$i]);
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
	 * Cancelar uma conta
	 */
	public function cancela($codConta,$motivo) {
		global $em,$_user,$log,$system,$tr;
		
		#################################################################################
		## Verifica se a conta existe
		#################################################################################
		$oConta		= $em->getRepository('Entidades\ZgfinContaPagar')->findOneBy(array('codOrganizacao' => $system->getcodOrganizacao(), 'codigo' => $codConta));
		
		if (!$oConta) {
			return($tr->trans('Conta %s não encontrada !!!',array('%s' => $codConta)));
		}
		
		#################################################################################
		## Valida o status da conta
		#################################################################################
		$status 	= $oConta->getCodStatus()->getCodigo(); 
		switch ($status) {
			case "A":
			case "P":
				$podeCan	= true;
				break;
			case "L":
			case "S":
			case "C":
				$podeCan	= false;
				break;
			default:
				$podeCan	= false;
				break;
		}
		
		if (!$podeCan) {
			return($tr->trans('Conta não pode ser cancelada, status não permitido (%s)',array('%s' => $oConta->getCodStatus()->getCodigo())));
		}
		
		
/*		if ($status == "A") {
			$oStatus		= $em->getRepository('Entidades\ZgfinContaStatusTipo')->findOneBy(array('codigo' => 'C'));
			$valorCancelar	= ( floatval($oConta->getValor()) + floatval($oConta->getValorJuros()) + floatval($oConta->getValorMora()) - floatval($oConta->getValorDesconto()) - floatval($oConta->getValorCancelado())  );

			$oConta->setValorCancelado($valorCancelar);
			$oConta->setDataCancelamento(new \DateTime("now"));
			$oConta->setCodStatus($oStatus);
			
			$hist		= new \Entidades\ZgfinContaPagHistCanc();
			$hist->setCodConta($oConta);
			$hist->setCodUsuario($_user);
			$hist->setMotivo($motivo);
			
		}elseif ($status == "P") {
			$oStatus		= $em->getRepository('Entidades\ZgfinContaStatusTipo')->findOneBy(array('codigo' => 'SC'));

			$valorCancelar	= $this->getSaldoAPagar($codConta);
			
			$oConta->setValorCancelado($valorCancelar);
			$oConta->setDataCancelamento(new \DateTime("now"));
			$oConta->setCodStatus($oStatus);
				
			$hist		= new \Entidades\ZgfinContaPagHistCanc();
			$hist->setCodConta($oConta);
			$hist->setCodUsuario($_user);
			$hist->setMotivo($motivo);
				
		}
		
		try {
			$em->persist($oConta);
			$em->persist($hist);
			$em->flush();
			$em->detach($hist);
			$em->detach($oConta);
				
			return null;
		} catch (\Exception $e) {
			return $e->getMessage();
		}
		
	*/
		
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
		
		$rateios	= $em->getRepository('Entidades\ZgfinContaPagarRateio')->findBy(array('codContaPag' => $oConta->getCodigo())); 
	
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