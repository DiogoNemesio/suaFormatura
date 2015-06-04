<?php

namespace Entidades;

use Doctrine\ORM\Mapping as ORM;

/**
 * ZgadmEstado
 *
 * @ORM\Table(name="ZGADM_ESTADO", indexes={@ORM\Index(name="fk_ESTADOS_1_idx", columns={"COD_REGIAO"})})
 * @ORM\Entity
 */
class ZgadmEstado
{
    /**
     * @var string
     *
     * @ORM\Column(name="COD_UF", type="string", length=2, nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $codUf;

    /**
     * @var string
     *
     * @ORM\Column(name="NOME", type="string", length=60, nullable=false)
     */
    private $nome;

    /**
     * @var \Entidades\ZgadmRegiao
     *
     * @ORM\ManyToOne(targetEntity="Entidades\ZgadmRegiao")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="COD_REGIAO", referencedColumnName="CODIGO")
     * })
     */
    private $codRegiao;


    /**
     * Get codUf
     *
     * @return string 
     */
    public function getCodUf()
    {
        return $this->codUf;
    }

    /**
     * Set nome
     *
     * @param string $nome
     * @return ZgadmEstado
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
     * Set codRegiao
     *
     * @param \Entidades\ZgadmRegiao $codRegiao
     * @return ZgadmEstado
     */
    public function setCodRegiao(\Entidades\ZgadmRegiao $codRegiao = null)
    {
        $this->codRegiao = $codRegiao;

        return $this;
    }

    /**
     * Get codRegiao
     *
     * @return \Entidades\ZgadmRegiao 
     */
    public function getCodRegiao()
    {
        return $this->codRegiao;
    }
}
