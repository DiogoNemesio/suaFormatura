<?php

namespace Entidades;

use Doctrine\ORM\Mapping as ORM;

/**
 * ZgfmtItemOrcContratoFornec
 *
 * @ORM\Table(name="ZGFMT_ITEM_ORC_CONTRATO_FORNEC", indexes={@ORM\Index(name="fk_ZGFMT_ITEM_ORC_CONTRATO_FORNEC_1_idx", columns={"COD_ITEM_CONTRATO"}), @ORM\Index(name="fk_ZGFMT_ITEM_ORC_CONTRATO_FORNEC_2_idx", columns={"COD_PESSOA"})})
 * @ORM\Entity
 */
class ZgfmtItemOrcContratoFornec
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
     * @var float
     *
     * @ORM\Column(name="QUANTIDADE", type="float", precision=10, scale=0, nullable=false)
     */
    private $quantidade;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="DATA_OPERACAO", type="datetime", nullable=true)
     */
    private $dataOperacao;

    /**
     * @var \Entidades\ZgfmtItemOrcContrato
     *
     * @ORM\ManyToOne(targetEntity="Entidades\ZgfmtItemOrcContrato")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="COD_ITEM_CONTRATO", referencedColumnName="CODIGO")
     * })
     */
    private $codItemContrato;

    /**
     * @var \Entidades\ZgfinPessoa
     *
     * @ORM\ManyToOne(targetEntity="Entidades\ZgfinPessoa")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="COD_PESSOA", referencedColumnName="CODIGO")
     * })
     */
    private $codPessoa;


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
     * Set quantidade
     *
     * @param float $quantidade
     * @return ZgfmtItemOrcContratoFornec
     */
    public function setQuantidade($quantidade)
    {
        $this->quantidade = $quantidade;

        return $this;
    }

    /**
     * Get quantidade
     *
     * @return float 
     */
    public function getQuantidade()
    {
        return $this->quantidade;
    }

    /**
     * Set dataOperacao
     *
     * @param \DateTime $dataOperacao
     * @return ZgfmtItemOrcContratoFornec
     */
    public function setDataOperacao($dataOperacao)
    {
        $this->dataOperacao = $dataOperacao;

        return $this;
    }

    /**
     * Get dataOperacao
     *
     * @return \DateTime 
     */
    public function getDataOperacao()
    {
        return $this->dataOperacao;
    }

    /**
     * Set codItemContrato
     *
     * @param \Entidades\ZgfmtItemOrcContrato $codItemContrato
     * @return ZgfmtItemOrcContratoFornec
     */
    public function setCodItemContrato(\Entidades\ZgfmtItemOrcContrato $codItemContrato = null)
    {
        $this->codItemContrato = $codItemContrato;

        return $this;
    }

    /**
     * Get codItemContrato
     *
     * @return \Entidades\ZgfmtItemOrcContrato 
     */
    public function getCodItemContrato()
    {
        return $this->codItemContrato;
    }

    /**
     * Set codPessoa
     *
     * @param \Entidades\ZgfinPessoa $codPessoa
     * @return ZgfmtItemOrcContratoFornec
     */
    public function setCodPessoa(\Entidades\ZgfinPessoa $codPessoa = null)
    {
        $this->codPessoa = $codPessoa;

        return $this;
    }

    /**
     * Get codPessoa
     *
     * @return \Entidades\ZgfinPessoa 
     */
    public function getCodPessoa()
    {
        return $this->codPessoa;
    }
}
