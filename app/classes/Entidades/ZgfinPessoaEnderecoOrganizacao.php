<?php

namespace Entidades;

use Doctrine\ORM\Mapping as ORM;

/**
 * ZgfinPessoaEnderecoOrganizacao
 *
 * @ORM\Table(name="ZGFIN_PESSOA_ENDERECO_ORGANIZACAO", indexes={@ORM\Index(name="fk_ZGFIN_PESSOA_ENDERECO_ORGANIZACAO_1_idx", columns={"COD_PESSOA"}), @ORM\Index(name="fk_ZGFIN_PESSOA_ENDERECO_ORGANIZACAO_2_idx", columns={"COD_TIPO_ENDERECO"}), @ORM\Index(name="fk_ZGFIN_PESSOA_ENDERECO_ORGANIZACAO_3_idx", columns={"COD_LOGRADOURO"}), @ORM\Index(name="fk_ZGFIN_PESSOA_ENDERECO_ORGANIZACAO_4_idx", columns={"COD_ORGANIZACAO"})})
 * @ORM\Entity
 */
class ZgfinPessoaEnderecoOrganizacao
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
     * @ORM\Column(name="ENDERECO", type="string", length=100, nullable=true)
     */
    private $endereco;

    /**
     * @var string
     *
     * @ORM\Column(name="NUMERO", type="string", length=10, nullable=true)
     */
    private $numero;

    /**
     * @var string
     *
     * @ORM\Column(name="CEP", type="string", length=8, nullable=false)
     */
    private $cep;

    /**
     * @var string
     *
     * @ORM\Column(name="BAIRRO", type="string", length=60, nullable=false)
     */
    private $bairro;

    /**
     * @var string
     *
     * @ORM\Column(name="COMPLEMENTO", type="string", length=120, nullable=true)
     */
    private $complemento;

    /**
     * @var integer
     *
     * @ORM\Column(name="IND_END_CORRETO", type="integer", nullable=true)
     */
    private $indEndCorreto;

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
     * @var \Entidades\ZgfinEnderecoTipo
     *
     * @ORM\ManyToOne(targetEntity="Entidades\ZgfinEnderecoTipo")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="COD_TIPO_ENDERECO", referencedColumnName="CODIGO")
     * })
     */
    private $codTipoEndereco;

    /**
     * @var \Entidades\ZgadmLogradouro
     *
     * @ORM\ManyToOne(targetEntity="Entidades\ZgadmLogradouro")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="COD_LOGRADOURO", referencedColumnName="CODIGO")
     * })
     */
    private $codLogradouro;

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
     * Get codigo
     *
     * @return integer 
     */
    public function getCodigo()
    {
        return $this->codigo;
    }

    /**
     * Set endereco
     *
     * @param string $endereco
     * @return ZgfinPessoaEnderecoOrganizacao
     */
    public function setEndereco($endereco)
    {
        $this->endereco = $endereco;

        return $this;
    }

    /**
     * Get endereco
     *
     * @return string 
     */
    public function getEndereco()
    {
        return $this->endereco;
    }

    /**
     * Set numero
     *
     * @param string $numero
     * @return ZgfinPessoaEnderecoOrganizacao
     */
    public function setNumero($numero)
    {
        $this->numero = $numero;

        return $this;
    }

    /**
     * Get numero
     *
     * @return string 
     */
    public function getNumero()
    {
        return $this->numero;
    }

    /**
     * Set cep
     *
     * @param string $cep
     * @return ZgfinPessoaEnderecoOrganizacao
     */
    public function setCep($cep)
    {
        $this->cep = $cep;

        return $this;
    }

    /**
     * Get cep
     *
     * @return string 
     */
    public function getCep()
    {
        return $this->cep;
    }

    /**
     * Set bairro
     *
     * @param string $bairro
     * @return ZgfinPessoaEnderecoOrganizacao
     */
    public function setBairro($bairro)
    {
        $this->bairro = $bairro;

        return $this;
    }

    /**
     * Get bairro
     *
     * @return string 
     */
    public function getBairro()
    {
        return $this->bairro;
    }

    /**
     * Set complemento
     *
     * @param string $complemento
     * @return ZgfinPessoaEnderecoOrganizacao
     */
    public function setComplemento($complemento)
    {
        $this->complemento = $complemento;

        return $this;
    }

    /**
     * Get complemento
     *
     * @return string 
     */
    public function getComplemento()
    {
        return $this->complemento;
    }

    /**
     * Set indEndCorreto
     *
     * @param integer $indEndCorreto
     * @return ZgfinPessoaEnderecoOrganizacao
     */
    public function setIndEndCorreto($indEndCorreto)
    {
        $this->indEndCorreto = $indEndCorreto;

        return $this;
    }

    /**
     * Get indEndCorreto
     *
     * @return integer 
     */
    public function getIndEndCorreto()
    {
        return $this->indEndCorreto;
    }

    /**
     * Set codPessoa
     *
     * @param \Entidades\ZgfinPessoa $codPessoa
     * @return ZgfinPessoaEnderecoOrganizacao
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

    /**
     * Set codTipoEndereco
     *
     * @param \Entidades\ZgfinEnderecoTipo $codTipoEndereco
     * @return ZgfinPessoaEnderecoOrganizacao
     */
    public function setCodTipoEndereco(\Entidades\ZgfinEnderecoTipo $codTipoEndereco = null)
    {
        $this->codTipoEndereco = $codTipoEndereco;

        return $this;
    }

    /**
     * Get codTipoEndereco
     *
     * @return \Entidades\ZgfinEnderecoTipo 
     */
    public function getCodTipoEndereco()
    {
        return $this->codTipoEndereco;
    }

    /**
     * Set codLogradouro
     *
     * @param \Entidades\ZgadmLogradouro $codLogradouro
     * @return ZgfinPessoaEnderecoOrganizacao
     */
    public function setCodLogradouro(\Entidades\ZgadmLogradouro $codLogradouro = null)
    {
        $this->codLogradouro = $codLogradouro;

        return $this;
    }

    /**
     * Get codLogradouro
     *
     * @return \Entidades\ZgadmLogradouro 
     */
    public function getCodLogradouro()
    {
        return $this->codLogradouro;
    }

    /**
     * Set codOrganizacao
     *
     * @param \Entidades\ZgadmOrganizacao $codOrganizacao
     * @return ZgfinPessoaEnderecoOrganizacao
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
}
