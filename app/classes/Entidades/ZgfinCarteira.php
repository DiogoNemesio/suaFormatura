<?php

namespace Entidades;

use Doctrine\ORM\Mapping as ORM;

/**
 * ZgfinCarteira
 *
 * @ORM\Table(name="ZGFIN_CARTEIRA", indexes={@ORM\Index(name="fk_ZGFIN_CARTEIRA_1_idx", columns={"COD_BANCO"})})
 * @ORM\Entity
 */
class ZgfinCarteira
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
     * @ORM\Column(name="COD_CARTEIRA", type="string", length=3, nullable=false)
     */
    private $codCarteira;

    /**
     * @var \Entidades\ZgfinBanco
     *
     * @ORM\ManyToOne(targetEntity="Entidades\ZgfinBanco")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="COD_BANCO", referencedColumnName="CODIGO")
     * })
     */
    private $codBanco;


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
     * Set codCarteira
     *
     * @param string $codCarteira
     * @return ZgfinCarteira
     */
    public function setCodCarteira($codCarteira)
    {
        $this->codCarteira = $codCarteira;

        return $this;
    }

    /**
     * Get codCarteira
     *
     * @return string 
     */
    public function getCodCarteira()
    {
        return $this->codCarteira;
    }

    /**
     * Set codBanco
     *
     * @param \Entidades\ZgfinBanco $codBanco
     * @return ZgfinCarteira
     */
    public function setCodBanco(\Entidades\ZgfinBanco $codBanco = null)
    {
        $this->codBanco = $codBanco;

        return $this;
    }

    /**
     * Get codBanco
     *
     * @return \Entidades\ZgfinBanco 
     */
    public function getCodBanco()
    {
        return $this->codBanco;
    }
}
