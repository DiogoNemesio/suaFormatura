<?php

namespace Entidades;

use Doctrine\ORM\Mapping as ORM;

/**
 * ZgfinContaPagarRateio
 *
 * @ORM\Table(name="ZGFIN_CONTA_PAGAR_RATEIO", indexes={@ORM\Index(name="fk_ZGFIN_CONTA_PAGAR_RATEIO_1_idx", columns={"COD_CONTA_PAG"}), @ORM\Index(name="fk_ZGFIN_CONTA_PAGAR_RATEIO_2_idx", columns={"COD_CATEGORIA"}), @ORM\Index(name="fk_ZGFIN_CONTA_PAGAR_RATEIO_3_idx", columns={"COD_CENTRO_CUSTO"})})
 * @ORM\Entity
 */
class ZgfinContaPagarRateio
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
     * @ORM\Column(name="PCT_VALOR", type="float", precision=10, scale=0, nullable=false)
     */
    private $pctValor;

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
     * @return ZgfinContaPagarRateio
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
     * @return ZgfinContaPagarRateio
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
     * Set codContaPag
     *
     * @param \Entidades\ZgfinContaPagar $codContaPag
     * @return ZgfinContaPagarRateio
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
     * Set codCategoria
     *
     * @param \Entidades\ZgfinCategoria $codCategoria
     * @return ZgfinContaPagarRateio
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
     * @return ZgfinContaPagarRateio
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
