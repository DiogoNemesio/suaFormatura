<?php

namespace Entidades;

use Doctrine\ORM\Mapping as ORM;

/**
 * ZgmcoEnqueteResposta
 *
 * @ORM\Table(name="ZGMCO_ENQUETE_RESPOSTA", indexes={@ORM\Index(name="fk_ZGMCO_ENQUETE_RESPOSTA_2_idx", columns={"COD_USUARIO"}), @ORM\Index(name="fk_ZGMCO_ENQUETE_RESPOSTA_1_idx", columns={"COD_PERGUNTA"})})
 * @ORM\Entity
 */
class ZgmcoEnqueteResposta
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
     * @ORM\Column(name="RESPOSTA", type="string", length=200, nullable=false)
     */
    private $resposta;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="DATA_RESPOSTA", type="datetime", nullable=false)
     */
    private $dataResposta;

    /**
     * @var \Entidades\ZgsegUsuario
     *
     * @ORM\ManyToOne(targetEntity="Entidades\ZgsegUsuario")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="COD_USUARIO", referencedColumnName="CODIGO")
     * })
     */
    private $codUsuario;

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
     * Set resposta
     *
     * @param string $resposta
     * @return ZgmcoEnqueteResposta
     */
    public function setResposta($resposta)
    {
        $this->resposta = $resposta;

        return $this;
    }

    /**
     * Get resposta
     *
     * @return string 
     */
    public function getResposta()
    {
        return $this->resposta;
    }

    /**
     * Set dataResposta
     *
     * @param \DateTime $dataResposta
     * @return ZgmcoEnqueteResposta
     */
    public function setDataResposta($dataResposta)
    {
        $this->dataResposta = $dataResposta;

        return $this;
    }

    /**
     * Get dataResposta
     *
     * @return \DateTime 
     */
    public function getDataResposta()
    {
        return $this->dataResposta;
    }

    /**
     * Set codUsuario
     *
     * @param \Entidades\ZgsegUsuario $codUsuario
     * @return ZgmcoEnqueteResposta
     */
    public function setCodUsuario(\Entidades\ZgsegUsuario $codUsuario = null)
    {
        $this->codUsuario = $codUsuario;

        return $this;
    }

    /**
     * Get codUsuario
     *
     * @return \Entidades\ZgsegUsuario 
     */
    public function getCodUsuario()
    {
        return $this->codUsuario;
    }

    /**
     * Set codPergunta
     *
     * @param \Entidades\ZgmcoEnquetePergunta $codPergunta
     * @return ZgmcoEnqueteResposta
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
