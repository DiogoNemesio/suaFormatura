<?php

namespace Entidades;

use Doctrine\ORM\Mapping as ORM;

/**
 * ZgappNotificacaoUsuario
 *
 * @ORM\Table(name="ZGAPP_NOTIFICACAO_USUARIO", indexes={@ORM\Index(name="fk_ZGAPP_NOTIFICACAO_USUARIO_1_idx", columns={"COD_NOTIFICACAO"}), @ORM\Index(name="fk_ZGAPP_NOTIFICACAO_USUARIO_2_idx", columns={"COD_USUARIO"})})
 * @ORM\Entity
 */
class ZgappNotificacaoUsuario
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
     * @ORM\Column(name="IND_LIDA", type="integer", nullable=false)
     */
    private $indLida;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="DATA_LEITURA", type="datetime", nullable=true)
     */
    private $dataLeitura;

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
     * Get codigo
     *
     * @return integer 
     */
    public function getCodigo()
    {
        return $this->codigo;
    }

    /**
     * Set indLida
     *
     * @param integer $indLida
     * @return ZgappNotificacaoUsuario
     */
    public function setIndLida($indLida)
    {
        $this->indLida = $indLida;

        return $this;
    }

    /**
     * Get indLida
     *
     * @return integer 
     */
    public function getIndLida()
    {
        return $this->indLida;
    }

    /**
     * Set dataLeitura
     *
     * @param \DateTime $dataLeitura
     * @return ZgappNotificacaoUsuario
     */
    public function setDataLeitura($dataLeitura)
    {
        $this->dataLeitura = $dataLeitura;

        return $this;
    }

    /**
     * Get dataLeitura
     *
     * @return \DateTime 
     */
    public function getDataLeitura()
    {
        return $this->dataLeitura;
    }

    /**
     * Set codNotificacao
     *
     * @param \Entidades\ZgappNotificacao $codNotificacao
     * @return ZgappNotificacaoUsuario
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
     * @return ZgappNotificacaoUsuario
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
