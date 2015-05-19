<?php

namespace Entidades;

use Doctrine\ORM\Mapping as ORM;

/**
 * ZgforOrganizacao
 *
 * @ORM\Table(name="ZGFOR_ORGANIZACAO", uniqueConstraints={@ORM\UniqueConstraint(name="IDENTIFICACAO_UNIQUE", columns={"IDENTIFICACAO"})}, indexes={@ORM\Index(name="fk_ZGFOR_ORGANIZACAO_1_idx", columns={"COD_TIPO_PESSOA"}), @ORM\Index(name="fk_ZGFOR_ORGANIZACAO_2_idx", columns={"COD_TIPO"}), @ORM\Index(name="fk_ZGFOR_ORGANIZACAO_4_idx", columns={"COD_SERVICO"}), @ORM\Index(name="fk_ZGFOR_ORGANIZACAO_5_idx", columns={"COD_LOGRADOURO"})})
 * @ORM\Entity
 */
class ZgforOrganizacao
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
     * @ORM\Column(name="NOME", type="string", length=60, nullable=false)
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
     * @ORM\Column(name="RG", type="string", length=18, nullable=true)
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
     * @ORM\Column(name="EMAIL", type="string", length=120, nullable=true)
     */
    private $email;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="DATA_NASCIMENTO", type="date", nullable=true)
     */
    private $dataNascimento;

    /**
     * @var string
     *
     * @ORM\Column(name="COD_SEXO", type="string", length=1, nullable=true)
     */
    private $codSexo;

    /**
     * @var integer
     *
     * @ORM\Column(name="IND_ATIVO", type="integer", nullable=false)
     */
    private $indAtivo;

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
     * @ORM\Column(name="COMPLEMENTO", type="string", length=100, nullable=true)
     */
    private $complemento;

    /**
     * @var string
     *
     * @ORM\Column(name="NUMERO", type="string", length=10, nullable=true)
     */
    private $numero;

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
     * @var \Entidades\ZgforOrganizacaoPessoaTipo
     *
     * @ORM\ManyToOne(targetEntity="Entidades\ZgforOrganizacaoPessoaTipo")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="COD_TIPO_PESSOA", referencedColumnName="CODIGO")
     * })
     */
    private $codTipoPessoa;

    /**
     * @var \Entidades\ZgforOrganizacaoTipo
     *
     * @ORM\ManyToOne(targetEntity="Entidades\ZgforOrganizacaoTipo")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="COD_TIPO", referencedColumnName="CODIGO")
     * })
     */
    private $codTipo;

    /**
     * @var \Entidades\ZgforOrganizacaoServico
     *
     * @ORM\ManyToOne(targetEntity="Entidades\ZgforOrganizacaoServico")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="COD_SERVICO", referencedColumnName="CODIGO")
     * })
     */
    private $codServico;

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
     * @return ZgforOrganizacao
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
     * @return ZgforOrganizacao
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
     * @return ZgforOrganizacao
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
     * @return ZgforOrganizacao
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
     * @return ZgforOrganizacao
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
     * @return ZgforOrganizacao
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
     * @return ZgforOrganizacao
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
     * @return ZgforOrganizacao
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
     * @return ZgforOrganizacao
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
     * @return ZgforOrganizacao
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
     * Set codSexo
     *
     * @param string $codSexo
     * @return ZgforOrganizacao
     */
    public function setCodSexo($codSexo)
    {
        $this->codSexo = $codSexo;

        return $this;
    }

    /**
     * Get codSexo
     *
     * @return string 
     */
    public function getCodSexo()
    {
        return $this->codSexo;
    }

    /**
     * Set indAtivo
     *
     * @param integer $indAtivo
     * @return ZgforOrganizacao
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
     * Set dataCadastro
     *
     * @param \DateTime $dataCadastro
     * @return ZgforOrganizacao
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
     * @return ZgforOrganizacao
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
     * @return ZgforOrganizacao
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
     * Set cep
     *
     * @param string $cep
     * @return ZgforOrganizacao
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
     * @return ZgforOrganizacao
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
     * @return ZgforOrganizacao
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
     * @return ZgforOrganizacao
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
     * Set numero
     *
     * @param string $numero
     * @return ZgforOrganizacao
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
     * Set latitude
     *
     * @param string $latitude
     * @return ZgforOrganizacao
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
     * @return ZgforOrganizacao
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
     * @param \Entidades\ZgforOrganizacaoPessoaTipo $codTipoPessoa
     * @return ZgforOrganizacao
     */
    public function setCodTipoPessoa(\Entidades\ZgforOrganizacaoPessoaTipo $codTipoPessoa = null)
    {
        $this->codTipoPessoa = $codTipoPessoa;

        return $this;
    }

    /**
     * Get codTipoPessoa
     *
     * @return \Entidades\ZgforOrganizacaoPessoaTipo 
     */
    public function getCodTipoPessoa()
    {
        return $this->codTipoPessoa;
    }

    /**
     * Set codTipo
     *
     * @param \Entidades\ZgforOrganizacaoTipo $codTipo
     * @return ZgforOrganizacao
     */
    public function setCodTipo(\Entidades\ZgforOrganizacaoTipo $codTipo = null)
    {
        $this->codTipo = $codTipo;

        return $this;
    }

    /**
     * Get codTipo
     *
     * @return \Entidades\ZgforOrganizacaoTipo 
     */
    public function getCodTipo()
    {
        return $this->codTipo;
    }

    /**
     * Set codServico
     *
     * @param \Entidades\ZgforOrganizacaoServico $codServico
     * @return ZgforOrganizacao
     */
    public function setCodServico(\Entidades\ZgforOrganizacaoServico $codServico = null)
    {
        $this->codServico = $codServico;

        return $this;
    }

    /**
     * Get codServico
     *
     * @return \Entidades\ZgforOrganizacaoServico 
     */
    public function getCodServico()
    {
        return $this->codServico;
    }

    /**
     * Set codLogradouro
     *
     * @param \Entidades\ZgadmLogradouro $codLogradouro
     * @return ZgforOrganizacao
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
