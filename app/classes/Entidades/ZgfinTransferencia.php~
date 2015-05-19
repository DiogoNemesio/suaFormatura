<?php

namespace Entidades;

use Doctrine\ORM\Mapping as ORM;

/**
 * ZgfinTransferencia
 *
 * @ORM\Table(name="ZGFIN_TRANSFERENCIA", indexes={@ORM\Index(name="fk_ZGFIN_TRANSFERENCIA_1_idx", columns={"COD_ORGANIZACAO"}), @ORM\Index(name="fk_ZGFIN_TRANSFERENCIA_2_idx", columns={"COD_STATUS"}), @ORM\Index(name="fk_ZGFIN_TRANSFERENCIA_3_idx", columns={"COD_MOEDA"}), @ORM\Index(name="fk_ZGFIN_TRANSFERENCIA_4_idx", columns={"COD_FORMA_PAGAMENTO"}), @ORM\Index(name="fk_ZGFIN_TRANSFERENCIA_5_idx", columns={"COD_TIPO_RECORRENCIA"}), @ORM\Index(name="fk_ZGFIN_TRANSFERENCIA_6_idx", columns={"COD_PERIODO_RECORRENCIA"}), @ORM\Index(name="fk_ZGFIN_TRANSFERENCIA_7_idx", columns={"COD_CONTA_ORIGEM"}), @ORM\Index(name="fk_ZGFIN_TRANSFERENCIA_8_idx", columns={"COD_CONTA_DESTINO"})})
 * @ORM\Entity
 */
