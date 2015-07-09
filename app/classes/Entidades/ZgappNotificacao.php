<?php

namespace Entidades;

use Doctrine\ORM\Mapping as ORM;

/**
 * ZgappNotificacao
 *
 * @ORM\Table(name="ZGAPP_NOTIFICACAO", indexes={@ORM\Index(name="fk_ZGAPP_NOTIFICACAO_1_idx", columns={"COD_USUARIO"}), @ORM\Index(name="fk_ZGAPP_NOTIFICACAO_2_idx", columns={"COD_TIPO_MENSAGEM"}), @ORM\Index(name="fk_ZGAPP_NOTIFICACAO_3_idx", columns={"COD_TIPO_DESTINATARIO"}), @ORM\Index(name="fk_ZGAPP_NOTIFICACAO_4_idx", columns={"COD_TEMPLATE"})})
 * @ORM\Entity
 */
class ZgappNotificacao
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
     * @ORM\Column(name="DATA", type="datetime", nullable=false)
     */
    private $data;

    /**
     * @var string
     *
     * @ORM\Column(name="MENSAGEM", type="string", length=1000, nullable=true)
     */
    private $mensagem;

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
     * @var \Entidades\ZgappNotificacaoMensTipo
     *
     * @ORM\ManyToOne(targetEntity="Entidades\ZgappNotificacaoMensTipo")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="COD_TIPO_MENSAGEM", referencedColumnName="CODIGO")
     * })
     */
    private $codTipoMensagem;

    /**
     * @var \Entidades\ZgappNotificacaoDestTipo
     *
     * @ORM\ManyToOne(targetEntity="Entidades\ZgappNotificacaoDestTipo")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="COD_TIPO_DESTINATARIO", referencedColumnName="CODIGO")
     * })
     */
    private $codTipoDestinatario;

    /**
     * @var \Entidades\ZgappNotificacaoTemplate
     *
     * @ORM\ManyToOne(targetEntity="Entidades\ZgappNotificacaoTemplate")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="COD_TEMPLATE", referencedColumnName="CODIGO")
     * })
     */
    private $codTemplate;


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
     * Set data
     *
     * @param \DateTime $data
     * @return ZgappNotificacao
     */
    public function setData($data)
    {
        $this->data = $data;

        return $this;
    }

    /**
     * Get data
     *
     * @return \DateTime 
     */
    public function getData()
    {
        return $this->data;
    }

    /**
     * Set mensagem
     *
     * @param string $mensagem
     * @return ZgappNotificacao
     */
    public function setMensagem($mensagem)
    {
        $this->mensagem = $mensagem;

        return $this;
    }

    /**
     * Get mensagem
     *
     * @return string 
     */
    public function getMensagem()
    {
        return $this->mensagem;
    }

    /**
     * Set codUsuario
     *
     * @param \Entidades\ZgsegUsuario $codUsuario
     * @return ZgappNotificacao
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
     * Set codTipoMensagem
     *
     * @param \Entidades\ZgappNotificacaoMensTipo $codTipoMensagem
     * @return ZgappNotificacao
     */
    public function setCodTipoMensagem(\Entidades\ZgappNotificacaoMensTipo $codTipoMensagem = null)
    {
        $this->codTipoMensagem = $codTipoMensagem;

        return $this;
    }

    /**
     * Get codTipoMensagem
     *
     * @return \Entidades\ZgappNotificacaoMensTipo 
     */
    public function getCodTipoMensagem()
    {
        return $this->codTipoMensagem;
    }

    /**
     * Set codTipoDestinatario
     *
     * @param \Entidades\ZgappNotificacaoDestTipo $codTipoDestinatario
     * @return ZgappNotificacao
     */
    public function setCodTipoDestinatario(\Entidades\ZgappNotificacaoDestTipo $codTipoDestinatario = null)
    {
        $this->codTipoDestinatario = $codTipoDestinatario;

        return $this;
    }

    /**
     * Get codTipoDestinatario
     *
     * @return \Entidades\ZgappNotificacaoDestTipo 
     */
    public function getCodTipoDestinatario()
    {
        return $this->codTipoDestinatario;
    }

    /**
     * Set codTemplate
     *
     * @param \Entidades\ZgappNotificacaoTemplate $codTemplate
     * @return ZgappNotificacao
     */
    public function setCodTemplate(\Entidades\ZgappNotificacaoTemplate $codTemplate = null)
    {
        $this->codTemplate = $codTemplate;

        return $this;
    }

    /**
     * Get codTemplate
     *
     * @return \Entidades\ZgappNotificacaoTemplate 
     */
    public function getCodTemplate()
    {
        return $this->codTemplate;
    }
}
