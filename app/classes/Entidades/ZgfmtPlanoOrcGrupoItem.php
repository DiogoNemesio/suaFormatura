<?php

namespace Entidades;

use Doctrine\ORM\Mapping as ORM;

/**
 * ZgfmtPlanoOrcGrupoItem
 *
 * @ORM\Table(name="ZGFMT_PLANO_ORC_GRUPO_ITEM", indexes={@ORM\Index(name="fk_ZGFMT_PLANO_ORC_GRUPO_ITEM_1_idx", columns={"COD_TIPO_EVENTO"})})
 * @ORM\Entity
 */
class ZgfmtPlanoOrcGrupoItem
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
     * @var \Entidades\ZgfmtEventoTipo
     *
     * @ORM\ManyToOne(targetEntity="Entidades\ZgfmtEventoTipo")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="COD_TIPO_EVENTO", referencedColumnName="CODIGO")
     * })
     */
    private $codTipoEvento;


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
     * @return ZgfmtPlanoOrcGrupoItem
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
     * Set codTipoEvento
     *
     * @param \Entidades\ZgfmtEventoTipo $codTipoEvento
     * @return ZgfmtPlanoOrcGrupoItem
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
}
