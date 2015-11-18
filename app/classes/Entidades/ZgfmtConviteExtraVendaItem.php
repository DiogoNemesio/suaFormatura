<?php

namespace Entidades;

use Doctrine\ORM\Mapping as ORM;

/**
 * ZgfmtConviteExtraVendaItem
 *
 * @ORM\Table(name="ZGFMT_CONVITE_EXTRA_VENDA_ITEM", indexes={@ORM\Index(name="fk_ZGFMT_CONVITE_EXTRA_VENDA_ITEM_2_idx", columns={"COD_VENDA"}), @ORM\Index(name="fk_ZGFMT_CONVITE_EXTRA_VENDA_ITEM_1_idx", columns={"COD_EVENTO"})})
 * @ORM\Entity
 */
class ZgfmtConviteExtraVendaItem
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
     * @ORM\Column(name="QUANTIDADE", type="integer", nullable=false)
     */
    private $quantidade;

    /**
     * @var float
     *
     * @ORM\Column(name="VALOR_UNITARIO", type="float", precision=10, scale=0, nullable=false)
     */
    private $valorUnitario;

    /**
     * @var \Entidades\ZgfmtEvento
     *
     * @ORM\ManyToOne(targetEntity="Entidades\ZgfmtEvento")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="COD_EVENTO", referencedColumnName="CODIGO")
     * })
     */
    private $codEvento;

    /**
     * @var \Entidades\ZgfmtConviteExtraVenda
     *
     * @ORM\ManyToOne(targetEntity="Entidades\ZgfmtConviteExtraVenda")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="COD_VENDA", referencedColumnName="CODIGO")
     * })
     */
    private $codVenda;


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
     * @param integer $quantidade
     * @return ZgfmtConviteExtraVendaItem
     */
    public function setQuantidade($quantidade)
    {
        $this->quantidade = $quantidade;

        return $this;
    }

    /**
     * Get quantidade
     *
     * @return integer 
     */
    public function getQuantidade()
    {
        return $this->quantidade;
    }

    /**
     * Set valorUnitario
     *
     * @param float $valorUnitario
     * @return ZgfmtConviteExtraVendaItem
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
     * Set codEvento
     *
     * @param \Entidades\ZgfmtEvento $codEvento
     * @return ZgfmtConviteExtraVendaItem
     */
    public function setCodEvento(\Entidades\ZgfmtEvento $codEvento = null)
    {
        $this->codEvento = $codEvento;

        return $this;
    }

    /**
     * Get codEvento
     *
     * @return \Entidades\ZgfmtEvento 
     */
    public function getCodEvento()
    {
        return $this->codEvento;
    }

    /**
     * Set codVenda
     *
     * @param \Entidades\ZgfmtConviteExtraVenda $codVenda
     * @return ZgfmtConviteExtraVendaItem
     */
    public function setCodVenda(\Entidades\ZgfmtConviteExtraVenda $codVenda = null)
    {
        $this->codVenda = $codVenda;

        return $this;
    }

    /**
     * Get codVenda
     *
     * @return \Entidades\ZgfmtConviteExtraVenda 
     */
    public function getCodVenda()
    {
        return $this->codVenda;
    }
}
