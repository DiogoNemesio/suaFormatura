<?php

namespace Entidades;

use Doctrine\ORM\Mapping as ORM;

/**
 * ZgfmtListaConvidado
 *
 * @ORM\Table(name="ZGFMT_LISTA_CONVIDADO", indexes={@ORM\Index(name="fk_ZGFMT_LISTA_CONVIDADO_1_idx", columns={"COD_GRUPO"}), @ORM\Index(name="fk_ZGFMT_LISTA_CONVIDADO_2_idx", columns={"COD_USUARIO"}), @ORM\Index(name="fk_ZGFMT_LISTA_CONVIDADO_3_idx", columns={"COD_FAIXA_ETARIA"}), @ORM\Index(name="fk_ZGFMT_LISTA_CONVIDADO_4_idx", columns={"SEXO"})})
 * @ORM\Entity
 */
class ZgfmtListaConvidado
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
     * @ORM\Column(name="NOME", type="string", length=100, nullable=false)
     */
    private $nome;

    /**
     * @var string
     *
     * @ORM\Column(name="TELEFONE", type="string", length=11, nullable=true)
     */
    private $telefone;

    /**
     * @var string
     *
     * @ORM\Column(name="EMAIL", type="string", length=100, nullable=true)
     */
    private $email;

    /**
     * @var \Entidades\ZgfmtConvidadoGrupo
     *
     * @ORM\ManyToOne(targetEntity="Entidades\ZgfmtConvidadoGrupo")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="COD_GRUPO", referencedColumnName="CODIGO")
     * })
     */
    private $codGrupo;

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
     * @var \Entidades\ZgfmtConvidadoFaixaEtaria
     *
     * @ORM\ManyToOne(targetEntity="Entidades\ZgfmtConvidadoFaixaEtaria")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="COD_FAIXA_ETARIA", referencedColumnName="CODIGO")
     * })
     */
    private $codFaixaEtaria;

    /**
     * @var \Entidades\ZgsegSexoTipo
     *
     * @ORM\ManyToOne(targetEntity="Entidades\ZgsegSexoTipo")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="SEXO", referencedColumnName="CODIGO")
     * })
     */
    private $sexo;


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
     * Set nome
     *
     * @param string $nome
     * @return ZgfmtListaConvidado
     */
    public function setNome($nome)
    {
        $this->nome = $nome;

        return $this;
    }

    /**
     * Get nome
     *
     * @return string 
     */
    public function getNome()
    {
        return $this->nome;
    }

    /**
     * Set telefone
     *
     * @param string $telefone
     * @return ZgfmtListaConvidado
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
     * Set email
     *
     * @param string $email
     * @return ZgfmtListaConvidado
     */
    public function setEmail($email)
    {
        $this->email = $email;

        return $this;
    }

    /**
     * Get email
     *
     * @return string 
     */
    public function getEmail()
    {
        return $this->email;
    }

    /**
     * Set codGrupo
     *
     * @param \Entidades\ZgfmtConvidadoGrupo $codGrupo
     * @return ZgfmtListaConvidado
     */
    public function setCodGrupo(\Entidades\ZgfmtConvidadoGrupo $codGrupo = null)
    {
        $this->codGrupo = $codGrupo;

        return $this;
    }

    /**
     * Get codGrupo
     *
     * @return \Entidades\ZgfmtConvidadoGrupo 
     */
    public function getCodGrupo()
    {
        return $this->codGrupo;
    }

    /**
     * Set codUsuario
     *
     * @param \Entidades\ZgsegUsuario $codUsuario
     * @return ZgfmtListaConvidado
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
     * Set codFaixaEtaria
     *
     * @param \Entidades\ZgfmtConvidadoFaixaEtaria $codFaixaEtaria
     * @return ZgfmtListaConvidado
     */
    public function setCodFaixaEtaria(\Entidades\ZgfmtConvidadoFaixaEtaria $codFaixaEtaria = null)
    {
        $this->codFaixaEtaria = $codFaixaEtaria;

        return $this;
    }

    /**
     * Get codFaixaEtaria
     *
     * @return \Entidades\ZgfmtConvidadoFaixaEtaria 
     */
    public function getCodFaixaEtaria()
    {
        return $this->codFaixaEtaria;
    }

    /**
     * Set sexo
     *
     * @param \Entidades\ZgsegSexoTipo $sexo
     * @return ZgfmtListaConvidado
     */
    public function setSexo(\Entidades\ZgsegSexoTipo $sexo = null)
    {
        $this->sexo = $sexo;

        return $this;
    }

    /**
     * Get sexo
     *
     * @return \Entidades\ZgsegSexoTipo 
     */
    public function getSexo()
    {
        return $this->sexo;
    }
}
