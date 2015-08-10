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
