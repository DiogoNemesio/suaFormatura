<?php

namespace Entidades;

use Doctrine\ORM\Mapping as ORM;

/**
 * ZgfmtOrcamentoCortesiaTipo
 *
 * @ORM\Table(name="ZGFMT_ORCAMENTO_CORTESIA_TIPO")
 * @ORM\Entity
 */
class ZgfmtOrcamentoCortesiaTipo
{
    /**
     * @var string
     *
     * @ORM\Column(name="CODIGO", type="string", length=1, nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $codigo;

    /**
     * @var string
     *
     * @ORM\Column(name="DESCRICAO", type="string", length=60, nullable=true)
     */
    private $descricao;

    /**
     * @var string
     *
     * @ORM\Column(name="TEXTO", type="string", length=100, nullable=true)
     */
    private $texto;


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
     * @return ZgfmtOrcamentoCortesiaTipo
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
     * Set texto
     *
     * @param string $texto
     * @return ZgfmtOrcamentoCortesiaTipo
     */
    public function setTexto($texto)
    {
        $this->texto = $texto;

        return $this;
    }

    /**
     * Get texto
     *
     * @return string 
     */
    public function getTexto()
    {
        return $this->texto;
    }
}
