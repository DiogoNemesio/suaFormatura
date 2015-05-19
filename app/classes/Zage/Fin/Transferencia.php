<?php

namespace Zage\Fin;

/**
 * Gerenciar as transferências
 * 
 * @package: Transferencia
 * @Author: Daniel Henrique Cassela
 * @version: 1.0.1
 * 
 */

class Transferencia extends \Entidades\ZgfinTransferencia {

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
	 * Array com os valores
	 * @var array
	 */
	private $_valores;
	
	/**
	 * Array com as datas
	 * @var array
	 */
	private $_datas;
	
	/**
	 * Código da Conta
	 * @var unknown
	 */
	private $_codTransferencia;
	
	/**
	 * Valor total da transferência
	 * @var float
	 */
	private $_valorTotal;
	
	/**
	 * _flagRealiza
	 * @var int
	 */
	private $_flagRealizada;
	
	/**
	 * _indAlterarSeq
	 * @var number
	 */
	private $_indAlterarSeq;
	
	/**
	 *
	 * Lista
	 */
	public static function lista () {
		global $em,$system;
		
		$qb 	= $em->createQueryBuilder();
		try {
			$qb->select('t')
			->from('\Entidades\ZgfinTransferencia','t')
			->where($qb->expr()->andX(
				$qb->expr()->eq('t.codFilial'	, ':codFilial')
			))
			->orderBy('t.codStatus','ASC')
			->addOrderBy('t.dataEmissao','DESC')
			->setParameter('codFilial', $system->getCodEmpresa());
			
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
	public static function busca ($dataIni = null, $dataFim = null, $dataTipo = null,$valorIni = null, $valorFim = null,$aStatus = array(),$aForma = array(),$aContaOrig = array(),$aContaDest = array(),$descricao = null) {
		global $em,$system,$log;
	
		$qb 	= $em->createQueryBuilder();
	
		try {
			$qb->select('t')
			->from('\Entidades\ZgfinTransferencia','t')
			->where($qb->expr()->andX(
				$qb->expr()->eq('t.codFilial'	, ':codFilial')
			))
			->orderBy('t.dataTransferencia','ASC')
			->addOrderBy('t.descricao,t.parcela,t.dataEmissao','ASC')
			->setParameter('codFilial', $system->getCodEmpresa());
				
			if ($dataTipo == "E") {
				$campoData		= "t.dataEmissao";
			}else{
				$campoData		= "t.dataTransferencia";
			}
				
			if (!empty($valorIni)) {
				$qb->andWhere(
					$qb->expr()->gte("t.valor", ':valorIni')
				);
				$qb->setParameter('valorIni', $valorIni);
			}
				
			if (!empty($valorFim)) {
				$qb->andWhere(
					$qb->expr()->lte("t.valor", ':valorFim')
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
					$qb->expr()->in('t.codStatus'	, ':aStatus')
				);
				$qb->setParameter('aStatus', $aStatus);
			}
	
			if (!empty($aForma)) {
				$qb->andWhere(
					$qb->expr()->in('t.codFormaPagamento'	, ':aForma')
				);
				$qb->setParameter('aForma', $aForma);
			}
	
			if (!empty($aContaOrig)) {
				$qb->andWhere(
					$qb->expr()->in('t.codContaOrigem'	, ':aContaOrig')
				);
				$qb->setParameter('aContaOrig', $aContaOrig);
			}
	
			if (!empty($aContaDest)) {
				$qb->andWhere(
					$qb->expr()->in('t.codContaDestino'	, ':aContaDest')
				);
				$qb->setParameter('aContaDest', $aContaDest);
			}
			
			if (!empty($descricao)) {
				$qb->andWhere(
					$qb->expr()->like($qb->expr()->upper('t.descricao')	, ':descricao')
				);
				$qb->setParameter('descricao', strtoupper('%'.$descricao.'%'));
			}
	
			$query 		= $qb->getQuery();
			//echo $query->getSQL();
			return($query->getResult());
		} catch (\Exception $e) {
			\Zage\App\Erro::halt($e->getMessage());
		}
	}
	
	
	
	/**
	 * Salva a transferência no banco
	 */
	public function salva () {
		global $em,$system,$log,$tr;
		
		
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
		$_valorTotal	= 0; 
		for ($i = 0; $i < $n; $i++) {
			if ($this->_valores[$i] == 0) {
				return $tr->trans('Array de valores tem registro com valor = 0 na posição "'.$i.'"');
			}elseif (!\Zage\App\Util::ehNumero($this->_valores[$i])) {
				return $tr->trans('Array de valores tem registro inválido na posição "'.$i.'" !!!');
			}else{
				$valores[$i]	= \Zage\App\Util::toMysqlNumber($this->_valores[$i]);
				$_valorTotal	+= $valores[$i]; 
			}
		}
		
		if (\Zage\App\Util::toPHPNumber($_valorTotal) !== $this->_getValorTotal()) {
			$log->debug("Valor informado: ".$this->_getValorTotal()." Valor calculado: ".$_valorTotal);
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
		if (!$this->getCodPeriodoRecorrencia()) {
			return $tr->trans('Período de Recorrência deve ser Diário, Semanal, Mensal ou Anual');
		}
		
		if ($this->getCodTipoRecorrencia()->getCodigo() == "P") {
				
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
		
		
		#################################################################################
		## Validações das configurações
		#################################################################################
		if (!$this->getCodFormaPagamento()) {
			return $tr->trans('"Forma de Pagamento" deve ser selecionada');
		}
		
		if (!$this->getCodContaOrigem()) {
			return $tr->trans('"Conta de Origem" deve ser selecionada');
		}
		
		if (!$this->getCodContaDestino()) {
			return $tr->trans('"Conta de Destino" deve ser selecionada');
		}
		
		if ($this->getCodContaDestino()->getCodigo() == $this->getCodContaOrigem()->getCodigo()) {
			return $tr->trans('"Conta de Destino" deve ser diferente da "Conta de Origem');
		}
		
		#################################################################################
		## Grupo de Conta e lancamento, se não definido resgatar um valor da sequence
		#################################################################################
		if (!$this->_getCodTransferencia()) {
			if (!$this->getCodGrupoTransferencia()) $this->setCodGrupoTransferencia(\Zage\Adm\Sequencial::proximoValor("ZgfinSeqCodGrupoConta"));
			if (!$this->getCodGrupoLanc()) 			$this->setCodGrupoLanc(\Zage\Adm\Sequencial::proximoValor("ZgfinSeqCodGrupoLanc"));
		}else{
			$oTransfInicial		= $em->getRepository('Entidades\ZgfinTransferencia')->findOneBy(array('codFilial' => $system->getCodEmpresa(),'codigo' => $this->_getCodTransferencia()));
			$this->setCodGrupoTransferencia($oTransfInicial->getCodGrupoTransferencia());
			$this->setCodGrupoLanc($oTransfInicial->getCodGrupoLanc());
				
			#################################################################################
			## Verificar se a transferência já está realizada, pois não poderá ser alterada
			#################################################################################
			if ($oTransfInicial->getCodStatus() == "R") return $tr->trans('Transferência não pode ser alterada, status atual não permite');
		}
			
		
		#################################################################################
		## Número de Parcelas, se não definido usar o padrão que é "1"
		#################################################################################
		if (!$this->getNumParcelas()) 	$this->setNumParcelas(1);
		
		#################################################################################
		## Verificar se o código foi informado, pois tentaremos alterar a conta,
		## caso contrário, vamos cadastrar.
		## Usaremos o método de loop para cadastrar as parcelas, então se a transferẽncia não
		## for parcelada o loop será de 1
		#################################################################################
		if ($this->getCodTipoRecorrencia()->getCodigo() == "U") {
			$parcelaIni		= $this->getParcela();
			$parcelaFim		= $this->getParcela();
		}else {
			if ($this->_getCodTransferencia()) {
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
			if ($this->_getCodTransferencia() != null) {
				if ($this->_getIndAlterarSeq () == 1) {
					$object			= $em->getRepository('Entidades\ZgfinTransferencia')->findOneBy(array('codFilial' => $system->getCodEmpresa(),'parcela' => $p, 'codGrupoTransferencia' => $this->getCodGrupoTransferencia(),'codStatus' => array('P')));
				}else{
					$object			= $em->getRepository('Entidades\ZgfinTransferencia')->findOneBy(array('codFilial' => $system->getCodEmpresa(),'codigo' => $this->_getCodTransferencia()));
				}
				if (!$object)	continue;
			}else{
				$object	= new \Entidades\ZgfinTransferencia();
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
			## Só informar alguns campos, quando a transferência for nova
			#################################################################################
			if (!$this->_getCodTransferencia()) {
				$object->setCodGrupoTransferencia($this->getCodGrupoTransferencia());
				$object->setCodGrupoLanc($this->getCodGrupoLanc());
				$object->setCodStatus($this->getCodStatus());
			}

			$object->setCodFilial($this->getCodFilial());
			$object->setCodFormaPagamento($this->getCodFormaPagamento());
			$object->setCodMoeda($this->getCodMoeda());
			$object->setDescricao($this->getDescricao());
			$object->setDocumento($this->getDocumento());
			$object->setNumParcelas($this->getNumParcelas());
			$object->setParcelaInicial($this->getParcelaInicial());
			$object->setObservacao($this->getObservacao());
			$object->setCodPeriodoRecorrencia($this->getCodPeriodoRecorrencia());
			$object->setIntervaloRecorrencia($this->getIntervaloRecorrencia());
			$object->setCodTipoRecorrencia($this->getCodTipoRecorrencia());
			$object->setCodContaOrigem($this->getCodContaOrigem());
			$object->setCodContaDestino($this->getCodContaDestino());
			$object->setIndTransferirAuto($this->getIndTransferirAuto());
			$object->setValorCancelado($valorCanc);
			
			
			#################################################################################
			## Data de Autorização e Indicador de Autorizado, se não for definido consultar o parâmetro do sistema
			#################################################################################
			$indAutAuto		=	\Zage\Adm\Parametro::getValor('FIN_AUTORIZA_TRANSFERENCIA_NA_EMISSAO');
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
				$object->setDataTransferencia(\DateTime::createFromFormat($system->config["data"]["dateFormat"],$datas[$i]));
			}
			
			#################################################################################
			## Valor
			#################################################################################
			$object->setValor($valores[$i]);
			
			try {
			
				$em->persist($object);
			
				if ($p == $parcelaFim) {
					$this->_setCodTransferencia($object->getCodigo());
					$this->setCodStatus($object->getCodStatus());
				}
			
				#################################################################################
				## Relaliza automaticamente se a flag estiver setada, e a data de vencimento for
				## maior ou igual a hoje
				#################################################################################
				if ($this->_getFlagRealizada()) {
					if ($object->getDataTransferencia() <= \DateTime::createFromFormat($system->config["data"]["dateFormat"],date($system->config["data"]["dateFormat"]))) {
						$erro = $this->realiza($object, $object->getCodContaOrigem(), $object->getCodContaDestino(), $object->getCodFormaPagamento(), $object->getDataTransferencia()->format($system->config["data"]["dateFormat"]), \Zage\App\Util::toPHPNumber($object->getValor()), $object->getDocumento());
						if ($erro) {
							$log->debug("Erro ao salvar: ".$erro);
							return $erro;
						}
						
					}
				}
			
					
			} catch (\Exception $e) {
				return $e->getMessage();
			}
			
			if ($this->_getIndAlterarSeq () != 1) {
				$i++;
			}
		}		
		
	}
	
	
	/**
	 * Realizar a transferência
	 * @param int $codConta
	 * @param int $codFormaPag
	 * @param date $dataPag
	 * @param float $valor
	 * @param float $valorJuros
	 * @param float $valorMora
	 * @param float $valorDesconto
	 * @param string $documento
	 */
	public function realiza (\Entidades\ZgfinTransferencia $oTransf,$codContaOrig,$codContaDest,$codFormaPag,$dataTransf,$valor,$documento) {
		global $em,$system,$tr,$log,$_user;
	
		#################################################################################
		## Valida o status da conta
		#################################################################################
		switch ($oTransf->getCodStatus()->getCodigo()) {
			case "P":
			case "PA":
				$podePag	= true;
			break;
			default:
				$podePag	= false;
			break;
		}
	
		if (!$podePag) {
			return($tr->trans('Transferência não pode ser realizada, status não permitido (%s)',array('%s' => $oTransf->getCodStatus()->getCodigo())));
		}
	
		if (!isset($dataTransf) || empty($dataTransf)) {
			return("Falta de parâmetros (DATA_TRANSF)");
		}
	
		if (!isset($valor) || empty($valor)) {
			return("Falta de parâmetros (VALOR)");
		}
	
		$valData	= new \Zage\App\Validador\DataBR();
	
		if ($valData->isValid($dataTransf) == false) {
			return("Campo DATA DE TRANSFERÊNCIA inválida");
		}
	
	
		#################################################################################
		## Ajusta os valores para o Formato do Banco
		#################################################################################
		$valor			= \Zage\App\Util::toMysqlNumber($valor);
	
		#################################################################################
		## Resgatar o saldo da transferencia
		#################################################################################
		if ($oTransf->getCodigo()) {
			$saldo		= $this::getSaldoATransferir($oTransf->getCodigo());
		}else{
			$saldo		= $valor;
		}
	
	
		#################################################################################
		## Calcular o novo status
		#################################################################################
		if ($valor < $saldo) {
			$codStatus	= "PA";
			$dataReal	= null;
		}else{
			$codStatus	= "R";
			$dataReal	= $dataTransf;
		}
	
		#################################################################################
		## Resgatar o objeto do status
		#################################################################################
		$oStatus		= $em->getRepository('Entidades\ZgfinTransferenciaStatusTipo')->findOneBy(array('codigo' => $codStatus));
	
		#################################################################################
		## Resgatar os objetos das chaves estrangeiras
		#################################################################################
		$oMoeda		= $em->getRepository('Entidades\ZgfinMoeda')->findOneBy(array('codigo' => 1));
		$oFil		= $em->getRepository('Entidades\ZgadmEmpresa')->findOneBy(array('codigo' => $system->getCodEmpresa()));
		$oOrigem	= $em->getRepository('Entidades\ZgadmOrigem')->findOneBy(array('codigo' => 3));
		$oTipoOperD	= $em->getRepository('Entidades\ZgfinOperacaoTipo')->findOneBy(array('codigo' => "D"));
		$oTipoOperC	= $em->getRepository('Entidades\ZgfinOperacaoTipo')->findOneBy(array('codigo' => "C"));
		$oContaOrig	= $em->getRepository('Entidades\ZgfinConta')->findOneBy(array('codigo' => $codContaOrig));
		$oContaDest	= $em->getRepository('Entidades\ZgfinConta')->findOneBy(array('codigo' => $codContaDest));
		$oFormaPag	= $em->getRepository('Entidades\ZgfinFormaPagamento')->findOneBy(array('codigo' => $codFormaPag));
	
		#################################################################################
		## Criar o objeto das datas
		#################################################################################
		if (!empty($dataTransf)) {
			$dataTransf = \DateTime::createFromFormat($system->config["data"]["dateFormat"], $dataTransf);
		}else{
			$dataTransf		= null;
		}
	
		if (!empty($dataReal)) {
			$dataReal = \DateTime::createFromFormat($system->config["data"]["dateFormat"], $dataReal);
		}else{
			$dataReal		= null;
		}
	
	
		#################################################################################
		## Grupo de Movimentação
		#################################################################################
		$grupoMov	= \Zage\Adm\Sequencial::proximoValor("ZgfinSeqCodGrupoMov");
	
		#################################################################################
		## Criar o objeto do Histórico de Transferência
		#################################################################################
		$oHist			= new \Entidades\ZgfinHistoricoTransf();
		$oHist->setCodTransferencia($oTransf);
		$oHist->setCodContaOrigem($oContaOrig);
		$oHist->setCodContaDestino($oContaDest);
		$oHist->setCodFormaPagamento($oFormaPag);
		$oHist->setCodGrupoLanc($oTransf->getCodGrupoLanc());
		$oHist->setCodMoeda($oMoeda);
		$oHist->setCodUsuario($_user);
		$oHist->setDataTransferencia($dataTransf);
		$oHist->setDataTransacao(new \DateTime("now"));
		$oHist->setDocumento($documento);
		$oHist->setValor($valor);
		$oHist->setCodGrupoMov($grupoMov);
		
		#################################################################################
		## Atualizar as informações da conta
		#################################################################################
		$oTransf->setCodStatus($oStatus);
		$oTransf->setDataRealizacao($dataReal);
	
		#################################################################################
		## Gerar as movimentações bancárias
		#################################################################################
		$oMovOrig	= new \Zage\Fin\MovBancaria();
		$oMovOrig->setCodFilial($oFil);
		$oMovOrig->setCodConta($oContaOrig);
		$oMovOrig->setCodOrigem($oOrigem);
		$oMovOrig->setCodTipoOperacao($oTipoOperD);
		$oMovOrig->setDataMovimentacao($dataTransf);
		$oMovOrig->setDataOperacao(new \DateTime("now"));
		$oMovOrig->setValor($valor);
		$oMovOrig->setCodGrupoMov($grupoMov);
	
		$oMovDest	= new \Zage\Fin\MovBancaria();
		$oMovDest->setCodFilial($oFil);
		$oMovDest->setCodConta($oContaDest);
		$oMovDest->setCodOrigem($oOrigem);
		$oMovDest->setCodTipoOperacao($oTipoOperC);
		$oMovDest->setDataMovimentacao($dataTransf);
		$oMovDest->setDataOperacao(new \DateTime("now"));
		$oMovDest->setValor($valor);
		$oMovDest->setCodGrupoMov($grupoMov);
		
		
		$err	= $oMovOrig->salva();
		if ($err) 	return $err;
		
		$err	= $oMovDest->salva();
		if ($err) 	return $err;
		
	
		try {
			$em->persist($oTransf);
			$em->persist($oHist);
	
			return null;
		} catch (\Exception $e) {
			return $e->getMessage();
		}
	}
	
	
	/**
	 * Cancelar uma conta
	 */
	public function cancela($codTransferencia,$motivo) {
		global $em,$_user,$log,$system,$tr;
		
		#################################################################################
		## Verifica se a conta existe
		#################################################################################
		$oTransf		= $em->getRepository('Entidades\ZgfinTransferencia')->findOneBy(array('codFilial' => $system->getCodEmpresa(), 'codigo' => $codTransferencia));
		
		if (!$oTransf) {
			return($tr->trans('Transferência %s não encontrada !!!',array('%s' => $codTransferencia)));
		}
		
		#################################################################################
		## Valida o status da conta
		#################################################################################
		$status 	= $oTransf->getCodStatus()->getCodigo(); 
		switch ($status) {
			case "P":
			case "PA":
				$podeCan	= true;
				break;
			default:
				$podeCan	= false;
				break;
		}
		
		if (!$podeCan) {
			return($tr->trans('Transferência não pode ser cancelada, status não permitido (%s)',array('%s' => $oTransf->getCodStatus()->getCodigo())));
		}
		
		
		if ($status == "P") {
			$oStatus		= $em->getRepository('Entidades\ZgfinTransferenciaStatusTipo')->findOneBy(array('codigo' => 'C'));
			$valorCancelar	= floatval($oTransf->getValor());

			$oTransf->setDataCancelamento(new \DateTime("now"));
			$oTransf->setCodStatus($oStatus);
			$oTransf->setValorCancelado($valorCancelar);
			
			$hist		= new \Entidades\ZgfinTransferenciaHistCanc();
			$hist->setCodTransferencia($oTransf);
			$hist->setCodUsuario($_user);
			$hist->setMotivo($motivo);
			$hist->setValor($valorCancelar);
		}elseif ($status == "PA") {
			$oStatus		= $em->getRepository('Entidades\ZgfinTransferenciaStatusTipo')->findOneBy(array('codigo' => 'R'));
			$valorCancelar	= floatval($this::getSaldoATransferir($codTransferencia));
			
			$oTransf->setDataCancelamento(new \DateTime("now"));
			$oTransf->setCodStatus($oStatus);
			$oTransf->setValorCancelado($valorCancelar);
				
			$hist		= new \Entidades\ZgfinTransferenciaHistCanc();
			$hist->setCodTransferencia($oTransf);
			$hist->setCodUsuario($_user);
			$hist->setMotivo($motivo);
			$hist->setValor($valorCancelar);

		}
		
		try {
			$em->persist($oTransf);
			$em->persist($hist);
				
			return null;
		} catch (\Exception $e) {
			return $e->getMessage();
		}
	}
	
	
	
	/**
	 * Excluir os rateios de uma conta
	 */
	public function exclui($oTransf) {
		global $em,$_user,$log,$system,$tr;
	
		#################################################################################
		## Valida o status da conta
		#################################################################################
		$status 	= $oTransf->getCodStatus()->getCodigo();
		
		switch ($status) {
			case "P":
				$podeExc	= true;
				break;
			default:
				$podeExc	= false;
				break;
		}
	
		if (!$podeExc) {
			return($tr->trans('Transferência não pode ser excluída, status não permitido (%s)',array('%s' => $oTransf->getCodStatus()->getCodigo())));
		}
		
		try {
			
			$em->remove($oTransf);
			return null;
		} catch (\Exception $e) {
			return $e->getMessage();
		}
	}
	
	public function getValorJaTransferido($codTransf) {
		global $em,$system,$tr,$log;
		
		#################################################################################
		## Resgata as informações da transferência
		#################################################################################
		$oTransf		= $em->getRepository('Entidades\ZgfinTransferencia')->findOneBy(array('codFilial' => $system->getCodEmpresa(), 'codigo' => $codTransf));
		
		if (!$oTransf) {
			return (null);
		}
		
		/** calcular valores já transferidos **/
		$histTransf		= $em->getRepository('Entidades\ZgfinHistoricoTransf')->findBy(array('codTransferencia' => $codTransf));
		$valorTransf	= 0;
					
		for ($i = 0; $i < sizeof($histTransf); $i++) {
			$valorTransf += floatval($histTransf[$i]->getValor());
		}
		
		return round($valorTransf,2);
	}
	

	public function getSaldoATransferir($codTransf) {
		global $em,$system,$tr,$log;
		
		#################################################################################
		## Resgata as informações da transferência
		#################################################################################
		$oTransf		= $em->getRepository('Entidades\ZgfinTransferencia')->findOneBy(array('codFilial' => $system->getCodEmpresa(), 'codigo' => $codTransf));
		
		if (!$oTransf) {
			return (null);
		}
		
		/** calcular valores já transferidos **/
		$hist			= $em->getRepository('Entidades\ZgfinHistoricoTransf')->findBy(array('codTransferencia' => $codTransf));
		$valorTransf		= 0;
			
		for ($i = 0; $i < sizeof($hist); $i++) {
			$valorTransf += floatval($hist[$i]->getValor());
		}
		
		$valorTotal		= floatval($oTransf->getValor());
		
		return round($valorTotal - $valorTransf,2);
	}
	
	public static function geraNumero() {
		return (date('Ymd').'/'.str_pad(mt_rand(0,999999), 6, "0", STR_PAD_LEFT));
	}
	
	public function _setCodTransferencia($codTransferencia) {
		$this->_codTransferencia	= $codTransferencia;
	}
	
	public function _getCodTransferencia() {
		return ($this->_codTransferencia);
	}
	
	public function _setArrayValores($array) {
		$this->_valores	= $array;
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
	
	public function _setFlagRealizada($flagRealizada) {
		$this->_flagRealizada	= $flagRealizada;
	}
	
	public function _getFlagRealizada() {
		return($this->_flagRealizada);
	}
	
	public function _setIndAlterarSeq($flagAlterarSeq) {
		$this->_indAlterarSeq	= $flagAlterarSeq;
	}
	
	public function _getIndAlterarSeq() {
		return($this->_indAlterarSeq);
	}
}