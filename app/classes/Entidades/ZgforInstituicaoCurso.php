<?php

namespace Entidades;

use Doctrine\ORM\Mapping as ORM;

/**
 * ZgforInstituicaoCurso
 *
 * @ORM\Table(name="ZGFOR_INSTITUICAO_CURSO", uniqueConstraints={@ORM\UniqueConstraint(name="ZGFOR_INSTITUICAO_CURSO_UK01", columns={"COD_INSTITUICAO", "COD_CURSO"})}, indexes={@ORM\Index(name="fk_ZGFOR_INSTITUICAO_CURSO_2_idx", columns={"COD_CURSO"}), @ORM\Index(name="IDX_7C264C78B2D8C3C9", columns={"COD_INSTITUICAO"})})
 * @ORM\Entity
 */
class ZgforInstituicaoCurso
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
     * @var \Entidades\ZgforInstituicao
     *
     * @ORM\ManyToOne(targetEntity="Entidades\ZgforInstituicao")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="COD_INSTITUICAO", referencedColumnName="CODIGO")
     * })
     */
    private $codInstituicao;

    /**
     * @var \Entidades\ZgforCurso
     *
     * @ORM\ManyToOne(targetEntity="Entidades\ZgforCurso")
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
     * @param \Entidades\ZgforInstituicao $codInstituicao
     * @return ZgforInstituicaoCurso
     */
    public function setCodInstituicao(\Entidades\ZgforInstituicao $codInstituicao = null)
    {
        $this->codInstituicao = $codInstituicao;

        return $this;
    }

    /**
     * Get codInstituicao
     *
     * @return \Entidades\ZgforInstituicao 
     */
    public function getCodInstituicao()
    {
        return $this->codInstituicao;
    }

    /**
     * Set codCurso
     *
     * @param \Entidades\ZgforCurso $codCurso
     * @return ZgforInstituicaoCurso
     */
    public function setCodCurso(\Entidades\ZgforCurso $codCurso = null)
    {
        $this->codCurso = $codCurso;

        return $this;
    }

    /**
     * Get codCurso
     *
     * @return \Entidades\ZgforCurso 
     */
    public function getCodCurso()
    {
        return $this->codCurso;
    }
}
