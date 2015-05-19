<?php

namespace Entidades;

use Doctrine\ORM\Mapping as ORM;

/**
 * ZgfmtOrganizacaoServico
 *
 * @ORM\Table(name="ZGFMT_ORGANIZACAO_SERVICO", indexes={@ORM\Index(name="fk_ZGFOR_FORNEC_SERVICO_2_idx", columns={"COD_SERVICO"}), @ORM\Index(name="fk_ZGFOR_ORGANIZACAO_SERVICO_1_idx", columns={"COD_FORNECEDOR"})})
 * @ORM\Entity
 */
class ZgfmtOrganizacaoServico
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
     * @var \Entidades\ZgfmtServico
     *
     * @ORM\ManyToOne(targetEntity="Entidades\ZgfmtServico")
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
     *   @ORM\JoinColumn(name="COD_FORNECEDOR", referencedColumnName="CODIGO")
     * })
     */
    private $codFornecedor;


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
     * @param \Entidades\ZgfmtServico $codServico
     * @return ZgfmtOrganizacaoServico
     */
    public function setCodServico(\Entidades\ZgfmtServico $codServico = null)
    {
        $this->codServico = $codServico;

        return $this;
    }

    /**
     * Get codServico
     *
     * @return \Entidades\ZgfmtServico 
     */
    public function getCodServico()
    {
        return $this->codServico;
    }

    /**
     * Set codFornecedor
     *
     * @param \Entidades\ZgfmtOrganizacao $codFornecedor
     * @return ZgfmtOrganizacaoServico
     */
    public function setCodFornecedor(\Entidades\ZgfmtOrganizacao $codFornecedor = null)
    {
        $this->codFornecedor = $codFornecedor;

        return $this;
    }

    /**
     * Get codFornecedor
     *
     * @return \Entidades\ZgfmtOrganizacao 
     */
    public function getCodFornecedor()
    {
        return $this->codFornecedor;
    }
}
