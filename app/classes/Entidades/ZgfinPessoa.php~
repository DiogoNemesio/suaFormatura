<?php

namespace Entidades;

use Doctrine\ORM\Mapping as ORM;

/**
 * ZgfinPessoa
 *
 * @ORM\Table(name="ZGFIN_PESSOA", indexes={@ORM\Index(name="fk_ZGFIN_PESSOA_1_idx", columns={"COD_TIPO_PESSOA"}), @ORM\Index(name="fk_ZGFIN_PESSOA_2_idx", columns={"COD_PARCEIRO"}), @ORM\Index(name="fk_ZGFIN_PESSOA_3_idx", columns={"COD_SEXO"}), @ORM\Index(name="ZGFIN_PESSOA_UK01", columns={"COD_PARCEIRO", "CGC"}), @ORM\Index(name="fk_ZGFIN_PESSOA_4_idx", columns={"COD_ORGANIZACAO_CADASTRO"})})
 * @ORM\Entity
 */
class ZgfinPessoa
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
     * @ORM\Column(name="FANTASIA", type="string", length=60, nullable=true)
     */
    private $fantasia;

    /**
     * @var string
     *
     * @ORM\Column(name="CGC", type="string", length=14, nullable=true)
     */
    private $cgc;

    /**
     * @var string
     *
     * @ORM\Column(name="RG", type="string", length=14, nullable=true)
     */
    private $rg;

    /**
     * @var string
     *
     * @ORM\Column(name="INSC_ESTADUAL", type="string", length=18, nullable=true)
     */
    private $inscEstadual;

    /**
     * @var string
     *
     * @ORM\Column(name="INSC_MUNICIPAL", type="string", length=18, nullable=true)
     */
    private $inscMunicipal;

    /**
     * @var string
     *
     * @ORM\Column(name="EMAIL", type="string", length=200, nullable=true)
     */
    private $email;

    /**
     * @var integer
     *
     * @ORM\Column(name="IND_CONTRIBUINTE", type="integer", nullable=false)
     */
    private $indContribuinte;

    /**
     * @var integer
     *
     * @ORM\Column(name="IND_CLIENTE", type="integer", nullable=false)
     */
    private $indCliente;

    /**
     * @var integer
     *
     * @ORM\Column(name="IND_FORNECEDOR", type="integer", nullable=false)
     */
    private $indFornecedor;

    /**
     * @var integer
     *
     * @ORM\Column(name="IND_TRANSPORTADORA", type="integer", nullable=false)
     */
    private $indTransportadora;

    /**
     * @var integer
     *
     * @ORM\Column(name="IND_ESTRANGEIRO", type="integer", nullable=false)
     */
    private $indEstrangeiro;

    /**
     * @var integer
     *
     * @ORM\Column(name="IND_ATIVO", type="integer", nullable=false)
     */
    private $indAtivo;

    /**
     * @var string
     *
     * @ORM\Column(name="OBSERVACAO", type="string", length=400, nullable=true)
     */
    private $observacao;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="DATA_CADASTRO", type="datetime", nullable=false)
     */
    private $dataCadastro;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="DATA_NASCIMENTO", type="date", nullable=true)
     */
    private $dataNascimento;

    /**
     * @var string
     *
     * @ORM\Column(name="LINK", type="string", length=200, nullable=true)
     */
    private $link;

    /**
     * @var integer
     *
     * @ORM\Column(name="COD_ORGANIZACAO_CADASTRO", type="integer", nullable=true)
     */
    private $codOrganizacaoCadastro;

    /**
     * @var \Entidades\ZgfinPessoaTipo
     *
     * @ORM\ManyToOne(targetEntity="Entidades\ZgfinPessoaTipo")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="COD_TIPO_PESSOA", referencedColumnName="CODIGO")
     * })
     */
    private $codTipoPessoa;

    /**
     * @var \Entidades\ZgadmOrganizacao
     *
     * @ORM\ManyToOne(targetEntity="Entidades\ZgadmOrganizacao")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="COD_PARCEIRO", referencedColumnName="CODIGO")
     * })
     */
    private $codParceiro;

    /**
     * @var \Entidades\ZgsegSexoTipo
     *
     * @ORM\ManyToOne(targetEntity="Entidades\ZgsegSexoTipo")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="COD_SEXO", referencedColumnName="CODIGO")
     * })
     */
    private $codSexo;


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
     * @return ZgfinPessoa
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
     * Set fantasia
     *
     * @param string $fantasia
     * @return ZgfinPessoa
     */
    public function setFantasia($fantasia)
    {
        $this->fantasia = $fantasia;

        return $this;
    }

    /**
     * Get fantasia
     *
     * @return string 
     */
    public function getFantasia()
    {
        return $this->fantasia;
    }

    /**
     * Set cgc
     *
     * @param string $cgc
     * @return ZgfinPessoa
     */
    public function setCgc($cgc)
    {
        $this->cgc = $cgc;

        return $this;
    }

    /**
     * Get cgc
     *
     * @return string 
     */
    public function getCgc()
    {
        return $this->cgc;
    }

    /**
     * Set rg
     *
     * @param string $rg
     * @return ZgfinPessoa
     */
    public function setRg($rg)
    {
        $this->rg = $rg;

        return $this;
    }

    /**
     * Get rg
     *
     * @return string 
     */
    public function getRg()
    {
        return $this->rg;
    }

    /**
     * Set inscEstadual
     *
     * @param string $inscEstadual
     * @return ZgfinPessoa
     */
    public function setInscEstadual($inscEstadual)
    {
        $this->inscEstadual = $inscEstadual;

        return $this;
    }

    /**
     * Get inscEstadual
     *
     * @return string 
     */
    public function getInscEstadual()
    {
        return $this->inscEstadual;
    }

    /**
     * Set inscMunicipal
     *
     * @param string $inscMunicipal
     * @return ZgfinPessoa
     */
    public function setInscMunicipal($inscMunicipal)
    {
        $this->inscMunicipal = $inscMunicipal;

        return $this;
    }

    /**
     * Get inscMunicipal
     *
     * @return string 
     */
    public function getInscMunicipal()
    {
        return $this->inscMunicipal;
    }

    /**
     * Set email
     *
     * @param string $email
     * @return ZgfinPessoa
     */
    public function setEmail($email)
    {
        $this->email = $email;

        return $this;
    }

    /**
     * Get email
     *
     * @return string 
     */
    public function getEmail()
    {
        return $this->email;
    }

    /**
     * Set indContribuinte
     *
     * @param integer $indContribuinte
     * @return ZgfinPessoa
     */
    public function setIndContribuinte($indContribuinte)
    {
        $this->indContribuinte = $indContribuinte;

        return $this;
    }

    /**
     * Get indContribuinte
     *
     * @return integer 
     */
    public function getIndContribuinte()
    {
        return $this->indContribuinte;
    }

    /**
     * Set indCliente
     *
     * @param integer $indCliente
     * @return ZgfinPessoa
     */
    public function setIndCliente($indCliente)
    {
        $this->indCliente = $indCliente;

        return $this;
    }

    /**
     * Get indCliente
     *
     * @return integer 
     */
    public function getIndCliente()
    {
        return $this->indCliente;
    }

    /**
     * Set indFornecedor
     *
     * @param integer $indFornecedor
     * @return ZgfinPessoa
     */
    public function setIndFornecedor($indFornecedor)
    {
        $this->indFornecedor = $indFornecedor;

        return $this;
    }

    /**
     * Get indFornecedor
     *
     * @return integer 
     */
    public function getIndFornecedor()
    {
        return $this->indFornecedor;
    }

    /**
     * Set indTransportadora
     *
     * @param integer $indTransportadora
     * @return ZgfinPessoa
     */
    public function setIndTransportadora($indTransportadora)
    {
        $this->indTransportadora = $indTransportadora;

        return $this;
    }

    /**
     * Get indTransportadora
     *
     * @return integer 
     */
    public function getIndTransportadora()
    {
        return $this->indTransportadora;
    }

    /**
     * Set indEstrangeiro
     *
     * @param integer $indEstrangeiro
     * @return ZgfinPessoa
     */
    public function setIndEstrangeiro($indEstrangeiro)
    {
        $this->indEstrangeiro = $indEstrangeiro;

        return $this;
    }

    /**
     * Get indEstrangeiro
     *
     * @return integer 
     */
    public function getIndEstrangeiro()
    {
        return $this->indEstrangeiro;
    }

    /**
     * Set indAtivo
     *
     * @param integer $indAtivo
     * @return ZgfinPessoa
     */
    public function setIndAtivo($indAtivo)
    {
        $this->indAtivo = $indAtivo;

        return $this;
    }

    /**
     * Get indAtivo
     *
     * @return integer 
     */
    public function getIndAtivo()
    {
        return $this->indAtivo;
    }

    /**
     * Set observacao
     *
     * @param string $observacao
     * @return ZgfinPessoa
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

    /**
     * Set dataCadastro
     *
     * @param \DateTime $dataCadastro
     * @return ZgfinPessoa
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
     * Set dataNascimento
     *
     * @param \DateTime $dataNascimento
     * @return ZgfinPessoa
     */
    public function setDataNascimento($dataNascimento)
    {
        $this->dataNascimento = $dataNascimento;

        return $this;
    }

    /**
     * Get dataNascimento
     *
     * @return \DateTime 
     */
    public function getDataNascimento()
    {
        return $this->dataNascimento;
    }

    /**
     * Set link
     *
     * @param string $link
     * @return ZgfinPessoa
     */
    public function setLink($link)
    {
        $this->link = $link;

        return $this;
    }

    /**
     * Get link
     *
     * @return string 
     */
    public function getLink()
    {
        return $this->link;
    }

    /**
     * Set codOrganizacaoCadastro
     *
     * @param integer $codOrganizacaoCadastro
     * @return ZgfinPessoa
     */
    public function setCodOrganizacaoCadastro($codOrganizacaoCadastro)
    {
        $this->codOrganizacaoCadastro = $codOrganizacaoCadastro;

        return $this;
    }

    /**
     * Get codOrganizacaoCadastro
     *
     * @return integer 
     */
    public function getCodOrganizacaoCadastro()
    {
        return $this->codOrganizacaoCadastro;
    }

    /**
     * Set codTipoPessoa
     *
     * @param \Entidades\ZgfinPessoaTipo $codTipoPessoa
     * @return ZgfinPessoa
     */
    public function setCodTipoPessoa(\Entidades\ZgfinPessoaTipo $codTipoPessoa = null)
    {
        $this->codTipoPessoa = $codTipoPessoa;

        return $this;
    }

    /**
     * Get codTipoPessoa
     *
     * @return \Entidades\ZgfinPessoaTipo 
     */
    public function getCodTipoPessoa()
    {
        return $this->codTipoPessoa;
    }

    /**
     * Set codParceiro
     *
     * @param \Entidades\ZgadmOrganizacao $codParceiro
     * @return ZgfinPessoa
     */
    public function setCodParceiro(\Entidades\ZgadmOrganizacao $codParceiro = null)
    {
        $this->codParceiro = $codParceiro;

        return $this;
    }

    /**
     * Get codParceiro
     *
     * @return \Entidades\ZgadmOrganizacao 
     */
    public function getCodParceiro()
    {
        return $this->codParceiro;
    }

    /**
     * Set codSexo
     *
     * @param \Entidades\ZgsegSexoTipo $codSexo
     * @return ZgfinPessoa
     */
    public function setCodSexo(\Entidades\ZgsegSexoTipo $codSexo = null)
    {
        $this->codSexo = $codSexo;

        return $this;
    }

    /**
     * Get codSexo
     *
     * @return \Entidades\ZgsegSexoTipo 
     */
    public function getCodSexo()
    {
        return $this->codSexo;
    }
}
