<?php

namespace Entidades;

use Doctrine\ORM\Mapping as ORM;

/**
 * ZgsegUsuarioStatusTipo
 *
 * @ORM\Table(name="ZGSEG_USUARIO_STATUS_TIPO")
 * @ORM\Entity
 */
class ZgsegUsuarioStatusTipo
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
     * @ORM\Column(name="DESCRICAO", type="string", length=60, nullable=false)
     */
    private $descricao;

    /**
     * @var integer
     *
     * @ORM\Column(name="IND_PERMITE_ACESSO", type="integer", nullable=false)
     */
    private $indPermiteAcesso;


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
     * @return ZgsegUsuarioStatusTipo
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
     * Set indPermiteAcesso
     *
     * @param integer $indPermiteAcesso
     * @return ZgsegUsuarioStatusTipo
     */
    public function setIndPermiteAcesso($indPermiteAcesso)
    {
        $this->indPermiteAcesso = $indPermiteAcesso;

        return $this;
    }

    /**
     * Get indPermiteAcesso
     *
     * @return integer 
     */
    public function getIndPermiteAcesso()
    {
        return $this->indPermiteAcesso;
    }
}
