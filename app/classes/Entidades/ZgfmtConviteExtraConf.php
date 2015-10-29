<?php

namespace Entidades;

use Doctrine\ORM\Mapping as ORM;

/**
 * ZgfmtConviteExtraConf
 *
 * @ORM\Table(name="ZGFMT_CONVITE_EXTRA_CONF", uniqueConstraints={@ORM\UniqueConstraint(name="fk_ZGFMT_CONVITE_EXTRA_CONF_3_idx", columns={"COD_TIPO_EVENTO", "COD_ORGANIZACAO"})}, indexes={@ORM\Index(name="fk_ZGFMT_CONVITE_EXTRA_CONF_1_idx", columns={"COD_ORGANIZACAO"}), @ORM\Index(name="fk_ZGFMT_CONVITE_EXTRA_CONF_2_idx", columns={"CONTA_RECEBIMENTO_INTERNET"}), @ORM\Index(name="fk_ZGFMT_CONVITE_EXTRA_CONF_4_idx", columns={"COD_TIPO_EVENTO"})})
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
     * @ORM\Column(name="DATA_FIM_PRESENCIAL", type="date", nullable=true)
     */
    private $dataFimPresencial;

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
     * @var \DateTime
     *
     * @ORM\Column(name="DATA_CADASTRO", type="datetime", nullable=false)
     */
    private $dataCadastro;

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
     * @var \Entidades\ZgfinConta
     *
     * @ORM\ManyToOne(targetEntity="Entidades\ZgfinConta")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="CONTA_RECEBIMENTO_INTERNET", referencedColumnName="CODIGO")
     * })
     */
    private $contaRecebimentoInternet;

    /**
     * @var \Entidades\ZgfmtEventoTipo
     *
     * @ORM\ManyToOne(targetEntity="Entidades\ZgfmtEventoTipo")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="COD_TIPO_EVENTO", referencedColumnName="CODIGO")
     * })
     */
    private $codTipoEvento;


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
     * Set dataFimPresencial
     *
     * @param \DateTime $dataFimPresencial
     * @return ZgfmtConviteExtraConf
     */
    public function setDataFimPresencial($dataFimPresencial)
    {
        $this->dataFimPresencial = $dataFimPresencial;

        return $this;
    }

    /**
     * Get dataFimPresencial
     *
     * @return \DateTime 
     */
    public function getDataFimPresencial()
    {
        return $this->dataFimPresencial;
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
     * Set dataCadastro
     *
     * @param \DateTime $dataCadastro
     * @return ZgfmtConviteExtraConf
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
     * Set codTipoEvento
     *
     * @param \Entidades\ZgfmtEventoTipo $codTipoEvento
     * @return ZgfmtConviteExtraConf
     */
    public function setCodTipoEvento(\Entidades\ZgfmtEventoTipo $codTipoEvento = null)
    {
        $this->codTipoEvento = $codTipoEvento;

        return $this;
    }

    /**
     * Get codTipoEvento
     *
     * @return \Entidades\ZgfmtEventoTipo 
     */
    public function getCodTipoEvento()
    {
        return $this->codTipoEvento;
    }
}
