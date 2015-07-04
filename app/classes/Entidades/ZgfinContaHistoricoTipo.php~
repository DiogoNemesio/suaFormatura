<?php

namespace Entidades;

use Doctrine\ORM\Mapping as ORM;

/**
 * ZgfinContaHistoricoTipo
 *
 * @ORM\Table(name="ZGFIN_CONTA_HISTORICO_TIPO")
 * @ORM\Entity
 */
class ZgfinContaHistoricoTipo
{
    /**
     * @var string
     *
     * @ORM\Column(name="CODIGO", type="string", length=3, nullable=false)
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
     * @return ZgfinContaHistoricoTipo
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
