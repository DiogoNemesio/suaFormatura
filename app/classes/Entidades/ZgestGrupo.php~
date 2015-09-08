<?php

namespace Entidades;

use Doctrine\ORM\Mapping as ORM;

/**
 * ZgestGrupo
 *
 * @ORM\Table(name="ZGEST_GRUPO", indexes={@ORM\Index(name="fk_ZGEST_GRUPO_1_idx", columns={"COD_GRUPO_PAI"})})
 * @ORM\Entity
 */
class ZgestGrupo
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
     *   @ORM\JoinColumn(name="COD_GRUPO_PAI", referencedColumnName="CODIGO")
     * })
     */
    private $codGrupoPai;


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
     * @return ZgestGrupo
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
     * Set codGrupoPai
     *
     * @param \Entidades\ZgestGrupo $codGrupoPai
     * @return ZgestGrupo
     */
    public function setCodGrupoPai(\Entidades\ZgestGrupo $codGrupoPai = null)
    {
        $this->codGrupoPai = $codGrupoPai;

        return $this;
    }

    /**
     * Get codGrupoPai
     *
     * @return \Entidades\ZgestGrupo 
     */
    public function getCodGrupoPai()
    {
        return $this->codGrupoPai;
    }
}
