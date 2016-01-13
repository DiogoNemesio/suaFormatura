<?php

namespace Entidades;

use Doctrine\ORM\Mapping as ORM;

/**
 * ZgfmtContratoFormandoParcela
 *
 * @ORM\Table(name="ZGFMT_CONTRATO_FORMANDO_PARCELA", indexes={@ORM\Index(name="fk_ZGFMT_CONTRATO_FORMANDO_PARCELA_1_idx", columns={"COD_CONTRATO"})})
 * @ORM\Entity
 */
class ZgfmtContratoFormandoParcela
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
     * @ORM\Column(name="PARCELA", type="integer", nullable=false)
     */
    private $parcela;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="DATA_VENCIMENTO", type="date", nullable=false)
     */
    private $dataVencimento;

    /**
     * @var float
     *
     * @ORM\Column(name="VALOR", type="float", precision=10, scale=0, nullable=false)
     */
    private $valor;

    /**
     * @var \Entidades\ZgfmtContratoFormando
     *
     * @ORM\ManyToOne(targetEntity="Entidades\ZgfmtContratoFormando")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="COD_CONTRATO", referencedColumnName="CODIGO")
     * })
     */
    private $codContrato;


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
     * Set parcela
     *
     * @param integer $parcela
     * @return ZgfmtContratoFormandoParcela
     */
    public function setParcela($parcela)
    {
        $this->parcela = $parcela;

        return $this;
    }

    /**
     * Get parcela
     *
     * @return integer 
     */
    public function getParcela()
    {
        return $this->parcela;
    }

    /**
     * Set dataVencimento
     *
     * @param \DateTime $dataVencimento
     * @return ZgfmtContratoFormandoParcela
     */
    public function setDataVencimento($dataVencimento)
    {
        $this->dataVencimento = $dataVencimento;

        return $this;
    }

    /**
     * Get dataVencimento
     *
     * @return \DateTime 
     */
    public function getDataVencimento()
    {
        return $this->dataVencimento;
    }

    /**
     * Set valor
     *
     * @param float $valor
     * @return ZgfmtContratoFormandoParcela
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
     * Set codContrato
     *
     * @param \Entidades\ZgfmtContratoFormando $codContrato
     * @return ZgfmtContratoFormandoParcela
     */
    public function setCodContrato(\Entidades\ZgfmtContratoFormando $codContrato = null)
    {
        $this->codContrato = $codContrato;

        return $this;
    }

    /**
     * Get codContrato
     *
     * @return \Entidades\ZgfmtContratoFormando 
     */
    public function getCodContrato()
    {
        return $this->codContrato;
    }
}
