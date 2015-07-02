<?php

namespace Entidades;

use Doctrine\ORM\Mapping as ORM;

/**
 * ZgfinArquivoRegistroTipo
 *
 * @ORM\Table(name="ZGFIN_ARQUIVO_REGISTRO_TIPO", uniqueConstraints={@ORM\UniqueConstraint(name="ZGFIN_ARQUIVO_REGISTRO_TIPO_uk01", columns={"COD_TIPO_REGISTRO", "COD_TIPO_ARQUIVO"})}, indexes={@ORM\Index(name="fk_ZGFIN_ARQUIVO_REGISTRO_TIPO_1_idx", columns={"COD_TIPO_ARQUIVO"})})
 * @ORM\Entity
 */
class ZgfinArquivoRegistroTipo
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
     * @ORM\Column(name="COD_TIPO_REGISTRO", type="string", length=1, nullable=false)
     */
    private $codTipoRegistro;

    /**
     * @var string
     *
     * @ORM\Column(name="NOME", type="string", length=60, nullable=false)
     */
    private $nome;

    /**
     * @var integer
     *
     * @ORM\Column(name="IND_OBRIGATORIO", type="integer", nullable=false)
     */
    private $indObrigatorio;

    /**
     * @var \Entidades\ZgfinArquivoTipo
     *
     * @ORM\ManyToOne(targetEntity="Entidades\ZgfinArquivoTipo")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="COD_TIPO_ARQUIVO", referencedColumnName="CODIGO")
     * })
     */
    private $codTipoArquivo;


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
     * Set codTipoRegistro
     *
     * @param string $codTipoRegistro
     * @return ZgfinArquivoRegistroTipo
     */
    public function setCodTipoRegistro($codTipoRegistro)
    {
        $this->codTipoRegistro = $codTipoRegistro;

        return $this;
    }

    /**
     * Get codTipoRegistro
     *
     * @return string 
     */
    public function getCodTipoRegistro()
    {
        return $this->codTipoRegistro;
    }

    /**
     * Set nome
     *
     * @param string $nome
     * @return ZgfinArquivoRegistroTipo
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
     * Set indObrigatorio
     *
     * @param integer $indObrigatorio
     * @return ZgfinArquivoRegistroTipo
     */
    public function setIndObrigatorio($indObrigatorio)
    {
        $this->indObrigatorio = $indObrigatorio;

        return $this;
    }

    /**
     * Get indObrigatorio
     *
     * @return integer 
     */
    public function getIndObrigatorio()
    {
        return $this->indObrigatorio;
    }

    /**
     * Set codTipoArquivo
     *
     * @param \Entidades\ZgfinArquivoTipo $codTipoArquivo
     * @return ZgfinArquivoRegistroTipo
     */
    public function setCodTipoArquivo(\Entidades\ZgfinArquivoTipo $codTipoArquivo = null)
    {
        $this->codTipoArquivo = $codTipoArquivo;

        return $this;
    }

    /**
     * Get codTipoArquivo
     *
     * @return \Entidades\ZgfinArquivoTipo 
     */
    public function getCodTipoArquivo()
    {
        return $this->codTipoArquivo;
    }
}
