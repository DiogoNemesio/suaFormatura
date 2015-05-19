<?php

namespace Entidades;

use Doctrine\ORM\Mapping as ORM;

/**
 * ZgfinMovBancaria
 *
 * @ORM\Table(name="ZGFIN_MOV_BANCARIA", indexes={@ORM\Index(name="fk_ZGFIN_MOV_BANCARIA_1_idx", columns={"COD_ORGANIZACAO"}), @ORM\Index(name="fk_ZGFIN_MOV_BANCARIA_2_idx", columns={"COD_CONTA"}), @ORM\Index(name="fk_ZGFIN_MOV_BANCARIA_3_idx", columns={"COD_TIPO_OPERACAO"}), @ORM\Index(name="fk_ZGFIN_MOV_BANCARIA_4_idx", columns={"COD_ORIGEM"})})
 * @ORM\Entity
 */
class ZgfinMovBancaria
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
     * @var float
     *
     * @ORM\Column(name="VALOR", type="float", precision=10, scale=0, nullable=false)
     */
    private $valor;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="DATA_OPERACAO", type="datetime", nullable=false)
     */
    private $dataOperacao;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="DATA_MOVIMENTACAO", type="date", nullable=false)
     */
    private $dataMovimentacao;

    /**
     * @var integer
     *
     * @ORM\Column(name="COD_GRUPO_MOV", type="bigint", nullable=false)
     */
    private $codGrupoMov;

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
     * @var \Entidades\ZgfinConta
     *
     * @ORM\ManyToOne(targetEntity="Entidades\ZgfinConta")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="COD_CONTA", referencedColumnName="CODIGO")
     * })
     */
    private $codConta;

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
     * @var \Entidades\ZgadmOrigem
     *
     * @ORM\ManyToOne(targetEntity="Entidades\ZgadmOrigem")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="COD_ORIGEM", referencedColumnName="CODIGO")
     * })
     */
    private $codOrigem;


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
     * Set valor
     *
     * @param float $valor
     * @return ZgfinMovBancaria
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
     * Set dataOperacao
     *
     * @param \DateTime $dataOperacao
     * @return ZgfinMovBancaria
     */
    public function setDataOperacao($dataOperacao)
    {
        $this->dataOperacao = $dataOperacao;

        return $this;
    }

    /**
     * Get dataOperacao
     *
     * @return \DateTime 
     */
    public function getDataOperacao()
    {
        return $this->dataOperacao;
    }

    /**
     * Set dataMovimentacao
     *
     * @param \DateTime $dataMovimentacao
     * @return ZgfinMovBancaria
     */
    public function setDataMovimentacao($dataMovimentacao)
    {
        $this->dataMovimentacao = $dataMovimentacao;

        return $this;
    }

    /**
     * Get dataMovimentacao
     *
     * @return \DateTime 
     */
    public function getDataMovimentacao()
    {
        return $this->dataMovimentacao;
    }

    /**
     * Set codGrupoMov
     *
     * @param integer $codGrupoMov
     * @return ZgfinMovBancaria
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
     * Set codOrganizacao
     *
     * @param \Entidades\ZgfmtOrganizacao $codOrganizacao
     * @return ZgfinMovBancaria
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
     * Set codConta
     *
     * @param \Entidades\ZgfinConta $codConta
     * @return ZgfinMovBancaria
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
     * Set codTipoOperacao
     *
     * @param \Entidades\ZgfinOperacaoTipo $codTipoOperacao
     * @return ZgfinMovBancaria
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
     * Set codOrigem
     *
     * @param \Entidades\ZgadmOrigem $codOrigem
     * @return ZgfinMovBancaria
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
}
