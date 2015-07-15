<?php

namespace Entidades;

use Doctrine\ORM\Mapping as ORM;

/**
 * ZgappNotificacaoLogDest
 *
 * @ORM\Table(name="ZGAPP_NOTIFICACAO_LOG_DEST", indexes={@ORM\Index(name="fk_ZGAPP_NOTIFICACAO_LOG_DEST_1_idx", columns={"COD_LOG"}), @ORM\Index(name="fk_ZGAPP_NOTIFICACAO_LOG_DEST_2_idx", columns={"COD_DESTINATARIO"})})
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
     *   @ORM\JoinColumn(name="COD_DESTINATARIO", referencedColumnName="CODIGO")
     * })
     */
    private $codDestinatario;


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
     * Set codDestinatario
     *
     * @param \Entidades\ZgsegUsuario $codDestinatario
     * @return ZgappNotificacaoLogDest
     */
    public function setCodDestinatario(\Entidades\ZgsegUsuario $codDestinatario = null)
    {
        $this->codDestinatario = $codDestinatario;

        return $this;
    }

    /**
     * Get codDestinatario
     *
     * @return \Entidades\ZgsegUsuario 
     */
    public function getCodDestinatario()
    {
        return $this->codDestinatario;
    }
}
