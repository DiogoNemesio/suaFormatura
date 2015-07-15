<?php

namespace Entidades;

use Doctrine\ORM\Mapping as ORM;

/**
 * ZgadmPais
 *
 * @ORM\Table(name="ZGADM_PAIS")
 * @ORM\Entity
 */
class ZgadmPais
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
     * @ORM\Column(name="SIGLA", type="string", length=3, nullable=false)
     */
    private $sigla;

    /**
     * @var integer
     *
     * @ORM\Column(name="COD_PAIS", type="integer", nullable=false)
     */
    private $codPais;

    /**
     * @var string
     *
     * @ORM\Column(name="NOME", type="string", length=100, nullable=false)
     */
    private $nome;

    /**
     * @var string
     *
     * @ORM\Column(name="TIMEZONE", type="string", length=60, nullable=true)
     */
    private $timezone;

    /**
     * @var string
     *
     * @ORM\Column(name="CALLING_CODE", type="string", length=3, nullable=true)
     */
    private $callingCode;


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
     * Set sigla
     *
     * @param string $sigla
     * @return ZgadmPais
     */
    public function setSigla($sigla)
    {
        $this->sigla = $sigla;

        return $this;
    }

    /**
     * Get sigla
     *
     * @return string 
     */
    public function getSigla()
    {
        return $this->sigla;
    }

    /**
     * Set codPais
     *
     * @param integer $codPais
     * @return ZgadmPais
     */
    public function setCodPais($codPais)
    {
        $this->codPais = $codPais;

        return $this;
    }

    /**
     * Get codPais
     *
     * @return integer 
     */
    public function getCodPais()
    {
        return $this->codPais;
    }

    /**
     * Set nome
     *
     * @param string $nome
     * @return ZgadmPais
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
     * Set timezone
     *
     * @param string $timezone
     * @return ZgadmPais
     */
    public function setTimezone($timezone)
    {
        $this->timezone = $timezone;

        return $this;
    }

    /**
     * Get timezone
     *
     * @return string 
     */
    public function getTimezone()
    {
        return $this->timezone;
    }

    /**
     * Set callingCode
     *
     * @param string $callingCode
     * @return ZgadmPais
     */
    public function setCallingCode($callingCode)
    {
        $this->callingCode = $callingCode;

        return $this;
    }

    /**
     * Get callingCode
     *
     * @return string 
     */
    public function getCallingCode()
    {
        return $this->callingCode;
    }
}
