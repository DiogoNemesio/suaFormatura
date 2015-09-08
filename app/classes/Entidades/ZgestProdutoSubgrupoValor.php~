<?php

namespace Entidades;

use Doctrine\ORM\Mapping as ORM;

/**
 * ZgestProdutoSubgrupoValor
 *
 * @ORM\Table(name="ZGEST_PRODUTO_SUBGRUPO_VALOR", indexes={@ORM\Index(name="fk_ZGEST_PRODUTO_SUBGRUPO_VALOR_1_idx", columns={"COD_PRODUTO"}), @ORM\Index(name="fk_ZGEST_PRODUTO_SUBGRUPO_VALOR_2_idx", columns={"COD_SUBGRUPO_CONF"})})
 * @ORM\Entity
 */
class ZgestProdutoSubgrupoValor
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
     * @ORM\Column(name="VALOR", type="string", length=200, nullable=false)
     */
    private $valor;

    /**
     * @var \Entidades\ZgestProduto
     *
     * @ORM\ManyToOne(targetEntity="Entidades\ZgestProduto")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="COD_PRODUTO", referencedColumnName="CODIGO")
     * })
     */
    private $codProduto;

    /**
     * @var \Entidades\ZgestSubgrupoConf
     *
     * @ORM\ManyToOne(targetEntity="Entidades\ZgestSubgrupoConf")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="COD_SUBGRUPO_CONF", referencedColumnName="CODIGO")
     * })
     */
    private $codSubgrupoConf;


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
     * Set valor
     *
     * @param string $valor
     * @return ZgestProdutoSubgrupoValor
     */
    public function setValor($valor)
    {
        $this->valor = $valor;

        return $this;
    }

    /**
     * Get valor
     *
     * @return string 
     */
    public function getValor()
    {
        return $this->valor;
    }

    /**
     * Set codProduto
     *
     * @param \Entidades\ZgestProduto $codProduto
     * @return ZgestProdutoSubgrupoValor
     */
    public function setCodProduto(\Entidades\ZgestProduto $codProduto = null)
    {
        $this->codProduto = $codProduto;

        return $this;
    }

    /**
     * Get codProduto
     *
     * @return \Entidades\ZgestProduto 
     */
    public function getCodProduto()
    {
        return $this->codProduto;
    }

    /**
     * Set codSubgrupoConf
     *
     * @param \Entidades\ZgestSubgrupoConf $codSubgrupoConf
     * @return ZgestProdutoSubgrupoValor
     */
    public function setCodSubgrupoConf(\Entidades\ZgestSubgrupoConf $codSubgrupoConf = null)
    {
        $this->codSubgrupoConf = $codSubgrupoConf;

        return $this;
    }

    /**
     * Get codSubgrupoConf
     *
     * @return \Entidades\ZgestSubgrupoConf 
     */
    public function getCodSubgrupoConf()
    {
        return $this->codSubgrupoConf;
    }
}
