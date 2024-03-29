<?php

namespace Zage\Fin;

/**
 * Gerenciar as Contas a Pagar
 * 
 * @package: ContaPagar
 * @Author: Daniel Henrique Cassela
 * @version: 1.0.1
 * 
 */

class ContaPagar extends \Entidades\ZgfinContaPagar {

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
	 * _flagPaga
	 * @var int
	 */
	private $_flagPaga;
	
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
	 * Objeto que vai será salvo
	 */
	private $_object;
	
	
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
			$qb->select('cp')
			->from('\Entidades\ZgfinContaPagar','cp')
			->where($qb->expr()->andX(
				$qb->expr()->eq('cp.codOrganizacao'	, ':codOrganizacao')
			))
			->orderBy('cp.codStatus','ASC')
			->addOrderBy('cp.dataEmissao','DESC')
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
	public static function busca ($dataIni = null, $dataFim = null, $dataTipo = null,$valorIni = null, $valorFim = null,$aCategoria = array(),$aStatus = array(),$aCentroCusto = array(),$aForma = array(),$aContaDeb = array(),$descricao = null,$fornecedor = null) {
		global $em,$system,$log;
	
		$qb 	= $em->createQueryBuilder();
	
	
		try {
			$qb->select('cp')
			->from('\Entidades\ZgfinContaPagar','cp')
			->leftJoin('\Entidades\ZgfinPessoa', 'p', \Doctrine\ORM\Query\Expr\Join::WITH, 'cp.codPessoa = p.codigo')
			->where($qb->expr()->andX(
				$qb->expr()->eq('cp.codOrganizacao'	, ':codOrganizacao')
			))
			->orderBy('cp.dataVencimento','ASC')
			->addOrderBy('cp.descricao,cp.parcela,cp.dataEmissao','ASC')
			->setParameter('codOrganizacao', $system->getCodOrganizacao());
			
			if ($dataTipo == "E") {
				$campoData		= "cp.dataEmissao";
			}else{
				$campoData		= "cp.dataVencimento";
			}
			
			if (!empty($valorIni)) {
				$qb->andWhere(
					$qb->expr()->gte("cp.valor", ':valorIni')
				);
				$qb->setParameter('valorIni', $valorIni);
			}
			
			if (!empty($valorFim)) {
				$qb->andWhere(
					$qb->expr()->lte("cp.valor", ':valorFim')
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
					$qb->expr()->in('cp.codStatus'	, ':aStatus')
				);
				$qb->setParameter('aStatus', $aStatus);
			}
	
			if (!empty($aCentroCusto)) {
				$qb2 	= $em->createQueryBuilder();
				$qb->andWhere(
					$qb->expr()->exists(
						$qb2->select('cpr1')
							->from('\Entidades\ZgfinContaPagarRateio','cpr1')
							->where($qb2->expr()->andX(
								$qb2->expr()->eq('cpr1.codContaPag'		, 'cp.codigo'),
								$qb2->expr()->in('cpr1.codCentroCusto'	, $aCentroCusto)
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
							->from('\Entidades\ZgfinContaPagarRateio','cpr2')
							->where($qb2->expr()->andX(
								$qb2->expr()->eq('cpr2.codContaPag'		, 'cp.codigo'),
								$qb2->expr()->in('cpr2.codCategoria'		, $aCategoria)
							)
						)->getDQL()
					)
				);
			}
				
			if (!empty($aForma)) {
				$qb->andWhere(
					$qb->expr()->in('cp.codFormaPagamento'	, ':aForma')
				);
				$qb->setParameter('aForma', $aForma);
			}
	
			if (!empty($aContaDeb)) {
				$qb->andWhere(
					$qb->expr()->in('cp.codConta'	, ':aConta')
				);
				$qb->setParameter('aConta', $aContaDeb);
			}
	
			if (!empty($descricao)) {
				$qb->andWhere(
					$qb->expr()->like($qb->expr()->upper('cp.descricao')	, ':descricao')
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
				$log->debug("Código de erro: 0x61763131, Problema na posição: $i, array de valores: ".serialize($this->_valores));
				return $tr->trans('Código de erro: 0x61763131 entre em contato com o suporte !!, informações adicionais: "'.$i.'"');
			}elseif (!\Zage\App\Util::ehNumero($this->_valores[$i])) {
				return $tr->trans('Array de valores tem registro inválido na posição "'.$i.'" !!!');
			}else{
				$_valorOutros		= (isset($this->_outrosValores)) ? $this->_outrosValores[$i] : $this->getValorOutros();
				$_val				= \Zage\App\Util::to_float($this->_valores[$i]) + \Zage\App\Util::to_float($this->getValorJuros()) + \Zage\App\Util::to_float($this->getValorMora()) + \Zage\App\Util::to_float($_valorOutros) - \Zage\App\Util::to_float($this->getValorDesconto());
				$_valorTotal		+= $_val;
				$valores[$i]		= \Zage\App\Util::to_float($this->_valores[$i]);
				$outrosValores[$i]	= ($_valorOutros) ? \Zage\App\Util::to_float($_valorOutros) : 0;
			}
		}
		
		$_valorTotal				= \Zage\App\Util::to_float(round($_valorTotal,2));
		$valTotalInformado			= \Zage\App\Util::to_float(round($this->_getValorTotal(),2));
		
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
		}elseif (\Zage\App\Util::validaData($this->getDataVencimento(), $system->config["data"]["dateFormat"]) == false) {
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
			return $tr->trans('"Conta de Pagamento" deve ser selecionada');
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
						$log->debug("Código de erro: 0x61763132, Problema na posição: $i, array de valores: ".serialize($this->_valoresRateio));
						return $tr->trans('Código de erro: 0x61763132 entre em contato com o suporte !!, informações adicionais: "'.$i.'"');
					}elseif (!\Zage\App\Util::ehNumero($this->_valoresRateio[$i][$j])) {
						return $tr->trans('Array de valores tem registro inválido na posição "'.$j.'" !!!');
					}
				}
			}else{
				if ($this->_valoresRateio[$i] == 0) {
					$log->debug("Código de erro: 0x61763133, Problema na posição: $i, array de valores: ".serialize($this->_valoresRateio));
					return $tr->trans('Código de erro: 0x61763133 entre em contato com o suporte !!, informações adicionais: "'.$i.'"');
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
							return $tr->trans('Array de Centro de Custos tem Centro de Custo inexistente na posição "'.$j.'" código: "'.$this->_centroCustosRateio[$i][$j].'" !!!');
						}
					}
				}
			}else{
				if (!empty($this->_centroCustosRateio[$i])) {
					$oCentro		= $em->getRepository('Entidades\ZgfinCentroCusto')->findOneBy(array('codigo' => $this->_centroCustosRateio[$i]));
					if (!$oCentro) {
						return $tr->trans('Array de Centro de Custos tem Centro de Custo inexistente na posição "'.$j.'" código: "'.$this->_centroCustosRateio[$i][$j].'" !!!');
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
			$oContaInicial		= $em->getRepository('Entidades\ZgfinContaPagar')->findOneBy(array('codOrganizacao' => $system->getCodOrganizacao(),'codigo' => $this->_getCodigo()));
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
		$this->setValorOutros(\Zage\App\Util::to_float($this->getValorOutros()));
		$this->setValorDesconto(\Zage\App\Util::to_float($this->getValorDesconto()));
		
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
		$valorInicial	= $this->getValor();
		$vencInicial	= $this->getDataVencimento();
		
		for ($p	= $parcelaIni; $p <= $parcelaFim; $p++) {
			
			#################################################################################
			## Copia os valores de um objeto para o outro
			#################################################################################
			if ($this->_getCodigo() != null) {
				if ($this->_getIndAlterarSeq () == 1) {
					$object			= $em->getRepository('Entidades\ZgfinContaPagar')->findOneBy(array('codOrganizacao' => $system->getCodOrganizacao(),'parcela' => $p, 'codGrupoConta' => $this->getCodGrupoConta(),'codStatus' => array('A')));
				}else{
					$object			= $em->getRepository('Entidades\ZgfinContaPagar')->findOneBy(array('codOrganizacao' => $system->getCodOrganizacao(),'codigo' => $this->_getCodigo()));
				}
				if (!$object)	continue;
			}else{
				$object	= new \Entidades\ZgfinContaPagar();
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
				$object->setCodContaPerfil($this->getCodContaPerfil());
			}
				
			$object->setCodOrganizacao($this->getCodOrganizacao());
			$object->setCodFormaPagamento($this->getCodFormaPagamento());
			$object->setCodMoeda($this->getCodMoeda());
			$object->setCodPessoa($this->getCodPessoa());
			$object->setDescricao($this->getDescricao());
			$object->setDocumento($this->getDocumento());
			$object->setNossoNumero($this->getNossoNumero());
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
			$object->setIndPagarAuto($this->getIndPagarAuto());
			
			#################################################################################
			## Data de Autorização e Indicador de Autorizado, se não for definido consultar o parâmetro do sistema
			#################################################################################
			$indAutAuto		=	\Zage\Adm\Parametro::getValor('FIN_AUTORIZA_CONTA_PAG_NA_EMISSAO');
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
			
			$valorTotalParcela		= \Zage\App\Util::to_float($this->_valores[$i]) + \Zage\App\Util::to_float($this->getValorJuros()) + \Zage\App\Util::to_float($this->getValorMora()) + \Zage\App\Util::to_float($this->getValorOutros()) - \Zage\App\Util::to_float($this->getValorDesconto());

			#################################################################################
			## Guarda o código do grupo da conta caso a conta esteja sendo substituída
			#################################################################################
			$this->setCodGrupoSubstituicao($object->getCodGrupoConta());
					
			
			try {
				
				$em->persist($object);
				$this->_object	= $object;
				
				if ($p == $parcelaFim) {
					$this->_setCodConta($object->getCodigo());
					$this->setCodStatus($object->getCodStatus());
				}
				
				
				#################################################################################
				## Gravar os rateios
				#################################################################################
				$rateio		= new \Zage\Fin\ContaPagarRateio();
				
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
				$rateio->setCodContaPag($object);
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
				if ($this->_getFlagPaga()) {
					//if ($object->getDataVencimento() <= \DateTime::createFromFormat($system->config["data"]["dateFormat"],date($system->config["data"]["dateFormat"]))) {
						$erro = $this->paga($object, $object->getCodConta(), $object->getCodFormaPagamento(), $object->getDataVencimento()->format($system->config["data"]["dateFormat"]), \Zage\App\Util::toPHPNumber($object->getValor()), \Zage\App\Util::toPHPNumber($object->getValorJuros()), \Zage\App\Util::toPHPNumber($object->getValorMora()), \Zage\App\Util::toPHPNumber($object->getValorDesconto()), \Zage\App\Util::toPHPNumber($object->getValorOutros()), $object->getDocumento(),"MAN");
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
		global $em,$_user,$log,$system,$tr;
		
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
		
		
		if ($status == "A") {

			#################################################################################
			## Calcula o valor a cancelar
			#################################################################################
			$valorCancelar	= ( floatval($oConta->getValor()) + floatval($oConta->getValorJuros()) + floatval($oConta->getValorMora()) + floatval($oConta->getValorOutros()) - floatval($oConta->getValorDesconto()) - floatval($oConta->getValorCancelado())  );

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
			$hist		= new \Entidades\ZgfinContaPagHistCanc();
			$hist->setCodConta($oConta);
			$hist->setCodUsuario($_user);
			$hist->setMotivo($motivo);
			$hist->setValor($valorCancelar);
			$hist->setDataCancelamento(new \DateTime("now"));
			
		}elseif ($status == "P") {
			
			#################################################################################
			## Calcula o valor a cancelar
			#################################################################################
			$valorCancelar	= $this::getSaldoAPagar($oConta->getCodigo());
			
			#################################################################################
			## Remove o valor cancelado da tabela de Rateio
			#################################################################################
			$rateios	= $em->getRepository('Entidades\ZgfinContaPagarRateio')->findBy(array('codContaPag' => $oConta->getCodigo()));
			$numRateios	= sizeof($rateios);
			$valorTotal	= ( floatval($oConta->getValor()) + floatval($oConta->getValorJuros()) + floatval($oConta->getValorMora()) + floatval($oConta->getValorOutros()) - floatval($oConta->getValorDesconto()) - floatval($oConta->getValorCancelado())  );
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
			$hist		= new \Entidades\ZgfinContaPagHistCanc();
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
	 * Efetivar o pagamento de uma conta
	 * @param object $oConta
	 * @param int $codContaDeb
	 * @param int $codFormaPag
	 * @param date $dataPag
	 * @param float $valor
	 * @param float $valorJuros
	 * @param float $valorMora
	 * @param float $valorDesconto
	 * @param float $valorOutros
	 * @param string $documento
	 * @param int $codTipoBaixa
	 * @param int $seqRetorno
	 * @param int $usarAdiantamento
	 */
	public function paga (\Entidades\ZgfinContaPagar $oConta,$codContaDeb,$codFormaPag,$dataPag,$valor,$valorJuros,$valorMora,$valorDesconto,$valorOutros,$documento,$codTipoBaixa,$seqRetorno = null,$usarAdiantamento = null) {
		global $em,$system,$tr,$log;
		
		#################################################################################
		## Valida o status da conta
		#################################################################################
		switch ($oConta->getCodStatus()->getCodigo()) {
			case "A":
			case "P":
				$podePag	= true;
				break;
			default:
				$podePag	= false;
				break;
		}
		
		if (!$podePag) {
			return($tr->trans('Conta não pode ser confirmada, status não permitido (%s)',array('%s' => $oConta->getCodStatus()->getCodigo())));
		}
		
		#################################################################################
		## Validação da conta
		#################################################################################
		/*if (!isset($codConta) || empty($codConta)) {
			return("Falta de parâmetros (COD_CONTA)");
		}*/
		
		/*if (!isset($codFormaPag) || empty($codFormaPag)) {
			return("Falta de parâmetros (FORMA_PAG)");
		}*/
		
		if (!isset($dataPag) || empty($dataPag)) {
			return("Falta de parâmetros (DATA_PAG)");
		}
		
		if (!isset($valor) || empty($valor)) {
			return("Falta de parâmetros (VALOR)");
		}
		
		$valData	= new \Zage\App\Validador\DataBR();
		
		if ($valData->isValid($dataPag) == false) {
			return("Campo DATA DE PAGAMENTO inválido");
		}
		
		
		/** 
		 * 
		 * 
		 * 
		 * 
		 * Verificar o que fazer quando o valor do pagamento for maior do que o valor da conta 
		 * 
		 * 
		 * 
		 * 
		 * 
		 * 
		 * **/

		#################################################################################
		## Ajusta os valores para o Formato do Banco
		#################################################################################
		$valor			= \Zage\App\Util::to_float($valor);
		$valorJuros		= \Zage\App\Util::to_float($valorJuros);
		$valorMora		= \Zage\App\Util::to_float($valorMora);
		$valorOutros	= \Zage\App\Util::to_float($valorOutros);
		$valorDesconto	= \Zage\App\Util::to_float($valorDesconto);
		
		
		#################################################################################
		## Calcular o valor total pago
		#################################################################################
		$valorTotal	= $valor + $valorJuros + $valorMora + $valorOutros - $valorDesconto;
		
		#################################################################################
		## Verificar se foi usado o adiantamento para baixar, caso tenha sido
		## verificar se o saldo de adiantamento do cliente é sulficiente para cobrir
		## a baixa
		#################################################################################
		if ($usarAdiantamento == 1 && $oConta->getCodPessoa()) {
			$saldoAd			= \Zage\Fin\Adiantamento::getSaldo($oConta->getCodOrganizacao(), $oConta->getCodPessoa()->getCodigo());
			if ($valorTotal	> $saldoAd)	{
				return($tr->trans('Saldo de adiantamento insuficiente para efetuar a baixa'));
			}
		}
		
		#################################################################################
		## Resgatar o saldo da conta
		#################################################################################
		if ($oConta->getCodigo()) {
			$saldo		= $this::getSaldoAPagar($oConta->getCodigo());
		}else{
			$saldo		= $valorTotal;
		}
		
		
		#################################################################################
		## Calcular o novo status
		#################################################################################
		if ($valorTotal < $saldo) {
			$codStatus	= "P";
			$dataLiq	= null;
		}else{
			$codStatus	= "L";
			$dataLiq	= $dataPag;
		}
		
		#################################################################################
		## Resgatar o objeto do status
		#################################################################################
		$oStatus		= $em->getRepository('Entidades\ZgfinContaStatusTipo')->findOneBy(array('codigo' => $codStatus));

		#################################################################################
		## Resgatar os objetos das chaves estrangeiras
		#################################################################################
		$oOrg		= $em->getRepository('Entidades\ZgadmOrganizacao')->findOneBy(array('codigo' => $oConta->getCodOrganizacao()->getCodigo()));
		$oMoeda		= $em->getRepository('Entidades\ZgfinMoeda')->findOneBy(array('codigo' => 1));
		$oFil		= $em->getRepository('Entidades\ZgadmOrganizacao')->findOneBy(array('codigo' => $system->getCodOrganizacao()));
		$oOrigem	= $em->getRepository('Entidades\ZgadmOrigem')->findOneBy(array('codigo' => 1));
		$oTipoOper	= $em->getRepository('Entidades\ZgfinOperacaoTipo')->findOneBy(array('codigo' => "D"));
		$oBaixa		= $em->getRepository('Entidades\ZgfinBaixaTipo')->findOneBy(array('codigo' => $codTipoBaixa));
		
		if ($codContaDeb) {
			$oContaDeb		= $em->getRepository('Entidades\ZgfinConta')->findOneBy(array('codigo' => $codContaDeb));
		}else{
			$oContaDeb		= null;
		}
		
		if ($codFormaPag) {
			$oFormaPag		= $em->getRepository('Entidades\ZgfinFormaPagamento')->findOneBy(array('codigo' => $codFormaPag));
		}else{
			$oFormaPag		= null;
		}
		
		if (!$oBaixa)	return ('Tipo de baixa "'.$codTipoBaixa.'" não encontrado');
		
		#################################################################################
		## Criar o objeto das datas
		#################################################################################
		if (!empty($dataPag)) {
			$dataPag = \DateTime::createFromFormat($system->config["data"]["dateFormat"], $dataPag);
		}else{
			$dataPag		= null;
		}
		
		if (!empty($dataLiq)) {
			$dataLiq = \DateTime::createFromFormat($system->config["data"]["dateFormat"], $dataLiq);
		}else{
			$dataLiq		= null;
		}
		
		
		#################################################################################
		## Grupo de Movimentação
		#################################################################################
		$grupoMov	= \Zage\Adm\Sequencial::proximoValor("ZgfinSeqCodGrupoMov");
		
		#################################################################################
		## Criar o objeto do Histórico de Pagamento
		#################################################################################
		$oHist			= new \Entidades\ZgfinHistoricoPag();
		$oHist->setCodContaPag($oConta);
		$oHist->setCodConta($oContaDeb);
		$oHist->setCodFormaPagamento($oFormaPag);
		$oHist->setCodGrupoLanc($oConta->getCodGrupoLanc());
		$oHist->setCodMoeda($oMoeda);
		$oHist->setDataPagamento($dataPag);
		$oHist->setDataTransacao(new \DateTime("now"));
		$oHist->setDocumento($documento);
		$oHist->setValorDesconto($valorDesconto);
		$oHist->setValorJuros($valorJuros);
		$oHist->setValorMora($valorMora);
		$oHist->setValorOutros($valorOutros);
		$oHist->setValorPago($valor);
		$oHist->setCodGrupoMov($grupoMov);
		$oHist->setCodTipoBaixa($oBaixa);
		$oHist->setSeqRetornoBancario($seqRetorno);
		
		#################################################################################
		## Atualizar as informações da conta
		#################################################################################
		$oConta->setCodStatus($oStatus);
		$oConta->setDataLiquidacao($dataLiq);
		
		
		#################################################################################
		## Gerar a movimentação bancária, apenas se não for por adiantamento
		#################################################################################
		$oMov	= new \Zage\Fin\MovBancaria();
		$oMov->setCodOrganizacao($oFil);
		$oMov->setCodConta($oContaDeb);
		$oMov->setCodOrigem($oOrigem);
		$oMov->setCodTipoOperacao($oTipoOper);
		$oMov->setDataMovimentacao($dataPag);
		$oMov->setDataOperacao(new \DateTime("now"));
		$oMov->setValor($valorTotal);
		$oMov->setCodGrupoMov($grupoMov);
		
		$err	= $oMov->salva();
		if ($err) return $err;

		if (($usarAdiantamento == 1) && $oConta->getCodPessoa() ){

			#################################################################################
			## Cria o adiantamento de débito
			#################################################################################
			\Zage\Fin\Adiantamento::salva($oOrg->getCodigo(),"5","D",$oConta->getCodPessoa()->getCodigo(),null,$oConta,$dataPag->format($system->config["data"]["dateFormat"]),$valorTotal,$grupoMov);
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
	 * Calcular o saldo a pagar de uma conta
	 * @param int $codConta
	 */
	public static function getSaldoAPagar($codConta) {
		global $em,$system,$tr,$log;
		
		#################################################################################
		## Resgata as informações da conta
		#################################################################################
		$oConta		= $em->getRepository('Entidades\ZgfinContaPagar')->findOneBy(array('codOrganizacao' => $system->getCodOrganizacao(), 'codigo' => $codConta));
		
		if (!$oConta) {
			return (null);
		}
		
		/** calcular valores já pagos **/
		$histPag		= $em->getRepository('Entidades\ZgfinHistoricoPag')->findBy(array('codContaPag' => $codConta));
		$valorPag		= 0;
			
		for ($i = 0; $i < sizeof($histPag); $i++) {
			$valorPag += floatval($histPag[$i]->getValorPago()) + floatval($histPag[$i]->getValorJuros()) + floatval($histPag[$i]->getValorMora()) + floatval($histPag[$i]->getValorOutros()) - floatval($histPag[$i]->getValorDesconto());
		}
		
		$valorTotal		= round( floatval($oConta->getValor()) + floatval($oConta->getValorJuros()) + floatval($oConta->getValorMora()) + floatval($oConta->getValorOutros()) - floatval($oConta->getValorDesconto()) - floatval($oConta->getValorCancelado()),2);
		
		return round($valorTotal - $valorPag,2);
		
	}
	

	/**
	 * Calcular o saldo a pagar de uma conta
	 * @param int $codConta
	 */
	public function getValorJaPago($codConta) {
		global $em,$system,$tr,$log;
		
		#################################################################################
		## Resgata as informações da conta
		#################################################################################
		$oConta		= $em->getRepository('Entidades\ZgfinContaPagar')->findOneBy(array('codOrganizacao' => $system->getCodOrganizacao(), 'codigo' => $codConta));
	
		if (!$oConta) {
			return (null);
		}
	
		/** calcular valores já pagos **/
		$histPag		= $em->getRepository('Entidades\ZgfinHistoricoPag')->findBy(array('codContaPag' => $codConta));
		$valorPag		= 0;
			
		for ($i = 0; $i < sizeof($histPag); $i++) {
			$valorPag += floatval($histPag[$i]->getValorPago()) + floatval($histPag[$i]->getValorJuros()) + floatval($histPag[$i]->getValorMora()) + floatval($histPag[$i]->getValorOutros()) - floatval($histPag[$i]->getValorDesconto());
		}
	
		return round($valorPag,2);
	
	}
	
	
	/**
	 * Excluir uma conta
	 */
	public function exclui($codConta) {
		global $em,$_user,$log,$system,$tr;
	
		#################################################################################
		## Verifica se a conta existe
		#################################################################################
		$oConta		= $em->getRepository('Entidades\ZgfinContaPagar')->findOneBy(array('codOrganizacao' => $system->getCodOrganizacao(), 'codigo' => $codConta));
	
		if (!$oConta) {
			return($tr->trans('Conta %s não encontrada !!!',array('%s' => $codConta)));
		}
	
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
			return($tr->trans('Conta não pode ser excluída, status não permitido (%s)',array('%s' => $oConta->getCodStatus()->getCodigo())));
		}
	
		#################################################################################
		## Apaga os rateios
		#################################################################################
		$rateios	= $em->getRepository('Entidades\ZgfinContaPagarRateio')->findBy(array('codContaPag' => $codConta));
		
		for ($i = 0; $i < sizeof($rateios); $i++) {
			$em->remove($rateios[$i]);
		}

		#################################################################################
		## Apagar os históricos
		#################################################################################
		$hist	= $em->getRepository('Entidades\ZgfinContaPagarHistorico')->findBy(array('codConta' => $codConta));
		
		for ($i = 0; $i < sizeof($hist); $i++) {
			$em->remove($hist[$i]);
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
		global $em,$system,$log;
	
		$qb 	= $em->createQueryBuilder();
	
	
		try {
			$qb->select('c.descricao as categoria,sum(cpr.valor) as valor')
			->from('\Entidades\ZgfinContaPagar','cp')
			->leftJoin('\Entidades\ZgfinPessoa', 'p', \Doctrine\ORM\Query\Expr\Join::WITH, 'cp.codPessoa = p.codigo')
			->leftJoin('\Entidades\ZgfinContaPagarRateio', 'cpr', \Doctrine\ORM\Query\Expr\Join::WITH, 'cp.codigo = cpr.codContaPag')
			->leftJoin('\Entidades\ZgfinCategoria', 'c', \Doctrine\ORM\Query\Expr\Join::WITH, 'cpr.codCategoria = c.codigo')
			->where($qb->expr()->andX(
				$qb->expr()->eq('cp.codOrganizacao'	, ':codOrganizacao'),
				$qb->expr()->isNotNull('cpr.codCategoria')
			))
			->addGroupBy('c.descricao')
			->orderBy('cp.dataVencimento','ASC')
			->addOrderBy('cp.descricao,cp.parcela,cp.dataEmissao','ASC')
			->setParameter('codOrganizacao', $system->getCodOrganizacao());
				
			if ($dataTipo == "E") {
				$campoData		= "cp.dataEmissao";
			}else{
				$campoData		= "cp.dataVencimento";
			}
				
			if (!empty($valorIni)) {
				$qb->andWhere(
						$qb->expr()->gte("cp.valor", ':valorIni')
				);
				$qb->setParameter('valorIni', $valorIni);
			}
				
			if (!empty($valorFim)) {
				$qb->andWhere(
						$qb->expr()->lte("cp.valor", ':valorFim')
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
						$qb->expr()->in('cp.codStatus'	, ':aStatus')
				);
				$qb->setParameter('aStatus', $aStatus);
			}
	
			if (!empty($aCentroCusto)) {
				$qb2 	= $em->createQueryBuilder();
				$qb->andWhere(
						$qb->expr()->exists(
								$qb2->select('cpr1')
								->from('\Entidades\ZgfinContaPagarRateio','cpr1')
								->where($qb2->expr()->andX(
										$qb2->expr()->eq('cpr1.codContaPag'		, 'cp.codigo'),
										$qb2->expr()->in('cpr1.codCentroCusto'	, $aCentroCusto)
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
								->from('\Entidades\ZgfinContaPagarRateio','cpr2')
								->where($qb2->expr()->andX(
										$qb2->expr()->eq('cpr2.codContaPag'		, 'cp.codigo'),
										$qb2->expr()->in('cpr2.codCategoria'		, $aCategoria)
								)
								)->getDQL()
						)
				);
			}
	
			if (!empty($aForma)) {
				$qb->andWhere(
						$qb->expr()->in('cp.codFormaPagamento'	, ':aForma')
				);
				$qb->setParameter('aForma', $aForma);
			}
	
			if (!empty($aContaDeb)) {
				$qb->andWhere(
						$qb->expr()->in('cp.codConta'	, ':aConta')
				);
				$qb->setParameter('aConta', $aContaDeb);
			}
	
			if (!empty($descricao)) {
				$qb->andWhere(
						$qb->expr()->like($qb->expr()->upper('cp.descricao')	, ':descricao')
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
		global $em,$system,$log;
	
		$qb 	= $em->createQueryBuilder();
	
	
		try {
			$qb->select('c.descricao as centroCusto,sum(cpr.valor) as valor')
			->from('\Entidades\ZgfinContaPagar','cp')
			->leftJoin('\Entidades\ZgfinPessoa', 'p', \Doctrine\ORM\Query\Expr\Join::WITH, 'cp.codPessoa = p.codigo')
			->leftJoin('\Entidades\ZgfinContaPagarRateio', 'cpr', \Doctrine\ORM\Query\Expr\Join::WITH, 'cp.codigo = cpr.codContaPag')
			->leftJoin('\Entidades\ZgfinCentroCusto', 'c', \Doctrine\ORM\Query\Expr\Join::WITH, 'cpr.codCentroCusto = c.codigo')
			->where($qb->expr()->andX(
				$qb->expr()->eq('cp.codOrganizacao'	, ':codOrganizacao'),
				$qb->expr()->isNotNull('cpr.codCentroCusto')
			))
			->addGroupBy('c.descricao')
			->orderBy('cp.dataVencimento','ASC')
			->addOrderBy('cp.descricao,cp.parcela,cp.dataEmissao','ASC')
			->setParameter('codOrganizacao', $system->getCodOrganizacao());
	
			if ($dataTipo == "E") {
				$campoData		= "cp.dataEmissao";
			}else{
				$campoData		= "cp.dataVencimento";
			}
	
			if (!empty($valorIni)) {
				$qb->andWhere(
						$qb->expr()->gte("cp.valor", ':valorIni')
				);
				$qb->setParameter('valorIni', $valorIni);
			}
	
			if (!empty($valorFim)) {
				$qb->andWhere(
						$qb->expr()->lte("cp.valor", ':valorFim')
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
						$qb->expr()->in('cp.codStatus'	, ':aStatus')
				);
				$qb->setParameter('aStatus', $aStatus);
			}
	
			if (!empty($aCentroCusto)) {
				$qb2 	= $em->createQueryBuilder();
				$qb->andWhere(
						$qb->expr()->exists(
								$qb2->select('cpr1')
								->from('\Entidades\ZgfinContaPagarRateio','cpr1')
								->where($qb2->expr()->andX(
										$qb2->expr()->eq('cpr1.codContaPag'		, 'cp.codigo'),
										$qb2->expr()->in('cpr1.codCentroCusto'	, $aCentroCusto)
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
								->from('\Entidades\ZgfinContaPagarRateio','cpr2')
								->where($qb2->expr()->andX(
										$qb2->expr()->eq('cpr2.codContaPag'		, 'cp.codigo'),
										$qb2->expr()->in('cpr2.codCategoria'		, $aCategoria)
								)
								)->getDQL()
						)
				);
			}
	
			if (!empty($aForma)) {
				$qb->andWhere(
						$qb->expr()->in('cp.codFormaPagamento'	, ':aForma')
				);
				$qb->setParameter('aForma', $aForma);
			}
	
			if (!empty($aContaDeb)) {
				$qb->andWhere(
						$qb->expr()->in('cp.codConta'	, ':aConta')
				);
				$qb->setParameter('aConta', $aContaDeb);
			}
	
			if (!empty($descricao)) {
				$qb->andWhere(
						$qb->expr()->like($qb->expr()->upper('cp.descricao')	, ':descricao')
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
	 * Substitui contas
	 */
	public function substitui () {
		global $em,$system,$log,$tr;
	
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
		$contas		= $em->getRepository('Entidades\ZgfinContaPagar')->findBy(array('codOrganizacao' => $system->getcodOrganizacao(), 'codigo' => $_contas));
		
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
		global $em,$_user,$log,$system,$tr;
	
		#################################################################################
		## Valida o status da conta
		#################################################################################
		$status 	= $oConta->getCodStatus()->getCodigo();
		switch ($status) {
			case "A":
			case "P":
				$podeSub	= true;
				break;
			default:
				$podeSub	= false;
				break;
		}
	
		if (!$podeSub) {
			return($tr->trans('Conta não pode ser substituída, status não permitido (%s)',array('%s' => $oConta->getCodStatus()->getCodigo())));
		}
	
	
		if ($status == "A") {
	
			#################################################################################
			## Calcula o valor a substituir
			#################################################################################
			$valorSub	= ( floatval($oConta->getValor()) + floatval($oConta->getValorJuros()) + floatval($oConta->getValorMora()) + floatval($oConta->getValorOutros()) - floatval($oConta->getValorDesconto()) - floatval($oConta->getValorCancelado())  );
	
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
			$hist		= new \Entidades\ZgfinContaPagHistSub();
			$hist->setCodConta($oConta);
			$hist->setCodUsuario($_user);
			$hist->setValor($valorSub);
			$hist->setData(new \DateTime("now"));
				
		}elseif ($status == "P") {
				
			#################################################################################
			## Calcula o valor a substituir
			#################################################################################
			$valorSub	= $this::getSaldoAPagar($oConta->getCodigo());
				
			#################################################################################
			## Remove o valor substituido da tabela de Rateio
			#################################################################################
			$rateios	= $em->getRepository('Entidades\ZgfinContaPagarRateio')->findBy(array('codContaPag' => $oConta->getCodigo()));
			$numRateios	= sizeof($rateios);
			$valorTotal	= ( floatval($oConta->getValor()) + floatval($oConta->getValorJuros()) + floatval($oConta->getValorMora()) + floatval($oConta->getValorOutros()) - floatval($oConta->getValorDesconto()) - floatval($oConta->getValorCancelado())  );
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
			$hist		= new \Entidades\ZgfinContaPagHistSub();
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
	 * @param \Entidades\ZgfinContaPagar $oConta
	 */
	public static function calculaValorTotal(\Entidades\ZgfinContaPagar $oConta) {
	
		#################################################################################
		## Calcula o valor do Júros
		#################################################################################
		$valJuros		= $oConta->getValorJuros();
		$valJuros		= ($valJuros < 0) ? 0 : $valJuros;
	
		#################################################################################
		## Calcula o valor da Mora
		#################################################################################
		$valMora		= $oConta->getValorMora();
		$valMora		= ($valMora < 0) ? 0 : $valMora;
	
		#################################################################################
		## Calcula o valor total da conta
		#################################################################################
		$valorTotal			= \Zage\App\Util::to_float($oConta->getValor() + $valJuros + $valMora + $oConta->getValorOutros() - ($oConta->getValorCancelado() + $oConta->getValorDesconto()));
	
		return $valorTotal;
	}
	
	
	
	public static function geraNumero() {
		return (date('Ymd').'/'.str_pad(mt_rand(0,999999), 6, "0", STR_PAD_LEFT));
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
	
	public function _setFlagPaga($flagPaga) {
		$this->_flagPaga	= $flagPaga;
	}
	
	public function _getFlagPaga() {
		return($this->_flagPaga);
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
	
	public function _getObject() {
		return  $this->_object;
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
	 * Excluir uma baixa, retornar ao estado anterior da baixa
	 * @param \Entidades\ZgfinContaPagar $oConta
	 * @param \Entidades\ZgfinHistoricoPag $oHist
	 * @throws \Exception
	 */
	public function excluiBaixa (\Entidades\ZgfinContaPagar $oConta,\Entidades\ZgfinHistoricoPag $oHist) {
	
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
			throw new \Exception($tr->trans("Pagamento não pode ser excluído"));
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
		if ($oConta->getCodPessoa()) {
			$saldoAdiant	= \Zage\Fin\Adiantamento::getSaldo($oConta->getCodOrganizacao()->getCodigo(),$oConta->getCodPessoa()->getCodigo());
		}else{
			$saldoAdiant	= 0;
		}
		
		#################################################################################
		## Verifica se foi cadastrado adiantamento para essa baixa
		#################################################################################
		$aAdiant	=  $em->getRepository('Entidades\ZgfinMovAdiantamento')->findBy(array('codContaPag' => $oHist->getCodContaPag()->getCodigo(), 'codGrupoMov' => $grupoMov));
	
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
		$aCanc		=  $em->getRepository('Entidades\ZgfinContaPagHistCanc')->findBy(array('codConta' => $oHist->getCodContaPag()->getCodigo()));
		if ($aCanc)	throw new \Exception("Houve cancelamento de saldo na conta, para excluir a baixa, primeiro desfaça o cancelamento !!!");
	
		#################################################################################
		## Excluir os adiatamentos
		#################################################################################
		$aAdiant	=  $em->getRepository('Entidades\ZgfinMovAdiantamento')->findBy(array('codContaPag' => $oHist->getCodContaPag()->getCodigo(), 'codGrupoMov' => $grupoMov));
		for ($i = 0; $i < sizeof($aAdiant); $i++) {
			$em->remove($aAdiant[$i]);
		}
		
		#################################################################################
		## Verificar se a baixa que foi excluída agregou juros, mora e desconto a conta
		#################################################################################
		$valJurosBaixa		= round(floatval($oHist->getValorJuros())			,2);
		$valMoraBaixa		= round(floatval($oHist->getValorMora()) 			,2);
		$valJurosConta		= round(floatval($oConta->getValorJuros()) 			,2);
		$valMoraConta		= round(floatval($oConta->getValorMora()) 			,2);
		$valDescontoBaixa	= round(floatval($oHist->getValorDesconto())		,2);
		$valDescontoConta	= round(floatval($oConta->getValorDesconto())		,2);
	
		if (($valJurosBaixa > 0) && ($valJurosBaixa <= $valJurosConta) ) {
			$oConta->setValorJuros($valJurosConta - $valJurosBaixa);
			$_indSalvar			= true;
		}
	
		if (($valMoraBaixa > 0) && ($valMoraBaixa <= $valMoraConta) ) {
			$oConta->setValorMora($valMoraConta - $valMoraBaixa);
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
		$aHist			= new \Entidades\ZgfinContaPagarHistorico();
		$aHist->setCodConta($oConta);
		$aHist->setCodTipoHist($codTipoHist);
		$aHist->setCodUsuario($oUsu);
		$aHist->setData(new \DateTime());
		$aHist->setHistorico("Exclusão de baixa, código: ".$oHist->getCodigo());
		$em->persist($aHist);
		
		#################################################################################
		## Excluir a Baixa
		#################################################################################
		$aBaixa	=  $em->getRepository('Entidades\ZgfinHistoricoPag')->findBy(array('codigo' => $oHist->getCodigo()));
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
	 * @param \Zage\Fin\ContaPagar $oConta
	 * @return string $codStatus
	 */
	public static function recalculaStatus (\Entidades\ZgfinContaPagar $oConta) {
	
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
		$oCanc	=  $em->getRepository('Entidades\ZgfinContaPagHistCanc')->findOneBy(array('codConta' => $oConta->getCodigo()));
	
		#################################################################################
		## Verificar se Existem baixas
		#################################################################################
		$aBaixa	=  $em->getRepository('Entidades\ZgfinHistoricoPag')->findBy(array('codContaPag' => $oConta->getCodigo()));
	
		#################################################################################
		## Verificar se Existem Substituições
		#################################################################################
		$oSub	=  $em->getRepository('Entidades\ZgfinContaPagHistSub')->findBy(array('codConta' => $oConta->getCodigo()));
			
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