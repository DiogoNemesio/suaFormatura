<?php

namespace Entidades;

use Doctrine\ORM\Mapping as ORM;

/**
 * ZgadmParametro
 *
 * @ORM\Table(name="ZGADM_PARAMETRO", indexes={@ORM\Index(name="fk_ZGADM_PARAMETRO_1_idx", columns={"COD_MODULO"})})
 * @ORM\Entity
 */
class ZgadmParametro
{
    /**
     * @var string
     *
     * @ORM\Column(name="PARAMETRO", type="string", length=30, nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $parametro;

    /**
     * @var string
     *
     * @ORM\Column(name="DESCRICAO", type="string", length=60, nullable=false)
     */
    private $descricao;

    /**
     * @var string
     *
     * @ORM\Column(name="VALOR", type="string", length=200, nullable=true)
     */
    private $valor;

    /**
     * @var \Entidades\ZgappModulo
     *
     * @ORM\ManyToOne(targetEntity="Entidades\ZgappModulo")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="COD_MODULO", referencedColumnName="CODIGO")
     * })
     */
    private $codModulo;


    /**
     * Get parametro
     *
     * @return string 
     */
    public function getParametro()
    {
        return $this->parametro;
    }

    /**
     * Set descricao
     *
     * @param string $descricao
     * @return ZgadmParametro
     */
    public function setDescricao($descricao)
    {
        $this->descricao = $descricao;

        return $this;
    }

    /**
     * Get descricao
     *
     * @return string 
     */
    public function getDescricao()
    {
        return $this->descricao;
    }

    /**
     * Set valor
     *
     * @param string $valor
     * @return ZgadmParametro
     */
    public function setValor($valor)
    {
        $this->valor = $valor;

        return $this;
    }

    /**
     * Get valor
     *
     * @return string 
     */
    public function getValor()
    {
        return $this->valor;
    }

    /**
     * Set codModulo
     *
     * @param \Entidades\ZgappModulo $codModulo
     * @return ZgadmParametro
     */
    public function setCodModulo(\Entidades\ZgappModulo $codModulo = null)
    {
        $this->codModulo = $codModulo;

        return $this;
    }

    /**
     * Get codModulo
     *
     * @return \Entidades\ZgappModulo 
     */
    public function getCodModulo()
    {
        return $this->codModulo;
    }
}
