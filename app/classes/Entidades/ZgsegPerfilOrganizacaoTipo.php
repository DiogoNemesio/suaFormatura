<?php

namespace Entidades;

use Doctrine\ORM\Mapping as ORM;

/**
 * ZgsegPerfilOrganizacaoTipo
 *
 * @ORM\Table(name="ZGSEG_PERFIL_ORGANIZACAO_TIPO", indexes={@ORM\Index(name="fk_ZGSEG_PERFIL_TIPO_ORGANIZACAO_1_idx", columns={"COD_TIPO_ORGANIZACAO"}), @ORM\Index(name="fk_ZGSEG_PERFIL_TIPO_ORGANIZACAO_2_idx", columns={"COD_PERFIL"})})
 * @ORM\Entity
 */
class ZgsegPerfilOrganizacaoTipo
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
     * @var \Entidades\ZgadmOrganizacaoTipo
     *
     * @ORM\ManyToOne(targetEntity="Entidades\ZgadmOrganizacaoTipo")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="COD_TIPO_ORGANIZACAO", referencedColumnName="CODIGO")
     * })
     */
    private $codTipoOrganizacao;

    /**
     * @var \Entidades\ZgsegPerfil
     *
     * @ORM\ManyToOne(targetEntity="Entidades\ZgsegPerfil")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="COD_PERFIL", referencedColumnName="CODIGO")
     * })
     */
    private $codPerfil;


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
     * Set codTipoOrganizacao
     *
     * @param \Entidades\ZgadmOrganizacaoTipo $codTipoOrganizacao
     * @return ZgsegPerfilOrganizacaoTipo
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

    /**
     * Set codPerfil
     *
     * @param \Entidades\ZgsegPerfil $codPerfil
     * @return ZgsegPerfilOrganizacaoTipo
     */
    public function setCodPerfil(\Entidades\ZgsegPerfil $codPerfil = null)
    {
        $this->codPerfil = $codPerfil;

        return $this;
    }

    /**
     * Get codPerfil
     *
     * @return \Entidades\ZgsegPerfil 
     */
    public function getCodPerfil()
    {
        return $this->codPerfil;
    }
}
