<?php

namespace Entidades;

use Doctrine\ORM\Mapping as ORM;

/**
 * ZgappMenuPerfil
 *
 * @ORM\Table(name="ZGAPP_MENU_PERFIL", uniqueConstraints={@ORM\UniqueConstraint(name="ZGAPP_MENU_PERFIL_UK01", columns={"COD_MENU", "COD_PERFIL", "COD_TIPO_ORGANIZACAO"})}, indexes={@ORM\Index(name="fk_ZG_MENU_PERFIL_1_idx", columns={"COD_MENU"}), @ORM\Index(name="fk_ZG_MENU_PERFIL_2_idx", columns={"COD_PERFIL"}), @ORM\Index(name="fk_ZGAPP_MENU_PERFIL_1_idx", columns={"COD_TIPO_ORGANIZACAO"})})
 * @ORM\Entity
 */
class ZgappMenuPerfil
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
     * @var integer
     *
     * @ORM\Column(name="ORDEM", type="integer", nullable=false)
     */
    private $ordem;

    /**
     * @var \Entidades\ZgappMenu
     *
     * @ORM\ManyToOne(targetEntity="Entidades\ZgappMenu")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="COD_MENU", referencedColumnName="CODIGO")
     * })
     */
    private $codMenu;

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
     * Set ordem
     *
     * @param integer $ordem
     * @return ZgappMenuPerfil
     */
    public function setOrdem($ordem)
    {
        $this->ordem = $ordem;

        return $this;
    }

    /**
     * Get ordem
     *
     * @return integer 
     */
    public function getOrdem()
    {
        return $this->ordem;
    }

    /**
     * Set codMenu
     *
     * @param \Entidades\ZgappMenu $codMenu
     * @return ZgappMenuPerfil
     */
    public function setCodMenu(\Entidades\ZgappMenu $codMenu = null)
    {
        $this->codMenu = $codMenu;

        return $this;
    }

    /**
     * Get codMenu
     *
     * @return \Entidades\ZgappMenu 
     */
    public function getCodMenu()
    {
        return $this->codMenu;
    }

    /**
     * Set codPerfil
     *
     * @param \Entidades\ZgsegPerfil $codPerfil
     * @return ZgappMenuPerfil
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

    /**
     * Set codTipoOrganizacao
     *
     * @param \Entidades\ZgadmOrganizacaoTipo $codTipoOrganizacao
     * @return ZgappMenuPerfil
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
