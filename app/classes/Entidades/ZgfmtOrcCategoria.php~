<?php

namespace Entidades;

use Doctrine\ORM\Mapping as ORM;

/**
 * ZgfmtOrcCategoria
 *
 * @ORM\Table(name="ZGFMT_ORC_CATEGORIA", indexes={@ORM\Index(name="fk_ZGFMT_ORC_CATEGORIA_1_idx", columns={"COD_ORC_SUBCATEGORIA"})})
 * @ORM\Entity
 */
class ZgfmtOrcCategoria
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
     * @ORM\Column(name="NOME", type="string", length=60, nullable=false)
     */
    private $nome;

    /**
     * @var \Entidades\ZgfmtOrcSubcategoria
     *
     * @ORM\ManyToOne(targetEntity="Entidades\ZgfmtOrcSubcategoria")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="COD_ORC_SUBCATEGORIA", referencedColumnName="CODIGO")
     * })
     */
    private $codOrcSubcategoria;


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
     * Set nome
     *
     * @param string $nome
     * @return ZgfmtOrcCategoria
     */
    public function setNome($nome)
    {
        $this->nome = $nome;

        return $this;
    }

    /**
     * Get nome
     *
     * @return string 
     */
    public function getNome()
    {
        return $this->nome;
    }

    /**
     * Set codOrcSubcategoria
     *
     * @param \Entidades\ZgfmtOrcSubcategoria $codOrcSubcategoria
     * @return ZgfmtOrcCategoria
     */
    public function setCodOrcSubcategoria(\Entidades\ZgfmtOrcSubcategoria $codOrcSubcategoria = null)
    {
        $this->codOrcSubcategoria = $codOrcSubcategoria;

        return $this;
    }

    /**
     * Get codOrcSubcategoria
     *
     * @return \Entidades\ZgfmtOrcSubcategoria 
     */
    public function getCodOrcSubcategoria()
    {
        return $this->codOrcSubcategoria;
    }
}
