<?php

namespace Entidades;

use Doctrine\ORM\Mapping as ORM;

/**
 * ZgfmtRifaNumero
 *
 * @ORM\Table(name="ZGFMT_RIFA_NUMERO", uniqueConstraints={@ORM\UniqueConstraint(name="ZGFMT_RIFA_NUMERO_UK01", columns={"COD_RIFA", "NUMERO"})}, indexes={@ORM\Index(name="fk_ZGFMT_RIFA_NUMERO_2_idx", columns={"COD_FORMANDO"}), @ORM\Index(name="fk_ZGFMT_RIFA_NUMERO_3_idx", columns={"COD_VENDA"}), @ORM\Index(name="IDX_90F282C9E74EE774", columns={"COD_RIFA"})})
 * @ORM\Entity
 */
class ZgfmtRifaNumero
{
    /**
     * @var integer
     *
     * @ORM\Column(name="CODIGO", type="bigint", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $codigo;

    /**
     * @var integer
     *
     * @ORM\Column(name="NUMERO", type="integer", nullable=false)
     */
    private $numero;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="DATA", type="datetime", nullable=true)
     */
    private $data;

    /**
     * @var string
     *
     * @ORM\Column(name="NOME", type="string", length=100, nullable=true)
     */
    private $nome;

    /**
     * @var string
     *
     * @ORM\Column(name="EMAIL", type="string", length=200, nullable=true)
     */
    private $email;

    /**
     * @var string
     *
     * @ORM\Column(name="TELEFONE", type="string", length=11, nullable=true)
     */
    private $telefone;

    /**
     * @var \Entidades\ZgfmtRifaVendaSequencial
     *
     * @ORM\ManyToOne(targetEntity="Entidades\ZgfmtRifaVendaSequencial")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="COD_VENDA", referencedColumnName="CODIGO")
     * })
     */
    private $codVenda;

    /**
     * @var \Entidades\ZgfmtRifa
     *
     * @ORM\ManyToOne(targetEntity="Entidades\ZgfmtRifa")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="COD_RIFA", referencedColumnName="CODIGO")
     * })
     */
    private $codRifa;

    /**
     * @var \Entidades\ZgsegUsuario
     *
     * @ORM\ManyToOne(targetEntity="Entidades\ZgsegUsuario")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="COD_FORMANDO", referencedColumnName="CODIGO")
     * })
     */
    private $codFormando;


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
     * Set numero
     *
     * @param integer $numero
     * @return ZgfmtRifaNumero
     */
    public function setNumero($numero)
    {
        $this->numero = $numero;

        return $this;
    }

    /**
     * Get numero
     *
     * @return integer 
     */
    public function getNumero()
    {
        return $this->numero;
    }

    /**
     * Set data
     *
     * @param \DateTime $data
     * @return ZgfmtRifaNumero
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
     * Set nome
     *
     * @param string $nome
     * @return ZgfmtRifaNumero
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
     * Set email
     *
     * @param string $email
     * @return ZgfmtRifaNumero
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
     * Set telefone
     *
     * @param string $telefone
     * @return ZgfmtRifaNumero
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
     * Set codVenda
     *
     * @param \Entidades\ZgfmtRifaVendaSequencial $codVenda
     * @return ZgfmtRifaNumero
     */
    public function setCodVenda(\Entidades\ZgfmtRifaVendaSequencial $codVenda = null)
    {
        $this->codVenda = $codVenda;

        return $this;
    }

    /**
     * Get codVenda
     *
     * @return \Entidades\ZgfmtRifaVendaSequencial 
     */
    public function getCodVenda()
    {
        return $this->codVenda;
    }

    /**
     * Set codRifa
     *
     * @param \Entidades\ZgfmtRifa $codRifa
     * @return ZgfmtRifaNumero
     */
    public function setCodRifa(\Entidades\ZgfmtRifa $codRifa = null)
    {
        $this->codRifa = $codRifa;

        return $this;
    }

    /**
     * Get codRifa
     *
     * @return \Entidades\ZgfmtRifa 
     */
    public function getCodRifa()
    {
        return $this->codRifa;
    }

    /**
     * Set codFormando
     *
     * @param \Entidades\ZgsegUsuario $codFormando
     * @return ZgfmtRifaNumero
     */
    public function setCodFormando(\Entidades\ZgsegUsuario $codFormando = null)
    {
        $this->codFormando = $codFormando;

        return $this;
    }

    /**
     * Get codFormando
     *
     * @return \Entidades\ZgsegUsuario 
     */
    public function getCodFormando()
    {
        return $this->codFormando;
    }
}
