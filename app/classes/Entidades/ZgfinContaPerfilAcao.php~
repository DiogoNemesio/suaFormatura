<?php

namespace Entidades;

use Doctrine\ORM\Mapping as ORM;

/**
 * ZgfinContaPerfilAcao
 *
 * @ORM\Table(name="ZGFIN_CONTA_PERFIL_ACAO", indexes={@ORM\Index(name="ZGFIN_CONTA_PERFIL_ACAO_UK01", columns={"COD_CONTA_PERFIL", "COD_ACAO"}), @ORM\Index(name="fk_ZGFIN_CONTA_PERFIL_ACAO_2_idx", columns={"COD_ACAO"}), @ORM\Index(name="IDX_27CB7B1F49E4CF1C", columns={"COD_CONTA_PERFIL"})})
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
     *   @ORM\JoinColumn(name="COD_ACAO", referencedColumnName="CODIGO")
     * })
     */
    private $codAcao;


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
     * Set codAcao
     *
     * @param \Entidades\ZgfinContaAcaoTipo $codAcao
     * @return ZgfinContaPerfilAcao
     */
    public function setCodAcao(\Entidades\ZgfinContaAcaoTipo $codAcao = null)
    {
        $this->codAcao = $codAcao;

        return $this;
    }

    /**
     * Get codAcao
     *
     * @return \Entidades\ZgfinContaAcaoTipo 
     */
    public function getCodAcao()
    {
        return $this->codAcao;
    }
}
