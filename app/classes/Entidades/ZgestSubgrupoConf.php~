<?php

namespace Entidades;

use Doctrine\ORM\Mapping as ORM;

/**
 * ZgestSubgrupoConf
 *
 * @ORM\Table(name="ZGEST_SUBGRUPO_CONF", indexes={@ORM\Index(name="fk_ZGEST_SUBGRUPO_CONF_1_idx", columns={"COD_SUBGRUPO"}), @ORM\Index(name="fk_ZGEST_SUBGRUPO_CONF_2_idx", columns={"COD_TIPO"})})
 * @ORM\Entity
 */
class ZgestSubgrupoConf
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
     * @ORM\Column(name="DESCRICAO", type="string", length=60, nullable=false)
     */
    private $descricao;

    /**
     * @var integer
     *
     * @ORM\Column(name="IND_OBRIGATORIO", type="integer", nullable=false)
     */
    private $indObrigatorio;

    /**
     * @var integer
     *
     * @ORM\Column(name="IND_ATIVO", type="integer", nullable=false)
     */
    private $indAtivo;

    /**
     * @var \Entidades\ZgestSubgrupo
     *
     * @ORM\ManyToOne(targetEntity="Entidades\ZgestSubgrupo")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="COD_SUBGRUPO", referencedColumnName="CODIGO")
     * })
     */
    private $codSubgrupo;

    /**
     * @var \Entidades\ZgestSubgrupoConfTipo
     *
     * @ORM\ManyToOne(targetEntity="Entidades\ZgestSubgrupoConfTipo")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="COD_TIPO", referencedColumnName="CODIGO")
     * })
     */
    private $codTipo;


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
     * @return ZgestSubgrupoConf
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
     * @return ZgestSubgrupoConf
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
     * Set indObrigatorio
     *
     * @param integer $indObrigatorio
     * @return ZgestSubgrupoConf
     */
    public function setIndObrigatorio($indObrigatorio)
    {
        $this->indObrigatorio = $indObrigatorio;

        return $this;
    }

    /**
     * Get indObrigatorio
     *
     * @return integer 
     */
    public function getIndObrigatorio()
    {
        return $this->indObrigatorio;
    }

    /**
     * Set indAtivo
     *
     * @param integer $indAtivo
     * @return ZgestSubgrupoConf
     */
    public function setIndAtivo($indAtivo)
    {
        $this->indAtivo = $indAtivo;

        return $this;
    }

    /**
     * Get indAtivo
     *
     * @return integer 
     */
    public function getIndAtivo()
    {
        return $this->indAtivo;
    }

    /**
     * Set codSubgrupo
     *
     * @param \Entidades\ZgestSubgrupo $codSubgrupo
     * @return ZgestSubgrupoConf
     */
    public function setCodSubgrupo(\Entidades\ZgestSubgrupo $codSubgrupo = null)
    {
        $this->codSubgrupo = $codSubgrupo;

        return $this;
    }

    /**
     * Get codSubgrupo
     *
     * @return \Entidades\ZgestSubgrupo 
     */
    public function getCodSubgrupo()
    {
        return $this->codSubgrupo;
    }

    /**
     * Set codTipo
     *
     * @param \Entidades\ZgestSubgrupoConfTipo $codTipo
     * @return ZgestSubgrupoConf
     */
    public function setCodTipo(\Entidades\ZgestSubgrupoConfTipo $codTipo = null)
    {
        $this->codTipo = $codTipo;

        return $this;
    }

    /**
     * Get codTipo
     *
     * @return \Entidades\ZgestSubgrupoConfTipo 
     */
    public function getCodTipo()
    {
        return $this->codTipo;
    }
}
