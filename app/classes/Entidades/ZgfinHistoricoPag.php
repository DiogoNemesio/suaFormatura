<?php

namespace Entidades;

use Doctrine\ORM\Mapping as ORM;

/**
 * ZgfinHistoricoPag
 *
 * @ORM\Table(name="ZGFIN_HISTORICO_PAG", indexes={@ORM\Index(name="fk_ZGFIN_HISTORICO_PAG_1_idx", columns={"COD_CONTA_PAG"}), @ORM\Index(name="fk_ZGFIN_HISTORICO_PAG_2_idx", columns={"COD_MOEDA"}), @ORM\Index(name="fk_ZGFIN_HISTORICO_PAG_3_idx", columns={"COD_CONTA"}), @ORM\Index(name="fk_ZGFIN_HISTORICO_PAG_4_idx", columns={"COD_FORMA_PAGAMENTO"}), @ORM\Index(name="fk_ZGFIN_HISTORICO_PAG_5_idx", columns={"COD_BANCO_FORNECEDOR"}), @ORM\Index(name="fk_ZGFIN_HISTORICO_PAG_6_idx", columns={"COD_TIPO_BAIXA"}), @ORM\Index(name="ZGFIN_HISTORICO_PAG_IX01", columns={"COD_CONTA_PAG", "SEQ_RETORNO_BANCARIO", "DOCUMENTO"})})
 * @ORM\Entity
 */
