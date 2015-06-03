<?php

namespace Entidades;

use Doctrine\ORM\Mapping as ORM;

/**
 * ZgmcoEnquetePergunta
 *
 * @ORM\Table(name="ZGMCO_ENQUETE_PERGUNTA", indexes={@ORM\Index(name="fk_ZGMCO_ENQUETE_PERGUNTA_1_idx", columns={"COD_ORGANIZACAO"}), @ORM\Index(name="fk_ZGMCO_ENQUETE_PERGUNTA_2_idx", columns={"COD_TIPO"}), @ORM\Index(name="fk_ZGMCO_ENQUETE_PERGUNTA_3_idx", columns={"COD_STATUS"})})
 * @ORM\Entity
 */
class ZgmcoEnquetePergunta
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
     * @var string
     *
     * @ORM\Column(name="DESCRICAO", type="string", length=100, nullable=true)
     */
    private $descricao;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="DATA_CADASTRO", type="datetime", nullable=false)
     */
    private $dataCadastro;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="DATA_PRAZO", type="datetime", nullable=true)
     */
    private $dataPrazo;

    /**
     * @var integer
     *
     * @ORM\Column(name="TAMANHO", type="integer", nullable=true)
     */
    private $tamanho;

    /**
     * @var \Entidades\ZgadmOrganizacao
     *
     * @ORM\ManyToOne(targetEntity="Entidades\ZgadmOrganizacao")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="COD_ORGANIZACAO", referencedColumnName="CODIGO")
     * })
     */
    private $codOrganizacao;

    /**
     * @var \Entidades\ZgmcoEnquetePerguntaTipo
     *
     * @ORM\ManyToOne(targetEntity="Entidades\ZgmcoEnquetePerguntaTipo")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="COD_TIPO", referencedColumnName="CODIGO")
     * })
     */
    private $codTipo;

    /**
     * @var \Entidades\ZgmcoEnqueteStatus
     *
     * @ORM\ManyToOne(targetEntity="Entidades\ZgmcoEnqueteStatus")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="COD_STATUS", referencedColumnName="CODIGO")
     * })
     */
    private $codStatus;


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
     * @return ZgmcoEnquetePergunta
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
     * Set descricao
     *
     * @param string $descricao
     * @return ZgmcoEnquetePergunta
     */
    public function setDescricao($descricao)
    {
        $this->descricao = $descricao;

        return $this;
    }

    /**
     * Get descricao
     *
     * @return string 
     */
    public function getDescricao()
    {
        return $this->descricao;
    }

    /**
     * Set dataCadastro
     *
     * @param \DateTime $dataCadastro
     * @return ZgmcoEnquetePergunta
     */
    public function setDataCadastro($dataCadastro)
    {
        $this->dataCadastro = $dataCadastro;

        return $this;
    }

    /**
     * Get dataCadastro
     *
     * @return \DateTime 
     */
    public function getDataCadastro()
    {
        return $this->dataCadastro;
    }

    /**
     * Set dataPrazo
     *
     * @param \DateTime $dataPrazo
     * @return ZgmcoEnquetePergunta
     */
    public function setDataPrazo($dataPrazo)
    {
        $this->dataPrazo = $dataPrazo;

        return $this;
    }

    /**
     * Get dataPrazo
     *
     * @return \DateTime 
     */
    public function getDataPrazo()
    {
        return $this->dataPrazo;
    }

    /**
     * Set tamanho
     *
     * @param integer $tamanho
     * @return ZgmcoEnquetePergunta
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
     * Set codOrganizacao
     *
     * @param \Entidades\ZgadmOrganizacao $codOrganizacao
     * @return ZgmcoEnquetePergunta
     */
    public function setCodOrganizacao(\Entidades\ZgadmOrganizacao $codOrganizacao = null)
    {
        $this->codOrganizacao = $codOrganizacao;

        return $this;
    }

    /**
     * Get codOrganizacao
     *
     * @return \Entidades\ZgadmOrganizacao 
     */
    public function getCodOrganizacao()
    {
        return $this->codOrganizacao;
    }

    /**
     * Set codTipo
     *
     * @param \Entidades\ZgmcoEnquetePerguntaTipo $codTipo
     * @return ZgmcoEnquetePergunta
     */
    public function setCodTipo(\Entidades\ZgmcoEnquetePerguntaTipo $codTipo = null)
    {
        $this->codTipo = $codTipo;

        return $this;
    }

    /**
     * Get codTipo
     *
     * @return \Entidades\ZgmcoEnquetePerguntaTipo 
     */
    public function getCodTipo()
    {
        return $this->codTipo;
    }

    /**
     * Set codStatus
     *
     * @param \Entidades\ZgmcoEnqueteStatus $codStatus
     * @return ZgmcoEnquetePergunta
     */
    public function setCodStatus(\Entidades\ZgmcoEnqueteStatus $codStatus = null)
    {
        $this->codStatus = $codStatus;

        return $this;
    }

    /**
     * Get codStatus
     *
     * @return \Entidades\ZgmcoEnqueteStatus 
     */
    public function getCodStatus()
    {
        return $this->codStatus;
    }
}
