<?php

namespace Entidades;

use Doctrine\ORM\Mapping as ORM;

/**
 * ZgadmCidade
 *
 * @ORM\Table(name="ZGADM_CIDADE", indexes={@ORM\Index(name="CIDADES_FK01_idx", columns={"COD_UF"})})
 * @ORM\Entity
 */
class ZgadmCidade
{
    /**
     * @var string
     *
     * @ORM\Column(name="CODIGO", type="string", length=8, nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $codigo;

    /**
     * @var string
     *
     * @ORM\Column(name="NOME", type="string", length=100, nullable=false)
     */
    private $nome;

    /**
     * @var \Entidades\ZgadmEstado
     *
     * @ORM\ManyToOne(targetEntity="Entidades\ZgadmEstado")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="COD_UF", referencedColumnName="COD_UF")
     * })
     */
    private $codUf;


    /**
     * Get codigo
     *
     * @return string 
     */
    public function getCodigo()
    {
        return $this->codigo;
    }

    /**
     * Set nome
     *
     * @param string $nome
     * @return ZgadmCidade
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
     * Set codUf
     *
     * @param \Entidades\ZgadmEstado $codUf
     * @return ZgadmCidade
     */
    public function setCodUf(\Entidades\ZgadmEstado $codUf = null)
    {
        $this->codUf = $codUf;

        return $this;
    }

    /**
     * Get codUf
     *
     * @return \Entidades\ZgadmEstado 
     */
    public function getCodUf()
    {
        return $this->codUf;
    }
}
