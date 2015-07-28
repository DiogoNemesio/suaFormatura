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
     * @ORM\Column(name="ASSUNTO", type="string", length=60, nullable=true)
     */
    private $assunto;

    /**
     * @var string
     *
     * @ORM\Column(name="MENSAGEM", type="string", length=1000, nullable=true)
     */
    private $mensagem;

    /**
     * @var integer
     *
     * @ORM\Column(name="IND_VIA_SISTEMA", type="integer", nullable=false)
     */
    private $indViaSistema;

    /**
     * @var integer
     *
     * @ORM\Column(name="IND_VIA_EMAIL", type="integer", nullable=false)
     */
    private $indViaEmail;

    /**
     * @var integer
     *
     * @ORM\Column(name="IND_VIA_WA", type="integer", nullable=false)
     */
    private $indViaWa;

    /**
     * @var integer
     *
     * @ORM\Column(name="IND_PROCESSADA", type="integer", nullable=false)
     */
    private $indProcessada;

    /**
     * @var string
     *
     * @ORM\Column(name="EMAIL", type="string", length=200, nullable=true)
     */
    private $email;

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
     * Set assunto
     *
     * @param string $assunto
     * @return ZgappNotificacao
     */
    public function setAssunto($assunto)
    {
        $this->assunto = $assunto;

        return $this;
    }

    /**
     * Get assunto
     *
     * @return string 
     */
    public function getAssunto()
    {
        return $this->assunto;
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
     * Set indViaSistema
     *
     * @param integer $indViaSistema
     * @return ZgappNotificacao
     */
    public function setIndViaSistema($indViaSistema)
    {
        $this->indViaSistema = $indViaSistema;

        return $this;
    }

    /**
     * Get indViaSistema
     *
     * @return integer 
     */
    public function getIndViaSistema()
    {
        return $this->indViaSistema;
    }

    /**
     * Set indViaEmail
     *
     * @param integer $indViaEmail
     * @return ZgappNotificacao
     */
    public function setIndViaEmail($indViaEmail)
    {
        $this->indViaEmail = $indViaEmail;

        return $this;
    }

    /**
     * Get indViaEmail
     *
     * @return integer 
     */
    public function getIndViaEmail()
    {
        return $this->indViaEmail;
    }

    /**
     * Set indViaWa
     *
     * @param integer $indViaWa
     * @return ZgappNotificacao
     */
    public function setIndViaWa($indViaWa)
    {
        $this->indViaWa = $indViaWa;

        return $this;
    }

    /**
     * Get indViaWa
     *
     * @return integer 
     */
    public function getIndViaWa()
    {
        return $this->indViaWa;
    }

    /**
     * Set indProcessada
     *
     * @param integer $indProcessada
     * @return ZgappNotificacao
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
     * Set email
     *
     * @param string $email
     * @return ZgappNotificacao
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
