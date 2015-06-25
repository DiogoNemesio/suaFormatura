<?php

namespace Entidades;

use Doctrine\ORM\Mapping as ORM;

/**
 * ZgfmtCurso
 *
 * @ORM\Table(name="ZGFMT_CURSO", indexes={@ORM\Index(name="fk_ZGFMT_CURSO_1_idx", columns={"COD_GRAU"}), @ORM\Index(name="fk_ZGFMT_CURSO_2_idx", columns={"COD_AREA"})})
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
     * @ORM\Column(name="COD_OCDE", type="string", length=8, nullable=false)
     */
    private $codOcde;

    /**
     * @var string
     *
     * @ORM\Column(name="NOME", type="string", length=100, nullable=false)
     */
    private $nome;

    /**
     * @var \Entidades\ZgfmtCursoGrau
     *
     * @ORM\ManyToOne(targetEntity="Entidades\ZgfmtCursoGrau")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="COD_GRAU", referencedColumnName="CODIGO")
     * })
     */
    private $codGrau;

    /**
     * @var \Entidades\ZgfmtCursoArea
     *
     * @ORM\ManyToOne(targetEntity="Entidades\ZgfmtCursoArea")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="COD_AREA", referencedColumnName="CODIGO")
     * })
     */
    private $codArea;


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
     * Set codOcde
     *
     * @param string $codOcde
     * @return ZgfmtCurso
     */
    public function setCodOcde($codOcde)
    {
        $this->codOcde = $codOcde;

        return $this;
    }

    /**
     * Get codOcde
     *
     * @return string 
     */
    public function getCodOcde()
    {
        return $this->codOcde;
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
     * Set codGrau
     *
     * @param \Entidades\ZgfmtCursoGrau $codGrau
     * @return ZgfmtCurso
     */
    public function setCodGrau(\Entidades\ZgfmtCursoGrau $codGrau = null)
    {
        $this->codGrau = $codGrau;

        return $this;
    }

    /**
     * Get codGrau
     *
     * @return \Entidades\ZgfmtCursoGrau 
     */
    public function getCodGrau()
    {
        return $this->codGrau;
    }

    /**
     * Set codArea
     *
     * @param \Entidades\ZgfmtCursoArea $codArea
     * @return ZgfmtCurso
     */
    public function setCodArea(\Entidades\ZgfmtCursoArea $codArea = null)
    {
        $this->codArea = $codArea;

        return $this;
    }

    /**
     * Get codArea
     *
     * @return \Entidades\ZgfmtCursoArea 
     */
    public function getCodArea()
    {
        return $this->codArea;
    }
}
