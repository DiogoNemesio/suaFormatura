<?php

namespace Entidades;

use Doctrine\ORM\Mapping as ORM;

/**
 * ZgadmPlanoValor
 *
 * @ORM\Table(name="ZGADM_PLANO_VALOR", uniqueConstraints={@ORM\UniqueConstraint(name="ZGADM_PLANO_VALOR_UK01", columns={"COD_PLANO", "DATA_BASE"})}, indexes={@ORM\Index(name="fk_ZGADM_PLANO_VALOR_2_idx", columns={"COD_TIPO_LICENCA"}), @ORM\Index(name="IDX_A4AA6093AC27E156", columns={"COD_PLANO"})})
 * @ORM\Entity
 */
class ZgadmPlanoValor
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
     * @var float
     *
     * @ORM\Column(name="VALOR", type="float", precision=10, scale=0, nullable=false)
     */
    private $valor;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="DATA_BASE", type="date", nullable=false)
     */
    private $dataBase;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="DATA_CADASTRO", type="datetime", nullable=false)
     */
    private $dataCadastro;

    /**
     * @var \Entidades\ZgadmPlano
     *
     * @ORM\ManyToOne(targetEntity="Entidades\ZgadmPlano")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="COD_PLANO", referencedColumnName="CODIGO")
     * })
     */
    private $codPlano;

    /**
     * @var \Entidades\ZgadmPlanoLicencaTipo
     *
     * @ORM\ManyToOne(targetEntity="Entidades\ZgadmPlanoLicencaTipo")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="COD_TIPO_LICENCA", referencedColumnName="CODIGO")
     * })
     */
    private $codTipoLicenca;


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
     * @param float $valor
     * @return ZgadmPlanoValor
     */
    public function setValor($valor)
    {
        $this->valor = $valor;

        return $this;
    }

    /**
     * Get valor
     *
     * @return float 
     */
    public function getValor()
    {
        return $this->valor;
    }

    /**
     * Set dataBase
     *
     * @param \DateTime $dataBase
     * @return ZgadmPlanoValor
     */
    public function setDataBase($dataBase)
    {
        $this->dataBase = $dataBase;

        return $this;
    }

    /**
     * Get dataBase
     *
     * @return \DateTime 
     */
    public function getDataBase()
    {
        return $this->dataBase;
    }

    /**
     * Set dataCadastro
     *
     * @param \DateTime $dataCadastro
     * @return ZgadmPlanoValor
     */
    public function setDataCadastro($dataCadastro)
    {
        $this->dataCadastro = $dataCadastro;

        return $this;
    }

    /**
     * Get dataCadastro
     *
     * @return \DateTime 
     */
    public function getDataCadastro()
    {
        return $this->dataCadastro;
    }

    /**
     * Set codPlano
     *
     * @param \Entidades\ZgadmPlano $codPlano
     * @return ZgadmPlanoValor
     */
    public function setCodPlano(\Entidades\ZgadmPlano $codPlano = null)
    {
        $this->codPlano = $codPlano;

        return $this;
    }

    /**
     * Get codPlano
     *
     * @return \Entidades\ZgadmPlano 
     */
    public function getCodPlano()
    {
        return $this->codPlano;
    }

    /**
     * Set codTipoLicenca
     *
     * @param \Entidades\ZgadmPlanoLicencaTipo $codTipoLicenca
     * @return ZgadmPlanoValor
     */
    public function setCodTipoLicenca(\Entidades\ZgadmPlanoLicencaTipo $codTipoLicenca = null)
    {
        $this->codTipoLicenca = $codTipoLicenca;

        return $this;
    }

    /**
     * Get codTipoLicenca
     *
     * @return \Entidades\ZgadmPlanoLicencaTipo 
     */
    public function getCodTipoLicenca()
    {
        return $this->codTipoLicenca;
    }
}
