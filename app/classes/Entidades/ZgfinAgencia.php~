<?php

namespace Entidades;

use Doctrine\ORM\Mapping as ORM;

/**
 * ZgfinAgencia
 *
 * @ORM\Table(name="ZGFIN_AGENCIA", indexes={@ORM\Index(name="fk_ZGFIN_AGENCIA_1_idx", columns={"COD_ORGANIZACAO"}), @ORM\Index(name="fk_ZGFIN_AGENCIA_2_idx", columns={"COD_BANCO"})})
 * @ORM\Entity
 */
class ZgfinAgencia
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
     * @ORM\Column(name="NOME", type="string", length=60, nullable=true)
     */
    private $nome;

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
     * @return ZgfinAgencia
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
     * Set nome
     *
     * @param string $nome
     * @return ZgfinAgencia
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
     * Set codOrganizacao
     *
     * @param \Entidades\ZgadmOrganizacao $codOrganizacao
     * @return ZgfinAgencia
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
     * @return ZgfinAgencia
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
