<?php

namespace Entidades;

use Doctrine\ORM\Mapping as ORM;

/**
 * ZgadmLogradouro
 *
 * @ORM\Table(name="ZGADM_LOGRADOURO", uniqueConstraints={@ORM\UniqueConstraint(name="COD_CORREIO_UNIQUE", columns={"COD_CORREIO"})}, indexes={@ORM\Index(name="fk_ZGADM_LOGRADOURO_2_idx", columns={"COD_TIPO"}), @ORM\Index(name="fk_ZGADM_LOGRADOURO_1_idx", columns={"COD_BAIRRO"})})
 * @ORM\Entity
 */
class ZgadmLogradouro
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
     * @var string
     *
     * @ORM\Column(name="CEP", type="string", length=8, nullable=false)
     */
    private $cep;

    /**
     * @var \Entidades\ZgadmLogradouroTipo
     *
     * @ORM\ManyToOne(targetEntity="Entidades\ZgadmLogradouroTipo")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="COD_TIPO", referencedColumnName="CODIGO")
     * })
     */
    private $codTipo;

    /**
     * @var \Entidades\ZgadmBairro
     *
     * @ORM\ManyToOne(targetEntity="Entidades\ZgadmBairro")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="COD_BAIRRO", referencedColumnName="CODIGO")
     * })
     */
    private $codBairro;


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
     * @return ZgadmLogradouro
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
     * @return ZgadmLogradouro
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
     * Set cep
     *
     * @param string $cep
     * @return ZgadmLogradouro
     */
    public function setCep($cep)
    {
        $this->cep = $cep;

        return $this;
    }

    /**
     * Get cep
     *
     * @return string 
     */
    public function getCep()
    {
        return $this->cep;
    }

    /**
     * Set codTipo
     *
     * @param \Entidades\ZgadmLogradouroTipo $codTipo
     * @return ZgadmLogradouro
     */
    public function setCodTipo(\Entidades\ZgadmLogradouroTipo $codTipo = null)
    {
        $this->codTipo = $codTipo;

        return $this;
    }

    /**
     * Get codTipo
     *
     * @return \Entidades\ZgadmLogradouroTipo 
     */
    public function getCodTipo()
    {
        return $this->codTipo;
    }

    /**
     * Set codBairro
     *
     * @param \Entidades\ZgadmBairro $codBairro
     * @return ZgadmLogradouro
     */
    public function setCodBairro(\Entidades\ZgadmBairro $codBairro = null)
    {
        $this->codBairro = $codBairro;

        return $this;
    }

    /**
     * Get codBairro
     *
     * @return \Entidades\ZgadmBairro 
     */
    public function getCodBairro()
    {
        return $this->codBairro;
    }
}
