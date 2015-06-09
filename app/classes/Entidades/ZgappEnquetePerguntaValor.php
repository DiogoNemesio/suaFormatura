<?php

namespace Entidades;

use Doctrine\ORM\Mapping as ORM;

/**
 * ZgappEnquetePerguntaValor
 *
 * @ORM\Table(name="ZGAPP_ENQUETE_PERGUNTA_VALOR", indexes={@ORM\Index(name="fk_ZGAPP_ENQUETE_PERGUNTA_VALOR_1_idx", columns={"COD_PERGUNTA"})})
 * @ORM\Entity
 */
class ZgappEnquetePerguntaValor
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
     * @var \Entidades\ZgappEnquetePergunta
     *
     * @ORM\ManyToOne(targetEntity="Entidades\ZgappEnquetePergunta")
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
     * @return ZgappEnquetePerguntaValor
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
     * @param \Entidades\ZgappEnquetePergunta $codPergunta
     * @return ZgappEnquetePerguntaValor
     */
    public function setCodPergunta(\Entidades\ZgappEnquetePergunta $codPergunta = null)
    {
        $this->codPergunta = $codPergunta;

        return $this;
    }

    /**
     * Get codPergunta
     *
     * @return \Entidades\ZgappEnquetePergunta 
     */
    public function getCodPergunta()
    {
        return $this->codPergunta;
    }
}
