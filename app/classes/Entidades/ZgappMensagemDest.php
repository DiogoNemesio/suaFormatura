<?php

namespace Entidades;

use Doctrine\ORM\Mapping as ORM;

/**
 * ZgappMensagemDest
 *
 * @ORM\Table(name="ZGAPP_MENSAGEM_DEST", indexes={@ORM\Index(name="fk_ZGAPP_MENSAGEM_DEST_1_idx", columns={"COD_DESTINATARIO"}), @ORM\Index(name="fk_ZGAPP_MENSAGEM_DEST_2_idx", columns={"COD_MENSAGEM"})})
 * @ORM\Entity
 */
class ZgappMensagemDest
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
     * @var \Entidades\ZgsegUsuario
     *
     * @ORM\ManyToOne(targetEntity="Entidades\ZgsegUsuario")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="COD_DESTINATARIO", referencedColumnName="CODIGO")
     * })
     */
    private $codDestinatario;

    /**
     * @var \Entidades\ZgappMensagem
     *
     * @ORM\ManyToOne(targetEntity="Entidades\ZgappMensagem")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="COD_MENSAGEM", referencedColumnName="CODIGO")
     * })
     */
    private $codMensagem;


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
     * @return ZgappMensagemDest
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
     * @return ZgappMensagemDest
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
     * Set codDestinatario
     *
     * @param \Entidades\ZgsegUsuario $codDestinatario
     * @return ZgappMensagemDest
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

    /**
     * Set codMensagem
     *
     * @param \Entidades\ZgappMensagem $codMensagem
     * @return ZgappMensagemDest
     */
    public function setCodMensagem(\Entidades\ZgappMensagem $codMensagem = null)
    {
        $this->codMensagem = $codMensagem;

        return $this;
    }

    /**
     * Get codMensagem
     *
     * @return \Entidades\ZgappMensagem 
     */
    public function getCodMensagem()
    {
        return $this->codMensagem;
    }
}
