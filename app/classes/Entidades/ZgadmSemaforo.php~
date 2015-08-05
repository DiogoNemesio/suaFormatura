<?php

namespace Entidades;

use Doctrine\ORM\Mapping as ORM;

/**
 * ZgadmSemaforo
 *
 * @ORM\Table(name="ZGADM_SEMAFORO", uniqueConstraints={@ORM\UniqueConstraint(name="ZGADM_SEMAFORO_UK01", columns={"COD_ORGANIZACAO", "PARAMETRO"})}, indexes={@ORM\Index(name="fk_ZGADM_SEMAFORO_1_idx", columns={"COD_ORGANIZACAO"})})
 * @ORM\Entity
 */
class ZgadmSemaforo
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
     * @ORM\Column(name="PARAMETRO", type="string", length=30, nullable=false)
     */
    private $parametro;

    /**
     * @var integer
     *
     * @ORM\Column(name="VALOR", type="integer", nullable=false)
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
     * Get codigo
     *
     * @return integer 
     */
    public function getCodigo()
    {
        return $this->codigo;
    }

    /**
     * Set parametro
     *
     * @param string $parametro
     * @return ZgadmSemaforo
     */
    public function setParametro($parametro)
    {
        $this->parametro = $parametro;

        return $this;
    }

    /**
     * Get parametro
     *
     * @return string 
     */
    public function getParametro()
    {
        return $this->parametro;
    }

    /**
     * Set valor
     *
     * @param integer $valor
     * @return ZgadmSemaforo
     */
    public function setValor($valor)
    {
        $this->valor = $valor;

        return $this;
    }

    /**
     * Get valor
     *
     * @return integer 
     */
    public function getValor()
    {
        return $this->valor;
    }

    /**
     * Set codOrganizacao
     *
     * @param \Entidades\ZgadmOrganizacao $codOrganizacao
     * @return ZgadmSemaforo
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
