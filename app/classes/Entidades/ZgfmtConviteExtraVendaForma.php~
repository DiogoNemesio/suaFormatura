<?php

namespace Entidades;

use Doctrine\ORM\Mapping as ORM;

/**
 * ZgfmtConviteExtraVendaForma
 *
 * @ORM\Table(name="ZGFMT_CONVITE_EXTRA_VENDA_FORMA", indexes={@ORM\Index(name="fk_ZGFMT_CONVITE_EXTRA_VENDA_FORMA_1_idx", columns={"COD_VENDA_CONF"}), @ORM\Index(name="fk_ZGFMT_CONVITE_EXTRA_VENDA_FORMA_2_idx", columns={"COD_FORMA_PAGAMENTO"})})
 * @ORM\Entity
 */
class ZgfmtConviteExtraVendaForma
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
     * @var \Entidades\ZgfmtConviteExtraVendaConf
     *
     * @ORM\ManyToOne(targetEntity="Entidades\ZgfmtConviteExtraVendaConf")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="COD_VENDA_CONF", referencedColumnName="CODIGO")
     * })
     */
    private $codVendaConf;

    /**
     * @var \Entidades\ZgfinFormaPagamento
     *
     * @ORM\ManyToOne(targetEntity="Entidades\ZgfinFormaPagamento")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="COD_FORMA_PAGAMENTO", referencedColumnName="CODIGO")
     * })
     */
    private $codFormaPagamento;


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
     * Set codVendaConf
     *
     * @param \Entidades\ZgfmtConviteExtraVendaConf $codVendaConf
     * @return ZgfmtConviteExtraVendaForma
     */
    public function setCodVendaConf(\Entidades\ZgfmtConviteExtraVendaConf $codVendaConf = null)
    {
        $this->codVendaConf = $codVendaConf;

        return $this;
    }

    /**
     * Get codVendaConf
     *
     * @return \Entidades\ZgfmtConviteExtraVendaConf 
     */
    public function getCodVendaConf()
    {
        return $this->codVendaConf;
    }

    /**
     * Set codFormaPagamento
     *
     * @param \Entidades\ZgfinFormaPagamento $codFormaPagamento
     * @return ZgfmtConviteExtraVendaForma
     */
    public function setCodFormaPagamento(\Entidades\ZgfinFormaPagamento $codFormaPagamento = null)
    {
        $this->codFormaPagamento = $codFormaPagamento;

        return $this;
    }

    /**
     * Get codFormaPagamento
     *
     * @return \Entidades\ZgfinFormaPagamento 
     */
    public function getCodFormaPagamento()
    {
        return $this->codFormaPagamento;
    }
}
