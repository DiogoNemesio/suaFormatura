<?php

namespace Entidades;

use Doctrine\ORM\Mapping as ORM;

/**
 * ZgadmOrganizacaoTelefone
 *
 * @ORM\Table(name="ZGADM_ORGANIZACAO_TELEFONE", indexes={@ORM\Index(name="fk_ZGFOR_ORGANIZACAO_TELEFONE_1_idx", columns={"COD_TIPO_TELEFONE"}), @ORM\Index(name="fk_ZGFOR_ORGANIZACAO_TELEFONE_2_idx", columns={"COD_ORGANIZACAO"})})
 * @ORM\Entity
 */
class ZgadmOrganizacaoTelefone
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
     * @var \Entidades\ZgappTelefoneTipo
     *
     * @ORM\ManyToOne(targetEntity="Entidades\ZgappTelefoneTipo")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="COD_TIPO_TELEFONE", referencedColumnName="CODIGO")
     * })
     */
    private $codTipoTelefone;

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
     * Set telefone
     *
     * @param string $telefone
     * @return ZgadmOrganizacaoTelefone
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
     * Set codTipoTelefone
     *
     * @param \Entidades\ZgappTelefoneTipo $codTipoTelefone
     * @return ZgadmOrganizacaoTelefone
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

    /**
     * Set codOrganizacao
     *
     * @param \Entidades\ZgadmOrganizacao $codOrganizacao
     * @return ZgadmOrganizacaoTelefone
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
