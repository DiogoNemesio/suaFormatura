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
     * @var \DateTime
     *
     * @ORM\Column(name="DATA_PROXIMA_EXECUCAO", type="datetime", nullable=true)
     */
    private $dataProximaExecucao;

    /**
     * @var string
     *
     * @ORM\Column(name="ANO", type="string", length=4, nullable=true)
     */
    private $ano;

    /**
     * @var string
     *
     * @ORM\Column(name="MES", type="string", length=2, nullable=true)
     */
    private $mes;

    /**
     * @var string
     *
     * @ORM\Column(name="DIA", type="string", length=2, nullable=true)
     */
    private $dia;

    /**
     * @var string
     *
     * @ORM\Column(name="HORA", type="string", length=2, nullable=true)
     */
    private $hora;

    /**
     * @var string
     *
     * @ORM\Column(name="MINUTO", type="string", length=2, nullable=true)
     */
    private $minuto;

    /**
     * @var string
     *
     * @ORM\Column(name="SEGUNDO", type="string", length=2, nullable=true)
     */
    private $segundo;

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
     * Set ano
     *
     * @param string $ano
     * @return ZgutlJob
     */
    public function setAno($ano)
    {
        $this->ano = $ano;

        return $this;
    }

    /**
     * Get ano
     *
     * @return string 
     */
    public function getAno()
    {
        return $this->ano;
    }

    /**
     * Set mes
     *
     * @param string $mes
     * @return ZgutlJob
     */
    public function setMes($mes)
    {
        $this->mes = $mes;

        return $this;
    }

    /**
     * Get mes
     *
     * @return string 
     */
    public function getMes()
    {
        return $this->mes;
    }

    /**
     * Set dia
     *
     * @param string $dia
     * @return ZgutlJob
     */
    public function setDia($dia)
    {
        $this->dia = $dia;

        return $this;
    }

    /**
     * Get dia
     *
     * @return string 
     */
    public function getDia()
    {
        return $this->dia;
    }

    /**
     * Set hora
     *
     * @param string $hora
     * @return ZgutlJob
     */
    public function setHora($hora)
    {
        $this->hora = $hora;

        return $this;
    }

    /**
     * Get hora
     *
     * @return string 
     */
    public function getHora()
    {
        return $this->hora;
    }

    /**
     * Set minuto
     *
     * @param string $minuto
     * @return ZgutlJob
     */
    public function setMinuto($minuto)
    {
        $this->minuto = $minuto;

        return $this;
    }

    /**
     * Get minuto
     *
     * @return string 
     */
    public function getMinuto()
    {
        return $this->minuto;
    }

    /**
     * Set segundo
     *
     * @param string $segundo
     * @return ZgutlJob
     */
    public function setSegundo($segundo)
    {
        $this->segundo = $segundo;

        return $this;
    }

    /**
     * Get segundo
     *
     * @return string 
     */
    public function getSegundo()
    {
        return $this->segundo;
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
