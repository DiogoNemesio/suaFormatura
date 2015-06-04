<?php

namespace Entidades;

use Doctrine\ORM\Mapping as ORM;

/**
 * ZgfinContaPagHistCanc
 *
 * @ORM\Table(name="ZGFIN_CONTA_PAG_HIST_CANC", indexes={@ORM\Index(name="fk_ZGFIN_CONTA_PAG_HIST_CANC_1_idx", columns={"COD_CONTA"}), @ORM\Index(name="fk_ZGFIN_CONTA_PAG_HIST_CANC_2_idx", columns={"COD_USUARIO"})})
 * @ORM\Entity
 */
class ZgfinContaPagHistCanc
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
     * @var string
     *
     * @ORM\Column(name="MOTIVO", type="string", length=200, nullable=true)
     */
    private $motivo;

    /**
     * @var float
     *
     * @ORM\Column(name="VALOR", type="float", precision=10, scale=0, nullable=true)
     */
    private $valor;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="DATA_CANCELAMENTO", type="datetime", nullable=false)
     */
    private $dataCancelamento;

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
     * Get codigo
     *
     * @return integer 
     */
    public function getCodigo()
    {
        return $this->codigo;
    }

    /**
     * Set motivo
     *
     * @param string $motivo
     * @return ZgfinContaPagHistCanc
     */
    public function setMotivo($motivo)
    {
        $this->motivo = $motivo;

        return $this;
    }

    /**
     * Get motivo
     *
     * @return string 
     */
    public function getMotivo()
    {
        return $this->motivo;
    }

    /**
     * Set valor
     *
     * @param float $valor
     * @return ZgfinContaPagHistCanc
     */
    public function setValor($valor)
    {
        $this->valor = $valor;

        return $this;
    }

    /**
     * Get valor
     *
     * @return float 
     */
    public function getValor()
    {
        return $this->valor;
    }

    /**
     * Set dataCancelamento
     *
     * @param \DateTime $dataCancelamento
     * @return ZgfinContaPagHistCanc
     */
    public function setDataCancelamento($dataCancelamento)
    {
        $this->dataCancelamento = $dataCancelamento;

        return $this;
    }

    /**
     * Get dataCancelamento
     *
     * @return \DateTime 
     */
    public function getDataCancelamento()
    {
        return $this->dataCancelamento;
    }

    /**
     * Set codConta
     *
     * @param \Entidades\ZgfinContaPagar $codConta
     * @return ZgfinContaPagHistCanc
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
     * @return ZgfinContaPagHistCanc
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
