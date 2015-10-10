<?php

namespace Entidades;

use Doctrine\ORM\Mapping as ORM;

/**
 * ZgestProdutoValor
 *
 * @ORM\Table(name="ZGEST_PRODUTO_VALOR", indexes={@ORM\Index(name="fk_ZGEST_PRODUTO_VALOR_1_idx", columns={"COD_PRODUTO"})})
 * @ORM\Entity
 */
class ZgestProdutoValor
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
     * @var float
     *
     * @ORM\Column(name="VALOR", type="float", precision=10, scale=0, nullable=false)
     */
    private $valor;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="DATA_BASE", type="date", nullable=false)
     */
    private $dataBase;

    /**
     * @var float
     *
     * @ORM\Column(name="DESCONTO_PORCENTO_MAX", type="float", precision=10, scale=0, nullable=true)
     */
    private $descontoPorcentoMax;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="DATA_CADASTRO", type="datetime", nullable=false)
     */
    private $dataCadastro;

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
     * @param float $valor
     * @return ZgestProdutoValor
     */
    public function setValor($valor)
    {
        $this->valor = $valor;

        return $this;
    }

    /**
     * Get valor
     *
     * @return float 
     */
    public function getValor()
    {
        return $this->valor;
    }

    /**
     * Set dataBase
     *
     * @param \DateTime $dataBase
     * @return ZgestProdutoValor
     */
    public function setDataBase($dataBase)
    {
        $this->dataBase = $dataBase;

        return $this;
    }

    /**
     * Get dataBase
     *
     * @return \DateTime 
     */
    public function getDataBase()
    {
        return $this->dataBase;
    }

    /**
     * Set descontoPorcentoMax
     *
     * @param float $descontoPorcentoMax
     * @return ZgestProdutoValor
     */
    public function setDescontoPorcentoMax($descontoPorcentoMax)
    {
        $this->descontoPorcentoMax = $descontoPorcentoMax;

        return $this;
    }

    /**
     * Get descontoPorcentoMax
     *
     * @return float 
     */
    public function getDescontoPorcentoMax()
    {
        return $this->descontoPorcentoMax;
    }

    /**
     * Set dataCadastro
     *
     * @param \DateTime $dataCadastro
     * @return ZgestProdutoValor
     */
    public function setDataCadastro($dataCadastro)
    {
        $this->dataCadastro = $dataCadastro;

        return $this;
    }

    /**
     * Get dataCadastro
     *
     * @return \DateTime 
     */
    public function getDataCadastro()
    {
        return $this->dataCadastro;
    }

    /**
     * Set codProduto
     *
     * @param \Entidades\ZgestProduto $codProduto
     * @return ZgestProdutoValor
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
}
