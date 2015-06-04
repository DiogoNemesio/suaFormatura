<?php

namespace Entidades;

use Doctrine\ORM\Mapping as ORM;

/**
 * ZgfinPessoaSegmento
 *
 * @ORM\Table(name="ZGFIN_PESSOA_SEGMENTO", indexes={@ORM\Index(name="fk_ZGFIN_PESSOA_SEGMENTO_1_idx", columns={"COD_PESSOA"}), @ORM\Index(name="fk_ZGFIN_PESSOA_SEGMENTO_2_idx", columns={"COD_SEGMENTO"})})
 * @ORM\Entity
 */
class ZgfinPessoaSegmento
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
     * @return ZgfinPessoaSegmento
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
     * Set codSegmento
     *
     * @param \Entidades\ZgfinSegmentoMercado $codSegmento
     * @return ZgfinPessoaSegmento
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
