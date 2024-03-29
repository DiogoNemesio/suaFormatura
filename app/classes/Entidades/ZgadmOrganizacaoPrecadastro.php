<?php

namespace Entidades;

use Doctrine\ORM\Mapping as ORM;

/**
 * ZgadmOrganizacaoPrecadastro
 *
 * @ORM\Table(name="ZGADM_ORGANIZACAO_PRECADASTRO", indexes={@ORM\Index(name="fk_ZGADM_ORGANIZACAO_PRECADASTRO_1_idx", columns={"COD_TIPO_PESSOA"}), @ORM\Index(name="fk_ZGADM_ORGANIZACAO_PRECADASTRO_2_idx", columns={"COD_CIDADE"}), @ORM\Index(name="fk_ZGADM_ORGANIZACAO_PRECADASTRO_3_idx", columns={"COD_ORGANIZACAO_PRECADASTRO_ATIVIDADE"})})
 * @ORM\Entity
 */
class ZgadmOrganizacaoPrecadastro
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
     * @ORM\Column(name="CGC", type="string", length=14, nullable=false)
     */
    private $cgc;

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
     * @ORM\Column(name="EMAIL", type="string", length=200, nullable=false)
     */
    private $email;

    /**
     * @var string
     *
     * @ORM\Column(name="TELEFONE_CELULAR", type="string", length=11, nullable=false)
     */
    private $telefoneCelular;

    /**
     * @var string
     *
     * @ORM\Column(name="TELEFONE_COMERCIAL", type="string", length=11, nullable=false)
     */
    private $telefoneComercial;

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
     * @var \Entidades\ZgadmCidade
     *
     * @ORM\ManyToOne(targetEntity="Entidades\ZgadmCidade")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="COD_CIDADE", referencedColumnName="CODIGO")
     * })
     */
    private $codCidade;

    /**
     * @var \Entidades\ZgadmOrganizacaoPrecadastroAtividade
     *
     * @ORM\ManyToOne(targetEntity="Entidades\ZgadmOrganizacaoPrecadastroAtividade")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="COD_ORGANIZACAO_PRECADASTRO_ATIVIDADE", referencedColumnName="CODIGO")
     * })
     */
    private $codOrganizacaoPrecadastroAtividade;


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
     * Set cgc
     *
     * @param string $cgc
     * @return ZgadmOrganizacaoPrecadastro
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
     * Set nome
     *
     * @param string $nome
     * @return ZgadmOrganizacaoPrecadastro
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
     * @return ZgadmOrganizacaoPrecadastro
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
     * Set email
     *
     * @param string $email
     * @return ZgadmOrganizacaoPrecadastro
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
     * Set telefoneCelular
     *
     * @param string $telefoneCelular
     * @return ZgadmOrganizacaoPrecadastro
     */
    public function setTelefoneCelular($telefoneCelular)
    {
        $this->telefoneCelular = $telefoneCelular;

        return $this;
    }

    /**
     * Get telefoneCelular
     *
     * @return string 
     */
    public function getTelefoneCelular()
    {
        return $this->telefoneCelular;
    }

    /**
     * Set telefoneComercial
     *
     * @param string $telefoneComercial
     * @return ZgadmOrganizacaoPrecadastro
     */
    public function setTelefoneComercial($telefoneComercial)
    {
        $this->telefoneComercial = $telefoneComercial;

        return $this;
    }

    /**
     * Get telefoneComercial
     *
     * @return string 
     */
    public function getTelefoneComercial()
    {
        return $this->telefoneComercial;
    }

    /**
     * Set codTipoPessoa
     *
     * @param \Entidades\ZgadmOrganizacaoPessoaTipo $codTipoPessoa
     * @return ZgadmOrganizacaoPrecadastro
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
     * Set codCidade
     *
     * @param \Entidades\ZgadmCidade $codCidade
     * @return ZgadmOrganizacaoPrecadastro
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
     * Set codOrganizacaoPrecadastroAtividade
     *
     * @param \Entidades\ZgadmOrganizacaoPrecadastroAtividade $codOrganizacaoPrecadastroAtividade
     * @return ZgadmOrganizacaoPrecadastro
     */
    public function setCodOrganizacaoPrecadastroAtividade(\Entidades\ZgadmOrganizacaoPrecadastroAtividade $codOrganizacaoPrecadastroAtividade = null)
    {
        $this->codOrganizacaoPrecadastroAtividade = $codOrganizacaoPrecadastroAtividade;

        return $this;
    }

    /**
     * Get codOrganizacaoPrecadastroAtividade
     *
     * @return \Entidades\ZgadmOrganizacaoPrecadastroAtividade 
     */
    public function getCodOrganizacaoPrecadastroAtividade()
    {
        return $this->codOrganizacaoPrecadastroAtividade;
    }
}
