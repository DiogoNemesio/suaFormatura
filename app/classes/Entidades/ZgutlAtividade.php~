<?php

namespace Entidades;

use Doctrine\ORM\Mapping as ORM;

/**
 * ZgutlAtividade
 *
 * @ORM\Table(name="ZGUTL_ATIVIDADE", indexes={@ORM\Index(name="fk_ZGUTL_ATIVIDADE_1_idx", columns={"COD_TIPO_ATIVIDADE"})})
 * @ORM\Entity
 */
class ZgutlAtividade
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
     * @ORM\Column(name="IDENTIFICACAO", type="string", length=40, nullable=false)
     */
    private $identificacao;

    /**
     * @var string
     *
     * @ORM\Column(name="DESCRICAO", type="string", length=100, nullable=true)
     */
    private $descricao;

    /**
     * @var \Entidades\ZgutlAtividadeTipo
     *
     * @ORM\ManyToOne(targetEntity="Entidades\ZgutlAtividadeTipo")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="COD_TIPO_ATIVIDADE", referencedColumnName="CODIGO")
     * })
     */
    private $codTipoAtividade;


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
     * Set identificacao
     *
     * @param string $identificacao
     * @return ZgutlAtividade
     */
    public function setIdentificacao($identificacao)
    {
        $this->identificacao = $identificacao;

        return $this;
    }

    /**
     * Get identificacao
     *
     * @return string 
     */
    public function getIdentificacao()
    {
        return $this->identificacao;
    }

    /**
     * Set descricao
     *
     * @param string $descricao
     * @return ZgutlAtividade
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
     * Set codTipoAtividade
     *
     * @param \Entidades\ZgutlAtividadeTipo $codTipoAtividade
     * @return ZgutlAtividade
     */
    public function setCodTipoAtividade(\Entidades\ZgutlAtividadeTipo $codTipoAtividade = null)
    {
        $this->codTipoAtividade = $codTipoAtividade;

        return $this;
    }

    /**
     * Get codTipoAtividade
     *
     * @return \Entidades\ZgutlAtividadeTipo 
     */
    public function getCodTipoAtividade()
    {
        return $this->codTipoAtividade;
    }
}
