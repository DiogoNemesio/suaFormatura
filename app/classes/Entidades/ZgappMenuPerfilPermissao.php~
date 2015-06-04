<?php

namespace Entidades;

use Doctrine\ORM\Mapping as ORM;

/**
 * ZgappMenuPerfilPermissao
 *
 * @ORM\Table(name="ZGAPP_MENU_PERFIL_PERMISSAO", indexes={@ORM\Index(name="fk_ZG_MENU_PERFIL_PERMISSAO_1_idx", columns={"COD_MENU_PERFIL"}), @ORM\Index(name="fk_ZG_MENU_PERFIL_PERMISSAO_2_idx", columns={"COD_PERMISSAO"})})
 * @ORM\Entity
 */
class ZgappMenuPerfilPermissao
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
     * @var \Entidades\ZgappMenuPerfil
     *
     * @ORM\ManyToOne(targetEntity="Entidades\ZgappMenuPerfil")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="COD_MENU_PERFIL", referencedColumnName="CODIGO")
     * })
     */
    private $codMenuPerfil;

    /**
     * @var \Entidades\ZgappPermissao
     *
     * @ORM\ManyToOne(targetEntity="Entidades\ZgappPermissao")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="COD_PERMISSAO", referencedColumnName="CODIGO")
     * })
     */
    private $codPermissao;


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
     * Set codMenuPerfil
     *
     * @param \Entidades\ZgappMenuPerfil $codMenuPerfil
     * @return ZgappMenuPerfilPermissao
     */
    public function setCodMenuPerfil(\Entidades\ZgappMenuPerfil $codMenuPerfil = null)
    {
        $this->codMenuPerfil = $codMenuPerfil;

        return $this;
    }

    /**
     * Get codMenuPerfil
     *
     * @return \Entidades\ZgappMenuPerfil 
     */
    public function getCodMenuPerfil()
    {
        return $this->codMenuPerfil;
    }

    /**
     * Set codPermissao
     *
     * @param \Entidades\ZgappPermissao $codPermissao
     * @return ZgappMenuPerfilPermissao
     */
    public function setCodPermissao(\Entidades\ZgappPermissao $codPermissao = null)
    {
        $this->codPermissao = $codPermissao;

        return $this;
    }

    /**
     * Get codPermissao
     *
     * @return \Entidades\ZgappPermissao 
     */
    public function getCodPermissao()
    {
        return $this->codPermissao;
    }
}
