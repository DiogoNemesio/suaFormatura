<?php

namespace Entidades;

use Doctrine\ORM\Mapping as ORM;

/**
 * ZgfmtEnquetePergunta
 *
 * @ORM\Table(name="ZGFMT_ENQUETE_PERGUNTA", indexes={@ORM\Index(name="fk_ZGFOR_ENQUETE_PERGUNTA_1_idx", columns={"COD_ENQUETE"}), @ORM\Index(name="fk_ZGFOR_ENQUETE_PERGUNTA_2_idx", columns={"COD_TIPO"})})
 * @ORM\Entity
 */
class ZgfmtEnquetePergunta
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
     * @var \Entidades\ZgfmtEnquete
     *
     * @ORM\ManyToOne(targetEntity="Entidades\ZgfmtEnquete")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="COD_ENQUETE", referencedColumnName="CODIGO")
     * })
     */
    private $codEnquete;

    /**
     * @var \Entidades\ZgfmtEnquetePerguntaTipo
     *
     * @ORM\ManyToOne(targetEntity="Entidades\ZgfmtEnquetePerguntaTipo")
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
     * @return ZgfmtEnquetePergunta
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
     * @param \Entidades\ZgfmtEnquete $codEnquete
     * @return ZgfmtEnquetePergunta
     */
    public function setCodEnquete(\Entidades\ZgfmtEnquete $codEnquete = null)
    {
        $this->codEnquete = $codEnquete;

        return $this;
    }

    /**
     * Get codEnquete
     *
     * @return \Entidades\ZgfmtEnquete 
     */
    public function getCodEnquete()
    {
        return $this->codEnquete;
    }

    /**
     * Set codTipo
     *
     * @param \Entidades\ZgfmtEnquetePerguntaTipo $codTipo
     * @return ZgfmtEnquetePergunta
     */
    public function setCodTipo(\Entidades\ZgfmtEnquetePerguntaTipo $codTipo = null)
    {
        $this->codTipo = $codTipo;

        return $this;
    }

    /**
     * Get codTipo
     *
     * @return \Entidades\ZgfmtEnquetePerguntaTipo 
     */
    public function getCodTipo()
    {
        return $this->codTipo;
    }
}
