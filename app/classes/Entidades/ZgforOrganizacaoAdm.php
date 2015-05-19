<?php

namespace Entidades;

use Doctrine\ORM\Mapping as ORM;

/**
 * ZgforOrganizacaoAdm
 *
 * @ORM\Table(name="ZGFOR_ORGANIZACAO_ADM", indexes={@ORM\Index(name="fk_ZGFOR_ORGANIZACAO_ADM_2", columns={"COD_EMPRESA"}), @ORM\Index(name="fk_ZGFOR_ORGANIZACAO_ADM_1_idx", columns={"COD_FORMATURA"})})
 * @ORM\Entity
 */
class ZgforOrganizacaoAdm
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
     * @var \Entidades\ZgforOrganizacao
     *
     * @ORM\ManyToOne(targetEntity="Entidades\ZgforOrganizacao")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="COD_FORMATURA", referencedColumnName="CODIGO")
     * })
     */
    private $codFormatura;

    /**
     * @var \Entidades\ZgforOrganizacao
     *
     * @ORM\ManyToOne(targetEntity="Entidades\ZgforOrganizacao")
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
     * @return ZgforOrganizacaoAdm
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
     * @param \Entidades\ZgforOrganizacao $codFormatura
     * @return ZgforOrganizacaoAdm
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
     * Set codEmpresa
     *
     * @param \Entidades\ZgforOrganizacao $codEmpresa
     * @return ZgforOrganizacaoAdm
     */
    public function setCodEmpresa(\Entidades\ZgforOrganizacao $codEmpresa = null)
    {
        $this->codEmpresa = $codEmpresa;

        return $this;
    }

    /**
     * Get codEmpresa
     *
     * @return \Entidades\ZgforOrganizacao 
     */
    public function getCodEmpresa()
    {
        return $this->codEmpresa;
    }
}
