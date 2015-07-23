<?php

namespace Entidades;

use Doctrine\ORM\Mapping as ORM;

/**
 * ZgfinContaPagHistSub
 *
 * @ORM\Table(name="ZGFIN_CONTA_PAG_HIST_SUB", indexes={@ORM\Index(name="fk_ZGFIN_CONTA_PAG_HIST_SUB_1_idx", columns={"COD_CONTA"}), @ORM\Index(name="fk_ZGFIN_CONTA_PAG_HIST_SUB_2_idx", columns={"COD_USUARIO"})})
 * @ORM\Entity
 */
class ZgfinContaPagHistSub
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
     * @ORM\Column(name="DATA", type="datetime", nullable=false)
     */
    private $data;

    /**
     * @var \Entidades\ZgfinContaPagar
     *
     * @ORM\ManyToOne(targetEntity="Entidades\ZgfinContaPagar")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="COD_CONTA", referencedColumnName="CODIGO")
     * })
     */
    private $codConta;

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
     * Set valor
     *
     * @param float $valor
     * @return ZgfinContaPagHistSub
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
     * Set data
     *
     * @param \DateTime $data
     * @return ZgfinContaPagHistSub
     */
    public function setData($data)
    {
        $this->data = $data;

        return $this;
    }

    /**
     * Get data
     *
     * @return \DateTime 
     */
    public function getData()
    {
        return $this->data;
    }

    /**
     * Set codConta
     *
     * @param \Entidades\ZgfinContaPagar $codConta
     * @return ZgfinContaPagHistSub
     */
    public function setCodConta(\Entidades\ZgfinContaPagar $codConta = null)
    {
        $this->codConta = $codConta;

        return $this;
    }

    /**
     * Get codConta
     *
     * @return \Entidades\ZgfinContaPagar 
     */
    public function getCodConta()
    {
        return $this->codConta;
    }

    /**
     * Set codUsuario
     *
     * @param \Entidades\ZgsegUsuario $codUsuario
     * @return ZgfinContaPagHistSub
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
