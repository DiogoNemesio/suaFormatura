<?php

namespace Entidades;

use Doctrine\ORM\Mapping as ORM;

/**
 * ZgappModulo
 *
 * @ORM\Table(name="ZGAPP_MODULO")
 * @ORM\Entity
 */
class ZgappModulo
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
     * @ORM\Column(name="NOME", type="string", length=60, nullable=false)
     */
    private $nome;

    /**
     * @var string
     *
     * @ORM\Column(name="DESCRICAO", type="string", length=300, nullable=true)
     */
    private $descricao;

    /**
     * @var string
     *
     * @ORM\Column(name="APELIDO", type="string", length=3, nullable=false)
     */
    private $apelido;

    /**
     * @var string
     *
     * @ORM\Column(name="ICONE", type="string", length=200, nullable=true)
     */
    private $icone;

    /**
     * @var string
     *
     * @ORM\Column(name="CLASSE_CSS", type="string", length=60, nullable=true)
     */
    private $classeCss;


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
     * Set nome
     *
     * @param string $nome
     * @return ZgappModulo
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
     * Set descricao
     *
     * @param string $descricao
     * @return ZgappModulo
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
     * Set apelido
     *
     * @param string $apelido
     * @return ZgappModulo
     */
    public function setApelido($apelido)
    {
        $this->apelido = $apelido;

        return $this;
    }

    /**
     * Get apelido
     *
     * @return string 
     */
    public function getApelido()
    {
        return $this->apelido;
    }

    /**
     * Set icone
     *
     * @param string $icone
     * @return ZgappModulo
     */
    public function setIcone($icone)
    {
        $this->icone = $icone;

        return $this;
    }

    /**
     * Get icone
     *
     * @return string 
     */
    public function getIcone()
    {
        return $this->icone;
    }

    /**
     * Set classeCss
     *
     * @param string $classeCss
     * @return ZgappModulo
     */
    public function setClasseCss($classeCss)
    {
        $this->classeCss = $classeCss;

        return $this;
    }

    /**
     * Get classeCss
     *
     * @return string 
     */
    public function getClasseCss()
    {
        return $this->classeCss;
    }
}
