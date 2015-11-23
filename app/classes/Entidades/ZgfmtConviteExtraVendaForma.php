<?php

namespace Entidades;

use Doctrine\ORM\Mapping as ORM;

/**
 * ZgfmtConviteExtraVendaForma
 *
 * @ORM\Table(name="ZGFMT_CONVITE_EXTRA_VENDA_FORMA", uniqueConstraints={@ORM\UniqueConstraint(name="ZGFMT_CONVITE_EXTRA_VENDA_FORMA_UK01", columns={"COD_ORGANIZACAO", "COD_VENDA_TIPO", "COD_FORMA_PAGAMENTO"})}, indexes={@ORM\Index(name="fk_ZGFMT_CONVITE_EXTRA_VENDA_FORMA_2_idx", columns={"COD_FORMA_PAGAMENTO"}), @ORM\Index(name="fk_ZGFMT_CONVITE_EXTRA_VENDA_FORMA_3_idx", columns={"COD_VENDA_TIPO"}), @ORM\Index(name="IDX_4A4618A29F83D42B", columns={"COD_ORGANIZACAO"})})
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
     * @var \Entidades\ZgadmOrganizacao
     *
     * @ORM\ManyToOne(targetEntity="Entidades\ZgadmOrganizacao")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="COD_ORGANIZACAO", referencedColumnName="CODIGO")
     * })
     */
    private $codOrganizacao;

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
     * @var \Entidades\ZgfmtConviteExtraVendaTipo
     *
     * @ORM\ManyToOne(targetEntity="Entidades\ZgfmtConviteExtraVendaTipo")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="COD_VENDA_TIPO", referencedColumnName="CODIGO")
     * })
     */
    private $codVendaTipo;


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
     * Set codOrganizacao
     *
     * @param \Entidades\ZgadmOrganizacao $codOrganizacao
     * @return ZgfmtConviteExtraVendaForma
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

    /**
     * Set codVendaTipo
     *
     * @param \Entidades\ZgfmtConviteExtraVendaTipo $codVendaTipo
     * @return ZgfmtConviteExtraVendaForma
     */
    public function setCodVendaTipo(\Entidades\ZgfmtConviteExtraVendaTipo $codVendaTipo = null)
    {
        $this->codVendaTipo = $codVendaTipo;

        return $this;
    }

    /**
     * Get codVendaTipo
     *
     * @return \Entidades\ZgfmtConviteExtraVendaTipo 
     */
    public function getCodVendaTipo()
    {
        return $this->codVendaTipo;
    }
}
