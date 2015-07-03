<?php

namespace Entidades;

use Doctrine\ORM\Mapping as ORM;

/**
 * ZgfinPessoaTelefone
 *
 * @ORM\Table(name="ZGFIN_PESSOA_TELEFONE", indexes={@ORM\Index(name="fk_ZGFIN_PESSOA_TELEFONE_1_idx", columns={"COD_PROPRIETARIO"}), @ORM\Index(name="fk_ZGFIN_PESSOA_TELEFONE_2_idx", columns={"COD_TIPO_TELEFONE"})})
 * @ORM\Entity
 */
class ZgfinPessoaTelefone
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
     * @ORM\Column(name="TELEFONE", type="string", length=11, nullable=false)
     */
    private $telefone;

    /**
     * @var \Entidades\ZgfinPessoa
     *
     * @ORM\ManyToOne(targetEntity="Entidades\ZgfinPessoa")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="COD_PROPRIETARIO", referencedColumnName="CODIGO")
     * })
     */
    private $codProprietario;

    /**
     * @var \Entidades\ZgappTelefoneTipo
     *
     * @ORM\ManyToOne(targetEntity="Entidades\ZgappTelefoneTipo")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="COD_TIPO_TELEFONE", referencedColumnName="CODIGO")
     * })
     */
    private $codTipoTelefone;


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
     * Set telefone
     *
     * @param string $telefone
     * @return ZgfinPessoaTelefone
     */
    public function setTelefone($telefone)
    {
        $this->telefone = $telefone;

        return $this;
    }

    /**
     * Get telefone
     *
     * @return string 
     */
    public function getTelefone()
    {
        return $this->telefone;
    }

    /**
     * Set codProprietario
     *
     * @param \Entidades\ZgfinPessoa $codProprietario
     * @return ZgfinPessoaTelefone
     */
    public function setCodProprietario(\Entidades\ZgfinPessoa $codProprietario = null)
    {
        $this->codProprietario = $codProprietario;

        return $this;
    }

    /**
     * Get codProprietario
     *
     * @return \Entidades\ZgfinPessoa 
     */
    public function getCodProprietario()
    {
        return $this->codProprietario;
    }

    /**
     * Set codTipoTelefone
     *
     * @param \Entidades\ZgappTelefoneTipo $codTipoTelefone
     * @return ZgfinPessoaTelefone
     */
    public function setCodTipoTelefone(\Entidades\ZgappTelefoneTipo $codTipoTelefone = null)
    {
        $this->codTipoTelefone = $codTipoTelefone;

        return $this;
    }

    /**
     * Get codTipoTelefone
     *
     * @return \Entidades\ZgappTelefoneTipo 
     */
    public function getCodTipoTelefone()
    {
        return $this->codTipoTelefone;
    }
}
