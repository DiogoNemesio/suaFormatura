<?php

namespace Entidades;

use Doctrine\ORM\Mapping as ORM;

/**
 * ZgfmtRifaGeracao
 *
 * @ORM\Table(name="ZGFMT_RIFA_GERACAO", indexes={@ORM\Index(name="fk_ZGFMT_RIFA_GERACAO_1_idx", columns={"COD_USUARIO"}), @ORM\Index(name="fk_ZGFMT_RIFA_GERACAO_2_idx", columns={"COD_GERACAO"}), @ORM\Index(name="fk_ZGFMT_RIFA_GERACAO_3_idx", columns={"COD_RIFA"})})
 * @ORM\Entity
 */
class ZgfmtRifaGeracao
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
     *   @ORM\JoinColumn(name="COD_USUARIO", referencedColumnName="CODIGO")
     * })
     */
    private $codUsuario;

    /**
     * @var \Entidades\ZgfmtRifaGeracaoSequencial
     *
     * @ORM\ManyToOne(targetEntity="Entidades\ZgfmtRifaGeracaoSequencial")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="COD_GERACAO", referencedColumnName="CODIGO")
     * })
     */
    private $codGeracao;

    /**
     * @var \Entidades\ZgfmtRifa
     *
     * @ORM\ManyToOne(targetEntity="Entidades\ZgfmtRifa")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="COD_RIFA", referencedColumnName="CODIGO")
     * })
     */
    private $codRifa;


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
     * Set data
     *
     * @param \DateTime $data
     * @return ZgfmtRifaGeracao
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
     * Set codUsuario
     *
     * @param \Entidades\ZgsegUsuario $codUsuario
     * @return ZgfmtRifaGeracao
     */
    public function setCodUsuario(\Entidades\ZgsegUsuario $codUsuario = null)
    {
        $this->codUsuario = $codUsuario;

        return $this;
    }

    /**
     * Get codUsuario
     *
     * @return \Entidades\ZgsegUsuario 
     */
    public function getCodUsuario()
    {
        return $this->codUsuario;
    }

    /**
     * Set codGeracao
     *
     * @param \Entidades\ZgfmtRifaGeracaoSequencial $codGeracao
     * @return ZgfmtRifaGeracao
     */
    public function setCodGeracao(\Entidades\ZgfmtRifaGeracaoSequencial $codGeracao = null)
    {
        $this->codGeracao = $codGeracao;

        return $this;
    }

    /**
     * Get codGeracao
     *
     * @return \Entidades\ZgfmtRifaGeracaoSequencial 
     */
    public function getCodGeracao()
    {
        return $this->codGeracao;
    }

    /**
     * Set codRifa
     *
     * @param \Entidades\ZgfmtRifa $codRifa
     * @return ZgfmtRifaGeracao
     */
    public function setCodRifa(\Entidades\ZgfmtRifa $codRifa = null)
    {
        $this->codRifa = $codRifa;

        return $this;
    }

    /**
     * Get codRifa
     *
     * @return \Entidades\ZgfmtRifa 
     */
    public function getCodRifa()
    {
        return $this->codRifa;
    }
}
