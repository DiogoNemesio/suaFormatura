<?php

namespace Entidades;

use Doctrine\ORM\Mapping as ORM;

/**
 * ZgsegUsuario
 *
 * @ORM\Table(name="ZGSEG_USUARIO", uniqueConstraints={@ORM\UniqueConstraint(name="EMAIL_UNIQUE", columns={"USUARIO"}), @ORM\UniqueConstraint(name="CPF_UNIQUE", columns={"CPF"})}, indexes={@ORM\Index(name="fk_ZGSEG_USUARIO_1_idx", columns={"AVATAR"}), @ORM\Index(name="fk_ZGSEG_USUARIO_4_idx", columns={"SEXO"}), @ORM\Index(name="fk_ZGSEG_USUARIO_2_idx", columns={"COD_LOGRADOURO"}), @ORM\Index(name="fk_ZGSEG_USUARIO_3_idx", columns={"COD_STATUS"}), @ORM\Index(name="fk_ZGSEG_USUARIO_5_idx", columns={"ULT_ORG_ACESSO"})})
 * @ORM\Entity
 */
class ZgsegUsuario
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
     * @ORM\Column(name="USUARIO", type="string", length=200, nullable=false)
     */
    private $usuario;

    /**
     * @var string
     *
     * @ORM\Column(name="CPF", type="string", length=11, nullable=true)
     */
    private $cpf;

    /**
     * @var string
     *
     * @ORM\Column(name="RG", type="string", length=14, nullable=true)
     */
    private $rg;

    /**
     * @var string
     *
     * @ORM\Column(name="NOME", type="string", length=100, nullable=false)
     */
    private $nome;

    /**
     * @var string
     *
     * @ORM\Column(name="APELIDO", type="string", length=60, nullable=true)
     */
    private $apelido;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="DATA_NASCIMENTO", type="date", nullable=true)
     */
    private $dataNascimento;

    /**
     * @var string
     *
     * @ORM\Column(name="SENHA", type="string", length=60, nullable=true)
     */
    private $senha;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="DATA_ULT_ACESSO", type="datetime", nullable=true)
     */
    private $dataUltAcesso;

    /**
     * @var integer
     *
     * @ORM\Column(name="IND_TROCAR_SENHA", type="integer", nullable=true)
     */
    private $indTrocarSenha;

    /**
     * @var integer
     *
     * @ORM\Column(name="IND_END_CORRETO", type="integer", nullable=true)
     */
    private $indEndCorreto;

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
     * @ORM\Column(name="CEP", type="string", length=8, nullable=true)
     */
    private $cep;

    /**
     * @var string
     *
     * @ORM\Column(name="COMPLEMENTO", type="string", length=100, nullable=true)
     */
    private $complemento;

    /**
     * @var \Entidades\ZgsegAvatar
     *
     * @ORM\ManyToOne(targetEntity="Entidades\ZgsegAvatar")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="AVATAR", referencedColumnName="CODIGO")
     * })
     */
    private $avatar;

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
     * @var \Entidades\ZgsegUsuarioStatusTipo
     *
     * @ORM\ManyToOne(targetEntity="Entidades\ZgsegUsuarioStatusTipo")
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
     *   @ORM\JoinColumn(name="SEXO", referencedColumnName="CODIGO")
     * })
     */
    private $sexo;

    /**
     * @var \Entidades\ZgadmOrganizacao
     *
     * @ORM\ManyToOne(targetEntity="Entidades\ZgadmOrganizacao")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="ULT_ORG_ACESSO", referencedColumnName="CODIGO")
     * })
     */
    private $ultOrgAcesso;


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
     * Set usuario
     *
     * @param string $usuario
     * @return ZgsegUsuario
     */
    public function setUsuario($usuario)
    {
        $this->usuario = $usuario;

        return $this;
    }

    /**
     * Get usuario
     *
     * @return string 
     */
    public function getUsuario()
    {
        return $this->usuario;
    }

    /**
     * Set cpf
     *
     * @param string $cpf
     * @return ZgsegUsuario
     */
    public function setCpf($cpf)
    {
        $this->cpf = $cpf;

        return $this;
    }

    /**
     * Get cpf
     *
     * @return string 
     */
    public function getCpf()
    {
        return $this->cpf;
    }

    /**
     * Set rg
     *
     * @param string $rg
     * @return ZgsegUsuario
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
     * Set nome
     *
     * @param string $nome
     * @return ZgsegUsuario
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
     * Set apelido
     *
     * @param string $apelido
     * @return ZgsegUsuario
     */
    public function setApelido($apelido)
    {
        $this->apelido = $apelido;

        return $this;
    }

    /**
     * Get apelido
     *
     * @return string 
     */
    public function getApelido()
    {
        return $this->apelido;
    }

    /**
     * Set dataNascimento
     *
     * @param \DateTime $dataNascimento
     * @return ZgsegUsuario
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
     * Set senha
     *
     * @param string $senha
     * @return ZgsegUsuario
     */
    public function setSenha($senha)
    {
        $this->senha = $senha;

        return $this;
    }

    /**
     * Get senha
     *
     * @return string 
     */
    public function getSenha()
    {
        return $this->senha;
    }

    /**
     * Set dataUltAcesso
     *
     * @param \DateTime $dataUltAcesso
     * @return ZgsegUsuario
     */
    public function setDataUltAcesso($dataUltAcesso)
    {
        $this->dataUltAcesso = $dataUltAcesso;

        return $this;
    }

    /**
     * Get dataUltAcesso
     *
     * @return \DateTime 
     */
    public function getDataUltAcesso()
    {
        return $this->dataUltAcesso;
    }

    /**
     * Set indTrocarSenha
     *
     * @param integer $indTrocarSenha
     * @return ZgsegUsuario
     */
    public function setIndTrocarSenha($indTrocarSenha)
    {
        $this->indTrocarSenha = $indTrocarSenha;

        return $this;
    }

    /**
     * Get indTrocarSenha
     *
     * @return integer 
     */
    public function getIndTrocarSenha()
    {
        return $this->indTrocarSenha;
    }

    /**
     * Set indEndCorreto
     *
     * @param integer $indEndCorreto
     * @return ZgsegUsuario
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
     * Set endereco
     *
     * @param string $endereco
     * @return ZgsegUsuario
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
     * @return ZgsegUsuario
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
     * @return ZgsegUsuario
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
     * @return ZgsegUsuario
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
     * Set complemento
     *
     * @param string $complemento
     * @return ZgsegUsuario
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
     * Set avatar
     *
     * @param \Entidades\ZgsegAvatar $avatar
     * @return ZgsegUsuario
     */
    public function setAvatar(\Entidades\ZgsegAvatar $avatar = null)
    {
        $this->avatar = $avatar;

        return $this;
    }

    /**
     * Get avatar
     *
     * @return \Entidades\ZgsegAvatar 
     */
    public function getAvatar()
    {
        return $this->avatar;
    }

    /**
     * Set codLogradouro
     *
     * @param \Entidades\ZgadmLogradouro $codLogradouro
     * @return ZgsegUsuario
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
     * @param \Entidades\ZgsegUsuarioStatusTipo $codStatus
     * @return ZgsegUsuario
     */
    public function setCodStatus(\Entidades\ZgsegUsuarioStatusTipo $codStatus = null)
    {
        $this->codStatus = $codStatus;

        return $this;
    }

    /**
     * Get codStatus
     *
     * @return \Entidades\ZgsegUsuarioStatusTipo 
     */
    public function getCodStatus()
    {
        return $this->codStatus;
    }

    /**
     * Set sexo
     *
     * @param \Entidades\ZgsegSexoTipo $sexo
     * @return ZgsegUsuario
     */
    public function setSexo(\Entidades\ZgsegSexoTipo $sexo = null)
    {
        $this->sexo = $sexo;

        return $this;
    }

    /**
     * Get sexo
     *
     * @return \Entidades\ZgsegSexoTipo 
     */
    public function getSexo()
    {
        return $this->sexo;
    }

    /**
     * Set ultOrgAcesso
     *
     * @param \Entidades\ZgadmOrganizacao $ultOrgAcesso
     * @return ZgsegUsuario
     */
    public function setUltOrgAcesso(\Entidades\ZgadmOrganizacao $ultOrgAcesso = null)
    {
        $this->ultOrgAcesso = $ultOrgAcesso;

        return $this;
    }

    /**
     * Get ultOrgAcesso
     *
     * @return \Entidades\ZgadmOrganizacao 
     */
    public function getUltOrgAcesso()
    {
        return $this->ultOrgAcesso;
    }
}
