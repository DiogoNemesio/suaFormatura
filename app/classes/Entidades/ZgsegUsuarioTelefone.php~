<?php

namespace Entidades;

use Doctrine\ORM\Mapping as ORM;

/**
 * ZgsegUsuarioTelefone
 *
 * @ORM\Table(name="ZGSEG_USUARIO_TELEFONE", indexes={@ORM\Index(name="fk_ZFSEG_USUARIO_TELEFONE_1_idx", columns={"COD_TIPO_TELEFONE"}), @ORM\Index(name="fk_ZFSEG_USUARIO_TELEFONE_2_idx", columns={"COD_USUARIO"})})
 * @ORM\Entity
 */
class ZgsegUsuarioTelefone
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
     * @var string
     *
     * @ORM\Column(name="TELEFONE", type="string", length=11, nullable=false)
     */
    private $telefone;

    /**
     * @var \Entidades\ZgappTipoTelefone
     *
     * @ORM\ManyToOne(targetEntity="Entidades\ZgappTipoTelefone")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="COD_TIPO_TELEFONE", referencedColumnName="CODIGO")
     * })
     */
    private $codTipoTelefone;

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
     * Set telefone
     *
     * @param string $telefone
     * @return ZgsegUsuarioTelefone
     */
    public function setTelefone($telefone)
    {
        $this->telefone = $telefone;

        return $this;
    }

    /**
     * Get telefone
     *
     * @return string 
     */
    public function getTelefone()
    {
        return $this->telefone;
    }

    /**
     * Set codTipoTelefone
     *
     * @param \Entidades\ZgappTipoTelefone $codTipoTelefone
     * @return ZgsegUsuarioTelefone
     */
    public function setCodTipoTelefone(\Entidades\ZgappTipoTelefone $codTipoTelefone = null)
    {
        $this->codTipoTelefone = $codTipoTelefone;

        return $this;
    }

    /**
     * Get codTipoTelefone
     *
     * @return \Entidades\ZgappTipoTelefone 
     */
    public function getCodTipoTelefone()
    {
        return $this->codTipoTelefone;
    }

    /**
     * Set codUsuario
     *
     * @param \Entidades\ZgsegUsuario $codUsuario
     * @return ZgsegUsuarioTelefone
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
