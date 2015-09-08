<?php

namespace Entidades;

use Doctrine\ORM\Mapping as ORM;

/**
 * ZgestSubgrupoConfValor
 *
 * @ORM\Table(name="ZGEST_SUBGRUPO_CONF_VALOR", indexes={@ORM\Index(name="fk_ZGEST_SUBGRUPO_CONF_VALOR_1_idx", columns={"COD_SUBGRUPO_CONF"})})
 * @ORM\Entity
 */
class ZgestSubgrupoConfValor
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
     * @ORM\Column(name="VALOR", type="string", length=60, nullable=false)
     */
    private $valor;

    /**
     * @var \Entidades\ZgestSubgrupoConf
     *
     * @ORM\ManyToOne(targetEntity="Entidades\ZgestSubgrupoConf")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="COD_SUBGRUPO_CONF", referencedColumnName="CODIGO")
     * })
     */
    private $codSubgrupoConf;


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
     * @param string $valor
     * @return ZgestSubgrupoConfValor
     */
    public function setValor($valor)
    {
        $this->valor = $valor;

        return $this;
    }

    /**
     * Get valor
     *
     * @return string 
     */
    public function getValor()
    {
        return $this->valor;
    }

    /**
     * Set codSubgrupoConf
     *
     * @param \Entidades\ZgestSubgrupoConf $codSubgrupoConf
     * @return ZgestSubgrupoConfValor
     */
    public function setCodSubgrupoConf(\Entidades\ZgestSubgrupoConf $codSubgrupoConf = null)
    {
        $this->codSubgrupoConf = $codSubgrupoConf;

        return $this;
    }

    /**
     * Get codSubgrupoConf
     *
     * @return \Entidades\ZgestSubgrupoConf 
     */
    public function getCodSubgrupoConf()
    {
        return $this->codSubgrupoConf;
    }
}
