<?php

namespace Entidades;

use Doctrine\ORM\Mapping as ORM;

/**
 * ZgsegAvatar
 *
 * @ORM\Table(name="ZGSEG_AVATAR", indexes={@ORM\Index(name="fk_ZGSEG_AVATAR_1_idx", columns={"SEXO"})})
 * @ORM\Entity
 */
class ZgsegAvatar
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
     * @ORM\Column(name="NOME", type="string", length=45, nullable=false)
     */
    private $nome;

    /**
     * @var string
     *
     * @ORM\Column(name="LINK", type="string", length=200, nullable=false)
     */
    private $link;

    /**
     * @var \Entidades\ZgsegSexoTipo
     *
     * @ORM\ManyToOne(targetEntity="Entidades\ZgsegSexoTipo")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="SEXO", referencedColumnName="CODIGO")
     * })
     */
    private $sexo;


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
     * Set nome
     *
     * @param string $nome
     * @return ZgsegAvatar
     */
    public function setNome($nome)
    {
        $this->nome = $nome;

        return $this;
    }

    /**
     * Get nome
     *
     * @return string 
     */
    public function getNome()
    {
        return $this->nome;
    }

    /**
     * Set link
     *
     * @param string $link
     * @return ZgsegAvatar
     */
    public function setLink($link)
    {
        $this->link = $link;

        return $this;
    }

    /**
     * Get link
     *
     * @return string 
     */
    public function getLink()
    {
        return $this->link;
    }

    /**
     * Set sexo
     *
     * @param \Entidades\ZgsegSexoTipo $sexo
     * @return ZgsegAvatar
     */
    public function setSexo(\Entidades\ZgsegSexoTipo $sexo = null)
    {
        $this->sexo = $sexo;

        return $this;
    }

    /**
     * Get sexo
     *
     * @return \Entidades\ZgsegSexoTipo 
     */
    public function getSexo()
    {
        return $this->sexo;
    }
}
