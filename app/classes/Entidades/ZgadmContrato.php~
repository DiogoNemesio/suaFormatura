<?php

namespace Entidades;

use Doctrine\ORM\Mapping as ORM;

/**
 * ZgadmContrato
 *
 * @ORM\Table(name="ZGADM_CONTRATO", indexes={@ORM\Index(name="fk_ZGADM_CONTRATO_1_idx", columns={"COD_ORGANIZACAO"}), @ORM\Index(name="fk_ZGADM_CONTRATO_2_idx", columns={"COD_PLANO"}), @ORM\Index(name="fk_ZGADM_CONTRATO_3_idx", columns={"COD_STATUS"})})
 * @ORM\Entity
 */
class ZgadmContrato
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
     * @var \DateTime
     *
     * @ORM\Column(name="DATA_INICIO", type="date", nullable=false)
     */
    private $dataInicio;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="DATA_FIM", type="date", nullable=true)
     */
    private $dataFim;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="DATA_CADASTRO", type="datetime", nullable=false)
     */
    private $dataCadastro;

    /**
     * @var float
     *
     * @ORM\Column(name="PCT_DESCONTO", type="float", precision=10, scale=0, nullable=true)
     */
    private $pctDesconto;

    /**
     * @var float
     *
     * @ORM\Column(name="VALOR_DESCONTO", type="float", precision=10, scale=0, nullable=true)
     */
    private $valorDesconto;

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
     * @var \Entidades\ZgadmPlano
     *
     * @ORM\ManyToOne(targetEntity="Entidades\ZgadmPlano")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="COD_PLANO", referencedColumnName="CODIGO")
     * })
     */
    private $codPlano;

    /**
     * @var \Entidades\ZgadmContratoStatusTipo
     *
     * @ORM\ManyToOne(targetEntity="Entidades\ZgadmContratoStatusTipo")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="COD_STATUS", referencedColumnName="CODIGO")
     * })
     */
    private $codStatus;


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
     * Set dataInicio
     *
     * @param \DateTime $dataInicio
     * @return ZgadmContrato
     */
    public function setDataInicio($dataInicio)
    {
        $this->dataInicio = $dataInicio;

        return $this;
    }

    /**
     * Get dataInicio
     *
     * @return \DateTime 
     */
    public function getDataInicio()
    {
        return $this->dataInicio;
    }

    /**
     * Set dataFim
     *
     * @param \DateTime $dataFim
     * @return ZgadmContrato
     */
    public function setDataFim($dataFim)
    {
        $this->dataFim = $dataFim;

        return $this;
    }

    /**
     * Get dataFim
     *
     * @return \DateTime 
     */
    public function getDataFim()
    {
        return $this->dataFim;
    }

    /**
     * Set dataCadastro
     *
     * @param \DateTime $dataCadastro
     * @return ZgadmContrato
     */
    public function setDataCadastro($dataCadastro)
    {
        $this->dataCadastro = $dataCadastro;

        return $this;
    }

    /**
     * Get dataCadastro
     *
     * @return \DateTime 
     */
    public function getDataCadastro()
    {
        return $this->dataCadastro;
    }

    /**
     * Set pctDesconto
     *
     * @param float $pctDesconto
     * @return ZgadmContrato
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
     * Set valorDesconto
     *
     * @param float $valorDesconto
     * @return ZgadmContrato
     */
    public function setValorDesconto($valorDesconto)
    {
        $this->valorDesconto = $valorDesconto;

        return $this;
    }

    /**
     * Get valorDesconto
     *
     * @return float 
     */
    public function getValorDesconto()
    {
        return $this->valorDesconto;
    }

    /**
     * Set codOrganizacao
     *
     * @param \Entidades\ZgadmOrganizacao $codOrganizacao
     * @return ZgadmContrato
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
     * Set codPlano
     *
     * @param \Entidades\ZgadmPlano $codPlano
     * @return ZgadmContrato
     */
    public function setCodPlano(\Entidades\ZgadmPlano $codPlano = null)
    {
        $this->codPlano = $codPlano;

        return $this;
    }

    /**
     * Get codPlano
     *
     * @return \Entidades\ZgadmPlano 
     */
    public function getCodPlano()
    {
        return $this->codPlano;
    }

    /**
     * Set codStatus
     *
     * @param \Entidades\ZgadmContratoStatusTipo $codStatus
     * @return ZgadmContrato
     */
    public function setCodStatus(\Entidades\ZgadmContratoStatusTipo $codStatus = null)
    {
        $this->codStatus = $codStatus;

        return $this;
    }

    /**
     * Get codStatus
     *
     * @return \Entidades\ZgadmContratoStatusTipo 
     */
    public function getCodStatus()
    {
        return $this->codStatus;
    }
}
