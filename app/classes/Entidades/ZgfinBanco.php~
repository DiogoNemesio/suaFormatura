<?php

namespace Entidades;

use Doctrine\ORM\Mapping as ORM;

/**
 * ZgfinBanco
 *
 * @ORM\Table(name="ZGFIN_BANCO")
 * @ORM\Entity
 */
class ZgfinBanco
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
     * @ORM\Column(name="COD_BANCO", type="string", length=5, nullable=true)
     */
    private $codBanco;

    /**
     * @var string
     *
     * @ORM\Column(name="CNPJ", type="string", length=14, nullable=true)
     */
    private $cnpj;

    /**
     * @var string
     *
     * @ORM\Column(name="NOME", type="string", length=60, nullable=false)
     */
    private $nome;

    /**
     * @var string
     *
     * @ORM\Column(name="SITE", type="string", length=120, nullable=true)
     */
    private $site;


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
     * Set codBanco
     *
     * @param string $codBanco
     * @return ZgfinBanco
     */
    public function setCodBanco($codBanco)
    {
        $this->codBanco = $codBanco;

        return $this;
    }

    /**
     * Get codBanco
     *
     * @return string 
     */
    public function getCodBanco()
    {
        return $this->codBanco;
    }

    /**
     * Set cnpj
     *
     * @param string $cnpj
     * @return ZgfinBanco
     */
    public function setCnpj($cnpj)
    {
        $this->cnpj = $cnpj;

        return $this;
    }

    /**
     * Get cnpj
     *
     * @return string 
     */
    public function getCnpj()
    {
        return $this->cnpj;
    }

    /**
     * Set nome
     *
     * @param string $nome
     * @return ZgfinBanco
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
     * Set site
     *
     * @param string $site
     * @return ZgfinBanco
     */
    public function setSite($site)
    {
        $this->site = $site;

        return $this;
    }

    /**
     * Get site
     *
     * @return string 
     */
    public function getSite()
    {
        return $this->site;
    }
}
