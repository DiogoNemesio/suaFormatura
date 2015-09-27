<?php

namespace Entidades;

use Doctrine\ORM\Mapping as ORM;

/**
 * ZgfinContaPagar
 *
 * @ORM\Table(name="ZGFIN_CONTA_PAGAR", uniqueConstraints={@ORM\UniqueConstraint(name="ZGFIN_CONTA_PAGAR_UK01", columns={"COD_ORGANIZACAO", "NUMERO"})}, indexes={@ORM\Index(name="fk_ZGFIN_CONTA_PAGAR_2_idx", columns={"COD_PESSOA"}), @ORM\Index(name="fk_ZGFIN_CONTA_PAGAR_3_idx", columns={"COD_STATUS"}), @ORM\Index(name="fk_ZGFIN_CONTA_PAGAR_4_idx", columns={"COD_MOEDA"}), @ORM\Index(name="fk_ZGFIN_CONTA_PAGAR_5_idx", columns={"COD_FORMA_PAGAMENTO"}), @ORM\Index(name="fk_ZGFIN_CONTA_PAGAR_6_idx", columns={"COD_TIPO_RECORRENCIA"}), @ORM\Index(name="fk_ZGFIN_CONTA_PAGAR_7_idx", columns={"COD_PERIODO_RECORRENCIA"}), @ORM\Index(name="fk_ZGFIN_CONTA_PAGAR_8_idx", columns={"COD_CONTA"}), @ORM\Index(name="ZGFIN_CONTA_PAGAR_IX01", columns={"COD_ORGANIZACAO", "COD_GRUPO_CONTA"}), @ORM\Index(name="ZGFIN_CONTA_PAGAR_IX02", columns={"COD_TRANSACAO"}), @ORM\Index(name="ZGFIN_CONTA_PAGAR_IX03", columns={"COD_GRUPO_ASSOCIACAO"}), @ORM\Index(name="IDX_96D3C4759F83D42B", columns={"COD_ORGANIZACAO"})})
 * @ORM\Entity
 */
class ZgfinContaPagar
{
    /**
     * @var integer
     *
     * @ORM\Column(name="CODIGO", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $codigo;

    /**
     * @var string
     *
     * @ORM\Column(name="NUMERO", type="string", length=20, nullable=false)
     */
    private $numero;

    /**
     * @var string
     *
     * @ORM\Column(name="DESCRICAO", type="string", length=60, nullable=false)
     */
    private $descricao;

    /**
     * @var integer
     *
     * @ORM\Column(name="PARCELA", type="integer", nullable=false)
     */
    private $parcela;

    /**
     * @var integer
     *
     * @ORM\Column(name="NUM_PARCELAS", type="integer", nullable=false)
     */
    private $numParcelas;

    /**
     * @var float
     *
     * @ORM\Column(name="VALOR", type="float", precision=10, scale=0, nullable=false)
     */
    private $valor;

    /**
     * @var float
     *
     * @ORM\Column(name="VALOR_JUROS", type="float", precision=10, scale=0, nullable=false)
     */
    private $valorJuros;

    /**
     * @var float
     *
     * @ORM\Column(name="VALOR_MORA", type="float", precision=10, scale=0, nullable=false)
     */
    private $valorMora;

    /**
     * @var float
     *
     * @ORM\Column(name="VALOR_DESCONTO", type="float", precision=10, scale=0, nullable=false)
     */
    private $valorDesconto;

    /**
     * @var float
     *
     * @ORM\Column(name="VALOR_CANCELADO", type="float", precision=10, scale=0, nullable=false)
     */
    private $valorCancelado;

