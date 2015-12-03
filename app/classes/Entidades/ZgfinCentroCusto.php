<?php

namespace Entidades;

use Doctrine\ORM\Mapping as ORM;

/**
 * ZgfinCentroCusto
 *
 * @ORM\Table(name="ZGFIN_CENTRO_CUSTO", indexes={@ORM\Index(name="fk_ZGFIN_CENTRO_CUSTO_1_idx", columns={"COD_ORGANIZACAO"}), @ORM\Index(name="fk_ZGFIN_CENTRO_CUSTO_2_idx", columns={"COD_TIPO_CENTRO_CUSTO"}), @ORM\Index(name="fk_ZGFIN_CENTRO_CUSTO_3_idx", columns={"COD_TIPO_ORGANIZACAO"})})
 * @ORM\Entity
 */
class ZgfinCentroCusto
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
     * @ORM\Column(name="DESCRICAO", type="string", length=60, nullable=false)
     */
    private $descricao;

    /**
     * @var integer
     *
     * @ORM\Column(name="IND_DEBITO", type="integer", nullable=false)
     */
    private $indDebito;

    /**
     * @var integer
     *
     * @ORM\Column(name="IND_CREDITO", type="integer", nullable=false)
     */
    private $indCredito;

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
     * @var \Entidades\ZgfinCentroCustoTipo
     *
     * @ORM\ManyToOne(targetEntity="Entidades\ZgfinCentroCustoTipo")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="COD_TIPO_CENTRO_CUSTO", referencedColumnName="CODIGO")
     * })
     */
    private $codTipoCentroCusto;

    /**
     * @var \Entidades\ZgadmOrganizacaoTipo
     *
     * @ORM\ManyToOne(targetEntity="Entidades\ZgadmOrganizacaoTipo")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="COD_TIPO_ORGANIZACAO", referencedColumnName="CODIGO")
     * })
     */
    private $codTipoOrganizacao;


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
     * Set descricao
     *
     * @param string $descricao
     * @return ZgfinCentroCusto
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
     * Set indDebito
     *
     * @param integer $indDebito
     * @return ZgfinCentroCusto
     */
    public function setIndDebito($indDebito)
    {
        $this->indDebito = $indDebito;

        return $this;
    }

    /**
     * Get indDebito
     *
     * @return integer 
     */
    public function getIndDebito()
    {
        return $this->indDebito;
    }

    /**
     * Set indCredito
     *
     * @param integer $indCredito
     * @return ZgfinCentroCusto
     */
    public function setIndCredito($indCredito)
    {
        $this->indCredito = $indCredito;

        return $this;
    }

    /**
     * Get indCredito
     *
     * @return integer 
     */
    public function getIndCredito()
    {
        return $this->indCredito;
    }

    /**
     * Set codOrganizacao
     *
     * @param \Entidades\ZgadmOrganizacao $codOrganizacao
     * @return ZgfinCentroCusto
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
     * Set codTipoCentroCusto
     *
     * @param \Entidades\ZgfinCentroCustoTipo $codTipoCentroCusto
     * @return ZgfinCentroCusto
     */
    public function setCodTipoCentroCusto(\Entidades\ZgfinCentroCustoTipo $codTipoCentroCusto = null)
    {
        $this->codTipoCentroCusto = $codTipoCentroCusto;

        return $this;
    }

    /**
     * Get codTipoCentroCusto
     *
     * @return \Entidades\ZgfinCentroCustoTipo 
     */
    public function getCodTipoCentroCusto()
    {
        return $this->codTipoCentroCusto;
    }

    /**
     * Set codTipoOrganizacao
     *
     * @param \Entidades\ZgadmOrganizacaoTipo $codTipoOrganizacao
     * @return ZgfinCentroCusto
     */
    public function setCodTipoOrganizacao(\Entidades\ZgadmOrganizacaoTipo $codTipoOrganizacao = null)
    {
        $this->codTipoOrganizacao = $codTipoOrganizacao;

        return $this;
    }

    /**
     * Get codTipoOrganizacao
     *
     * @return \Entidades\ZgadmOrganizacaoTipo 
     */
    public function getCodTipoOrganizacao()
    {
        return $this->codTipoOrganizacao;
    }
}
