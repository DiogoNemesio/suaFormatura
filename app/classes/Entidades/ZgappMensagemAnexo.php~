<?php

namespace Entidades;

use Doctrine\ORM\Mapping as ORM;

/**
 * ZgappMensagemAnexo
 *
 * @ORM\Table(name="ZGAPP_MENSAGEM_ANEXO", indexes={@ORM\Index(name="fk_ZGAPP_MENSAGEM_ANEXO_1_idx", columns={"COD_MENSAGEM"})})
 * @ORM\Entity
 */
class ZgappMensagemAnexo
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
     * @ORM\Column(name="NOME", type="string", length=200, nullable=false)
     */
    private $nome;

    /**
     * @var integer
     *
     * @ORM\Column(name="TAMANHO", type="integer", nullable=false)
     */
    private $tamanho;

    /**
     * @var string
     *
     * @ORM\Column(name="MIMETYPE", type="string", length=120, nullable=false)
     */
    private $mimetype;

    /**
     * @var string
     *
     * @ORM\Column(name="ARQUIVO", type="blob", nullable=true)
     */
    private $arquivo;

    /**
     * @var \Entidades\ZgappMensagem
     *
     * @ORM\ManyToOne(targetEntity="Entidades\ZgappMensagem")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="COD_MENSAGEM", referencedColumnName="CODIGO")
     * })
     */
    private $codMensagem;


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
     * @return ZgappMensagemAnexo
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
     * Set tamanho
     *
     * @param integer $tamanho
     * @return ZgappMensagemAnexo
     */
    public function setTamanho($tamanho)
    {
        $this->tamanho = $tamanho;

        return $this;
    }

    /**
     * Get tamanho
     *
     * @return integer 
     */
    public function getTamanho()
    {
        return $this->tamanho;
    }

    /**
     * Set mimetype
     *
     * @param string $mimetype
     * @return ZgappMensagemAnexo
     */
    public function setMimetype($mimetype)
    {
        $this->mimetype = $mimetype;

        return $this;
    }

    /**
     * Get mimetype
     *
     * @return string 
     */
    public function getMimetype()
    {
        return $this->mimetype;
    }

    /**
     * Set arquivo
     *
     * @param string $arquivo
     * @return ZgappMensagemAnexo
     */
    public function setArquivo($arquivo)
    {
        $this->arquivo = $arquivo;

        return $this;
    }

    /**
     * Get arquivo
     *
     * @return string 
     */
    public function getArquivo()
    {
        return $this->arquivo;
    }

    /**
     * Set codMensagem
     *
     * @param \Entidades\ZgappMensagem $codMensagem
     * @return ZgappMensagemAnexo
     */
    public function setCodMensagem(\Entidades\ZgappMensagem $codMensagem = null)
    {
        $this->codMensagem = $codMensagem;

        return $this;
    }

    /**
     * Get codMensagem
     *
     * @return \Entidades\ZgappMensagem 
     */
    public function getCodMensagem()
    {
        return $this->codMensagem;
    }
}
