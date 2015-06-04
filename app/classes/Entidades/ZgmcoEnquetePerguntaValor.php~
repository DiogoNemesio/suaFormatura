<?php

namespace Entidades;

use Doctrine\ORM\Mapping as ORM;

/**
 * ZgmcoEnquetePerguntaValor
 *
 * @ORM\Table(name="ZGMCO_ENQUETE_PERGUNTA_VALOR", indexes={@ORM\Index(name="fk_ZGMCO_ENQUETE_PERGUNTA_VALOR_1_idx", columns={"COD_PERGUNTA"})})
 * @ORM\Entity
 */
class ZgmcoEnquetePerguntaValor
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
     * @var \Entidades\ZgmcoEnquetePergunta
     *
     * @ORM\ManyToOne(targetEntity="Entidades\ZgmcoEnquetePergunta")
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
     * @return ZgmcoEnquetePerguntaValor
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
     * @param \Entidades\ZgmcoEnquetePergunta $codPergunta
     * @return ZgmcoEnquetePerguntaValor
     */
    public function setCodPergunta(\Entidades\ZgmcoEnquetePergunta $codPergunta = null)
    {
        $this->codPergunta = $codPergunta;

        return $this;
    }

    /**
     * Get codPergunta
     *
     * @return \Entidades\ZgmcoEnquetePergunta 
     */
    public function getCodPergunta()
    {
        return $this->codPergunta;
    }
}
