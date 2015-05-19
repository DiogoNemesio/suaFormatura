<?php

namespace Entidades;

use Doctrine\ORM\Mapping as ORM;

/**
 * ZgsegLog
 *
 * @ORM\Table(name="ZGSEG_LOG", indexes={@ORM\Index(name="fk_ZGSEG_LOG_1_idx", columns={"COD_USUARIO"}), @ORM\Index(name="fk_ZGSEG_LOG_2_idx", columns={"COD_TIPO_EVENTO"}), @ORM\Index(name="fk_ZGSEG_LOG_4_idx", columns={"COD_ORGANIZACAO"}), @ORM\Index(name="fk_ZGSEG_LOG_3_idx", columns={"COD_CAMPO"})})
 * @ORM\Entity
 */
class ZgsegLog
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
     * @ORM\Column(name="DATA", type="datetime", nullable=false)
     */
    private $data;

    /**
     * @var string
     *
     * @ORM\Column(name="VALOR_ANTERIOR", type="string", length=4000, nullable=true)
     */
    private $valorAnterior;

    /**
     * @var string
     *
     * @ORM\Column(name="VALOR_POSTERIOR", type="string", length=4000, nullable=true)
     */
    private $valorPosterior;

    /**
     * @var string
     *
     * @ORM\Column(name="HISTORICO", type="string", length=100, nullable=true)
     */
    private $historico;

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
     * @var \Entidades\ZgsegLogTipo
     *
     * @ORM\ManyToOne(targetEntity="Entidades\ZgsegLogTipo")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="COD_TIPO_EVENTO", referencedColumnName="CODIGO")
     * })
     */
    private $codTipoEvento;

    /**
     * @var \Entidades\ZgsegDicionarioCampo
     *
     * @ORM\ManyToOne(targetEntity="Entidades\ZgsegDicionarioCampo")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="COD_CAMPO", referencedColumnName="CODIGO")
     * })
     */
    private $codCampo;

    /**
     * @var \Entidades\ZgfmtOrganizacao
     *
     * @ORM\ManyToOne(targetEntity="Entidades\ZgfmtOrganizacao")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="COD_ORGANIZACAO", referencedColumnName="CODIGO")
     * })
     */
    private $codOrganizacao;


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
     * @return ZgsegLog
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
     * Set valorAnterior
     *
     * @param string $valorAnterior
     * @return ZgsegLog
     */
    public function setValorAnterior($valorAnterior)
    {
        $this->valorAnterior = $valorAnterior;

        return $this;
    }

    /**
     * Get valorAnterior
     *
     * @return string 
     */
    public function getValorAnterior()
    {
        return $this->valorAnterior;
    }

    /**
     * Set valorPosterior
     *
     * @param string $valorPosterior
     * @return ZgsegLog
     */
    public function setValorPosterior($valorPosterior)
    {
        $this->valorPosterior = $valorPosterior;

        return $this;
    }

    /**
     * Get valorPosterior
     *
     * @return string 
     */
    public function getValorPosterior()
    {
        return $this->valorPosterior;
    }

    /**
     * Set historico
     *
     * @param string $historico
     * @return ZgsegLog
     */
    public function setHistorico($historico)
    {
        $this->historico = $historico;

        return $this;
    }

    /**
     * Get historico
     *
     * @return string 
     */
    public function getHistorico()
    {
        return $this->historico;
    }

    /**
     * Set codUsuario
     *
     * @param \Entidades\ZgsegUsuario $codUsuario
     * @return ZgsegLog
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
     * Set codTipoEvento
     *
     * @param \Entidades\ZgsegLogTipo $codTipoEvento
     * @return ZgsegLog
     */
    public function setCodTipoEvento(\Entidades\ZgsegLogTipo $codTipoEvento = null)
    {
        $this->codTipoEvento = $codTipoEvento;

        return $this;
    }

    /**
     * Get codTipoEvento
     *
     * @return \Entidades\ZgsegLogTipo 
     */
    public function getCodTipoEvento()
    {
        return $this->codTipoEvento;
    }

    /**
     * Set codCampo
     *
     * @param \Entidades\ZgsegDicionarioCampo $codCampo
     * @return ZgsegLog
     */
    public function setCodCampo(\Entidades\ZgsegDicionarioCampo $codCampo = null)
    {
        $this->codCampo = $codCampo;

        return $this;
    }

    /**
     * Get codCampo
     *
     * @return \Entidades\ZgsegDicionarioCampo 
     */
    public function getCodCampo()
    {
        return $this->codCampo;
    }

    /**
     * Set codOrganizacao
     *
     * @param \Entidades\ZgfmtOrganizacao $codOrganizacao
     * @return ZgsegLog
     */
    public function setCodOrganizacao(\Entidades\ZgfmtOrganizacao $codOrganizacao = null)
    {
        $this->codOrganizacao = $codOrganizacao;

        return $this;
    }

    /**
     * Get codOrganizacao
     *
     * @return \Entidades\ZgfmtOrganizacao 
     */
    public function getCodOrganizacao()
    {
        return $this->codOrganizacao;
    }
}
