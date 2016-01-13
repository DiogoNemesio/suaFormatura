<?php

namespace Entidades;

use Doctrine\ORM\Mapping as ORM;

/**
 * ZgfinPessoaOrganizacao
 *
 * @ORM\Table(name="ZGFIN_PESSOA_ORGANIZACAO", indexes={@ORM\Index(name="fk_ZGFIN_PESSOA_ORGANIZACAO_2_idx", columns={"COD_ORGANIZACAO"}), @ORM\Index(name="fk_ZGFIN_PESSOA_ORGANIZACAO_1_idx", columns={"COD_PESSOA"})})
 * @ORM\Entity
 */
class ZgfinPessoaOrganizacao
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
     * @ORM\Column(name="IND_CONTRIBUINTE", type="integer", nullable=true)
     */
    private $indContribuinte;

    /**
     * @var integer
     *
     * @ORM\Column(name="IND_CLIENTE", type="integer", nullable=true)
     */
    private $indCliente;

    /**
     * @var integer
     *
     * @ORM\Column(name="IND_FORNECEDOR", type="integer", nullable=true)
     */
    private $indFornecedor;

    /**
     * @var integer
     *
     * @ORM\Column(name="IND_TRANSPORTADORA", type="integer", nullable=true)
     */
    private $indTransportadora;

    /**
     * @var integer
     *
     * @ORM\Column(name="IND_FORMANDO", type="integer", nullable=true)
     */
    private $indFormando;

    /**
     * @var integer
     *
     * @ORM\Column(name="IND_ATIVO", type="integer", nullable=true)
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
     * @ORM\Column(name="DATA_CADASTRO", type="datetime", nullable=true)
     */
    private $dataCadastro;

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
     *   @ORM\JoinColumn(name="COD_PESSOA", referencedColumnName="CODIGO")
     * })
     */
    private $codPessoa;


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
     * Set indContribuinte
     *
     * @param integer $indContribuinte
     * @return ZgfinPessoaOrganizacao
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
     * @return ZgfinPessoaOrganizacao
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
     * @return ZgfinPessoaOrganizacao
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
     * @return ZgfinPessoaOrganizacao
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
     * Set indFormando
     *
     * @param integer $indFormando
     * @return ZgfinPessoaOrganizacao
     */
    public function setIndFormando($indFormando)
    {
        $this->indFormando = $indFormando;

        return $this;
    }

    /**
     * Get indFormando
     *
     * @return integer 
     */
    public function getIndFormando()
    {
        return $this->indFormando;
    }

    /**
     * Set indAtivo
     *
     * @param integer $indAtivo
     * @return ZgfinPessoaOrganizacao
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
     * @return ZgfinPessoaOrganizacao
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
     * @return ZgfinPessoaOrganizacao
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
     * Set codOrganizacao
     *
     * @param \Entidades\ZgadmOrganizacao $codOrganizacao
     * @return ZgfinPessoaOrganizacao
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
     * Set codPessoa
     *
     * @param \Entidades\ZgfinPessoa $codPessoa
     * @return ZgfinPessoaOrganizacao
     */
    public function setCodPessoa(\Entidades\ZgfinPessoa $codPessoa = null)
    {
        $this->codPessoa = $codPessoa;

        return $this;
    }

    /**
     * Get codPessoa
     *
     * @return \Entidades\ZgfinPessoa 
     */
    public function getCodPessoa()
    {
        return $this->codPessoa;
    }
}
