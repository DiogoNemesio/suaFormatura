<?php

namespace Entidades;

use Doctrine\ORM\Mapping as ORM;

/**
 * ZgfinContaPagarHistorico
 *
 * @ORM\Table(name="ZGFIN_CONTA_PAGAR_HISTORICO", indexes={@ORM\Index(name="fk_ZGFIN_CONTA_PAGAR_HISTORICO_1_idx", columns={"COD_CONTA"}), @ORM\Index(name="fk_ZGFIN_CONTA_PAGAR_HISTORICO_2_idx", columns={"COD_USUARIO"}), @ORM\Index(name="fk_ZGFIN_CONTA_PAGAR_HISTORICO_3_idx", columns={"COD_TIPO_HIST"})})
 * @ORM\Entity
 */
class ZgfinContaPagarHistorico
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
     * @ORM\Column(name="HISTORICO", type="string", length=2000, nullable=false)
     */
    private $historico;

    /**
     * @var \Entidades\ZgfinContaPagar
     *
     * @ORM\ManyToOne(targetEntity="Entidades\ZgfinContaPagar")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="COD_CONTA", referencedColumnName="CODIGO")
     * })
     */
    private $codConta;

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
     * @var \Entidades\ZgfinContaHistoricoTipo
     *
     * @ORM\ManyToOne(targetEntity="Entidades\ZgfinContaHistoricoTipo")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="COD_TIPO_HIST", referencedColumnName="CODIGO")
     * })
     */
    private $codTipoHist;


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
     * @return ZgfinContaPagarHistorico
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
     * Set historico
     *
     * @param string $historico
     * @return ZgfinContaPagarHistorico
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
     * Set codConta
     *
     * @param \Entidades\ZgfinContaPagar $codConta
     * @return ZgfinContaPagarHistorico
     */
    public function setCodConta(\Entidades\ZgfinContaPagar $codConta = null)
    {
        $this->codConta = $codConta;

        return $this;
    }

    /**
     * Get codConta
     *
     * @return \Entidades\ZgfinContaPagar 
     */
    public function getCodConta()
    {
        return $this->codConta;
    }

    /**
     * Set codUsuario
     *
     * @param \Entidades\ZgsegUsuario $codUsuario
     * @return ZgfinContaPagarHistorico
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
     * Set codTipoHist
     *
     * @param \Entidades\ZgfinContaHistoricoTipo $codTipoHist
     * @return ZgfinContaPagarHistorico
     */
    public function setCodTipoHist(\Entidades\ZgfinContaHistoricoTipo $codTipoHist = null)
    {
        $this->codTipoHist = $codTipoHist;

        return $this;
    }

    /**
     * Get codTipoHist
     *
     * @return \Entidades\ZgfinContaHistoricoTipo 
     */
    public function getCodTipoHist()
    {
        return $this->codTipoHist;
    }
}
