<?php

namespace Entidades;

use Doctrine\ORM\Mapping as ORM;

/**
 * ZgwapChipContato
 *
 * @ORM\Table(name="ZGWAP_CHIP_CONTATO", indexes={@ORM\Index(name="fk_ZGWAP_CHIP_CONTACT_1_idx", columns={"COD_USUARIO_TELEFONE"}), @ORM\Index(name="fk_ZGWAP_CHIP_CONTACT_2_idx", columns={"COD_CHIP"})})
 * @ORM\Entity
 */
class ZgwapChipContato
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
     * @var \DateTime
     *
     * @ORM\Column(name="DATA_ULTIMA_SINCRONIZACAO", type="datetime", nullable=true)
     */
    private $dataUltimaSincronizacao;

    /**
     * @var \Entidades\ZgsegUsuarioTelefone
     *
     * @ORM\ManyToOne(targetEntity="Entidades\ZgsegUsuarioTelefone")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="COD_USUARIO_TELEFONE", referencedColumnName="CODIGO")
     * })
     */
    private $codUsuarioTelefone;

    /**
     * @var \Entidades\ZgwapChip
     *
     * @ORM\ManyToOne(targetEntity="Entidades\ZgwapChip")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="COD_CHIP", referencedColumnName="CODIGO")
     * })
     */
    private $codChip;


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
     * Set dataUltimaSincronizacao
     *
     * @param \DateTime $dataUltimaSincronizacao
     * @return ZgwapChipContato
     */
    public function setDataUltimaSincronizacao($dataUltimaSincronizacao)
    {
        $this->dataUltimaSincronizacao = $dataUltimaSincronizacao;

        return $this;
    }

    /**
     * Get dataUltimaSincronizacao
     *
     * @return \DateTime 
     */
    public function getDataUltimaSincronizacao()
    {
        return $this->dataUltimaSincronizacao;
    }

    /**
     * Set codUsuarioTelefone
     *
     * @param \Entidades\ZgsegUsuarioTelefone $codUsuarioTelefone
     * @return ZgwapChipContato
     */
    public function setCodUsuarioTelefone(\Entidades\ZgsegUsuarioTelefone $codUsuarioTelefone = null)
    {
        $this->codUsuarioTelefone = $codUsuarioTelefone;

        return $this;
    }

    /**
     * Get codUsuarioTelefone
     *
     * @return \Entidades\ZgsegUsuarioTelefone 
     */
    public function getCodUsuarioTelefone()
    {
        return $this->codUsuarioTelefone;
    }

    /**
     * Set codChip
     *
     * @param \Entidades\ZgwapChip $codChip
     * @return ZgwapChipContato
     */
    public function setCodChip(\Entidades\ZgwapChip $codChip = null)
    {
        $this->codChip = $codChip;

        return $this;
    }

    /**
     * Get codChip
     *
     * @return \Entidades\ZgwapChip 
     */
    public function getCodChip()
    {
        return $this->codChip;
    }
}
