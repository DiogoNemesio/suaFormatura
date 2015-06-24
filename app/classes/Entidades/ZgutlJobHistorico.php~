<?php

namespace Entidades;

use Doctrine\ORM\Mapping as ORM;

/**
 * ZgutlJobHistorico
 *
 * @ORM\Table(name="ZGUTL_JOB_HISTORICO", indexes={@ORM\Index(name="fk_ZGUTL_JOB_HISTORICO_1_idx", columns={"COD_JOB"}), @ORM\Index(name="fk_ZGUTL_JOB_HISTORICO_2_idx", columns={"COD_STATUS"}), @ORM\Index(name="ZGUTL_JOB_HISTORICO_IX01", columns={"COD_JOB", "COD_STATUS"})})
 * @ORM\Entity
 */
class ZgutlJobHistorico
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
     * @ORM\Column(name="DATA_INICIO", type="datetime", nullable=false)
     */
    private $dataInicio;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="DATA_FIM", type="datetime", nullable=true)
     */
    private $dataFim;

    /**
     * @var string
     *
     * @ORM\Column(name="RETORNO", type="string", length=4000, nullable=true)
     */
    private $retorno;

    /**
     * @var \Entidades\ZgutlJob
     *
     * @ORM\ManyToOne(targetEntity="Entidades\ZgutlJob")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="COD_JOB", referencedColumnName="CODIGO")
     * })
     */
    private $codJob;

    /**
     * @var \Entidades\ZgutlJobStatusTipo
     *
     * @ORM\ManyToOne(targetEntity="Entidades\ZgutlJobStatusTipo")
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
     * @return ZgutlJobHistorico
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
     * @return ZgutlJobHistorico
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
     * Set retorno
     *
     * @param string $retorno
     * @return ZgutlJobHistorico
     */
    public function setRetorno($retorno)
    {
        $this->retorno = $retorno;

        return $this;
    }

    /**
     * Get retorno
     *
     * @return string 
     */
    public function getRetorno()
    {
        return $this->retorno;
    }

    /**
     * Set codJob
     *
     * @param \Entidades\ZgutlJob $codJob
     * @return ZgutlJobHistorico
     */
    public function setCodJob(\Entidades\ZgutlJob $codJob = null)
    {
        $this->codJob = $codJob;

        return $this;
    }

    /**
     * Get codJob
     *
     * @return \Entidades\ZgutlJob 
     */
    public function getCodJob()
    {
        return $this->codJob;
    }

    /**
     * Set codStatus
     *
     * @param \Entidades\ZgutlJobStatusTipo $codStatus
     * @return ZgutlJobHistorico
     */
    public function setCodStatus(\Entidades\ZgutlJobStatusTipo $codStatus = null)
    {
        $this->codStatus = $codStatus;

        return $this;
    }

    /**
     * Get codStatus
     *
     * @return \Entidades\ZgutlJobStatusTipo 
     */
    public function getCodStatus()
    {
        return $this->codStatus;
    }
}
