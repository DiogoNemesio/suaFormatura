<?php

namespace Entidades;

use Doctrine\ORM\Mapping as ORM;

/**
 * ZgsegDicionarioCampo
 *
 * @ORM\Table(name="ZGSEG_DICIONARIO_CAMPO", indexes={@ORM\Index(name="fk_DICIONARIO_CAMPO_1_idx", columns={"COD_DICIONARIO"})})
 * @ORM\Entity
 */
class ZgsegDicionarioCampo
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
     * @var integer
     *
     * @ORM\Column(name="ORDEM", type="integer", nullable=false)
     */
    private $ordem;

    /**
     * @var string
     *
     * @ORM\Column(name="DESCRICAO", type="string", length=100, nullable=true)
     */
    private $descricao;

    /**
     * @var integer
     *
     * @ORM\Column(name="IND_AUDIT", type="integer", nullable=false)
     */
    private $indAudit;

    /**
     * @var \Entidades\ZgsegDicionario
     *
     * @ORM\ManyToOne(targetEntity="Entidades\ZgsegDicionario")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="COD_DICIONARIO", referencedColumnName="CODIGO")
     * })
     */
    private $codDicionario;


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
     * @return ZgsegDicionarioCampo
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
     * Set ordem
     *
     * @param integer $ordem
     * @return ZgsegDicionarioCampo
     */
    public function setOrdem($ordem)
    {
        $this->ordem = $ordem;

        return $this;
    }

    /**
     * Get ordem
     *
     * @return integer 
     */
    public function getOrdem()
    {
        return $this->ordem;
    }

    /**
     * Set descricao
     *
     * @param string $descricao
     * @return ZgsegDicionarioCampo
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
     * @return ZgsegDicionarioCampo
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

    /**
     * Set codDicionario
     *
     * @param \Entidades\ZgsegDicionario $codDicionario
     * @return ZgsegDicionarioCampo
     */
    public function setCodDicionario(\Entidades\ZgsegDicionario $codDicionario = null)
    {
        $this->codDicionario = $codDicionario;

        return $this;
    }

    /**
     * Get codDicionario
     *
     * @return \Entidades\ZgsegDicionario 
     */
    public function getCodDicionario()
    {
        return $this->codDicionario;
    }
}
