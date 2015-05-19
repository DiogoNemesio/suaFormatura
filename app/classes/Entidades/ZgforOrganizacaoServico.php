<?php

namespace Entidades;

use Doctrine\ORM\Mapping as ORM;

/**
 * ZgforOrganizacaoServico
 *
 * @ORM\Table(name="ZGFOR_ORGANIZACAO_SERVICO", indexes={@ORM\Index(name="fk_ZGFOR_FORNEC_SERVICO_2_idx", columns={"COD_SERVICO"}), @ORM\Index(name="fk_ZGFOR_ORGANIZACAO_SERVICO_1_idx", columns={"COD_FORNECEDOR"})})
 * @ORM\Entity
 */
class ZgforOrganizacaoServico
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
     * @var \Entidades\ZgforServico
     *
     * @ORM\ManyToOne(targetEntity="Entidades\ZgforServico")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="COD_SERVICO", referencedColumnName="CODIGO")
     * })
     */
    private $codServico;

    /**
     * @var \Entidades\ZgforOrganizacao
     *
     * @ORM\ManyToOne(targetEntity="Entidades\ZgforOrganizacao")
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
     * @param \Entidades\ZgforServico $codServico
     * @return ZgforOrganizacaoServico
     */
    public function setCodServico(\Entidades\ZgforServico $codServico = null)
    {
        $this->codServico = $codServico;

        return $this;
    }

    /**
     * Get codServico
     *
     * @return \Entidades\ZgforServico 
     */
    public function getCodServico()
    {
        return $this->codServico;
    }

    /**
     * Set codFornecedor
     *
     * @param \Entidades\ZgforOrganizacao $codFornecedor
     * @return ZgforOrganizacaoServico
     */
    public function setCodFornecedor(\Entidades\ZgforOrganizacao $codFornecedor = null)
    {
        $this->codFornecedor = $codFornecedor;

        return $this;
    }

    /**
     * Get codFornecedor
     *
     * @return \Entidades\ZgforOrganizacao 
     */
    public function getCodFornecedor()
    {
        return $this->codFornecedor;
    }
}
