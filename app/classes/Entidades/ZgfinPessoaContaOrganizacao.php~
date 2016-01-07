<?php

namespace Entidades;

use Doctrine\ORM\Mapping as ORM;

/**
 * ZgfinPessoaContaOrganizacao
 *
 * @ORM\Table(name="ZGFIN_PESSOA_CONTA_ORGANIZACAO", indexes={@ORM\Index(name="fk_ZGFIN_PESSOA_CONTA_ORGANIZACAO_1_idx", columns={"COD_PESSOA"}), @ORM\Index(name="fk_ZGFIN_PESSOA_CONTA_ORGANIZACAO_2_idx", columns={"COD_ORGANIZACAO"}), @ORM\Index(name="fk_ZGFIN_PESSOA_CONTA_ORGANIZACAO_3_idx", columns={"COD_BANCO"})})
 * @ORM\Entity
 */
class ZgfinPessoaContaOrganizacao
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
     * @ORM\Column(name="AGENCIA", type="string", length=8, nullable=false)
     */
    private $agencia;

    /**
     * @var string
     *
     * @ORM\Column(name="CCORRENTE", type="string", length=20, nullable=false)
     */
    private $ccorrente;

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
     * @var \Entidades\ZgadmOrganizacao
     *
     * @ORM\ManyToOne(targetEntity="Entidades\ZgadmOrganizacao")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="COD_ORGANIZACAO", referencedColumnName="CODIGO")
     * })
     */
    private $codOrganizacao;

    /**
     * @var \Entidades\ZgfinBanco
     *
     * @ORM\ManyToOne(targetEntity="Entidades\ZgfinBanco")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="COD_BANCO", referencedColumnName="CODIGO")
     * })
     */
    private $codBanco;


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
     * Set agencia
     *
     * @param string $agencia
     * @return ZgfinPessoaContaOrganizacao
     */
    public function setAgencia($agencia)
    {
        $this->agencia = $agencia;

        return $this;
    }

    /**
     * Get agencia
     *
     * @return string 
     */
    public function getAgencia()
    {
        return $this->agencia;
    }

    /**
     * Set ccorrente
     *
     * @param string $ccorrente
     * @return ZgfinPessoaContaOrganizacao
     */
    public function setCcorrente($ccorrente)
    {
        $this->ccorrente = $ccorrente;

        return $this;
    }

    /**
     * Get ccorrente
     *
     * @return string 
     */
    public function getCcorrente()
    {
        return $this->ccorrente;
    }

    /**
     * Set codPessoa
     *
     * @param \Entidades\ZgfinPessoa $codPessoa
     * @return ZgfinPessoaContaOrganizacao
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
     * Set codOrganizacao
     *
     * @param \Entidades\ZgadmOrganizacao $codOrganizacao
     * @return ZgfinPessoaContaOrganizacao
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
     * Set codBanco
     *
     * @param \Entidades\ZgfinBanco $codBanco
     * @return ZgfinPessoaContaOrganizacao
     */
    public function setCodBanco(\Entidades\ZgfinBanco $codBanco = null)
    {
        $this->codBanco = $codBanco;

        return $this;
    }

    /**
     * Get codBanco
     *
     * @return \Entidades\ZgfinBanco 
     */
    public function getCodBanco()
    {
        return $this->codBanco;
    }
}
