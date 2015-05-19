<?php

namespace Entidades;

use Doctrine\ORM\Mapping as ORM;

/**
 * ZgfinHistoricoTransf
 *
 * @ORM\Table(name="ZGFIN_HISTORICO_TRANSF", indexes={@ORM\Index(name="fk_ZGFIN_HISTORICO_TRANSF_1_idx", columns={"COD_TRANSFERENCIA"}), @ORM\Index(name="fk_ZGFIN_HISTORICO_TRANSF_3_idx", columns={"COD_CONTA_ORIGEM"}), @ORM\Index(name="fk_ZGFIN_HISTORICO_TRANSF_4_idx", columns={"COD_CONTA_DESTINO"}), @ORM\Index(name="fk_ZGFIN_HISTORICO_TRANSF_5_idx", columns={"COD_FORMA_PAGAMENTO"}), @ORM\Index(name="fk_ZGFIN_HISTORICO_TRANSF_6_idx", columns={"COD_USUARIO"}), @ORM\Index(name="fk_ZGFIN_HISTORICO_TRANSF_2_idx", columns={"COD_MOEDA"})})
 * @ORM\Entity
 */
class ZgfinHistoricoTransf
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
     * @ORM\Column(name="DATA_TRANSACAO", type="datetime", nullable=false)
     */
    private $dataTransacao;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="DATA_TRANSFERENCIA", type="date", nullable=false)
     */
    private $dataTransferencia;

    /**
     * @var float
     *
     * @ORM\Column(name="VALOR", type="float", precision=10, scale=0, nullable=false)
     */
    private $valor;

    /**
     * @var string
     *
     * @ORM\Column(name="DOCUMENTO", type="string", length=14, nullable=true)
     */
    private $documento;

    /**
     * @var \Entidades\ZgfinTransferencia
     *
     * @ORM\ManyToOne(targetEntity="Entidades\ZgfinTransferencia")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="COD_TRANSFERENCIA", referencedColumnName="CODIGO")
     * })
     */
    private $codTransferencia;

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
     * @var \Entidades\ZgfinFormaPagamento
     *
     * @ORM\ManyToOne(targetEntity="Entidades\ZgfinFormaPagamento")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="COD_FORMA_PAGAMENTO", referencedColumnName="CODIGO")
     * })
     */
    private $codFormaPagamento;

    /**
     * @var \Entidades\ZgsegUsuario
     *
     * @ORM\ManyToOne(targetEntity="Entidades\ZgsegUsuario")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="COD_USUARIO", referencedColumnName="CODIGO")
     * })
     */
    private $codUsuario;


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
     * @return ZgfinHistoricoTransf
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
     * @return ZgfinHistoricoTransf
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
     * Set dataTransacao
     *
     * @param \DateTime $dataTransacao
     * @return ZgfinHistoricoTransf
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
     * Set dataTransferencia
     *
     * @param \DateTime $dataTransferencia
     * @return ZgfinHistoricoTransf
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
     * Set valor
     *
     * @param float $valor
     * @return ZgfinHistoricoTransf
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
     * Set documento
     *
     * @param string $documento
     * @return ZgfinHistoricoTransf
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
     * Set codTransferencia
     *
     * @param \Entidades\ZgfinTransferencia $codTransferencia
     * @return ZgfinHistoricoTransf
     */
    public function setCodTransferencia(\Entidades\ZgfinTransferencia $codTransferencia = null)
    {
        $this->codTransferencia = $codTransferencia;

        return $this;
    }

    /**
     * Get codTransferencia
     *
     * @return \Entidades\ZgfinTransferencia 
     */
    public function getCodTransferencia()
    {
        return $this->codTransferencia;
    }

    /**
     * Set codMoeda
     *
     * @param \Entidades\ZgfinMoeda $codMoeda
     * @return ZgfinHistoricoTransf
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
     * Set codContaOrigem
     *
     * @param \Entidades\ZgfinConta $codContaOrigem
     * @return ZgfinHistoricoTransf
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
     * @return ZgfinHistoricoTransf
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

    /**
     * Set codFormaPagamento
     *
     * @param \Entidades\ZgfinFormaPagamento $codFormaPagamento
     * @return ZgfinHistoricoTransf
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
     * Set codUsuario
     *
     * @param \Entidades\ZgsegUsuario $codUsuario
     * @return ZgfinHistoricoTransf
     */
    public function setCodUsuario(\Entidades\ZgsegUsuario $codUsuario = null)
    {
        $this->codUsuario = $codUsuario;

        return $this;
    }

    /**
     * Get codUsuario
     *
     * @return \Entidades\ZgsegUsuario 
     */
    public function getCodUsuario()
    {
        return $this->codUsuario;
    }
}
