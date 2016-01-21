<?php

namespace Entidades;

use Doctrine\ORM\Mapping as ORM;

/**
 * ZgfmtItemOrcContrato
 *
 * @ORM\Table(name="ZGFMT_ITEM_ORC_CONTRATO", indexes={@ORM\Index(name="fk_ZGFMT_ITEM_ORC_CONTRATO_1_idx", columns={"COD_ORGANIZACAO"}), @ORM\Index(name="fk_ZGFMT_ITEM_ORC_CONTRATO_2_idx", columns={"COD_ITEM_ORCAMENTO"}), @ORM\Index(name="fk_ZGFMT_ITEM_ORC_CONTRATO_3_idx", columns={"COD_ORCAMENTO"})})
 * @ORM\Entity
 */
class ZgfmtItemOrcContrato
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
     * @ORM\Column(name="DATA_EVENTO", type="datetime", nullable=true)
     */
    private $dataEvento;

    /**
     * @var integer
     *
     * @ORM\Column(name="IND_CONTRATADO", type="integer", nullable=true)
     */
    private $indContratado;

    /**
     * @var integer
     *
     * @ORM\Column(name="COD_TRANSACAO", type="integer", nullable=true)
     */
    private $codTransacao;

    /**
     * @var \Entidades\ZgadmOrganizacao
     *
     * @ORM\ManyToOne(targetEntity="Entidades\ZgadmOrganizacao")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="COD_ORGANIZACAO", referencedColumnName="CODIGO")
     * })
     */
    private $codOrganizacao;

    /**
     * @var \Entidades\ZgfmtOrcamentoItem
     *
     * @ORM\ManyToOne(targetEntity="Entidades\ZgfmtOrcamentoItem")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="COD_ITEM_ORCAMENTO", referencedColumnName="CODIGO")
     * })
     */
    private $codItemOrcamento;

    /**
     * @var \Entidades\ZgfmtOrcamento
     *
     * @ORM\ManyToOne(targetEntity="Entidades\ZgfmtOrcamento")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="COD_ORCAMENTO", referencedColumnName="CODIGO")
     * })
     */
    private $codOrcamento;


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
     * Set dataEvento
     *
     * @param \DateTime $dataEvento
     * @return ZgfmtItemOrcContrato
     */
    public function setDataEvento($dataEvento)
    {
        $this->dataEvento = $dataEvento;

        return $this;
    }

    /**
     * Get dataEvento
     *
     * @return \DateTime 
     */
    public function getDataEvento()
    {
        return $this->dataEvento;
    }

    /**
     * Set indContratado
     *
     * @param integer $indContratado
     * @return ZgfmtItemOrcContrato
     */
    public function setIndContratado($indContratado)
    {
        $this->indContratado = $indContratado;

        return $this;
    }

    /**
     * Get indContratado
     *
     * @return integer 
     */
    public function getIndContratado()
    {
        return $this->indContratado;
    }

    /**
     * Set codTransacao
     *
     * @param integer $codTransacao
     * @return ZgfmtItemOrcContrato
     */
    public function setCodTransacao($codTransacao)
    {
        $this->codTransacao = $codTransacao;

        return $this;
    }

    /**
     * Get codTransacao
     *
     * @return integer 
     */
    public function getCodTransacao()
    {
        return $this->codTransacao;
    }

    /**
     * Set codOrganizacao
     *
     * @param \Entidades\ZgadmOrganizacao $codOrganizacao
     * @return ZgfmtItemOrcContrato
     */
    public function setCodOrganizacao(\Entidades\ZgadmOrganizacao $codOrganizacao = null)
    {
        $this->codOrganizacao = $codOrganizacao;

        return $this;
    }

    /**
     * Get codOrganizacao
     *
     * @return \Entidades\ZgadmOrganizacao 
     */
    public function getCodOrganizacao()
    {
        return $this->codOrganizacao;
    }

    /**
     * Set codItemOrcamento
     *
     * @param \Entidades\ZgfmtOrcamentoItem $codItemOrcamento
     * @return ZgfmtItemOrcContrato
     */
    public function setCodItemOrcamento(\Entidades\ZgfmtOrcamentoItem $codItemOrcamento = null)
    {
        $this->codItemOrcamento = $codItemOrcamento;

        return $this;
    }

    /**
     * Get codItemOrcamento
     *
     * @return \Entidades\ZgfmtOrcamentoItem 
     */
    public function getCodItemOrcamento()
    {
        return $this->codItemOrcamento;
    }

    /**
     * Set codOrcamento
     *
     * @param \Entidades\ZgfmtOrcamento $codOrcamento
     * @return ZgfmtItemOrcContrato
     */
    public function setCodOrcamento(\Entidades\ZgfmtOrcamento $codOrcamento = null)
    {
        $this->codOrcamento = $codOrcamento;

        return $this;
    }

    /**
     * Get codOrcamento
     *
     * @return \Entidades\ZgfmtOrcamento 
     */
    public function getCodOrcamento()
    {
        return $this->codOrcamento;
    }
}
