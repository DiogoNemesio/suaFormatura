<?php

namespace Entidades;

use Doctrine\ORM\Mapping as ORM;

/**
 * ZgappMenuPermissao
 *
 * @ORM\Table(name="ZGAPP_MENU_PERMISSAO", uniqueConstraints={@ORM\UniqueConstraint(name="COD_MENU_UNIQUE", columns={"COD_MENU"}), @ORM\UniqueConstraint(name="COD_PERMISSAO_UNIQUE", columns={"COD_PERMISSAO"})})
 * @ORM\Entity
 */
class ZgappMenuPermissao
{
    /**
     * @var string
     *
     * @ORM\Column(name="CODIGO", type="string", length=45, nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $codigo;

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
     * @return string 
     */
    public function getCodigo()
    {
        return $this->codigo;
    }

    /**
     * Set codMenu
     *
     * @param \Entidades\ZgappMenu $codMenu
     * @return ZgappMenuPermissao
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
     * Set codPermissao
     *
     * @param \Entidades\ZgappPermissao $codPermissao
     * @return ZgappMenuPermissao
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
