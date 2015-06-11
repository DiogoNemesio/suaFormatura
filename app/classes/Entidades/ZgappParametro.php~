<?php

namespace Entidades;

use Doctrine\ORM\Mapping as ORM;

/**
 * ZgappParametro
 *
 * @ORM\Table(name="ZGAPP_PARAMETRO", uniqueConstraints={@ORM\UniqueConstraint(name="PARAMETRO_UNIQUE", columns={"PARAMETRO"})}, indexes={@ORM\Index(name="fk_ZGAPP_PARAMETRO_1_idx", columns={"COD_TIPO"}), @ORM\Index(name="fk_ZGAPP_PARAMETRO_2_idx", columns={"COD_MODULO"}), @ORM\Index(name="fk_ZGAPP_PARAMETRO_3_idx", columns={"COD_SECAO"}), @ORM\Index(name="fk_ZGAPP_PARAMETRO_4_idx", columns={"COD_USO"})})
 * @ORM\Entity
 */
class ZgappParametro
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
     * @ORM\Column(name="PARAMETRO", type="string", length=60, nullable=false)
     */
    private $parametro;

    /**
     * @var string
     *
     * @ORM\Column(name="DESCRICAO", type="string", length=100, nullable=true)
     */
    private $descricao;

    /**
     * @var string
     *
     * @ORM\Column(name="VALOR_PADRAO", type="string", length=400, nullable=true)
     */
    private $valorPadrao;

    /**
     * @var integer
     *
     * @ORM\Column(name="IND_OBRIGATORIO", type="integer", nullable=true)
     */
    private $indObrigatorio;

    /**
     * @var integer
     *
     * @ORM\Column(name="TAMANHO", type="integer", nullable=true)
     */
    private $tamanho;

    /**
     * @var \Entidades\ZgappParametroTipo
     *
     * @ORM\ManyToOne(targetEntity="Entidades\ZgappParametroTipo")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="COD_TIPO", referencedColumnName="CODIGO")
     * })
     */
    private $codTipo;

    /**
     * @var \Entidades\ZgappModulo
     *
     * @ORM\ManyToOne(targetEntity="Entidades\ZgappModulo")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="COD_MODULO", referencedColumnName="CODIGO")
     * })
     */
    private $codModulo;

    /**
     * @var \Entidades\ZgappParametroSecao
     *
     * @ORM\ManyToOne(targetEntity="Entidades\ZgappParametroSecao")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="COD_SECAO", referencedColumnName="CODIGO")
     * })
     */
    private $codSecao;

    /**
     * @var \Entidades\ZgappParametroUso
     *
     * @ORM\ManyToOne(targetEntity="Entidades\ZgappParametroUso")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="COD_USO", referencedColumnName="CODIGO")
     * })
     */
    private $codUso;


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
     * Set parametro
     *
     * @param string $parametro
     * @return ZgappParametro
     */
    public function setParametro($parametro)
    {
        $this->parametro = $parametro;

        return $this;
    }

    /**
     * Get parametro
     *
     * @return string 
     */
    public function getParametro()
    {
        return $this->parametro;
    }

    /**
     * Set descricao
     *
     * @param string $descricao
     * @return ZgappParametro
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
     * Set valorPadrao
     *
     * @param string $valorPadrao
     * @return ZgappParametro
     */
    public function setValorPadrao($valorPadrao)
    {
        $this->valorPadrao = $valorPadrao;

        return $this;
    }

    /**
     * Get valorPadrao
     *
     * @return string 
     */
    public function getValorPadrao()
    {
        return $this->valorPadrao;
    }

    /**
     * Set indObrigatorio
     *
     * @param integer $indObrigatorio
     * @return ZgappParametro
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
     * Set tamanho
     *
     * @param integer $tamanho
     * @return ZgappParametro
     */
    public function setTamanho($tamanho)
    {
        $this->tamanho = $tamanho;

        return $this;
    }

    /**
     * Get tamanho
     *
     * @return integer 
     */
    public function getTamanho()
    {
        return $this->tamanho;
    }

    /**
     * Set codTipo
     *
     * @param \Entidades\ZgappParametroTipo $codTipo
     * @return ZgappParametro
     */
    public function setCodTipo(\Entidades\ZgappParametroTipo $codTipo = null)
    {
        $this->codTipo = $codTipo;

        return $this;
    }

    /**
     * Get codTipo
     *
     * @return \Entidades\ZgappParametroTipo 
     */
    public function getCodTipo()
    {
        return $this->codTipo;
    }

    /**
     * Set codModulo
     *
     * @param \Entidades\ZgappModulo $codModulo
     * @return ZgappParametro
     */
    public function setCodModulo(\Entidades\ZgappModulo $codModulo = null)
    {
        $this->codModulo = $codModulo;

        return $this;
    }

    /**
     * Get codModulo
     *
     * @return \Entidades\ZgappModulo 
     */
    public function getCodModulo()
    {
        return $this->codModulo;
    }

    /**
     * Set codSecao
     *
     * @param \Entidades\ZgappParametroSecao $codSecao
     * @return ZgappParametro
     */
    public function setCodSecao(\Entidades\ZgappParametroSecao $codSecao = null)
    {
        $this->codSecao = $codSecao;

        return $this;
    }

    /**
     * Get codSecao
     *
     * @return \Entidades\ZgappParametroSecao 
     */
    public function getCodSecao()
    {
        return $this->codSecao;
    }

    /**
     * Set codUso
     *
     * @param \Entidades\ZgappParametroUso $codUso
     * @return ZgappParametro
     */
    public function setCodUso(\Entidades\ZgappParametroUso $codUso = null)
    {
        $this->codUso = $codUso;

        return $this;
    }

    /**
     * Get codUso
     *
     * @return \Entidades\ZgappParametroUso 
     */
    public function getCodUso()
    {
        return $this->codUso;
    }
}
