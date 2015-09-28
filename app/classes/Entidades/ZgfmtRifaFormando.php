<?php

namespace Entidades;

use Doctrine\ORM\Mapping as ORM;

/**
 * ZgfmtRifaFormando
 *
 * @ORM\Table(name="ZGFMT_RIFA_FORMANDO", indexes={@ORM\Index(name="fk_ZGFMT_RIFA_FORMANDO_1_idx", columns={"COD_RIFA"}), @ORM\Index(name="fk_ZGFMT_RIFA_FORMANDO_2_idx", columns={"COD_FORMANDO"})})
 * @ORM\Entity
 */
class ZgfmtRifaFormando
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
     * @ORM\Column(name="QTDE_VENDIDA", type="integer", nullable=false)
     */
    private $qtdeVendida;

    /**
     * @var \Entidades\ZgfmtRifa
     *
     * @ORM\ManyToOne(targetEntity="Entidades\ZgfmtRifa")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="COD_RIFA", referencedColumnName="CODIGO")
     * })
     */
    private $codRifa;

    /**
     * @var \Entidades\ZgsegUsuario
     *
     * @ORM\ManyToOne(targetEntity="Entidades\ZgsegUsuario")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="COD_FORMANDO", referencedColumnName="CODIGO")
     * })
     */
    private $codFormando;


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
     * Set qtdeVendida
     *
     * @param integer $qtdeVendida
     * @return ZgfmtRifaFormando
     */
    public function setQtdeVendida($qtdeVendida)
    {
        $this->qtdeVendida = $qtdeVendida;

        return $this;
    }

    /**
     * Get qtdeVendida
     *
     * @return integer 
     */
    public function getQtdeVendida()
    {
        return $this->qtdeVendida;
    }

    /**
     * Set codRifa
     *
     * @param \Entidades\ZgfmtRifa $codRifa
     * @return ZgfmtRifaFormando
     */
    public function setCodRifa(\Entidades\ZgfmtRifa $codRifa = null)
    {
        $this->codRifa = $codRifa;

        return $this;
    }

    /**
     * Get codRifa
     *
     * @return \Entidades\ZgfmtRifa 
     */
    public function getCodRifa()
    {
        return $this->codRifa;
    }

    /**
     * Set codFormando
     *
     * @param \Entidades\ZgsegUsuario $codFormando
     * @return ZgfmtRifaFormando
     */
    public function setCodFormando(\Entidades\ZgsegUsuario $codFormando = null)
    {
        $this->codFormando = $codFormando;

        return $this;
    }

    /**
     * Get codFormando
     *
     * @return \Entidades\ZgsegUsuario 
     */
    public function getCodFormando()
    {
        return $this->codFormando;
    }
}
