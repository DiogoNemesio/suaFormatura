<?php

namespace Entidades;

use Doctrine\ORM\Mapping as ORM;

/**
 * ZgfmtPlanoOrcItem
 *
 * @ORM\Table(name="ZGFMT_PLANO_ORC_ITEM", indexes={@ORM\Index(name="fk_ZGFMT_PLANO_ORC_ITEM_1_idx", columns={"COD_VERSAO"}), @ORM\Index(name="fk_ZGFMT_PLANO_ORC_ITEM_2_idx", columns={"COD_TIPO_EVENTO"}), @ORM\Index(name="fk_ZGFMT_PLANO_ORC_ITEM_3_idx", columns={"COD_CATEGORIA"}), @ORM\Index(name="fk_ZGFMT_PLANO_ORC_ITEM_4_idx", columns={"COD_TIPO_ITEM"})})
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
     * @var \Entidades\ZgfmtPlanoOrcamentario
     *
     * @ORM\ManyToOne(targetEntity="Entidades\ZgfmtPlanoOrcamentario")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="COD_VERSAO", referencedColumnName="CODIGO")
     * })
     */
    private $codVersao;

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
     * Set codVersao
     *
     * @param \Entidades\ZgfmtPlanoOrcamentario $codVersao
     * @return ZgfmtPlanoOrcItem
     */
    public function setCodVersao(\Entidades\ZgfmtPlanoOrcamentario $codVersao = null)
    {
        $this->codVersao = $codVersao;

        return $this;
    }

    /**
     * Get codVersao
     *
     * @return \Entidades\ZgfmtPlanoOrcamentario 
     */
    public function getCodVersao()
    {
        return $this->codVersao;
    }

    /**
     * Set codTipoEvento
     *
     * @param \Entidades\ZgfmtEventoTipo $codTipoEvento
     * @return ZgfmtPlanoOrcItem
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