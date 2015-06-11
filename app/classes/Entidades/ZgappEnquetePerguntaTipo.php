<?php

namespace Entidades;

use Doctrine\ORM\Mapping as ORM;

/**
 * ZgappEnquetePerguntaTipo
 *
 * @ORM\Table(name="ZGAPP_ENQUETE_PERGUNTA_TIPO")
 * @ORM\Entity
 */
class ZgappEnquetePerguntaTipo
{
    /**
     * @var string
     *
     * @ORM\Column(name="CODIGO", type="string", length=2, nullable=false)
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
     * @var integer
     *
     * @ORM\Column(name="INT_ATIVO", type="integer", nullable=false)
     */
    private $intAtivo;


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
     * Set descricao
     *
     * @param string $descricao
     * @return ZgappEnquetePerguntaTipo
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
     * Set intAtivo
     *
     * @param integer $intAtivo
     * @return ZgappEnquetePerguntaTipo
     */
    public function setIntAtivo($intAtivo)
    {
        $this->intAtivo = $intAtivo;

        return $this;
    }

    /**
     * Get intAtivo
     *
     * @return integer 
     */
    public function getIntAtivo()
    {
        return $this->intAtivo;
    }
}