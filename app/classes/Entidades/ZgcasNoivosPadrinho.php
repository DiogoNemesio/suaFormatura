<?php

namespace Entidades;

use Doctrine\ORM\Mapping as ORM;

/**
 * ZgcasNoivosPadrinho
 *
 * @ORM\Table(name="ZGCAS_NOIVOS_PADRINHO")
 * @ORM\Entity
 */
class ZgcasNoivosPadrinho
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
     * @var integer
     *
     * @ORM\Column(name="COD_ORGANIZACAO", type="integer", nullable=false)
     */
    private $codOrganizacao;

    /**
     * @var integer
     *
     * @ORM\Column(name="COD_GRUPO", type="integer", nullable=false)
     */
    private $codGrupo;


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
     * Set codOrganizacao
     *
     * @param integer $codOrganizacao
     * @return ZgcasNoivosPadrinho
     */
    public function setCodOrganizacao($codOrganizacao)
    {
        $this->codOrganizacao = $codOrganizacao;

        return $this;
    }

    /**
     * Get codOrganizacao
     *
     * @return integer 
     */
    public function getCodOrganizacao()
    {
        return $this->codOrganizacao;
    }

    /**
     * Set codGrupo
     *
     * @param integer $codGrupo
     * @return ZgcasNoivosPadrinho
     */
    public function setCodGrupo($codGrupo)
    {
        $this->codGrupo = $codGrupo;

        return $this;
    }

    /**
     * Get codGrupo
     *
     * @return integer 
     */
    public function getCodGrupo()
    {
        return $this->codGrupo;
    }
}
