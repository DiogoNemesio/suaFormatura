<?php

namespace Entidades;

use Doctrine\ORM\Mapping as ORM;

/**
 * ZgfinContaReceberRateio
 *
 * @ORM\Table(name="ZGFIN_CONTA_RECEBER_RATEIO", indexes={@ORM\Index(name="fk_ZGFIN_CONTA_RECEBER_RATEIO_1_idx", columns={"COD_CONTA_REC"}), @ORM\Index(name="fk_ZGFIN_CONTA_RECEBER_RATEIO_2_idx", columns={"COD_CATEGORIA"}), @ORM\Index(name="fk_ZGFIN_CONTA_RECEBER_RATEIO_3_idx", columns={"COD_CENTRO_CUSTO"}), @ORM\Index(name="ZGFIN_CONTA_RECEBER_RATEIO_IX01", columns={"COD_CONTA_REC", "COD_CATEGORIA"})})
 * @ORM\Entity
 */
class ZgfinContaReceberRateio
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
     * @var float
     *
     * @ORM\Column(name="PCT_VALOR", type="float", precision=10, scale=0, nullable=true)
     */
    private $pctValor;

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
     * @var \Entidades\ZgfinCategoria
     *
     * @ORM\ManyToOne(targetEntity="Entidades\ZgfinCategoria")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="COD_CATEGORIA", referencedColumnName="CODIGO")
     * })
     */
    private $codCategoria;

    /**
     * @var \Entidades\ZgfinCentroCusto
     *
     * @ORM\ManyToOne(targetEntity="Entidades\ZgfinCentroCusto")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="COD_CENTRO_CUSTO", referencedColumnName="CODIGO")
     * })
     */
    private $codCentroCusto;


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
     * @return ZgfinContaReceberRateio
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
     * Set pctValor
     *
     * @param float $pctValor
     * @return ZgfinContaReceberRateio
     */
    public function setPctValor($pctValor)
    {
        $this->pctValor = $pctValor;

        return $this;
    }

    /**
     * Get pctValor
     *
     * @return float 
     */
    public function getPctValor()
    {
        return $this->pctValor;
    }

    /**
     * Set codContaRec
     *
     * @param \Entidades\ZgfinContaReceber $codContaRec
     * @return ZgfinContaReceberRateio
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
     * Set codCategoria
     *
     * @param \Entidades\ZgfinCategoria $codCategoria
     * @return ZgfinContaReceberRateio
     */
    public function setCodCategoria(\Entidades\ZgfinCategoria $codCategoria = null)
    {
        $this->codCategoria = $codCategoria;

        return $this;
    }

    /**
     * Get codCategoria
     *
     * @return \Entidades\ZgfinCategoria 
     */
    public function getCodCategoria()
    {
        return $this->codCategoria;
    }

    /**
     * Set codCentroCusto
     *
     * @param \Entidades\ZgfinCentroCusto $codCentroCusto
     * @return ZgfinContaReceberRateio
     */
    public function setCodCentroCusto(\Entidades\ZgfinCentroCusto $codCentroCusto = null)
    {
        $this->codCentroCusto = $codCentroCusto;

        return $this;
    }

    /**
     * Get codCentroCusto
     *
     * @return \Entidades\ZgfinCentroCusto 
     */
    public function getCodCentroCusto()
    {
        return $this->codCentroCusto;
    }
}
