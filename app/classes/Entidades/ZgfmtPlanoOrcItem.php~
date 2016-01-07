<?php

namespace Entidades;

use Doctrine\ORM\Mapping as ORM;

/**
 * ZgfmtPlanoOrcItem
 *
 * @ORM\Table(name="ZGFMT_PLANO_ORC_ITEM", indexes={@ORM\Index(name="fk_ZGFMT_PLANO_ORC_ITEM_1_idx", columns={"COD_PLANO"}), @ORM\Index(name="fk_ZGFMT_PLANO_ORC_ITEM_3_idx", columns={"COD_CATEGORIA"}), @ORM\Index(name="fk_ZGFMT_PLANO_ORC_ITEM_4_idx", columns={"COD_TIPO_ITEM"}), @ORM\Index(name="fk_ZGFMT_PLANO_ORC_ITEM_2_idx", columns={"COD_GRUPO_ITEM"})})
 * @ORM\Entity
 */
class ZgfmtPlanoOrcItem
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
     * @ORM\Column(name="ITEM", type="string", length=60, nullable=false)
     */
    private $item;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="DATA_CADASTRO", type="datetime", nullable=false)
     */
    private $dataCadastro;

    /**
     * @var integer
     *
     * @ORM\Column(name="IND_ATIVO", type="integer", nullable=false)
     */
    private $indAtivo;

    /**
     * @var integer
     *
     * @ORM\Column(name="ORDEM", type="integer", nullable=true)
     */
    private $ordem;

    /**
     * @var string
     *
     * @ORM\Column(name="TEXTO_DESCRITIVO", type="string", length=1000, nullable=true)
     */
    private $textoDescritivo;

    /**
     * @var integer
     *
     * @ORM\Column(name="IND_PADRAO", type="integer", nullable=true)
     */
    private $indPadrao;

    /**
     * @var float
     *
     * @ORM\Column(name="VALOR_PADRAO", type="float", precision=10, scale=0, nullable=true)
     */
    private $valorPadrao;

    /**
     * @var \Entidades\ZgfmtPlanoOrcamentario
     *
     * @ORM\ManyToOne(targetEntity="Entidades\ZgfmtPlanoOrcamentario")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="COD_PLANO", referencedColumnName="CODIGO")
     * })
     */
    private $codPlano;

    /**
     * @var \Entidades\ZgfmtPlanoOrcGrupoItem
     *
     * @ORM\ManyToOne(targetEntity="Entidades\ZgfmtPlanoOrcGrupoItem")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="COD_GRUPO_ITEM", referencedColumnName="CODIGO")
     * })
     */
    private $codGrupoItem;

    /**
     * @var \Entidades\ZgfinCategoria
     *
     * @ORM\ManyToOne(targetEntity="Entidades\ZgfinCategoria")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="COD_CATEGORIA", referencedColumnName="CODIGO")
     * })
     */
    private $codCategoria;

    /**
     * @var \Entidades\ZgfmtPlanoOrcItemTipo
     *
     * @ORM\ManyToOne(targetEntity="Entidades\ZgfmtPlanoOrcItemTipo")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="COD_TIPO_ITEM", referencedColumnName="CODIGO")
     * })
     */
    private $codTipoItem;


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
     * Set item
     *
     * @param string $item
     * @return ZgfmtPlanoOrcItem
     */
    public function setItem($item)
    {
        $this->item = $item;

        return $this;
    }

    /**
     * Get item
     *
     * @return string 
     */
    public function getItem()
    {
        return $this->item;
    }

    /**
     * Set dataCadastro
     *
     * @param \DateTime $dataCadastro
     * @return ZgfmtPlanoOrcItem
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
     * Set indAtivo
     *
     * @param integer $indAtivo
     * @return ZgfmtPlanoOrcItem
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
     * Set ordem
     *
     * @param integer $ordem
     * @return ZgfmtPlanoOrcItem
     */
    public function setOrdem($ordem)
    {
        $this->ordem = $ordem;

        return $this;
    }

    /**
     * Get ordem
     *
     * @return integer 
     */
    public function getOrdem()
    {
        return $this->ordem;
    }

    /**
     * Set textoDescritivo
     *
     * @param string $textoDescritivo
     * @return ZgfmtPlanoOrcItem
     */
    public function setTextoDescritivo($textoDescritivo)
    {
        $this->textoDescritivo = $textoDescritivo;

        return $this;
    }

    /**
     * Get textoDescritivo
     *
     * @return string 
     */
    public function getTextoDescritivo()
    {
        return $this->textoDescritivo;
    }

    /**
     * Set indPadrao
     *
     * @param integer $indPadrao
     * @return ZgfmtPlanoOrcItem
     */
    public function setIndPadrao($indPadrao)
    {
        $this->indPadrao = $indPadrao;

        return $this;
    }

    /**
     * Get indPadrao
     *
     * @return integer 
     */
    public function getIndPadrao()
    {
        return $this->indPadrao;
    }

    /**
     * Set valorPadrao
     *
     * @param float $valorPadrao
     * @return ZgfmtPlanoOrcItem
     */
    public function setValorPadrao($valorPadrao)
    {
        $this->valorPadrao = $valorPadrao;

        return $this;
    }

    /**
     * Get valorPadrao
     *
     * @return float 
     */
    public function getValorPadrao()
    {
        return $this->valorPadrao;
    }

    /**
     * Set codPlano
     *
     * @param \Entidades\ZgfmtPlanoOrcamentario $codPlano
     * @return ZgfmtPlanoOrcItem
     */
    public function setCodPlano(\Entidades\ZgfmtPlanoOrcamentario $codPlano = null)
    {
        $this->codPlano = $codPlano;

        return $this;
    }

    /**
     * Get codPlano
     *
     * @return \Entidades\ZgfmtPlanoOrcamentario 
     */
    public function getCodPlano()
    {
        return $this->codPlano;
    }

    /**
     * Set codGrupoItem
     *
     * @param \Entidades\ZgfmtPlanoOrcGrupoItem $codGrupoItem
     * @return ZgfmtPlanoOrcItem
     */
    public function setCodGrupoItem(\Entidades\ZgfmtPlanoOrcGrupoItem $codGrupoItem = null)
    {
        $this->codGrupoItem = $codGrupoItem;

        return $this;
    }

    /**
     * Get codGrupoItem
     *
     * @return \Entidades\ZgfmtPlanoOrcGrupoItem 
     */
    public function getCodGrupoItem()
    {
        return $this->codGrupoItem;
    }

    /**
     * Set codCategoria
     *
     * @param \Entidades\ZgfinCategoria $codCategoria
     * @return ZgfmtPlanoOrcItem
     */
    public function setCodCategoria(\Entidades\ZgfinCategoria $codCategoria = null)
    {
        $this->codCategoria = $codCategoria;

        return $this;
    }

    /**
     * Get codCategoria
     *
     * @return \Entidades\ZgfinCategoria 
     */
    public function getCodCategoria()
    {
        return $this->codCategoria;
    }

    /**
     * Set codTipoItem
     *
     * @param \Entidades\ZgfmtPlanoOrcItemTipo $codTipoItem
     * @return ZgfmtPlanoOrcItem
     */
    public function setCodTipoItem(\Entidades\ZgfmtPlanoOrcItemTipo $codTipoItem = null)
    {
        $this->codTipoItem = $codTipoItem;

        return $this;
    }

    /**
     * Get codTipoItem
     *
     * @return \Entidades\ZgfmtPlanoOrcItemTipo 
     */
    public function getCodTipoItem()
    {
        return $this->codTipoItem;
    }
}
