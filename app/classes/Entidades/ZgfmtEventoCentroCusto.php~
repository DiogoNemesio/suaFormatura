<?php

namespace Entidades;

use Doctrine\ORM\Mapping as ORM;

/**
 * ZgfmtEventoCentroCusto
 *
 * @ORM\Table(name="ZGFMT_EVENTO_CENTRO_CUSTO", indexes={@ORM\Index(name="fk_ZGFMT_EVENTO_CENTRO_CUSTO_1_idx", columns={"COD_TIPO_EVENTO"}), @ORM\Index(name="fk_ZGFMT_EVENTO_CENTRO_CUSTO_2_idx", columns={"COD_CENTRO_CUSTO"})})
 * @ORM\Entity
 */
class ZgfmtEventoCentroCusto
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
     * @var \Entidades\ZgfmtEventoTipo
     *
     * @ORM\ManyToOne(targetEntity="Entidades\ZgfmtEventoTipo")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="COD_TIPO_EVENTO", referencedColumnName="CODIGO")
     * })
     */
    private $codTipoEvento;

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
     * Set codTipoEvento
     *
     * @param \Entidades\ZgfmtEventoTipo $codTipoEvento
     * @return ZgfmtEventoCentroCusto
     */
    public function setCodTipoEvento(\Entidades\ZgfmtEventoTipo $codTipoEvento = null)
    {
        $this->codTipoEvento = $codTipoEvento;

        return $this;
    }

    /**
     * Get codTipoEvento
     *
     * @return \Entidades\ZgfmtEventoTipo 
     */
    public function getCodTipoEvento()
    {
        return $this->codTipoEvento;
    }

    /**
     * Set codCentroCusto
     *
     * @param \Entidades\ZgfinCentroCusto $codCentroCusto
     * @return ZgfmtEventoCentroCusto
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
