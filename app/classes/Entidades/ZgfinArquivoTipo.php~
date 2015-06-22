<?php

namespace Entidades;

use Doctrine\ORM\Mapping as ORM;

/**
 * ZgfinArquivoTipo
 *
 * @ORM\Table(name="ZGFIN_ARQUIVO_TIPO")
 * @ORM\Entity
 */
class ZgfinArquivoTipo
{
    /**
     * @var string
     *
     * @ORM\Column(name="CODIGO", type="string", length=4, nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $codigo;

    /**
     * @var string
     *
     * @ORM\Column(name="NOME", type="string", length=60, nullable=false)
     */
    private $nome;

    /**
     * @var integer
     *
     * @ORM\Column(name="IND_ATIVO", type="integer", nullable=false)
     */
    private $indAtivo;

    /**
     * @var integer
     *
     * @ORM\Column(name="IND_TAMANHO_FIXO", type="integer", nullable=false)
     */
    private $indTamanhoFixo;

    /**
     * @var integer
     *
     * @ORM\Column(name="TAMANHO", type="integer", nullable=true)
     */
    private $tamanho;


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
     * @return ZgfinArquivoTipo
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
     * Set indAtivo
     *
     * @param integer $indAtivo
     * @return ZgfinArquivoTipo
     */
    public function setIndAtivo($indAtivo)
    {
        $this->indAtivo = $indAtivo;

        return $this;
    }

    /**
     * Get indAtivo
     *
     * @return integer 
     */
    public function getIndAtivo()
    {
        return $this->indAtivo;
    }

    /**
     * Set indTamanhoFixo
     *
     * @param integer $indTamanhoFixo
     * @return ZgfinArquivoTipo
     */
    public function setIndTamanhoFixo($indTamanhoFixo)
    {
        $this->indTamanhoFixo = $indTamanhoFixo;

        return $this;
    }

    /**
     * Get indTamanhoFixo
     *
     * @return integer 
     */
    public function getIndTamanhoFixo()
    {
        return $this->indTamanhoFixo;
    }

    /**
     * Set tamanho
     *
     * @param integer $tamanho
     * @return ZgfinArquivoTipo
     */
    public function setTamanho($tamanho)
    {
        $this->tamanho = $tamanho;

        return $this;
    }

    /**
     * Get tamanho
     *
     * @return integer 
     */
    public function getTamanho()
    {
        return $this->tamanho;
    }
}
