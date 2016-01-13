<?php

namespace Entidades;

use Doctrine\ORM\Mapping as ORM;

/**
 * ZgsegUsuarioOrganizacao
 *
 * @ORM\Table(name="ZGSEG_USUARIO_ORGANIZACAO", uniqueConstraints={@ORM\UniqueConstraint(name="ZGSEG_USUARIO_ORGANIZACAO_1_UNI", columns={"COD_USUARIO", "COD_ORGANIZACAO"})}, indexes={@ORM\Index(name="USUARIO_EMPRESA_IX01", columns={"COD_USUARIO"}), @ORM\Index(name="fk_ZG_USUARIO_ORG_1_idx", columns={"COD_PERFIL"}), @ORM\Index(name="fk_ZGSEG_USUARIO_ORGANIZACAO_1_idx", columns={"COD_ORGANIZACAO"}), @ORM\Index(name="fk_ZGSEG_USUARIO_ORGANIZACAO_2_idx", columns={"COD_STATUS"})})
 * @ORM\Entity
 */
class ZgsegUsuarioOrganizacao
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
     * @ORM\Column(name="DATA_CADASTRO", type="datetime", nullable=true)
     */
    private $dataCadastro;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="DATA_CANCELAMENTO", type="datetime", nullable=true)
     */
    private $dataCancelamento;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="DATA_BLOQUEIO", type="datetime", nullable=true)
     */
    private $dataBloqueio;

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
     * @var \Entidades\ZgsegPerfil
     *
     * @ORM\ManyToOne(targetEntity="Entidades\ZgsegPerfil")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="COD_PERFIL", referencedColumnName="CODIGO")
     * })
     */
    private $codPerfil;

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
     * @var \Entidades\ZgsegUsuarioOrganizacaoStatus
     *
     * @ORM\ManyToOne(targetEntity="Entidades\ZgsegUsuarioOrganizacaoStatus")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="COD_STATUS", referencedColumnName="CODIGO")
     * })
     */
    private $codStatus;


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
     * Set dataCadastro
     *
     * @param \DateTime $dataCadastro
     * @return ZgsegUsuarioOrganizacao
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
     * Set dataCancelamento
     *
     * @param \DateTime $dataCancelamento
     * @return ZgsegUsuarioOrganizacao
     */
    public function setDataCancelamento($dataCancelamento)
    {
        $this->dataCancelamento = $dataCancelamento;

        return $this;
    }

    /**
     * Get dataCancelamento
     *
     * @return \DateTime 
     */
    public function getDataCancelamento()
    {
        return $this->dataCancelamento;
    }

    /**
     * Set dataBloqueio
     *
     * @param \DateTime $dataBloqueio
     * @return ZgsegUsuarioOrganizacao
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
     * Set codUsuario
     *
     * @param \Entidades\ZgsegUsuario $codUsuario
     * @return ZgsegUsuarioOrganizacao
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

    /**
     * Set codPerfil
     *
     * @param \Entidades\ZgsegPerfil $codPerfil
     * @return ZgsegUsuarioOrganizacao
     */
    public function setCodPerfil(\Entidades\ZgsegPerfil $codPerfil = null)
    {
        $this->codPerfil = $codPerfil;

        return $this;
    }

    /**
     * Get codPerfil
     *
     * @return \Entidades\ZgsegPerfil 
     */
    public function getCodPerfil()
    {
        return $this->codPerfil;
    }

    /**
     * Set codOrganizacao
     *
     * @param \Entidades\ZgadmOrganizacao $codOrganizacao
     * @return ZgsegUsuarioOrganizacao
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
     * @param \Entidades\ZgsegUsuarioOrganizacaoStatus $codStatus
     * @return ZgsegUsuarioOrganizacao
     */
    public function setCodStatus(\Entidades\ZgsegUsuarioOrganizacaoStatus $codStatus = null)
    {
        $this->codStatus = $codStatus;

        return $this;
    }

    /**
     * Get codStatus
     *
     * @return \Entidades\ZgsegUsuarioOrganizacaoStatus 
     */
    public function getCodStatus()
    {
        return $this->codStatus;
    }
}
