<?php

namespace Entidades;

use Doctrine\ORM\Mapping as ORM;

/**
 * ZgfmtOrganizacaoTelefone
 *
 * @ORM\Table(name="ZGFMT_ORGANIZACAO_TELEFONE", indexes={@ORM\Index(name="fk_ZGFOR_ORGANIZACAO_TELEFONE_1_idx", columns={"COD_TIPO_TELEFONE"}), @ORM\Index(name="fk_ZGFOR_ORGANIZACAO_TELEFONE_2_idx", columns={"COD_ORGANIZACAO"})})
 * @ORM\Entity
 */
class ZgfmtOrganizacaoTelefone
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
     * @var \Entidades\ZgappTipoTelefone
     *
     * @ORM\ManyToOne(targetEntity="Entidades\ZgappTipoTelefone")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="COD_TIPO_TELEFONE", referencedColumnName="CODIGO")
     * })
     */
    private $codTipoTelefone;

    /**
     * @var \Entidades\ZgfmtOrganizacao
     *
     * @ORM\ManyToOne(targetEntity="Entidades\ZgfmtOrganizacao")
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
     * @return ZgfmtOrganizacaoTelefone
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
     * @param \Entidades\ZgappTipoTelefone $codTipoTelefone
     * @return ZgfmtOrganizacaoTelefone
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

    /**
     * Set codOrganizacao
     *
     * @param \Entidades\ZgfmtOrganizacao $codOrganizacao
     * @return ZgfmtOrganizacaoTelefone
     */
    public function setCodOrganizacao(\Entidades\ZgfmtOrganizacao $codOrganizacao = null)
    {
        $this->codOrganizacao = $codOrganizacao;

        return $this;
    }

    /**
     * Get codOrganizacao
     *
     * @return \Entidades\ZgfmtOrganizacao 
     */
    public function getCodOrganizacao()
    {
        return $this->codOrganizacao;
    }
}
