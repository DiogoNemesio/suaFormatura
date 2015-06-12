<?php

namespace Entidades;

use Doctrine\ORM\Mapping as ORM;

/**
 * ZgappParametroUso
 *
 * @ORM\Table(name="ZGAPP_PARAMETRO_USO")
 * @ORM\Entity
 */
class ZgappParametroUso
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
     * @ORM\Column(name="NOME", type="string", length=40, nullable=false)
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
     * @return ZgappParametroUso
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
