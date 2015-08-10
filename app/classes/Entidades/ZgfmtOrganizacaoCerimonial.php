<?php

namespace Entidades;

use Doctrine\ORM\Mapping as ORM;

/**
 * ZgfmtOrganizacaoCerimonial
 *
 * @ORM\Table(name="ZGFMT_ORGANIZACAO_CERIMONIAL", indexes={@ORM\Index(name="fk_ZGFMT_ORGANIZACAO_CERIMONIAL_1_idx", columns={"COD_ORGANIZACAO"}), @ORM\Index(name="fk_ZGFMT_ORGANIZACAO_CERIMONIAL_2_idx", columns={"COD_PLANO_FORMATURA"})})
 * @ORM\Entity
 */
class ZgfmtOrganizacaoCerimonial
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
     * @var \Entidades\ZgadmOrganizacao
     *
     * @ORM\ManyToOne(targetEntity="Entidades\ZgadmOrganizacao")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="COD_ORGANIZACAO", referencedColumnName="CODIGO")
     * })
     */
    private $codOrganizacao;

    /**
     * @var \Entidades\ZgadmPlano
     *
     * @ORM\ManyToOne(targetEntity="Entidades\ZgadmPlano")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="COD_PLANO_FORMATURA", referencedColumnName="CODIGO")
     * })
     */
    private $codPlanoFormatura;


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
     * Set codOrganizacao
     *
     * @param \Entidades\ZgadmOrganizacao $codOrganizacao
     * @return ZgfmtOrganizacaoCerimonial
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
     * Set codPlanoFormatura
     *
     * @param \Entidades\ZgadmPlano $codPlanoFormatura
     * @return ZgfmtOrganizacaoCerimonial
     */
    public function setCodPlanoFormatura(\Entidades\ZgadmPlano $codPlanoFormatura = null)
    {
        $this->codPlanoFormatura = $codPlanoFormatura;

        return $this;
    }

    /**
     * Get codPlanoFormatura
     *
     * @return \Entidades\ZgadmPlano 
     */
    public function getCodPlanoFormatura()
    {
        return $this->codPlanoFormatura;
    }
}
