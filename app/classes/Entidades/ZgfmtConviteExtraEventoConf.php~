<?php

namespace Entidades;

use Doctrine\ORM\Mapping as ORM;

/**
 * ZgfmtConviteExtraEventoConf
 *
 * @ORM\Table(name="ZGFMT_CONVITE_EXTRA_EVENTO_CONF", uniqueConstraints={@ORM\UniqueConstraint(name="fk_ZGFMT_CONVITE_EXTRA_CONF_3_idx", columns={"COD_EVENTO", "COD_ORGANIZACAO"})}, indexes={@ORM\Index(name="fk_ZGFMT_CONVITE_EXTRA_EVENTO_CONF_1_idx", columns={"COD_ORGANIZACAO"}), @ORM\Index(name="IDX_12FF0F6CE9DA206A", columns={"COD_EVENTO"})})
 * @ORM\Entity
 */
class ZgfmtConviteExtraEventoConf
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
     * @var \Entidades\ZgfmtEvento
     *
     * @ORM\ManyToOne(targetEntity="Entidades\ZgfmtEvento")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="COD_EVENTO", referencedColumnName="CODIGO")
     * })
     */
    private $codEvento;


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
     * @return ZgfmtConviteExtraEventoConf
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
     * @return ZgfmtConviteExtraEventoConf
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
     * Set dataInicioPresencial
     *
     * @param \DateTime $dataInicioPresencial
     * @return ZgfmtConviteExtraEventoConf
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
     * @return ZgfmtConviteExtraEventoConf
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
     * @return ZgfmtConviteExtraEventoConf
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
     * @return ZgfmtConviteExtraEventoConf
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
     * @return ZgfmtConviteExtraEventoConf
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
     * @return ZgfmtConviteExtraEventoConf
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
     * Set codEvento
     *
     * @param \Entidades\ZgfmtEvento $codEvento
     * @return ZgfmtConviteExtraEventoConf
     */
    public function setCodEvento(\Entidades\ZgfmtEvento $codEvento = null)
    {
        $this->codEvento = $codEvento;

        return $this;
    }

    /**
     * Get codEvento
     *
     * @return \Entidades\ZgfmtEvento 
     */
    public function getCodEvento()
    {
        return $this->codEvento;
    }
}
