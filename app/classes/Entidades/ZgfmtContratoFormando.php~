<?php

namespace Entidades;

use Doctrine\ORM\Mapping as ORM;

/**
 * ZgfmtContratoFormando
 *
 * @ORM\Table(name="ZGFMT_CONTRATO_FORMANDO", indexes={@ORM\Index(name="fk_ZGFMT_CONTRATO_FORMANDO_1_idx", columns={"COD_ORGANIZACAO"}), @ORM\Index(name="fk_ZGFMT_CONTRATO_FORMANDO_2_idx", columns={"COD_FORMANDO"}), @ORM\Index(name="fk_ZGFMT_CONTRATO_FORMANDO_3_idx", columns={"COD_FORMA_PAGAMENTO"}), @ORM\Index(name="fk_ZGFMT_CONTRATO_FORMANDO_4_idx", columns={"COD_TIPO_CONTRATO"})})
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
     * @var \Entidades\ZgfinFormaPagamento
     *
     * @ORM\ManyToOne(targetEntity="Entidades\ZgfinFormaPagamento")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="COD_FORMA_PAGAMENTO", referencedColumnName="CODIGO")
     * })
     */
    private $codFormaPagamento;

    /**
     * @var \Entidades\ZgfmtContratoFormandoTipo
     *
     * @ORM\ManyToOne(targetEntity="Entidades\ZgfmtContratoFormandoTipo")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="COD_TIPO_CONTRATO", referencedColumnName="CODIGO")
     * })
     */
    private $codTipoContrato;


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

    /**
     * Set codFormaPagamento
     *
     * @param \Entidades\ZgfinFormaPagamento $codFormaPagamento
     * @return ZgfmtContratoFormando
     */
    public function setCodFormaPagamento(\Entidades\ZgfinFormaPagamento $codFormaPagamento = null)
    {
        $this->codFormaPagamento = $codFormaPagamento;

        return $this;
    }

    /**
     * Get codFormaPagamento
     *
     * @return \Entidades\ZgfinFormaPagamento 
     */
    public function getCodFormaPagamento()
    {
        return $this->codFormaPagamento;
    }

    /**
     * Set codTipoContrato
     *
     * @param \Entidades\ZgfmtContratoFormandoTipo $codTipoContrato
     * @return ZgfmtContratoFormando
     */
    public function setCodTipoContrato(\Entidades\ZgfmtContratoFormandoTipo $codTipoContrato = null)
    {
        $this->codTipoContrato = $codTipoContrato;

        return $this;
    }

    /**
     * Get codTipoContrato
     *
     * @return \Entidades\ZgfmtContratoFormandoTipo 
     */
    public function getCodTipoContrato()
    {
        return $this->codTipoContrato;
    }
}
