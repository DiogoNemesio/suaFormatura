<?php

namespace Entidades;

use Doctrine\ORM\Mapping as ORM;

/**
 * ZgappNotificacaoLogDest
 *
 * @ORM\Table(name="ZGAPP_NOTIFICACAO_LOG_DEST", indexes={@ORM\Index(name="fk_ZGAPP_NOTIFICACAO_LOG_DEST_1_idx", columns={"COD_LOG"}), @ORM\Index(name="fk_ZGAPP_NOTIFICACAO_LOG_DEST_3_idx", columns={"COD_PESSOA"}), @ORM\Index(name="fk_ZGAPP_NOTIFICACAO_LOG_DEST_2_idx", columns={"COD_USUARIO"})})
 * @ORM\Entity
 */
class ZgappNotificacaoLogDest
{
    /**
     * @var integer
     *
     * @ORM\Column(name="CODIGO", type="bigint", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $codigo;

    /**
     * @var string
     *
     * @ORM\Column(name="EMAIL", type="string", length=200, nullable=true)
     */
    private $email;

    /**
     * @var \Entidades\ZgappNotificacaoLog
     *
     * @ORM\ManyToOne(targetEntity="Entidades\ZgappNotificacaoLog")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="COD_LOG", referencedColumnName="CODIGO")
     * })
     */
    private $codLog;

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
     * @var \Entidades\ZgfinPessoa
     *
     * @ORM\ManyToOne(targetEntity="Entidades\ZgfinPessoa")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="COD_PESSOA", referencedColumnName="CODIGO")
     * })
     */
    private $codPessoa;


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
     * Set email
     *
     * @param string $email
     * @return ZgappNotificacaoLogDest
     */
    public function setEmail($email)
    {
        $this->email = $email;

        return $this;
    }

    /**
     * Get email
     *
     * @return string 
     */
    public function getEmail()
    {
        return $this->email;
    }

    /**
     * Set codLog
     *
     * @param \Entidades\ZgappNotificacaoLog $codLog
     * @return ZgappNotificacaoLogDest
     */
    public function setCodLog(\Entidades\ZgappNotificacaoLog $codLog = null)
    {
        $this->codLog = $codLog;

        return $this;
    }

    /**
     * Get codLog
     *
     * @return \Entidades\ZgappNotificacaoLog 
     */
    public function getCodLog()
    {
        return $this->codLog;
    }

    /**
     * Set codUsuario
     *
     * @param \Entidades\ZgsegUsuario $codUsuario
     * @return ZgappNotificacaoLogDest
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

    /**
     * Set codPessoa
     *
     * @param \Entidades\ZgfinPessoa $codPessoa
     * @return ZgappNotificacaoLogDest
     */
    public function setCodPessoa(\Entidades\ZgfinPessoa $codPessoa = null)
    {
        $this->codPessoa = $codPessoa;

        return $this;
    }

    /**
     * Get codPessoa
     *
     * @return \Entidades\ZgfinPessoa 
     */
    public function getCodPessoa()
    {
        return $this->codPessoa;
    }
}
