<?php

namespace Entidades;

use Doctrine\ORM\Mapping as ORM;

/**
 * ZgsegUsuarioHistEmail
 *
 * @ORM\Table(name="ZGSEG_USUARIO_HIST_EMAIL", indexes={@ORM\Index(name="fk_ZGSEG_USUARIO_HIST_EMAIL_1_idx", columns={"COD_USUARIO"}), @ORM\Index(name="fk_ZGSEG_USUARIO_HIST_EMAIL_2_idx", columns={"COD_STATUS"})})
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
     * @var string
     *
     * @ORM\Column(name="SENHA_ALTERACAO", type="string", length=200, nullable=true)
     */
    private $senhaAlteracao;

    /**
     * @var integer
     *
     * @ORM\Column(name="IND_CONFIRMADO_ANTERIOR", type="integer", nullable=false)
     */
    private $indConfirmadoAnterior;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="DATA_CONFIRMACAO_ANTERIOR", type="datetime", nullable=true)
     */
    private $dataConfirmacaoAnterior;

    /**
     * @var string
     *
     * @ORM\Column(name="IP_CONFIRMACAO_ANTERIOR", type="string", length=45, nullable=true)
     */
    private $ipConfirmacaoAnterior;

    /**
     * @var integer
     *
     * @ORM\Column(name="IND_CONFIRMADO_NOVO", type="integer", nullable=false)
     */
    private $indConfirmadoNovo;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="DATA_CONFIRMACAO_NOVO", type="datetime", nullable=true)
     */
    private $dataConfirmacaoNovo;

    /**
     * @var string
     *
     * @ORM\Column(name="IP_CONFIRMACAO_NOVO", type="string", length=45, nullable=true)
     */
    private $ipConfirmacaoNovo;

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
     * @var \Entidades\ZgsegHistEmailStatus
     *
     * @ORM\ManyToOne(targetEntity="Entidades\ZgsegHistEmailStatus")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="COD_STATUS", referencedColumnName="CODIGO")
     * })
     */
    private $codStatus;


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
     * Set indConfirmadoAnterior
     *
     * @param integer $indConfirmadoAnterior
     * @return ZgsegUsuarioHistEmail
     */
    public function setIndConfirmadoAnterior($indConfirmadoAnterior)
    {
        $this->indConfirmadoAnterior = $indConfirmadoAnterior;

        return $this;
    }

    /**
     * Get indConfirmadoAnterior
     *
     * @return integer 
     */
    public function getIndConfirmadoAnterior()
    {
        return $this->indConfirmadoAnterior;
    }

    /**
     * Set dataConfirmacaoAnterior
     *
     * @param \DateTime $dataConfirmacaoAnterior
     * @return ZgsegUsuarioHistEmail
     */
    public function setDataConfirmacaoAnterior($dataConfirmacaoAnterior)
    {
        $this->dataConfirmacaoAnterior = $dataConfirmacaoAnterior;

        return $this;
    }

    /**
     * Get dataConfirmacaoAnterior
     *
     * @return \DateTime 
     */
    public function getDataConfirmacaoAnterior()
    {
        return $this->dataConfirmacaoAnterior;
    }

    /**
     * Set ipConfirmacaoAnterior
     *
     * @param string $ipConfirmacaoAnterior
     * @return ZgsegUsuarioHistEmail
     */
    public function setIpConfirmacaoAnterior($ipConfirmacaoAnterior)
    {
        $this->ipConfirmacaoAnterior = $ipConfirmacaoAnterior;

        return $this;
    }

    /**
     * Get ipConfirmacaoAnterior
     *
     * @return string 
     */
    public function getIpConfirmacaoAnterior()
    {
        return $this->ipConfirmacaoAnterior;
    }

    /**
     * Set indConfirmadoNovo
     *
     * @param integer $indConfirmadoNovo
     * @return ZgsegUsuarioHistEmail
     */
    public function setIndConfirmadoNovo($indConfirmadoNovo)
    {
        $this->indConfirmadoNovo = $indConfirmadoNovo;

        return $this;
    }

    /**
     * Get indConfirmadoNovo
     *
     * @return integer 
     */
    public function getIndConfirmadoNovo()
    {
        return $this->indConfirmadoNovo;
    }

    /**
     * Set dataConfirmacaoNovo
     *
     * @param \DateTime $dataConfirmacaoNovo
     * @return ZgsegUsuarioHistEmail
     */
    public function setDataConfirmacaoNovo($dataConfirmacaoNovo)
    {
        $this->dataConfirmacaoNovo = $dataConfirmacaoNovo;

        return $this;
    }

    /**
     * Get dataConfirmacaoNovo
     *
     * @return \DateTime 
     */
    public function getDataConfirmacaoNovo()
    {
        return $this->dataConfirmacaoNovo;
    }

    /**
     * Set ipConfirmacaoNovo
     *
     * @param string $ipConfirmacaoNovo
     * @return ZgsegUsuarioHistEmail
     */
    public function setIpConfirmacaoNovo($ipConfirmacaoNovo)
    {
        $this->ipConfirmacaoNovo = $ipConfirmacaoNovo;

        return $this;
    }

    /**
     * Get ipConfirmacaoNovo
     *
     * @return string 
     */
    public function getIpConfirmacaoNovo()
    {
        return $this->ipConfirmacaoNovo;
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

    /**
     * Set codStatus
     *
     * @param \Entidades\ZgsegHistEmailStatus $codStatus
     * @return ZgsegUsuarioHistEmail
     */
    public function setCodStatus(\Entidades\ZgsegHistEmailStatus $codStatus = null)
    {
        $this->codStatus = $codStatus;

        return $this;
    }

    /**
     * Get codStatus
     *
     * @return \Entidades\ZgsegHistEmailStatus 
     */
    public function getCodStatus()
    {
        return $this->codStatus;
    }
}
