<?php

namespace Entidades;

use Doctrine\ORM\Mapping as ORM;

/**
 * ZgadmOrganizacao
 *
 * @ORM\Table(name="ZGADM_ORGANIZACAO", uniqueConstraints={@ORM\UniqueConstraint(name="IDENTIFICACAO_UNIQUE", columns={"IDENTIFICACAO"})}, indexes={@ORM\Index(name="fk_ZGADM_ORGANIZACAO_1_idx", columns={"COD_TIPO_PESSOA"}), @ORM\Index(name="fk_ZGADM_ORGANIZACAO_2_idx", columns={"COD_TIPO"}), @ORM\Index(name="fk_ZGADM_ORGANIZACAO_3_idx", columns={"COD_STATUS"}), @ORM\Index(name="fk_ZGADM_ORGANIZACAO_4_idx", columns={"COD_SEXO"}), @ORM\Index(name="fk_ZGADM_ORGANIZACAO_5_idx", columns={"COD_LOGRADOURO"})})
 * @ORM\Entity
 */
class ZgadmOrganizacao
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
     * @ORM\Column(name="IDENTIFICACAO", type="string", length=60, nullable=false)
     */
    private $identificacao;

    /**
     * @var string
     *
     * @ORM\Column(name="NOME", type="string", length=100, nullable=false)
     */
    private $nome;

    /**
     * @var string
     *
     * @ORM\Column(name="RAZAO", type="string", length=100, nullable=true)
     */
    private $razao;

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
     * @ORM\Column(name="LOGOMARCA", type="blob", nullable=true)
     */
    private $logomarca;

    /**
     * @var string
     *
     * @ORM\Column(name="EMAIL", type="string", length=200, nullable=true)
     */
    private $email;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="DATA_NASCIMENTO", type="date", nullable=true)
     */
    private $dataNascimento;

    /**
     * @var integer
     *
     * @ORM\Column(name="IND_END_CORRETO", type="integer", nullable=true)
     */
    private $indEndCorreto;

    /**
     * @var string
     *
     * @ORM\Column(name="CEP", type="string", length=8, nullable=true)
     */
    private $cep;

    /**
     * @var string
     *
     * @ORM\Column(name="ENDERECO", type="string", length=100, nullable=true)
     */
    private $endereco;

    /**
     * @var string
     *
     * @ORM\Column(name="BAIRRO", type="string", length=60, nullable=true)
     */
    private $bairro;

    /**
     * @var string
     *
     * @ORM\Column(name="NUMERO", type="string", length=10, nullable=true)
     */
    private $numero;

    /**
     * @var string
     *
     * @ORM\Column(name="COMPLEMENTO", type="string", length=100, nullable=true)
     */
    private $complemento;

    /**
     * @var string
     *
     * @ORM\Column(name="LATITUDE", type="string", length=15, nullable=true)
     */
    private $latitude;

    /**
     * @var string
     *
     * @ORM\Column(name="LONGITUDE", type="string", length=15, nullable=true)
     */
    private $longitude;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="DATA_CADASTRO", type="datetime", nullable=false)
     */
    private $dataCadastro;

    /**
     * @var \Entidades\ZgadmOrganizacaoPessoaTipo
     *
     * @ORM\ManyToOne(targetEntity="Entidades\ZgadmOrganizacaoPessoaTipo")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="COD_TIPO_PESSOA", referencedColumnName="CODIGO")
     * })
     */
    private $codTipoPessoa;

    /**
     * @var \Entidades\ZgadmOrganizacaoTipo
     *
     * @ORM\ManyToOne(targetEntity="Entidades\ZgadmOrganizacaoTipo")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="COD_TIPO", referencedColumnName="CODIGO")
     * })
     */
    private $codTipo;

    /**
     * @var \Entidades\ZgadmOrganizacaoStatusTipo
     *
     * @ORM\ManyToOne(targetEntity="Entidades\ZgadmOrganizacaoStatusTipo")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="COD_STATUS", referencedColumnName="CODIGO")
     * })
     */
    private $codStatus;

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
     * Set identificacao
     *
     * @param string $identificacao
     * @return ZgadmOrganizacao
     */
    public function setIdentificacao($identificacao)
    {
        $this->identificacao = $identificacao;

        return $this;
    }

    /**
     * Get identificacao
     *
     * @return string 
     */
    public function getIdentificacao()
    {
        return $this->identificacao;
    }

    /**
     * Set nome
     *
     * @param string $nome
     * @return ZgadmOrganizacao
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
     * Set razao
     *
     * @param string $razao
     * @return ZgadmOrganizacao
     */
    public function setRazao($razao)
    {
        $this->razao = $razao;

        return $this;
    }

    /**
     * Get razao
     *
     * @return string 
     */
    public function getRazao()
    {
        return $this->razao;
    }

    /**
     * Set cgc
     *
     * @param string $cgc
     * @return ZgadmOrganizacao
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
     * @return ZgadmOrganizacao
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
     * @return ZgadmOrganizacao
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
     * @return ZgadmOrganizacao
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
     * Set logomarca
     *
     * @param string $logomarca
     * @return ZgadmOrganizacao
     */
    public function setLogomarca($logomarca)
    {
        $this->logomarca = $logomarca;

        return $this;
    }

    /**
     * Get logomarca
     *
     * @return string 
     */
    public function getLogomarca()
    {
        return $this->logomarca;
    }

    /**
     * Set email
     *
     * @param string $email
     * @return ZgadmOrganizacao
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
     * Set dataNascimento
     *
     * @param \DateTime $dataNascimento
     * @return ZgadmOrganizacao
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
     * Set indEndCorreto
     *
     * @param integer $indEndCorreto
     * @return ZgadmOrganizacao
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
     * Set cep
     *
     * @param string $cep
     * @return ZgadmOrganizacao
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
     * Set endereco
     *
     * @param string $endereco
     * @return ZgadmOrganizacao
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
     * Set bairro
     *
     * @param string $bairro
     * @return ZgadmOrganizacao
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
     * Set numero
     *
     * @param string $numero
     * @return ZgadmOrganizacao
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
     * Set complemento
     *
     * @param string $complemento
     * @return ZgadmOrganizacao
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
     * Set latitude
     *
     * @param string $latitude
     * @return ZgadmOrganizacao
     */
    public function setLatitude($latitude)
    {
        $this->latitude = $latitude;

        return $this;
    }

    /**
     * Get latitude
     *
     * @return string 
     */
    public function getLatitude()
    {
        return $this->latitude;
    }

    /**
     * Set longitude
     *
     * @param string $longitude
     * @return ZgadmOrganizacao
     */
    public function setLongitude($longitude)
    {
        $this->longitude = $longitude;

        return $this;
    }

    /**
     * Get longitude
     *
     * @return string 
     */
    public function getLongitude()
    {
        return $this->longitude;
    }

    /**
     * Set dataCadastro
     *
     * @param \DateTime $dataCadastro
     * @return ZgadmOrganizacao
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
     * Set codTipoPessoa
     *
     * @param \Entidades\ZgadmOrganizacaoPessoaTipo $codTipoPessoa
     * @return ZgadmOrganizacao
     */
    public function setCodTipoPessoa(\Entidades\ZgadmOrganizacaoPessoaTipo $codTipoPessoa = null)
    {
        $this->codTipoPessoa = $codTipoPessoa;

        return $this;
    }

    /**
     * Get codTipoPessoa
     *
     * @return \Entidades\ZgadmOrganizacaoPessoaTipo 
     */
    public function getCodTipoPessoa()
    {
        return $this->codTipoPessoa;
    }

    /**
     * Set codTipo
     *
     * @param \Entidades\ZgadmOrganizacaoTipo $codTipo
     * @return ZgadmOrganizacao
     */
    public function setCodTipo(\Entidades\ZgadmOrganizacaoTipo $codTipo = null)
    {
        $this->codTipo = $codTipo;

        return $this;
    }

    /**
     * Get codTipo
     *
     * @return \Entidades\ZgadmOrganizacaoTipo 
     */
    public function getCodTipo()
    {
        return $this->codTipo;
    }

    /**
     * Set codStatus
     *
     * @param \Entidades\ZgadmOrganizacaoStatusTipo $codStatus
     * @return ZgadmOrganizacao
     */
    public function setCodStatus(\Entidades\ZgadmOrganizacaoStatusTipo $codStatus = null)
    {
        $this->codStatus = $codStatus;

        return $this;
    }

    /**
     * Get codStatus
     *
     * @return \Entidades\ZgadmOrganizacaoStatusTipo 
     */
    public function getCodStatus()
    {
        return $this->codStatus;
    }

    /**
     * Set codSexo
     *
     * @param \Entidades\ZgsegSexoTipo $codSexo
     * @return ZgadmOrganizacao
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

    /**
     * Set codLogradouro
     *
     * @param \Entidades\ZgadmLogradouro $codLogradouro
     * @return ZgadmOrganizacao
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
