<?php

namespace Entidades;

use Doctrine\ORM\Mapping as ORM;

/**
 * ZgfmtPerguntaValor
 *
 * @ORM\Table(name="ZGFMT_PERGUNTA_VALOR", indexes={@ORM\Index(name="fk_ZGFOR_PERGUNTA_VALOR_1_idx", columns={"COD_PERGUNTA"})})
 * @ORM\Entity
 */
class ZgfmtPerguntaValor
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
     * @ORM\Column(name="VALOR", type="string", length=200, nullable=false)
     */
    private $valor;

    /**
     * @var \Entidades\ZgfmtEnquetePergunta
     *
     * @ORM\ManyToOne(targetEntity="Entidades\ZgfmtEnquetePergunta")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="COD_PERGUNTA", referencedColumnName="CODIGO")
     * })
     */
    private $codPergunta;


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
     * Set valor
     *
     * @param string $valor
     * @return ZgfmtPerguntaValor
     */
    public function setValor($valor)
    {
        $this->valor = $valor;

        return $this;
    }

    /**
     * Get valor
     *
     * @return string 
     */
    public function getValor()
    {
        return $this->valor;
    }

    /**
     * Set codPergunta
     *
     * @param \Entidades\ZgfmtEnquetePergunta $codPergunta
     * @return ZgfmtPerguntaValor
     */
    public function setCodPergunta(\Entidades\ZgfmtEnquetePergunta $codPergunta = null)
    {
        $this->codPergunta = $codPergunta;

        return $this;
    }

    /**
     * Get codPergunta
     *
     * @return \Entidades\ZgfmtEnquetePergunta 
     */
    public function getCodPergunta()
    {
        return $this->codPergunta;
    }
}
