<?php

namespace Zage\Fin;

use Zend\Form\Annotation\Object;
/**
 * Gerenciar as Contas a Receber
 * 
 * @package: ContaReceber
 * @Author: Daniel Henrique Cassela
 * @version: 1.0.1
 * 
 */

class ContaReceber extends \Entidades\ZgfinContaReceber {

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
	 * Array com os valores
	 * @var array
	 */
	private $_valores;
	
	/**
	 * Array com os outros valores
	 * @var array
	 */
	private $_outrosValores;
	
	/**
	 * Array com as datas
	 * @var array
	 */
	private $_datas;
	
	/**
	 * Valor total
	 * @var float
	 */
	private $_valorTotal;
	
	/**
	 * _FlagRecebida
	 * @var int
	 */
	private $_FlagRecebida;
	
	/**
	 * _indValorParcela
	 * @var number
	 */
	private $_indValorParcela;
	
	/**
	 * _indAlterarSeq
	 * @var number
	 */
	private $_indAlterarSeq;
	
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
	 * Código do Grupo de Substituição
	 * @var int
	 */
	protected $codGrupoSubstituicao;
	
	
	/**
	 *
	 * Lista
	 */
	public static function lista () {
		global $em,$system;
		
		$qb 	= $em->createQueryBuilder();
		try {
			$qb->select('cr')
			->from('\Entidades\ZgfinContaReceber','cr')
			->where($qb->expr()->andX(
				$qb->expr()->eq('cr.codOrganizacao'	, ':codOrganizacao')
			))
			->orderBy('cr.codStatus','ASC')
			->addOrderBy('cr.dataEmissao','DESC')
			->setParameter('codOrganizacao', $system->getCodOrganizacao());
			
			$query 		= $qb->getQuery();
			return($query->getResult());
		} catch (\Exception $e) {
			\Zage\App\Erro::halt($e->getMessage());
		}
	}
	
