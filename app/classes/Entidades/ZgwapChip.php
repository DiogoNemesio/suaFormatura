<?php

namespace Entidades;

use Doctrine\ORM\Mapping as ORM;

/**
 * ZgwapChip
 *
 * @ORM\Table(name="ZGWAP_CHIP", indexes={@ORM\Index(name="fk_ZGWAP_CHIP_1_idx", columns={"COD_ORGANIZACAO"}), @ORM\Index(name="fk_ZGWAP_CHIP_2_idx", columns={"COD_STATUS"}), @ORM\Index(name="fk_ZGWAP_CHIP_3_idx", columns={"COD_PAIS"})})
 * @ORM\Entity
 */
class ZgwapChip
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
     * @ORM\Column(name="DDD", type="string", length=2, nullable=false)
     */
    private $ddd;

    /**
     * @var string
     *
     * @ORM\Column(name="NUMERO", type="string", length=9, nullable=false)
     */
    private $numero;

    /**
     * @var string
     *
     * @ORM\Column(name="SENHA", type="string", length=60, nullable=true)
     */
    private $senha;

    /**
     * @var string
     *
     * @ORM\Column(name="CODE", type="string", length=10, nullable=true)
     */
    private $code;

    /**
     * @var string
     *
     * @ORM\Column(name="IDENTIFICACAO", type="string", length=60, nullable=true)
     */
    private $identificacao;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="DATA_CADASTRO", type="datetime", nullable=true)
     */
    private $dataCadastro;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="DATA_REGISTRO", type="datetime", nullable=true)
     */
    private $dataRegistro;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="DATA_ULTIMA_SINCRONIZACAO", type="datetime", nullable=true)
     */
    private $dataUltimaSincronizacao;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="DATA_BLOQUEIO", type="datetime", nullable=true)
     */
    private $dataBloqueio;

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
     * @var \Entidades\ZgwapChipStatus
     *
     * @ORM\ManyToOne(targetEntity="Entidades\ZgwapChipStatus")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="COD_STATUS", referencedColumnName="CODIGO")
     * })
     */
    private $codStatus;

    /**
     * @var \Entidades\ZgadmPais
     *
     * @ORM\ManyToOne(targetEntity="Entidades\ZgadmPais")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="COD_PAIS", referencedColumnName="CODIGO")
     * })
     */
    private $codPais;


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
     * Set ddd
     *
     * @param string $ddd
     * @return ZgwapChip
     */
    public function setDdd($ddd)
    {
        $this->ddd = $ddd;

        return $this;
    }

    /**
     * Get ddd
     *
     * @return string 
     */
    public function getDdd()
    {
        return $this->ddd;
    }

    /**
     * Set numero
     *
     * @param string $numero
     * @return ZgwapChip
     */
    public function setNumero($numero)
    {
        $this->numero = $numero;

        return $this;
    }

    /**
     * Get numero
     *
     * @return string 
     */
    public function getNumero()
    {
        return $this->numero;
    }

    /**
     * Set senha
     *
     * @param string $senha
     * @return ZgwapChip
     */
    public function setSenha($senha)
    {
        $this->senha = $senha;

        return $this;
    }

    /**
     * Get senha
     *
     * @return string 
     */
    public function getSenha()
    {
        return $this->senha;
    }

    /**
     * Set code
     *
     * @param string $code
     * @return ZgwapChip
     */
    public function setCode($code)
    {
        $this->code = $code;

        return $this;
    }

    /**
     * Get code
     *
     * @return string 
     */
    public function getCode()
    {
        return $this->code;
    }

    /**
     * Set identificacao
     *
     * @param string $identificacao
     * @return ZgwapChip
     */
    public function setIdentificacao($identificacao)
    {
        $this->identificacao = $identificacao;

        return $this;
    }

    /**
     * Get identificacao
     *
     * @return string 
     */
    public function getIdentificacao()
    {
        return $this->identificacao;
    }

    /**
     * Set dataCadastro
     *
     * @param \DateTime $dataCadastro
     * @return ZgwapChip
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
     * Set dataRegistro
     *
     * @param \DateTime $dataRegistro
     * @return ZgwapChip
     */
    public function setDataRegistro($dataRegistro)
    {
        $this->dataRegistro = $dataRegistro;

        return $this;
    }

    /**
     * Get dataRegistro
     *
     * @return \DateTime 
     */
    public function getDataRegistro()
    {
        return $this->dataRegistro;
    }

    /**
     * Set dataUltimaSincronizacao
     *
     * @param \DateTime $dataUltimaSincronizacao
     * @return ZgwapChip
     */
    public function setDataUltimaSincronizacao($dataUltimaSincronizacao)
    {
        $this->dataUltimaSincronizacao = $dataUltimaSincronizacao;

        return $this;
    }

    /**
     * Get dataUltimaSincronizacao
     *
     * @return \DateTime 
     */
    public function getDataUltimaSincronizacao()
    {
        return $this->dataUltimaSincronizacao;
    }

    /**
     * Set dataBloqueio
     *
     * @param \DateTime $dataBloqueio
     * @return ZgwapChip
     */
    public function setDataBloqueio($dataBloqueio)
    {
        $this->dataBloqueio = $dataBloqueio;

        return $this;
    }

    /**
     * Get dataBloqueio
     *
     * @return \DateTime 
     */
    public function getDataBloqueio()
    {
        return $this->dataBloqueio;
    }

    /**
     * Set codOrganizacao
     *
     * @param \Entidades\ZgadmOrganizacao $codOrganizacao
     * @return ZgwapChip
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
     * Set codStatus
     *
     * @param \Entidades\ZgwapChipStatus $codStatus
     * @return ZgwapChip
     */
    public function setCodStatus(\Entidades\ZgwapChipStatus $codStatus = null)
    {
        $this->codStatus = $codStatus;

        return $this;
    }

    /**
     * Get codStatus
     *
     * @return \Entidades\ZgwapChipStatus 
     */
    public function getCodStatus()
    {
        return $this->codStatus;
    }

    /**
     * Set codPais
     *
     * @param \Entidades\ZgadmPais $codPais
     * @return ZgwapChip
     */
    public function setCodPais(\Entidades\ZgadmPais $codPais = null)
    {
        $this->codPais = $codPais;

        return $this;
    }

    /**
     * Get codPais
     *
     * @return \Entidades\ZgadmPais 
     */
    public function getCodPais()
    {
        return $this->codPais;
    }
}
