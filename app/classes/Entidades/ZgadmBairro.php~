<?php

namespace Entidades;

use Doctrine\ORM\Mapping as ORM;

/**
 * ZgadmBairro
 *
 * @ORM\Table(name="ZGADM_BAIRRO", uniqueConstraints={@ORM\UniqueConstraint(name="COD_CORREIO_UNIQUE", columns={"COD_CORREIO"})}, indexes={@ORM\Index(name="fk_ZGADM_BAIRRO_1_idx", columns={"COD_LOCALIDADE"})})
 * @ORM\Entity
 */
class ZgadmBairro
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
     * @var \Entidades\ZgadmLocalidade
     *
     * @ORM\ManyToOne(targetEntity="Entidades\ZgadmLocalidade")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="COD_LOCALIDADE", referencedColumnName="CODIGO")
     * })
     */
    private $codLocalidade;


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
     * @return ZgadmBairro
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
     * @return ZgadmBairro
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
     * Set codLocalidade
     *
     * @param \Entidades\ZgadmLocalidade $codLocalidade
     * @return ZgadmBairro
     */
    public function setCodLocalidade(\Entidades\ZgadmLocalidade $codLocalidade = null)
    {
        $this->codLocalidade = $codLocalidade;

        return $this;
    }

    /**
     * Get codLocalidade
     *
     * @return \Entidades\ZgadmLocalidade 
     */
    public function getCodLocalidade()
    {
        return $this->codLocalidade;
    }
}
