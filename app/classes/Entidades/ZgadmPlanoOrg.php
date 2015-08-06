<?php

namespace Entidades;

use Doctrine\ORM\Mapping as ORM;

/**
 * ZgadmPlanoOrg
 *
 * @ORM\Table(name="ZGADM_PLANO_ORG", indexes={@ORM\Index(name="fk_ZGADM_PLANO_ORG_1_idx", columns={"COD_PLANO"}), @ORM\Index(name="fk_ZGADM_PLANO_ORG_2_idx", columns={"COD_TIPO_ORGANIZACAO"})})
 * @ORM\Entity
 */
class ZgadmPlanoOrg
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
     * @var \Entidades\ZgadmPlano
     *
     * @ORM\ManyToOne(targetEntity="Entidades\ZgadmPlano")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="COD_PLANO", referencedColumnName="CODIGO")
     * })
     */
    private $codPlano;

    /**
     * @var \Entidades\ZgadmOrganizacaoTipo
     *
     * @ORM\ManyToOne(targetEntity="Entidades\ZgadmOrganizacaoTipo")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="COD_TIPO_ORGANIZACAO", referencedColumnName="CODIGO")
     * })
     */
    private $codTipoOrganizacao;


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
     * Set codPlano
     *
     * @param \Entidades\ZgadmPlano $codPlano
     * @return ZgadmPlanoOrg
     */
    public function setCodPlano(\Entidades\ZgadmPlano $codPlano = null)
    {
        $this->codPlano = $codPlano;

        return $this;
    }

    /**
     * Get codPlano
     *
     * @return \Entidades\ZgadmPlano 
     */
    public function getCodPlano()
    {
        return $this->codPlano;
    }

    /**
     * Set codTipoOrganizacao
     *
     * @param \Entidades\ZgadmOrganizacaoTipo $codTipoOrganizacao
     * @return ZgadmPlanoOrg
     */
    public function setCodTipoOrganizacao(\Entidades\ZgadmOrganizacaoTipo $codTipoOrganizacao = null)
    {
        $this->codTipoOrganizacao = $codTipoOrganizacao;

        return $this;
    }

    /**
     * Get codTipoOrganizacao
     *
     * @return \Entidades\ZgadmOrganizacaoTipo 
     */
    public function getCodTipoOrganizacao()
    {
        return $this->codTipoOrganizacao;
    }
}
