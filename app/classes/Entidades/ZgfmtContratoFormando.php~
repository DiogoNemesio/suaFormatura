<?php

namespace Entidades;

use Doctrine\ORM\Mapping as ORM;

/**
 * ZgfmtContratoFormando
 *
 * @ORM\Table(name="ZGFMT_CONTRATO_FORMANDO", indexes={@ORM\Index(name="fk_ZGFMT_CONTRATO_FORMANDO_1_idx", columns={"COD_ORGANIZACAO"}), @ORM\Index(name="fk_ZGFMT_CONTRATO_FORMANDO_2_idx", columns={"COD_FORMANDO"})})
 * @ORM\Entity
 */
class ZgfmtContratoFormando
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
     * @ORM\Column(name="NUM_MESES", type="integer", nullable=false)
     */
    private $numMeses;

    /**
     * @var float
     *
     * @ORM\Column(name="PCT_DESCONTO", type="float", precision=10, scale=0, nullable=false)
     */
    private $pctDesconto;

    /**
     * @var \Entidades\ZgadmOrganizacao
     *
     * @ORM\ManyToOne(targetEntity="Entidades\ZgadmOrganizacao")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="COD_ORGANIZACAO", referencedColumnName="CODIGO")
     * })
     */
    private $codOrganizacao;

    /**
     * @var \Entidades\ZgsegUsuario
     *
     * @ORM\ManyToOne(targetEntity="Entidades\ZgsegUsuario")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="COD_FORMANDO", referencedColumnName="CODIGO")
     * })
     */
    private $codFormando;


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
     * Set numMeses
     *
     * @param integer $numMeses
     * @return ZgfmtContratoFormando
     */
    public function setNumMeses($numMeses)
    {
        $this->numMeses = $numMeses;

        return $this;
    }

    /**
     * Get numMeses
     *
     * @return integer 
     */
    public function getNumMeses()
    {
        return $this->numMeses;
    }

    /**
     * Set pctDesconto
     *
     * @param float $pctDesconto
     * @return ZgfmtContratoFormando
     */
    public function setPctDesconto($pctDesconto)
    {
        $this->pctDesconto = $pctDesconto;

        return $this;
    }

    /**
     * Get pctDesconto
     *
     * @return float 
     */
    public function getPctDesconto()
    {
        return $this->pctDesconto;
    }

    /**
     * Set codOrganizacao
     *
     * @param \Entidades\ZgadmOrganizacao $codOrganizacao
     * @return ZgfmtContratoFormando
     */
    public function setCodOrganizacao(\Entidades\ZgadmOrganizacao $codOrganizacao = null)
    {
        $this->codOrganizacao = $codOrganizacao;

        return $this;
    }

    /**
     * Get codOrganizacao
     *
     * @return \Entidades\ZgadmOrganizacao 
     */
    public function getCodOrganizacao()
    {
        return $this->codOrganizacao;
    }

    /**
     * Set codFormando
     *
     * @param \Entidades\ZgsegUsuario $codFormando
     * @return ZgfmtContratoFormando
     */
    public function setCodFormando(\Entidades\ZgsegUsuario $codFormando = null)
    {
        $this->codFormando = $codFormando;

        return $this;
    }

    /**
     * Get codFormando
     *
     * @return \Entidades\ZgsegUsuario 
     */
    public function getCodFormando()
    {
        return $this->codFormando;
    }
}
