<?php

namespace Entidades;

use Doctrine\ORM\Mapping as ORM;

/**
 * ZgfmtOrganizacaoFormatura
 *
 * @ORM\Table(name="ZGFMT_ORGANIZACAO_FORMATURA", uniqueConstraints={@ORM\UniqueConstraint(name="ZGFMT_ORGANIZACAO_FORMATURA_UK01", columns={"COD_ORGANIZACAO"})}, indexes={@ORM\Index(name="fk_ZGFMT_ORGANIZACAO_FORMATURA_2_idx", columns={"COD_INSTITUICAO"}), @ORM\Index(name="fk_ZGFMT_ORGANIZACAO_FORMATURA_3_idx", columns={"COD_CURSO"}), @ORM\Index(name="fk_ZGFMT_ORGANIZACAO_FORMATURA_4_idx", columns={"COD_CIDADE"})})
 * @ORM\Entity
 */
class ZgfmtOrganizacaoFormatura
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
     * @ORM\Column(name="DATA_CONCLUSAO", type="date", nullable=false)
     */
    private $dataConclusao;

    /**
     * @var float
     *
     * @ORM\Column(name="VALOR_POR_FORMANDO", type="float", precision=10, scale=0, nullable=true)
     */
    private $valorPorFormando;

    /**
     * @var float
     *
     * @ORM\Column(name="VALOR_POR_BOLETO", type="float", precision=10, scale=0, nullable=true)
     */
    private $valorPorBoleto;

    /**
     * @var integer
     *
     * @ORM\Column(name="DIA_VENCIMENTO", type="integer", nullable=true)
     */
    private $diaVencimento;

    /**
     * @var float
     *
     * @ORM\Column(name="VALOR_PREVISTO_TOTAL", type="float", precision=10, scale=0, nullable=true)
     */
    private $valorPrevistoTotal;

    /**
     * @var integer
     *
     * @ORM\Column(name="QTDE_PREVISTA_FORMANDOS", type="integer", nullable=true)
     */
    private $qtdePrevistaFormandos;

    /**
     * @var integer
     *
     * @ORM\Column(name="QTDE_PREVISTA_CONVIDADOS", type="integer", nullable=true)
     */
    private $qtdePrevistaConvidados;

    /**
     * @var float
     *
     * @ORM\Column(name="PCT_JUROS_TURMA", type="float", precision=10, scale=0, nullable=true)
     */
    private $pctJurosTurma;

    /**
     * @var float
     *
     * @ORM\Column(name="PCT_MORA_TURMA", type="float", precision=10, scale=0, nullable=true)
     */
    private $pctMoraTurma;

    /**
     * @var float
     *
     * @ORM\Column(name="PCT_CONVITE_EXTRA_TURMA", type="float", precision=10, scale=0, nullable=true)
     */
    private $pctConviteExtraTurma;

    /**
     * @var float
     *
     * @ORM\Column(name="PCT_DEVOLUCAO", type="float", precision=10, scale=0, nullable=true)
     */
    private $pctDevolucao;

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
     * @var \Entidades\ZgfmtInstituicao
     *
     * @ORM\ManyToOne(targetEntity="Entidades\ZgfmtInstituicao")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="COD_INSTITUICAO", referencedColumnName="CODIGO")
     * })
     */
    private $codInstituicao;

    /**
     * @var \Entidades\ZgfmtCurso
     *
     * @ORM\ManyToOne(targetEntity="Entidades\ZgfmtCurso")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="COD_CURSO", referencedColumnName="CODIGO")
     * })
     */
    private $codCurso;

    /**
     * @var \Entidades\ZgadmCidade
     *
     * @ORM\ManyToOne(targetEntity="Entidades\ZgadmCidade")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="COD_CIDADE", referencedColumnName="CODIGO")
     * })
     */
    private $codCidade;


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
     * Set dataConclusao
     *
     * @param \DateTime $dataConclusao
     * @return ZgfmtOrganizacaoFormatura
     */
    public function setDataConclusao($dataConclusao)
    {
        $this->dataConclusao = $dataConclusao;

        return $this;
    }

    /**
     * Get dataConclusao
     *
     * @return \DateTime 
     */
    public function getDataConclusao()
    {
        return $this->dataConclusao;
    }

    /**
     * Set valorPorFormando
     *
     * @param float $valorPorFormando
     * @return ZgfmtOrganizacaoFormatura
     */
    public function setValorPorFormando($valorPorFormando)
    {
        $this->valorPorFormando = $valorPorFormando;

        return $this;
    }

    /**
     * Get valorPorFormando
     *
     * @return float 
     */
    public function getValorPorFormando()
    {
        return $this->valorPorFormando;
    }

    /**
     * Set valorPorBoleto
     *
     * @param float $valorPorBoleto
     * @return ZgfmtOrganizacaoFormatura
     */
    public function setValorPorBoleto($valorPorBoleto)
    {
        $this->valorPorBoleto = $valorPorBoleto;

        return $this;
    }

    /**
     * Get valorPorBoleto
     *
     * @return float 
     */
    public function getValorPorBoleto()
    {
        return $this->valorPorBoleto;
    }

    /**
     * Set diaVencimento
     *
     * @param integer $diaVencimento
     * @return ZgfmtOrganizacaoFormatura
     */
    public function setDiaVencimento($diaVencimento)
    {
        $this->diaVencimento = $diaVencimento;

        return $this;
    }

    /**
     * Get diaVencimento
     *
     * @return integer 
     */
    public function getDiaVencimento()
    {
        return $this->diaVencimento;
    }

    /**
     * Set valorPrevistoTotal
     *
     * @param float $valorPrevistoTotal
     * @return ZgfmtOrganizacaoFormatura
     */
    public function setValorPrevistoTotal($valorPrevistoTotal)
    {
        $this->valorPrevistoTotal = $valorPrevistoTotal;

        return $this;
    }

    /**
     * Get valorPrevistoTotal
     *
     * @return float 
     */
    public function getValorPrevistoTotal()
    {
        return $this->valorPrevistoTotal;
    }

    /**
     * Set qtdePrevistaFormandos
     *
     * @param integer $qtdePrevistaFormandos
     * @return ZgfmtOrganizacaoFormatura
     */
    public function setQtdePrevistaFormandos($qtdePrevistaFormandos)
    {
        $this->qtdePrevistaFormandos = $qtdePrevistaFormandos;

        return $this;
    }

    /**
     * Get qtdePrevistaFormandos
     *
     * @return integer 
     */
    public function getQtdePrevistaFormandos()
    {
        return $this->qtdePrevistaFormandos;
    }

    /**
     * Set qtdePrevistaConvidados
     *
     * @param integer $qtdePrevistaConvidados
     * @return ZgfmtOrganizacaoFormatura
     */
    public function setQtdePrevistaConvidados($qtdePrevistaConvidados)
    {
        $this->qtdePrevistaConvidados = $qtdePrevistaConvidados;

        return $this;
    }

    /**
     * Get qtdePrevistaConvidados
     *
     * @return integer 
     */
    public function getQtdePrevistaConvidados()
    {
        return $this->qtdePrevistaConvidados;
    }

    /**
     * Set pctJurosTurma
     *
     * @param float $pctJurosTurma
     * @return ZgfmtOrganizacaoFormatura
     */
    public function setPctJurosTurma($pctJurosTurma)
    {
        $this->pctJurosTurma = $pctJurosTurma;

        return $this;
    }

    /**
     * Get pctJurosTurma
     *
     * @return float 
     */
    public function getPctJurosTurma()
    {
        return $this->pctJurosTurma;
    }

    /**
     * Set pctMoraTurma
     *
     * @param float $pctMoraTurma
     * @return ZgfmtOrganizacaoFormatura
     */
    public function setPctMoraTurma($pctMoraTurma)
    {
        $this->pctMoraTurma = $pctMoraTurma;

        return $this;
    }

    /**
     * Get pctMoraTurma
     *
     * @return float 
     */
    public function getPctMoraTurma()
    {
        return $this->pctMoraTurma;
    }

    /**
     * Set pctConviteExtraTurma
     *
     * @param float $pctConviteExtraTurma
     * @return ZgfmtOrganizacaoFormatura
     */
    public function setPctConviteExtraTurma($pctConviteExtraTurma)
    {
        $this->pctConviteExtraTurma = $pctConviteExtraTurma;

        return $this;
    }

    /**
     * Get pctConviteExtraTurma
     *
     * @return float 
     */
    public function getPctConviteExtraTurma()
    {
        return $this->pctConviteExtraTurma;
    }

    /**
     * Set pctDevolucao
     *
     * @param float $pctDevolucao
     * @return ZgfmtOrganizacaoFormatura
     */
    public function setPctDevolucao($pctDevolucao)
    {
        $this->pctDevolucao = $pctDevolucao;

        return $this;
    }

    /**
     * Get pctDevolucao
     *
     * @return float 
     */
    public function getPctDevolucao()
    {
        return $this->pctDevolucao;
    }

    /**
     * Set codOrganizacao
     *
     * @param \Entidades\ZgadmOrganizacao $codOrganizacao
     * @return ZgfmtOrganizacaoFormatura
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
     * Set codInstituicao
     *
     * @param \Entidades\ZgfmtInstituicao $codInstituicao
     * @return ZgfmtOrganizacaoFormatura
     */
    public function setCodInstituicao(\Entidades\ZgfmtInstituicao $codInstituicao = null)
    {
        $this->codInstituicao = $codInstituicao;

        return $this;
    }

    /**
     * Get codInstituicao
     *
     * @return \Entidades\ZgfmtInstituicao 
     */
    public function getCodInstituicao()
    {
        return $this->codInstituicao;
    }

    /**
     * Set codCurso
     *
     * @param \Entidades\ZgfmtCurso $codCurso
     * @return ZgfmtOrganizacaoFormatura
     */
    public function setCodCurso(\Entidades\ZgfmtCurso $codCurso = null)
    {
        $this->codCurso = $codCurso;

        return $this;
    }

    /**
     * Get codCurso
     *
     * @return \Entidades\ZgfmtCurso 
     */
    public function getCodCurso()
    {
        return $this->codCurso;
    }

    /**
     * Set codCidade
     *
     * @param \Entidades\ZgadmCidade $codCidade
     * @return ZgfmtOrganizacaoFormatura
     */
    public function setCodCidade(\Entidades\ZgadmCidade $codCidade = null)
    {
        $this->codCidade = $codCidade;

        return $this;
    }

    /**
     * Get codCidade
     *
     * @return \Entidades\ZgadmCidade 
     */
    public function getCodCidade()
    {
        return $this->codCidade;
    }
}
