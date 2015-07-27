<?php

namespace Entidades;

use Doctrine\ORM\Mapping as ORM;

/**
 * ZgfmtRifa
 *
 * @ORM\Table(name="ZGFMT_RIFA", indexes={@ORM\Index(name="fk_ZGFMT_RIFA_1_idx", columns={"COD_ORGANIZACAO"})})
 * @ORM\Entity
 */
class ZgfmtRifa
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
     * @ORM\Column(name="PREMIO", type="string", length=100, nullable=false)
     */
    private $premio;

    /**
     * @var float
     *
     * @ORM\Column(name="CUSTO", type="float", precision=10, scale=0, nullable=false)
     */
    private $custo;

    /**
     * @var integer
     *
     * @ORM\Column(name="QTDE_OBRIGATORIO", type="integer", nullable=false)
     */
    private $qtdeObrigatorio;

    /**
     * @var float
     *
     * @ORM\Column(name="VALOR_UNITARIO", type="float", precision=10, scale=0, nullable=false)
     */
    private $valorUnitario;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="DATA_CADASTRO", type="datetime", nullable=false)
     */
    private $dataCadastro;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="DATA_SORTEIO", type="datetime", nullable=false)
     */
    private $dataSorteio;

    /**
     * @var string
     *
     * @ORM\Column(name="LOCAL_SORTEIO", type="string", length=60, nullable=true)
     */
    private $localSorteio;

    /**
     * @var integer
     *
     * @ORM\Column(name="IND_SORTEIO_ELETRONICO", type="integer", nullable=false)
     */
    private $indSorteioEletronico;

    /**
     * @var integer
     *
     * @ORM\Column(name="NUMERO_VENCEDOR", type="integer", nullable=true)
     */
    private $numeroVencedor;

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
     * @return ZgfmtRifa
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
     * Set premio
     *
     * @param string $premio
     * @return ZgfmtRifa
     */
    public function setPremio($premio)
    {
        $this->premio = $premio;

        return $this;
    }

    /**
     * Get premio
     *
     * @return string 
     */
    public function getPremio()
    {
        return $this->premio;
    }

    /**
     * Set custo
     *
     * @param float $custo
     * @return ZgfmtRifa
     */
    public function setCusto($custo)
    {
        $this->custo = $custo;

        return $this;
    }

    /**
     * Get custo
     *
     * @return float 
     */
    public function getCusto()
    {
        return $this->custo;
    }

    /**
     * Set qtdeObrigatorio
     *
     * @param integer $qtdeObrigatorio
     * @return ZgfmtRifa
     */
    public function setQtdeObrigatorio($qtdeObrigatorio)
    {
        $this->qtdeObrigatorio = $qtdeObrigatorio;

        return $this;
    }

    /**
     * Get qtdeObrigatorio
     *
     * @return integer 
     */
    public function getQtdeObrigatorio()
    {
        return $this->qtdeObrigatorio;
    }

    /**
     * Set valorUnitario
     *
     * @param float $valorUnitario
     * @return ZgfmtRifa
     */
    public function setValorUnitario($valorUnitario)
    {
        $this->valorUnitario = $valorUnitario;

        return $this;
    }

    /**
     * Get valorUnitario
     *
     * @return float 
     */
    public function getValorUnitario()
    {
        return $this->valorUnitario;
    }

    /**
     * Set dataCadastro
     *
     * @param \DateTime $dataCadastro
     * @return ZgfmtRifa
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
     * Set dataSorteio
     *
     * @param \DateTime $dataSorteio
     * @return ZgfmtRifa
     */
    public function setDataSorteio($dataSorteio)
    {
        $this->dataSorteio = $dataSorteio;

        return $this;
    }

    /**
     * Get dataSorteio
     *
     * @return \DateTime 
     */
    public function getDataSorteio()
    {
        return $this->dataSorteio;
    }

    /**
     * Set localSorteio
     *
     * @param string $localSorteio
     * @return ZgfmtRifa
     */
    public function setLocalSorteio($localSorteio)
    {
        $this->localSorteio = $localSorteio;

        return $this;
    }

    /**
     * Get localSorteio
     *
     * @return string 
     */
    public function getLocalSorteio()
    {
        return $this->localSorteio;
    }

    /**
     * Set indSorteioEletronico
     *
     * @param integer $indSorteioEletronico
     * @return ZgfmtRifa
     */
    public function setIndSorteioEletronico($indSorteioEletronico)
    {
        $this->indSorteioEletronico = $indSorteioEletronico;

        return $this;
    }

    /**
     * Get indSorteioEletronico
     *
     * @return integer 
     */
    public function getIndSorteioEletronico()
    {
        return $this->indSorteioEletronico;
    }

    /**
     * Set numeroVencedor
     *
     * @param integer $numeroVencedor
     * @return ZgfmtRifa
     */
    public function setNumeroVencedor($numeroVencedor)
    {
        $this->numeroVencedor = $numeroVencedor;

        return $this;
    }

    /**
     * Get numeroVencedor
     *
     * @return integer 
     */
    public function getNumeroVencedor()
    {
        return $this->numeroVencedor;
    }

    /**
     * Set codOrganizacao
     *
     * @param \Entidades\ZgadmOrganizacao $codOrganizacao
     * @return ZgfmtRifa
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
}
