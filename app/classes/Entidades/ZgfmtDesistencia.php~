<?php

namespace Entidades;

use Doctrine\ORM\Mapping as ORM;

/**
 * ZgfmtDesistencia
 *
 * @ORM\Table(name="ZGFMT_DESISTENCIA", indexes={@ORM\Index(name="fk_ZGFMT_DESISTENCIA_1_idx", columns={"COD_ORGANIZACAO"}), @ORM\Index(name="fk_ZGFMT_DESISTENCIA_2_idx", columns={"COD_TIPO_DESISTENCIA"}), @ORM\Index(name="fk_ZGFMT_DESISTENCIA_3_idx", columns={"COD_FORMANDO"}), @ORM\Index(name="fk_ZGFMT_DESISTENCIA_4_idx", columns={"COD_TIPO_BASE_CALCULO"})})
 * @ORM\Entity
 */
class ZgfmtDesistencia
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
     * @ORM\Column(name="DATA_DESISTENCIA", type="date", nullable=false)
     */
    private $dataDesistencia;

    /**
     * @var float
     *
     * @ORM\Column(name="PCT_DEVOLUCAO", type="float", precision=10, scale=0, nullable=false)
     */
    private $pctDevolucao;

    /**
     * @var float
     *
     * @ORM\Column(name="VALOR_MULTA", type="float", precision=10, scale=0, nullable=false)
     */
    private $valorMulta;

    /**
     * @var integer
     *
     * @ORM\Column(name="COD_TRANSACAO", type="integer", nullable=true)
     */
    private $codTransacao;

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
     * @var \Entidades\ZgfmtDesistenciaTipo
     *
     * @ORM\ManyToOne(targetEntity="Entidades\ZgfmtDesistenciaTipo")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="COD_TIPO_DESISTENCIA", referencedColumnName="CODIGO")
     * })
     */
    private $codTipoDesistencia;

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
     * @var \Entidades\ZgfmtBaseCalculoTipo
     *
     * @ORM\ManyToOne(targetEntity="Entidades\ZgfmtBaseCalculoTipo")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="COD_TIPO_BASE_CALCULO", referencedColumnName="CODIGO")
     * })
     */
    private $codTipoBaseCalculo;


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
     * Set dataDesistencia
     *
     * @param \DateTime $dataDesistencia
     * @return ZgfmtDesistencia
     */
    public function setDataDesistencia($dataDesistencia)
    {
        $this->dataDesistencia = $dataDesistencia;

        return $this;
    }

    /**
     * Get dataDesistencia
     *
     * @return \DateTime 
     */
    public function getDataDesistencia()
    {
        return $this->dataDesistencia;
    }

    /**
     * Set pctDevolucao
     *
     * @param float $pctDevolucao
     * @return ZgfmtDesistencia
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
     * Set valorMulta
     *
     * @param float $valorMulta
     * @return ZgfmtDesistencia
     */
    public function setValorMulta($valorMulta)
    {
        $this->valorMulta = $valorMulta;

        return $this;
    }

    /**
     * Get valorMulta
     *
     * @return float 
     */
    public function getValorMulta()
    {
        return $this->valorMulta;
    }

    /**
     * Set codTransacao
     *
     * @param integer $codTransacao
     * @return ZgfmtDesistencia
     */
    public function setCodTransacao($codTransacao)
    {
        $this->codTransacao = $codTransacao;

        return $this;
    }

    /**
     * Get codTransacao
     *
     * @return integer 
     */
    public function getCodTransacao()
    {
        return $this->codTransacao;
    }

    /**
     * Set codOrganizacao
     *
     * @param \Entidades\ZgadmOrganizacao $codOrganizacao
     * @return ZgfmtDesistencia
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
     * Set codTipoDesistencia
     *
     * @param \Entidades\ZgfmtDesistenciaTipo $codTipoDesistencia
     * @return ZgfmtDesistencia
     */
    public function setCodTipoDesistencia(\Entidades\ZgfmtDesistenciaTipo $codTipoDesistencia = null)
    {
        $this->codTipoDesistencia = $codTipoDesistencia;

        return $this;
    }

    /**
     * Get codTipoDesistencia
     *
     * @return \Entidades\ZgfmtDesistenciaTipo 
     */
    public function getCodTipoDesistencia()
    {
        return $this->codTipoDesistencia;
    }

    /**
     * Set codFormando
     *
     * @param \Entidades\ZgsegUsuario $codFormando
     * @return ZgfmtDesistencia
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
     * Set codTipoBaseCalculo
     *
     * @param \Entidades\ZgfmtBaseCalculoTipo $codTipoBaseCalculo
     * @return ZgfmtDesistencia
     */
    public function setCodTipoBaseCalculo(\Entidades\ZgfmtBaseCalculoTipo $codTipoBaseCalculo = null)
    {
        $this->codTipoBaseCalculo = $codTipoBaseCalculo;

        return $this;
    }

    /**
     * Get codTipoBaseCalculo
     *
     * @return \Entidades\ZgfmtBaseCalculoTipo 
     */
    public function getCodTipoBaseCalculo()
    {
        return $this->codTipoBaseCalculo;
    }
}
