<?php

namespace Entidades;

use Doctrine\ORM\Mapping as ORM;

/**
 * ZgadmOrganizacaoAdm
 *
 * @ORM\Table(name="ZGADM_ORGANIZACAO_ADM", indexes={@ORM\Index(name="fk_ZGFMT_ORGANIZACAO_ADM_1_idx", columns={"COD_ORGANIZACAO"}), @ORM\Index(name="fk_ZGFMT_ORGANIZACAO_ADM_2_idx", columns={"COD_ORGANIZACAO_PAI"})})
 * @ORM\Entity
 */
class ZgadmOrganizacaoAdm
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
     * @ORM\Column(name="DATA_VALIDADE", type="datetime", nullable=true)
     */
    private $dataValidade;

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
     * @var \Entidades\ZgadmOrganizacao
     *
     * @ORM\ManyToOne(targetEntity="Entidades\ZgadmOrganizacao")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="COD_ORGANIZACAO_PAI", referencedColumnName="CODIGO")
     * })
     */
    private $codOrganizacaoPai;


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
     * Set dataValidade
     *
     * @param \DateTime $dataValidade
     * @return ZgadmOrganizacaoAdm
     */
    public function setDataValidade($dataValidade)
    {
        $this->dataValidade = $dataValidade;

        return $this;
    }

    /**
     * Get dataValidade
     *
     * @return \DateTime 
     */
    public function getDataValidade()
    {
        return $this->dataValidade;
    }

    /**
     * Set codOrganizacao
     *
     * @param \Entidades\ZgadmOrganizacao $codOrganizacao
     * @return ZgadmOrganizacaoAdm
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
     * Set codOrganizacaoPai
     *
     * @param \Entidades\ZgadmOrganizacao $codOrganizacaoPai
     * @return ZgadmOrganizacaoAdm
     */
    public function setCodOrganizacaoPai(\Entidades\ZgadmOrganizacao $codOrganizacaoPai = null)
    {
        $this->codOrganizacaoPai = $codOrganizacaoPai;

        return $this;
    }

    /**
     * Get codOrganizacaoPai
     *
     * @return \Entidades\ZgadmOrganizacao 
     */
    public function getCodOrganizacaoPai()
    {
        return $this->codOrganizacaoPai;
    }
}
