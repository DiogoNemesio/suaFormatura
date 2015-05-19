<?php

namespace Entidades;

use Doctrine\ORM\Mapping as ORM;

/**
 * ZgappMenuHistAcesso
 *
 * @ORM\Table(name="ZGAPP_MENU_HIST_ACESSO", indexes={@ORM\Index(name="fk_ZG_MENU_HIST_ACESSO_1_idx", columns={"COD_MENU"}), @ORM\Index(name="fk_ZG_MENU_HIST_ACESSO_2_idx", columns={"COD_USUARIO"}), @ORM\Index(name="ZGAPP_MENU_HIST_ACESSO_uk01", columns={"COD_MENU", "COD_USUARIO"})})
 * @ORM\Entity
 */
class ZgappMenuHistAcesso
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
     * @ORM\Column(name="DATA_ULT_ACESSO", type="datetime", nullable=false)
     */
    private $dataUltAcesso;

    /**
     * @var integer
     *
     * @ORM\Column(name="QUANTIDADE", type="integer", nullable=false)
     */
    private $quantidade;

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
     * @var \Entidades\ZgsegUsuario
     *
     * @ORM\ManyToOne(targetEntity="Entidades\ZgsegUsuario")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="COD_USUARIO", referencedColumnName="CODIGO")
     * })
     */
    private $codUsuario;


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
     * Set dataUltAcesso
     *
     * @param \DateTime $dataUltAcesso
     * @return ZgappMenuHistAcesso
     */
    public function setDataUltAcesso($dataUltAcesso)
    {
        $this->dataUltAcesso = $dataUltAcesso;

        return $this;
    }

    /**
     * Get dataUltAcesso
     *
     * @return \DateTime 
     */
    public function getDataUltAcesso()
    {
        return $this->dataUltAcesso;
    }

    /**
     * Set quantidade
     *
     * @param integer $quantidade
     * @return ZgappMenuHistAcesso
     */
    public function setQuantidade($quantidade)
    {
        $this->quantidade = $quantidade;

        return $this;
    }

    /**
     * Get quantidade
     *
     * @return integer 
     */
    public function getQuantidade()
    {
        return $this->quantidade;
    }

    /**
     * Set codMenu
     *
     * @param \Entidades\ZgappMenu $codMenu
     * @return ZgappMenuHistAcesso
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
     * Set codUsuario
     *
     * @param \Entidades\ZgsegUsuario $codUsuario
     * @return ZgappMenuHistAcesso
     */
    public function setCodUsuario(\Entidades\ZgsegUsuario $codUsuario = null)
    {
        $this->codUsuario = $codUsuario;

        return $this;
    }

    /**
     * Get codUsuario
     *
     * @return \Entidades\ZgsegUsuario 
     */
    public function getCodUsuario()
    {
        return $this->codUsuario;
    }
}
