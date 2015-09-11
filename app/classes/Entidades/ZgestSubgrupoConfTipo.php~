<?php

namespace Entidades;

use Doctrine\ORM\Mapping as ORM;

/**
 * ZgestSubgrupoConfTipo
 *
 * @ORM\Table(name="ZGEST_SUBGRUPO_CONF_TIPO", indexes={@ORM\Index(name="fk_ZGEST_SUBGRUPO_CONF_TIPO_1_idx", columns={"COD_MASCARA"})})
 * @ORM\Entity
 */
class ZgestSubgrupoConfTipo
{
    /**
     * @var string
     *
     * @ORM\Column(name="CODIGO", type="string", length=3, nullable=false)
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
     * @var \Entidades\ZgappMascara
     *
     * @ORM\ManyToOne(targetEntity="Entidades\ZgappMascara")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="COD_MASCARA", referencedColumnName="CODIGO")
     * })
     */
    private $codMascara;


    /**
     * Get codigo
     *
     * @return string 
     */
    public function getCodigo()
    {
        return $this->codigo;
    }

    /**
     * Set descricao
     *
     * @param string $descricao
     * @return ZgestSubgrupoConfTipo
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
     * Set codMascara
     *
     * @param \Entidades\ZgappMascara $codMascara
     * @return ZgestSubgrupoConfTipo
     */
    public function setCodMascara(\Entidades\ZgappMascara $codMascara = null)
    {
        $this->codMascara = $codMascara;

        return $this;
    }

    /**
     * Get codMascara
     *
     * @return \Entidades\ZgappMascara 
     */
    public function getCodMascara()
    {
        return $this->codMascara;
    }
}
