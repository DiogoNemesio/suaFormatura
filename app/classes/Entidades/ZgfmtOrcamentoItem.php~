<?php

namespace Entidades;

use Doctrine\ORM\Mapping as ORM;

/**
 * ZgfmtOrcamentoItem
 *
 * @ORM\Table(name="ZGFMT_ORCAMENTO_ITEM", indexes={@ORM\Index(name="fk_ZGFMT_ORCAMENTO_ITEM_1_idx", columns={"COD_ORCAMENTO"}), @ORM\Index(name="fk_ZGFMT_ORCAMENTO_ITEM_2_idx", columns={"COD_ITEM"}), @ORM\Index(name="fk_ZGFMT_ORCAMENTO_ITEM_3_idx", columns={"COD_TIPO_CORTESIA"})})
 * @ORM\Entity
 */
class ZgfmtOrcamentoItem
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
     * @var float
     *
     * @ORM\Column(name="VALOR_UNITARIO", type="float", precision=10, scale=0, nullable=false)
     */
    private $valorUnitario;

    /**
     * @var string
     *
     * @ORM\Column(name="TEXTO_DESCRITIVO", type="string", length=1000, nullable=true)
     */
    private $textoDescritivo;

    /**
     * @var integer
     *
     * @ORM\Column(name="IND_HABILITADO", type="integer", nullable=false)
     */
    private $indHabilitado;

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
     * @var \Entidades\ZgfmtPlanoOrcItem
     *
     * @ORM\ManyToOne(targetEntity="Entidades\ZgfmtPlanoOrcItem")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="COD_ITEM", referencedColumnName="CODIGO")
     * })
     */
    private $codItem;

    /**
     * @var \Entidades\ZgfmtOrcamentoCortesiaTipo
     *
     * @ORM\ManyToOne(targetEntity="Entidades\ZgfmtOrcamentoCortesiaTipo")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="COD_TIPO_CORTESIA", referencedColumnName="CODIGO")
     * })
     */
    private $codTipoCortesia;


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
     * @return ZgfmtOrcamentoItem
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
     * Set valorUnitario
     *
     * @param float $valorUnitario
     * @return ZgfmtOrcamentoItem
     */
    public function setValorUnitario($valorUnitario)
    {
        $this->valorUnitario = $valorUnitario;

        return $this;
    }

    /**
     * Get valorUnitario
     *
     * @return float 
     */
    public function getValorUnitario()
    {
        return $this->valorUnitario;
    }

    /**
     * Set textoDescritivo
     *
     * @param string $textoDescritivo
     * @return ZgfmtOrcamentoItem
     */
    public function setTextoDescritivo($textoDescritivo)
    {
        $this->textoDescritivo = $textoDescritivo;

        return $this;
    }

    /**
     * Get textoDescritivo
     *
     * @return string 
     */
    public function getTextoDescritivo()
    {
        return $this->textoDescritivo;
    }

    /**
     * Set indHabilitado
     *
     * @param integer $indHabilitado
     * @return ZgfmtOrcamentoItem
     */
    public function setIndHabilitado($indHabilitado)
    {
        $this->indHabilitado = $indHabilitado;

        return $this;
    }

    /**
     * Get indHabilitado
     *
     * @return integer 
     */
    public function getIndHabilitado()
    {
        return $this->indHabilitado;
    }

    /**
     * Set codOrcamento
     *
     * @param \Entidades\ZgfmtOrcamento $codOrcamento
     * @return ZgfmtOrcamentoItem
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

    /**
     * Set codItem
     *
     * @param \Entidades\ZgfmtPlanoOrcItem $codItem
     * @return ZgfmtOrcamentoItem
     */
    public function setCodItem(\Entidades\ZgfmtPlanoOrcItem $codItem = null)
    {
        $this->codItem = $codItem;

        return $this;
    }

    /**
     * Get codItem
     *
     * @return \Entidades\ZgfmtPlanoOrcItem 
     */
    public function getCodItem()
    {
        return $this->codItem;
    }

    /**
     * Set codTipoCortesia
     *
     * @param \Entidades\ZgfmtOrcamentoCortesiaTipo $codTipoCortesia
     * @return ZgfmtOrcamentoItem
     */
    public function setCodTipoCortesia(\Entidades\ZgfmtOrcamentoCortesiaTipo $codTipoCortesia = null)
    {
        $this->codTipoCortesia = $codTipoCortesia;

        return $this;
    }

    /**
     * Get codTipoCortesia
     *
     * @return \Entidades\ZgfmtOrcamentoCortesiaTipo 
     */
    public function getCodTipoCortesia()
    {
        return $this->codTipoCortesia;
    }
}
