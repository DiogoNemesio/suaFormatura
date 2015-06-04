<?php

namespace Entidades;

use Doctrine\ORM\Mapping as ORM;

/**
 * ZgfinPessoaEndereco
 *
 * @ORM\Table(name="ZGFIN_PESSOA_ENDERECO", indexes={@ORM\Index(name="fk_ZGFIN_PESSOA_ENDERECO_1_idx", columns={"COD_PESSOA"}), @ORM\Index(name="fk_ZGFIN_PESSOA_ENDERECO_2_idx", columns={"COD_TIPO_ENDERECO"}), @ORM\Index(name="fk_ZGFIN_PESSOA_ENDERECO_3_idx", columns={"COD_CIDADE"}), @ORM\Index(name="fk_ZGFIN_PESSOA_ENDERECO_4_idx", columns={"COD_LOGRADOURO"})})
 * @ORM\Entity
 */
class ZgfinPessoaEndereco
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
     * @ORM\Column(name="NUMERO", type="string", length=100, nullable=true)
     */
    private $numero;

    /**
     * @var string
     *
     * @ORM\Column(name="CEP", type="string", length=8, nullable=true)
     */
    private $cep;

    /**
     * @var string
     *
     * @ORM\Column(name="BAIRRO", type="string", length=60, nullable=true)
     */
    private $bairro;

    /**
     * @var string
     *
     * @ORM\Column(name="COMPLEMENTO", type="string", length=120, nullable=true)
     */
    private $complemento;

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
     * @var \Entidades\ZgadmCidade
     *
     * @ORM\ManyToOne(targetEntity="Entidades\ZgadmCidade")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="COD_CIDADE", referencedColumnName="CODIGO")
     * })
     */
    private $codCidade;

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
     * Get codigo
     *
     * @return integer 
     */
    public function getCodigo()
    {
        return $this->codigo;
    }

    /**
     * Set numero
     *
     * @param string $numero
     * @return ZgfinPessoaEndereco
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
     * @return ZgfinPessoaEndereco
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
     * @return ZgfinPessoaEndereco
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
     * @return ZgfinPessoaEndereco
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
     * Set codPessoa
     *
     * @param \Entidades\ZgfinPessoa $codPessoa
     * @return ZgfinPessoaEndereco
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
     * @return ZgfinPessoaEndereco
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
     * Set codCidade
     *
     * @param \Entidades\ZgadmCidade $codCidade
     * @return ZgfinPessoaEndereco
     */
    public function setCodCidade(\Entidades\ZgadmCidade $codCidade = null)
    {
        $this->codCidade = $codCidade;

        return $this;
    }

    /**
     * Get codCidade
     *
     * @return \Entidades\ZgadmCidade 
     */
    public function getCodCidade()
    {
        return $this->codCidade;
    }

    /**
     * Set codLogradouro
     *
     * @param \Entidades\ZgadmLogradouro $codLogradouro
     * @return ZgfinPessoaEndereco
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
}
