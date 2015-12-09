<?php

namespace Entidades;

use Doctrine\ORM\Mapping as ORM;

/**
 * ZgfmtDesistenciaTipo
 *
 * @ORM\Table(name="ZGFMT_DESISTENCIA_TIPO", indexes={@ORM\Index(name="fk_ZGFMT_DESISTENCIA_TIPO_1_idx", columns={"COD_STATUS"})})
 * @ORM\Entity
 */
class ZgfmtDesistenciaTipo
{
    /**
     * @var string
     *
     * @ORM\Column(name="CODIGO", type="string", length=1, nullable=false)
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
     * @var \Entidades\ZgsegUsuarioOrganizacaoStatus
     *
     * @ORM\ManyToOne(targetEntity="Entidades\ZgsegUsuarioOrganizacaoStatus")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="COD_STATUS", referencedColumnName="CODIGO")
     * })
     */
    private $codStatus;


    /**
     * Get codigo
     *
     * @return string 
     */
    public function getCodigo()
    {
        return $this->codigo;
    }

    /**
     * Set descricao
     *
     * @param string $descricao
     * @return ZgfmtDesistenciaTipo
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
     * Set codStatus
     *
     * @param \Entidades\ZgsegUsuarioOrganizacaoStatus $codStatus
     * @return ZgfmtDesistenciaTipo
     */
    public function setCodStatus(\Entidades\ZgsegUsuarioOrganizacaoStatus $codStatus = null)
    {
        $this->codStatus = $codStatus;

        return $this;
    }

    /**
     * Get codStatus
     *
     * @return \Entidades\ZgsegUsuarioOrganizacaoStatus 
     */
    public function getCodStatus()
    {
        return $this->codStatus;
    }
}
