<?php

namespace Entidades;

use Doctrine\ORM\Mapping as ORM;

/**
 * ZgfinTransferenciaHistCanc
 *
 * @ORM\Table(name="ZGFIN_TRANSFERENCIA_HIST_CANC", indexes={@ORM\Index(name="fk_ZGFIN_TRANSFERENCIA_HIST_CANC_1_idx", columns={"COD_TRANSFERENCIA"}), @ORM\Index(name="fk_ZGFIN_TRANSFERENCIA_HIST_CANC_2_idx", columns={"COD_USUARIO"})})
 * @ORM\Entity
 */
class ZgfinTransferenciaHistCanc
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
     * @ORM\Column(name="MOTIVO", type="string", length=200, nullable=false)
     */
    private $motivo;

    /**
     * @var float
     *
     * @ORM\Column(name="VALOR", type="float", precision=10, scale=0, nullable=false)
     */
    private $valor;

    /**
     * @var \Entidades\ZgfinTransferencia
     *
     * @ORM\ManyToOne(targetEntity="Entidades\ZgfinTransferencia")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="COD_TRANSFERENCIA", referencedColumnName="CODIGO")
     * })
     */
    private $codTransferencia;

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
     * @return ZgfinTransferenciaHistCanc
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
     * @return ZgfinTransferenciaHistCanc
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
     * Set codTransferencia
     *
     * @param \Entidades\ZgfinTransferencia $codTransferencia
     * @return ZgfinTransferenciaHistCanc
     */
    public function setCodTransferencia(\Entidades\ZgfinTransferencia $codTransferencia = null)
    {
        $this->codTransferencia = $codTransferencia;

        return $this;
    }

    /**
     * Get codTransferencia
     *
     * @return \Entidades\ZgfinTransferencia 
     */
    public function getCodTransferencia()
    {
        return $this->codTransferencia;
    }

    /**
     * Set codUsuario
     *
     * @param \Entidades\ZgsegUsuario $codUsuario
     * @return ZgfinTransferenciaHistCanc
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
