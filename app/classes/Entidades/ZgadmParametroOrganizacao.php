<?php

namespace Entidades;

use Doctrine\ORM\Mapping as ORM;

/**
 * ZgadmParametroOrganizacao
 *
 * @ORM\Table(name="ZGADM_PARAMETRO_ORGANIZACAO", indexes={@ORM\Index(name="fk_ZGADM_PARAMETRO_ORGANIZACAO_1_idx", columns={"COD_ORGANIZACAO"}), @ORM\Index(name="fk_ZGADM_PARAMETRO_ORGANIZACAO_2_idx", columns={"COD_PARAMETRO"})})
 * @ORM\Entity
 */
class ZgadmParametroOrganizacao
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
     * @ORM\Column(name="VALOR", type="string", length=400, nullable=true)
     */
    private $valor;

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
     * @var \Entidades\ZgappParametro
     *
     * @ORM\ManyToOne(targetEntity="Entidades\ZgappParametro")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="COD_PARAMETRO", referencedColumnName="CODIGO")
     * })
     */
    private $codParametro;


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
     * Set valor
     *
     * @param string $valor
     * @return ZgadmParametroOrganizacao
     */
    public function setValor($valor)
    {
        $this->valor = $valor;

        return $this;
    }

    /**
     * Get valor
     *
     * @return string 
     */
    public function getValor()
    {
        return $this->valor;
    }

    /**
     * Set codOrganizacao
     *
     * @param \Entidades\ZgadmOrganizacao $codOrganizacao
     * @return ZgadmParametroOrganizacao
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

    /**
     * Set codParametro
     *
     * @param \Entidades\ZgappParametro $codParametro
     * @return ZgadmParametroOrganizacao
     */
    public function setCodParametro(\Entidades\ZgappParametro $codParametro = null)
    {
        $this->codParametro = $codParametro;

        return $this;
    }

    /**
     * Get codParametro
     *
     * @return \Entidades\ZgappParametro 
     */
    public function getCodParametro()
    {
        return $this->codParametro;
    }
}