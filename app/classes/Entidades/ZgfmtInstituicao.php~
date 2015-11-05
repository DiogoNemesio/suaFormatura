<?php

namespace Entidades;

use Doctrine\ORM\Mapping as ORM;

/**
 * ZgfmtInstituicao
 *
 * @ORM\Table(name="ZGFMT_INSTITUICAO", indexes={@ORM\Index(name="fk_ZGFOR_INSTITUICAO_1_idx", columns={"COD_CIDADE"}), @ORM\Index(name="fk_ZGFOR_INSTITUICAO_2_idx", columns={"COD_REDE"}), @ORM\Index(name="fk_ZGFOR_INSTITUICAO_3_idx", columns={"COD_TIPO"}), @ORM\Index(name="fk_ZGFMT_INSTITUICAO_1_idx", columns={"COD_ADM"})})
 * @ORM\Entity
 */
class ZgfmtInstituicao
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
     * @ORM\Column(name="COD_IES", type="integer", nullable=false)
     */
    private $codIes;

    /**
     * @var string
     *
     * @ORM\Column(name="NOME", type="string", length=100, nullable=false)
     */
    private $nome;

    /**
     * @var string
     *
     * @ORM\Column(name="SIGLA", type="string", length=20, nullable=false)
     */
    private $sigla;

    /**
     * @var \Entidades\ZgfmtInstituicaoAdm
     *
     * @ORM\ManyToOne(targetEntity="Entidades\ZgfmtInstituicaoAdm")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="COD_ADM", referencedColumnName="CODIGO")
     * })
     */
    private $codAdm;

    /**
     * @var \Entidades\ZgadmCidade
     *
     * @ORM\ManyToOne(targetEntity="Entidades\ZgadmCidade")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="COD_CIDADE", referencedColumnName="CODIGO")
     * })
     */
    private $codCidade;

    /**
     * @var \Entidades\ZgfmtInstituicaoRede
     *
     * @ORM\ManyToOne(targetEntity="Entidades\ZgfmtInstituicaoRede")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="COD_REDE", referencedColumnName="CODIGO")
     * })
     */
    private $codRede;

    /**
     * @var \Entidades\ZgfmtInstituicaoTipo
     *
     * @ORM\ManyToOne(targetEntity="Entidades\ZgfmtInstituicaoTipo")
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
     * Set codIes
     *
     * @param integer $codIes
     * @return ZgfmtInstituicao
     */
    public function setCodIes($codIes)
    {
        $this->codIes = $codIes;

        return $this;
    }

    /**
     * Get codIes
     *
     * @return integer 
     */
    public function getCodIes()
    {
        return $this->codIes;
    }

    /**
     * Set nome
     *
     * @param string $nome
     * @return ZgfmtInstituicao
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
     * Set sigla
     *
     * @param string $sigla
     * @return ZgfmtInstituicao
     */
    public function setSigla($sigla)
    {
        $this->sigla = $sigla;

        return $this;
    }

    /**
     * Get sigla
     *
     * @return string 
     */
    public function getSigla()
    {
        return $this->sigla;
    }

    /**
     * Set codAdm
     *
     * @param \Entidades\ZgfmtInstituicaoAdm $codAdm
     * @return ZgfmtInstituicao
     */
    public function setCodAdm(\Entidades\ZgfmtInstituicaoAdm $codAdm = null)
    {
        $this->codAdm = $codAdm;

        return $this;
    }

    /**
     * Get codAdm
     *
     * @return \Entidades\ZgfmtInstituicaoAdm 
     */
    public function getCodAdm()
    {
        return $this->codAdm;
    }

    /**
     * Set codCidade
     *
     * @param \Entidades\ZgadmCidade $codCidade
     * @return ZgfmtInstituicao
     */
    public function setCodCidade(\Entidades\ZgadmCidade $codCidade = null)
    {
        $this->codCidade = $codCidade;

        return $this;
    }

    /**
     * Get codCidade
     *
     * @return \Entidades\ZgadmCidade 
     */
    public function getCodCidade()
    {
        return $this->codCidade;
    }

    /**
     * Set codRede
     *
     * @param \Entidades\ZgfmtInstituicaoRede $codRede
     * @return ZgfmtInstituicao
     */
    public function setCodRede(\Entidades\ZgfmtInstituicaoRede $codRede = null)
    {
        $this->codRede = $codRede;

        return $this;
    }

    /**
     * Get codRede
     *
     * @return \Entidades\ZgfmtInstituicaoRede 
     */
    public function getCodRede()
    {
        return $this->codRede;
    }

    /**
     * Set codTipo
     *
     * @param \Entidades\ZgfmtInstituicaoTipo $codTipo
     * @return ZgfmtInstituicao
     */
    public function setCodTipo(\Entidades\ZgfmtInstituicaoTipo $codTipo = null)
    {
        $this->codTipo = $codTipo;

        return $this;
    }

    /**
     * Get codTipo
     *
     * @return \Entidades\ZgfmtInstituicaoTipo 
     */
    public function getCodTipo()
    {
        return $this->codTipo;
    }
}
