<?php

namespace Entidades;

use Doctrine\ORM\Mapping as ORM;

/**
 * ZgfinSeqCodGrupoLanc
 *
 * @ORM\Table(name="ZGFIN_SEQ_COD_GRUPO_LANC")
 * @ORM\Entity
 */
class ZgfinSeqCodGrupoLanc
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
     * Get codigo
     *
     * @return integer 
     */
    public function getCodigo()
    {
        return $this->codigo;
    }
}
