<?php

namespace Entidades;

use Doctrine\ORM\Mapping as ORM;

/**
 * ZgadmSemaforo
 *
 * @ORM\Table(name="ZGADM_SEMAFORO")
 * @ORM\Entity
 */
class ZgadmSemaforo
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
     * @var integer
     *
     * @ORM\Column(name="COD_EMPRESA", type="integer", nullable=false)
     */
    private $codEmpresa;

    /**
     * @var string
     *
     * @ORM\Column(name="PARAMETRO", type="string", length=30, nullable=false)
     */
    private $parametro;

    /**
     * @var integer
     *
     * @ORM\Column(name="VALOR", type="integer", nullable=false)
     */
    private $valor;


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
     * Set codEmpresa
     *
     * @param integer $codEmpresa
     * @return ZgadmSemaforo
     */
    public function setCodEmpresa($codEmpresa)
    {
        $this->codEmpresa = $codEmpresa;

        return $this;
    }

    /**
     * Get codEmpresa
     *
     * @return integer 
     */
    public function getCodEmpresa()
    {
        return $this->codEmpresa;
    }

    /**
     * Set parametro
     *
     * @param string $parametro
     * @return ZgadmSemaforo
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
     * Set valor
     *
     * @param integer $valor
     * @return ZgadmSemaforo
     */
    public function setValor($valor)
    {
        $this->valor = $valor;

        return $this;
    }

    /**
     * Get valor
     *
     * @return integer 
     */
    public function getValor()
    {
        return $this->valor;
    }
}
