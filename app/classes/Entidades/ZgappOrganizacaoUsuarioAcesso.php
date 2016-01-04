<?php

namespace Entidades;

use Doctrine\ORM\Mapping as ORM;

/**
 * ZgappOrganizacaoUsuarioAcesso
 *
 * @ORM\Table(name="ZGAPP_ORGANIZACAO_USUARIO_ACESSO", indexes={@ORM\Index(name="fk_ZGAPP_ORGANIZACAO_USUARIO_ACESSO_1_idx", columns={"COD_ORGANIZACAO"}), @ORM\Index(name="fk_ZGAPP_ORGANIZACAO_USUARIO_ACESSO_2_idx", columns={"COD_USUARIO"})})
 * @ORM\Entity
 */
class ZgappOrganizacaoUsuarioAcesso
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
     * @var integer
     *
     * @ORM\Column(name="NUM_ACESSOS", type="integer", nullable=true)
     */
    private $numAcessos;

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
     * Set numAcessos
     *
     * @param integer $numAcessos
     * @return ZgappOrganizacaoUsuarioAcesso
     */
    public function setNumAcessos($numAcessos)
    {
        $this->numAcessos = $numAcessos;

        return $this;
    }

    /**
     * Get numAcessos
     *
     * @return integer 
     */
    public function getNumAcessos()
    {
        return $this->numAcessos;
    }

    /**
     * Set codOrganizacao
     *
     * @param \Entidades\ZgadmOrganizacao $codOrganizacao
     * @return ZgappOrganizacaoUsuarioAcesso
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
     * @return ZgappOrganizacaoUsuarioAcesso
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
