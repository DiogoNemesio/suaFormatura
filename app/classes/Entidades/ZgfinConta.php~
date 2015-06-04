<?php

namespace Entidades;

use Doctrine\ORM\Mapping as ORM;

/**
 * ZgfinConta
 *
 * @ORM\Table(name="ZGFIN_CONTA", indexes={@ORM\Index(name="fk_ZGFIN_CONTA_1_idx", columns={"COD_ORGANIZACAO"}), @ORM\Index(name="fk_ZGFIN_CONTA_2_idx", columns={"COD_TIPO"}), @ORM\Index(name="fk_ZGFIN_CONTA_3_idx", columns={"COD_AGENCIA"})})
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
     * @var string
     *
     * @ORM\Column(name="SALDO_INICIAL", type="decimal", precision=15, scale=2, nullable=false)
     */
    private $saldoInicial;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="DATA_INICIAL", type="date", nullable=false)
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
     * @param string $saldoInicial
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
     * @return string 
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
}
