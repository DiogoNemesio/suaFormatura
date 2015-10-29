<?php

namespace Entidades;

use Doctrine\ORM\Mapping as ORM;

/**
 * ZgfmtConviteExtraItem
 *
 * @ORM\Table(name="ZGFMT_CONVITE_EXTRA_ITEM", indexes={@ORM\Index(name="fk_ZGFMT_CONVITE_EXTRA_ITEM_1_idx", columns={"COD_VENDA"}), @ORM\Index(name="fk_ZGFMT_CONVITE_EXTRA_ITEM_2_idx", columns={"COD_CONVITE_CONF"})})
 * @ORM\Entity
 */
class ZgfmtConviteExtraItem
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
     * @var \Entidades\ZgfmtConviteExtraVenda
     *
     * @ORM\ManyToOne(targetEntity="Entidades\ZgfmtConviteExtraVenda")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="COD_VENDA", referencedColumnName="CODIGO")
     * })
     */
    private $codVenda;

    /**
     * @var \Entidades\ZgfmtConviteExtraConf
     *
     * @ORM\ManyToOne(targetEntity="Entidades\ZgfmtConviteExtraConf")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="COD_CONVITE_CONF", referencedColumnName="CODIGO")
     * })
     */
    private $codConviteConf;


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
     * @return ZgfmtConviteExtraItem
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
     * @return ZgfmtConviteExtraItem
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
     * Set codVenda
     *
     * @param \Entidades\ZgfmtConviteExtraVenda $codVenda
     * @return ZgfmtConviteExtraItem
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

    /**
     * Set codConviteConf
     *
     * @param \Entidades\ZgfmtConviteExtraConf $codConviteConf
     * @return ZgfmtConviteExtraItem
     */
    public function setCodConviteConf(\Entidades\ZgfmtConviteExtraConf $codConviteConf = null)
    {
        $this->codConviteConf = $codConviteConf;

        return $this;
    }

    /**
     * Get codConviteConf
     *
     * @return \Entidades\ZgfmtConviteExtraConf 
     */
    public function getCodConviteConf()
    {
        return $this->codConviteConf;
    }
}
