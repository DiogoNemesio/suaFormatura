<?php

namespace Entidades;

use Doctrine\ORM\Mapping as ORM;

/**
 * ZgfmtConviteExtraVenda
 *
 * @ORM\Table(name="ZGFMT_CONVITE_EXTRA_VENDA", indexes={@ORM\Index(name="fk_ZGFMT_CONVITE_EXTRA_VENDA_1_idx", columns={"COD_CONVITE_CONF"}), @ORM\Index(name="fk_ZGFMT_CONVITE_EXTRA_VENDA_2_idx", columns={"COD_CLIENTE"})})
 * @ORM\Entity
 */
class ZgfmtConviteExtraVenda
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
     * @ORM\Column(name="QUANTIDADE", type="integer", nullable=false)
     */
    private $quantidade;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="DATA_CADASTRO", type="datetime", nullable=false)
     */
    private $dataCadastro;

    /**
     * @var \Entidades\ZgfmtConviteExtraConf
     *
     * @ORM\ManyToOne(targetEntity="Entidades\ZgfmtConviteExtraConf")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="COD_CONVITE_CONF", referencedColumnName="CODIGO")
     * })
     */
    private $codConviteConf;

    /**
     * @var \Entidades\ZgfinPessoa
     *
     * @ORM\ManyToOne(targetEntity="Entidades\ZgfinPessoa")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="COD_CLIENTE", referencedColumnName="CODIGO")
     * })
     */
    private $codCliente;


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
     * Set quantidade
     *
     * @param integer $quantidade
     * @return ZgfmtConviteExtraVenda
     */
    public function setQuantidade($quantidade)
    {
        $this->quantidade = $quantidade;

        return $this;
    }

    /**
     * Get quantidade
     *
     * @return integer 
     */
    public function getQuantidade()
    {
        return $this->quantidade;
    }

    /**
     * Set dataCadastro
     *
     * @param \DateTime $dataCadastro
     * @return ZgfmtConviteExtraVenda
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
     * Set codConviteConf
     *
     * @param \Entidades\ZgfmtConviteExtraConf $codConviteConf
     * @return ZgfmtConviteExtraVenda
     */
    public function setCodConviteConf(\Entidades\ZgfmtConviteExtraConf $codConviteConf = null)
    {
        $this->codConviteConf = $codConviteConf;

        return $this;
    }

    /**
     * Get codConviteConf
     *
     * @return \Entidades\ZgfmtConviteExtraConf 
     */
    public function getCodConviteConf()
    {
        return $this->codConviteConf;
    }

    /**
     * Set codCliente
     *
     * @param \Entidades\ZgfinPessoa $codCliente
     * @return ZgfmtConviteExtraVenda
     */
    public function setCodCliente(\Entidades\ZgfinPessoa $codCliente = null)
    {
        $this->codCliente = $codCliente;

        return $this;
    }

    /**
     * Get codCliente
     *
     * @return \Entidades\ZgfinPessoa 
     */
    public function getCodCliente()
    {
        return $this->codCliente;
    }
}
