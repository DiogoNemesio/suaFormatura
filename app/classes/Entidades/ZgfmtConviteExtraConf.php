<?php

namespace Entidades;

use Doctrine\ORM\Mapping as ORM;

/**
 * ZgfmtConviteExtraConf
 *
 * @ORM\Table(name="ZGFMT_CONVITE_EXTRA_CONF", indexes={@ORM\Index(name="fk_ZGFMT_CONVITE_EXTRA_CONF_1_idx", columns={"COD_ORGANIZACAO"}), @ORM\Index(name="fk_ZGFMT_CONVITE_EXTRA_CONF_2_idx", columns={"CONTA_RECEBIMENTO_INTERNET"})})
 * @ORM\Entity
 */
class ZgfmtConviteExtraConf
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
     * @ORM\Column(name="DATA_INICIO_INTERNET", type="date", nullable=true)
     */
    private $dataInicioInternet;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="DATA_FIM_INTERNET", type="date", nullable=true)
     */
    private $dataFimInternet;

    /**
     * @var float
     *
     * @ORM\Column(name="TAXA_CONVENIENCIA", type="float", precision=10, scale=0, nullable=true)
     */
    private $taxaConveniencia;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="DATA_INICIO_PRESENCIAL", type="date", nullable=true)
     */
    private $dataInicioPresencial;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="DATE_FIM_PRESENCIAL", type="date", nullable=true)
     */
    private $dateFimPresencial;

    /**
     * @var integer
     *
     * @ORM\Column(name="QTDE_MAX_ALUNO", type="integer", nullable=true)
     */
    private $qtdeMaxAluno;

    /**
     * @var string
     *
     * @ORM\Column(name="VALOR", type="string", length=45, nullable=true)
     */
    private $valor;

    /**
     * @var \Entidades\ZgfinConta
     *
     * @ORM\ManyToOne(targetEntity="Entidades\ZgfinConta")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="CONTA_RECEBIMENTO_INTERNET", referencedColumnName="CODIGO")
     * })
     */
    private $contaRecebimentoInternet;

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
     * Get codigo
     *
     * @return integer 
     */
    public function getCodigo()
    {
        return $this->codigo;
    }

    /**
     * Set dataInicioInternet
     *
     * @param \DateTime $dataInicioInternet
     * @return ZgfmtConviteExtraConf
     */
    public function setDataInicioInternet($dataInicioInternet)
    {
        $this->dataInicioInternet = $dataInicioInternet;

        return $this;
    }

    /**
     * Get dataInicioInternet
     *
     * @return \DateTime 
     */
    public function getDataInicioInternet()
    {
        return $this->dataInicioInternet;
    }

    /**
     * Set dataFimInternet
     *
     * @param \DateTime $dataFimInternet
     * @return ZgfmtConviteExtraConf
     */
    public function setDataFimInternet($dataFimInternet)
    {
        $this->dataFimInternet = $dataFimInternet;

        return $this;
    }

    /**
     * Get dataFimInternet
     *
     * @return \DateTime 
     */
    public function getDataFimInternet()
    {
        return $this->dataFimInternet;
    }

    /**
     * Set taxaConveniencia
     *
     * @param float $taxaConveniencia
     * @return ZgfmtConviteExtraConf
     */
    public function setTaxaConveniencia($taxaConveniencia)
    {
        $this->taxaConveniencia = $taxaConveniencia;

        return $this;
    }

    /**
     * Get taxaConveniencia
     *
     * @return float 
     */
    public function getTaxaConveniencia()
    {
        return $this->taxaConveniencia;
    }

    /**
     * Set dataInicioPresencial
     *
     * @param \DateTime $dataInicioPresencial
     * @return ZgfmtConviteExtraConf
     */
    public function setDataInicioPresencial($dataInicioPresencial)
    {
        $this->dataInicioPresencial = $dataInicioPresencial;

        return $this;
    }

    /**
     * Get dataInicioPresencial
     *
     * @return \DateTime 
     */
    public function getDataInicioPresencial()
    {
        return $this->dataInicioPresencial;
    }

    /**
     * Set dateFimPresencial
     *
     * @param \DateTime $dateFimPresencial
     * @return ZgfmtConviteExtraConf
     */
    public function setDateFimPresencial($dateFimPresencial)
    {
        $this->dateFimPresencial = $dateFimPresencial;

        return $this;
    }

    /**
     * Get dateFimPresencial
     *
     * @return \DateTime 
     */
    public function getDateFimPresencial()
    {
        return $this->dateFimPresencial;
    }

    /**
     * Set qtdeMaxAluno
     *
     * @param integer $qtdeMaxAluno
     * @return ZgfmtConviteExtraConf
     */
    public function setQtdeMaxAluno($qtdeMaxAluno)
    {
        $this->qtdeMaxAluno = $qtdeMaxAluno;

        return $this;
    }

    /**
     * Get qtdeMaxAluno
     *
     * @return integer 
     */
    public function getQtdeMaxAluno()
    {
        return $this->qtdeMaxAluno;
    }

    /**
     * Set valor
     *
     * @param string $valor
     * @return ZgfmtConviteExtraConf
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
     * Set contaRecebimentoInternet
     *
     * @param \Entidades\ZgfinConta $contaRecebimentoInternet
     * @return ZgfmtConviteExtraConf
     */
    public function setContaRecebimentoInternet(\Entidades\ZgfinConta $contaRecebimentoInternet = null)
    {
        $this->contaRecebimentoInternet = $contaRecebimentoInternet;

        return $this;
    }

    /**
     * Get contaRecebimentoInternet
     *
     * @return \Entidades\ZgfinConta 
     */
    public function getContaRecebimentoInternet()
    {
        return $this->contaRecebimentoInternet;
    }

    /**
     * Set codOrganizacao
     *
     * @param \Entidades\ZgadmOrganizacao $codOrganizacao
     * @return ZgfmtConviteExtraConf
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
}
