<?php

namespace Entidades;

use Doctrine\ORM\Mapping as ORM;

/**
 * ZgforFormaturaCurso
 *
 * @ORM\Table(name="ZGFOR_FORMATURA_CURSO", uniqueConstraints={@ORM\UniqueConstraint(name="ZGFOR_TURMA_INST_UK01", columns={"COD_FORMATURA", "COD_CURSO_INST"})}, indexes={@ORM\Index(name="fk_ZGFOR_TURMA_INSTITUICAO_2_idx", columns={"COD_CURSO_INST"}), @ORM\Index(name="IDX_FB151DA6FB2E24A9", columns={"COD_FORMATURA"})})
 * @ORM\Entity
 */
class ZgforFormaturaCurso
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
     * @var \Entidades\ZgforOrganizacao
     *
     * @ORM\ManyToOne(targetEntity="Entidades\ZgforOrganizacao")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="COD_FORMATURA", referencedColumnName="CODIGO")
     * })
     */
    private $codFormatura;

    /**
     * @var \Entidades\ZgforInstituicaoCurso
     *
     * @ORM\ManyToOne(targetEntity="Entidades\ZgforInstituicaoCurso")
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
     * @param \Entidades\ZgforOrganizacao $codFormatura
     * @return ZgforFormaturaCurso
     */
    public function setCodFormatura(\Entidades\ZgforOrganizacao $codFormatura = null)
    {
        $this->codFormatura = $codFormatura;

        return $this;
    }

    /**
     * Get codFormatura
     *
     * @return \Entidades\ZgforOrganizacao 
     */
    public function getCodFormatura()
    {
        return $this->codFormatura;
    }

    /**
     * Set codCursoInst
     *
     * @param \Entidades\ZgforInstituicaoCurso $codCursoInst
     * @return ZgforFormaturaCurso
     */
    public function setCodCursoInst(\Entidades\ZgforInstituicaoCurso $codCursoInst = null)
    {
        $this->codCursoInst = $codCursoInst;

        return $this;
    }

    /**
     * Get codCursoInst
     *
     * @return \Entidades\ZgforInstituicaoCurso 
     */
    public function getCodCursoInst()
    {
        return $this->codCursoInst;
    }
}
