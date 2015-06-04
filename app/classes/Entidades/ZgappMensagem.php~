<?php

namespace Entidades;

use Doctrine\ORM\Mapping as ORM;

/**
 * ZgappMensagem
 *
 * @ORM\Table(name="ZGAPP_MENSAGEM", indexes={@ORM\Index(name="fk_ZGAPP_MENSAGEM_1_idx", columns={"COD_REMETENTE"})})
 * @ORM\Entity
 */
class ZgappMensagem
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
     * @ORM\Column(name="TITULO", type="string", length=100, nullable=false)
     */
    private $titulo;

    /**
     * @var string
     *
     * @ORM\Column(name="MENSAGEM", type="string", length=4000, nullable=false)
     */
    private $mensagem;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="DATA", type="datetime", nullable=false)
     */
    private $data;

    /**
     * @var \Entidades\ZgsegUsuario
     *
     * @ORM\ManyToOne(targetEntity="Entidades\ZgsegUsuario")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="COD_REMETENTE", referencedColumnName="CODIGO")
     * })
     */
    private $codRemetente;


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
     * Set titulo
     *
     * @param string $titulo
     * @return ZgappMensagem
     */
    public function setTitulo($titulo)
    {
        $this->titulo = $titulo;

        return $this;
    }

    /**
     * Get titulo
     *
     * @return string 
     */
    public function getTitulo()
    {
        return $this->titulo;
    }

    /**
     * Set mensagem
     *
     * @param string $mensagem
     * @return ZgappMensagem
     */
    public function setMensagem($mensagem)
    {
        $this->mensagem = $mensagem;

        return $this;
    }

    /**
     * Get mensagem
     *
     * @return string 
     */
    public function getMensagem()
    {
        return $this->mensagem;
    }

    /**
     * Set data
     *
     * @param \DateTime $data
     * @return ZgappMensagem
     */
    public function setData($data)
    {
        $this->data = $data;

        return $this;
    }

    /**
     * Get data
     *
     * @return \DateTime 
     */
    public function getData()
    {
        return $this->data;
    }

    /**
     * Set codRemetente
     *
     * @param \Entidades\ZgsegUsuario $codRemetente
     * @return ZgappMensagem
     */
    public function setCodRemetente(\Entidades\ZgsegUsuario $codRemetente = null)
    {
        $this->codRemetente = $codRemetente;

        return $this;
    }

    /**
     * Get codRemetente
     *
     * @return \Entidades\ZgsegUsuario 
     */
    public function getCodRemetente()
    {
        return $this->codRemetente;
    }
}
