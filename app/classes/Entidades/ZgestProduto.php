<?php

namespace Entidades;

use Doctrine\ORM\Mapping as ORM;

/**
 * ZgestProduto
 *
 * @ORM\Table(name="ZGEST_PRODUTO", indexes={@ORM\Index(name="fk_ZGEST_PRODUTO_1_idx", columns={"COD_TIPO_MATERIAL"}), @ORM\Index(name="fk_ZGEST_PRODUTO_2_idx", columns={"COD_ORGANIZACAO"}), @ORM\Index(name="fk_ZGEST_PRODUTO_3_idx", columns={"COD_SUBGRUPO"})})
 * @ORM\Entity
 */
class ZgestProduto
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
     * @var string
     *
     * @ORM\Column(name="NOME", type="string", length=100, nullable=false)
     */
    private $nome;

    /**
     * @var string
     *
     * @ORM\Column(name="DESCRICAO", type="string", length=500, nullable=true)
     */
    private $descricao;

    /**
     * @var string
     *
     * @ORM\Column(name="IND_ATIVO", type="string", length=45, nullable=false)
     */
    private $indAtivo;

    /**
     * @var integer
     *
     * @ORM\Column(name="IND_RESERVA_ONLINE", type="integer", nullable=true)
     */
    private $indReservaOnline;

    /**
     * @var integer
     *
     * @ORM\Column(name="QUANTIDADE", type="integer", nullable=true)
     */
    private $quantidade;

    /**
     * @var integer
     *
     * @ORM\Column(name="NUM_DIAS_INDISPONIVEL", type="integer", nullable=true)
     */
    private $numDiasIndisponivel;

    /**
     * @var integer
     *
     * @ORM\Column(name="QTDE_DIAS_PRE_RESERVA", type="integer", nullable=true)
     */
    private $qtdeDiasPreReserva;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="DATA_CADASTRO", type="datetime", nullable=false)
     */
    private $dataCadastro;

    /**
     * @var \Entidades\ZgestTipoProduto
     *
     * @ORM\ManyToOne(targetEntity="Entidades\ZgestTipoProduto")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="COD_TIPO_MATERIAL", referencedColumnName="CODIGO")
     * })
     */
    private $codTipoMaterial;

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
     * @var \Entidades\ZgestSubgrupo
     *
     * @ORM\ManyToOne(targetEntity="Entidades\ZgestSubgrupo")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="COD_SUBGRUPO", referencedColumnName="CODIGO")
     * })
     */
    private $codSubgrupo;


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
     * Set nome
     *
     * @param string $nome
     * @return ZgestProduto
     */
    public function setNome($nome)
    {
        $this->nome = $nome;

        return $this;
    }

    /**
     * Get nome
     *
     * @return string 
     */
    public function getNome()
    {
        return $this->nome;
    }

    /**
     * Set descricao
     *
     * @param string $descricao
     * @return ZgestProduto
     */
    public function setDescricao($descricao)
    {
        $this->descricao = $descricao;

        return $this;
    }

    /**
     * Get descricao
     *
     * @return string 
     */
    public function getDescricao()
    {
        return $this->descricao;
    }

    /**
     * Set indAtivo
     *
     * @param string $indAtivo
     * @return ZgestProduto
     */
    public function setIndAtivo($indAtivo)
    {
        $this->indAtivo = $indAtivo;

        return $this;
    }

    /**
     * Get indAtivo
     *
     * @return string 
     */
    public function getIndAtivo()
    {
        return $this->indAtivo;
    }

    /**
     * Set indReservaOnline
     *
     * @param integer $indReservaOnline
     * @return ZgestProduto
     */
    public function setIndReservaOnline($indReservaOnline)
    {
        $this->indReservaOnline = $indReservaOnline;

        return $this;
    }

    /**
     * Get indReservaOnline
     *
     * @return integer 
     */
    public function getIndReservaOnline()
    {
        return $this->indReservaOnline;
    }

    /**
     * Set quantidade
     *
     * @param integer $quantidade
     * @return ZgestProduto
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
     * Set numDiasIndisponivel
     *
     * @param integer $numDiasIndisponivel
     * @return ZgestProduto
     */
    public function setNumDiasIndisponivel($numDiasIndisponivel)
    {
        $this->numDiasIndisponivel = $numDiasIndisponivel;

        return $this;
    }

    /**
     * Get numDiasIndisponivel
     *
     * @return integer 
     */
    public function getNumDiasIndisponivel()
    {
        return $this->numDiasIndisponivel;
    }

    /**
     * Set qtdeDiasPreReserva
     *
     * @param integer $qtdeDiasPreReserva
     * @return ZgestProduto
     */
    public function setQtdeDiasPreReserva($qtdeDiasPreReserva)
    {
        $this->qtdeDiasPreReserva = $qtdeDiasPreReserva;

        return $this;
    }

    /**
     * Get qtdeDiasPreReserva
     *
     * @return integer 
     */
    public function getQtdeDiasPreReserva()
    {
        return $this->qtdeDiasPreReserva;
    }

    /**
     * Set dataCadastro
     *
     * @param \DateTime $dataCadastro
     * @return ZgestProduto
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
     * Set codTipoMaterial
     *
     * @param \Entidades\ZgestTipoProduto $codTipoMaterial
     * @return ZgestProduto
     */
    public function setCodTipoMaterial(\Entidades\ZgestTipoProduto $codTipoMaterial = null)
    {
        $this->codTipoMaterial = $codTipoMaterial;

        return $this;
    }

    /**
     * Get codTipoMaterial
     *
     * @return \Entidades\ZgestTipoProduto 
     */
    public function getCodTipoMaterial()
    {
        return $this->codTipoMaterial;
    }

    /**
     * Set codOrganizacao
     *
     * @param \Entidades\ZgadmOrganizacao $codOrganizacao
     * @return ZgestProduto
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
     * Set codSubgrupo
     *
     * @param \Entidades\ZgestSubgrupo $codSubgrupo
     * @return ZgestProduto
     */
    public function setCodSubgrupo(\Entidades\ZgestSubgrupo $codSubgrupo = null)
    {
        $this->codSubgrupo = $codSubgrupo;

        return $this;
    }

    /**
     * Get codSubgrupo
     *
     * @return \Entidades\ZgestSubgrupo 
     */
    public function getCodSubgrupo()
    {
        return $this->codSubgrupo;
    }
}
