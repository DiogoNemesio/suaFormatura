<?php

namespace Entidades;

use Doctrine\ORM\Mapping as ORM;

/**
 * ZgfmtOrcamentoHistoricoAceite
 *
 * @ORM\Table(name="ZGFMT_ORCAMENTO_HISTORICO_ACEITE", indexes={@ORM\Index(name="fk_ZGFMT_ORCAMENTO_HISTORICO_ACEITE_1_idx", columns={"COD_ORCAMENTO"}), @ORM\Index(name="fk_ZGFMT_ORCAMENTO_HISTORICO_ACEITE_2_idx", columns={"COD_USUARIO"})})
 * @ORM\Entity
 */
class ZgfmtOrcamentoHistoricoAceite
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
     * @var float
     *
     * @ORM\Column(name="VALOR_TOTAL", type="float", precision=10, scale=0, nullable=false)
     */
    private $valorTotal;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="DATA_CADASTRO", type="datetime", nullable=false)
     */
    private $dataCadastro;

    /**
     * @var \Entidades\ZgfmtOrcamento
     *
     * @ORM\ManyToOne(targetEntity="Entidades\ZgfmtOrcamento")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="COD_ORCAMENTO", referencedColumnName="CODIGO")
     * })
     */
    private $codOrcamento;

    /**
     * @var \Entidades\ZgsegUsuario
     *
     * @ORM\ManyToOne(targetEntity="Entidades\ZgsegUsuario")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="COD_USUARIO", referencedColumnName="CODIGO")
     * })
     */
    private $codUsuario;


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
     * Set valorTotal
     *
     * @param float $valorTotal
     * @return ZgfmtOrcamentoHistoricoAceite
     */
    public function setValorTotal($valorTotal)
    {
        $this->valorTotal = $valorTotal;

        return $this;
    }

    /**
     * Get valorTotal
     *
     * @return float 
     */
    public function getValorTotal()
    {
        return $this->valorTotal;
    }

    /**
     * Set dataCadastro
     *
     * @param \DateTime $dataCadastro
     * @return ZgfmtOrcamentoHistoricoAceite
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
     * Set codOrcamento
     *
     * @param \Entidades\ZgfmtOrcamento $codOrcamento
     * @return ZgfmtOrcamentoHistoricoAceite
     */
    public function setCodOrcamento(\Entidades\ZgfmtOrcamento $codOrcamento = null)
    {
        $this->codOrcamento = $codOrcamento;

        return $this;
    }

    /**
     * Get codOrcamento
     *
     * @return \Entidades\ZgfmtOrcamento 
     */
    public function getCodOrcamento()
    {
        return $this->codOrcamento;
    }

    /**
     * Set codUsuario
     *
     * @param \Entidades\ZgsegUsuario $codUsuario
     * @return ZgfmtOrcamentoHistoricoAceite
     */
    public function setCodUsuario(\Entidades\ZgsegUsuario $codUsuario = null)
    {
        $this->codUsuario = $codUsuario;

        return $this;
    }

    /**
     * Get codUsuario
     *
     * @return \Entidades\ZgsegUsuario 
     */
    public function getCodUsuario()
    {
        return $this->codUsuario;
    }
}