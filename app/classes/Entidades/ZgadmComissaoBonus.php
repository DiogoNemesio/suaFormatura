<?php

namespace Entidades;

use Doctrine\ORM\Mapping as ORM;

/**
 * ZgadmComissaoBonus
 *
 * @ORM\Table(name="ZGADM_COMISSAO_BONUS")
 * @ORM\Entity
 */
class ZgadmComissaoBonus
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
     * @ORM\Column(name="NUM_FORMATURAS", type="integer", nullable=false)
     */
    private $numFormaturas;

    /**
     * @var integer
     *
     * @ORM\Column(name="NUM_PARCEIROS", type="integer", nullable=false)
     */
    private $numParceiros;

    /**
     * @var float
     *
     * @ORM\Column(name="PCT_BONUS", type="float", precision=10, scale=0, nullable=false)
     */
    private $pctBonus;


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
     * @return ZgadmComissaoBonus
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
     * Set numFormaturas
     *
     * @param integer $numFormaturas
     * @return ZgadmComissaoBonus
     */
    public function setNumFormaturas($numFormaturas)
    {
        $this->numFormaturas = $numFormaturas;

        return $this;
    }

    /**
     * Get numFormaturas
     *
     * @return integer 
     */
    public function getNumFormaturas()
    {
        return $this->numFormaturas;
    }

    /**
     * Set numParceiros
     *
     * @param integer $numParceiros
     * @return ZgadmComissaoBonus
     */
    public function setNumParceiros($numParceiros)
    {
        $this->numParceiros = $numParceiros;

        return $this;
    }

    /**
     * Get numParceiros
     *
     * @return integer 
     */
    public function getNumParceiros()
    {
        return $this->numParceiros;
    }

    /**
     * Set pctBonus
     *
     * @param float $pctBonus
     * @return ZgadmComissaoBonus
     */
    public function setPctBonus($pctBonus)
    {
        $this->pctBonus = $pctBonus;

        return $this;
    }

    /**
     * Get pctBonus
     *
     * @return float 
     */
    public function getPctBonus()
    {
        return $this->pctBonus;
    }
}
