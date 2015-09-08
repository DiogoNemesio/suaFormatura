<?php

namespace Entidades;

use Doctrine\ORM\Mapping as ORM;

/**
 * ZgestSubgrupo
 *
 * @ORM\Table(name="ZGEST_SUBGRUPO", indexes={@ORM\Index(name="fk_ZGEST_SUBGRUPO_1_idx", columns={"COD_GRUPO"})})
 * @ORM\Entity
 */
class ZgestSubgrupo
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
     * @var \Entidades\ZgestGrupo
     *
     * @ORM\ManyToOne(targetEntity="Entidades\ZgestGrupo")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="COD_GRUPO", referencedColumnName="CODIGO")
     * })
     */
    private $codGrupo;


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
     * @return ZgestSubgrupo
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
     * Set codGrupo
     *
     * @param \Entidades\ZgestGrupo $codGrupo
     * @return ZgestSubgrupo
     */
    public function setCodGrupo(\Entidades\ZgestGrupo $codGrupo = null)
    {
        $this->codGrupo = $codGrupo;

        return $this;
    }

    /**
     * Get codGrupo
     *
     * @return \Entidades\ZgestGrupo 
     */
    public function getCodGrupo()
    {
        return $this->codGrupo;
    }
}
