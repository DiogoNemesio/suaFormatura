<?php

namespace Entidades;

use Doctrine\ORM\Mapping as ORM;

/**
 * ZgfinCategoria
 *
 * @ORM\Table(name="ZGFIN_CATEGORIA", indexes={@ORM\Index(name="fk_ZGFIN_CATEGORIA_1_idx", columns={"COD_ORGANIZACAO"}), @ORM\Index(name="fk_ZGFIN_CATEGORIA_2_idx", columns={"COD_TIPO"}), @ORM\Index(name="fk_ZGFIN_CATEGORIA_3_idx", columns={"COD_CATEGORIA_PAI"}), @ORM\Index(name="fk_ZGFIN_CATEGORIA_4_idx", columns={"COD_TIPO_ORGANIZACAO"})})
 * @ORM\Entity
 */
class ZgfinCategoria
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
     * @ORM\Column(name="DESCRICAO", type="string", length=60, nullable=false)
     */
    private $descricao;

    /**
     * @var integer
     *
     * @ORM\Column(name="IND_ATIVA", type="integer", nullable=false)
     */
    private $indAtiva;

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
     * @var \Entidades\ZgfinCategoriaTipo
     *
     * @ORM\ManyToOne(targetEntity="Entidades\ZgfinCategoriaTipo")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="COD_TIPO", referencedColumnName="CODIGO")
     * })
     */
    private $codTipo;

    /**
     * @var \Entidades\ZgfinCategoria
     *
     * @ORM\ManyToOne(targetEntity="Entidades\ZgfinCategoria")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="COD_CATEGORIA_PAI", referencedColumnName="CODIGO")
     * })
     */
    private $codCategoriaPai;

    /**
     * @var \Entidades\ZgadmOrganizacaoTipo
     *
     * @ORM\ManyToOne(targetEntity="Entidades\ZgadmOrganizacaoTipo")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="COD_TIPO_ORGANIZACAO", referencedColumnName="CODIGO")
     * })
     */
    private $codTipoOrganizacao;


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
     * Set descricao
     *
     * @param string $descricao
     * @return ZgfinCategoria
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
     * Set indAtiva
     *
     * @param integer $indAtiva
     * @return ZgfinCategoria
     */
    public function setIndAtiva($indAtiva)
    {
        $this->indAtiva = $indAtiva;

        return $this;
    }

    /**
     * Get indAtiva
     *
     * @return integer 
     */
    public function getIndAtiva()
    {
        return $this->indAtiva;
    }

    /**
     * Set codOrganizacao
     *
     * @param \Entidades\ZgadmOrganizacao $codOrganizacao
     * @return ZgfinCategoria
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
     * @param \Entidades\ZgfinCategoriaTipo $codTipo
     * @return ZgfinCategoria
     */
    public function setCodTipo(\Entidades\ZgfinCategoriaTipo $codTipo = null)
    {
        $this->codTipo = $codTipo;

        return $this;
    }

    /**
     * Get codTipo
     *
     * @return \Entidades\ZgfinCategoriaTipo 
     */
    public function getCodTipo()
    {
        return $this->codTipo;
    }

    /**
     * Set codCategoriaPai
     *
     * @param \Entidades\ZgfinCategoria $codCategoriaPai
     * @return ZgfinCategoria
     */
    public function setCodCategoriaPai(\Entidades\ZgfinCategoria $codCategoriaPai = null)
    {
        $this->codCategoriaPai = $codCategoriaPai;

        return $this;
    }

    /**
     * Get codCategoriaPai
     *
     * @return \Entidades\ZgfinCategoria 
     */
    public function getCodCategoriaPai()
    {
        return $this->codCategoriaPai;
    }

    /**
     * Set codTipoOrganizacao
     *
     * @param \Entidades\ZgadmOrganizacaoTipo $codTipoOrganizacao
     * @return ZgfinCategoria
     */
    public function setCodTipoOrganizacao(\Entidades\ZgadmOrganizacaoTipo $codTipoOrganizacao = null)
    {
        $this->codTipoOrganizacao = $codTipoOrganizacao;

        return $this;
    }

    /**
     * Get codTipoOrganizacao
     *
     * @return \Entidades\ZgadmOrganizacaoTipo 
     */
    public function getCodTipoOrganizacao()
    {
        return $this->codTipoOrganizacao;
    }
}
