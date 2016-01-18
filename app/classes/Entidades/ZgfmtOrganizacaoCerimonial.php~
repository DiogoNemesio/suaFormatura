<?php

namespace Entidades;

use Doctrine\ORM\Mapping as ORM;

/**
 * ZgfmtOrganizacaoCerimonial
 *
 * @ORM\Table(name="ZGFMT_ORGANIZACAO_CERIMONIAL", indexes={@ORM\Index(name="fk_ZGFMT_ORGANIZACAO_CERIMONIAL_1_idx", columns={"COD_ORGANIZACAO"}), @ORM\Index(name="fk_ZGFMT_ORGANIZACAO_CERIMONIAL_2_idx", columns={"COD_PLANO_FORMATURA"})})
 * @ORM\Entity
 */
class ZgfmtOrganizacaoCerimonial
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
     * @ORM\Column(name="VALOR_DESCONTO", type="float", precision=10, scale=0, nullable=true)
     */
    private $valorDesconto;

    /**
     * @var float
     *
     * @ORM\Column(name="PCT_DESCONTO", type="float", precision=10, scale=0, nullable=true)
     */
    private $pctDesconto;

    /**
     * @var integer
     *
     * @ORM\Column(name="IND_VENDEDOR_ACEITE", type="integer", nullable=true)
     */
    private $indVendedorAceite;

    /**
     * @var integer
     *
     * @ORM\Column(name="IND_VENDEDOR_DESMARCAR_PADRAO", type="integer", nullable=true)
     */
    private $indVendedorDesmarcarPadrao;

    /**
     * @var integer
     *
     * @ORM\Column(name="IND_VENDEDOR_DAR_CORTESIA", type="integer", nullable=true)
     */
    private $indVendedorDarCortesia;

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
     * @var \Entidades\ZgadmPlano
     *
     * @ORM\ManyToOne(targetEntity="Entidades\ZgadmPlano")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="COD_PLANO_FORMATURA", referencedColumnName="CODIGO")
     * })
     */
    private $codPlanoFormatura;


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
     * Set valorDesconto
     *
     * @param float $valorDesconto
     * @return ZgfmtOrganizacaoCerimonial
     */
    public function setValorDesconto($valorDesconto)
    {
        $this->valorDesconto = $valorDesconto;

        return $this;
    }

    /**
     * Get valorDesconto
     *
     * @return float 
     */
    public function getValorDesconto()
    {
        return $this->valorDesconto;
    }

    /**
     * Set pctDesconto
     *
     * @param float $pctDesconto
     * @return ZgfmtOrganizacaoCerimonial
     */
    public function setPctDesconto($pctDesconto)
    {
        $this->pctDesconto = $pctDesconto;

        return $this;
    }

    /**
     * Get pctDesconto
     *
     * @return float 
     */
    public function getPctDesconto()
    {
        return $this->pctDesconto;
    }

    /**
     * Set indVendedorAceite
     *
     * @param integer $indVendedorAceite
     * @return ZgfmtOrganizacaoCerimonial
     */
    public function setIndVendedorAceite($indVendedorAceite)
    {
        $this->indVendedorAceite = $indVendedorAceite;

        return $this;
    }

    /**
     * Get indVendedorAceite
     *
     * @return integer 
     */
    public function getIndVendedorAceite()
    {
        return $this->indVendedorAceite;
    }

    /**
     * Set indVendedorDesmarcarPadrao
     *
     * @param integer $indVendedorDesmarcarPadrao
     * @return ZgfmtOrganizacaoCerimonial
     */
    public function setIndVendedorDesmarcarPadrao($indVendedorDesmarcarPadrao)
    {
        $this->indVendedorDesmarcarPadrao = $indVendedorDesmarcarPadrao;

        return $this;
    }

    /**
     * Get indVendedorDesmarcarPadrao
     *
     * @return integer 
     */
    public function getIndVendedorDesmarcarPadrao()
    {
        return $this->indVendedorDesmarcarPadrao;
    }

    /**
     * Set indVendedorDarCortesia
     *
     * @param integer $indVendedorDarCortesia
     * @return ZgfmtOrganizacaoCerimonial
     */
    public function setIndVendedorDarCortesia($indVendedorDarCortesia)
    {
        $this->indVendedorDarCortesia = $indVendedorDarCortesia;

        return $this;
    }

    /**
     * Get indVendedorDarCortesia
     *
     * @return integer 
     */
    public function getIndVendedorDarCortesia()
    {
        return $this->indVendedorDarCortesia;
    }

    /**
     * Set codOrganizacao
     *
     * @param \Entidades\ZgadmOrganizacao $codOrganizacao
     * @return ZgfmtOrganizacaoCerimonial
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
     * Set codPlanoFormatura
     *
     * @param \Entidades\ZgadmPlano $codPlanoFormatura
     * @return ZgfmtOrganizacaoCerimonial
     */
    public function setCodPlanoFormatura(\Entidades\ZgadmPlano $codPlanoFormatura = null)
    {
        $this->codPlanoFormatura = $codPlanoFormatura;

        return $this;
    }

    /**
     * Get codPlanoFormatura
     *
     * @return \Entidades\ZgadmPlano 
     */
    public function getCodPlanoFormatura()
    {
        return $this->codPlanoFormatura;
    }
}
