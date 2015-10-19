<?php

namespace Entidades;

use Doctrine\ORM\Mapping as ORM;

/**
 * ZgfinMovAdiantamento
 *
 * @ORM\Table(name="ZGFIN_MOV_ADIANTAMENTO", indexes={@ORM\Index(name="fk_ZGFIN_MOV_ADIANTAMENTO_1_idx", columns={"COD_ORGANIZACAO"}), @ORM\Index(name="fk_ZGFIN_MOV_ADIANTAMENTO_2_idx", columns={"COD_ORIGEM"}), @ORM\Index(name="fk_ZGFIN_MOV_ADIANTAMENTO_3_idx", columns={"COD_TIPO_OPERACAO"}), @ORM\Index(name="fk_ZGFIN_MOV_ADIANTAMENTO_4_idx", columns={"COD_PESSOA"}), @ORM\Index(name="fk_ZGFIN_MOV_ADIANTAMENTO_5_idx", columns={"COD_CONTA_REC"}), @ORM\Index(name="fk_ZGFIN_MOV_ADIANTAMENTO_6_idx", columns={"COD_CONTA_PAG"})})
 * @ORM\Entity
 */
class ZgfinMovAdiantamento
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
     * @var \DateTime
     *
     * @ORM\Column(name="DATA_ADIANTAMENTO", type="date", nullable=false)
     */
    private $dataAdiantamento;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="DATA_TRANSACAO", type="datetime", nullable=false)
     */
    private $dataTransacao;

    /**
     * @var float
     *
     * @ORM\Column(name="VALOR", type="float", precision=10, scale=0, nullable=false)
     */
    private $valor;

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
     * @var \Entidades\ZgadmOrigem
     *
     * @ORM\ManyToOne(targetEntity="Entidades\ZgadmOrigem")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="COD_ORIGEM", referencedColumnName="CODIGO")
     * })
     */
    private $codOrigem;

    /**
     * @var \Entidades\ZgfinOperacaoTipo
     *
     * @ORM\ManyToOne(targetEntity="Entidades\ZgfinOperacaoTipo")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="COD_TIPO_OPERACAO", referencedColumnName="CODIGO")
     * })
     */
    private $codTipoOperacao;

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
     * @var \Entidades\ZgfinContaReceber
     *
     * @ORM\ManyToOne(targetEntity="Entidades\ZgfinContaReceber")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="COD_CONTA_REC", referencedColumnName="CODIGO")
     * })
     */
    private $codContaRec;

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
     * Get codigo
     *
     * @return integer 
     */
    public function getCodigo()
    {
        return $this->codigo;
    }

    /**
     * Set dataAdiantamento
     *
     * @param \DateTime $dataAdiantamento
     * @return ZgfinMovAdiantamento
     */
    public function setDataAdiantamento($dataAdiantamento)
    {
        $this->dataAdiantamento = $dataAdiantamento;

        return $this;
    }

    /**
     * Get dataAdiantamento
     *
     * @return \DateTime 
     */
    public function getDataAdiantamento()
    {
        return $this->dataAdiantamento;
    }

    /**
     * Set dataTransacao
     *
     * @param \DateTime $dataTransacao
     * @return ZgfinMovAdiantamento
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
     * Set valor
     *
     * @param float $valor
     * @return ZgfinMovAdiantamento
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
     * Set codOrganizacao
     *
     * @param \Entidades\ZgadmOrganizacao $codOrganizacao
     * @return ZgfinMovAdiantamento
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
     * Set codOrigem
     *
     * @param \Entidades\ZgadmOrigem $codOrigem
     * @return ZgfinMovAdiantamento
     */
    public function setCodOrigem(\Entidades\ZgadmOrigem $codOrigem = null)
    {
        $this->codOrigem = $codOrigem;

        return $this;
    }

    /**
     * Get codOrigem
     *
     * @return \Entidades\ZgadmOrigem 
     */
    public function getCodOrigem()
    {
        return $this->codOrigem;
    }

    /**
     * Set codTipoOperacao
     *
     * @param \Entidades\ZgfinOperacaoTipo $codTipoOperacao
     * @return ZgfinMovAdiantamento
     */
    public function setCodTipoOperacao(\Entidades\ZgfinOperacaoTipo $codTipoOperacao = null)
    {
        $this->codTipoOperacao = $codTipoOperacao;

        return $this;
    }

    /**
     * Get codTipoOperacao
     *
     * @return \Entidades\ZgfinOperacaoTipo 
     */
    public function getCodTipoOperacao()
    {
        return $this->codTipoOperacao;
    }

    /**
     * Set codPessoa
     *
     * @param \Entidades\ZgfinPessoa $codPessoa
     * @return ZgfinMovAdiantamento
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
     * Set codContaRec
     *
     * @param \Entidades\ZgfinContaReceber $codContaRec
     * @return ZgfinMovAdiantamento
     */
    public function setCodContaRec(\Entidades\ZgfinContaReceber $codContaRec = null)
    {
        $this->codContaRec = $codContaRec;

        return $this;
    }

    /**
     * Get codContaRec
     *
     * @return \Entidades\ZgfinContaReceber 
     */
    public function getCodContaRec()
    {
        return $this->codContaRec;
    }

    /**
     * Set codContaPag
     *
     * @param \Entidades\ZgfinContaPagar $codContaPag
     * @return ZgfinMovAdiantamento
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
}
