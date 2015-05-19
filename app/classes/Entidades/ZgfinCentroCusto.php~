<?php

namespace Entidades;

use Doctrine\ORM\Mapping as ORM;

/**
 * ZgfinCentroCusto
 *
 * @ORM\Table(name="ZGFIN_CENTRO_CUSTO", indexes={@ORM\Index(name="fk_ZGFIN_CENTRO_CUSTO_1_idx", columns={"COD_ORGANIZACAO"})})
 * @ORM\Entity
 */
class ZgfinCentroCusto
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
     * @ORM\Column(name="IND_DEBITO", type="integer", nullable=false)
     */
    private $indDebito;

    /**
     * @var integer
     *
     * @ORM\Column(name="IND_CREDITO", type="integer", nullable=false)
     */
    private $indCredito;

    /**
     * @var \Entidades\ZgfmtOrganizacao
     *
     * @ORM\ManyToOne(targetEntity="Entidades\ZgfmtOrganizacao")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="COD_ORGANIZACAO", referencedColumnName="CODIGO")
     * })
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
     * @return ZgfinCentroCusto
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
     * Set indDebito
     *
     * @param integer $indDebito
     * @return ZgfinCentroCusto
     */
    public function setIndDebito($indDebito)
    {
        $this->indDebito = $indDebito;

        return $this;
    }

    /**
     * Get indDebito
     *
     * @return integer 
     */
    public function getIndDebito()
    {
        return $this->indDebito;
    }

    /**
     * Set indCredito
     *
     * @param integer $indCredito
     * @return ZgfinCentroCusto
     */
    public function setIndCredito($indCredito)
    {
        $this->indCredito = $indCredito;

        return $this;
    }

    /**
     * Get indCredito
     *
     * @return integer 
     */
    public function getIndCredito()
    {
        return $this->indCredito;
    }

    /**
     * Set codOrganizacao
     *
     * @param \Entidades\ZgfmtOrganizacao $codOrganizacao
     * @return ZgfinCentroCusto
     */
    public function setCodOrganizacao(\Entidades\ZgfmtOrganizacao $codOrganizacao = null)
    {
        $this->codOrganizacao = $codOrganizacao;

        return $this;
    }

    /**
     * Get codOrganizacao
     *
     * @return \Entidades\ZgfmtOrganizacao 
     */
    public function getCodOrganizacao()
    {
        return $this->codOrganizacao;
    }
}
