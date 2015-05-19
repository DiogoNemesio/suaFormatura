<?php

namespace Entidades;

use Doctrine\ORM\Mapping as ORM;

/**
 * ZgfmtInstituicao
 *
 * @ORM\Table(name="ZGFMT_INSTITUICAO", indexes={@ORM\Index(name="fk_ZGFOR_INSTITUICAO_1_idx", columns={"COD_CIDADE"}), @ORM\Index(name="fk_ZGFOR_INSTITUICAO_2_idx", columns={"COD_CAT_ADM"}), @ORM\Index(name="fk_ZGFOR_INSTITUICAO_3_idx", columns={"COD_ORG_ACD"})})
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
     * @var \Entidades\ZgadmCidade
     *
     * @ORM\ManyToOne(targetEntity="Entidades\ZgadmCidade")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="COD_CIDADE", referencedColumnName="CODIGO")
     * })
     */
    private $codCidade;

    /**
     * @var \Entidades\ZgfmtCatAdm
     *
     * @ORM\ManyToOne(targetEntity="Entidades\ZgfmtCatAdm")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="COD_CAT_ADM", referencedColumnName="CODIGO")
     * })
     */
    private $codCatAdm;

    /**
     * @var \Entidades\ZgfmtOrgAcademica
     *
     * @ORM\ManyToOne(targetEntity="Entidades\ZgfmtOrgAcademica")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="COD_ORG_ACD", referencedColumnName="CODIGO")
     * })
     */
    private $codOrgAcd;


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
     * Set codCatAdm
     *
     * @param \Entidades\ZgfmtCatAdm $codCatAdm
     * @return ZgfmtInstituicao
     */
    public function setCodCatAdm(\Entidades\ZgfmtCatAdm $codCatAdm = null)
    {
        $this->codCatAdm = $codCatAdm;

        return $this;
    }

    /**
     * Get codCatAdm
     *
     * @return \Entidades\ZgfmtCatAdm 
     */
    public function getCodCatAdm()
    {
        return $this->codCatAdm;
    }

    /**
     * Set codOrgAcd
     *
     * @param \Entidades\ZgfmtOrgAcademica $codOrgAcd
     * @return ZgfmtInstituicao
     */
    public function setCodOrgAcd(\Entidades\ZgfmtOrgAcademica $codOrgAcd = null)
    {
        $this->codOrgAcd = $codOrgAcd;

        return $this;
    }

    /**
     * Get codOrgAcd
     *
     * @return \Entidades\ZgfmtOrgAcademica 
     */
    public function getCodOrgAcd()
    {
        return $this->codOrgAcd;
    }
}
