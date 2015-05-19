<?php

namespace Entidades;

use Doctrine\ORM\Mapping as ORM;

/**
 * ZgforOrganizacaoTelefone
 *
 * @ORM\Table(name="ZGFOR_ORGANIZACAO_TELEFONE", indexes={@ORM\Index(name="fk_ZGFOR_ORGANIZACAO_TELEFONE_1_idx", columns={"COD_TIPO_TELEFONE"}), @ORM\Index(name="fk_ZGFOR_ORGANIZACAO_TELEFONE_2_idx", columns={"COD_ORGANIZACAO"})})
 * @ORM\Entity
 */
class ZgforOrganizacaoTelefone
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
     * @var \Entidades\ZgforOrganizacao
     *
     * @ORM\ManyToOne(targetEntity="Entidades\ZgforOrganizacao")
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
     * @return ZgforOrganizacaoTelefone
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
     * @return ZgforOrganizacaoTelefone
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
     * @param \Entidades\ZgforOrganizacao $codOrganizacao
     * @return ZgforOrganizacaoTelefone
     */
    public function setCodOrganizacao(\Entidades\ZgforOrganizacao $codOrganizacao = null)
    {
        $this->codOrganizacao = $codOrganizacao;

        return $this;
    }

    /**
     * Get codOrganizacao
     *
     * @return \Entidades\ZgforOrganizacao 
     */
    public function getCodOrganizacao()
    {
        return $this->codOrganizacao;
    }
}
