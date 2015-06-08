<?php

namespace Entidades;

use Doctrine\ORM\Mapping as ORM;

/**
 * ZgfmtCurso
 *
 * @ORM\Table(name="ZGFMT_CURSO", indexes={@ORM\Index(name="fk_ZGFMT_CURSO_1_idx", columns={"COD_TIPO"})})
 * @ORM\Entity
 */
class ZgfmtCurso
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
     * @ORM\Column(name="NOME", type="string", length=100, nullable=false)
     */
    private $nome;

    /**
     * @var \Entidades\ZgfmtCursoTipo
     *
     * @ORM\ManyToOne(targetEntity="Entidades\ZgfmtCursoTipo")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="COD_TIPO", referencedColumnName="CODIGO")
     * })
     */
    private $codTipo;


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
     * @return ZgfmtCurso
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
     * Set codTipo
     *
     * @param \Entidades\ZgfmtCursoTipo $codTipo
     * @return ZgfmtCurso
     */
    public function setCodTipo(\Entidades\ZgfmtCursoTipo $codTipo = null)
    {
        $this->codTipo = $codTipo;

        return $this;
    }

    /**
     * Get codTipo
     *
     * @return \Entidades\ZgfmtCursoTipo 
     */
    public function getCodTipo()
    {
        return $this->codTipo;
    }
}
