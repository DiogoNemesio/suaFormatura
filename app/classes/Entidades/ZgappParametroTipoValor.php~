<?php

namespace Entidades;

use Doctrine\ORM\Mapping as ORM;

/**
 * ZgappParametroTipoValor
 *
 * @ORM\Table(name="ZGAPP_PARAMETRO_TIPO_VALOR", indexes={@ORM\Index(name="fk_ZGAPP_PARAMETRO_TIPO_VALOR_1_idx", columns={"COD_PARAMETRO"})})
 * @ORM\Entity
 */
class ZgappParametroTipoValor
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
     * @ORM\Column(name="VALOR", type="string", length=400, nullable=false)
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
     * @return ZgappParametroTipoValor
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
     * @return ZgappParametroTipoValor
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
