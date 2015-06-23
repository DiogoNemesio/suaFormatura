<?php

namespace Entidades;

use Doctrine\ORM\Mapping as ORM;

/**
 * ZgappImportacaoArquivoTipo
 *
 * @ORM\Table(name="ZGAPP_IMPORTACAO_ARQUIVO_TIPO")
 * @ORM\Entity
 */
class ZgappImportacaoArquivoTipo
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
     * @return ZgappImportacaoArquivoTipo
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
}
