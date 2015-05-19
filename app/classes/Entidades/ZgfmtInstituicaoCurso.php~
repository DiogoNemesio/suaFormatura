<?php

namespace Entidades;

use Doctrine\ORM\Mapping as ORM;

/**
 * ZgfmtInstituicaoCurso
 *
 * @ORM\Table(name="ZGFMT_INSTITUICAO_CURSO", uniqueConstraints={@ORM\UniqueConstraint(name="ZGFOR_INSTITUICAO_CURSO_UK01", columns={"COD_INSTITUICAO", "COD_CURSO"})}, indexes={@ORM\Index(name="fk_ZGFOR_INSTITUICAO_CURSO_2_idx", columns={"COD_CURSO"}), @ORM\Index(name="IDX_2B3431E7B2D8C3C9", columns={"COD_INSTITUICAO"})})
 * @ORM\Entity
 */
class ZgfmtInstituicaoCurso
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
     * @var \Entidades\ZgfmtInstituicao
     *
     * @ORM\ManyToOne(targetEntity="Entidades\ZgfmtInstituicao")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="COD_INSTITUICAO", referencedColumnName="CODIGO")
     * })
     */
    private $codInstituicao;

    /**
     * @var \Entidades\ZgfmtCurso
     *
     * @ORM\ManyToOne(targetEntity="Entidades\ZgfmtCurso")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="COD_CURSO", referencedColumnName="CODIGO")
     * })
     */
    private $codCurso;


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
     * Set codInstituicao
     *
     * @param \Entidades\ZgfmtInstituicao $codInstituicao
     * @return ZgfmtInstituicaoCurso
     */
    public function setCodInstituicao(\Entidades\ZgfmtInstituicao $codInstituicao = null)
    {
        $this->codInstituicao = $codInstituicao;

        return $this;
    }

    /**
     * Get codInstituicao
     *
     * @return \Entidades\ZgfmtInstituicao 
     */
    public function getCodInstituicao()
    {
        return $this->codInstituicao;
    }

    /**
     * Set codCurso
     *
     * @param \Entidades\ZgfmtCurso $codCurso
     * @return ZgfmtInstituicaoCurso
     */
    public function setCodCurso(\Entidades\ZgfmtCurso $codCurso = null)
    {
        $this->codCurso = $codCurso;

        return $this;
    }

    /**
     * Get codCurso
     *
     * @return \Entidades\ZgfmtCurso 
     */
    public function getCodCurso()
    {
        return $this->codCurso;
    }
}
