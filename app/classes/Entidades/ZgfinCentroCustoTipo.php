<?php

namespace Entidades;

use Doctrine\ORM\Mapping as ORM;

/**
 * ZgfinCentroCustoTipo
 *
 * @ORM\Table(name="ZGFIN_CENTRO_CUSTO_TIPO")
 * @ORM\Entity
 */
class ZgfinCentroCustoTipo
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
     * @return ZgfinCentroCustoTipo
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
