<?php

namespace Entidades;

use Doctrine\ORM\Mapping as ORM;

/**
 * ZgfmtOrganizacaoAdm
 *
 * @ORM\Table(name="ZGFMT_ORGANIZACAO_ADM", indexes={@ORM\Index(name="fk_ZGFMT_ORGANIZACAO_ADM_1_idx", columns={"COD_FORMATURA"}), @ORM\Index(name="fk_ZGFMT_ORGANIZACAO_ADM_2_idx", columns={"COD_EMPRESA"})})
 * @ORM\Entity
 */
class ZgfmtOrganizacaoAdm
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
     * @ORM\Column(name="DATA_VALIDADE", type="datetime", nullable=true)
     */
    private $dataValidade;

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
     * @var \Entidades\ZgfmtOrganizacao
     *
     * @ORM\ManyToOne(targetEntity="Entidades\ZgfmtOrganizacao")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="COD_EMPRESA", referencedColumnName="CODIGO")
     * })
     */
    private $codEmpresa;


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
     * Set dataValidade
     *
     * @param \DateTime $dataValidade
     * @return ZgfmtOrganizacaoAdm
     */
    public function setDataValidade($dataValidade)
    {
        $this->dataValidade = $dataValidade;

        return $this;
    }

    /**
     * Get dataValidade
     *
     * @return \DateTime 
     */
    public function getDataValidade()
    {
        return $this->dataValidade;
    }

    /**
     * Set codFormatura
     *
     * @param \Entidades\ZgfmtOrganizacao $codFormatura
     * @return ZgfmtOrganizacaoAdm
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
     * Set codEmpresa
     *
     * @param \Entidades\ZgfmtOrganizacao $codEmpresa
     * @return ZgfmtOrganizacaoAdm
     */
    public function setCodEmpresa(\Entidades\ZgfmtOrganizacao $codEmpresa = null)
    {
        $this->codEmpresa = $codEmpresa;

        return $this;
    }

    /**
     * Get codEmpresa
     *
     * @return \Entidades\ZgfmtOrganizacao 
     */
    public function getCodEmpresa()
    {
        return $this->codEmpresa;
    }
}
