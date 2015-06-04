<?php

namespace Entidades;

use Doctrine\ORM\Mapping as ORM;

/**
 * ZgsegDicionario
 *
 * @ORM\Table(name="ZGSEG_DICIONARIO", indexes={@ORM\Index(name="ZGSEG_DICIONARIO_1_IDX", columns={"NOME"})})
 * @ORM\Entity
 */
class ZgsegDicionario
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
     * @ORM\Column(name="DESCRICAO", type="string", length=60, nullable=true)
     */
    private $descricao;

    /**
     * @var integer
     *
     * @ORM\Column(name="IND_AUDIT", type="integer", nullable=false)
     */
    private $indAudit;


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
     * @return ZgsegDicionario
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
     * @return ZgsegDicionario
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
     * Set indAudit
     *
     * @param integer $indAudit
     * @return ZgsegDicionario
     */
    public function setIndAudit($indAudit)
    {
        $this->indAudit = $indAudit;

        return $this;
    }

    /**
     * Get indAudit
     *
     * @return integer 
     */
    public function getIndAudit()
    {
        return $this->indAudit;
    }
}
