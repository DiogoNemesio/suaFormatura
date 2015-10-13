<?php

namespace Entidades;

use Doctrine\ORM\Mapping as ORM;

/**
 * ZgappNotificacaoLog
 *
 * @ORM\Table(name="ZGAPP_NOTIFICACAO_LOG", indexes={@ORM\Index(name="fk_ZGAPP_NOTIFICACAO_LOG_1_idx", columns={"COD_NOTIFICACAO"}), @ORM\Index(name="fk_ZGAPP_NOTIFICACAO_LOG_3_idx", columns={"COD_FORMA_ENVIO"}), @ORM\Index(name="ZGAPP_NOTIFICACAO_LOG_IX01", columns={"COD_NOTIFICACAO", "COD_FORMA_ENVIO"})})
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
     * @var integer
     *
     * @ORM\Column(name="IND_PROCESSADA", type="integer", nullable=true)
     */
    private $indProcessada;

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
     * Set indProcessada
     *
     * @param integer $indProcessada
     * @return ZgappNotificacaoLog
     */
    public function setIndProcessada($indProcessada)
    {
        $this->indProcessada = $indProcessada;

        return $this;
    }

    /**
     * Get indProcessada
     *
     * @return integer 
     */
    public function getIndProcessada()
    {
        return $this->indProcessada;
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