    /**
     * @var float
     *
     * @ORM\Column(name="VALOR_OUTROS", type="float", precision=10, scale=0, nullable=false)
     */
    private $valorOutros;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="DATA_EMISSAO", type="datetime", nullable=false)
     */
    private $dataEmissao;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="DATA_VENCIMENTO", type="date", nullable=false)
     */
    private $dataVencimento;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="DATA_LIQUIDACAO", type="datetime", nullable=true)
     */
    private $dataLiquidacao;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="DATA_AUTORIZACAO", type="datetime", nullable=true)
     */
    private $dataAutorizacao;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="DATA_CANCELAMENTO", type="datetime", nullable=true)
     */
    private $dataCancelamento;

    /**
     * @var integer
     *
     * @ORM\Column(name="IND_AUTORIZADO", type="integer", nullable=false)
     */
    private $indAutorizado;

    /**
     * @var string
     *
     * @ORM\Column(name="DOCUMENTO", type="string", length=20, nullable=true)
     */
    private $documento;

    /**
     * @var string
     *
     * @ORM\Column(name="NOSSO_NUMERO", type="string", length=20, nullable=true)
     */
    private $nossoNumero;

    /**
     * @var integer
     *
     * @ORM\Column(name="COD_GRUPO_CONTA", type="bigint", nullable=false)
     */
    private $codGrupoConta;

    /**
     * @var integer
     *
     * @ORM\Column(name="COD_GRUPO_LANC", type="bigint", nullable=false)
     */
    private $codGrupoLanc;

    /**
     * @var string
     *
     * @ORM\Column(name="OBSERVACAO", type="string", length=400, nullable=true)
     */
    private $observacao;

    /**
     * @var integer
     *
     * @ORM\Column(name="PARCELA_INICIAL", type="integer", nullable=false)
     */
    private $parcelaInicial;

    /**
     * @var integer
     *
     * @ORM\Column(name="INTERVALO_RECORRENCIA", type="integer", nullable=true)
     */
    private $intervaloRecorrencia;

    /**
     * @var integer
     *
     * @ORM\Column(name="IND_PAGAR_AUTO", type="integer", nullable=true)
     */
    private $indPagarAuto;

    /**
     * @var integer
     *
     * @ORM\Column(name="SEQUENCIAL_NOSSO_NUMERO", type="integer", nullable=true)
     */
    private $sequencialNossoNumero;

    /**
     * @var integer
     *
     * @ORM\Column(name="COD_GRUPO_SUBSTITUICAO", type="integer", nullable=true)
     */
    private $codGrupoSubstituicao;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="DATA_SUBSTITUICAO", type="datetime", nullable=true)
     */
    private $dataSubstituicao;

    /**
     * @var integer
     *
     * @ORM\Column(name="COD_TRANSACAO", type="integer", nullable=true)
     */
    private $codTransacao;

    /**
     * @var string
     *
     * @ORM\Column(name="COD_GRUPO_ASSOCIACAO", type="string", length=20, nullable=true)
     */
    private $codGrupoAssociacao;

    /**
     * @var \Entidades\ZgadmOrganizacao
     *
     * @ORM\ManyToOne(targetEntity="Entidades\ZgadmOrganizacao")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="COD_ORGANIZACAO", referencedColumnName="CODIGO")
     * })
     */
    private $codOrganizacao;

    /**
     * @var \Entidades\ZgfinPessoa
     *
     * @ORM\ManyToOne(targetEntity="Entidades\ZgfinPessoa")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="COD_PESSOA", referencedColumnName="CODIGO")
     * })
     */
    private $codPessoa;

    /**
     * @var \Entidades\ZgfinContaStatusTipo
     *
     * @ORM\ManyToOne(targetEntity="Entidades\ZgfinContaStatusTipo")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="COD_STATUS", referencedColumnName="CODIGO")
     * })
     */
    private $codStatus;

    /**
     * @var \Entidades\ZgfinMoeda
     *
     * @ORM\ManyToOne(targetEntity="Entidades\ZgfinMoeda")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="COD_MOEDA", referencedColumnName="CODIGO")
     * })
     */
    private $codMoeda;

    /**
     * @var \Entidades\ZgfinFormaPagamento
     *
     * @ORM\ManyToOne(targetEntity="Entidades\ZgfinFormaPagamento")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="COD_FORMA_PAGAMENTO", referencedColumnName="CODIGO")
     * })
     */
    private $codFormaPagamento;

    /**
     * @var \Entidades\ZgfinContaRecorrenciaTipo
     *
     * @ORM\ManyToOne(targetEntity="Entidades\ZgfinContaRecorrenciaTipo")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="COD_TIPO_RECORRENCIA", referencedColumnName="CODIGO")
     * })
     */
    private $codTipoRecorrencia;

    /**
     * @var \Entidades\ZgfinContaRecorrenciaPeriodo
     *
     * @ORM\ManyToOne(targetEntity="Entidades\ZgfinContaRecorrenciaPeriodo")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="COD_PERIODO_RECORRENCIA", referencedColumnName="CODIGO")
     * })
     */
    private $codPeriodoRecorrencia;

    /**
     * @var \Entidades\ZgfinConta
     *
     * @ORM\ManyToOne(targetEntity="Entidades\ZgfinConta")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="COD_CONTA", referencedColumnName="CODIGO")
     * })
     */
    private $codConta;


    /**
     * Get codigo
     *
     * @return integer 
     */
    public function getCodigo()
    {
        return $this->codigo;
    }

    /**
     * Set numero
     *
     * @param string $numero
     * @return ZgfinContaPagar
     */
    public function setNumero($numero)
    {
        $this->numero = $numero;

        return $this;
    }

    /**
     * Get numero
     *
     * @return string 
     */
    public function getNumero()
    {
        return $this->numero;
    }

    /**
     * Set descricao
     *
     * @param string $descricao
     * @return ZgfinContaPagar
     */
    public function setDescricao($descricao)
    {
        $this->descricao = $descricao;

        return $this;
    }

    /**
     * Get descricao
     *
     * @return string 
     */
    public function getDescricao()
    {
        return $this->descricao;
    }

    /**
     * Set parcela
     *
     * @param integer $parcela
     * @return ZgfinContaPagar
     */
    public function setParcela($parcela)
    {
        $this->parcela = $parcela;

        return $this;
    }

    /**
     * Get parcela
     *
     * @return integer 
     */
    public function getParcela()
    {
        return $this->parcela;
    }

    /**
     * Set numParcelas
     *
     * @param integer $numParcelas
     * @return ZgfinContaPagar
     */
    public function setNumParcelas($numParcelas)
    {
        $this->numParcelas = $numParcelas;

        return $this;
    }

    /**
     * Get numParcelas
     *
     * @return integer 
     */
    public function getNumParcelas()
    {
        return $this->numParcelas;
    }

    /**
     * Set valor
     *
     * @param float $valor
     * @return ZgfinContaPagar
     */
    public function setValor($valor)
    {
        $this->valor = $valor;

        return $this;
    }

    /**
     * Get valor
     *
     * @return float 
     */
    public function getValor()
    {
        return $this->valor;
    }

    /**
     * Set valorJuros
     *
     * @param float $valorJuros
     * @return ZgfinContaPagar
     */
    public function setValorJuros($valorJuros)
    {
        $this->valorJuros = $valorJuros;

        return $this;
    }

    /**
     * Get valorJuros
     *
     * @return float 
     */
    public function getValorJuros()
    {
        return $this->valorJuros;
    }

    /**
     * Set valorMora
     *
     * @param float $valorMora
     * @return ZgfinContaPagar
     */
    public function setValorMora($valorMora)
    {
        $this->valorMora = $valorMora;

        return $this;
    }

    /**
     * Get valorMora
     *
     * @return float 
     */
    public function getValorMora()
    {
        return $this->valorMora;
    }

    /**
     * Set valorDesconto
     *
     * @param float $valorDesconto
     * @return ZgfinContaPagar
     */
    public function setValorDesconto($valorDesconto)
    {
        $this->valorDesconto = $valorDesconto;

        return $this;
    }

    /**
     * Get valorDesconto
     *
     * @return float 
     */
    public function getValorDesconto()
    {
        return $this->valorDesconto;
    }

    /**
     * Set valorCancelado
     *
     * @param float $valorCancelado
     * @return ZgfinContaPagar
     */
    public function setValorCancelado($valorCancelado)
    {
        $this->valorCancelado = $valorCancelado;

        return $this;
    }

    /**
     * Get valorCancelado
     *
     * @return float 
     */
    public function getValorCancelado()
    {
        return $this->valorCancelado;
    }

    /**
     * Set valorOutros
     *
     * @param float $valorOutros
     * @return ZgfinContaPagar
     */
    public function setValorOutros($valorOutros)
    {
        $this->valorOutros = $valorOutros;

        return $this;
    }

    /**
     * Get valorOutros
     *
     * @return float 
     */
    public function getValorOutros()
    {
        return $this->valorOutros;
    }

    /**
     * Set dataEmissao
     *
     * @param \DateTime $dataEmissao
     * @return ZgfinContaPagar
     */
    public function setDataEmissao($dataEmissao)
    {
        $this->dataEmissao = $dataEmissao;

        return $this;
    }

    /**
     * Get dataEmissao
     *
     * @return \DateTime 
     */
    public function getDataEmissao()
    {
        return $this->dataEmissao;
    }

    /**
     * Set dataVencimento
     *
     * @param \DateTime $dataVencimento
     * @return ZgfinContaPagar
     */
    public function setDataVencimento($dataVencimento)
    {
        $this->dataVencimento = $dataVencimento;

        return $this;
    }

    /**
     * Get dataVencimento
     *
     * @return \DateTime 
     */
    public function getDataVencimento()
    {
        return $this->dataVencimento;
    }

    /**
     * Set dataLiquidacao
     *
     * @param \DateTime $dataLiquidacao
     * @return ZgfinContaPagar
     */
    public function setDataLiquidacao($dataLiquidacao)
    {
        $this->dataLiquidacao = $dataLiquidacao;

        return $this;
    }

    /**
     * Get dataLiquidacao
     *
     * @return \DateTime 
     */
    public function getDataLiquidacao()
    {
        return $this->dataLiquidacao;
    }

    /**
     * Set dataAutorizacao
     *
     * @param \DateTime $dataAutorizacao
     * @return ZgfinContaPagar
     */
    public function setDataAutorizacao($dataAutorizacao)
    {
        $this->dataAutorizacao = $dataAutorizacao;

        return $this;
    }

    /**
     * Get dataAutorizacao
     *
     * @return \DateTime 
     */
    public function getDataAutorizacao()
    {
        return $this->dataAutorizacao;
    }

    /**
     * Set dataCancelamento
     *
     * @param \DateTime $dataCancelamento
     * @return ZgfinContaPagar
     */
    public function setDataCancelamento($dataCancelamento)
    {
        $this->dataCancelamento = $dataCancelamento;

        return $this;
    }

    /**
     * Get dataCancelamento
     *
     * @return \DateTime 
     */
    public function getDataCancelamento()
    {
        return $this->dataCancelamento;
    }

    /**
     * Set indAutorizado
     *
     * @param integer $indAutorizado
     * @return ZgfinContaPagar
     */
    public function setIndAutorizado($indAutorizado)
    {
        $this->indAutorizado = $indAutorizado;

        return $this;
    }

    /**
     * Get indAutorizado
     *
     * @return integer 
     */
    public function getIndAutorizado()
    {
        return $this->indAutorizado;
    }

    /**
     * Set documento
     *
     * @param string $documento
     * @return ZgfinContaPagar
     */
    public function setDocumento($documento)
    {
        $this->documento = $documento;

        return $this;
    }

    /**
     * Get documento
     *
     * @return string 
     */
    public function getDocumento()
    {
        return $this->documento;
    }

    /**
     * Set nossoNumero
     *
     * @param string $nossoNumero
     * @return ZgfinContaPagar
     */
    public function setNossoNumero($nossoNumero)
    {
        $this->nossoNumero = $nossoNumero;

        return $this;
    }

    /**
     * Get nossoNumero
     *
     * @return string 
     */
    public function getNossoNumero()
    {
        return $this->nossoNumero;
    }

    /**
     * Set codGrupoConta
     *
     * @param integer $codGrupoConta
     * @return ZgfinContaPagar
     */
    public function setCodGrupoConta($codGrupoConta)
    {
        $this->codGrupoConta = $codGrupoConta;

        return $this;
    }

    /**
     * Get codGrupoConta
     *
     * @return integer 
     */
    public function getCodGrupoConta()
    {
        return $this->codGrupoConta;
    }

    /**
     * Set codGrupoLanc
     *
     * @param integer $codGrupoLanc
     * @return ZgfinContaPagar
     */
    public function setCodGrupoLanc($codGrupoLanc)
    {
        $this->codGrupoLanc = $codGrupoLanc;

        return $this;
    }

    /**
     * Get codGrupoLanc
     *
     * @return integer 
     */
    public function getCodGrupoLanc()
    {
        return $this->codGrupoLanc;
    }

    /**
     * Set observacao
     *
     * @param string $observacao
     * @return ZgfinContaPagar
     */
    public function setObservacao($observacao)
    {
        $this->observacao = $observacao;

        return $this;
    }

    /**
     * Get observacao
     *
     * @return string 
     */
    public function getObservacao()
    {
        return $this->observacao;
    }

    /**
     * Set parcelaInicial
     *
     * @param integer $parcelaInicial
     * @return ZgfinContaPagar
     */
    public function setParcelaInicial($parcelaInicial)
    {
        $this->parcelaInicial = $parcelaInicial;

        return $this;
    }

    /**
     * Get parcelaInicial
     *
     * @return integer 
     */
    public function getParcelaInicial()
    {
        return $this->parcelaInicial;
    }

    /**
     * Set intervaloRecorrencia
     *
     * @param integer $intervaloRecorrencia
     * @return ZgfinContaPagar
     */
    public function setIntervaloRecorrencia($intervaloRecorrencia)
    {
        $this->intervaloRecorrencia = $intervaloRecorrencia;

        return $this;
    }

    /**
     * Get intervaloRecorrencia
     *
     * @return integer 
     */
    public function getIntervaloRecorrencia()
    {
        return $this->intervaloRecorrencia;
    }

    /**
     * Set indPagarAuto
     *
     * @param integer $indPagarAuto
     * @return ZgfinContaPagar
     */
    public function setIndPagarAuto($indPagarAuto)
    {
        $this->indPagarAuto = $indPagarAuto;

        return $this;
    }

    /**
     * Get indPagarAuto
     *
     * @return integer 
     */
    public function getIndPagarAuto()
    {
        return $this->indPagarAuto;
    }

    /**
     * Set sequencialNossoNumero
     *
     * @param integer $sequencialNossoNumero
     * @return ZgfinContaPagar
     */
    public function setSequencialNossoNumero($sequencialNossoNumero)
    {
        $this->sequencialNossoNumero = $sequencialNossoNumero;

        return $this;
    }

    /**
     * Get sequencialNossoNumero
     *
     * @return integer 
     */
    public function getSequencialNossoNumero()
    {
        return $this->sequencialNossoNumero;
    }

    /**
     * Set codGrupoSubstituicao
     *
     * @param integer $codGrupoSubstituicao
     * @return ZgfinContaPagar
     */
    public function setCodGrupoSubstituicao($codGrupoSubstituicao)
    {
        $this->codGrupoSubstituicao = $codGrupoSubstituicao;

        return $this;
    }

    /**
     * Get codGrupoSubstituicao
     *
     * @return integer 
     */
    public function getCodGrupoSubstituicao()
    {
        return $this->codGrupoSubstituicao;
    }

    /**
     * Set dataSubstituicao
     *
     * @param \DateTime $dataSubstituicao
     * @return ZgfinContaPagar
     */
    public function setDataSubstituicao($dataSubstituicao)
    {
        $this->dataSubstituicao = $dataSubstituicao;

        return $this;
    }

    /**
     * Get dataSubstituicao
     *
     * @return \DateTime 
     */
    public function getDataSubstituicao()
    {
        return $this->dataSubstituicao;
    }

    /**
     * Set codTransacao
     *
     * @param integer $codTransacao
     * @return ZgfinContaPagar
     */
    public function setCodTransacao($codTransacao)
    {
        $this->codTransacao = $codTransacao;

        return $this;
    }

    /**
     * Get codTransacao
     *
     * @return integer 
     */
    public function getCodTransacao()
    {
        return $this->codTransacao;
    }

    /**
     * Set codGrupoAssociacao
     *
     * @param string $codGrupoAssociacao
     * @return ZgfinContaPagar
     */
    public function setCodGrupoAssociacao($codGrupoAssociacao)
    {
        $this->codGrupoAssociacao = $codGrupoAssociacao;

        return $this;
    }

    /**
     * Get codGrupoAssociacao
     *
     * @return string 
     */
    public function getCodGrupoAssociacao()
    {
        return $this->codGrupoAssociacao;
    }

    /**
     * Set codOrganizacao
     *
     * @param \Entidades\ZgadmOrganizacao $codOrganizacao
     * @return ZgfinContaPagar
     */
    public function setCodOrganizacao(\Entidades\ZgadmOrganizacao $codOrganizacao = null)
    {
        $this->codOrganizacao = $codOrganizacao;

        return $this;
    }

    /**
     * Get codOrganizacao
     *
     * @return \Entidades\ZgadmOrganizacao 
     */
    public function getCodOrganizacao()
    {
        return $this->codOrganizacao;
    }

    /**
     * Set codPessoa
     *
     * @param \Entidades\ZgfinPessoa $codPessoa
     * @return ZgfinContaPagar
     */
    public function setCodPessoa(\Entidades\ZgfinPessoa $codPessoa = null)
    {
        $this->codPessoa = $codPessoa;

        return $this;
    }

    /**
     * Get codPessoa
     *
     * @return \Entidades\ZgfinPessoa 
     */
    public function getCodPessoa()
    {
        return $this->codPessoa;
    }

    /**
     * Set codStatus
     *
     * @param \Entidades\ZgfinContaStatusTipo $codStatus
     * @return ZgfinContaPagar
     */
    public function setCodStatus(\Entidades\ZgfinContaStatusTipo $codStatus = null)
    {
        $this->codStatus = $codStatus;

        return $this;
    }

    /**
     * Get codStatus
     *
     * @return \Entidades\ZgfinContaStatusTipo 
     */
    public function getCodStatus()
    {
        return $this->codStatus;
    }

    /**
     * Set codMoeda
     *
     * @param \Entidades\ZgfinMoeda $codMoeda
     * @return ZgfinContaPagar
     */
    public function setCodMoeda(\Entidades\ZgfinMoeda $codMoeda = null)
    {
        $this->codMoeda = $codMoeda;

        return $this;
    }

    /**
     * Get codMoeda
     *
     * @return \Entidades\ZgfinMoeda 
     */
    public function getCodMoeda()
    {
        return $this->codMoeda;
    }

    /**
     * Set codFormaPagamento
     *
     * @param \Entidades\ZgfinFormaPagamento $codFormaPagamento
     * @return ZgfinContaPagar
     */
    public function setCodFormaPagamento(\Entidades\ZgfinFormaPagamento $codFormaPagamento = null)
    {
        $this->codFormaPagamento = $codFormaPagamento;

        return $this;
    }

    /**
     * Get codFormaPagamento
     *
     * @return \Entidades\ZgfinFormaPagamento 
     */
    public function getCodFormaPagamento()
    {
        return $this->codFormaPagamento;
    }

    /**
     * Set codTipoRecorrencia
     *
     * @param \Entidades\ZgfinContaRecorrenciaTipo $codTipoRecorrencia
     * @return ZgfinContaPagar
     */
    public function setCodTipoRecorrencia(\Entidades\ZgfinContaRecorrenciaTipo $codTipoRecorrencia = null)
    {
        $this->codTipoRecorrencia = $codTipoRecorrencia;

        return $this;
    }

    /**
     * Get codTipoRecorrencia
     *
     * @return \Entidades\ZgfinContaRecorrenciaTipo 
     */
    public function getCodTipoRecorrencia()
    {
        return $this->codTipoRecorrencia;
    }

    /**
     * Set codPeriodoRecorrencia
     *
     * @param \Entidades\ZgfinContaRecorrenciaPeriodo $codPeriodoRecorrencia
     * @return ZgfinContaPagar
     */
    public function setCodPeriodoRecorrencia(\Entidades\ZgfinContaRecorrenciaPeriodo $codPeriodoRecorrencia = null)
    {
        $this->codPeriodoRecorrencia = $codPeriodoRecorrencia;

        return $this;
    }

    /**
     * Get codPeriodoRecorrencia
     *
     * @return \Entidades\ZgfinContaRecorrenciaPeriodo 
     */
    public function getCodPeriodoRecorrencia()
    {
        return $this->codPeriodoRecorrencia;
    }

    /**
     * Set codConta
     *
     * @param \Entidades\ZgfinConta $codConta
     * @return ZgfinContaPagar
     */
    public function setCodConta(\Entidades\ZgfinConta $codConta = null)
    {
        $this->codConta = $codConta;

        return $this;
    }

    /**
     * Get codConta
     *
     * @return \Entidades\ZgfinConta 
     */
    public function getCodConta()
    {
        return $this->codConta;
    }
}
