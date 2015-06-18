<?php

namespace Entidades;

use Doctrine\ORM\Mapping as ORM;

/**
 * ZgfinArquivoCampoFormato
 *
 * @ORM\Table(name="ZGFIN_ARQUIVO_CAMPO_FORMATO")
 * @ORM\Entity
 */
class ZgfinArquivoCampoFormato
{
    /**
     * @var string
     *
     * @ORM\Column(name="CODIGO", type="string", length=4, nullable=false)
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
     * @ORM\Column(name="ALINHAMENTO", type="string", length=1, nullable=false)
     */
    private $alinhamento;

    /**
     * @var string
     *
     * @ORM\Column(name="CHAR_PREENCHIMENTO", type="string", length=1, nullable=false)
     */
    private $charPreenchimento;


    /**
     * Get codigo
     *
     * @return string 
     */
    public function getCodigo()
    {
        return $this->codigo;
    }

    /**
     * Set nome
     *
     * @param string $nome
     * @return ZgfinArquivoCampoFormato
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
     * Set alinhamento
     *
     * @param string $alinhamento
     * @return ZgfinArquivoCampoFormato
     */
    public function setAlinhamento($alinhamento)
    {
        $this->alinhamento = $alinhamento;

        return $this;
    }

    /**
     * Get alinhamento
     *
     * @return string 
     */
    public function getAlinhamento()
    {
        return $this->alinhamento;
    }

    /**
     * Set charPreenchimento
     *
     * @param string $charPreenchimento
     * @return ZgfinArquivoCampoFormato
     */
    public function setCharPreenchimento($charPreenchimento)
    {
        $this->charPreenchimento = $charPreenchimento;

        return $this;
    }

    /**
     * Get charPreenchimento
     *
     * @return string 
     */
    public function getCharPreenchimento()
    {
        return $this->charPreenchimento;
    }
}
