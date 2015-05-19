<?php

namespace Entidades;

use Doctrine\ORM\Mapping as ORM;

/**
 * ZgappMascara
 *
 * @ORM\Table(name="ZGAPP_MASCARA", indexes={@ORM\Index(name="MASCARAS_FK1", columns={"COD_TIPO"})})
 * @ORM\Entity
 */
class ZgappMascara
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
     * @ORM\Column(name="NOME", type="string", length=40, nullable=false)
     */
    private $nome;

    /**
     * @var string
     *
     * @ORM\Column(name="MASCARA", type="string", length=100, nullable=false)
     */
    private $mascara;

    /**
     * @var integer
     *
     * @ORM\Column(name="IND_REVERSA", type="integer", nullable=false)
     */
    private $indReversa;

    /**
     * @var string
     *
     * @ORM\Column(name="FUNCAO", type="string", length=100, nullable=true)
     */
    private $funcao;

    /**
     * @var integer
     *
     * @ORM\Column(name="IND_MESMO_TAMANHO", type="integer", nullable=false)
     */
    private $indMesmoTamanho;

    /**
     * @var integer
     *
     * @ORM\Column(name="IND_RETIRA_MASCARA", type="integer", nullable=false)
     */
    private $indRetiraMascara;

    /**
     * @var \Entidades\ZgappMascaraTipo
     *
     * @ORM\ManyToOne(targetEntity="Entidades\ZgappMascaraTipo")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="COD_TIPO", referencedColumnName="CODIGO")
     * })
     */
    private $codTipo;


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
     * @return ZgappMascara
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
     * Set mascara
     *
     * @param string $mascara
     * @return ZgappMascara
     */
    public function setMascara($mascara)
    {
        $this->mascara = $mascara;

        return $this;
    }

    /**
     * Get mascara
     *
     * @return string 
     */
    public function getMascara()
    {
        return $this->mascara;
    }

    /**
     * Set indReversa
     *
     * @param integer $indReversa
     * @return ZgappMascara
     */
    public function setIndReversa($indReversa)
    {
        $this->indReversa = $indReversa;

        return $this;
    }

    /**
     * Get indReversa
     *
     * @return integer 
     */
    public function getIndReversa()
    {
        return $this->indReversa;
    }

    /**
     * Set funcao
     *
     * @param string $funcao
     * @return ZgappMascara
     */
    public function setFuncao($funcao)
    {
        $this->funcao = $funcao;

        return $this;
    }

    /**
     * Get funcao
     *
     * @return string 
     */
    public function getFuncao()
    {
        return $this->funcao;
    }

    /**
     * Set indMesmoTamanho
     *
     * @param integer $indMesmoTamanho
     * @return ZgappMascara
     */
    public function setIndMesmoTamanho($indMesmoTamanho)
    {
        $this->indMesmoTamanho = $indMesmoTamanho;

        return $this;
    }

    /**
     * Get indMesmoTamanho
     *
     * @return integer 
     */
    public function getIndMesmoTamanho()
    {
        return $this->indMesmoTamanho;
    }

    /**
     * Set indRetiraMascara
     *
     * @param integer $indRetiraMascara
     * @return ZgappMascara
     */
    public function setIndRetiraMascara($indRetiraMascara)
    {
        $this->indRetiraMascara = $indRetiraMascara;

        return $this;
    }

    /**
     * Get indRetiraMascara
     *
     * @return integer 
     */
    public function getIndRetiraMascara()
    {
        return $this->indRetiraMascara;
    }

    /**
     * Set codTipo
     *
     * @param \Entidades\ZgappMascaraTipo $codTipo
     * @return ZgappMascara
     */
    public function setCodTipo(\Entidades\ZgappMascaraTipo $codTipo = null)
    {
        $this->codTipo = $codTipo;

        return $this;
    }

    /**
     * Get codTipo
     *
     * @return \Entidades\ZgappMascaraTipo 
     */
    public function getCodTipo()
    {
        return $this->codTipo;
    }
}
