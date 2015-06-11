<?php

namespace Entidades;

use Doctrine\ORM\Mapping as ORM;

/**
 * ZgadmParametroUsuario
 *
 * @ORM\Table(name="ZGADM_PARAMETRO_USUARIO", indexes={@ORM\Index(name="fk_ZGADM_PARAMETRO_USUARIO_1_idx", columns={"COD_USUARIO"}), @ORM\Index(name="fk_ZGADM_PARAMETRO_USUARIO_2_idx", columns={"COD_PARAMETRO"})})
 * @ORM\Entity
 */
class ZgadmParametroUsuario
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
     * @var \Entidades\ZgsegUsuario
     *
     * @ORM\ManyToOne(targetEntity="Entidades\ZgsegUsuario")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="COD_USUARIO", referencedColumnName="CODIGO")
     * })
     */
    private $codUsuario;

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
     * @return ZgadmParametroUsuario
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
     * Set codUsuario
     *
     * @param \Entidades\ZgsegUsuario $codUsuario
     * @return ZgadmParametroUsuario
     */
    public function setCodUsuario(\Entidades\ZgsegUsuario $codUsuario = null)
    {
        $this->codUsuario = $codUsuario;

        return $this;
    }

    /**
     * Get codUsuario
     *
     * @return \Entidades\ZgsegUsuario 
     */
    public function getCodUsuario()
    {
        return $this->codUsuario;
    }

    /**
     * Set codParametro
     *
     * @param \Entidades\ZgappParametro $codParametro
     * @return ZgadmParametroUsuario
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
