<?php

namespace Entidades;

use Doctrine\ORM\Mapping as ORM;

/**
 * ZgfmtConviteExtraVenda
 *
 * @ORM\Table(name="ZGFMT_CONVITE_EXTRA_VENDA", indexes={@ORM\Index(name="fk_ZGFMT_CONVITE_EXTRA_VENDA_1_idx", columns={"COD_CONTA_RECEBIMENTO"}), @ORM\Index(name="fk_ZGFMT_CONVITE_EXTRA_VENDA_2_idx", columns={"COD_ORGANIZACAO"}), @ORM\Index(name="fk_ZGFMT_CONVITE_EXTRA_VENDA_3_idx", columns={"COD_FORMANDO"}), @ORM\Index(name="fk_ZGFMT_CONVITE_EXTRA_VENDA_4_idx", columns={"COD_FORMA_PAGAMENTO"}), @ORM\Index(name="fk_ZGFMT_CONVITE_EXTRA_VENDA_5_idx", columns={"COD_VENDA_TIPO"})})
 * @ORM\Entity
 */
class ZgfmtConviteExtraVenda
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
     * @ORM\Column(name="COD_TRANSACAO", type="integer", nullable=false)
     */
    private $codTransacao;

    /**
     * @var float
     *
     * @ORM\Column(name="VALOR_TOTAL", type="float", precision=10, scale=0, nullable=false)
     */
    private $valorTotal;

    /**
     * @var float
     *
     * @ORM\Column(name="TAXA_CONVENIENCIA", type="float", precision=10, scale=0, nullable=true)
     */
    private $taxaConveniencia;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="DATA_CADASTRO", type="datetime", nullable=false)
     */
    private $dataCadastro;

    /**
     * @var \Entidades\ZgfinConta
     *
     * @ORM\ManyToOne(targetEntity="Entidades\ZgfinConta")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="COD_CONTA_RECEBIMENTO", referencedColumnName="CODIGO")
     * })
     */
    private $codContaRecebimento;

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
     * @var \Entidades\ZgfinPessoa
     *
     * @ORM\ManyToOne(targetEntity="Entidades\ZgfinPessoa")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="COD_FORMANDO", referencedColumnName="CODIGO")
     * })
     */
    private $codFormando;

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
     * Set codTransacao
     *
     * @param integer $codTransacao
     * @return ZgfmtConviteExtraVenda
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
     * Set valorTotal
     *
     * @param float $valorTotal
     * @return ZgfmtConviteExtraVenda
     */
    public function setValorTotal($valorTotal)
    {
        $this->valorTotal = $valorTotal;

        return $this;
    }

    /**
     * Get valorTotal
     *
     * @return float 
     */
    public function getValorTotal()
    {
        return $this->valorTotal;
    }

    /**
     * Set taxaConveniencia
     *
     * @param float $taxaConveniencia
     * @return ZgfmtConviteExtraVenda
     */
    public function setTaxaConveniencia($taxaConveniencia)
    {
        $this->taxaConveniencia = $taxaConveniencia;

        return $this;
    }

    /**
     * Get taxaConveniencia
     *
     * @return float 
     */
    public function getTaxaConveniencia()
    {
        return $this->taxaConveniencia;
    }

    /**
     * Set dataCadastro
     *
     * @param \DateTime $dataCadastro
     * @return ZgfmtConviteExtraVenda
     */
    public function setDataCadastro($dataCadastro)
    {
        $this->dataCadastro = $dataCadastro;

        return $this;
    }

    /**
     * Get dataCadastro
     *
     * @return \DateTime 
     */
    public function getDataCadastro()
    {
        return $this->dataCadastro;
    }

    /**
     * Set codContaRecebimento
     *
     * @param \Entidades\ZgfinConta $codContaRecebimento
     * @return ZgfmtConviteExtraVenda
     */
    public function setCodContaRecebimento(\Entidades\ZgfinConta $codContaRecebimento = null)
    {
        $this->codContaRecebimento = $codContaRecebimento;

        return $this;
    }

    /**
     * Get codContaRecebimento
     *
     * @return \Entidades\ZgfinConta 
     */
    public function getCodContaRecebimento()
    {
        return $this->codContaRecebimento;
    }

    /**
     * Set codOrganizacao
     *
     * @param \Entidades\ZgadmOrganizacao $codOrganizacao
     * @return ZgfmtConviteExtraVenda
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
     * Set codFormando
     *
     * @param \Entidades\ZgfinPessoa $codFormando
     * @return ZgfmtConviteExtraVenda
     */
    public function setCodFormando(\Entidades\ZgfinPessoa $codFormando = null)
    {
        $this->codFormando = $codFormando;

        return $this;
    }

    /**
     * Get codFormando
     *
     * @return \Entidades\ZgfinPessoa 
     */
    public function getCodFormando()
    {
        return $this->codFormando;
    }

    /**
     * Set codFormaPagamento
     *
     * @param \Entidades\ZgfinFormaPagamento $codFormaPagamento
     * @return ZgfmtConviteExtraVenda
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
     * @return ZgfmtConviteExtraVenda
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
