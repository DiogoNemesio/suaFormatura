<?php

namespace Entidades;

use Doctrine\ORM\Mapping as ORM;

/**
 * ZgfmtOrganizacaoSeguimento
 *
 * @ORM\Table(name="ZGFMT_ORGANIZACAO_SEGUIMENTO", indexes={@ORM\Index(name="fk_ZGFOR_FORNEC_SERVICO_2_idx", columns={"COD_SERVICO"}), @ORM\Index(name="fk_ZGFOR_ORGANIZACAO_SERVICO_1_idx", columns={"COD_ORGANIZACAO"})})
 * @ORM\Entity
 */
class ZgfmtOrganizacaoSeguimento
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
     *   @ORM\JoinColumn(name="COD_SERVICO", referencedColumnName="CODIGO")
     * })
     */
    private $codServico;

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
     * Set codServico
     *
     * @param \Entidades\ZgfmtSegmentoMercado $codServico
     * @return ZgfmtOrganizacaoSeguimento
     */
    public function setCodServico(\Entidades\ZgfmtSegmentoMercado $codServico = null)
    {
        $this->codServico = $codServico;

        return $this;
    }

    /**
     * Get codServico
     *
     * @return \Entidades\ZgfmtSegmentoMercado 
     */
    public function getCodServico()
    {
        return $this->codServico;
    }

    /**
     * Set codOrganizacao
     *
     * @param \Entidades\ZgfmtOrganizacao $codOrganizacao
     * @return ZgfmtOrganizacaoSeguimento
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
