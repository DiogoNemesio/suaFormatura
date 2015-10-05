<?php

namespace Entidades;

use Doctrine\ORM\Mapping as ORM;

/**
 * ZgcasNoivos
 *
 * @ORM\Table(name="ZGCAS_NOIVOS", indexes={@ORM\Index(name="fk_ZGCAS_NOIVOS_1_idx", columns={"COD_NOIVOS_TIPO"}), @ORM\Index(name="fk_ZGCAS_NOIVOS_2_idx", columns={"COD_USUARIO"})})
 * @ORM\Entity
 */
class ZgcasNoivos
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
     * @var \Entidades\ZgcasNoivosTipo
     *
     * @ORM\ManyToOne(targetEntity="Entidades\ZgcasNoivosTipo")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="COD_NOIVOS_TIPO", referencedColumnName="CODIGO")
     * })
     */
    private $codNoivosTipo;

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
     * Get codigo
     *
     * @return integer 
     */
    public function getCodigo()
    {
        return $this->codigo;
    }

    /**
     * Set codNoivosTipo
     *
     * @param \Entidades\ZgcasNoivosTipo $codNoivosTipo
     * @return ZgcasNoivos
     */
    public function setCodNoivosTipo(\Entidades\ZgcasNoivosTipo $codNoivosTipo = null)
    {
        $this->codNoivosTipo = $codNoivosTipo;

        return $this;
    }

    /**
     * Get codNoivosTipo
     *
     * @return \Entidades\ZgcasNoivosTipo 
     */
    public function getCodNoivosTipo()
    {
        return $this->codNoivosTipo;
    }

    /**
     * Set codUsuario
     *
     * @param \Entidades\ZgsegUsuario $codUsuario
     * @return ZgcasNoivos
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
}
