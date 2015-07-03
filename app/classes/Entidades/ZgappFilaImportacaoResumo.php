<?php

namespace Entidades;

use Doctrine\ORM\Mapping as ORM;

/**
 * ZgappFilaImportacaoResumo
 *
 * @ORM\Table(name="ZGAPP_FILA_IMPORTACAO_RESUMO", indexes={@ORM\Index(name="fk_ZGAPP_FILA_IMPORTACAO_RESUMO_1_idx", columns={"COD_FILA"})})
 * @ORM\Entity
 */
class ZgappFilaImportacaoResumo
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
     * @ORM\Column(name="RESUMO", type="blob", nullable=true)
     */
    private $resumo;

    /**
     * @var \Entidades\ZgappFilaImportacao
     *
     * @ORM\ManyToOne(targetEntity="Entidades\ZgappFilaImportacao")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="COD_FILA", referencedColumnName="CODIGO")
     * })
     */
    private $codFila;


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
     * Set resumo
     *
     * @param string $resumo
     * @return ZgappFilaImportacaoResumo
     */
    public function setResumo($resumo)
    {
        $this->resumo = $resumo;

        return $this;
    }

    /**
     * Get resumo
     *
     * @return string 
     */
    public function getResumo()
    {
        return $this->resumo;
    }

    /**
     * Set codFila
     *
     * @param \Entidades\ZgappFilaImportacao $codFila
     * @return ZgappFilaImportacaoResumo
     */
    public function setCodFila(\Entidades\ZgappFilaImportacao $codFila = null)
    {
        $this->codFila = $codFila;

        return $this;
    }

    /**
     * Get codFila
     *
     * @return \Entidades\ZgappFilaImportacao 
     */
    public function getCodFila()
    {
        return $this->codFila;
    }
}