class ZgfinHistoricoPag
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
     * @var integer
     *
     * @ORM\Column(name="COD_GRUPO_LANC", type="bigint", nullable=false)
     */
    private $codGrupoLanc;

    /**
     * @var integer
     *
     * @ORM\Column(name="COD_GRUPO_MOV", type="bigint", nullable=false)
     */
    private $codGrupoMov;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="DATA_PAGAMENTO", type="date", nullable=false)
     */
    private $dataPagamento;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="DATA_TRANSACAO", type="datetime", nullable=false)
     */
    private $dataTransacao;

    /**
     * @var float
     *
     * @ORM\Column(name="VALOR_PAGO", type="float", precision=10, scale=0, nullable=false)
     */
    private $valorPago;

    /**
     * @var float
     *
     * @ORM\Column(name="VALOR_DESCONTO", type="float", precision=10, scale=0, nullable=false)
     */
    private $valorDesconto;

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
     * @ORM\Column(name="VALOR_OUTROS", type="float", precision=10, scale=0, nullable=false)
     */
    private $valorOutros;

    /**
     * @var string
     *
     * @ORM\Column(name="DOCUMENTO", type="string", length=14, nullable=true)
     */
    private $documento;

    /**
     * @var string
     *
     * @ORM\Column(name="AGENCIA_FORNECEDOR", type="string", length=8, nullable=true)
     */
    private $agenciaFornecedor;

    /**
     * @var string
     *
     * @ORM\Column(name="CCORRENTE_FORNECEDOR", type="string", length=20, nullable=true)
     */
    private $ccorrenteFornecedor;

    /**
     * @var integer
     *
     * @ORM\Column(name="SEQ_RETORNO_BANCARIO", type="integer", nullable=true)
     */
    private $seqRetornoBancario;

    /**
     * @var \Entidades\ZgfinContaPagar
     *
     * @ORM\ManyToOne(targetEntity="Entidades\ZgfinContaPagar")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="COD_CONTA_PAG", referencedColumnName="CODIGO")
     * })
     */
    private $codContaPag;

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
     * @var \Entidades\ZgfinConta
     *
     * @ORM\ManyToOne(targetEntity="Entidades\ZgfinConta")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="COD_CONTA", referencedColumnName="CODIGO")
     * })
     */
    private $codConta;

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
     * @var \Entidades\ZgfinBanco
     *
     * @ORM\ManyToOne(targetEntity="Entidades\ZgfinBanco")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="COD_BANCO_FORNECEDOR", referencedColumnName="CODIGO")
     * })
     */
    private $codBancoFornecedor;

    /**
     * @var \Entidades\ZgfinBaixaTipo
     *
     * @ORM\ManyToOne(targetEntity="Entidades\ZgfinBaixaTipo")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="COD_TIPO_BAIXA", referencedColumnName="CODIGO")
     * })
     */
    private $codTipoBaixa;


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
     * Set codGrupoLanc
     *
     * @param integer $codGrupoLanc
     * @return ZgfinHistoricoPag
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
     * Set codGrupoMov
     *
     * @param integer $codGrupoMov
     * @return ZgfinHistoricoPag
     */
    public function setCodGrupoMov($codGrupoMov)
    {
        $this->codGrupoMov = $codGrupoMov;

        return $this;
    }

    /**
     * Get codGrupoMov
     *
     * @return integer 
     */
    public function getCodGrupoMov()
    {
        return $this->codGrupoMov;
    }

    /**
     * Set dataPagamento
     *
     * @param \DateTime $dataPagamento
     * @return ZgfinHistoricoPag
     */
    public function setDataPagamento($dataPagamento)
    {
        $this->dataPagamento = $dataPagamento;

        return $this;
    }

    /**
     * Get dataPagamento
     *
     * @return \DateTime 
     */
    public function getDataPagamento()
    {
        return $this->dataPagamento;
    }

    /**
     * Set dataTransacao
     *
     * @param \DateTime $dataTransacao
     * @return ZgfinHistoricoPag
     */
    public function setDataTransacao($dataTransacao)
    {
        $this->dataTransacao = $dataTransacao;

        return $this;
    }

    /**
     * Get dataTransacao
     *
     * @return \DateTime 
     */
    public function getDataTransacao()
    {
        return $this->dataTransacao;
    }

    /**
     * Set valorPago
     *
     * @param float $valorPago
     * @return ZgfinHistoricoPag
     */
    public function setValorPago($valorPago)
    {
        $this->valorPago = $valorPago;

        return $this;
    }

    /**
     * Get valorPago
     *
     * @return float 
     */
    public function getValorPago()
    {
        return $this->valorPago;
    }

    /**
     * Set valorDesconto
     *
     * @param float $valorDesconto
     * @return ZgfinHistoricoPag
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
     * Set valorJuros
     *
     * @param float $valorJuros
     * @return ZgfinHistoricoPag
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
     * @return ZgfinHistoricoPag
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
     * Set valorOutros
     *
     * @param float $valorOutros
     * @return ZgfinHistoricoPag
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
     * Set documento
     *
     * @param string $documento
     * @return ZgfinHistoricoPag
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
     * Set agenciaFornecedor
     *
     * @param string $agenciaFornecedor
     * @return ZgfinHistoricoPag
     */
    public function setAgenciaFornecedor($agenciaFornecedor)
    {
        $this->agenciaFornecedor = $agenciaFornecedor;

        return $this;
    }

    /**
     * Get agenciaFornecedor
     *
     * @return string 
     */
    public function getAgenciaFornecedor()
    {
        return $this->agenciaFornecedor;
    }

    /**
     * Set ccorrenteFornecedor
     *
     * @param string $ccorrenteFornecedor
     * @return ZgfinHistoricoPag
     */
    public function setCcorrenteFornecedor($ccorrenteFornecedor)
    {
        $this->ccorrenteFornecedor = $ccorrenteFornecedor;

        return $this;
    }

    /**
     * Get ccorrenteFornecedor
     *
     * @return string 
     */
    public function getCcorrenteFornecedor()
    {
        return $this->ccorrenteFornecedor;
    }

    /**
     * Set seqRetornoBancario
     *
     * @param integer $seqRetornoBancario
     * @return ZgfinHistoricoPag
     */
    public function setSeqRetornoBancario($seqRetornoBancario)
    {
        $this->seqRetornoBancario = $seqRetornoBancario;

        return $this;
    }

    /**
     * Get seqRetornoBancario
     *
     * @return integer 
     */
    public function getSeqRetornoBancario()
    {
        return $this->seqRetornoBancario;
    }

    /**
     * Set codContaPag
     *
     * @param \Entidades\ZgfinContaPagar $codContaPag
     * @return ZgfinHistoricoPag
     */
    public function setCodContaPag(\Entidades\ZgfinContaPagar $codContaPag = null)
    {
        $this->codContaPag = $codContaPag;

        return $this;
    }

    /**
     * Get codContaPag
     *
     * @return \Entidades\ZgfinContaPagar 
     */
    public function getCodContaPag()
    {
        return $this->codContaPag;
    }

    /**
     * Set codMoeda
     *
     * @param \Entidades\ZgfinMoeda $codMoeda
     * @return ZgfinHistoricoPag
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
     * Set codConta
     *
     * @param \Entidades\ZgfinConta $codConta
     * @return ZgfinHistoricoPag
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

    /**
     * Set codFormaPagamento
     *
     * @param \Entidades\ZgfinFormaPagamento $codFormaPagamento
     * @return ZgfinHistoricoPag
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
     * Set codBancoFornecedor
     *
     * @param \Entidades\ZgfinBanco $codBancoFornecedor
     * @return ZgfinHistoricoPag
     */
    public function setCodBancoFornecedor(\Entidades\ZgfinBanco $codBancoFornecedor = null)
    {
        $this->codBancoFornecedor = $codBancoFornecedor;

        return $this;
    }

    /**
     * Get codBancoFornecedor
     *
     * @return \Entidades\ZgfinBanco 
     */
    public function getCodBancoFornecedor()
    {
        return $this->codBancoFornecedor;
    }

    /**
     * Set codTipoBaixa
     *
     * @param \Entidades\ZgfinBaixaTipo $codTipoBaixa
     * @return ZgfinHistoricoPag
     */
    public function setCodTipoBaixa(\Entidades\ZgfinBaixaTipo $codTipoBaixa = null)
    {
        $this->codTipoBaixa = $codTipoBaixa;

        return $this;
    }

    /**
     * Get codTipoBaixa
     *
     * @return \Entidades\ZgfinBaixaTipo 
     */
    public function getCodTipoBaixa()
    {
        return $this->codTipoBaixa;
    }
}
