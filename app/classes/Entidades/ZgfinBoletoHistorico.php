<?php

namespace Entidades;

use Doctrine\ORM\Mapping as ORM;

/**
 * ZgfinBoletoHistorico
 *
 * @ORM\Table(name="ZGFIN_BOLETO_HISTORICO", indexes={@ORM\Index(name="fk_ZGFIN_BOLETO_HISTORICO_1_idx", columns={"COD_USUARIO"}), @ORM\Index(name="fk_ZGFIN_BOLETO_HISTORICO_2_idx", columns={"COD_CONTA"})})
 * @ORM\Entity
 */
class ZgfinBoletoHistorico
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
     * @var string
     *
     * @ORM\Column(name="LINHA_DIGITAVEL", type="string", length=100, nullable=true)
     */
    private $linhaDigitavel;

    /**
     * @var float
     *
     * @ORM\Column(name="VALOR", type="float", precision=10, scale=0, nullable=true)
     */
    private $valor;

    /**
     * @var float
     *
     * @ORM\Column(name="JUROS", type="float", precision=10, scale=0, nullable=true)
     */
    private $juros;

    /**
     * @var float
     *
     * @ORM\Column(name="MORA", type="float", precision=10, scale=0, nullable=true)
     */
    private $mora;

    /**
     * @var float
     *
     * @ORM\Column(name="DESCONTO", type="float", precision=10, scale=0, nullable=true)
     */
    private $desconto;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="VENCIMENTO", type="date", nullable=true)
     */
    private $vencimento;

    /**
     * @var string
     *
     * @ORM\Column(name="MIDIA", type="string", length=10, nullable=true)
     */
    private $midia;

    /**
     * @var string
     *
     * @ORM\Column(name="EMAIL", type="string", length=800, nullable=true)
     */
    private $email;

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
     * @var \Entidades\ZgfinContaReceber
     *
     * @ORM\ManyToOne(targetEntity="Entidades\ZgfinContaReceber")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="COD_CONTA", referencedColumnName="CODIGO")
     * })
     */
    private $codConta;


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
     * @return ZgfinBoletoHistorico
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
     * Set linhaDigitavel
     *
     * @param string $linhaDigitavel
     * @return ZgfinBoletoHistorico
     */
    public function setLinhaDigitavel($linhaDigitavel)
    {
        $this->linhaDigitavel = $linhaDigitavel;

        return $this;
    }

    /**
     * Get linhaDigitavel
     *
     * @return string 
     */
    public function getLinhaDigitavel()
    {
        return $this->linhaDigitavel;
    }

    /**
     * Set valor
     *
     * @param float $valor
     * @return ZgfinBoletoHistorico
     */
    public function setValor($valor)
    {
        $this->valor = $valor;

        return $this;
    }

    /**
     * Get valor
     *
     * @return float 
     */
    public function getValor()
    {
        return $this->valor;
    }

    /**
     * Set juros
     *
     * @param float $juros
     * @return ZgfinBoletoHistorico
     */
    public function setJuros($juros)
    {
        $this->juros = $juros;

        return $this;
    }

    /**
     * Get juros
     *
     * @return float 
     */
    public function getJuros()
    {
        return $this->juros;
    }

    /**
     * Set mora
     *
     * @param float $mora
     * @return ZgfinBoletoHistorico
     */
    public function setMora($mora)
    {
        $this->mora = $mora;

        return $this;
    }

    /**
     * Get mora
     *
     * @return float 
     */
    public function getMora()
    {
        return $this->mora;
    }

    /**
     * Set desconto
     *
     * @param float $desconto
     * @return ZgfinBoletoHistorico
     */
    public function setDesconto($desconto)
    {
        $this->desconto = $desconto;

        return $this;
    }

    /**
     * Get desconto
     *
     * @return float 
     */
    public function getDesconto()
    {
        return $this->desconto;
    }

    /**
     * Set vencimento
     *
     * @param \DateTime $vencimento
     * @return ZgfinBoletoHistorico
     */
    public function setVencimento($vencimento)
    {
        $this->vencimento = $vencimento;

        return $this;
    }

    /**
     * Get vencimento
     *
     * @return \DateTime 
     */
    public function getVencimento()
    {
        return $this->vencimento;
    }

    /**
     * Set midia
     *
     * @param string $midia
     * @return ZgfinBoletoHistorico
     */
    public function setMidia($midia)
    {
        $this->midia = $midia;

        return $this;
    }

    /**
     * Get midia
     *
     * @return string 
     */
    public function getMidia()
    {
        return $this->midia;
    }

    /**
     * Set email
     *
     * @param string $email
     * @return ZgfinBoletoHistorico
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
     * Set codUsuario
     *
     * @param \Entidades\ZgsegUsuario $codUsuario
     * @return ZgfinBoletoHistorico
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
     * Set codConta
     *
     * @param \Entidades\ZgfinContaReceber $codConta
     * @return ZgfinBoletoHistorico
     */
    public function setCodConta(\Entidades\ZgfinContaReceber $codConta = null)
    {
        $this->codConta = $codConta;

        return $this;
    }

    /**
     * Get codConta
     *
     * @return \Entidades\ZgfinContaReceber 
     */
    public function getCodConta()
    {
        return $this->codConta;
    }
}
