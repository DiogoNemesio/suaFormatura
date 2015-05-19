<?php

namespace Entidades;

use Doctrine\ORM\Mapping as ORM;

/**
 * ZgappLoadHtml
 *
 * @ORM\Table(name="ZGAPP_LOAD_HTML")
 * @ORM\Entity
 */
class ZgappLoadHtml
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
     * @var string
     *
     * @ORM\Column(name="URL", type="string", length=512, nullable=false)
     */
    private $url;

    /**
     * @var boolean
     *
     * @ORM\Column(name="ATIVO", type="boolean", nullable=false)
     */
    private $ativo;

    /**
     * @var integer
     *
     * @ORM\Column(name="ORDEM", type="integer", nullable=false)
     */
    private $ordem;


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
     * Set url
     *
     * @param string $url
     * @return ZgappLoadHtml
     */
    public function setUrl($url)
    {
        $this->url = $url;

        return $this;
    }

    /**
     * Get url
     *
     * @return string 
     */
    public function getUrl()
    {
        return $this->url;
    }

    /**
     * Set ativo
     *
     * @param boolean $ativo
     * @return ZgappLoadHtml
     */
    public function setAtivo($ativo)
    {
        $this->ativo = $ativo;

        return $this;
    }

    /**
     * Get ativo
     *
     * @return boolean 
     */
    public function getAtivo()
    {
        return $this->ativo;
    }

    /**
     * Set ordem
     *
     * @param integer $ordem
     * @return ZgappLoadHtml
     */
    public function setOrdem($ordem)
    {
        $this->ordem = $ordem;

        return $this;
    }

    /**
     * Get ordem
     *
     * @return integer 
     */
    public function getOrdem()
    {
        return $this->ordem;
    }
}
