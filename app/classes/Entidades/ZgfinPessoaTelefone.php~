<?php

namespace Entidades;

use Doctrine\ORM\Mapping as ORM;

/**
 * ZgfinPessoaTelefone
 *
 * @ORM\Table(name="ZGFIN_PESSOA_TELEFONE", indexes={@ORM\Index(name="fk_ZGFIN_PESSOA_TELEFONE_1_idx", columns={"COD_PESSOA"}), @ORM\Index(name="fk_ZGFIN_PESSOA_TELEFONE_2_idx", columns={"COD_TIPO_TELEFONE"})})
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
     *   @ORM\JoinColumn(name="COD_PESSOA", referencedColumnName="CODIGO")
     * })
     */
    private $codPessoa;

    /**
     * @var \Entidades\ZgappTipoTelefone
     *
     * @ORM\ManyToOne(targetEntity="Entidades\ZgappTipoTelefone")
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
     * Set codPessoa
     *
     * @param \Entidades\ZgfinPessoa $codPessoa
     * @return ZgfinPessoaTelefone
     */
    public function setCodPessoa(\Entidades\ZgfinPessoa $codPessoa = null)
    {
        $this->codPessoa = $codPessoa;

        return $this;
    }

    /**
     * Get codPessoa
     *
     * @return \Entidades\ZgfinPessoa 
     */
    public function getCodPessoa()
    {
        return $this->codPessoa;
    }

    /**
     * Set codTipoTelefone
     *
     * @param \Entidades\ZgappTipoTelefone $codTipoTelefone
     * @return ZgfinPessoaTelefone
     */
    public function setCodTipoTelefone(\Entidades\ZgappTipoTelefone $codTipoTelefone = null)
    {
        $this->codTipoTelefone = $codTipoTelefone;

        return $this;
    }

    /**
     * Get codTipoTelefone
     *
     * @return \Entidades\ZgappTipoTelefone 
     */
    public function getCodTipoTelefone()
    {
        return $this->codTipoTelefone;
    }
}
