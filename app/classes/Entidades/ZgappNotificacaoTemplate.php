<?php

namespace Entidades;

use Doctrine\ORM\Mapping as ORM;

/**
 * ZgappNotificacaoTemplate
 *
 * @ORM\Table(name="ZGAPP_NOTIFICACAO_TEMPLATE", uniqueConstraints={@ORM\UniqueConstraint(name="TEMPLATE_UNIQUE", columns={"TEMPLATE"})})
 * @ORM\Entity
 */
class ZgappNotificacaoTemplate
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
     * @ORM\Column(name="TEMPLATE", type="string", length=30, nullable=false)
     */
    private $template;

    /**
     * @var string
     *
     * @ORM\Column(name="DESCRICAO", type="string", length=60, nullable=true)
     */
    private $descricao;

    /**
     * @var string
     *
     * @ORM\Column(name="CAMINHO", type="string", length=100, nullable=false)
     */
    private $caminho;


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
     * Set template
     *
     * @param string $template
     * @return ZgappNotificacaoTemplate
     */
    public function setTemplate($template)
    {
        $this->template = $template;

        return $this;
    }

    /**
     * Get template
     *
     * @return string 
     */
    public function getTemplate()
    {
        return $this->template;
    }

    /**
     * Set descricao
     *
     * @param string $descricao
     * @return ZgappNotificacaoTemplate
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
     * Set caminho
     *
     * @param string $caminho
     * @return ZgappNotificacaoTemplate
     */
    public function setCaminho($caminho)
    {
        $this->caminho = $caminho;

        return $this;
    }

    /**
     * Get caminho
     *
     * @return string 
     */
    public function getCaminho()
    {
        return $this->caminho;
    }
}
