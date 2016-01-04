<?php

namespace Entidades;

use Doctrine\ORM\Mapping as ORM;

/**
 * ZgappOrganizacaoHistAcesso
 *
 * @ORM\Table(name="ZGAPP_ORGANIZACAO_HIST_ACESSO", indexes={@ORM\Index(name="fk_ZGAPP_ORGANIZACAO_HIST_ACESSO_1_idx", columns={"COD_ORGANIZACAO"}), @ORM\Index(name="fk_ZGAPP_ORGANIZACAO_HIST_ACESSO_2_idx", columns={"COD_USUARIO"})})
 * @ORM\Entity
 */
class ZgappOrganizacaoHistAcesso
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
     * @var \DateTime
     *
     * @ORM\Column(name="DATA_ACESSO", type="datetime", nullable=false)
     */
    private $dataAcesso;

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
     * @var \Entidades\ZgsegUsuario
     *
     * @ORM\ManyToOne(targetEntity="Entidades\ZgsegUsuario")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="COD_USUARIO", referencedColumnName="CODIGO")
     * })
     */
    private $codUsuario;


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
     * Set dataAcesso
     *
     * @param \DateTime $dataAcesso
     * @return ZgappOrganizacaoHistAcesso
     */
    public function setDataAcesso($dataAcesso)
    {
        $this->dataAcesso = $dataAcesso;

        return $this;
    }

    /**
     * Get dataAcesso
     *
     * @return \DateTime 
     */
    public function getDataAcesso()
    {
        return $this->dataAcesso;
    }

    /**
     * Set codOrganizacao
     *
     * @param \Entidades\ZgadmOrganizacao $codOrganizacao
     * @return ZgappOrganizacaoHistAcesso
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
     * Set codUsuario
     *
     * @param \Entidades\ZgsegUsuario $codUsuario
     * @return ZgappOrganizacaoHistAcesso
     */
    public function setCodUsuario(\Entidades\ZgsegUsuario $codUsuario = null)
    {
        $this->codUsuario = $codUsuario;

        return $this;
    }

    /**
     * Get codUsuario
     *
     * @return \Entidades\ZgsegUsuario 
     */
    public function getCodUsuario()
    {
        return $this->codUsuario;
    }
}
