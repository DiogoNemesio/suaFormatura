<?php

namespace Entidades;

use Doctrine\ORM\Mapping as ORM;

/**
 * ZgfinArquivoLayoutTipo
 *
 * @ORM\Table(name="ZGFIN_ARQUIVO_LAYOUT_TIPO", indexes={@ORM\Index(name="fk_ZGFIN_ARQUIVO_LAYOUT_TIPO_1_idx", columns={"COD_FLUXO"}), @ORM\Index(name="fk_ZGFIN_ARQUIVO_LAYOUT_TIPO_2_idx", columns={"COD_TIPO_ARQUIVO"})})
 * @ORM\Entity
 */
class ZgfinArquivoLayoutTipo
{
    /**
     * @var string
     *
     * @ORM\Column(name="CODIGO", type="string", length=8, nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $codigo;

    /**
     * @var string
     *
     * @ORM\Column(name="NOME", type="string", length=60, nullable=true)
     */
    private $nome;

    /**
     * @var \Entidades\ZgfinArquivoFluxo
     *
     * @ORM\ManyToOne(targetEntity="Entidades\ZgfinArquivoFluxo")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="COD_FLUXO", referencedColumnName="CODIGO")
     * })
     */
    private $codFluxo;

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
     * @return ZgfinArquivoLayoutTipo
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
     * Set codFluxo
     *
     * @param \Entidades\ZgfinArquivoFluxo $codFluxo
     * @return ZgfinArquivoLayoutTipo
     */
    public function setCodFluxo(\Entidades\ZgfinArquivoFluxo $codFluxo = null)
    {
        $this->codFluxo = $codFluxo;

        return $this;
    }

    /**
     * Get codFluxo
     *
     * @return \Entidades\ZgfinArquivoFluxo 
     */
    public function getCodFluxo()
    {
        return $this->codFluxo;
    }

    /**
     * Set codTipoArquivo
     *
     * @param \Entidades\ZgfinArquivoTipo $codTipoArquivo
     * @return ZgfinArquivoLayoutTipo
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
