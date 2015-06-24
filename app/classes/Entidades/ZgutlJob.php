<?php

namespace Entidades;

use Doctrine\ORM\Mapping as ORM;

/**
 * ZgutlJob
 *
 * @ORM\Table(name="ZGUTL_JOB", indexes={@ORM\Index(name="fk_ZGUTL_JOB_1_idx", columns={"COD_ATIVIDADE"}), @ORM\Index(name="fk_ZGUTL_JOB_2_idx", columns={"COD_MODULO"})})
 * @ORM\Entity
 */
class ZgutlJob
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
     * @ORM\Column(name="COMANDO", type="string", length=400, nullable=false)
     */
    private $comando;

    /**
     * @var integer
     *
     * @ORM\Column(name="IND_ATIVO", type="integer", nullable=false)
     */
    private $indAtivo;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="DATA_ULTIMA_EXECUCAO", type="datetime", nullable=true)
     */
    private $dataUltimaExecucao;

    /**
     * @var string
     *
     * @ORM\Column(name="INTERVALO", type="string", length=200, nullable=false)
     */
    private $intervalo;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="DATA_PROXIMA_EXECUCAO", type="datetime", nullable=true)
     */
    private $dataProximaExecucao;

    /**
     * @var \Entidades\ZgutlAtividade
     *
     * @ORM\ManyToOne(targetEntity="Entidades\ZgutlAtividade")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="COD_ATIVIDADE", referencedColumnName="CODIGO")
     * })
     */
    private $codAtividade;

    /**
     * @var \Entidades\ZgappModulo
     *
     * @ORM\ManyToOne(targetEntity="Entidades\ZgappModulo")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="COD_MODULO", referencedColumnName="CODIGO")
     * })
     */
    private $codModulo;


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
     * Set comando
     *
     * @param string $comando
     * @return ZgutlJob
     */
    public function setComando($comando)
    {
        $this->comando = $comando;

        return $this;
    }

    /**
     * Get comando
     *
     * @return string 
     */
    public function getComando()
    {
        return $this->comando;
    }

    /**
     * Set indAtivo
     *
     * @param integer $indAtivo
     * @return ZgutlJob
     */
    public function setIndAtivo($indAtivo)
    {
        $this->indAtivo = $indAtivo;

        return $this;
    }

    /**
     * Get indAtivo
     *
     * @return integer 
     */
    public function getIndAtivo()
    {
        return $this->indAtivo;
    }

    /**
     * Set dataUltimaExecucao
     *
     * @param \DateTime $dataUltimaExecucao
     * @return ZgutlJob
     */
    public function setDataUltimaExecucao($dataUltimaExecucao)
    {
        $this->dataUltimaExecucao = $dataUltimaExecucao;

        return $this;
    }

    /**
     * Get dataUltimaExecucao
     *
     * @return \DateTime 
     */
    public function getDataUltimaExecucao()
    {
        return $this->dataUltimaExecucao;
    }

    /**
     * Set intervalo
     *
     * @param string $intervalo
     * @return ZgutlJob
     */
    public function setIntervalo($intervalo)
    {
        $this->intervalo = $intervalo;

        return $this;
    }

    /**
     * Get intervalo
     *
     * @return string 
     */
    public function getIntervalo()
    {
        return $this->intervalo;
    }

    /**
     * Set dataProximaExecucao
     *
     * @param \DateTime $dataProximaExecucao
     * @return ZgutlJob
     */
    public function setDataProximaExecucao($dataProximaExecucao)
    {
        $this->dataProximaExecucao = $dataProximaExecucao;

        return $this;
    }

    /**
     * Get dataProximaExecucao
     *
     * @return \DateTime 
     */
    public function getDataProximaExecucao()
    {
        return $this->dataProximaExecucao;
    }

    /**
     * Set codAtividade
     *
     * @param \Entidades\ZgutlAtividade $codAtividade
     * @return ZgutlJob
     */
    public function setCodAtividade(\Entidades\ZgutlAtividade $codAtividade = null)
    {
        $this->codAtividade = $codAtividade;

        return $this;
    }

    /**
     * Get codAtividade
     *
     * @return \Entidades\ZgutlAtividade 
     */
    public function getCodAtividade()
    {
        return $this->codAtividade;
    }

    /**
     * Set codModulo
     *
     * @param \Entidades\ZgappModulo $codModulo
     * @return ZgutlJob
     */
    public function setCodModulo(\Entidades\ZgappModulo $codModulo = null)
    {
        $this->codModulo = $codModulo;

        return $this;
    }

    /**
     * Get codModulo
     *
     * @return \Entidades\ZgappModulo 
     */
    public function getCodModulo()
    {
        return $this->codModulo;
    }
}