	/**
	 *
	 * Busca
	 */
	public static function busca ($dataIni = null, $dataFim = null, $dataTipo = null,$valorIni = null, $valorFim = null,$aCategoria = array(),$aStatus = array(),$aCentroCusto = array(),$aForma = array(),$aContaDeb = array(),$descricao = null,$cliente = null) {
		global $em,$system;
	
		$qb 	= $em->createQueryBuilder();
	
	
		try {
			$qb->select('cr')
			->from('\Entidades\ZgfinContaReceber','cr')
			->leftJoin('\Entidades\ZgfinPessoa', 'p', \Doctrine\ORM\Query\Expr\Join::WITH, 'cr.codPessoa = p.codigo')
			->where($qb->expr()->andX(
					$qb->expr()->eq('cr.codOrganizacao'	, ':codOrganizacao')
			))
			->orderBy('cr.dataVencimento','ASC')
			->addOrderBy('cr.descricao,cr.parcela,cr.dataEmissao','ASC')
			->setParameter('codOrganizacao', $system->getCodOrganizacao());
			
			if ($dataTipo == "E") {
				$campoData		= "cr.dataEmissao";
			}else{
				$campoData		= "cr.dataVencimento";
			}
			
			if (!empty($valorIni)) {
				$qb->andWhere(
					$qb->expr()->gte("cr.valor", ':valorIni')
				);
				$qb->setParameter('valorIni', $valorIni);
			}
			
			if (!empty($valorFim)) {
				$qb->andWhere(
					$qb->expr()->lte("cr.valor", ':valorFim')
				);
				$qb->setParameter('valorFim', $valorFim);
			}
				
			
			if (!empty($dataIni)) {
				$qb->andWhere(
					$qb->expr()->gte($campoData, ':dataIni')
				);
				$qb->setParameter('dataIni', \DateTime::createFromFormat( $system->config["data"]["dateFormat"], $dataIni ), \Doctrine\DBAL\Types\Type::DATE);
			}
			
			if (!empty($dataFim)) {
				$qb->andWhere(
					$qb->expr()->lte($campoData, ':dataFim')
				);
				$qb->setParameter('dataFim', \DateTime::createFromFormat( $system->config["data"]["dateFormat"], $dataFim ), \Doctrine\DBAL\Types\Type::DATE);
			}
				
			if (!empty($aStatus)) {
				$qb->andWhere(
					$qb->expr()->in('cr.codStatus'	, ':aStatus')
				);
				$qb->setParameter('aStatus', $aStatus);
			}
	
			if (!empty($aCentroCusto)) {
				$qb2 	= $em->createQueryBuilder();
				$qb->andWhere(
					$qb->expr()->exists(
						$qb2->select('cpr1')
							->from('\Entidades\ZgfinContaReceberRateio','cpr1')
							->where($qb2->expr()->andX(
								$qb2->expr()->eq('cpr1.codContaRec'		, 'cr.codigo'),
								$qb2->expr()->in('cpr1.codCentroCusto'	, $aCentroCusto)
							)
						)->getDQL()
					)
				);
			}
	
			if (!empty($aCategoria)) {
				$qb3 	= $em->createQueryBuilder();
				$qb->andWhere(
					$qb->expr()->exists(
						$qb3->select('cpr2')
							->from('\Entidades\ZgfinContaReceberRateio','cpr2')
							->where($qb3->expr()->andX(
								$qb3->expr()->eq('cpr2.codContaRec'		, 'cr.codigo'),
								$qb3->expr()->in('cpr2.codCategoria'		, $aCategoria)
							)
						)->getDQL()
					)
				);
			}
				
			if (!empty($aForma)) {
				$qb->andWhere(
					$qb->expr()->in('cr.codFormaPagamento'	, ':aForma')
				);
				$qb->setParameter('aForma', $aForma);
			}
	
			if (!empty($aContaDeb)) {
				$qb->andWhere(
					$qb->expr()->in('cr.codConta'	, ':aConta')
				);
				$qb->setParameter('aConta', $aContaDeb);
			}
	
			if (!empty($descricao)) {
				$qb->andWhere(
					$qb->expr()->like($qb->expr()->upper('cr.descricao')	, ':descricao')
				);
				$qb->setParameter('descricao', strtoupper('%'.$descricao.'%'));
			}
				
			if (!empty($cliente)) {
				$qb->andWhere(
					$qb->expr()->like($qb->expr()->upper('p.nome')	, ':cliente')
				);
				$qb->setParameter('cliente', strtoupper('%'.$cliente.'%'));
			}
				
			$query 		= $qb->getQuery();
			//echo $query->getSQL();
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

		#################################################################################
		## Validações dos arrays
		#################################################################################
		if (!is_array($this->_valores)) {
			return $tr->trans('"Valores" deve ser um array');
		}
		
		if (!is_array($this->_datas)) {
			return $tr->trans('"Datas" deve ser um array');
		}
		
		if (sizeof($this->_valores) != sizeof($this->_datas)) {
			return $tr->trans('Quantidade de Valores difere da Quantidade de datas');
		}
		
		if (!$this->_getValorTotal()) {
			return $tr->trans('Valor total não informado !!!');
		}
		
		#################################################################################
		## Calcula o número de registros
		#################################################################################
		$n		= sizeof($this->_valores);
		
		#################################################################################
		## Validações dos valores
		#################################################################################
		$valores		= array();
		$outrosValores	= array();
		$_valorTotal	= 0;
		for ($i = 0; $i < $n; $i++) {
			if ($this->_valores[$i] == 0) {
				return $tr->trans('Array de valores tem registro com valor = 0 na posição "'.$i.'"');
			}elseif (!\Zage\App\Util::ehNumero($this->_valores[$i])) {
				return $tr->trans('Array de valores tem registro inválido na posição "'.$i.'" !!!');
			}elseif (\Zage\App\Util::to_float($this->_valores[$i]) < 0) {
				return $tr->trans('Array de valores tem registro negativo na posição "'.$i.'" !!!');
			}else{
				//$log->debug("Valor: ".\Zage\App\Util::to_float($this->_valores[$i]).", Juros: ".\Zage\App\Util::to_float($this->getValorJuros()).", Mora: ".\Zage\App\Util::to_float($this->getValorMora()).", Outros: ".\Zage\App\Util::to_float($this->getValorOutros()).", Desconto: ".\Zage\App\Util::to_float($this->getValorDesconto()));
				$_valorOutros		= (isset($this->_outrosValores)) ? $this->_outrosValores[$i] : $this->getValorOutros(); 
				$_val				= \Zage\App\Util::to_float($this->_valores[$i]) + \Zage\App\Util::to_float($this->getValorJuros()) + \Zage\App\Util::to_float($this->getValorMora()) + \Zage\App\Util::to_float($_valorOutros) - \Zage\App\Util::to_float($this->getValorDesconto()) - \Zage\App\Util::to_float($this->getValorDescontoJuros()) - \Zage\App\Util::to_float($this->getValorDescontoMora());
				$_valorTotal		+= $_val;
				$valores[$i]		= \Zage\App\Util::to_float($this->_valores[$i]); 
				$outrosValores[$i]	= ($_valorOutros) ? \Zage\App\Util::to_float($_valorOutros) : 0;
			}
		}
		
		$_valorTotal				= \Zage\App\Util::to_float(round($_valorTotal,2));
		$valTotalInformado			= \Zage\App\Util::to_float($this->_getValorTotal());
		if ($_valorTotal != $valTotalInformado) {
			$log->debug("Valor informado: ".$valTotalInformado." Valor calculado: ".$_valorTotal);
			return $tr->trans('Valor total difere da soma de valores do array !!!');
		}
		
		#################################################################################
		## Validações das datas
		#################################################################################
		$datas		= array();
		for ($i = 0; $i < $n; $i++) {
			if (!$this->_datas[$i]) {
				return $tr->trans('Array de datas tem registro sem valor na posição "'.$i.'"');
			}elseif (\Zage\App\Util::validaData($this->_datas[$i], $system->config["data"]["dateFormat"]) == false) {
				return $tr->trans('Array de datas tem registro inválido valor na posição "'.$i.'"');
			}else{
				$datas[$i]	= $this->_datas[$i];
			}
		}
				
		#################################################################################
		## Validações de Recorrência
		#################################################################################
		if ($this->getCodTipoRecorrencia()->getCodigo() == "P") {
			
			if (!$this->getCodPeriodoRecorrencia()) {
				return $tr->trans('Período de Recorrência deve ser Diário, Semanal, Mensal ou Anual');
			}
			
			if ( (!$this->getNumParcelas()) || (!\Zage\App\Util::ehNumero($this->getNumParcelas())) || ($this->getNumParcelas() <= 1)) {
				return $tr->trans('Número de parcelas deve ser preenchido com um número maior que 1');
			}elseif ( ($this->getCodPeriodoRecorrencia() == "D") && ($this->getNumParcelas() > 365) ) {
				return $tr->trans('Número de parcelas deve ser menor ou igual a 365 para o tipo de recorrência Diário');
			}elseif ( ($this->getCodPeriodoRecorrencia() == "S") && ($this->getNumParcelas() > 54) ) {
				return $tr->trans('Número de parcelas deve ser menor ou igual a 38 para o tipo de recorrência Semanal');
			}elseif ( ($this->getCodPeriodoRecorrencia() == "M") && ($this->getNumParcelas() > 120) ) {
				return $tr->trans('Número de parcelas deve ser menor ou igual a 120 para o tipo de recorrência Mensal');
			}else if ( ($this->getCodPeriodoRecorrencia() == "A") && ($this->getNumParcelas() > 10) ) {
				return $tr->trans('Número de parcelas deve ser menor ou igual a 10 para o tipo de recorrência Anual');
			}
		
			if (!$this->getParcelaInicial()) {
				return $tr->trans('Parcela Inicial deve ser Preenchido com um número maior que 0');
			}else if ($this->getParcelaInicial() > $this->getNumParcelas()) {
				return $tr->trans('Parcela Inicial deve ser menor que Número de parcelas');
			}
		
			if (!$this->getIntervaloRecorrencia()) {
				return $tr->trans('Intervalo de repetição deve ser preenchido com um número maior que 0');
			}

		}
		
		#################################################################################
		## Validações de identificação 
		#################################################################################
		if (!$this->getDescricao()) {
			return $tr->trans('"Descriçao" deve ser preenchida');
		}elseif (strlen($this->getDescricao()) > 60) {
			return $tr->trans('"Descrição" deve conter no máximo 60 caracteres');
		}elseif (strlen($this->getDescricao()) < 2) {
			return $tr->trans('"Descrição" deve conter no mínimo 2 caracteres');
		}
		
		if (!$this->getDataVencimento()) {
			return $tr->trans('"Data de Vencimento" deve ser preenchida');
		}elseif (\Zage\App\Util::validaData($this->getDataVencimento(), 'd/m/Y') == false) {
			return $tr->trans('"Data de Vencimento" inválida');
		}
		

		#################################################################################
		## Validações dos valores 
		#################################################################################
		if (!$this->getValor()) {
			return $tr->trans('"Valor" deve ser preenchido');
		}elseif ($this->getValor() == 0) {
			return $tr->trans('"Valor" deve ser maior que 0');
		}elseif (!\Zage\App\Util::ehNumero($this->getValor())) {
			return $tr->trans('"Valor" deve ser numérico '.$this->getValor());
		}
		
		if (($this->getValorJuros()) && (!\Zage\App\Util::ehNumero($this->getValorJuros()))) {
			return $tr->trans('"Juros" deve ser numérico');
		}

		if (($this->getValorMora()) && (!\Zage\App\Util::ehNumero($this->getValorMora()))) {
			return $tr->trans('"Mora" deve ser numérico');
		}
		
		if (($this->getValorDesconto()) && (!\Zage\App\Util::ehNumero($this->getValorDesconto()))) {
			return $tr->trans('"Desconto" deve ser numérico');
		}
		
		if (($this->getValorOutros()) && (!\Zage\App\Util::ehNumero($this->getValorOutros()))) {
			return $tr->trans('"Outros Valores" deve ser numérico');
		}
		
		#################################################################################
		## Validações das configurações
		#################################################################################
		if (!$this->getCodFormaPagamento()) {
			return $tr->trans('"Forma de Pagamento" deve ser selecionada');
		}
		
		if (!$this->getCodConta()) {
			return $tr->trans('"Conta de Recebimento" deve ser selecionada');
		}
		
		
		#################################################################################
		## Calcula o número de registros do rateio
		#################################################################################
		$numRateio		= sizeof($this->_valoresRateio);
		$numParcelas	= sizeof($this->_valores);

		#################################################################################
		## Verificar se os arrays de rateios são multidimensionais, ou seja, se o rateio
		## de cada parcela é diferente
		#################################################################################
		if (($numParcelas == $numRateio) && (isset($this->_valoresRateio[0])) && (is_array($this->_valoresRateio[0]))) {
			$numItensRateio				= $numParcelas;
			$numRateio					= sizeof($this->_valoresRateio[0]);
			
		}else{
			$numItensRateio				= $numRateio;
		}
		
		#################################################################################
		## Validações dos valores
		#################################################################################
		for ($i = 0; $i < $numItensRateio; $i++) {
			if (is_array($this->_valoresRateio[$i])) {
				for ($j = 0; $j < sizeof($numRateio); $j++) {
					if ($this->_valoresRateio[$i][$j] == 0) {
						return $tr->trans('Array de valores de rateio tem registro com valor = 0 na posição "'.$j.'"');
					}elseif (\Zage\App\Util::to_float($this->_valoresRateio[$i][$j]) < 0) {
							return $tr->trans('Array de valores de rateio tem registro negativo na posição "'.$i.'" !!!');
					}elseif (!\Zage\App\Util::ehNumero($this->_valoresRateio[$i][$j])) {
						return $tr->trans('Array de valores de rateio tem registro inválido na posição "'.$j.'" !!!');
					}
				}
			}else{
				if ($this->_valoresRateio[$i] == 0) {
					return $tr->trans('Array de valores tem registro com valor = 0 na posição "'.$i.'"');
				}elseif (\Zage\App\Util::to_float($this->_valoresRateio[$i]) < 0) {
					return $tr->trans('Array de valores de rateio tem registro negativo na posição "'.$i.'" !!!');
				}elseif (!\Zage\App\Util::ehNumero($this->_valoresRateio[$i])) {
					return $tr->trans('Array de valores tem registro inválido na posição "'.$i.'" !!!');
				}
			}
		}
		

		#################################################################################
		## Validações dos valores
		#################################################################################
		for ($i = 0; $i < $numItensRateio; $i++) {
			if (is_array($this->_pctRateio[$i])) {
				for ($j = 0; $j < sizeof($numRateio); $j++) {
					$perc		= \Zage\App\Util::to_float(str_replace("%", "", $this->_pctRateio[$i][$j]));
					if ($perc == 0) {
						return $tr->trans('Array de Percentuais tem registro com percentual = 0 na posição "'.$j.'" Percentual: '.$perc);
					}elseif (!\Zage\App\Util::ehNumero($perc)) {
						return $tr->trans('Array de Percentuais tem registro inválido na posição "'.$j.'" !!!');
					}
				}
			}else{
				$perc		= \Zage\App\Util::to_float(str_replace("%", "", $this->_pctRateio[$i]));
				if ($perc == 0) {
					return $tr->trans('Array de Percentuais tem registro com percentual = 0 na posição "'.$i.'" Percentual: '.$perc);
				}elseif (!\Zage\App\Util::ehNumero($perc)) {
					return $tr->trans('Array de Percentuais tem registro inválido na posição "'.$i.'" !!!');
				}
			}
		}
		
		#################################################################################
		## Validações das categorias
		#################################################################################
		for ($i = 0; $i < $numItensRateio; $i++) {
			if (is_array($this->_categoriasRateio[$i])) {
				for ($j = 0; $j < sizeof($numRateio); $j++) {
					if (!empty($this->_categoriasRateio[$i][$j])) {
						$oCat		= $em->getRepository('Entidades\ZgfinCategoria')->findOneBy(array('codigo' => $this->_categoriasRateio[$i][$j]));
						if (!$oCat) {
							return $tr->trans('Array de Categorias tem categoria inexistente  na posição "'.$j.'" !!!');
						}
					}
				}
			}else{
				if (!empty($this->_categoriasRateio[$i])) {
					$oCat		= $em->getRepository('Entidades\ZgfinCategoria')->findOneBy(array('codigo' => $this->_categoriasRateio[$i]));
					if (!$oCat) {
						return $tr->trans('Array de Categorias tem categoria inexistente  na posição "'.$i.'" !!!');
					}
				}
			}
		}
		
		#################################################################################
		## Validações dos Centros de Custo
		#################################################################################
		for ($i = 0; $i < $numItensRateio; $i++) {
			if (is_array($this->_centroCustosRateio[$i])) {
				for ($j = 0; $j < sizeof($numRateio); $j++) {
					if (!empty($this->_centroCustosRateio[$i][$j])) {
						$oCentro		= $em->getRepository('Entidades\ZgfinCentroCusto')->findOneBy(array('codigo' => $this->_centroCustosRateio[$i][$j]));
						if (!$oCentro) {
							return $tr->trans('Array de Centro de Custos tem Centro de Custo inexistente na posição "'.$j.'" !!!');
						}
					}
				}
			}else{
				if (!empty($this->_centroCustosRateio[$i])) {
					$oCentro		= $em->getRepository('Entidades\ZgfinCentroCusto')->findOneBy(array('codigo' => $this->_centroCustosRateio[$i]));
					if (!$oCentro) {
						return $tr->trans('Array de Centro de Custos tem Centro de Custo inexistente na posição "'.$i.'" !!!');
					}
				}
			}
		}
		
		#################################################################################
		## Grupo de Conta e lancamento, se não definido resgatar um valor da sequence
		#################################################################################
		if (!$this->_getCodigo()) {
			if (!$this->getCodGrupoConta()) $this->setCodGrupoConta(\Zage\Adm\Sequencial::proximoValor("ZgfinSeqCodGrupoConta"));
			if (!$this->getCodGrupoLanc()) 	$this->setCodGrupoLanc(\Zage\Adm\Sequencial::proximoValor("ZgfinSeqCodGrupoLanc"));
		}else{
			$oContaInicial		= $em->getRepository('Entidades\ZgfinContaReceber')->findOneBy(array('codOrganizacao' => $system->getCodOrganizacao(),'codigo' => $this->_getCodigo()));
			$this->setCodGrupoConta($oContaInicial->getCodGrupoConta());
			$this->setCodGrupoLanc($oContaInicial->getCodGrupoLanc());
			
			#################################################################################
			## Verificar se a conta já está liquidada, pois não poderá ser alterada
			#################################################################################
			if ($oContaInicial->getCodStatus() == "L") return $tr->trans('Conta não pode ser alterada, status atual não permite');
		}
			
		#################################################################################
		## Ajustes nos campos
		#################################################################################
		$this->setValor(\Zage\App\Util::to_float($this->getValor()));
		$this->setValorJuros(\Zage\App\Util::to_float($this->getValorJuros()));
		$this->setValorMora(\Zage\App\Util::to_float($this->getValorMora()));
		$this->setValorDesconto(\Zage\App\Util::to_float($this->getValorDesconto()));
		$this->setValorOutros(\Zage\App\Util::to_float($this->getValorOutros()));

		#################################################################################
		## Número de Parcelas, se não definido usar o padrão que é "1"
		#################################################################################
		if (!$this->getNumParcelas()) 	$this->setNumParcelas(1);
		
		#################################################################################
		## Verificar se o código foi informado, pois tentaremos alterar a conta,
		## caso contrário, vamos cadastrar.
		## Usaremos o método de loop para cadastrar as parcelas, então se a conta não
		## for parcelada o loop será de 1
		#################################################################################
		if ($this->getCodTipoRecorrencia()->getCodigo() == "U") {
			$parcelaIni		= $this->getParcela();
			$parcelaFim		= $this->getParcela();
		}else {
			if ($this->_getCodigo()) {
				$parcelaIni		= $this->getParcela();
				if ($this->_getIndAlterarSeq() == 1) {
					$parcelaFim		= $this->getNumParcelas();
				}else{
					$parcelaFim		= $this->getParcela();
				}
			}else{
				$parcelaIni		= $this->getParcelaInicial();
				$parcelaFim		= $this->getNumParcelas();
			}
		}
		
		#################################################################################
		## Fazer o loop para cadastrar as parcelas
		#################################################################################
		$i				= 0;
		
		for ($p	= $parcelaIni; $p <= $parcelaFim; $p++) {
			
			#################################################################################
			## Copia os valores de um objeto para o outro
			#################################################################################
			if ($this->_getCodigo() != null) {
				if ($this->_getIndAlterarSeq () == 1) {
					$object			= $em->getRepository('Entidades\ZgfinContaReceber')->findOneBy(array('codOrganizacao' => $system->getCodOrganizacao(),'parcela' => $p, 'codGrupoConta' => $this->getCodGrupoConta(),'codStatus' => array('A')));
				}else{
					$object			= $em->getRepository('Entidades\ZgfinContaReceber')->findOneBy(array('codOrganizacao' => $system->getCodOrganizacao(),'codigo' => $this->_getCodigo()));
				}
				if (!$object)	continue;
			}else{
				$object	= new \Entidades\ZgfinContaReceber();
			}
			
			#################################################################################
			## Ajustar Valor Cancelado
			#################################################################################
			$valorCanc		= $object->getValorCancelado();
			if (empty($valorCanc))	$valorCanc	= 0;
			
			#################################################################################
			## usar padrão do sistema se não especificado
			#################################################################################
			if (!$object->getCodigo()) $object->setNumero(self::geraNumero());
			
			#################################################################################
			## Só informar alguns campos, quando a conta for nova
			#################################################################################
			if (!$this->_getCodigo()) {
				$object->setCodGrupoConta($this->getCodGrupoConta());
				$object->setCodGrupoLanc($this->getCodGrupoLanc());
				$object->setCodStatus($this->getCodStatus());
				$object->setValorCancelado(0);
				$object->setCodTransacao($this->getCodTransacao());
				$object->setCodGrupoAssociacao($this->getCodGrupoAssociacao());
				$object->setValorDescontoJuros(0);
				$object->setValorDescontoMora(0);
				$object->setCodContaPerfil($this->getCodContaPerfil());
			}
				
			$object->setCodOrganizacao($this->getCodOrganizacao());
			$object->setCodFormaPagamento($this->getCodFormaPagamento());
			$object->setCodMoeda($this->getCodMoeda());
			$object->setCodPessoa($this->getCodPessoa());
			$object->setDescricao($this->getDescricao());
			$object->setDocumento($this->getDocumento());
			$object->setNumParcelas($this->getNumParcelas());
			$object->setParcelaInicial($this->getParcelaInicial());
			$object->setObservacao($this->getObservacao());
			$object->setValorDesconto($this->getValorDesconto());
			$object->setValorJuros($this->getValorJuros());
			$object->setValorMora($this->getValorMora());
			//$object->setValorOutros($this->getValorOutros());
			$object->setCodPeriodoRecorrencia($this->getCodPeriodoRecorrencia());
			$object->setIntervaloRecorrencia($this->getIntervaloRecorrencia());
			$object->setCodTipoRecorrencia($this->getCodTipoRecorrencia());
			$object->setCodConta($this->getCodConta());
			$object->setIndReceberAuto($this->getIndReceberAuto());
			
			#################################################################################
			## Data de Autorização e Indicador de Autorizado, se não for definido consultar o parâmetro do sistema
			#################################################################################
			$indAutAuto		=	\Zage\Adm\Parametro::getValor('FIN_AUTORIZA_CONTA_REC_NA_EMISSAO');
			if (!$object->getDataAutorizacao() && $indAutAuto	== 1) $object->setDataAutorizacao(new \DateTime("now"));
				
			if (($object->getIndAutorizado() == false) && $indAutAuto	== 1) {
				$object->setIndAutorizado(1);
			}else{
				$object->setIndAutorizado(0);
			}
		
			#################################################################################
			## Número da Parcela
			#################################################################################
			$object->setParcela($p);
			
			#################################################################################
			## Data de Emissão, é a data de geração da conta
			#################################################################################
			if (!$object->getCodigo()) {
				if (!$object->getDataEmissao()) 	$object->setDataEmissao(new \DateTime("now"));
			}

			#################################################################################
			## Data de Vencimento
			#################################################################################
			if ($this->_getIndAlterarSeq () != 1 || ($parcelaIni == $p)) {
				$object->setDataVencimento(\DateTime::createFromFormat($system->config["data"]["dateFormat"],$datas[$i]));
			}
						
			#################################################################################
			## Valor
			#################################################################################
			//if ($this->getCodTipoRecorrencia()->getCodigo() == "U") {
			//	$object->setValor($this->getValor());
			//}else{
				$object->setValor($valores[$i]);
				$object->setValorOutros($outrosValores[$i]);
			
			//}
			
			$valorTotalParcela		= \Zage\App\Util::to_float($this->_valores[$i]) + \Zage\App\Util::to_float($this->getValorJuros()) + \Zage\App\Util::to_float($this->getValorMora()) + \Zage\App\Util::to_float($outrosValores[$i]) - \Zage\App\Util::to_float($this->getValorDesconto()) - \Zage\App\Util::to_float($this->getValorDescontoJuros()) - \Zage\App\Util::to_float($this->getValorDescontoMora());
			
			#################################################################################
			## Guarda o código do grupo da conta caso a conta esteja sendo substituída
			#################################################################################
			$this->setCodGrupoSubstituicao($object->getCodGrupoConta());
				
			
			try {
				
				$em->persist($object);
				
				if ($p == $parcelaFim) {
					$this->_setCodConta($object->getCodigo());
					$this->setCodStatus($object->getCodStatus());
				}
				
				
				#################################################################################
				## Gravar os rateios
				#################################################################################
				$rateio		= new \Zage\Fin\ContaReceberRateio();
				
				#################################################################################
				## Criar os arrays de rateio
				#################################################################################
				if (is_array($this->_valoresRateio[$i])) {
					$aCodRat		= $this->_codigosRateio[$i];
					$aCadRat		= $this->_categoriasRateio[$i];
					$aCenRat		= $this->_centroCustosRateio[$i];
					$aValRat		= $this->_valoresRateio[$i];
					$aPctRat		= $this->_pctRateio[$i];
				}else{
					$aCodRat		= $this->_codigosRateio;
					$aCadRat		= $this->_categoriasRateio;
					$aCenRat		= $this->_centroCustosRateio;
					$aValRat		= $this->_valoresRateio;
					$aPctRat		= $this->_pctRateio;
				}
				
				#################################################################################
				## Gravar as configurações de Rateio
				#################################################################################
				$rateio->setCodContaRec($object);
				$rateio->_setArrayCodigosRateio($aCodRat);
				$rateio->_setArrayCategoriasRateio($aCadRat);
				$rateio->_setArrayCentroCustoRateio($aCenRat);
				$rateio->_setArrayValoresRateio($aValRat);
				$rateio->_setArrayPctRateio($aPctRat);
				$rateio->_setValorTotal($valorTotalParcela);
				
				$err = $rateio->salva();
				
				if ($err) return $err;
				
				
				#################################################################################
				## Baixar automaticamente se a flag estiver setada, e a data de vencimento for
				## maior ou igual a hoje
				#################################################################################
				if ($this->_getFlagRecebida()) {
					//if ($object->getDataVencimento() <= \DateTime::createFromFormat($system->config["data"]["dateFormat"],date($system->config["data"]["dateFormat"]))) {
						$erro = $this->recebe($object, $object->getCodConta(), $object->getCodFormaPagamento(), $object->getDataVencimento()->format($system->config["data"]["dateFormat"]), \Zage\App\Util::toPHPNumber($object->getValor()), \Zage\App\Util::toPHPNumber($object->getValorJuros()), \Zage\App\Util::toPHPNumber($object->getValorMora()), \Zage\App\Util::toPHPNumber($object->getValorDesconto()), \Zage\App\Util::toPHPNumber($object->getValorOutros()),0,0,$object->getDocumento(),"MAN");
						if ($erro) {
							$log->debug("Erro ao salvar: ".$erro);
							return $erro;
						}
					//}
				}
				
					
			} catch (\Exception $e) {
				return $e->getMessage();
			}
				
			if ($this->_getIndAlterarSeq () != 1) {
				$i++;
			}
			
		}
		
		
		return null;
	}
	
	/**
	 * Cancelar uma conta
	 */
	public function cancela($oConta,$motivo) {
		global $em,$_user,$tr;
	
		#################################################################################
		## Verifica se o perfil / status da conta permite o cancelamento
		#################################################################################
		$codPerfil	= ($oConta->getCodContaPerfil()) ? $oConta->getCodContaPerfil()->getCodigo() : 0;
		if (!\Zage\Fin\ContaAcao::verificaAcaoPermitida($codPerfil, $oConta->getCodStatus()->getCodigo(), "CAN")) {
			return($tr->trans('Conta não pode ser cancelada, status não permitido (%s)',array('%s' => $oConta->getCodStatus()->getCodigo())));
		}
		
		$status 	= $oConta->getCodStatus()->getCodigo();
		if ($status == "A") {

			#################################################################################
			## Calcula o valor a cancelar
			#################################################################################
			$valorCancelar	= self::calculaValorTotal($oConta);

			#################################################################################
			## Resgata o objeto do novo status
			#################################################################################
			$oStatus		= $em->getRepository('Entidades\ZgfinContaStatusTipo')->findOneBy(array('codigo' => 'C'));
				
			#################################################################################
			## Faz o cancelamento total da conta
			#################################################################################
			$oConta->setValorCancelado($valorCancelar);
			$oConta->setDataCancelamento(new \DateTime("now"));
			$oConta->setCodStatus($oStatus);
			
			#################################################################################
			## Gera o histórico de cancelamento
			#################################################################################
			$hist		= new \Entidades\ZgfinContaRecHistCanc();
			$hist->setCodConta($oConta);
			$hist->setCodUsuario($_user);
			$hist->setMotivo($motivo);
			$hist->setValor($valorCancelar);
			$hist->setDataCancelamento(new \DateTime("now"));
				
		}elseif ($status == "P") {
			
			#################################################################################
			## Calcula o valor a cancelar
			#################################################################################
			$valorCancelar	= $this::getSaldoAReceber($oConta->getCodigo());
			
			#################################################################################
			## Remove o valor cancelado da tabela de Rateio
			#################################################################################
			$rateios	= $em->getRepository('Entidades\ZgfinContaReceberRateio')->findBy(array('codContaRec' => $oConta->getCodigo()));
			$numRateios	= sizeof($rateios);
			$valorTotal	= self::calculaValorTotal($oConta);
			$novoValor	= $valorTotal - $valorCancelar;
			$somatorio	= 0;
			
			for ($i = 0; $i < $numRateios; $i++) {
				$valorRateio	= round(($novoValor * $rateios[$i]->getPctValor()/100),2); 
				$somatorio		+= $valorRateio;
				if ($i == ($numRateios - 1)) {
					$diff			= ($novoValor - $somatorio);
					$valorRateio	= $valorRateio + $diff;
				}
				$rateios[$i]->setValor($valorRateio);
				$em->persist($rateios[$i]);
			}
				
			#################################################################################
			## Resgata o objeto do novo status
			#################################################################################
			$oStatus		= $em->getRepository('Entidades\ZgfinContaStatusTipo')->findOneBy(array('codigo' => 'L'));
			
			#################################################################################
			## Faz o cancelamento do Saldo
			#################################################################################
			$oConta->setValorCancelado($valorCancelar);
			$oConta->setDataCancelamento(new \DateTime("now"));
			$oConta->setCodStatus($oStatus);
				
			#################################################################################
			## Gera o histórico de cancelamento
			#################################################################################
			$hist		= new \Entidades\ZgfinContaRecHistCanc();
			$hist->setCodConta($oConta);
			$hist->setCodUsuario($_user);
			$hist->setMotivo($motivo);
			$hist->setValor($valorCancelar);
			$hist->setDataCancelamento(new \DateTime("now"));
		}
		
		try {
			$em->persist($oConta);
			$em->persist($hist);
				
			return null;
		} catch (\Exception $e) {
			return $e->getMessage();
		}
		
	}
	
	/**
	 * Efetivar o recebimento de uma conta
	 * @param \Entidades\ZgfinContaReceber $oConta
	 * @param unknown $codContaDeb
	 * @param unknown $codFormaPag
	 * @param unknown $dataRec
	 * @param unknown $valor
	 * @param unknown $valorJuros
	 * @param unknown $valorMora
	 * @param unknown $valorDesconto
	 * @param unknown $valorOutros
	 * @param unknown $valorDescJuros
	 * @param unknown $valorDescMora
	 * @param unknown $documento
	 * @param unknown $codTipoBaixa
	 * @param string $seqRetorno
	 * @param string $valDescontoBoletoConcedido
	 * @param string $usarAdiantamento
	 * @return string|unknown|NULL
	 * @throws \Exception
	 */
	public function recebe (\Entidades\ZgfinContaReceber $oConta,$codContaDeb,$codFormaPag,$dataRec,$valor,$valorJuros,$valorMora,$valorDesconto,$valorOutros,$valorDescJuros,$valorDescMora,$documento,$codTipoBaixa,$seqRetorno = null,$valDescontoBoletoConcedido = null,$usarAdiantamento = null) {

		#################################################################################
		## Variáveis globais
		#################################################################################
		global $em,$system,$tr,$log;
		
		#################################################################################
		## Resgata o perfil da conta
		#################################################################################
		$codPerfil	= ($oConta->getCodContaPerfil()) ? $oConta->getCodContaPerfil()->getCodigo() : 0;
		
		#################################################################################
		## Verifica se a conta pode receber baixa
		#################################################################################
		if (!\Zage\Fin\ContaAcao::verificaAcaoPermitida($codPerfil, $oConta->getCodStatus()->getCodigo(), "CON")) {
			return($tr->trans('Conta não pode ser confirmada, status não permitido (%s)',array('%s' => $oConta->getCodStatus()->getCodigo())));
		}
		
		#################################################################################
		## Validação da conta
		#################################################################################
		/*if (!isset($codFormaPag) || empty($codFormaPag)) {
			return("Falta de parâmetros (FORMA_PAG)");
		}*/
		
		if (!isset($dataRec) || empty($dataRec)) {
			return("Falta de parâmetros (DATA_REC)");
		}
		
		if ( (!isset($valor) || empty($valor)) && (!isset($valorJuros) || empty($valorJuros)) && (!isset($valorMora) || empty($valorMora)) && (!isset($valorOutros) || empty($valorOutros)) ) {
			return($tr->trans("Pelo menos um dos valores deve ser informado !!"));
		}

		#################################################################################
		## Validação dos valores, não pode receber valores negativos
		#################################################################################
		if ($valor 			< 0)	return($tr->trans("Campo valor não pode ser negativo"));
		if ($valorJuros 	< 0)	return($tr->trans("Campo valor de júros não pode ser negativo"));
		if ($valorMora 		< 0)	return($tr->trans("Campo valor de mora não pode ser negativo"));
		if ($valorOutros 	< 0)	return($tr->trans("Campo Outros valores não pode ser negativo"));
		if ($valorDesconto 	< 0)	return($tr->trans("Campo Desconto não pode ser negativo"));
		if ($valorDescJuros < 0)	return($tr->trans("Campo Desconto de júros não pode ser negativo"));
		if ($valorDescMora 	< 0)	return($tr->trans("Campo Desconto de mora não pode ser negativo"));
		if (($valDescontoBoletoConcedido) && ($valDescontoBoletoConcedido < 0))	return($tr->trans("Campo valDescontoBoletoConcedido não pode ser negativo"));
		
		$valData	= new \Zage\App\Validador\DataBR();
		
		if ($valData->isValid($dataRec) == false) {
			return("Campo DATA DE RECEBIMENTO inválido");
		}
		
		#################################################################################
		## Ajusta os valores para o Formato do Banco
		#################################################################################
		$valor						= \Zage\App\Util::to_float($valor);
		$valorJuros					= \Zage\App\Util::to_float($valorJuros);
		$valorMora					= \Zage\App\Util::to_float($valorMora);
		$valorDesconto				= \Zage\App\Util::to_float($valorDesconto);
		$valorOutros				= \Zage\App\Util::to_float($valorOutros);
		$valorDescJuros				= \Zage\App\Util::to_float($valorDescJuros);
		$valorDescMora				= \Zage\App\Util::to_float($valorDescMora);
		$valDescontoBoletoConcedido	= \Zage\App\Util::to_float($valDescontoBoletoConcedido);

		#################################################################################
		## Resgatar o Códiga da Pessoa
		#################################################################################
		$codPessoa		= ($oConta->getCodPessoa()) ? $oConta->getCodPessoa()->getCodigo() : null; 
		
		#################################################################################
		## Calcular o valor total recebido
		#################################################################################
		$valorTotal			= round(floatval($valor + $valorJuros + $valorMora + $valorOutros - $valorDesconto),2);
		
		#################################################################################
		## Verificar se foi usado o adiantamento para baixar, caso tenha sido
		## verificar se o saldo de adiantamento do cliente é sulficiente para cobrir
		## a baixa
		#################################################################################
		if ($usarAdiantamento == 1) {
			$saldoAd			= \Zage\Fin\Adiantamento::getSaldo($oConta->getCodOrganizacao(), $oConta->getCodPessoa()->getCodigo());
			if ($valorTotal	> $saldoAd)	{
				return($tr->trans('Saldo de adiantamento insuficiente para efetuar a baixa'));
			}
		}
		
		#################################################################################
		## Resgatar os objetos das chaves estrangeiras
		#################################################################################
		$oMoeda		= $em->getRepository('Entidades\ZgfinMoeda')->findOneBy(array('codigo' => 1));
		$oOrg		= $em->getRepository('Entidades\ZgadmOrganizacao')->findOneBy(array('codigo' => $oConta->getCodOrganizacao()->getCodigo()));
		$oOrigem	= $em->getRepository('Entidades\ZgadmOrigem')->findOneBy(array('codigo' => 2));
		$oTipoOper	= $em->getRepository('Entidades\ZgfinOperacaoTipo')->findOneBy(array('codigo' => "C"));
		$oBaixa		= $em->getRepository('Entidades\ZgfinBaixaTipo')->findOneBy(array('codigo' => $codTipoBaixa));
		
		#################################################################################
		## Resgatar o saldo da conta
		#################################################################################
		if ($oConta->getCodigo()) {
			$saldo		= self::getSaldoAReceber($oConta->getCodigo());
			$aplDesc	= true;
		}else{
			$saldo		= $valorTotal;
			$aplDesc	= false;
		}
		
		#################################################################################
		## Verificar se a conta está atrasada e calcular o júros e mora caso existam
		#################################################################################
		if ($this->estaAtrasada($oConta->getCodigo(), $dataRec) == true) {

			#################################################################################
			## Calcula os valor através da data de referência
			#################################################################################
			$_valJuros		= $this->calculaJurosPorAtraso($oConta->getCodigo(), $dataRec);
			$_valMora		= $this->calculaMoraPorAtraso($oConta->getCodigo(), $dataRec);

			#################################################################################
			## Atualiza os valores de juros da conta
			#################################################################################
			$oConta->setValorJuros($oConta->getValorJuros() + $_valJuros);
			$oConta->setValorMora($oConta->getValorMora() + $_valMora);
			$oConta->setValorDescontoJuros($valorDescJuros);
			$oConta->setValorDescontoMora($valorDescMora);
			
			#################################################################################
			## Aplica os descontos
			#################################################################################
			$_valJuros		= ($_valJuros - $valorDescJuros); 
			$_valMora		= ($_valMora - $valorDescMora);
			
			#################################################################################
			## Verifica se o valor ficou negativo
			#################################################################################
			$_valJuros		= ($_valJuros < 0)	? 0 : $_valJuros;
			$_valMora		= ($_valMora < 0)	? 0 : $_valMora;
					
		}else{
			$_valJuros		= 0;
			$_valMora		= 0;
		}
		
		#################################################################################
		## Atualiza o valor de desconto na conta
		#################################################################################
		$oConta->setValorDesconto($oConta->getValorDesconto() + $valorDesconto);
		
		#################################################################################
		## Atualiza o saldo
		#################################################################################
		if ($aplDesc)	$saldo	-= $valorDesconto;
		$saldo 			+= ($_valJuros + $_valMora);
		$saldo			= round(floatval($saldo),2);
		
		//$_total			= self::calculaValorTotal($oConta);
		//$log->info("Conta: ".$oConta->getNumero()." Saldo a receber: ".$saldo." ValorJuros: ".$_valJuros." ValorMora: ".$_valMora." _CalcTotal:".$_total." ValorTotal: ".$valorTotal);

		#################################################################################
		## Grupo de Movimentação
		#################################################################################
		$grupoMov	= \Zage\Adm\Sequencial::proximoValor("ZgfinSeqCodGrupoMov");
		
		#################################################################################
		## Calcular o novo status
		#################################################################################
		if ($valorTotal < $saldo) {
			$codStatus	= "P";
			$dataLiq	= null;
		}else{
			
			$codStatus	= "L";
			$dataLiq	= $dataRec;
			
			#################################################################################
			## Verificar se o valor recebido é maior que o valor devido, caso seja,
			## Colocar o valor a mais na conta de adiantamento do cliente
			#################################################################################
			if (($valorTotal > $saldo) && ($oConta->getCodPessoa())) {
				
				#################################################################################
				## O Valor não deve ir para o histórico de recebimento, então deve ser retirado
				## o valor a maior da origem, ou seja, verificar se veio de júros, mora, ou do 
				## valor principal
				#################################################################################
				$saldoDet			= self::getSaldoAReceberDetalhado($oConta->getCodigo());
				$diferenca			= $valorTotal - $saldo;
				$valPrincipal		= $valor + $valorOutros - $valorDesconto; 

				$difPrincipal		= ($valPrincipal	> $saldoDet["PRINCIPAL"] + $saldoDet["OUTROS"]	)	? ($valPrincipal	- ($saldoDet["PRINCIPAL"] + $saldoDet["OUTROS"])) 	: 0; 
				$difJuros			= ($valorJuros		> $saldoDet["JUROS"]							) 	? ($valorJuros		- $saldoDet["JUROS"]) 								: 0; 
				$difMora			= ($valorMora 		> $saldoDet["MORA"]								) 	? ($valorMora		- $saldoDet["MORA"]) 								: 0;
				
				$difTotal			= ($difPrincipal + $difJuros + $difMora);
				
				if ($diferenca != $difTotal) {
					$valor			= $valor - $diferenca;
					if ($valor < 0)	{
						throw new \Exception("Valor pago a maior !!!, não conseguimos descobrir a origem da diferença");
					}
				}else{
					$valor			= $valor 		- $difPrincipal;
					$valorJuros		= $valorJuros 	- $difJuros;
					$valorMora		= $valorMora 	- $difMora;
				}
				
				#################################################################################
				## Atualiza os novos valores
				#################################################################################
				//$novoValorJuros			= ($oConta->getValorJuros() - $difJuros);
				//$novoValorMora			= ($oConta->getValorMora() - $difMora);
				//$oConta->setValorJuros($oConta->getValorJuros() - $difJuros);
				//$oConta->setValorMora($oConta->getValorMora() - $difMora);
				
				#################################################################################
				## Cria o adiantamento
				#################################################################################
				\Zage\Fin\Adiantamento::salva($oOrg->getCodigo(),$oOrigem->getCodigo(),$oTipoOper->getCodigo(),$codPessoa,$oConta->getCodigo(),null,$dataRec,$diferenca,$grupoMov);
			}
		}
		
		#################################################################################
		## Resgatar o objeto do status
		#################################################################################
		$oStatus		= $em->getRepository('Entidades\ZgfinContaStatusTipo')->findOneBy(array('codigo' => $codStatus));

		if ($codContaDeb) {
			$oContaCre		= $em->getRepository('Entidades\ZgfinConta')->findOneBy(array('codigo' => $codContaDeb));
		}else{
			$oContaCre		= null;
		}
		
		if ($codFormaPag) {
			$oFormaPag		= $em->getRepository('Entidades\ZgfinFormaPagamento')->findOneBy(array('codigo' => $codFormaPag));
		}else{
			$oFormaPag		= null;
		}
		
		if (!$oBaixa)	throw new \Exception('Tipo de baixa "'.$codTipoBaixa.'" não encontrado');
		
		#################################################################################
		## Criar o objeto das datas
		#################################################################################
		if (!empty($dataRec)) {
			$dataRec 		= \DateTime::createFromFormat($system->config["data"]["dateFormat"], $dataRec);
		}else{
			$dataRec		= null;
		}
		
		if (!empty($dataLiq)) {
			$dataLiq 		= \DateTime::createFromFormat($system->config["data"]["dateFormat"], $dataLiq);
		}else{
			$dataLiq		= null;
		}
		
		#################################################################################
		## Criar o objeto do Histórico de Recebimento
		#################################################################################
		$oHist			= new \Entidades\ZgfinHistoricoRec();
		$oHist->setCodContaRec($oConta);
		$oHist->setCodConta($oContaCre);
		$oHist->setCodFormaPagamento($oFormaPag);
		$oHist->setCodGrupoLanc($oConta->getCodGrupoLanc());
		$oHist->setCodMoeda($oMoeda);
		$oHist->setDataRecebimento($dataRec);
		$oHist->setDataTransacao(new \DateTime("now"));
		$oHist->setDocumento($documento);
		$oHist->setValorDesconto($valorDesconto);
		$oHist->setValorJuros($valorJuros + $valorDescJuros);
		$oHist->setValorMora($valorMora + $valorDescMora);
		$oHist->setValorOutros($valorOutros);
		$oHist->setValorRecebido($valor);
		$oHist->setValorDescontoJuros($valorDescJuros);
		$oHist->setValorDescontoMora($valorDescMora);
		$oHist->setCodGrupoMov($grupoMov);
		$oHist->setCodTipoBaixa($oBaixa);
		$oHist->setSeqRetornoBancario($seqRetorno);
		if ($valDescontoBoletoConcedido) {
			$oHist->setValDescontoBoletoConcedido($valDescontoBoletoConcedido);
		}
		
		#################################################################################
		## Atualizar as informações da conta
		#################################################################################
		$oConta->setCodStatus($oStatus);
		$oConta->setDataLiquidacao($dataLiq);

		#################################################################################
		## Gerar a movimentação bancária, apenas se não for por adiantamento
		#################################################################################
		if ($usarAdiantamento !== 1) {
			$oMov	= new \Zage\Fin\MovBancaria();
			$oMov->setCodOrganizacao($oOrg);
			$oMov->setCodConta($oContaCre);
			$oMov->setCodOrigem($oOrigem);
			$oMov->setCodTipoOperacao($oTipoOper);
			$oMov->setDataMovimentacao($dataRec);
			$oMov->setDataOperacao(new \DateTime("now"));
			$oMov->setValor($valorTotal);
			$oMov->setCodGrupoMov($grupoMov);
			
			$err	= $oMov->salva();
			if ($err) return $err;
		}else{
			#################################################################################
			## Cria o adiantamento de débito
			#################################################################################
			\Zage\Fin\Adiantamento::salva($oOrg->getCodigo(),"4","D",$codPessoa,$oConta->getCodigo(),null,$dataRec->format($system->config["data"]["dateFormat"]),$valorTotal,$grupoMov);
		}
		
		try {
			$em->persist($oConta);
			$em->persist($oHist);
		
			return null;
		} catch (\Exception $e) {
			return $e->getMessage();
		}
	}
	
	/**
	 * Calcular o saldo a Receber de uma conta
	 * @param int $codConta
	 */
	public static function getSaldoAReceber($codConta) {
		
		#################################################################################
		## Variáveis globais
		#################################################################################
		global $em;
		
		#################################################################################
		## Resgata as informações da conta
		#################################################################################
		$oConta		= $em->getRepository('Entidades\ZgfinContaReceber')->findOneBy(array('codigo' => $codConta));
		
		if (!$oConta) {
			return (null);
		}
		
		/** calcular valores já recebidos **/
		$histRec		= $em->getRepository('Entidades\ZgfinHistoricoRec')->findBy(array('codContaRec' => $codConta));
		$valorRec		= 0;
			
		for ($i = 0; $i < sizeof($histRec); $i++) {
			$valorRec += floatval($histRec[$i]->getValorRecebido()) + floatval($histRec[$i]->getValorJuros()) + floatval($histRec[$i]->getValorMora()) + floatval($histRec[$i]->getValorOutros()) - (floatval($histRec[$i]->getValorDesconto()) + floatval($histRec[$i]->getValorDescontoJuros()) + floatval($histRec[$i]->getValorDescontoMora()));
		}
		
		$valorTotal		= self::calculaValorTotal($oConta);
		
		return round($valorTotal - $valorRec,2);
		
	}
	

	/**
	 * Calcular o saldo a receber detalhado por valorPrincipal, júros e mora
	 * @param int $codConta
	 */
	public static function getSaldoAReceberDetalhado($codConta) {
	
		#################################################################################
		## Variáveis globais
		#################################################################################
		global $em;
	
		#################################################################################
		## Resgata as informações da conta
		#################################################################################
		$oConta		= $em->getRepository('Entidades\ZgfinContaReceber')->findOneBy(array('codigo' => $codConta));
	
		if (!$oConta) {
			return (null);
		}
	
		#################################################################################
		## Calcular valores já recebidos
		#################################################################################
		$histRec		= $em->getRepository('Entidades\ZgfinHistoricoRec')->findBy(array('codContaRec' => $codConta));
		$valRecPrinc	= 0;
		$valRecJuros	= 0;
		$valRecMora		= 0;
			
		for ($i = 0; $i < sizeof($histRec); $i++) {
			$valRecPrinc	+= floatval($histRec[$i]->getValorRecebido()) - floatval($histRec[$i]->getValorDesconto());
			$valRecJuros	+= floatval($histRec[$i]->getValorJuros()) - floatval($histRec[$i]->getValorDescontoJuros());
			$valRecMora		+= floatval($histRec[$i]->getValorMora()) - floatval($histRec[$i]->getValorDescontoMora());
			$valRecOutros	+= floatval($histRec[$i]->getValorOutros());
		}
	
		#################################################################################
		## Calcular valores totais
		#################################################################################
		$valTotPrinc		= $oConta->getValor() - $oConta->getValorDesconto();
		$valTotJuros		= $oConta->getValorJuros() - $oConta->getValorDescontoJuros();
		$valTotMora			= $oConta->getValorMora() - $oConta->getValorDescontoMora();
		$valTotOutros		= $oConta->getValorOutros();
		
		#################################################################################
		## Calcular o saldo de cada valor
		#################################################################################
		$saldo				= array();
		$saldo["PRINCIPAL"]	= round($valTotPrinc - $valRecPrinc,2); 
		$saldo["JUROS"]		= round($valTotJuros - $valRecJuros,2);
		$saldo["MORA"]		= round($valTotMora - $valRecMora,2);
		$saldo["OUTROS"]	= round($valTotOutros - $valRecOutros,2);
	
		return $saldo;
	
	}
	
	
	/**
	 * Calcular o saldo a Receber de uma conta
	 * @param int $codConta
	 */
	public static function getValorJaRecebido($codConta) {
		global $em,$system;
		
		#################################################################################
		## Resgata as informações da conta
		#################################################################################
		$oConta		= $em->getRepository('Entidades\ZgfinContaReceber')->findOneBy(array('codOrganizacao' => $system->getCodOrganizacao(), 'codigo' => $codConta));
	
		if (!$oConta) {
			return (null);
		}
	
		/** calcular valores já recebidos **/
		$histRec		= $em->getRepository('Entidades\ZgfinHistoricoRec')->findBy(array('codContaRec' => $codConta));
		$valorRec		= 0;
			
		for ($i = 0; $i < sizeof($histRec); $i++) {
			$valorRec += floatval($histRec[$i]->getValorRecebido()) + floatval($histRec[$i]->getValorJuros()) + floatval($histRec[$i]->getValorMora()) + floatval($histRec[$i]->getValorOutros()) - (floatval($histRec[$i]->getValorDesconto()) + floatval($histRec[$i]->getValorDescontoJuros()) + floatval($histRec[$i]->getValorDescontoMora()));
		}
	
		return round($valorRec,2);
	
	}
	
	
	/**
	 * Excluir uma conta
	 */
	public function exclui($codConta) {
		
		#################################################################################
		## Variáveis globais
		#################################################################################
		global $em,$system,$tr;
	
		#################################################################################
		## Verifica se a conta existe
		#################################################################################
		$oConta		= $em->getRepository('Entidades\ZgfinContaReceber')->findOneBy(array('codOrganizacao' => $system->getCodOrganizacao(), 'codigo' => $codConta));
	
		if (!$oConta) {
			return($tr->trans('Conta %s não encontrada !!!',array('%s' => $codConta)));
		}
	
		#################################################################################
		## Resgata o perfil da conta
		#################################################################################
		$codPerfil	= ($oConta->getCodContaPerfil()) ? $oConta->getCodContaPerfil()->getCodigo() : 0;
		
		#################################################################################
		## Verifica se a conta pode ser excluída
		#################################################################################
		if (!\Zage\Fin\ContaAcao::verificaAcaoPermitida($codPerfil, $oConta->getCodStatus()->getCodigo(), "EXC")) {
			return($tr->trans('Conta não pode ser excluída, status não permitido (%s)',array('%s' => $oConta->getCodStatus()->getCodigo())));
		}
	
		#################################################################################
		## Apaga os rateios
		#################################################################################
		$rateios	= $em->getRepository('Entidades\ZgfinContaReceberRateio')->findBy(array('codContaRec' => $codConta));
		
		for ($i = 0; $i < sizeof($rateios); $i++) {
			$em->remove($rateios[$i]);
		}
		
		#################################################################################
		## Apagar os históricos
		#################################################################################
		$hist	= $em->getRepository('Entidades\ZgfinContaReceberHistorico')->findBy(array('codConta' => $codConta));
		
		for ($i = 0; $i < sizeof($hist); $i++) {
			$em->remove($hist[$i]);
		}
		

		#################################################################################
		## Apagar os históricos de geração de boleto
		#################################################################################
		$histBol	= $em->getRepository('Entidades\ZgfinBoletoHistorico')->findBy(array('codConta' => $codConta));
		
		for ($i = 0; $i < sizeof($histBol); $i++) {
			$em->remove($histBol[$i]);
		}
		
		
		try {
			$em->remove($oConta);

			return null;
		} catch (\Exception $e) {
			return $e->getMessage();
		}
	
	}
	
	
	/**
	 *
	 * Lista por Categoria
	 */
	public static function listaPorCategoria ($dataIni = null, $dataFim = null, $dataTipo = null,$valorIni = null, $valorFim = null,$aCategoria = array(),$aStatus = array(),$aCentroCusto = array(),$aForma = array(),$aContaDeb = array(),$descricao = null,$fornecedor = null) {
		
		#################################################################################
		## Variáveis globais
		#################################################################################
		global $em,$system;
	
		$qb 	= $em->createQueryBuilder();
	
	
		try {
			$qb->select('c.descricao as categoria,sum(crr.valor) as valor')
			->from('\Entidades\ZgfinContaReceber','cr')
			->leftJoin('\Entidades\ZgfinPessoa', 'p', \Doctrine\ORM\Query\Expr\Join::WITH, 'cr.codPessoa = p.codigo')
			->leftJoin('\Entidades\ZgfinContaReceberRateio', 'crr', \Doctrine\ORM\Query\Expr\Join::WITH, 'cr.codigo = crr.codContaRec')
			->leftJoin('\Entidades\ZgfinCategoria', 'c', \Doctrine\ORM\Query\Expr\Join::WITH, 'crr.codCategoria = c.codigo')
			->where($qb->expr()->andX(
					$qb->expr()->eq('cr.codOrganizacao'	, ':codOrganizacao'),
					$qb->expr()->isNotNull('crr.codCategoria')
			))
			->addGroupBy('c.descricao')
			->orderBy('cr.dataVencimento','ASC')
			->addOrderBy('cr.descricao,cr.parcela,cr.dataEmissao','ASC')
			->setParameter('codOrganizacao', $system->getCodOrganizacao());
	
			if ($dataTipo == "E") {
				$campoData		= "cr.dataEmissao";
			}else{
				$campoData		= "cr.dataVencimento";
			}
	
			if (!empty($valorIni)) {
				$qb->andWhere(
						$qb->expr()->gte("cr.valor", ':valorIni')
				);
				$qb->setParameter('valorIni', $valorIni);
			}
	
			if (!empty($valorFim)) {
				$qb->andWhere(
						$qb->expr()->lte("cr.valor", ':valorFim')
				);
				$qb->setParameter('valorFim', $valorFim);
			}
	
	
			if (!empty($dataIni)) {
				$qb->andWhere(
						$qb->expr()->gte($campoData, ':dataIni')
				);
				$qb->setParameter('dataIni', \DateTime::createFromFormat( $system->config["data"]["dateFormat"], $dataIni ), \Doctrine\DBAL\Types\Type::DATE);
			}
	
			if (!empty($dataFim)) {
				$qb->andWhere(
						$qb->expr()->lte($campoData, ':dataFim')
				);
				$qb->setParameter('dataFim', \DateTime::createFromFormat( $system->config["data"]["dateFormat"], $dataFim ), \Doctrine\DBAL\Types\Type::DATE);
			}
	
			if (!empty($aStatus)) {
				$qb->andWhere(
						$qb->expr()->in('cr.codStatus'	, ':aStatus')
				);
				$qb->setParameter('aStatus', $aStatus);
			}
	
			if (!empty($aCentroCusto)) {
				$qb2 	= $em->createQueryBuilder();
				$qb->andWhere(
						$qb->expr()->exists(
								$qb2->select('crr1')
								->from('\Entidades\ZgfinContaReceberrRateio','crr1')
								->where($qb2->expr()->andX(
										$qb2->expr()->eq('crr1.codContaRec'		, 'cr.codigo'),
										$qb2->expr()->in('crr1.codCentroCusto'	, $aCentroCusto)
								)
								)->getDQL()
						)
				);
			}
	
			if (!empty($aCategoria)) {
				$qb2 	= $em->createQueryBuilder();
				$qb->andWhere(
						$qb->expr()->exists(
								$qb2->select('cpr2')
								->from('\Entidades\ZgfinContaReceberRateio','crr2')
								->where($qb2->expr()->andX(
										$qb2->expr()->eq('crr2.codContaRec'		, 'cr.codigo'),
										$qb2->expr()->in('crr2.codCategoria'	, $aCategoria)
								)
								)->getDQL()
						)
				);
			}
	
			if (!empty($aForma)) {
				$qb->andWhere(
						$qb->expr()->in('cr.codFormaPagamento'	, ':aForma')
				);
				$qb->setParameter('aForma', $aForma);
			}
	
			if (!empty($aContaDeb)) {
				$qb->andWhere(
						$qb->expr()->in('cr.codConta'	, ':aConta')
				);
				$qb->setParameter('aConta', $aContaDeb);
			}
	
			if (!empty($descricao)) {
				$qb->andWhere(
						$qb->expr()->like($qb->expr()->upper('cr.descricao')	, ':descricao')
				);
				$qb->setParameter('descricao', strtoupper('%'.$descricao.'%'));
			}
	
			if (!empty($fornecedor)) {
				$qb->andWhere(
						$qb->expr()->like($qb->expr()->upper('p.nome')	, ':fornecedor')
				);
				$qb->setParameter('fornecedor', strtoupper('%'.$fornecedor.'%'));
			}
	
			$query 		= $qb->getQuery();
			//echo $query->getSQL();
			return($query->getResult());
		} catch (\Exception $e) {
			\Zage\App\Erro::halt($e->getMessage());
		}
	}
	
	
	/**
	 *
	 * Lista por Centro de Custo
	 */
	public static function listaPorCentroCusto($dataIni = null, $dataFim = null, $dataTipo = null,$valorIni = null, $valorFim = null,$aCategoria = array(),$aStatus = array(),$aCentroCusto = array(),$aForma = array(),$aContaDeb = array(),$descricao = null,$fornecedor = null) {
		#################################################################################
		## Variáveis globais
		#################################################################################
		global $em,$system;
	
		$qb 	= $em->createQueryBuilder();
	
	
		try {
			$qb->select('c.descricao as centroCusto,sum(crr.valor) as valor')
			->from('\Entidades\ZgfinContaReceber','cr')
			->leftJoin('\Entidades\ZgfinPessoa', 'p', \Doctrine\ORM\Query\Expr\Join::WITH, 'cr.codPessoa = p.codigo')
			->leftJoin('\Entidades\ZgfinContaReceberRateio', 'crr', \Doctrine\ORM\Query\Expr\Join::WITH, 'cr.codigo = crr.codContaRec')
			->leftJoin('\Entidades\ZgfinCentroCusto', 'c', \Doctrine\ORM\Query\Expr\Join::WITH, 'crr.codCentroCusto = c.codigo')
			->where($qb->expr()->andX(
				$qb->expr()->eq('cr.codOrganizacao'	, ':codOrganizacao'),
				$qb->expr()->isNotNull('crr.codCentroCusto')
			))
			->addGroupBy('c.descricao')
			->orderBy('cr.dataVencimento','ASC')
			->addOrderBy('cr.descricao,cr.parcela,cr.dataEmissao','ASC')
			->setParameter('codOrganizacao', $system->getCodOrganizacao());
	
			if ($dataTipo == "E") {
				$campoData		= "cr.dataEmissao";
			}else{
				$campoData		= "cr.dataVencimento";
			}
	
			if (!empty($valorIni)) {
				$qb->andWhere(
						$qb->expr()->gte("cr.valor", ':valorIni')
				);
				$qb->setParameter('valorIni', $valorIni);
			}
	
			if (!empty($valorFim)) {
				$qb->andWhere(
						$qb->expr()->lte("cr.valor", ':valorFim')
				);
				$qb->setParameter('valorFim', $valorFim);
			}
	
	
			if (!empty($dataIni)) {
				$qb->andWhere(
						$qb->expr()->gte($campoData, ':dataIni')
				);
				$qb->setParameter('dataIni', \DateTime::createFromFormat( $system->config["data"]["dateFormat"], $dataIni ), \Doctrine\DBAL\Types\Type::DATE);
			}
	
			if (!empty($dataFim)) {
				$qb->andWhere(
						$qb->expr()->lte($campoData, ':dataFim')
				);
				$qb->setParameter('dataFim', \DateTime::createFromFormat( $system->config["data"]["dateFormat"], $dataFim ), \Doctrine\DBAL\Types\Type::DATE);
			}
	
			if (!empty($aStatus)) {
				$qb->andWhere(
						$qb->expr()->in('cr.codStatus'	, ':aStatus')
				);
				$qb->setParameter('aStatus', $aStatus);
			}
	
			if (!empty($aCentroCusto)) {
				$qb2 	= $em->createQueryBuilder();
				$qb->andWhere(
						$qb->expr()->exists(
								$qb2->select('crr1')
								->from('\Entidades\ZgfinContaReceberRateio','crr1')
								->where($qb2->expr()->andX(
										$qb2->expr()->eq('crr1.codContaRec'		, 'cr.codigo'),
										$qb2->expr()->in('crr1.codCentroCusto'	, $aCentroCusto)
								)
								)->getDQL()
						)
				);
			}
	
			if (!empty($aCategoria)) {
				$qb2 	= $em->createQueryBuilder();
				$qb->andWhere(
						$qb->expr()->exists(
								$qb2->select('crr2')
								->from('\Entidades\ZgfinContaReceberRateio','crr2')
								->where($qb2->expr()->andX(
										$qb2->expr()->eq('crr2.codContaRec'		, 'cr.codigo'),
										$qb2->expr()->in('crr2.codCategoria'		, $aCategoria)
								)
								)->getDQL()
						)
				);
			}
	
			if (!empty($aForma)) {
				$qb->andWhere(
						$qb->expr()->in('cr.codFormaPagamento'	, ':aForma')
				);
				$qb->setParameter('aForma', $aForma);
			}
	
			if (!empty($aContaDeb)) {
				$qb->andWhere(
						$qb->expr()->in('cr.codConta'	, ':aConta')
				);
				$qb->setParameter('aConta', $aContaDeb);
			}
	
			if (!empty($descricao)) {
				$qb->andWhere(
						$qb->expr()->like($qb->expr()->upper('cr.descricao')	, ':descricao')
				);
				$qb->setParameter('descricao', strtoupper('%'.$descricao.'%'));
			}
	
			if (!empty($fornecedor)) {
				$qb->andWhere(
						$qb->expr()->like($qb->expr()->upper('p.nome')	, ':fornecedor')
				);
				$qb->setParameter('fornecedor', strtoupper('%'.$fornecedor.'%'));
			}
	
			$query 		= $qb->getQuery();
			//echo $query->getSQL();
			return($query->getResult());
		} catch (\Exception $e) {
			\Zage\App\Erro::halt($e->getMessage());
		}
	}
	
	
	public static function geraNumero() {
		return (date('Ymd').'/'.str_pad(mt_rand(0,999999), 6, "0", STR_PAD_LEFT));
	}

	/**
	 * Gerar o nossoNumero para Emissão do Boleto
	 * @param number $codConta
	 */
	public static function geraNossoNumero($codConta) {
		#################################################################################
		## Variáveis globais
		#################################################################################
		global $em;
		
		
		#################################################################################
		## Resgata as informaçoes da conta 
		#################################################################################
		$oConta		= $em->getRepository('Entidades\ZgfinContaReceber')->findOneBy(array('codigo' => $codConta));
		if (!$oConta) return null;
		$codContaCorrente	= ($oConta->getCodConta()) ? $oConta->getCodConta()->getCodigo() : null;
		
		if (!$codContaCorrente)	throw new \Exception('Não pode gerar o nosso número referente a conta: '.$oConta->getDescricao().", pois a conta não possui conta corrente");

		#################################################################################
		## Resgata o último nossoNúmero da conta corrente
		#################################################################################
		$em->getConnection()->beginTransaction(); // suspend auto-commit
		try {
	
			$oCcorrente	= $em->getRepository('Entidades\ZgfinConta')->findOneBy(array('codigo' => $codContaCorrente),array(),\Doctrine\DBAL\LockMode::PESSIMISTIC_WRITE);
				
			if (!$oCcorrente) {
				throw new \Exception('Não pode gerar o nosso número referente a conta: '.$oConta->getDescricao().", pois a conta corrente da conta não foi encontrada !!!");
			}else{
				$valor		= ((int) $oCcorrente->getUltimoNossoNumero()) + 1;
			}

			$oCcorrente->setUltimoNossoNumero($valor);
			$em->persist($oCcorrente);
			$em->flush();
			$em->getConnection()->commit();
				
			return ($valor);
		} catch (\Exception $e) {
			$em->getConnection()->rollback();
			$em->close();
			throw $e;
		}
	}

	/**
	 * Verifica se a conta está atrasada
	 * @param number $codConta
	 * @param string $dataReferencia
	 */
	public static function estaAtrasada($codConta,$dataReferencia) {
		#################################################################################
		## Variáveis globais
		#################################################################################
		global $em,$system;
		
		#################################################################################
		## Resgata as informaçoes da conta
		#################################################################################
		$oConta		= $em->getRepository('\Entidades\ZgfinContaReceber')->findOneBy(array('codigo' => $codConta));
		if (!$oConta) return null;
		
		#################################################################################
		## Verificar se a conta está atrasada
		#################################################################################
		$vencimento			= $oConta->getDataVencimento()->format($system->config["data"]["dateFormat"]);
		$numDias			= \Zage\Fin\Data::numDiasAtraso($vencimento,$dataReferencia);
		
		//$log->info("estaAtrasada: Conta:".$codConta." DataReferência: ".$dataReferencia.)
		
		if ($numDias > 0) {
			return true;
		}else{
			return false;
		}
		
	}
	
	
	/**
	 * Calcular o júros caso a conta esteja atrasada, senão retorna 0
	 * @param integer $codConta
	 * @param date $dataReferencia
	 */
	public static function calculaJurosPorAtraso($codConta,$dataReferencia) {
		
		#################################################################################
		## Variáveis globais
		#################################################################################
		global $em,$system;
				
		#################################################################################
		## Resgata as informaçoes da conta
		#################################################################################
		$oConta		= $em->getRepository('Entidades\ZgfinContaReceber')->findOneBy(array('codigo' => $codConta));
		if (!$oConta) return null;
		
		#################################################################################
		## Verifica se a conta está atrasada
		#################################################################################
		if (self::estaAtrasada($codConta, $dataReferencia) == false) return 0;
		
		#################################################################################
		## Verificar se a conta tem juros por atraso
		#################################################################################
		$contaRec			= $oConta->getCodConta();
		$calculaJuros		= false;
		if ($contaRec)		{
			if ($contaRec->getValorMora() > 0 || $contaRec->getValorJuros() > 0 || $contaRec->getPctJuros() > 0 || $contaRec->getPctMora() > 0) {
				$calculaJuros	= true;
			}
		}
		
		if ($calculaJuros == false) return 0;
		
		#################################################################################
		## Calcular a data Base, se a conta estiver aberta, usar o vencimento
		## Caso esteja pendente, usar a maior data de recebimento
		#################################################################################
		$vencimento			= $oConta->getDataVencimento();

		if ($oConta->getCodStatus()->getCodigo() == "A") {
			$dataBase		= $vencimento;
		}elseif ($oConta->getCodStatus()->getCodigo() == "P") {
			#################################################################################
			## Buscar a maior data de recebimento
			#################################################################################
			//$log->info("Vou executar o _buscaUltimaDataRecebimento");
			$ultDataRec		= self::_buscaUltimaDataRecebimento($oConta->getCodigo());
		
			if (($ultDataRec) && ($ultDataRec > $vencimento)) {
				$dataBase	= $ultDataRec;
			}else{
				$dataBase	= $vencimento;
			}
		}else{
			#################################################################################
			## Retornar 0, caso o status não seja A ou P
			#################################################################################
			return 0;
		}
		
		
		#################################################################################
		## Calcular o número de dias em atraso
		#################################################################################
		$dataBase			= $dataBase->format($system->config["data"]["dateFormat"]);
		$numDias			= \Zage\Fin\Data::numDiasAtraso($dataBase,$dataReferencia);
		
		#################################################################################
		## Ajustar o número dias para zero caso não esteja vencido
		#################################################################################
		if ($numDias < 0) 	$numDias	= 0;
		
		#################################################################################
		## Calcular o Juros e Mora
		#################################################################################
		if ($numDias > 0) {
			$valJuros			= \Zage\App\Util::to_float($contaRec->getValorJuros());
			$pctJuros			= $contaRec->getPctJuros();
		
			#################################################################################
			## Calcular o valor da conta
			#################################################################################
			if (!self::getValorJaRecebido($oConta->getCodigo())) {
				$valor				= \Zage\App\Util::to_float($oConta->getValor());
				$valorDesconto		= \Zage\App\Util::to_float($oConta->getValorDesconto());
			}else{
				$valor				= \Zage\App\Util::to_float(self::getSaldoAReceber($oConta->getCodigo()));
				$valorDesconto		= 0;
			}
			
			$valorConta		= $valor - $valorDesconto;
				
			#################################################################################
			## Dar Prioridada aos valores, depois aos percentuais
			#################################################################################
			if ($valJuros) {
				$valorJuros	= ($valJuros/30)*$numDias;
			}elseif ($pctJuros) {
				$valorJuros	= (($valorConta * ($pctJuros/100))/30)*$numDias;
			}else{
				$valorJuros	= 0;
			}
		
			return round($valorJuros,2);
		}else{
			return 0;
		}
		
	}
	
	/**
	 * Calcular a mora caso a conta esteja atrasada, senão retorna 0
	 * @param integer $codConta
	 * @param date $dataReferencia
	 */
	public static function calculaMoraPorAtraso($codConta,$dataReferencia) {

		#################################################################################
		## Variáveis globais
		#################################################################################
		global $em,$system;
		
		#################################################################################
		## Resgata as informaçoes da conta
		#################################################################################
		$oConta		= $em->getRepository('Entidades\ZgfinContaReceber')->findOneBy(array('codigo' => $codConta));
		if (!$oConta) return null;
	
		#################################################################################
		## Verifica se a conta está atrasada
		#################################################################################
		if (self::estaAtrasada($codConta, $dataReferencia) == false) return 0;
		
		#################################################################################
		## Verificar se a conta tem juros por atraso
		#################################################################################
		$contaRec			= $oConta->getCodConta();
		$calculaMora		= false;
		if ($contaRec)		{
			if ($contaRec->getValorMora() > 0 || $contaRec->getValorJuros() > 0 || $contaRec->getPctJuros() > 0 || $contaRec->getPctMora() > 0) {
				$calculaMora	= true;
			}
		}
	
		if ($calculaMora == false) return 0;

		#################################################################################
		## Calcular a data Base, se a conta estiver aberta, usar o vencimento
		## Caso esteja pendente, usar a maior data de recebimento
		#################################################################################
		$vencimento			= $oConta->getDataVencimento();

		if ($oConta->getCodStatus()->getCodigo() == "A") {
			$dataBase		= $vencimento;
		}elseif ($oConta->getCodStatus()->getCodigo() == "P") {

			#################################################################################
			## Retornar 0, pois a mora é uma cobrança única e já compôs o saldo devedor
			## no campo valorMora da tabela
			#################################################################################
			return 0;
				
			
			#################################################################################
			## Buscar a maior data de recebimento
			#################################################################################
			$ultDataRec		= self::_buscaUltimaDataRecebimento($oConta->getCodigo());
		
			if (($ultDataRec) && ($ultDataRec > $vencimento)) {
				$dataBase	= $ultDataRec;
			}else{
				$dataBase	= $vencimento;
			}
		}else{
			#################################################################################
			## Retornar 0, caso o status não seja A ou P
			#################################################################################
			return 0;
		}
		
		
		#################################################################################
		## Calcular o número de dias em atraso
		#################################################################################
		$dataBase			= $dataBase->format($system->config["data"]["dateFormat"]);
		$numDias			= \Zage\Fin\Data::numDiasAtraso($dataBase,$dataReferencia);
		
		#################################################################################
		## Ajustar o número dias para zero caso não esteja vencido
		#################################################################################
		if ($numDias < 0) 	$numDias	= 0;
	
		#################################################################################
		## Calcular o Juros e Mora
		#################################################################################
		if ($numDias > 0) {
			$valMora			= \Zage\App\Util::to_float($contaRec->getValorMora());
			$pctMora			= $contaRec->getPctMora();
	
			#################################################################################
			## Calcular o valor da conta
			#################################################################################
			if (!self::getValorJaRecebido($oConta->getCodigo())) {
				$valor				= \Zage\App\Util::to_float($oConta->getValor());
				$valorDesconto		= \Zage\App\Util::to_float($oConta->getValorDesconto());
			}else{
				$valor				= \Zage\App\Util::to_float(self::getSaldoAReceber($oConta->getCodigo()));
				$valorDesconto		= 0;
			}
				
			$valorConta		= $valor - $valorDesconto;
				
			#################################################################################
			## Dar Prioridada aos valores, depois aos percentuais
			#################################################################################
			if ($valMora)	{
				$valorMora	= $valMora;
			}elseif ($pctMora) {
				$valorMora	= ($valorConta * ($pctMora/100));
			}else{
				$valorMora	= 0;
			}
				
			return round($valorMora,2);
		}else{
			return 0;
		}
	
	}
	
	/**
	 * Busca a conta através do Nosso Número
	 * @param number $codContaCorrente
	 * @param number $nossoNumero
	 */
	public static function buscaPorNossoNumero ($codContaCorrente,$nossoNumero) {
		
		#################################################################################
		## Variáveis globais
		#################################################################################
		global $em;
	
		$qb 	= $em->createQueryBuilder();
	
		try {
			$qb->select('r')
			->from('\Entidades\ZgfinContaReceber','r')
			->leftJoin('\Entidades\ZgfinConta', 'c', \Doctrine\ORM\Query\Expr\Join::WITH, 'r.codConta = c.codigo')
			->where($qb->expr()->andX(
				$qb->expr()->eq('c.codigo'			, ':codContaCorrente'),
				$qb->expr()->eq('r.nossoNumero'		, ':nossoNumero')
			))
			->setParameter('codContaCorrente'	, $codContaCorrente)
			->setParameter('nossoNumero'		, $nossoNumero);
			$query 		= $qb->getQuery();
			return($query->getOneOrNullResult());
		} catch (\Exception $e) {
			\Zage\App\Erro::halt($e->getMessage());
		}
	}
	
	
	/**
	 * Substitui contas
	 */
	public function substitui () {
		
		#################################################################################
		## Variáveis globais
		#################################################################################
		global $em,$system,$tr;
	
		#################################################################################
		## Validações de campos
		#################################################################################
		if (!is_array($this->_getCodigo()))	return $tr->trans('COD_CONTA deve ser um array');
	
		#################################################################################
		## Salva o array de contas a serem substituídas
		#################################################################################
		$_contas	= $this->_getCodigo();
	
		#################################################################################
		## Gera a nova conta
		#################################################################################
		$this->_setCodConta(null);
		$err	= $this->salva();
		if ($err)	return $err;
	
		#################################################################################
		## Verifica se o código de grupo de substituição foi gerado
		#################################################################################
		if (!$this->getCodGrupoSubstituicao())	return $tr->trans('Código de grupo de substituição não gerado !!!');
	
		#################################################################################
		## Seleciona as contas que serão substituídas
		#################################################################################
		$contas		= $em->getRepository('Entidades\ZgfinContaReceber')->findBy(array('codOrganizacao' => $system->getcodOrganizacao(), 'codigo' => $_contas));
	
		#################################################################################
		## Faz o loop nas contas para substituílas
		#################################################################################
		for ($i = 0; $i < sizeof($contas); $i++) {
			$err = $this->_substitui($contas[$i], $this->getCodGrupoSubstituicao());
			if ($err) return $err;
		}
	
	
		return null;
	}
	
	
	
	/**
	 * Faz a substituição de uma conta
	 */
	private function _substitui($oConta,$codGrupoSubstituicao) {
		
		#################################################################################
		## Variáveis globais
		#################################################################################
		global $em,$_user,$tr;

		
		#################################################################################
		## Verifica se o perfil / status da conta permite o cancelamento
		#################################################################################
		$codPerfil	= ($oConta->getCodContaPerfil()) ? $oConta->getCodContaPerfil()->getCodigo() : 0;
		if (!\Zage\Fin\ContaAcao::verificaAcaoPermitida($codPerfil, $oConta->getCodStatus()->getCodigo(), "SUB")) {
			return($tr->trans('Conta não pode ser substituída, status não permitido (%s)',array('%s' => $oConta->getCodStatus()->getCodigo())));
		}
		
		$status 	= $oConta->getCodStatus()->getCodigo();
		if ($status == "A") {
	
			#################################################################################
			## Calcula o valor a substituir
			#################################################################################
			$valorSub	= self::calculaValorTotal($oConta);
	
			#################################################################################
			## Resgata o objeto do novo status
			#################################################################################
			$oStatus		= $em->getRepository('Entidades\ZgfinContaStatusTipo')->findOneBy(array('codigo' => 'S'));
	
			#################################################################################
			## Faz a substituição total da conta
			#################################################################################
			$oConta->setValorCancelado($valorSub);
			$oConta->setDataSubstituicao(new \DateTime("now"));
			$oConta->setCodStatus($oStatus);
			$oConta->setCodGrupoSubstituicao($codGrupoSubstituicao);
	
			#################################################################################
			## Gera o histórico de substituição
			#################################################################################
			$hist		= new \Entidades\ZgfinContaRecHistSub();
			$hist->setCodConta($oConta);
			$hist->setCodUsuario($_user);
			$hist->setValor($valorSub);
			$hist->setData(new \DateTime("now"));
	
		}elseif ($status == "P") {
	
			#################################################################################
			## Calcula o valor a substituir
			#################################################################################
			$valorSub	= $this::getSaldoAReceber($oConta->getCodigo());
	
			#################################################################################
			## Remove o valor substituido da tabela de Rateio
			#################################################################################
			$rateios	= $em->getRepository('Entidades\ZgfinContaReceberRateio')->findBy(array('codContaRec' => $oConta->getCodigo()));
			$numRateios	= sizeof($rateios);
			$valorTotal	= self::calculaValorTotal($oConta);
			$novoValor	= $valorTotal - $valorSub;
			$somatorio	= 0;
	
			for ($i = 0; $i < $numRateios; $i++) {
				$valorRateio	= round(($novoValor * $rateios[$i]->getPctValor()/100),2);
				$somatorio		+= $valorRateio;
				if ($i == ($numRateios - 1)) {
					$diff			= ($novoValor - $somatorio);
					$valorRateio	= $valorRateio + $diff;
				}
				$rateios[$i]->setValor($valorRateio);
				$em->persist($rateios[$i]);
			}
	
			#################################################################################
			## Resgata o objeto do novo status
			#################################################################################
			$oStatus		= $em->getRepository('Entidades\ZgfinContaStatusTipo')->findOneBy(array('codigo' => 'L'));
	
			#################################################################################
			## Faz a substituição do Saldo
			#################################################################################
			$oConta->setValorCancelado($valorSub);
			$oConta->setDataSubstituicao(new \DateTime("now"));
			$oConta->setCodStatus($oStatus);
			$oConta->setCodGrupoSubstituicao($codGrupoSubstituicao);
	
			#################################################################################
			## Gera o histórico de substituição
			#################################################################################
			$hist		= new \Entidades\ZgfinContaRecHistSub();
			$hist->setCodConta($oConta);
			$hist->setCodUsuario($_user);
			$hist->setValor($valorSub);
			$hist->setData(new \DateTime("now"));
	
		}
	
		try {
			$em->persist($oConta);
			$em->persist($hist);
	
			return null;
		} catch (\Exception $e) {
			return $e->getMessage();
		}
	
	}
	
	/**
	 * Calcular o valor total da conta
	 * @param \Entidades\ZgfinContaReceber $oConta
	 */
	public static function calculaValorTotal(\Entidades\ZgfinContaReceber $oConta) {
		
		#################################################################################
		## Calcula o valor do Júros
		#################################################################################
		$valJuros		= $oConta->getValorJuros() - $oConta->getValorDescontoJuros();
		$valJuros		= ($valJuros < 0) ? 0 : $valJuros;

		#################################################################################
		## Calcula o valor da Mora
		#################################################################################
		$valMora		= $oConta->getValorMora() - $oConta->getValorDescontoMora();
		$valMora		= ($valMora < 0) ? 0 : $valMora;
		
		#################################################################################
		## Calcula o valor total da conta
		#################################################################################
		$valorTotal			= \Zage\App\Util::to_float($oConta->getValor() + $valJuros + $valMora + $oConta->getValorOutros() - ($oConta->getValorCancelado() + $oConta->getValorDesconto()));
		
		return $valorTotal;
	}
	

	/**
	 * Buscar a maior (última) data de recebimento de uma conta
	 * @param number $codConta
	 */
	public static function _buscaUltimaDataRecebimento ($codConta) {
	
		#################################################################################
		## Variáveis globais
		#################################################################################
		global $em;
	
		$qb 	= $em->createQueryBuilder();
	
		try {
			$qb->select('h')
			->from('\Entidades\ZgfinHistoricoRec','h')
			->where($qb->expr()->andX(
				$qb->expr()->eq('h.codContaRec'		, ':codConta')
			))
			->orderBy('h.dataRecebimento','DESC')
			->setParameter('codConta'		, $codConta);
			$query 		= $qb->getQuery();
			$return		= $query->getResult();

			if (($return) && ($return[0]->getDataRecebimento())) {
				return ($return[0]->getDataRecebimento());
			}else{
				return null;
			}
			
			
		} catch (\Exception $e) {
			\Zage\App\Erro::halt($e->getMessage());
		}
	}
	
	/**
	 * Verificar se a conta pode emitir boleto
	 * @param unknown $oConta
	 */
	public static function podeEmitirBoleto($oConta) {
		#################################################################################
		## Variáveis globais
		#################################################################################
		//global $log;
		
		#################################################################################
		## Verificar se a conta está configurada para emitir boleto
		## Fazer isso verificando se a carteira da conta está preenchida e o status
		## da conta seja aberta ou pendente
		#################################################################################
		try {
			$contaRec	= $oConta->getCodConta();
			$formaPag	= ($oConta->getCodFormaPagamento()) ? $oConta->getCodFormaPagamento()->getCodigo() : null;
			$status		= ($oConta->getCodStatus()) ? $oConta->getCodStatus()->getCodigo() : null;
			
			//$log->info("ContaRec: ".$contaRec->getCodigo()." FormaPag: ".$formaPag." Status: $status");
			
			if ( ($contaRec) && ($formaPag == 'BOL') ) {
				if (($contaRec->getCodTipo()->getCodigo() == 'CC') && ($contaRec->getCodCarteira() != null) && (($status == "A") || ($status == "P"))  ) {
					//$log->info("Conta: ".$oConta->getCodigo()." pode emitir boleto");
					$pode		= true;
				}else{
					$pode		= false;
				}
			}else{
				$pode		= false;
			}
		} catch (\Exception $e) {
			$pode		= false;
		}
		
		return $pode;
	}
	
	public function _setCodConta($codigo) {
		$this->_codigo	= $codigo;
	}
	
	public function _getCodigo() {
		return ($this->_codigo);
	}
	
	public function _setArrayValores($array) {
		$this->_valores	= $array;
	}
	
	public function _setArrayOutrosValores($array) {
		$this->_outrosValores	= $array;
	}
	
	public function _setArrayDatas($array) {
		$this->_datas	= $array;
	}
	
	public function _setValorTotal($valorTotal) {
		$this->_valorTotal	= $valorTotal;
	}
	
	public function _getValorTotal() {
		return ($this->_valorTotal);
	}
	
	public function _setFlagRecebida($FlagRecebida) {
		$this->_FlagRecebida	= $FlagRecebida;
	}
	
	public function _getFlagRecebida() {
		return($this->_FlagRecebida);
	}

	public function _setIndValorParcela($indValorParcela) {
		$this->_indValorParcela	= $indValorParcela;
	}
	
	public function _getIndValorParcela() {
		return($this->_indValorParcela);
	}
	
	public function _setIndAlterarSeq($indAlterarSeq) {
		$this->_indAlterarSeq	= $indAlterarSeq;
	}
	
	public function _getIndAlterarSeq() {
		return($this->_indAlterarSeq);
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
	
	/**
	 *
	 * @return the int
	 */
	public function getCodGrupoSubstituicao() {
		return $this->codGrupoSubstituicao;
	}
	
	/**
	 *
	 * @param int $codGrupoSubstituicao        	
	 */
	public function setCodGrupoSubstituicao($codGrupoSubstituicao) {
		$this->codGrupoSubstituicao = $codGrupoSubstituicao;
	}
	
	/**
	 *
	 * Listar contas de mensalidade de um formando
	 */
	public static function listaMensalidadeFormando ($cpf) {
		global $em,$system;
	
		$qb 	= $em->createQueryBuilder();
	
		try {
			$qb->select('cr')
			->from('\Entidades\ZgfinContaReceber','cr')
			->leftJoin('\Entidades\ZgfinContaReceberRateio', 'r', \Doctrine\ORM\Query\Expr\Join::WITH, 'cr.codigo = r.codContaRec')
			->leftJoin('\Entidades\ZgfinPessoa', 'p', \Doctrine\ORM\Query\Expr\Join::WITH, 'cr.codPessoa = p.codigo')
			->where($qb->expr()->andX(
					$qb->expr()->eq('cr.codOrganizacao'	, ':codOrganizacao'),
					$qb->expr()->eq('p.cgc'				, ':cpf'),
					$qb->expr()->eq('r.codCategoria'	, ':codCategoria')
			))
	
			->orderBy('cr.dataVencimento','ASC')
			->addOrderBy('cr.descricao,cr.parcela,cr.dataEmissao','ASC')
			->setParameter('codCategoria', '1')
			->setParameter('cpf', $cpf)
			->setParameter('codOrganizacao', $system->getCodOrganizacao());
	
			$query 		= $qb->getQuery();
	
			return($query->getResult());
	
		} catch (\Exception $e) {
			\Zage\App\Erro::halt($e->getMessage());
		}
	}
	
	
	/**
	 * Excluir uma baixa, retornar ao estado anterior da baixa
	 * @param \Entidades\ZgfinContaReceber $oConta
	 * @param \Entidades\ZgfinHistoricoRec $oHist
	 * @throws \Exception
	 */
	public function excluiBaixa (\Entidades\ZgfinContaReceber $oConta,\Entidades\ZgfinHistoricoRec $oHist) {
	
		#################################################################################
		## Variáveis globais
		#################################################################################
		global $em,$tr,$log,$system;
		
		#################################################################################
		## Valida se os parâmetros são objetos
		#################################################################################
		if (!is_object($oConta))	throw new \Exception("Parâmetro 1 passado a função: ".__FUNCTION__." é inconsistente");
		if (!is_object($oHist))		throw new \Exception("Parâmetro 2 passado a função: ".__FUNCTION__." é inconsistente");
		
		#################################################################################
		## Verifica se o perfil / status da conta permite a exclusão
		#################################################################################
		$codPerfil	= ($oConta->getCodContaPerfil()) ? $oConta->getCodContaPerfil()->getCodigo() : 0;
		if (!\Zage\Fin\ContaAcao::verificaAcaoPermitida($codPerfil, $oConta->getCodStatus()->getCodigo(), "EXB")) {
			throw new \Exception($tr->trans("Recebimento não pode ser excluído"));
		}
		
		#################################################################################
		## Resgata o grupo de movimentação
		#################################################################################
		$grupoMov			= $oHist->getCodGrupoMov();

		#################################################################################
		## Controlar se será necessário salvar
		#################################################################################
		$_indSalvar			= false;
		
		#################################################################################
		## Calcula o saldo de adiantamento dessa pessoa, para saber se a baixa pode ser 
		## Excluída
		#################################################################################
		$saldoAdiant	= \Zage\Fin\Adiantamento::getSaldo($oConta->getCodOrganizacao()->getCodigo(),$oConta->getCodPessoa()->getCodigo());
		
		#################################################################################
		## Verifica se foi cadastrado adiantamento para essa baixa
		#################################################################################
		$aAdiant	=  $em->getRepository('Entidades\ZgfinMovAdiantamento')->findBy(array('codContaRec' => $oHist->getCodContaRec()->getCodigo(), 'codGrupoMov' => $grupoMov,'codTipoOperacao' => "C"));
		
		#################################################################################
		## Soma os valores de adiantamentos pra saber se poderá excluir
		#################################################################################
		$valAdiantaExc	= 0;
		for ($i = 0; $i < sizeof($aAdiant); $i++) {
			$valAdiantaExc	+= \Zage\App\Util::to_float($aAdiant[$i]->getValor());
		}
		
		if (($valAdiantaExc > 0) && ($saldoAdiant < $valAdiantaExc)) {
			throw new \Exception("Baixa com adiantamento já utilizado !!!");
		}
			
		#################################################################################
		## Verificar se houve cancelamento de saldo, para não deixar remover a baixa,
		## Antes da exclusão do cancelamento
		#################################################################################
		$aCanc		=  $em->getRepository('Entidades\ZgfinContaRecHistCanc')->findBy(array('codConta' => $oHist->getCodContaRec()->getCodigo()));
		if ($aCanc)	throw new \Exception("Houve cancelamento de saldo na conta, para excluir a baixa, primeiro desfaça o cancelamento !!!");
		
		#################################################################################
		## Excluir os adiatamentos
		#################################################################################
		$aAdiant	=  $em->getRepository('Entidades\ZgfinMovAdiantamento')->findBy(array('codContaRec' => $oHist->getCodContaRec()->getCodigo(), 'codGrupoMov' => $grupoMov));
		for ($i = 0; $i < sizeof($aAdiant); $i++) {
			$em->remove($aAdiant[$i]);
		}

		#################################################################################
		## Verificar se a baixa que foi excluída agregou júros, mora e desconto a conta
		#################################################################################
		$valJurosBaixa		= round(floatval($oHist->getValorJuros())			,2); 
		$valMoraBaixa		= round(floatval($oHist->getValorMora()) 			,2);
		$valJurosConta		= round(floatval($oConta->getValorJuros()) 			,2);
		$valMoraConta		= round(floatval($oConta->getValorMora()) 			,2);
		$valDescJurosBaixa	= round(floatval($oHist->getValorDescontoJuros())	,2);
		$valDescMoraBaixa	= round(floatval($oHist->getValorDescontoJuros())	,2);
		$valDescJurosConta	= round(floatval($oConta->getValorDescontoJuros())	,2);
		$valDescMoraConta	= round(floatval($oConta->getValorDescontoJuros())	,2);
		$valDescontoBaixa	= round(floatval($oHist->getValorDesconto())		,2);
		$valDescontoConta	= round(floatval($oConta->getValorDesconto())		,2);
		
		if (($valJurosBaixa > 0) && ($valJurosBaixa <= $valJurosConta) ) {
			$oConta->setValorJuros($valJurosConta - $valJurosBaixa);
			$_indSalvar			= true;
		}
		if ($valDescJurosBaixa <= $valDescJurosConta) {
			$oConta->setValorDescontoJuros($valDescJurosConta - $valDescJurosBaixa);
			$_indSalvar			= true;
		}
		
		if (($valMoraBaixa > 0) && ($valMoraBaixa <= $valMoraConta) ) {
			$oConta->setValorMora($valMoraConta - $valMoraBaixa);
			$_indSalvar			= true;
		}
		if ($valDescMoraBaixa <= $valDescMoraConta) {
			$oConta->setValorDescontoMora($valDescMoraConta - $valDescMoraBaixa);
			$_indSalvar			= true;
		}
		
		if (($valDescontoBaixa > 0) && ($valDescontoBaixa >= $valDescontoConta) ) {
			$oConta->setValorDesconto($valDescontoConta - $valDescontoBaixa);
			$_indSalvar			= true;
		}
		
		#################################################################################
		## Cadastrar o Histórico 
		#################################################################################
		$codTipoHist	= $em->getRepository('Entidades\ZgfinContaHistoricoTipo')->findOneBy(array('codigo' => 'EXB'));
		$oUsu			= $em->getRepository('Entidades\ZgsegUsuario')->findOneBy(array('codigo' => $system->getCodUsuario()));
		$aHist			= new \Entidades\ZgfinContaReceberHistorico();
		$aHist->setCodConta($oConta);
		$aHist->setCodTipoHist($codTipoHist);
		$aHist->setCodUsuario($oUsu);
		$aHist->setData(new \DateTime());
		$aHist->setHistorico("Exclusão de baixa, código: ".$oHist->getCodigo());
		$em->persist($aHist);


		#################################################################################
		## Verificar se abaixa concedeu desconto de boleto, se houve, incluir novamente
		## o valor do boleto no valorOutros da conta, cadastrar novamente o rateio do boleto,
		## e recalcular os valores de rateio da conta
		#################################################################################
		if ($oHist->getValDescontoBoletoConcedido() > 0) {
			$valDescBolCon	= \Zage\App\Util::to_float($oHist->getValDescontoBoletoConcedido());
			$oConta->setValorOutros(\Zage\App\Util::to_float($oConta->getValorOutros()) + $valDescBolCon);
			$erro	= \Zage\Fmt\Financeiro::criaRateioBoleto($oConta->getCodigo(), $valDescBolCon);
			if ($erro) return $erro;
			$erro	= \Zage\Fin\ContaReceberRateio::recalculaPctRateio($oConta->getCodigo());
			if ($erro) return $erro;
		}
		
		#################################################################################
		## Excluir a Baixa 
		#################################################################################
		$aBaixa	=  $em->getRepository('Entidades\ZgfinHistoricoRec')->findBy(array('codigo' => $oHist->getCodigo()));		
		for ($i = 0; $i < sizeof($aBaixa); $i++) {
			$em->remove($aBaixa[$i]);
		}

		#################################################################################
		## Excluir a movimentação bancária
		#################################################################################
		$aMov	=  $em->getRepository('Entidades\ZgfinMovBancaria')->findBy(array('codOrganizacao' => $oConta->getCodOrganizacao()->getCodigo(),'codGrupoMov' => $oHist->getCodGrupoMov()));
		for ($i = 0; $i < sizeof($aMov); $i++) {
			$em->remove($aMov[$i]);
		}
		
		#################################################################################
		## Verificar se precisa salvar alguma alteração
		#################################################################################
		if ($_indSalvar		== true) {
			$em->persist($oConta);
		}
		
		return null;
	}
	
	/**
	 * Calcular o status da conta e retornar
	 * @param \Zage\Fin\ContaReceber $oConta
	 * @return string $codStatus
	 */
	public static function recalculaStatus (\Entidades\ZgfinContaReceber $oConta) {
		
		#################################################################################
		## Variáveis globais
		#################################################################################
		global $em,$log;
		
		#################################################################################
		## Iremos tentar detectar qual o status que a conta deve ter a partir das tabelas
		## de movimentação das ações, levando em conta as datas de acontecimento das ações
		#################################################################################
		
		#################################################################################
		## Verificar se houve Cancelamento,Caso haja alguma baixa o status deve ser 
		## Liquidada, caso contrário cancelar a conta
		#################################################################################
		$oCanc	=  $em->getRepository('Entidades\ZgfinContaRecHistCanc')->findOneBy(array('codConta' => $oConta->getCodigo()));
		
		#################################################################################
		## Verificar se Existem baixas
		#################################################################################
		$aBaixa	=  $em->getRepository('Entidades\ZgfinHistoricoRec')->findBy(array('codContaRec' => $oConta->getCodigo()));

		#################################################################################
		## Verificar se Existem Substituições
		#################################################################################
		$oSub	=  $em->getRepository('Entidades\ZgfinContaRecHistSub')->findBy(array('codConta' => $oConta->getCodigo()));
			
		#################################################################################
		## Calculo do novo status
		#################################################################################
		if ($oSub) {
			$codStatus			= "S";
		}elseif ($oCanc && $aBaixa)	{
			$codStatus			= "L";
		}elseif ($oCanc) {
			$codStatus			= "C";
		}elseif ($aBaixa) {
			$codStatus			= "P";
		}else{
			$codStatus			= "A";
		}
		
		return $codStatus;
		
	}
	
}