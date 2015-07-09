<?php

namespace Entidades;

use Doctrine\ORM\Mapping as ORM;

/**
 * ZgappNotificacaoVariavel
 *
 * @ORM\Table(name="ZGAPP_NOTIFICACAO_VARIAVEL", indexes={@ORM\Index(name="fk_ZGAPP_NOTIFICACAO_VARIAVEL_1_idx", columns={"COD_NOTIFICACAO"})})
 * @ORM\Entity
 */
class ZgappNotificacaoVariavel
{
    /**
     * @var integer
     *
     * @ORM\Column(name="CODIGO", type="bigint", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $codigo;

    /**
     * @var string
     *
     * @ORM\Column(name="VARIAVEL", type="string", length=60, nullable=false)
     */
    private $variavel;

    /**
     * @var string
     *
     * @ORM\Column(name="VALOR", type="string", length=1000, nullable=true)
     */
    private $valor;

    /**
     * @var \Entidades\ZgappNotificacao
     *
     * @ORM\ManyToOne(targetEntity="Entidades\ZgappNotificacao")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="COD_NOTIFICACAO", referencedColumnName="CODIGO")
     * })
     */
    private $codNotificacao;


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
     * Set variavel
     *
     * @param string $variavel
     * @return ZgappNotificacaoVariavel
     */
    public function setVariavel($variavel)
    {
        $this->variavel = $variavel;

        return $this;
    }

    /**
     * Get variavel
     *
     * @return string 
     */
    public function getVariavel()
    {
        return $this->variavel;
    }

    /**
     * Set valor
     *
     * @param string $valor
     * @return ZgappNotificacaoVariavel
     */
    public function setValor($valor)
    {
        $this->valor = $valor;

        return $this;
    }

    /**
     * Get valor
     *
     * @return string 
     */
    public function getValor()
    {
        return $this->valor;
    }

    /**
     * Set codNotificacao
     *
     * @param \Entidades\ZgappNotificacao $codNotificacao
     * @return ZgappNotificacaoVariavel
     */
    public function setCodNotificacao(\Entidades\ZgappNotificacao $codNotificacao = null)
    {
        $this->codNotificacao = $codNotificacao;

        return $this;
    }

    /**
     * Get codNotificacao
     *
     * @return \Entidades\ZgappNotificacao 
     */
    public function getCodNotificacao()
    {
        return $this->codNotificacao;
    }
}
