<?php

namespace Entidades;

use Doctrine\ORM\Mapping as ORM;

/**
 * ZgfmtConviteExtraVendaConf
 *
 * @ORM\Table(name="ZGFMT_CONVITE_EXTRA_VENDA_CONF", uniqueConstraints={@ORM\UniqueConstraint(name="ZGFMT_CONVITE_EXTRA_VENDA_CONF_UK01", columns={"COD_FORMATURA", "COD_VENDA_TIPO"})}, indexes={@ORM\Index(name="fk_ZGFMT_CONVITE_EXTRA_VENDA_CONF_1_idx", columns={"COD_FORMATURA"}), @ORM\Index(name="fk_ZGFMT_CONVITE_EXTRA_VENDA_CONF_2_idx", columns={"COD_VENDA_TIPO"}), @ORM\Index(name="fk_ZGFMT_CONVITE_EXTRA_VENDA_CONF_3_idx", columns={"COD_CONTA_BOLETO"})})
 * @ORM\Entity
 */
class ZgfmtConviteExtraVendaConf
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
     * @ORM\Column(name="TAXA_ADMINISTRACAO", type="float", precision=10, scale=0, nullable=true)
     */
    private $taxaAdministracao;

    /**
     * @var integer
     *
     * @ORM\Column(name="IND_ADICIONAR_TAXA_BOLETO", type="integer", nullable=true)
     */
    private $indAdicionarTaxaBoleto;

    /**
     * @var \Entidades\ZgadmOrganizacao
     *
     * @ORM\ManyToOne(targetEntity="Entidades\ZgadmOrganizacao")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="COD_FORMATURA", referencedColumnName="CODIGO")
     * })
     */
    private $codFormatura;

    /**
     * @var \Entidades\ZgfmtConviteExtraVendaTipo
     *
     * @ORM\ManyToOne(targetEntity="Entidades\ZgfmtConviteExtraVendaTipo")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="COD_VENDA_TIPO", referencedColumnName="CODIGO")
     * })
     */
    private $codVendaTipo;

    /**
     * @var \Entidades\ZgfinConta
     *
     * @ORM\ManyToOne(targetEntity="Entidades\ZgfinConta")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="COD_CONTA_BOLETO", referencedColumnName="CODIGO")
     * })
     */
    private $codContaBoleto;


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
     * Set taxaAdministracao
     *
     * @param float $taxaAdministracao
     * @return ZgfmtConviteExtraVendaConf
     */
    public function setTaxaAdministracao($taxaAdministracao)
    {
        $this->taxaAdministracao = $taxaAdministracao;

        return $this;
    }

    /**
     * Get taxaAdministracao
     *
     * @return float 
     */
    public function getTaxaAdministracao()
    {
        return $this->taxaAdministracao;
    }

    /**
     * Set indAdicionarTaxaBoleto
     *
     * @param integer $indAdicionarTaxaBoleto
     * @return ZgfmtConviteExtraVendaConf
     */
    public function setIndAdicionarTaxaBoleto($indAdicionarTaxaBoleto)
    {
        $this->indAdicionarTaxaBoleto = $indAdicionarTaxaBoleto;

        return $this;
    }

    /**
     * Get indAdicionarTaxaBoleto
     *
     * @return integer 
     */
    public function getIndAdicionarTaxaBoleto()
    {
        return $this->indAdicionarTaxaBoleto;
    }

    /**
     * Set codFormatura
     *
     * @param \Entidades\ZgadmOrganizacao $codFormatura
     * @return ZgfmtConviteExtraVendaConf
     */
    public function setCodFormatura(\Entidades\ZgadmOrganizacao $codFormatura = null)
    {
        $this->codFormatura = $codFormatura;

        return $this;
    }

    /**
     * Get codFormatura
     *
     * @return \Entidades\ZgadmOrganizacao 
     */
    public function getCodFormatura()
    {
        return $this->codFormatura;
    }

    /**
     * Set codVendaTipo
     *
     * @param \Entidades\ZgfmtConviteExtraVendaTipo $codVendaTipo
     * @return ZgfmtConviteExtraVendaConf
     */
    public function setCodVendaTipo(\Entidades\ZgfmtConviteExtraVendaTipo $codVendaTipo = null)
    {
        $this->codVendaTipo = $codVendaTipo;

        return $this;
    }

    /**
     * Get codVendaTipo
     *
     * @return \Entidades\ZgfmtConviteExtraVendaTipo 
     */
    public function getCodVendaTipo()
    {
        return $this->codVendaTipo;
    }

    /**
     * Set codContaBoleto
     *
     * @param \Entidades\ZgfinConta $codContaBoleto
     * @return ZgfmtConviteExtraVendaConf
     */
    public function setCodContaBoleto(\Entidades\ZgfinConta $codContaBoleto = null)
    {
        $this->codContaBoleto = $codContaBoleto;

        return $this;
    }

    /**
     * Get codContaBoleto
     *
     * @return \Entidades\ZgfinConta 
     */
    public function getCodContaBoleto()
    {
        return $this->codContaBoleto;
    }
}
