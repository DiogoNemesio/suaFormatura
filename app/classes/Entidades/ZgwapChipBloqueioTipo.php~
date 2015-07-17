<?php

namespace Entidades;

use Doctrine\ORM\Mapping as ORM;

/**
 * ZgwapChipBloqueioTipo
 *
 * @ORM\Table(name="ZGWAP_CHIP_BLOQUEIO_TIPO")
 * @ORM\Entity
 */
class ZgwapChipBloqueioTipo
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
     * @ORM\Column(name="NOME", type="string", length=60, nullable=false)
     */
    private $nome;


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
     * @return ZgwapChipBloqueioTipo
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
}
