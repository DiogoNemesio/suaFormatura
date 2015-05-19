<?php

namespace Entidades;

use Doctrine\ORM\Mapping as ORM;

/**
 * ZgforEnquetePergunta
 *
 * @ORM\Table(name="ZGFOR_ENQUETE_PERGUNTA", indexes={@ORM\Index(name="fk_ZGFOR_ENQUETE_PERGUNTA_1_idx", columns={"COD_ENQUETE"}), @ORM\Index(name="fk_ZGFOR_ENQUETE_PERGUNTA_2_idx", columns={"COD_TIPO"})})
 * @ORM\Entity
 */
class ZgforEnquetePergunta
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
     * @ORM\Column(name="PERGUNTA", type="string", length=200, nullable=false)
     */
    private $pergunta;

    /**
     * @var \Entidades\ZgforEnquete
     *
     * @ORM\ManyToOne(targetEntity="Entidades\ZgforEnquete")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="COD_ENQUETE", referencedColumnName="CODIGO")
     * })
     */
    private $codEnquete;

    /**
     * @var \Entidades\ZgforEnquetePerguntaTipo
     *
     * @ORM\ManyToOne(targetEntity="Entidades\ZgforEnquetePerguntaTipo")
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
     * Set pergunta
     *
     * @param string $pergunta
     * @return ZgforEnquetePergunta
     */
    public function setPergunta($pergunta)
    {
        $this->pergunta = $pergunta;

        return $this;
    }

    /**
     * Get pergunta
     *
     * @return string 
     */
    public function getPergunta()
    {
        return $this->pergunta;
    }

    /**
     * Set codEnquete
     *
     * @param \Entidades\ZgforEnquete $codEnquete
     * @return ZgforEnquetePergunta
     */
    public function setCodEnquete(\Entidades\ZgforEnquete $codEnquete = null)
    {
        $this->codEnquete = $codEnquete;

        return $this;
    }

    /**
     * Get codEnquete
     *
     * @return \Entidades\ZgforEnquete 
     */
    public function getCodEnquete()
    {
        return $this->codEnquete;
    }

    /**
     * Set codTipo
     *
     * @param \Entidades\ZgforEnquetePerguntaTipo $codTipo
     * @return ZgforEnquetePergunta
     */
    public function setCodTipo(\Entidades\ZgforEnquetePerguntaTipo $codTipo = null)
    {
        $this->codTipo = $codTipo;

        return $this;
    }

    /**
     * Get codTipo
     *
     * @return \Entidades\ZgforEnquetePerguntaTipo 
     */
    public function getCodTipo()
    {
        return $this->codTipo;
    }
}
