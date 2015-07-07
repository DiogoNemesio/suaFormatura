<?php

namespace Entidades;

use Doctrine\ORM\Mapping as ORM;

/**
 * ZgsegUsuarioTelefone
 *
 * @ORM\Table(name="ZGSEG_USUARIO_TELEFONE", indexes={@ORM\Index(name="fk_ZGSEG_USUARIO_TELEFONE_2_idx", columns={"COD_TIPO_TELEFONE"}), @ORM\Index(name="fk_ZGSEG_USUARIO_TELEFONE_1_idx", columns={"COD_PROPRIETARIO"})})
 * @ORM\Entity
 */
class ZgsegUsuarioTelefone
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
     * @var integer
     *
     * @ORM\Column(name="IND_TEM_WA", type="integer", nullable=true)
     */
    private $indTemWa;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="DATA_ULT_VERIFICACAO", type="datetime", nullable=true)
     */
    private $dataUltVerificacao;

    /**
     * @var \Entidades\ZgsegUsuario
     *
     * @ORM\ManyToOne(targetEntity="Entidades\ZgsegUsuario")
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
     * @return ZgsegUsuarioTelefone
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
     * Set indTemWa
     *
     * @param integer $indTemWa
     * @return ZgsegUsuarioTelefone
     */
    public function setIndTemWa($indTemWa)
    {
        $this->indTemWa = $indTemWa;

        return $this;
    }

    /**
     * Get indTemWa
     *
     * @return integer 
     */
    public function getIndTemWa()
    {
        return $this->indTemWa;
    }

    /**
     * Set dataUltVerificacao
     *
     * @param \DateTime $dataUltVerificacao
     * @return ZgsegUsuarioTelefone
     */
    public function setDataUltVerificacao($dataUltVerificacao)
    {
        $this->dataUltVerificacao = $dataUltVerificacao;

        return $this;
    }

    /**
     * Get dataUltVerificacao
     *
     * @return \DateTime 
     */
    public function getDataUltVerificacao()
    {
        return $this->dataUltVerificacao;
    }

    /**
     * Set codProprietario
     *
     * @param \Entidades\ZgsegUsuario $codProprietario
     * @return ZgsegUsuarioTelefone
     */
    public function setCodProprietario(\Entidades\ZgsegUsuario $codProprietario = null)
    {
        $this->codProprietario = $codProprietario;

        return $this;
    }

    /**
     * Get codProprietario
     *
     * @return \Entidades\ZgsegUsuario 
     */
    public function getCodProprietario()
    {
        return $this->codProprietario;
    }

    /**
     * Set codTipoTelefone
     *
     * @param \Entidades\ZgappTelefoneTipo $codTipoTelefone
     * @return ZgsegUsuarioTelefone
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
