<?php

namespace Entidades;

use Doctrine\ORM\Mapping as ORM;

/**
 * ZgfinArquivoLayout
 *
 * @ORM\Table(name="ZGFIN_ARQUIVO_LAYOUT", indexes={@ORM\Index(name="fk_ZGFIN_ARQUIVO_LAYOUT_1_idx", columns={"COD_TIPO_LAYOUT"}), @ORM\Index(name="fk_ZGFIN_ARQUIVO_LAYOUT_2_idx", columns={"COD_BANCO"})})
 * @ORM\Entity
 */
class ZgfinArquivoLayout
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
     * @var \Entidades\ZgfinArquivoLayoutTipo
     *
     * @ORM\ManyToOne(targetEntity="Entidades\ZgfinArquivoLayoutTipo")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="COD_TIPO_LAYOUT", referencedColumnName="CODIGO")
     * })
     */
    private $codTipoLayout;

    /**
     * @var \Entidades\ZgfinBanco
     *
     * @ORM\ManyToOne(targetEntity="Entidades\ZgfinBanco")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="COD_BANCO", referencedColumnName="CODIGO")
     * })
     */
    private $codBanco;


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
     * @return ZgfinArquivoLayout
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
     * Set codTipoLayout
     *
     * @param \Entidades\ZgfinArquivoLayoutTipo $codTipoLayout
     * @return ZgfinArquivoLayout
     */
    public function setCodTipoLayout(\Entidades\ZgfinArquivoLayoutTipo $codTipoLayout = null)
    {
        $this->codTipoLayout = $codTipoLayout;

        return $this;
    }

    /**
     * Get codTipoLayout
     *
     * @return \Entidades\ZgfinArquivoLayoutTipo 
     */
    public function getCodTipoLayout()
    {
        return $this->codTipoLayout;
    }

    /**
     * Set codBanco
     *
     * @param \Entidades\ZgfinBanco $codBanco
     * @return ZgfinArquivoLayout
     */
    public function setCodBanco(\Entidades\ZgfinBanco $codBanco = null)
    {
        $this->codBanco = $codBanco;

        return $this;
    }

    /**
     * Get codBanco
     *
     * @return \Entidades\ZgfinBanco 
     */
    public function getCodBanco()
    {
        return $this->codBanco;
    }
}
