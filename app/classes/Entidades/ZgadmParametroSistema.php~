<?php

namespace Entidades;

use Doctrine\ORM\Mapping as ORM;

/**
 * ZgadmParametroSistema
 *
 * @ORM\Table(name="ZGADM_PARAMETRO_SISTEMA", indexes={@ORM\Index(name="fk_ZGADM_PARAMETRO_SISTEMA_1", columns={"COD_PARAMETRO"})})
 * @ORM\Entity
 */
class ZgadmParametroSistema
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
     * @ORM\Column(name="VALOR", type="string", length=400, nullable=true)
     */
    private $valor;

    /**
     * @var \Entidades\ZgappParametro
     *
     * @ORM\ManyToOne(targetEntity="Entidades\ZgappParametro")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="COD_PARAMETRO", referencedColumnName="CODIGO")
     * })
     */
    private $codParametro;


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
     * @return ZgadmParametroSistema
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
     * Set codParametro
     *
     * @param \Entidades\ZgappParametro $codParametro
     * @return ZgadmParametroSistema
     */
    public function setCodParametro(\Entidades\ZgappParametro $codParametro = null)
    {
        $this->codParametro = $codParametro;

        return $this;
    }

    /**
     * Get codParametro
     *
     * @return \Entidades\ZgappParametro 
     */
    public function getCodParametro()
    {
        return $this->codParametro;
    }
}
