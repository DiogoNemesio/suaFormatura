<?php

namespace Entidades;

use Doctrine\ORM\Mapping as ORM;

/**
 * ZgfmtConvidadoFaixaEtaria
 *
 * @ORM\Table(name="ZGFMT_CONVIDADO_FAIXA_ETARIA")
 * @ORM\Entity
 */
class ZgfmtConvidadoFaixaEtaria
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
     * @ORM\Column(name="DESCRICAO", type="string", length=60, nullable=false)
     */
    private $descricao;

    /**
     * @var string
     *
     * @ORM\Column(name="IDADE_MINIMA", type="string", length=45, nullable=false)
     */
    private $idadeMinima;

    /**
     * @var string
     *
     * @ORM\Column(name="IDADE_MAXIMA", type="string", length=45, nullable=true)
     */
    private $idadeMaxima;


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
     * Set descricao
     *
     * @param string $descricao
     * @return ZgfmtConvidadoFaixaEtaria
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
     * Set idadeMinima
     *
     * @param string $idadeMinima
     * @return ZgfmtConvidadoFaixaEtaria
     */
    public function setIdadeMinima($idadeMinima)
    {
        $this->idadeMinima = $idadeMinima;

        return $this;
    }

    /**
     * Get idadeMinima
     *
     * @return string 
     */
    public function getIdadeMinima()
    {
        return $this->idadeMinima;
    }

    /**
     * Set idadeMaxima
     *
     * @param string $idadeMaxima
     * @return ZgfmtConvidadoFaixaEtaria
     */
    public function setIdadeMaxima($idadeMaxima)
    {
        $this->idadeMaxima = $idadeMaxima;

        return $this;
    }

    /**
     * Get idadeMaxima
     *
     * @return string 
     */
    public function getIdadeMaxima()
    {
        return $this->idadeMaxima;
    }
}
