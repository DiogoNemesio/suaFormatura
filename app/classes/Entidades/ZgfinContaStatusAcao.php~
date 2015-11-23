<?php

namespace Entidades;

use Doctrine\ORM\Mapping as ORM;

/**
 * ZgfinContaStatusAcao
 *
 * @ORM\Table(name="ZGFIN_CONTA_STATUS_ACAO", indexes={@ORM\Index(name="fk_ZGFIN_CONTA_STATUS_ACAO_1_idx", columns={"COD_STATUS"}), @ORM\Index(name="fk_ZGFIN_CONTA_STATUS_ACAO_2_idx", columns={"COD_ACAO"})})
 * @ORM\Entity
 */
class ZgfinContaStatusAcao
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
     * @var \Entidades\ZgfinContaStatusTipo
     *
     * @ORM\ManyToOne(targetEntity="Entidades\ZgfinContaStatusTipo")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="COD_STATUS", referencedColumnName="CODIGO")
     * })
     */
    private $codStatus;

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
     * Set codStatus
     *
     * @param \Entidades\ZgfinContaStatusTipo $codStatus
     * @return ZgfinContaStatusAcao
     */
    public function setCodStatus(\Entidades\ZgfinContaStatusTipo $codStatus = null)
    {
        $this->codStatus = $codStatus;

        return $this;
    }

    /**
     * Get codStatus
     *
     * @return \Entidades\ZgfinContaStatusTipo 
     */
    public function getCodStatus()
    {
        return $this->codStatus;
    }

    /**
     * Set codAcao
     *
     * @param \Entidades\ZgfinContaAcaoTipo $codAcao
     * @return ZgfinContaStatusAcao
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
