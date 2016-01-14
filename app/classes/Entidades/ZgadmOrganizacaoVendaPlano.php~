<?php

namespace Entidades;

use Doctrine\ORM\Mapping as ORM;

/**
 * ZgadmOrganizacaoVendaPlano
 *
 * @ORM\Table(name="ZGADM_ORGANIZACAO_VENDA_PLANO", indexes={@ORM\Index(name="fk_ZGADM_ORGANIZACAO_VENDA_PLANO_1_idx", columns={"COD_ORGANIZACAO"}), @ORM\Index(name="fk_ZGADM_ORGANIZACAO_VENDA_PLANO_2_idx", columns={"COD_PLANO"})})
 * @ORM\Entity
 */
class ZgadmOrganizacaoVendaPlano
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
     * @var integer
     *
     * @ORM\Column(name="IND_HABILITADO", type="integer", nullable=false)
     */
    private $indHabilitado;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="DATA_CADASTRO", type="datetime", nullable=false)
     */
    private $dataCadastro;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="DATA_ULTIMA_ALTERACAO", type="datetime", nullable=false)
     */
    private $dataUltimaAlteracao;

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
     *   @ORM\JoinColumn(name="COD_PLANO", referencedColumnName="CODIGO")
     * })
     */
    private $codPlano;


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
     * Set indHabilitado
     *
     * @param integer $indHabilitado
     * @return ZgadmOrganizacaoVendaPlano
     */
    public function setIndHabilitado($indHabilitado)
    {
        $this->indHabilitado = $indHabilitado;

        return $this;
    }

    /**
     * Get indHabilitado
     *
     * @return integer 
     */
    public function getIndHabilitado()
    {
        return $this->indHabilitado;
    }

    /**
     * Set dataCadastro
     *
     * @param \DateTime $dataCadastro
     * @return ZgadmOrganizacaoVendaPlano
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
     * @return ZgadmOrganizacaoVendaPlano
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
     * Set codOrganizacao
     *
     * @param \Entidades\ZgadmOrganizacao $codOrganizacao
     * @return ZgadmOrganizacaoVendaPlano
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
     * Set codPlano
     *
     * @param \Entidades\ZgadmPlano $codPlano
     * @return ZgadmOrganizacaoVendaPlano
     */
    public function setCodPlano(\Entidades\ZgadmPlano $codPlano = null)
    {
        $this->codPlano = $codPlano;

        return $this;
    }

    /**
     * Get codPlano
     *
     * @return \Entidades\ZgadmPlano 
     */
    public function getCodPlano()
    {
        return $this->codPlano;
    }
}
