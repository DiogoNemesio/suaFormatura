<?php

namespace Entidades;

use Doctrine\ORM\Mapping as ORM;

/**
 * ZgfmtOrcamentoItem
 *
 * @ORM\Table(name="ZGFMT_ORCAMENTO_ITEM")
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
     * @var integer
     *
     * @ORM\Column(name="COD_ORCAMENTO", type="integer", nullable=false)
     */
    private $codOrcamento;

    /**
     * @var integer
     *
     * @ORM\Column(name="COD_ITEM", type="integer", nullable=false)
     */
    private $codItem;

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
     * @ORM\Column(name="OBSERVACAO", type="string", length=200, nullable=false)
     */
    private $observacao;


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
     * Set codOrcamento
     *
     * @param integer $codOrcamento
     * @return ZgfmtOrcamentoItem
     */
    public function setCodOrcamento($codOrcamento)
    {
        $this->codOrcamento = $codOrcamento;

        return $this;
    }

    /**
     * Get codOrcamento
     *
     * @return integer 
     */
    public function getCodOrcamento()
    {
        return $this->codOrcamento;
    }

    /**
     * Set codItem
     *
     * @param integer $codItem
     * @return ZgfmtOrcamentoItem
     */
    public function setCodItem($codItem)
    {
        $this->codItem = $codItem;

        return $this;
    }

    /**
     * Get codItem
     *
     * @return integer 
     */
    public function getCodItem()
    {
        return $this->codItem;
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
     * Set observacao
     *
     * @param string $observacao
     * @return ZgfmtOrcamentoItem
     */
    public function setObservacao($observacao)
    {
        $this->observacao = $observacao;

        return $this;
    }

    /**
     * Get observacao
     *
     * @return string 
     */
    public function getObservacao()
    {
        return $this->observacao;
    }
}
