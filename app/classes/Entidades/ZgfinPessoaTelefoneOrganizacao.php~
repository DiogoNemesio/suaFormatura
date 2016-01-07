<?php

namespace Entidades;

use Doctrine\ORM\Mapping as ORM;

/**
 * ZgfinPessoaTelefoneOrganizacao
 *
 * @ORM\Table(name="ZGFIN_PESSOA_TELEFONE_ORGANIZACAO", indexes={@ORM\Index(name="fk_ZGFIN_PESSOA_TELEFONE_ORGANIZACAO_1_idx", columns={"COD_PESSOA"}), @ORM\Index(name="fk_ZGFIN_PESSOA_TELEFONE_ORGANIZACAO_2_idx", columns={"COD_ORGANIZACAO"}), @ORM\Index(name="fk_ZGFIN_PESSOA_TELEFONE_ORGANIZACAO_3_idx", columns={"COD_TIPO_TELEFONE"})})
 * @ORM\Entity
 */
class ZgfinPessoaTelefoneOrganizacao
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
     * @var \Entidades\ZgadmOrganizacao
     *
     * @ORM\ManyToOne(targetEntity="Entidades\ZgadmOrganizacao")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="COD_ORGANIZACAO", referencedColumnName="CODIGO")
     * })
     */
    private $codOrganizacao;

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
     * @return ZgfinPessoaTelefoneOrganizacao
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
     * @return ZgfinPessoaTelefoneOrganizacao
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
     * Set codOrganizacao
     *
     * @param \Entidades\ZgadmOrganizacao $codOrganizacao
     * @return ZgfinPessoaTelefoneOrganizacao
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
     * Set codTipoTelefone
     *
     * @param \Entidades\ZgappTelefoneTipo $codTipoTelefone
     * @return ZgfinPessoaTelefoneOrganizacao
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
