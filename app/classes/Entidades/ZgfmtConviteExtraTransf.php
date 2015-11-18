<?php

namespace Entidades;

use Doctrine\ORM\Mapping as ORM;

/**
 * ZgfmtConviteExtraTransf
 *
 * @ORM\Table(name="ZGFMT_CONVITE_EXTRA_TRANSF", indexes={@ORM\Index(name="fk_ZGFMT_CONVITE_EXTRA_TRANSF_1_idx", columns={"COD_EVENTO"}), @ORM\Index(name="fk_ZGFMT_CONVITE_EXTRA_TRANSF_2_idx", columns={"COD_FORMANDO_ORIGEM"}), @ORM\Index(name="fk_ZGFMT_CONVITE_EXTRA_TRANSF_3_idx", columns={"COD_FORMANDO_DESTINO"})})
 * @ORM\Entity
 */
class ZgfmtConviteExtraTransf
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
     * @var \Entidades\ZgfmtEvento
     *
     * @ORM\ManyToOne(targetEntity="Entidades\ZgfmtEvento")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="COD_EVENTO", referencedColumnName="CODIGO")
     * })
     */
    private $codEvento;

    /**
     * @var \Entidades\ZgfinPessoa
     *
     * @ORM\ManyToOne(targetEntity="Entidades\ZgfinPessoa")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="COD_FORMANDO_ORIGEM", referencedColumnName="CODIGO")
     * })
     */
    private $codFormandoOrigem;

    /**
     * @var \Entidades\ZgfinPessoa
     *
     * @ORM\ManyToOne(targetEntity="Entidades\ZgfinPessoa")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="COD_FORMANDO_DESTINO", referencedColumnName="CODIGO")
     * })
     */
    private $codFormandoDestino;


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
     * @return ZgfmtConviteExtraTransf
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
     * @return ZgfmtConviteExtraTransf
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
     * Set codEvento
     *
     * @param \Entidades\ZgfmtEvento $codEvento
     * @return ZgfmtConviteExtraTransf
     */
    public function setCodEvento(\Entidades\ZgfmtEvento $codEvento = null)
    {
        $this->codEvento = $codEvento;

        return $this;
    }

    /**
     * Get codEvento
     *
     * @return \Entidades\ZgfmtEvento 
     */
    public function getCodEvento()
    {
        return $this->codEvento;
    }

    /**
     * Set codFormandoOrigem
     *
     * @param \Entidades\ZgfinPessoa $codFormandoOrigem
     * @return ZgfmtConviteExtraTransf
     */
    public function setCodFormandoOrigem(\Entidades\ZgfinPessoa $codFormandoOrigem = null)
    {
        $this->codFormandoOrigem = $codFormandoOrigem;

        return $this;
    }

    /**
     * Get codFormandoOrigem
     *
     * @return \Entidades\ZgfinPessoa 
     */
    public function getCodFormandoOrigem()
    {
        return $this->codFormandoOrigem;
    }

    /**
     * Set codFormandoDestino
     *
     * @param \Entidades\ZgfinPessoa $codFormandoDestino
     * @return ZgfmtConviteExtraTransf
     */
    public function setCodFormandoDestino(\Entidades\ZgfinPessoa $codFormandoDestino = null)
    {
        $this->codFormandoDestino = $codFormandoDestino;

        return $this;
    }

    /**
     * Get codFormandoDestino
     *
     * @return \Entidades\ZgfinPessoa 
     */
    public function getCodFormandoDestino()
    {
        return $this->codFormandoDestino;
    }
}
