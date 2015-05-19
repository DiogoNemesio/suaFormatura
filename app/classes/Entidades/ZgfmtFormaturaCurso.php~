<?php

namespace Entidades;

use Doctrine\ORM\Mapping as ORM;

/**
 * ZgfmtFormaturaCurso
 *
 * @ORM\Table(name="ZGFMT_FORMATURA_CURSO", uniqueConstraints={@ORM\UniqueConstraint(name="ZGFOR_TURMA_INST_UK01", columns={"COD_FORMATURA", "COD_CURSO_INST"})}, indexes={@ORM\Index(name="fk_ZGFOR_TURMA_INSTITUICAO_2_idx", columns={"COD_CURSO_INST"}), @ORM\Index(name="IDX_59F5C12DFB2E24A9", columns={"COD_FORMATURA"})})
 * @ORM\Entity
 */
class ZgfmtFormaturaCurso
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
     * @var \Entidades\ZgfmtOrganizacao
     *
     * @ORM\ManyToOne(targetEntity="Entidades\ZgfmtOrganizacao")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="COD_FORMATURA", referencedColumnName="CODIGO")
     * })
     */
    private $codFormatura;

    /**
     * @var \Entidades\ZgfmtInstituicaoCurso
     *
     * @ORM\ManyToOne(targetEntity="Entidades\ZgfmtInstituicaoCurso")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="COD_CURSO_INST", referencedColumnName="CODIGO")
     * })
     */
    private $codCursoInst;


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
     * Set codFormatura
     *
     * @param \Entidades\ZgfmtOrganizacao $codFormatura
     * @return ZgfmtFormaturaCurso
     */
    public function setCodFormatura(\Entidades\ZgfmtOrganizacao $codFormatura = null)
    {
        $this->codFormatura = $codFormatura;

        return $this;
    }

    /**
     * Get codFormatura
     *
     * @return \Entidades\ZgfmtOrganizacao 
     */
    public function getCodFormatura()
    {
        return $this->codFormatura;
    }

    /**
     * Set codCursoInst
     *
     * @param \Entidades\ZgfmtInstituicaoCurso $codCursoInst
     * @return ZgfmtFormaturaCurso
     */
    public function setCodCursoInst(\Entidades\ZgfmtInstituicaoCurso $codCursoInst = null)
    {
        $this->codCursoInst = $codCursoInst;

        return $this;
    }

    /**
     * Get codCursoInst
     *
     * @return \Entidades\ZgfmtInstituicaoCurso 
     */
    public function getCodCursoInst()
    {
        return $this->codCursoInst;
    }
}
