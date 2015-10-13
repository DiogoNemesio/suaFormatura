<?php

namespace Entidades;

use Doctrine\ORM\Mapping as ORM;

/**
 * ZgfinConta
 *
 * @ORM\Table(name="ZGFIN_CONTA", uniqueConstraints={@ORM\UniqueConstraint(name="ZGFIN_CONTA_UK01", columns={"COD_ORGANIZACAO", "COD_AGENCIA", "CCORRENTE", "CCORRENTE_DV"})}, indexes={@ORM\Index(name="fk_ZGFIN_CONTA_1_idx", columns={"COD_ORGANIZACAO"}), @ORM\Index(name="fk_ZGFIN_CONTA_2_idx", columns={"COD_TIPO"}), @ORM\Index(name="fk_ZGFIN_CONTA_3_idx", columns={"COD_AGENCIA"}), @ORM\Index(name="fk_ZGFIN_CONTA_4_idx", columns={"COD_CARTEIRA"})})
 * @ORM\Entity
 */
class ZgfinConta
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
     * @ORM\Column(name="NOME", type="string", length=60, nullable=false)
     */
    private $nome;

    /**
     * @var float
     *
     * @ORM\Column(name="SALDO_INICIAL", type="float", precision=10, scale=0, nullable=false)
     */
    private $saldoInicial;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="DATA_INICIAL", type="date", nullable=true)
     */
    private $dataInicial;

    /**
     * @var integer
     *
     * @ORM\Column(name="IND_ATIVA", type="integer", nullable=false)
     */
    private $indAtiva;

    /**
     * @var string
     *
     * @ORM\Column(name="CCORRENTE", type="string", length=20, nullable=true)
     */
    private $ccorrente;

    /**
     * @var string
     *
     * @ORM\Column(name="CCORRENTE_DV", type="string", length=1, nullable=true)
     */
    private $ccorrenteDv;

    /**
     * @var float
     *
     * @ORM\Column(name="VALOR_MORA", type="float", precision=10, scale=0, nullable=true)
     */
    private $valorMora;

    /**
     * @var float
     *
     * @ORM\Column(name="VALOR_JUROS", type="float", precision=10, scale=0, nullable=true)
     */
    private $valorJuros;

    /**
     * @var float
     *
     * @ORM\Column(name="PCT_MORA", type="float", precision=10, scale=0, nullable=true)
     */
    private $pctMora;

    /**
     * @var float
     *
     * @ORM\Column(name="PCT_JUROS", type="float", precision=10, scale=0, nullable=true)
     */
    private $pctJuros;

    /**
     * @var string
     *
     * @ORM\Column(name="INSTRUCAO", type="string", length=60, nullable=true)
     */
    private $instrucao;

    /**
     * @var integer
     *
     * @ORM\Column(name="ULTIMO_NOSSO_NUMERO", type="integer", nullable=true)
     */
    private $ultimoNossoNumero;

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
     * @var \Entidades\ZgfinContaTipo
     *
     * @ORM\ManyToOne(targetEntity="Entidades\ZgfinContaTipo")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="COD_TIPO", referencedColumnName="CODIGO")
     * })
     */
    private $codTipo;

    /**
     * @var \Entidades\ZgfinAgencia
     *
     * @ORM\ManyToOne(targetEntity="Entidades\ZgfinAgencia")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="COD_AGENCIA", referencedColumnName="CODIGO")
     * })
     */
    private $codAgencia;

    /**
     * @var \Entidades\ZgfinCarteira
     *
     * @ORM\ManyToOne(targetEntity="Entidades\ZgfinCarteira")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="COD_CARTEIRA", referencedColumnName="CODIGO")
     * })
     */
    private $codCarteira;


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
     * @return ZgfinConta
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
     * Set saldoInicial
     *
     * @param float $saldoInicial
     * @return ZgfinConta
     */
    public function setSaldoInicial($saldoInicial)
    {
        $this->saldoInicial = $saldoInicial;

        return $this;
    }

    /**
     * Get saldoInicial
     *
     * @return float 
     */
    public function getSaldoInicial()
    {
        return $this->saldoInicial;
    }

    /**
     * Set dataInicial
     *
     * @param \DateTime $dataInicial
     * @return ZgfinConta
     */
    public function setDataInicial($dataInicial)
    {
        $this->dataInicial = $dataInicial;

        return $this;
    }

    /**
     * Get dataInicial
     *
     * @return \DateTime 
     */
    public function getDataInicial()
    {
        return $this->dataInicial;
    }

    /**
     * Set indAtiva
     *
     * @param integer $indAtiva
     * @return ZgfinConta
     */
    public function setIndAtiva($indAtiva)
    {
        $this->indAtiva = $indAtiva;

        return $this;
    }

    /**
     * Get indAtiva
     *
     * @return integer 
     */
    public function getIndAtiva()
    {
        return $this->indAtiva;
    }

    /**
     * Set ccorrente
     *
     * @param string $ccorrente
     * @return ZgfinConta
     */
    public function setCcorrente($ccorrente)
    {
        $this->ccorrente = $ccorrente;

        return $this;
    }

    /**
     * Get ccorrente
     *
     * @return string 
     */
    public function getCcorrente()
    {
        return $this->ccorrente;
    }

    /**
     * Set ccorrenteDv
     *
     * @param string $ccorrenteDv
     * @return ZgfinConta
     */
    public function setCcorrenteDv($ccorrenteDv)
    {
        $this->ccorrenteDv = $ccorrenteDv;

        return $this;
    }

    /**
     * Get ccorrenteDv
     *
     * @return string 
     */
    public function getCcorrenteDv()
    {
        return $this->ccorrenteDv;
    }

    /**
     * Set valorMora
     *
     * @param float $valorMora
     * @return ZgfinConta
     */
    public function setValorMora($valorMora)
    {
        $this->valorMora = $valorMora;

        return $this;
    }

    /**
     * Get valorMora
     *
     * @return float 
     */
    public function getValorMora()
    {
        return $this->valorMora;
    }

    /**
     * Set valorJuros
     *
     * @param float $valorJuros
     * @return ZgfinConta
     */
    public function setValorJuros($valorJuros)
    {
        $this->valorJuros = $valorJuros;

        return $this;
    }

    /**
     * Get valorJuros
     *
     * @return float 
     */
    public function getValorJuros()
    {
        return $this->valorJuros;
    }

    /**
     * Set pctMora
     *
     * @param float $pctMora
     * @return ZgfinConta
     */
    public function setPctMora($pctMora)
    {
        $this->pctMora = $pctMora;

        return $this;
    }

    /**
     * Get pctMora
     *
     * @return float 
     */
    public function getPctMora()
    {
        return $this->pctMora;
    }

    /**
     * Set pctJuros
     *
     * @param float $pctJuros
     * @return ZgfinConta
     */
    public function setPctJuros($pctJuros)
    {
        $this->pctJuros = $pctJuros;

        return $this;
    }

    /**
     * Get pctJuros
     *
     * @return float 
     */
    public function getPctJuros()
    {
        return $this->pctJuros;
    }

    /**
     * Set instrucao
     *
     * @param string $instrucao
     * @return ZgfinConta
     */
    public function setInstrucao($instrucao)
    {
        $this->instrucao = $instrucao;

        return $this;
    }

    /**
     * Get instrucao
     *
     * @return string 
     */
    public function getInstrucao()
    {
        return $this->instrucao;
    }

    /**
     * Set ultimoNossoNumero
     *
     * @param integer $ultimoNossoNumero
     * @return ZgfinConta
     */
    public function setUltimoNossoNumero($ultimoNossoNumero)
    {
        $this->ultimoNossoNumero = $ultimoNossoNumero;

        return $this;
    }

    /**
     * Get ultimoNossoNumero
     *
     * @return integer 
     */
    public function getUltimoNossoNumero()
    {
        return $this->ultimoNossoNumero;
    }

    /**
     * Set codOrganizacao
     *
     * @param \Entidades\ZgadmOrganizacao $codOrganizacao
     * @return ZgfinConta
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
     * Set codTipo
     *
     * @param \Entidades\ZgfinContaTipo $codTipo
     * @return ZgfinConta
     */
    public function setCodTipo(\Entidades\ZgfinContaTipo $codTipo = null)
    {
        $this->codTipo = $codTipo;

        return $this;
    }

    /**
     * Get codTipo
     *
     * @return \Entidades\ZgfinContaTipo 
     */
    public function getCodTipo()
    {
        return $this->codTipo;
    }

    /**
     * Set codAgencia
     *
     * @param \Entidades\ZgfinAgencia $codAgencia
     * @return ZgfinConta
     */
    public function setCodAgencia(\Entidades\ZgfinAgencia $codAgencia = null)
    {
        $this->codAgencia = $codAgencia;

        return $this;
    }

    /**
     * Get codAgencia
     *
     * @return \Entidades\ZgfinAgencia 
     */
    public function getCodAgencia()
    {
        return $this->codAgencia;
    }

    /**
     * Set codCarteira
     *
     * @param \Entidades\ZgfinCarteira $codCarteira
     * @return ZgfinConta
     */
    public function setCodCarteira(\Entidades\ZgfinCarteira $codCarteira = null)
    {
        $this->codCarteira = $codCarteira;

        return $this;
    }

    /**
     * Get codCarteira
     *
     * @return \Entidades\ZgfinCarteira 
     */
    public function getCodCarteira()
    {
        return $this->codCarteira;
    }
}
