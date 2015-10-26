<?php

namespace Entidades;

use Doctrine\ORM\Mapping as ORM;

/**
 * ZgfinSegmentoCategoria
 *
 * @ORM\Table(name="ZGFIN_SEGMENTO_CATEGORIA", uniqueConstraints={@ORM\UniqueConstraint(name="ZGFIN_SEGMENTO_CATEGORIA_UK01", columns={"COD_SEGMENTO", "COD_CATEGORIA"})}, indexes={@ORM\Index(name="fk_ZGFIN_SEGMENTO_CATEGORIA_1_idx", columns={"COD_SEGMENTO"}), @ORM\Index(name="fk_ZGFIN_SEGMENTO_CATEGORIA_2_idx", columns={"COD_CATEGORIA"})})
 * @ORM\Entity
 */
class ZgfinSegmentoCategoria
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
     * @var \Entidades\ZgfinSegmentoMercado
     *
     * @ORM\ManyToOne(targetEntity="Entidades\ZgfinSegmentoMercado")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="COD_SEGMENTO", referencedColumnName="CODIGO")
     * })
     */
    private $codSegmento;

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
     * Get codigo
     *
     * @return integer 
     */
    public function getCodigo()
    {
        return $this->codigo;
    }

    /**
     * Set codSegmento
     *
     * @param \Entidades\ZgfinSegmentoMercado $codSegmento
     * @return ZgfinSegmentoCategoria
     */
    public function setCodSegmento(\Entidades\ZgfinSegmentoMercado $codSegmento = null)
    {
        $this->codSegmento = $codSegmento;

        return $this;
    }

    /**
     * Get codSegmento
     *
     * @return \Entidades\ZgfinSegmentoMercado 
     */
    public function getCodSegmento()
    {
        return $this->codSegmento;
    }

    /**
     * Set codCategoria
     *
     * @param \Entidades\ZgfinCategoria $codCategoria
     * @return ZgfinSegmentoCategoria
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
}
