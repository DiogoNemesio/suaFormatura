<?php

namespace Entidades;

use Doctrine\ORM\Mapping as ORM;

/**
 * ZgcasGrupoPadrinho
 *
 * @ORM\Table(name="ZGCAS_GRUPO_PADRINHO")
 * @ORM\Entity
 */
class ZgcasGrupoPadrinho
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
     * @var integer
     *
     * @ORM\Column(name="COD_ORGANIZACAO", type="integer", nullable=true)
     */
    private $codOrganizacao;


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
     * @return ZgcasGrupoPadrinho
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
     * Set codOrganizacao
     *
     * @param integer $codOrganizacao
     * @return ZgcasGrupoPadrinho
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
}
