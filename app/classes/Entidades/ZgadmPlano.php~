<?php

namespace Entidades;

use Doctrine\ORM\Mapping as ORM;

/**
 * ZgadmPlano
 *
 * @ORM\Table(name="ZGADM_PLANO", indexes={@ORM\Index(name="fk_ZGADM_PLANO_1_idx", columns={"COD_TIPO_LICENCA"})})
 * @ORM\Entity
 */
class ZgadmPlano
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
     * @ORM\Column(name="NOME", type="string", length=60, nullable=false)
     */
    private $nome;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="DATA_CADASTRO", type="datetime", nullable=false)
     */
    private $dataCadastro;

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
     * Set nome
     *
     * @param string $nome
     * @return ZgadmPlano
     */
    public function setNome($nome)
    {
        $this->nome = $nome;

        return $this;
    }

    /**
     * Get nome
     *
     * @return string 
     */
    public function getNome()
    {
        return $this->nome;
    }

    /**
     * Set dataCadastro
     *
     * @param \DateTime $dataCadastro
     * @return ZgadmPlano
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
     * Set codTipoLicenca
     *
     * @param \Entidades\ZgadmPlanoLicencaTipo $codTipoLicenca
     * @return ZgadmPlano
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
