<?php

namespace Entidades;

use Doctrine\ORM\Mapping as ORM;

/**
 * ZgsegUsuarioRecSenha
 *
 * @ORM\Table(name="ZGSEG_USUARIO_REC_SENHA", indexes={@ORM\Index(name="fk_ZGSEG_USUARIO_REC_SENHA_1_idx", columns={"COD_USUARIO"}), @ORM\Index(name="fk_ZGSEG_USUARIO_REC_SENHA_2_idx", columns={"COD_STATUS"})})
 * @ORM\Entity
 */
class ZgsegUsuarioRecSenha
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
     * @ORM\Column(name="DATA_SOLICITACAO", type="datetime", nullable=false)
     */
    private $dataSolicitacao;

    /**
     * @var string
     *
     * @ORM\Column(name="IP_SOLICITACAO", type="string", length=60, nullable=true)
     */
    private $ipSolicitacao;

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
     * Set dataSolicitacao
     *
     * @param \DateTime $dataSolicitacao
     * @return ZgsegUsuarioRecSenha
     */
    public function setDataSolicitacao($dataSolicitacao)
    {
        $this->dataSolicitacao = $dataSolicitacao;

        return $this;
    }

    /**
     * Get dataSolicitacao
     *
     * @return \DateTime 
     */
    public function getDataSolicitacao()
    {
        return $this->dataSolicitacao;
    }

    /**
     * Set ipSolicitacao
     *
     * @param string $ipSolicitacao
     * @return ZgsegUsuarioRecSenha
     */
    public function setIpSolicitacao($ipSolicitacao)
    {
        $this->ipSolicitacao = $ipSolicitacao;

        return $this;
    }

    /**
     * Get ipSolicitacao
     *
     * @return string 
     */
    public function getIpSolicitacao()
    {
        return $this->ipSolicitacao;
    }

    /**
     * Set senhaAlteracao
     *
     * @param string $senhaAlteracao
     * @return ZgsegUsuarioRecSenha
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
     * @return ZgsegUsuarioRecSenha
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
     * @return ZgsegUsuarioRecSenha
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
