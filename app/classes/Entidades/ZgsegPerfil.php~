<?php

namespace Entidades;

use Doctrine\ORM\Mapping as ORM;

/**
 * ZgsegPerfil
 *
 * @ORM\Table(name="ZGSEG_PERFIL", indexes={@ORM\Index(name="fk_ZGSEG_PERFIL_2_idx", columns={"NOME"}), @ORM\Index(name="fk_ZGSEG_PERFIL_1_idx", columns={"COD_TIPO_USUARIO"})})
 * @ORM\Entity
 */
class ZgsegPerfil
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
     * @ORM\Column(name="NOME", type="string", length=60, nullable=false)
     */
    private $nome;

    /**
     * @var integer
     *
     * @ORM\Column(name="IND_ATIVO", type="integer", nullable=false)
     */
    private $indAtivo;

    /**
     * @var \Entidades\ZgsegPerfilUsuarioTipo
     *
     * @ORM\ManyToOne(targetEntity="Entidades\ZgsegPerfilUsuarioTipo")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="COD_TIPO_USUARIO", referencedColumnName="CODIGO")
     * })
     */
    private $codTipoUsuario;


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
     * Set nome
     *
     * @param string $nome
     * @return ZgsegPerfil
     */
    public function setNome($nome)
    {
        $this->nome = $nome;

        return $this;
    }

    /**
     * Get nome
     *
     * @return string 
     */
    public function getNome()
    {
        return $this->nome;
    }

    /**
     * Set indAtivo
     *
     * @param integer $indAtivo
     * @return ZgsegPerfil
     */
    public function setIndAtivo($indAtivo)
    {
        $this->indAtivo = $indAtivo;

        return $this;
    }

    /**
     * Get indAtivo
     *
     * @return integer 
     */
    public function getIndAtivo()
    {
        return $this->indAtivo;
    }

    /**
     * Set codTipoUsuario
     *
     * @param \Entidades\ZgsegPerfilUsuarioTipo $codTipoUsuario
     * @return ZgsegPerfil
     */
    public function setCodTipoUsuario(\Entidades\ZgsegPerfilUsuarioTipo $codTipoUsuario = null)
    {
        $this->codTipoUsuario = $codTipoUsuario;

        return $this;
    }

    /**
     * Get codTipoUsuario
     *
     * @return \Entidades\ZgsegPerfilUsuarioTipo 
     */
    public function getCodTipoUsuario()
    {
        return $this->codTipoUsuario;
    }
}
