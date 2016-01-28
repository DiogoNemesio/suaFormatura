<?php

namespace Entidades;

use Doctrine\ORM\Mapping as ORM;

/**
 * ZgfinArquivoVariavel
 *
 * @ORM\Table(name="ZGFIN_ARQUIVO_VARIAVEL")
 * @ORM\Entity
 */
class ZgfinArquivoVariavel
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
     * @ORM\Column(name="VARIAVEL", type="string", length=30, nullable=true)
     */
    private $variavel;

    /**
     * @var string
     *
     * @ORM\Column(name="DESCRICAO", type="string", length=100, nullable=false)
     */
    private $descricao;


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
     * Set variavel
     *
     * @param string $variavel
     * @return ZgfinArquivoVariavel
     */
    public function setVariavel($variavel)
    {
        $this->variavel = $variavel;

        return $this;
    }

    /**
     * Get variavel
     *
     * @return string 
     */
    public function getVariavel()
    {
        return $this->variavel;
    }

    /**
     * Set descricao
     *
     * @param string $descricao
     * @return ZgfinArquivoVariavel
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
}
