<?php

namespace Entidades;

use Doctrine\ORM\Mapping as ORM;

/**
 * ZgappNotificacaoAnexo
 *
 * @ORM\Table(name="ZGAPP_NOTIFICACAO_ANEXO", indexes={@ORM\Index(name="fk_ZGAPP_NOTIFICACAO_ANEXO_1_idx", columns={"COD_NOTIFICACAO"})})
 * @ORM\Entity
 */
class ZgappNotificacaoAnexo
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
     * @ORM\Column(name="NOME", type="string", length=60, nullable=false)
     */
    private $nome;

    /**
     * @var string
     *
     * @ORM\Column(name="ANEXO", type="blob", nullable=true)
     */
    private $anexo;

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
     * Set nome
     *
     * @param string $nome
     * @return ZgappNotificacaoAnexo
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
     * Set anexo
     *
     * @param string $anexo
     * @return ZgappNotificacaoAnexo
     */
    public function setAnexo($anexo)
    {
        $this->anexo = $anexo;

        return $this;
    }

    /**
     * Get anexo
     *
     * @return string 
     */
    public function getAnexo()
    {
        return $this->anexo;
    }

    /**
     * Set codNotificacao
     *
     * @param \Entidades\ZgappNotificacao $codNotificacao
     * @return ZgappNotificacaoAnexo
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
