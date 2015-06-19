<?php

namespace Entidades;

use Doctrine\ORM\Mapping as ORM;

/**
 * ZgfinArquivoLayoutRegistro
 *
 * @ORM\Table(name="ZGFIN_ARQUIVO_LAYOUT_REGISTRO", indexes={@ORM\Index(name="fk_ZGFIN_ARQUIVO_LAYOUT_REGISTRO_1_idx", columns={"COD_LAYOUT"}), @ORM\Index(name="fk_ZGFIN_ARQUIVO_LAYOUT_REGISTRO_2_idx", columns={"COD_TIPO_REGISTRO"}), @ORM\Index(name="fk_ZGFIN_ARQUIVO_LAYOUT_REGISTRO_3_idx", columns={"COD_FORMATO"}), @ORM\Index(name="fk_ZGFIN_ARQUIVO_LAYOUT_REGISTRO_4_idx", columns={"COD_VARIAVEL"})})
 * @ORM\Entity
 */
class ZgfinArquivoLayoutRegistro
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
     * @var integer
     *
     * @ORM\Column(name="ORDEM", type="integer", nullable=false)
     */
    private $ordem;

    /**
     * @var integer
     *
     * @ORM\Column(name="POSICAO_INICIAL", type="integer", nullable=false)
     */
    private $posicaoInicial;

    /**
     * @var integer
     *
     * @ORM\Column(name="TAMANHO", type="integer", nullable=false)
     */
    private $tamanho;

    /**
     * @var string
     *
     * @ORM\Column(name="VALOR_FIXO", type="string", length=400, nullable=true)
     */
    private $valorFixo;

    /**
     * @var \Entidades\ZgfinArquivoLayout
     *
     * @ORM\ManyToOne(targetEntity="Entidades\ZgfinArquivoLayout")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="COD_LAYOUT", referencedColumnName="CODIGO")
     * })
     */
    private $codLayout;

    /**
     * @var \Entidades\ZgfinArquivoRegistroTipo
     *
     * @ORM\ManyToOne(targetEntity="Entidades\ZgfinArquivoRegistroTipo")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="COD_TIPO_REGISTRO", referencedColumnName="CODIGO")
     * })
     */
    private $codTipoRegistro;

    /**
     * @var \Entidades\ZgfinArquivoCampoFormato
     *
     * @ORM\ManyToOne(targetEntity="Entidades\ZgfinArquivoCampoFormato")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="COD_FORMATO", referencedColumnName="CODIGO")
     * })
     */
    private $codFormato;

    /**
     * @var \Entidades\ZgfinArquivoVariavel
     *
     * @ORM\ManyToOne(targetEntity="Entidades\ZgfinArquivoVariavel")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="COD_VARIAVEL", referencedColumnName="CODIGO")
     * })
     */
    private $codVariavel;


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
     * Set ordem
     *
     * @param integer $ordem
     * @return ZgfinArquivoLayoutRegistro
     */
    public function setOrdem($ordem)
    {
        $this->ordem = $ordem;

        return $this;
    }

    /**
     * Get ordem
     *
     * @return integer 
     */
    public function getOrdem()
    {
        return $this->ordem;
    }

    /**
     * Set posicaoInicial
     *
     * @param integer $posicaoInicial
     * @return ZgfinArquivoLayoutRegistro
     */
    public function setPosicaoInicial($posicaoInicial)
    {
        $this->posicaoInicial = $posicaoInicial;

        return $this;
    }

    /**
     * Get posicaoInicial
     *
     * @return integer 
     */
    public function getPosicaoInicial()
    {
        return $this->posicaoInicial;
    }

    /**
     * Set tamanho
     *
     * @param integer $tamanho
     * @return ZgfinArquivoLayoutRegistro
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
     * Set valorFixo
     *
     * @param string $valorFixo
     * @return ZgfinArquivoLayoutRegistro
     */
    public function setValorFixo($valorFixo)
    {
        $this->valorFixo = $valorFixo;

        return $this;
    }

    /**
     * Get valorFixo
     *
     * @return string 
     */
    public function getValorFixo()
    {
        return $this->valorFixo;
    }

    /**
     * Set codLayout
     *
     * @param \Entidades\ZgfinArquivoLayout $codLayout
     * @return ZgfinArquivoLayoutRegistro
     */
    public function setCodLayout(\Entidades\ZgfinArquivoLayout $codLayout = null)
    {
        $this->codLayout = $codLayout;

        return $this;
    }

    /**
     * Get codLayout
     *
     * @return \Entidades\ZgfinArquivoLayout 
     */
    public function getCodLayout()
    {
        return $this->codLayout;
    }

    /**
     * Set codTipoRegistro
     *
     * @param \Entidades\ZgfinArquivoRegistroTipo $codTipoRegistro
     * @return ZgfinArquivoLayoutRegistro
     */
    public function setCodTipoRegistro(\Entidades\ZgfinArquivoRegistroTipo $codTipoRegistro = null)
    {
        $this->codTipoRegistro = $codTipoRegistro;

        return $this;
    }

    /**
     * Get codTipoRegistro
     *
     * @return \Entidades\ZgfinArquivoRegistroTipo 
     */
    public function getCodTipoRegistro()
    {
        return $this->codTipoRegistro;
    }

    /**
     * Set codFormato
     *
     * @param \Entidades\ZgfinArquivoCampoFormato $codFormato
     * @return ZgfinArquivoLayoutRegistro
     */
    public function setCodFormato(\Entidades\ZgfinArquivoCampoFormato $codFormato = null)
    {
        $this->codFormato = $codFormato;

        return $this;
    }

    /**
     * Get codFormato
     *
     * @return \Entidades\ZgfinArquivoCampoFormato 
     */
    public function getCodFormato()
    {
        return $this->codFormato;
    }

    /**
     * Set codVariavel
     *
     * @param \Entidades\ZgfinArquivoVariavel $codVariavel
     * @return ZgfinArquivoLayoutRegistro
     */
    public function setCodVariavel(\Entidades\ZgfinArquivoVariavel $codVariavel = null)
    {
        $this->codVariavel = $codVariavel;

        return $this;
    }

    /**
     * Get codVariavel
     *
     * @return \Entidades\ZgfinArquivoVariavel 
     */
    public function getCodVariavel()
    {
        return $this->codVariavel;
    }
}
