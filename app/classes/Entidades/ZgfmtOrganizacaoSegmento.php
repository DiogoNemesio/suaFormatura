<?php

namespace Entidades;

use Doctrine\ORM\Mapping as ORM;

/**
 * ZgfmtOrganizacaoSegmento
 *
 * @ORM\Table(name="ZGFMT_ORGANIZACAO_SEGMENTO", indexes={@ORM\Index(name="fk_ZGFOR_FORNEC_SERVICO_2_idx", columns={"COD_SEGMENTO"}), @ORM\Index(name="fk_ZGFOR_ORGANIZACAO_SERVICO_1_idx", columns={"COD_ORGANIZACAO"})})
 * @ORM\Entity
 */
class ZgfmtOrganizacaoSegmento
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
     * @var \Entidades\ZgfmtSegmentoMercado
     *
     * @ORM\ManyToOne(targetEntity="Entidades\ZgfmtSegmentoMercado")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="COD_SEGMENTO", referencedColumnName="CODIGO")
     * })
     */
    private $codSegmento;

    /**
     * @var \Entidades\ZgfmtOrganizacao
     *
     * @ORM\ManyToOne(targetEntity="Entidades\ZgfmtOrganizacao")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="COD_ORGANIZACAO", referencedColumnName="CODIGO")
     * })
     */
    private $codOrganizacao;


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
     * Set codSegmento
     *
     * @param \Entidades\ZgfmtSegmentoMercado $codSegmento
     * @return ZgfmtOrganizacaoSegmento
     */
    public function setCodSegmento(\Entidades\ZgfmtSegmentoMercado $codSegmento = null)
    {
        $this->codSegmento = $codSegmento;

        return $this;
    }

    /**
     * Get codSegmento
     *
     * @return \Entidades\ZgfmtSegmentoMercado 
     */
    public function getCodSegmento()
    {
        return $this->codSegmento;
    }

    /**
     * Set codOrganizacao
     *
     * @param \Entidades\ZgfmtOrganizacao $codOrganizacao
     * @return ZgfmtOrganizacaoSegmento
     */
    public function setCodOrganizacao(\Entidades\ZgfmtOrganizacao $codOrganizacao = null)
    {
        $this->codOrganizacao = $codOrganizacao;

        return $this;
    }

    /**
     * Get codOrganizacao
     *
     * @return \Entidades\ZgfmtOrganizacao 
     */
    public function getCodOrganizacao()
    {
        return $this->codOrganizacao;
    }
}
