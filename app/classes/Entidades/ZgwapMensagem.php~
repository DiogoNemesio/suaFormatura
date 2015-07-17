<?php

namespace Entidades;

use Doctrine\ORM\Mapping as ORM;

/**
 * ZgwapMensagem
 *
 * @ORM\Table(name="ZGWAP_MENSAGEM")
 * @ORM\Entity
 */
class ZgwapMensagem
{
    /**
     * @var integer
     *
     * @ORM\Column(name="CODIGO", type="bigint", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $codigo;

    /**
     * @var integer
     *
     * @ORM\Column(name="COD_CHIP", type="integer", nullable=false)
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
     * Set codChip
     *
     * @param integer $codChip
     * @return ZgwapMensagem
     */
    public function setCodChip($codChip)
    {
        $this->codChip = $codChip;

        return $this;
    }

    /**
     * Get codChip
     *
     * @return integer 
     */
    public function getCodChip()
    {
        return $this->codChip;
    }
}
