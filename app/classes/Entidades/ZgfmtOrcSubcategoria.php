<?php

namespace Entidades;

use Doctrine\ORM\Mapping as ORM;

/**
 * ZgfmtOrcSubcategoria
 *
 * @ORM\Table(name="ZGFMT_ORC_SUBCATEGORIA", indexes={@ORM\Index(name="fk_ZGFMT_SUBCATEGORIA_1_idx", columns={"COD_TIPO_EVENTO"}), @ORM\Index(name="fk_ZGFMT_ORC_SUBCATEGORIA_1_idx", columns={"COD_ORC_SUBCATEGORIA"}), @ORM\Index(name="fk_ZGFMT_ORC_SUBCATEGORIA_2_idx", columns={"COD_ORGANIZACAO"})})
 * @ORM\Entity
 */
class ZgfmtOrcSubcategoria
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
     * @ORM\Column(name="NOME", type="string", length=60, nullable=false)
     */
    private $nome;

    /**
     * @var string
     *
     * @ORM\Column(name="DESCRICAO", type="string", length=60, nullable=true)
     */
    private $descricao;

    /**
     * @var integer
     *
     * @ORM\Column(name="IND_ATIVO", type="integer", nullable=false)
     */
    private $indAtivo;

    /**
     * @var \Entidades\ZgfmtEventoTipo
     *
     * @ORM\ManyToOne(targetEntity="Entidades\ZgfmtEventoTipo")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="COD_TIPO_EVENTO", referencedColumnName="CODIGO")
     * })
     */
    private $codTipoEvento;

    /**
     * @var \Entidades\ZgfmtOrcSubcategoria
     *
     * @ORM\ManyToOne(targetEntity="Entidades\ZgfmtOrcSubcategoria")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="COD_ORC_SUBCATEGORIA", referencedColumnName="CODIGO")
     * })
     */
    private $codOrcSubcategoria;

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
     * @return ZgfmtOrcSubcategoria
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
     * Set descricao
     *
     * @param string $descricao
     * @return ZgfmtOrcSubcategoria
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
     * Set indAtivo
     *
     * @param integer $indAtivo
     * @return ZgfmtOrcSubcategoria
     */
    public function setIndAtivo($indAtivo)
    {
        $this->indAtivo = $indAtivo;

        return $this;
    }

    /**
     * Get indAtivo
     *
     * @return integer 
     */
    public function getIndAtivo()
    {
        return $this->indAtivo;
    }

    /**
     * Set codTipoEvento
     *
     * @param \Entidades\ZgfmtEventoTipo $codTipoEvento
     * @return ZgfmtOrcSubcategoria
     */
    public function setCodTipoEvento(\Entidades\ZgfmtEventoTipo $codTipoEvento = null)
    {
        $this->codTipoEvento = $codTipoEvento;

        return $this;
    }

    /**
     * Get codTipoEvento
     *
     * @return \Entidades\ZgfmtEventoTipo 
     */
    public function getCodTipoEvento()
    {
        return $this->codTipoEvento;
    }

    /**
     * Set codOrcSubcategoria
     *
     * @param \Entidades\ZgfmtOrcSubcategoria $codOrcSubcategoria
     * @return ZgfmtOrcSubcategoria
     */
    public function setCodOrcSubcategoria(\Entidades\ZgfmtOrcSubcategoria $codOrcSubcategoria = null)
    {
        $this->codOrcSubcategoria = $codOrcSubcategoria;

        return $this;
    }

    /**
     * Get codOrcSubcategoria
     *
     * @return \Entidades\ZgfmtOrcSubcategoria 
     */
    public function getCodOrcSubcategoria()
    {
        return $this->codOrcSubcategoria;
    }

    /**
     * Set codOrganizacao
     *
     * @param \Entidades\ZgadmOrganizacao $codOrganizacao
     * @return ZgfmtOrcSubcategoria
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
}
