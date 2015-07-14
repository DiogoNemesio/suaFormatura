<?php

namespace Entidades;

use Doctrine\ORM\Mapping as ORM;

/**
 * ZgappNotificacaoLog
 *
 * @ORM\Table(name="ZGAPP_NOTIFICACAO_LOG", indexes={@ORM\Index(name="fk_ZGAPP_NOTIFICACAO_LOG_1_idx", columns={"COD_NOTIFICACAO"}), @ORM\Index(name="fk_ZGAPP_NOTIFICACAO_LOG_2_idx", columns={"COD_USUARIO"}), @ORM\Index(name="fk_ZGAPP_NOTIFICACAO_LOG_3_idx", columns={"COD_FORMA_ENVIO"})})
 * @ORM\Entity
 */
class ZgappNotificacaoLog
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
     * @var \DateTime
     *
     * @ORM\Column(name="DATA_ENVIO", type="datetime", nullable=false)
     */
    private $dataEnvio;

    /**
     * @var integer
     *
     * @ORM\Column(name="IND_ERRO", type="integer", nullable=false)
     */
    private $indErro;

    /**
     * @var string
     *
     * @ORM\Column(name="ERRO", type="string", length=1000, nullable=true)
     */
    private $erro;

    /**
     * @var \Entidades\ZgappNotificacao
     *
     * @ORM\ManyToOne(targetEntity="Entidades\ZgappNotificacao")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="COD_NOTIFICACAO", referencedColumnName="CODIGO")
     * })
     */
    private $codNotificacao;

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
     * @var \Entidades\ZgappNotificacaoFormaEnvio
     *
     * @ORM\ManyToOne(targetEntity="Entidades\ZgappNotificacaoFormaEnvio")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="COD_FORMA_ENVIO", referencedColumnName="CODIGO")
     * })
     */
    private $codFormaEnvio;


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
     * Set dataEnvio
     *
     * @param \DateTime $dataEnvio
     * @return ZgappNotificacaoLog
     */
    public function setDataEnvio($dataEnvio)
    {
        $this->dataEnvio = $dataEnvio;

        return $this;
    }

    /**
     * Get dataEnvio
     *
     * @return \DateTime 
     */
    public function getDataEnvio()
    {
        return $this->dataEnvio;
    }

    /**
     * Set indErro
     *
     * @param integer $indErro
     * @return ZgappNotificacaoLog
     */
    public function setIndErro($indErro)
    {
        $this->indErro = $indErro;

        return $this;
    }

    /**
     * Get indErro
     *
     * @return integer 
     */
    public function getIndErro()
    {
        return $this->indErro;
    }

    /**
     * Set erro
     *
     * @param string $erro
     * @return ZgappNotificacaoLog
     */
    public function setErro($erro)
    {
        $this->erro = $erro;

        return $this;
    }

    /**
     * Get erro
     *
     * @return string 
     */
    public function getErro()
    {
        return $this->erro;
    }

    /**
     * Set codNotificacao
     *
     * @param \Entidades\ZgappNotificacao $codNotificacao
     * @return ZgappNotificacaoLog
     */
    public function setCodNotificacao(\Entidades\ZgappNotificacao $codNotificacao = null)
    {
        $this->codNotificacao = $codNotificacao;

        return $this;
    }

    /**
     * Get codNotificacao
     *
     * @return \Entidades\ZgappNotificacao 
     */
    public function getCodNotificacao()
    {
        return $this->codNotificacao;
    }

    /**
     * Set codUsuario
     *
     * @param \Entidades\ZgsegUsuario $codUsuario
     * @return ZgappNotificacaoLog
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
     * Set codFormaEnvio
     *
     * @param \Entidades\ZgappNotificacaoFormaEnvio $codFormaEnvio
     * @return ZgappNotificacaoLog
     */
    public function setCodFormaEnvio(\Entidades\ZgappNotificacaoFormaEnvio $codFormaEnvio = null)
    {
        $this->codFormaEnvio = $codFormaEnvio;

        return $this;
    }

    /**
     * Get codFormaEnvio
     *
     * @return \Entidades\ZgappNotificacaoFormaEnvio 
     */
    public function getCodFormaEnvio()
    {
        return $this->codFormaEnvio;
    }
}
