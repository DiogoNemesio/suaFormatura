<?php

namespace Entidades;

use Doctrine\ORM\Mapping as ORM;

/**
 * ZgfinMoeda
 *
 * @ORM\Table(name="ZGFIN_MOEDA")
 * @ORM\Entity
 */
class ZgfinMoeda
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
     * @ORM\Column(name="DESCRICAO", type="string", length=60, nullable=false)
     */
    private $descricao;

    /**
     * @var string
     *
     * @ORM\Column(name="SIMBOLO", type="string", length=8, nullable=false)
     */
    private $simbolo;

    /**
     * @var string
     *
     * @ORM\Column(name="COD_INTERNACIONAL", type="string", length=8, nullable=true)
     */
    private $codInternacional;

    /**
     * @var integer
     *
     * @ORM\Column(name="IND_ATIVA", type="integer", nullable=false)
     */
    private $indAtiva;


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
     * Set descricao
     *
     * @param string $descricao
     * @return ZgfinMoeda
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
     * Set simbolo
     *
     * @param string $simbolo
     * @return ZgfinMoeda
     */
    public function setSimbolo($simbolo)
    {
        $this->simbolo = $simbolo;

        return $this;
    }

    /**
     * Get simbolo
     *
     * @return string 
     */
    public function getSimbolo()
    {
        return $this->simbolo;
    }

    /**
     * Set codInternacional
     *
     * @param string $codInternacional
     * @return ZgfinMoeda
     */
    public function setCodInternacional($codInternacional)
    {
        $this->codInternacional = $codInternacional;

        return $this;
    }

    /**
     * Get codInternacional
     *
     * @return string 
     */
    public function getCodInternacional()
    {
        return $this->codInternacional;
    }

    /**
     * Set indAtiva
     *
     * @param integer $indAtiva
     * @return ZgfinMoeda
     */
    public function setIndAtiva($indAtiva)
    {
        $this->indAtiva = $indAtiva;

        return $this;
    }

    /**
     * Get indAtiva
     *
     * @return integer 
     */
    public function getIndAtiva()
    {
        return $this->indAtiva;
    }
}
