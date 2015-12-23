<?php

namespace Entidades;

use Doctrine\ORM\Mapping as ORM;

/**
 * ZgadmOrganizacaoVendaComissao
 *
 * @ORM\Table(name="ZGADM_ORGANIZACAO_VENDA_COMISSAO", indexes={@ORM\Index(name="fk_ZGADM_ORGANIZACAO_VENDA_COMISSAO_1_idx", columns={"COD_VENDA_PLANO"})})
 * @ORM\Entity
 */
class ZgadmOrganizacaoVendaComissao
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
     * @ORM\Column(name="DATA_BASE", type="date", nullable=false)
     */
    private $dataBase;

    /**
     * @var float
     *
     * @ORM\Column(name="PCT_COMISSAO", type="float", precision=10, scale=0, nullable=true)
     */
    private $pctComissao;

    /**
     * @var float
     *
     * @ORM\Column(name="VALOR_COMISSAO", type="float", precision=10, scale=0, nullable=true)
     */
    private $valorComissao;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="DATA_CADASTRO", type="datetime", nullable=false)
     */
    private $dataCadastro;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="DATA_ULTIMA_ALTERACAO", type="datetime", nullable=true)
     */
    private $dataUltimaAlteracao;

    /**
     * @var \Entidades\ZgadmOrganizacaoVendaPlano
     *
     * @ORM\ManyToOne(targetEntity="Entidades\ZgadmOrganizacaoVendaPlano")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="COD_VENDA_PLANO", referencedColumnName="CODIGO")
     * })
     */
    private $codVendaPlano;


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
     * Set dataBase
     *
     * @param \DateTime $dataBase
     * @return ZgadmOrganizacaoVendaComissao
     */
    public function setDataBase($dataBase)
    {
        $this->dataBase = $dataBase;

        return $this;
    }

    /**
     * Get dataBase
     *
     * @return \DateTime 
     */
    public function getDataBase()
    {
        return $this->dataBase;
    }

    /**
     * Set pctComissao
     *
     * @param float $pctComissao
     * @return ZgadmOrganizacaoVendaComissao
     */
    public function setPctComissao($pctComissao)
    {
        $this->pctComissao = $pctComissao;

        return $this;
    }

    /**
     * Get pctComissao
     *
     * @return float 
     */
    public function getPctComissao()
    {
        return $this->pctComissao;
    }

    /**
     * Set valorComissao
     *
     * @param float $valorComissao
     * @return ZgadmOrganizacaoVendaComissao
     */
    public function setValorComissao($valorComissao)
    {
        $this->valorComissao = $valorComissao;

        return $this;
    }

    /**
     * Get valorComissao
     *
     * @return float 
     */
    public function getValorComissao()
    {
        return $this->valorComissao;
    }

    /**
     * Set dataCadastro
     *
     * @param \DateTime $dataCadastro
     * @return ZgadmOrganizacaoVendaComissao
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
     * Set dataUltimaAlteracao
     *
     * @param \DateTime $dataUltimaAlteracao
     * @return ZgadmOrganizacaoVendaComissao
     */
    public function setDataUltimaAlteracao($dataUltimaAlteracao)
    {
        $this->dataUltimaAlteracao = $dataUltimaAlteracao;

        return $this;
    }

    /**
     * Get dataUltimaAlteracao
     *
     * @return \DateTime 
     */
    public function getDataUltimaAlteracao()
    {
        return $this->dataUltimaAlteracao;
    }

    /**
     * Set codVendaPlano
     *
     * @param \Entidades\ZgadmOrganizacaoVendaPlano $codVendaPlano
     * @return ZgadmOrganizacaoVendaComissao
     */
    public function setCodVendaPlano(\Entidades\ZgadmOrganizacaoVendaPlano $codVendaPlano = null)
    {
        $this->codVendaPlano = $codVendaPlano;

        return $this;
    }

    /**
     * Get codVendaPlano
     *
     * @return \Entidades\ZgadmOrganizacaoVendaPlano 
     */
    public function getCodVendaPlano()
    {
        return $this->codVendaPlano;
    }
}
