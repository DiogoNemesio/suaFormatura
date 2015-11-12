<?php

namespace Entidades;

use Doctrine\ORM\Mapping as ORM;

/**
 * ZgfmtConviteExtraVenda
 *
 * @ORM\Table(name="ZGFMT_CONVITE_EXTRA_VENDA", indexes={@ORM\Index(name="fk_table1_1_idx", columns={"COD_FORMANDO"}), @ORM\Index(name="fk_table1_2_idx", columns={"COD_FORMA_PAGAMENTO"}), @ORM\Index(name="fk_ZGFMT_CONVITE_EXTRA_VENDA_1_idx", columns={"COD_CONTA_RECEBIMENTO"})})
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
}
