<?php

namespace Entidades;

use Doctrine\ORM\Mapping as ORM;

/**
 * ZgsegUsuarioResponsavel
 *
 * @ORM\Table(name="ZGSEG_USUARIO_RESPONSAVEL", indexes={@ORM\Index(name="fk_ZGSEG_USUARIO_RESPONSAVEL_1_idx", columns={"COD_FORMANDO"}), @ORM\Index(name="fk_ZGSEG_USUARIO_RESPONSAVEL_2_idx", columns={"COD_RESPONSAVEL"})})
 * @ORM\Entity
 */
class ZgsegUsuarioResponsavel
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
     * @var \Entidades\ZgsegUsuario
     *
     * @ORM\ManyToOne(targetEntity="Entidades\ZgsegUsuario")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="COD_FORMANDO", referencedColumnName="CODIGO")
     * })
     */
    private $codFormando;

    /**
     * @var \Entidades\ZgsegUsuario
     *
     * @ORM\ManyToOne(targetEntity="Entidades\ZgsegUsuario")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="COD_RESPONSAVEL", referencedColumnName="CODIGO")
     * })
     */
    private $codResponsavel;


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
     * Set codFormando
     *
     * @param \Entidades\ZgsegUsuario $codFormando
     * @return ZgsegUsuarioResponsavel
     */
    public function setCodFormando(\Entidades\ZgsegUsuario $codFormando = null)
    {
        $this->codFormando = $codFormando;

        return $this;
    }

    /**
     * Get codFormando
     *
     * @return \Entidades\ZgsegUsuario 
     */
    public function getCodFormando()
    {
        return $this->codFormando;
    }

    /**
     * Set codResponsavel
     *
     * @param \Entidades\ZgsegUsuario $codResponsavel
     * @return ZgsegUsuarioResponsavel
     */
    public function setCodResponsavel(\Entidades\ZgsegUsuario $codResponsavel = null)
    {
        $this->codResponsavel = $codResponsavel;

        return $this;
    }

    /**
     * Get codResponsavel
     *
     * @return \Entidades\ZgsegUsuario 
     */
    public function getCodResponsavel()
    {
        return $this->codResponsavel;
    }
}
