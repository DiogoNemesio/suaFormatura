<?php

namespace Entidades;

use Doctrine\ORM\Mapping as ORM;

/**
 * ZgfinContaPerfilAcao
 *
 * @ORM\Table(name="ZGFIN_CONTA_PERFIL_ACAO", indexes={@ORM\Index(name="ZGFIN_CONTA_PERFIL_ACAO_UK01", columns={"COD_CONTA_PERFIL", "COD_CONTA_ACAO"}), @ORM\Index(name="fk_ZGFIN_CONTA_PERFIL_ACAO_2_idx", columns={"COD_CONTA_ACAO"}), @ORM\Index(name="IDX_27CB7B1F49E4CF1C", columns={"COD_CONTA_PERFIL"})})
 * @ORM\Entity
 */
class ZgfinContaPerfilAcao
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
     * @var \Entidades\ZgfinContaPerfil
     *
     * @ORM\ManyToOne(targetEntity="Entidades\ZgfinContaPerfil")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="COD_CONTA_PERFIL", referencedColumnName="CODIGO")
     * })
     */
    private $codContaPerfil;

    /**
     * @var \Entidades\ZgfinContaAcaoTipo
     *
     * @ORM\ManyToOne(targetEntity="Entidades\ZgfinContaAcaoTipo")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="COD_CONTA_ACAO", referencedColumnName="CODIGO")
     * })
     */
    private $codContaAcao;


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
     * Set codContaPerfil
     *
     * @param \Entidades\ZgfinContaPerfil $codContaPerfil
     * @return ZgfinContaPerfilAcao
     */
    public function setCodContaPerfil(\Entidades\ZgfinContaPerfil $codContaPerfil = null)
    {
        $this->codContaPerfil = $codContaPerfil;

        return $this;
    }

    /**
     * Get codContaPerfil
     *
     * @return \Entidades\ZgfinContaPerfil 
     */
    public function getCodContaPerfil()
    {
        return $this->codContaPerfil;
    }

    /**
     * Set codContaAcao
     *
     * @param \Entidades\ZgfinContaAcaoTipo $codContaAcao
     * @return ZgfinContaPerfilAcao
     */
    public function setCodContaAcao(\Entidades\ZgfinContaAcaoTipo $codContaAcao = null)
    {
        $this->codContaAcao = $codContaAcao;

        return $this;
    }

    /**
     * Get codContaAcao
     *
     * @return \Entidades\ZgfinContaAcaoTipo 
     */
    public function getCodContaAcao()
    {
        return $this->codContaAcao;
    }
}
