<?php

namespace Entidades;

use Doctrine\ORM\Mapping as ORM;

/**
 * ZgfmtOrganizacao
 *
 * @ORM\Table(name="ZGFMT_ORGANIZACAO", uniqueConstraints={@ORM\UniqueConstraint(name="IDENTIFICACAO_UNIQUE", columns={"IDENTIFICACAO"})}, indexes={@ORM\Index(name="fk_ZGFMT_ORGANIZACAO_1", columns={"COD_TIPO_PESSOA"}), @ORM\Index(name="fk_ZGFMT_ORGANIZACAO_2", columns={"COD_TIPO"}), @ORM\Index(name="fk_ZGFMT_ORGANIZACAO_4", columns={"COD_LOGRADOURO"}), @ORM\Index(name="fk_ZGFMT_ORGANIZACAO_5_idx", columns={"COD_STATUS"}), @ORM\Index(name="fk_ZGFMT_ORGANIZACAO_3_idx", columns={"COD_SEXO"})})
 * @ORM\Entity
 */
class ZgfmtOrganizacao
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
     * @ORM\Column(name="RAZAO", type="string", length=60, nullable=true)
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
     * @var \DateTime
     *
     * @ORM\Column(name="DATA_CADASTRO", type="datetime", nullable=false)
     */
    private $dataCadastro;

    /**
     * @var string
     *
     * @ORM\Column(name="ANO_INICIO", type="string", length=4, nullable=true)
     */
    private $anoInicio;

    /**
     * @var string
     *
     * @ORM\Column(name="ANO_CONCLUSAO", type="string", length=4, nullable=true)
     */
    private $anoConclusao;

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
     * @var \Entidades\ZgfmtOrganizacaoPessoaTipo
     *
     * @ORM\ManyToOne(targetEntity="Entidades\ZgfmtOrganizacaoPessoaTipo")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="COD_TIPO_PESSOA", referencedColumnName="CODIGO")
     * })
     */
    private $codTipoPessoa;

    /**
     * @var \Entidades\ZgfmtOrganizacaoTipo
     *
     * @ORM\ManyToOne(targetEntity="Entidades\ZgfmtOrganizacaoTipo")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="COD_TIPO", referencedColumnName="CODIGO")
     * })
     */
    private $codTipo;

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
     * @var \Entidades\ZgfmtOrganizacaoStatusTipo
     *
     * @ORM\ManyToOne(targetEntity="Entidades\ZgfmtOrganizacaoStatusTipo")
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
     * @return ZgfmtOrganizacao
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
     * @return ZgfmtOrganizacao
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
     * @return ZgfmtOrganizacao
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
     * @return ZgfmtOrganizacao
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
     * @return ZgfmtOrganizacao
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
     * @return ZgfmtOrganizacao
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
     * @return ZgfmtOrganizacao
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
     * @return ZgfmtOrganizacao
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
     * @return ZgfmtOrganizacao
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
     * @return ZgfmtOrganizacao
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
     * Set dataCadastro
     *
     * @param \DateTime $dataCadastro
     * @return ZgfmtOrganizacao
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
     * Set anoInicio
     *
     * @param string $anoInicio
     * @return ZgfmtOrganizacao
     */
    public function setAnoInicio($anoInicio)
    {
        $this->anoInicio = $anoInicio;

        return $this;
    }

    /**
     * Get anoInicio
     *
     * @return string 
     */
    public function getAnoInicio()
    {
        return $this->anoInicio;
    }

    /**
     * Set anoConclusao
     *
     * @param string $anoConclusao
     * @return ZgfmtOrganizacao
     */
    public function setAnoConclusao($anoConclusao)
    {
        $this->anoConclusao = $anoConclusao;

        return $this;
    }

    /**
     * Get anoConclusao
     *
     * @return string 
     */
    public function getAnoConclusao()
    {
        return $this->anoConclusao;
    }

    /**
     * Set indEndCorreto
     *
     * @param integer $indEndCorreto
     * @return ZgfmtOrganizacao
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
     * @return ZgfmtOrganizacao
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
     * @return ZgfmtOrganizacao
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
     * @return ZgfmtOrganizacao
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
     * @return ZgfmtOrganizacao
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
     * @return ZgfmtOrganizacao
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
     * @return ZgfmtOrganizacao
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
     * @return ZgfmtOrganizacao
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
     * Set codTipoPessoa
     *
     * @param \Entidades\ZgfmtOrganizacaoPessoaTipo $codTipoPessoa
     * @return ZgfmtOrganizacao
     */
    public function setCodTipoPessoa(\Entidades\ZgfmtOrganizacaoPessoaTipo $codTipoPessoa = null)
    {
        $this->codTipoPessoa = $codTipoPessoa;

        return $this;
    }

    /**
     * Get codTipoPessoa
     *
     * @return \Entidades\ZgfmtOrganizacaoPessoaTipo 
     */
    public function getCodTipoPessoa()
    {
        return $this->codTipoPessoa;
    }

    /**
     * Set codTipo
     *
     * @param \Entidades\ZgfmtOrganizacaoTipo $codTipo
     * @return ZgfmtOrganizacao
     */
    public function setCodTipo(\Entidades\ZgfmtOrganizacaoTipo $codTipo = null)
    {
        $this->codTipo = $codTipo;

        return $this;
    }

    /**
     * Get codTipo
     *
     * @return \Entidades\ZgfmtOrganizacaoTipo 
     */
    public function getCodTipo()
    {
        return $this->codTipo;
    }

    /**
     * Set codLogradouro
     *
     * @param \Entidades\ZgadmLogradouro $codLogradouro
     * @return ZgfmtOrganizacao
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
     * Set codStatus
     *
     * @param \Entidades\ZgfmtOrganizacaoStatusTipo $codStatus
     * @return ZgfmtOrganizacao
     */
    public function setCodStatus(\Entidades\ZgfmtOrganizacaoStatusTipo $codStatus = null)
    {
        $this->codStatus = $codStatus;

        return $this;
    }

    /**
     * Get codStatus
     *
     * @return \Entidades\ZgfmtOrganizacaoStatusTipo 
     */
    public function getCodStatus()
    {
        return $this->codStatus;
    }

    /**
     * Set codSexo
     *
     * @param \Entidades\ZgsegSexoTipo $codSexo
     * @return ZgfmtOrganizacao
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
