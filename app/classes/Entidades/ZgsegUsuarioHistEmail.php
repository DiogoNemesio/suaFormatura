<?php

namespace Entidades;

use Doctrine\ORM\Mapping as ORM;

/**
 * ZgsegUsuarioHistEmail
 *
 * @ORM\Table(name="ZGSEG_USUARIO_HIST_EMAIL", indexes={@ORM\Index(name="fk_ZGSEG_USUARIO_HIST_EMAIL_1_idx", columns={"COD_USUARIO"})})
 * @ORM\Entity
 */
class ZgsegUsuarioHistEmail
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
     * @ORM\Column(name="EMAIL_ANTERIOR", type="string", length=200, nullable=false)
     */
    private $emailAnterior;

    /**
     * @var string
     *
     * @ORM\Column(name="EMAIL_NOVO", type="string", length=200, nullable=false)
     */
    private $emailNovo;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="DATA_ALTERACAO", type="datetime", nullable=false)
     */
    private $dataAlteracao;

    /**
     * @var integer
     *
     * @ORM\Column(name="IND_CONFIRMADO", type="integer", nullable=false)
     */
    private $indConfirmado;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="DATA_CONFIRMACAO", type="datetime", nullable=true)
     */
    private $dataConfirmacao;

    /**
     * @var string
     *
     * @ORM\Column(name="IP_CONFIRMACAO", type="string", length=45, nullable=true)
     */
    private $ipConfirmacao;

    /**
     * @var string
     *
     * @ORM\Column(name="SENHA_ALTERACAO", type="string", length=200, nullable=true)
     */
    private $senhaAlteracao;

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
     * Set emailAnterior
     *
     * @param string $emailAnterior
     * @return ZgsegUsuarioHistEmail
     */
    public function setEmailAnterior($emailAnterior)
    {
        $this->emailAnterior = $emailAnterior;

        return $this;
    }

    /**
     * Get emailAnterior
     *
     * @return string 
     */
    public function getEmailAnterior()
    {
        return $this->emailAnterior;
    }

    /**
     * Set emailNovo
     *
     * @param string $emailNovo
     * @return ZgsegUsuarioHistEmail
     */
    public function setEmailNovo($emailNovo)
    {
        $this->emailNovo = $emailNovo;

        return $this;
    }

    /**
     * Get emailNovo
     *
     * @return string 
     */
    public function getEmailNovo()
    {
        return $this->emailNovo;
    }

    /**
     * Set dataAlteracao
     *
     * @param \DateTime $dataAlteracao
     * @return ZgsegUsuarioHistEmail
     */
    public function setDataAlteracao($dataAlteracao)
    {
        $this->dataAlteracao = $dataAlteracao;

        return $this;
    }

    /**
     * Get dataAlteracao
     *
     * @return \DateTime 
     */
    public function getDataAlteracao()
    {
        return $this->dataAlteracao;
    }

    /**
     * Set indConfirmado
     *
     * @param integer $indConfirmado
     * @return ZgsegUsuarioHistEmail
     */
    public function setIndConfirmado($indConfirmado)
    {
        $this->indConfirmado = $indConfirmado;

        return $this;
    }

    /**
     * Get indConfirmado
     *
     * @return integer 
     */
    public function getIndConfirmado()
    {
        return $this->indConfirmado;
    }

    /**
     * Set dataConfirmacao
     *
     * @param \DateTime $dataConfirmacao
     * @return ZgsegUsuarioHistEmail
     */
    public function setDataConfirmacao($dataConfirmacao)
    {
        $this->dataConfirmacao = $dataConfirmacao;

        return $this;
    }

    /**
     * Get dataConfirmacao
     *
     * @return \DateTime 
     */
    public function getDataConfirmacao()
    {
        return $this->dataConfirmacao;
    }

    /**
     * Set ipConfirmacao
     *
     * @param string $ipConfirmacao
     * @return ZgsegUsuarioHistEmail
     */
    public function setIpConfirmacao($ipConfirmacao)
    {
        $this->ipConfirmacao = $ipConfirmacao;

        return $this;
    }

    /**
     * Get ipConfirmacao
     *
     * @return string 
     */
    public function getIpConfirmacao()
    {
        return $this->ipConfirmacao;
    }

    /**
     * Set senhaAlteracao
     *
     * @param string $senhaAlteracao
     * @return ZgsegUsuarioHistEmail
     */
    public function setSenhaAlteracao($senhaAlteracao)
    {
        $this->senhaAlteracao = $senhaAlteracao;

        return $this;
    }

    /**
     * Get senhaAlteracao
     *
     * @return string 
     */
    public function getSenhaAlteracao()
    {
        return $this->senhaAlteracao;
    }

    /**
     * Set codUsuario
     *
     * @param \Entidades\ZgsegUsuario $codUsuario
     * @return ZgsegUsuarioHistEmail
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
