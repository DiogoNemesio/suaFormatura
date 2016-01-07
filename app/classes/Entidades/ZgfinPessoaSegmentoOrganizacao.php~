<?php

namespace Entidades;

use Doctrine\ORM\Mapping as ORM;

/**
 * ZgfinPessoaSegmentoOrganizacao
 *
 * @ORM\Table(name="ZGFIN_PESSOA_SEGMENTO_ORGANIZACAO", indexes={@ORM\Index(name="fk_ZGFIN_PESSOA_SEGMENTO_ORGANIZACAO_1_idx", columns={"COD_PESSOA"}), @ORM\Index(name="fk_ZGFIN_PESSOA_SEGMENTO_ORGANIZACAO_2_idx", columns={"COD_ORGANIZACAO"}), @ORM\Index(name="fk_ZGFIN_PESSOA_SEGMENTO_ORGANIZACAO_3_idx", columns={"COD_SEGMENTO"})})
 * @ORM\Entity
 */
class ZgfinPessoaSegmentoOrganizacao
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
     * @var \Entidades\ZgfinPessoa
     *
     * @ORM\ManyToOne(targetEntity="Entidades\ZgfinPessoa")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="COD_PESSOA", referencedColumnName="CODIGO")
     * })
     */
    private $codPessoa;

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
     * @var \Entidades\ZgfinSegmentoMercado
     *
     * @ORM\ManyToOne(targetEntity="Entidades\ZgfinSegmentoMercado")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="COD_SEGMENTO", referencedColumnName="CODIGO")
     * })
     */
    private $codSegmento;


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
     * Set codPessoa
     *
     * @param \Entidades\ZgfinPessoa $codPessoa
     * @return ZgfinPessoaSegmentoOrganizacao
     */
    public function setCodPessoa(\Entidades\ZgfinPessoa $codPessoa = null)
    {
        $this->codPessoa = $codPessoa;

        return $this;
    }

    /**
     * Get codPessoa
     *
     * @return \Entidades\ZgfinPessoa 
     */
    public function getCodPessoa()
    {
        return $this->codPessoa;
    }

    /**
     * Set codOrganizacao
     *
     * @param \Entidades\ZgadmOrganizacao $codOrganizacao
     * @return ZgfinPessoaSegmentoOrganizacao
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
     * Set codSegmento
     *
     * @param \Entidades\ZgfinSegmentoMercado $codSegmento
     * @return ZgfinPessoaSegmentoOrganizacao
     */
    public function setCodSegmento(\Entidades\ZgfinSegmentoMercado $codSegmento = null)
    {
        $this->codSegmento = $codSegmento;

        return $this;
    }

    /**
     * Get codSegmento
     *
     * @return \Entidades\ZgfinSegmentoMercado 
     */
    public function getCodSegmento()
    {
        return $this->codSegmento;
    }
}
