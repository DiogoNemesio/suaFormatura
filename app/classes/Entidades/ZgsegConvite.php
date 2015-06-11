<?php

namespace Entidades;

use Doctrine\ORM\Mapping as ORM;

/**
 * ZgsegConvite
 *
 * @ORM\Table(name="ZGSEG_CONVITE", indexes={@ORM\Index(name="fk_ZGSEG_CONVITE_1_idx", columns={"COD_USUARIO_ORIGEM"}), @ORM\Index(name="fk_ZGSEG_CONVITE_2_idx", columns={"COD_USUARIO_DESTINO"}), @ORM\Index(name="fk_ZGSEG_CONVITE_3_idx", columns={"COD_ORGANIZACAO_ORIGEM"}), @ORM\Index(name="fk_ZGSEG_CONVITE_4_idx", columns={"COD_ORGANIZACAO_DESTINO"}), @ORM\Index(name="fk_ZGSEG_CONVITE_5_idx", columns={"COD_USUARIO_SOLICITANTE"}), @ORM\Index(name="fk_ZGSEG_CONVITE_6_idx", columns={"COD_STATUS"})})
 * @ORM\Entity
 */
class ZgsegConvite
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
     * @ORM\Column(name="DATA", type="datetime", nullable=false)
     */
    private $data;

    /**
     * @var integer
     *
     * @ORM\Column(name="IND_UTILIZADO", type="integer", nullable=false)
     */
    private $indUtilizado;

    /**
     * @var string
     *
     * @ORM\Column(name="SENHA", type="string", length=60, nullable=false)
     */
    private $senha;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="DATA_UTILIZACAO", type="datetime", nullable=true)
     */
    private $dataUtilizacao;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="DATA_CANCELAMENTO", type="datetime", nullable=true)
     */
    private $dataCancelamento;

    /**
     * @var \Entidades\ZgsegUsuario
     *
     * @ORM\ManyToOne(targetEntity="Entidades\ZgsegUsuario")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="COD_USUARIO_ORIGEM", referencedColumnName="CODIGO")
     * })
     */
    private $codUsuarioOrigem;

    /**
     * @var \Entidades\ZgsegUsuario
     *
     * @ORM\ManyToOne(targetEntity="Entidades\ZgsegUsuario")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="COD_USUARIO_DESTINO", referencedColumnName="CODIGO")
     * })
     */
    private $codUsuarioDestino;

    /**
     * @var \Entidades\ZgadmOrganizacao
     *
     * @ORM\ManyToOne(targetEntity="Entidades\ZgadmOrganizacao")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="COD_ORGANIZACAO_ORIGEM", referencedColumnName="CODIGO")
     * })
     */
    private $codOrganizacaoOrigem;

    /**
     * @var \Entidades\ZgadmOrganizacao
     *
     * @ORM\ManyToOne(targetEntity="Entidades\ZgadmOrganizacao")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="COD_ORGANIZACAO_DESTINO", referencedColumnName="CODIGO")
     * })
     */
    private $codOrganizacaoDestino;

    /**
     * @var \Entidades\ZgsegUsuario
     *
     * @ORM\ManyToOne(targetEntity="Entidades\ZgsegUsuario")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="COD_USUARIO_SOLICITANTE", referencedColumnName="CODIGO")
     * })
     */
    private $codUsuarioSolicitante;

    /**
     * @var \Entidades\ZgsegConviteStatus
     *
     * @ORM\ManyToOne(targetEntity="Entidades\ZgsegConviteStatus")
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
     * Set data
     *
     * @param \DateTime $data
     * @return ZgsegConvite
     */
    public function setData($data)
    {
        $this->data = $data;

        return $this;
    }

    /**
     * Get data
     *
     * @return \DateTime 
     */
    public function getData()
    {
        return $this->data;
    }

    /**
     * Set indUtilizado
     *
     * @param integer $indUtilizado
     * @return ZgsegConvite
     */
    public function setIndUtilizado($indUtilizado)
    {
        $this->indUtilizado = $indUtilizado;

        return $this;
    }

    /**
     * Get indUtilizado
     *
     * @return integer 
     */
    public function getIndUtilizado()
    {
        return $this->indUtilizado;
    }

    /**
     * Set senha
     *
     * @param string $senha
     * @return ZgsegConvite
     */
    public function setSenha($senha)
    {
        $this->senha = $senha;

        return $this;
    }

    /**
     * Get senha
     *
     * @return string 
     */
    public function getSenha()
    {
        return $this->senha;
    }

    /**
     * Set dataUtilizacao
     *
     * @param \DateTime $dataUtilizacao
     * @return ZgsegConvite
     */
    public function setDataUtilizacao($dataUtilizacao)
    {
        $this->dataUtilizacao = $dataUtilizacao;

        return $this;
    }

    /**
     * Get dataUtilizacao
     *
     * @return \DateTime 
     */
    public function getDataUtilizacao()
    {
        return $this->dataUtilizacao;
    }

    /**
     * Set dataCancelamento
     *
     * @param \DateTime $dataCancelamento
     * @return ZgsegConvite
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
     * Set codUsuarioOrigem
     *
     * @param \Entidades\ZgsegUsuario $codUsuarioOrigem
     * @return ZgsegConvite
     */
    public function setCodUsuarioOrigem(\Entidades\ZgsegUsuario $codUsuarioOrigem = null)
    {
        $this->codUsuarioOrigem = $codUsuarioOrigem;

        return $this;
    }

    /**
     * Get codUsuarioOrigem
     *
     * @return \Entidades\ZgsegUsuario 
     */
    public function getCodUsuarioOrigem()
    {
        return $this->codUsuarioOrigem;
    }

    /**
     * Set codUsuarioDestino
     *
     * @param \Entidades\ZgsegUsuario $codUsuarioDestino
     * @return ZgsegConvite
     */
    public function setCodUsuarioDestino(\Entidades\ZgsegUsuario $codUsuarioDestino = null)
    {
        $this->codUsuarioDestino = $codUsuarioDestino;

        return $this;
    }

    /**
     * Get codUsuarioDestino
     *
     * @return \Entidades\ZgsegUsuario 
     */
    public function getCodUsuarioDestino()
    {
        return $this->codUsuarioDestino;
    }

    /**
     * Set codOrganizacaoOrigem
     *
     * @param \Entidades\ZgadmOrganizacao $codOrganizacaoOrigem
     * @return ZgsegConvite
     */
    public function setCodOrganizacaoOrigem(\Entidades\ZgadmOrganizacao $codOrganizacaoOrigem = null)
    {
        $this->codOrganizacaoOrigem = $codOrganizacaoOrigem;

        return $this;
    }

    /**
     * Get codOrganizacaoOrigem
     *
     * @return \Entidades\ZgadmOrganizacao 
     */
    public function getCodOrganizacaoOrigem()
    {
        return $this->codOrganizacaoOrigem;
    }

    /**
     * Set codOrganizacaoDestino
     *
     * @param \Entidades\ZgadmOrganizacao $codOrganizacaoDestino
     * @return ZgsegConvite
     */
    public function setCodOrganizacaoDestino(\Entidades\ZgadmOrganizacao $codOrganizacaoDestino = null)
    {
        $this->codOrganizacaoDestino = $codOrganizacaoDestino;

        return $this;
    }

    /**
     * Get codOrganizacaoDestino
     *
     * @return \Entidades\ZgadmOrganizacao 
     */
    public function getCodOrganizacaoDestino()
    {
        return $this->codOrganizacaoDestino;
    }

    /**
     * Set codUsuarioSolicitante
     *
     * @param \Entidades\ZgsegUsuario $codUsuarioSolicitante
     * @return ZgsegConvite
     */
    public function setCodUsuarioSolicitante(\Entidades\ZgsegUsuario $codUsuarioSolicitante = null)
    {
        $this->codUsuarioSolicitante = $codUsuarioSolicitante;

        return $this;
    }

    /**
     * Get codUsuarioSolicitante
     *
     * @return \Entidades\ZgsegUsuario 
     */
    public function getCodUsuarioSolicitante()
    {
        return $this->codUsuarioSolicitante;
    }

    /**
     * Set codStatus
     *
     * @param \Entidades\ZgsegConviteStatus $codStatus
     * @return ZgsegConvite
     */
    public function setCodStatus(\Entidades\ZgsegConviteStatus $codStatus = null)
    {
        $this->codStatus = $codStatus;

        return $this;
    }

    /**
     * Get codStatus
     *
     * @return \Entidades\ZgsegConviteStatus 
     */
    public function getCodStatus()
    {
        return $this->codStatus;
    }
}
