<?php

namespace Entidades;

use Doctrine\ORM\Mapping as ORM;

/**
 * ZgestSubgrupoOrg
 *
 * @ORM\Table(name="ZGEST_SUBGRUPO_ORG", indexes={@ORM\Index(name="fk_ZGEST_SUBGRUPO_ORG_1_idx", columns={"COD_SUBGRUPO"}), @ORM\Index(name="fk_ZGEST_SUBGRUPO_ORG_2_idx", columns={"COD_TIPO_ORGANIZACAO"})})
 * @ORM\Entity
 */
class ZgestSubgrupoOrg
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
     * @var \Entidades\ZgestSubgrupo
     *
     * @ORM\ManyToOne(targetEntity="Entidades\ZgestSubgrupo")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="COD_SUBGRUPO", referencedColumnName="CODIGO")
     * })
     */
    private $codSubgrupo;

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
     * Set codSubgrupo
     *
     * @param \Entidades\ZgestSubgrupo $codSubgrupo
     * @return ZgestSubgrupoOrg
     */
    public function setCodSubgrupo(\Entidades\ZgestSubgrupo $codSubgrupo = null)
    {
        $this->codSubgrupo = $codSubgrupo;

        return $this;
    }

    /**
     * Get codSubgrupo
     *
     * @return \Entidades\ZgestSubgrupo 
     */
    public function getCodSubgrupo()
    {
        return $this->codSubgrupo;
    }

    /**
     * Set codTipoOrganizacao
     *
     * @param \Entidades\ZgadmOrganizacaoTipo $codTipoOrganizacao
     * @return ZgestSubgrupoOrg
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