class ZgfinTransferencia
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
     * @ORM\Column(name="VALOR_CANCELADO", type="float", precision=10, scale=0, nullable=false)
     */
    private $valorCancelado;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="DATA_EMISSAO", type="datetime", nullable=false)
     */
    private $dataEmissao;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="DATA_TRANSFERENCIA", type="date", nullable=false)
     */
    private $dataTransferencia;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="DATA_AUTORIZACAO", type="datetime", nullable=true)
     */
    private $dataAutorizacao;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="DATA_REALIZACAO", type="datetime", nullable=true)
     */
    private $dataRealizacao;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="DATA_CANCELAMENTO", type="date", nullable=true)
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
     * @var integer
     *
     * @ORM\Column(name="COD_GRUPO_TRANSFERENCIA", type="bigint", nullable=false)
     */
    private $codGrupoTransferencia;

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
     * @ORM\Column(name="IND_TRANSFERIR_AUTO", type="integer", nullable=false)
     */
    private $indTransferirAuto;

    /**
     * @var \Entidades\ZgfmtOrganizacao
     *
     * @ORM\ManyToOne(targetEntity="Entidades\ZgfmtOrganizacao")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="COD_ORGANIZACAO", referencedColumnName="CODIGO")
     * })
     */
    private $codOrganizacao;

    /**
     * @var \Entidades\ZgfinTransferenciaStatusTipo
     *
     * @ORM\ManyToOne(targetEntity="Entidades\ZgfinTransferenciaStatusTipo")
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
     *   @ORM\JoinColumn(name="COD_CONTA_ORIGEM", referencedColumnName="CODIGO")
     * })
     */
    private $codContaOrigem;

    /**
     * @var \Entidades\ZgfinConta
     *
     * @ORM\ManyToOne(targetEntity="Entidades\ZgfinConta")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="COD_CONTA_DESTINO", referencedColumnName="CODIGO")
     * })
     */
    private $codContaDestino;


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
     * @return ZgfinTransferencia
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
     * @return ZgfinTransferencia
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
     * @return ZgfinTransferencia
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
     * @return ZgfinTransferencia
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
     * @return ZgfinTransferencia
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
     * Set valorCancelado
     *
     * @param float $valorCancelado
     * @return ZgfinTransferencia
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
     * Set dataEmissao
     *
     * @param \DateTime $dataEmissao
     * @return ZgfinTransferencia
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
     * Set dataTransferencia
     *
     * @param \DateTime $dataTransferencia
     * @return ZgfinTransferencia
     */
    public function setDataTransferencia($dataTransferencia)
    {
        $this->dataTransferencia = $dataTransferencia;

        return $this;
    }

    /**
     * Get dataTransferencia
     *
     * @return \DateTime 
     */
    public function getDataTransferencia()
    {
        return $this->dataTransferencia;
    }

    /**
     * Set dataAutorizacao
     *
     * @param \DateTime $dataAutorizacao
     * @return ZgfinTransferencia
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
     * Set dataRealizacao
     *
     * @param \DateTime $dataRealizacao
     * @return ZgfinTransferencia
     */
    public function setDataRealizacao($dataRealizacao)
    {
        $this->dataRealizacao = $dataRealizacao;

        return $this;
    }

    /**
     * Get dataRealizacao
     *
     * @return \DateTime 
     */
    public function getDataRealizacao()
    {
        return $this->dataRealizacao;
    }

    /**
     * Set dataCancelamento
     *
     * @param \DateTime $dataCancelamento
     * @return ZgfinTransferencia
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
     * @return ZgfinTransferencia
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
     * @return ZgfinTransferencia
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
     * Set codGrupoTransferencia
     *
     * @param integer $codGrupoTransferencia
     * @return ZgfinTransferencia
     */
    public function setCodGrupoTransferencia($codGrupoTransferencia)
    {
        $this->codGrupoTransferencia = $codGrupoTransferencia;

        return $this;
    }

    /**
     * Get codGrupoTransferencia
     *
     * @return integer 
     */
    public function getCodGrupoTransferencia()
    {
        return $this->codGrupoTransferencia;
    }

    /**
     * Set codGrupoLanc
     *
     * @param integer $codGrupoLanc
     * @return ZgfinTransferencia
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
     * @return ZgfinTransferencia
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
     * @return ZgfinTransferencia
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
     * @return ZgfinTransferencia
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
     * Set indTransferirAuto
     *
     * @param integer $indTransferirAuto
     * @return ZgfinTransferencia
     */
    public function setIndTransferirAuto($indTransferirAuto)
    {
        $this->indTransferirAuto = $indTransferirAuto;

        return $this;
    }

    /**
     * Get indTransferirAuto
     *
     * @return integer 
     */
    public function getIndTransferirAuto()
    {
        return $this->indTransferirAuto;
    }

    /**
     * Set codOrganizacao
     *
     * @param \Entidades\ZgfmtOrganizacao $codOrganizacao
     * @return ZgfinTransferencia
     */
    public function setCodOrganizacao(\Entidades\ZgfmtOrganizacao $codOrganizacao = null)
    {
        $this->codOrganizacao = $codOrganizacao;

        return $this;
    }

    /**
     * Get codOrganizacao
     *
     * @return \Entidades\ZgfmtOrganizacao 
     */
    public function getCodOrganizacao()
    {
        return $this->codOrganizacao;
    }

    /**
     * Set codStatus
     *
     * @param \Entidades\ZgfinTransferenciaStatusTipo $codStatus
     * @return ZgfinTransferencia
     */
    public function setCodStatus(\Entidades\ZgfinTransferenciaStatusTipo $codStatus = null)
    {
        $this->codStatus = $codStatus;

        return $this;
    }

    /**
     * Get codStatus
     *
     * @return \Entidades\ZgfinTransferenciaStatusTipo 
     */
    public function getCodStatus()
    {
        return $this->codStatus;
    }

    /**
     * Set codMoeda
     *
     * @param \Entidades\ZgfinMoeda $codMoeda
     * @return ZgfinTransferencia
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
     * @return ZgfinTransferencia
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
     * @return ZgfinTransferencia
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
     * @return ZgfinTransferencia
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
     * Set codContaOrigem
     *
     * @param \Entidades\ZgfinConta $codContaOrigem
     * @return ZgfinTransferencia
     */
    public function setCodContaOrigem(\Entidades\ZgfinConta $codContaOrigem = null)
    {
        $this->codContaOrigem = $codContaOrigem;

        return $this;
    }

    /**
     * Get codContaOrigem
     *
     * @return \Entidades\ZgfinConta 
     */
    public function getCodContaOrigem()
    {
        return $this->codContaOrigem;
    }

    /**
     * Set codContaDestino
     *
     * @param \Entidades\ZgfinConta $codContaDestino
     * @return ZgfinTransferencia
     */
    public function setCodContaDestino(\Entidades\ZgfinConta $codContaDestino = null)
    {
        $this->codContaDestino = $codContaDestino;

        return $this;
    }

    /**
     * Get codContaDestino
     *
     * @return \Entidades\ZgfinConta 
     */
    public function getCodContaDestino()
    {
        return $this->codContaDestino;
    }
}
