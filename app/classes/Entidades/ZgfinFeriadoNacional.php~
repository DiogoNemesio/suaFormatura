<?php

namespace Entidades;

use Doctrine\ORM\Mapping as ORM;

/**
 * ZgfinFeriadoNacional
 *
 * @ORM\Table(name="ZGFIN_FERIADO_NACIONAL", indexes={@ORM\Index(name="fk_ZGFIN_FERIADO_NACIONAL_1_idx", columns={"COD_ORGANIZACAO"})})
 * @ORM\Entity
 */
class ZgfinFeriadoNacional
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
     * @var string
     *
     * @ORM\Column(name="DIA", type="string", length=2, nullable=false)
     */
    private $dia;

    /**
     * @var string
     *
     * @ORM\Column(name="MES", type="string", length=2, nullable=false)
     */
    private $mes;

    /**
     * @var \Entidades\ZgadmOrganizacao
     *
     * @ORM\ManyToOne(targetEntity="Entidades\ZgadmOrganizacao")
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
     * @return ZgfinFeriadoNacional
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
     * Set dia
     *
     * @param string $dia
     * @return ZgfinFeriadoNacional
     */
    public function setDia($dia)
    {
        $this->dia = $dia;

        return $this;
    }

    /**
     * Get dia
     *
     * @return string 
     */
    public function getDia()
    {
        return $this->dia;
    }

    /**
     * Set mes
     *
     * @param string $mes
     * @return ZgfinFeriadoNacional
     */
    public function setMes($mes)
    {
        $this->mes = $mes;

        return $this;
    }

    /**
     * Get mes
     *
     * @return string 
     */
    public function getMes()
    {
        return $this->mes;
    }

    /**
     * Set codOrganizacao
     *
     * @param \Entidades\ZgadmOrganizacao $codOrganizacao
     * @return ZgfinFeriadoNacional
     */
    public function setCodOrganizacao(\Entidades\ZgadmOrganizacao $codOrganizacao = null)
    {
        $this->codOrganizacao = $codOrganizacao;

        return $this;
    }

    /**
     * Get codOrganizacao
     *
     * @return \Entidades\ZgadmOrganizacao 
     */
    public function getCodOrganizacao()
    {
        return $this->codOrganizacao;
    }
}
