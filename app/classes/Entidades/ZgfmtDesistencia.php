<?php

namespace Entidades;

use Doctrine\ORM\Mapping as ORM;

/**
 * ZgfmtDesistencia
 *
 * @ORM\Table(name="ZGFMT_DESISTENCIA", indexes={@ORM\Index(name="fk_ZGFMT_DESISTENCIA_1_idx", columns={"COD_ORGANIZACAO"}), @ORM\Index(name="fk_ZGFMT_DESISTENCIA_2_idx", columns={"COD_TIPO_DESISTENCIA"}), @ORM\Index(name="fk_ZGFMT_DESISTENCIA_3_idx", columns={"COD_FORMANDO"})})
 * @ORM\Entity
 */
class ZgfmtDesistencia
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
     * @var \DateTime
     *
     * @ORM\Column(name="DATA_DESISTENCIA", type="date", nullable=false)
     */
    private $dataDesistencia;

    /**
     * @var \Entidades\ZgadmOrganizacao
     *
     * @ORM\ManyToOne(targetEntity="Entidades\ZgadmOrganizacao")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="COD_ORGANIZACAO", referencedColumnName="CODIGO")
     * })
     */
    private $codOrganizacao;

    /**
     * @var \Entidades\ZgfmtDesistenciaTipo
     *
     * @ORM\ManyToOne(targetEntity="Entidades\ZgfmtDesistenciaTipo")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="COD_TIPO_DESISTENCIA", referencedColumnName="CODIGO")
     * })
     */
    private $codTipoDesistencia;

    /**
     * @var \Entidades\ZgsegUsuario
     *
     * @ORM\ManyToOne(targetEntity="Entidades\ZgsegUsuario")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="COD_FORMANDO", referencedColumnName="CODIGO")
     * })
     */
    private $codFormando;


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
     * Set dataDesistencia
     *
     * @param \DateTime $dataDesistencia
     * @return ZgfmtDesistencia
     */
    public function setDataDesistencia($dataDesistencia)
    {
        $this->dataDesistencia = $dataDesistencia;

        return $this;
    }

    /**
     * Get dataDesistencia
     *
     * @return \DateTime 
     */
    public function getDataDesistencia()
    {
        return $this->dataDesistencia;
    }

    /**
     * Set codOrganizacao
     *
     * @param \Entidades\ZgadmOrganizacao $codOrganizacao
     * @return ZgfmtDesistencia
     */
    public function setCodOrganizacao(\Entidades\ZgadmOrganizacao $codOrganizacao = null)
    {
        $this->codOrganizacao = $codOrganizacao;

        return $this;
    }

    /**
     * Get codOrganizacao
     *
     * @return \Entidades\ZgadmOrganizacao 
     */
    public function getCodOrganizacao()
    {
        return $this->codOrganizacao;
    }

    /**
     * Set codTipoDesistencia
     *
     * @param \Entidades\ZgfmtDesistenciaTipo $codTipoDesistencia
     * @return ZgfmtDesistencia
     */
    public function setCodTipoDesistencia(\Entidades\ZgfmtDesistenciaTipo $codTipoDesistencia = null)
    {
        $this->codTipoDesistencia = $codTipoDesistencia;

        return $this;
    }

    /**
     * Get codTipoDesistencia
     *
     * @return \Entidades\ZgfmtDesistenciaTipo 
     */
    public function getCodTipoDesistencia()
    {
        return $this->codTipoDesistencia;
    }

    /**
     * Set codFormando
     *
     * @param \Entidades\ZgsegUsuario $codFormando
     * @return ZgfmtDesistencia
     */
    public function setCodFormando(\Entidades\ZgsegUsuario $codFormando = null)
    {
        $this->codFormando = $codFormando;

        return $this;
    }

    /**
     * Get codFormando
     *
     * @return \Entidades\ZgsegUsuario 
     */
    public function getCodFormando()
    {
        return $this->codFormando;
    }
}
