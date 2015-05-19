<?php

namespace Entidades;

use Doctrine\ORM\Mapping as ORM;

/**
 * ZgsegUsuarioFormatura
 *
 * @ORM\Table(name="ZGSEG_USUARIO_FORMATURA", indexes={@ORM\Index(name="USUARIO_EMPRESA_IX01", columns={"COD_USUARIO"}), @ORM\Index(name="fk_ZG_USUARIO_EMPRESA_1_idx", columns={"COD_PERFIL"})})
 * @ORM\Entity
 */
class ZgsegUsuarioFormatura
{
    /**
     * @var integer
     *
     * @ORM\Column(name="COD_TURMA", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="NONE")
     */
    private $codTurma;

    /**
     * @var \Entidades\ZgsegUsuario
     *
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="NONE")
     * @ORM\OneToOne(targetEntity="Entidades\ZgsegUsuario")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="COD_USUARIO", referencedColumnName="CODIGO")
     * })
     */
    private $codUsuario;

    /**
     * @var \Entidades\ZgsegPerfil
     *
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="NONE")
     * @ORM\OneToOne(targetEntity="Entidades\ZgsegPerfil")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="COD_PERFIL", referencedColumnName="CODIGO")
     * })
     */
    private $codPerfil;


    /**
     * Set codTurma
     *
     * @param integer $codTurma
     * @return ZgsegUsuarioFormatura
     */
    public function setCodTurma($codTurma)
    {
        $this->codTurma = $codTurma;

        return $this;
    }

    /**
     * Get codTurma
     *
     * @return integer 
     */
    public function getCodTurma()
    {
        return $this->codTurma;
    }

    /**
     * Set codUsuario
     *
     * @param \Entidades\ZgsegUsuario $codUsuario
     * @return ZgsegUsuarioFormatura
     */
    public function setCodUsuario(\Entidades\ZgsegUsuario $codUsuario)
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

    /**
     * Set codPerfil
     *
     * @param \Entidades\ZgsegPerfil $codPerfil
     * @return ZgsegUsuarioFormatura
     */
    public function setCodPerfil(\Entidades\ZgsegPerfil $codPerfil)
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
