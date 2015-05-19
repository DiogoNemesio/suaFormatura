<?php

namespace Entidades;

use Doctrine\ORM\Mapping as ORM;

/**
 * ZgadmLocalidade
 *
 * @ORM\Table(name="ZGADM_LOCALIDADE", uniqueConstraints={@ORM\UniqueConstraint(name="COD_CORREIO_UNIQUE", columns={"COD_CORREIO"})}, indexes={@ORM\Index(name="fk_ZGADM_LOCALIDADE_1_idx", columns={"COD_UF"}), @ORM\Index(name="fk_ZGADM_LOCALIDADE_2_idx", columns={"COD_CIDADE"})})
 * @ORM\Entity
 */
class ZgadmLocalidade
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
     * @ORM\Column(name="COD_CORREIO", type="integer", nullable=true)
     */
    private $codCorreio;

    /**
     * @var string
     *
     * @ORM\Column(name="DESCRICAO", type="string", length=200, nullable=false)
     */
    private $descricao;

    /**
     * @var \Entidades\ZgadmEstado
     *
     * @ORM\ManyToOne(targetEntity="Entidades\ZgadmEstado")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="COD_UF", referencedColumnName="COD_UF")
     * })
     */
    private $codUf;

    /**
     * @var \Entidades\ZgadmCidade
     *
     * @ORM\ManyToOne(targetEntity="Entidades\ZgadmCidade")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="COD_CIDADE", referencedColumnName="CODIGO")
     * })
     */
    private $codCidade;


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
     * Set codCorreio
     *
     * @param integer $codCorreio
     * @return ZgadmLocalidade
     */
    public function setCodCorreio($codCorreio)
    {
        $this->codCorreio = $codCorreio;

        return $this;
    }

    /**
     * Get codCorreio
     *
     * @return integer 
     */
    public function getCodCorreio()
    {
        return $this->codCorreio;
    }

    /**
     * Set descricao
     *
     * @param string $descricao
     * @return ZgadmLocalidade
     */
    public function setDescricao($descricao)
    {
        $this->descricao = $descricao;

        return $this;
    }

    /**
     * Get descricao
     *
     * @return string 
     */
    public function getDescricao()
    {
        return $this->descricao;
    }

    /**
     * Set codUf
     *
     * @param \Entidades\ZgadmEstado $codUf
     * @return ZgadmLocalidade
     */
    public function setCodUf(\Entidades\ZgadmEstado $codUf = null)
    {
        $this->codUf = $codUf;

        return $this;
    }

    /**
     * Get codUf
     *
     * @return \Entidades\ZgadmEstado 
     */
    public function getCodUf()
    {
        return $this->codUf;
    }

    /**
     * Set codCidade
     *
     * @param \Entidades\ZgadmCidade $codCidade
     * @return ZgadmLocalidade
     */
    public function setCodCidade(\Entidades\ZgadmCidade $codCidade = null)
    {
        $this->codCidade = $codCidade;

        return $this;
    }

    /**
     * Get codCidade
     *
     * @return \Entidades\ZgadmCidade 
     */
    public function getCodCidade()
    {
        return $this->codCidade;
    }
}
