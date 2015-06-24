<?php

namespace Entidades;

use Doctrine\ORM\Mapping as ORM;

/**
 * ZgappFilaImportacao
 *
 * @ORM\Table(name="ZGAPP_FILA_IMPORTACAO", indexes={@ORM\Index(name="fk_ZGAPP_FILA_IMPORTACAO_1_idx", columns={"COD_ORGANIZACAO"}), @ORM\Index(name="fk_ZGAPP_FILA_IMPORTACAO_2_idx", columns={"COD_MODULO"}), @ORM\Index(name="fk_ZGAPP_FILA_IMPORTACAO_3_idx", columns={"COD_USUARIO"}), @ORM\Index(name="fk_ZGAPP_FILA_IMPORTACAO_4_idx", columns={"COD_TIPO_ARQUIVO"}), @ORM\Index(name="fk_ZGAPP_FILA_IMPORTACAO_5_idx", columns={"COD_STATUS"}), @ORM\Index(name="fk_ZGAPP_FILA_IMPORTACAO_6_idx", columns={"COD_ATIVIDADE"})})
 * @ORM\Entity
 */
class ZgappFilaImportacao
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
     * @ORM\Column(name="DATA_IMPORTACAO", type="datetime", nullable=false)
     */
    private $dataImportacao;

    /**
     * @var string
     *
     * @ORM\Column(name="NOME", type="string", length=60, nullable=false)
     */
    private $nome;

    /**
     * @var integer
     *
     * @ORM\Column(name="BYTES", type="integer", nullable=true)
     */
    private $bytes;

    /**
     * @var string
     *
     * @ORM\Column(name="ARQUIVO", type="string", length=200, nullable=true)
     */
    private $arquivo;

    /**
     * @var integer
     *
     * @ORM\Column(name="NUM_LINHAS", type="integer", nullable=true)
     */
    private $numLinhas;

    /**
     * @var integer
     *
     * @ORM\Column(name="LINHA_ATUAL", type="integer", nullable=true)
     */
    private $linhaAtual;

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
     * @var \Entidades\ZgappModulo
     *
     * @ORM\ManyToOne(targetEntity="Entidades\ZgappModulo")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="COD_MODULO", referencedColumnName="CODIGO")
     * })
     */
    private $codModulo;

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
     * @var \Entidades\ZgappImportacaoArquivoTipo
     *
     * @ORM\ManyToOne(targetEntity="Entidades\ZgappImportacaoArquivoTipo")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="COD_TIPO_ARQUIVO", referencedColumnName="CODIGO")
     * })
     */
    private $codTipoArquivo;

    /**
     * @var \Entidades\ZgappImportacaoStatusTipo
     *
     * @ORM\ManyToOne(targetEntity="Entidades\ZgappImportacaoStatusTipo")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="COD_STATUS", referencedColumnName="CODIGO")
     * })
     */
    private $codStatus;

    /**
     * @var \Entidades\ZgutlAtividade
     *
     * @ORM\ManyToOne(targetEntity="Entidades\ZgutlAtividade")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="COD_ATIVIDADE", referencedColumnName="CODIGO")
     * })
     */
    private $codAtividade;


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
     * Set dataImportacao
     *
     * @param \DateTime $dataImportacao
     * @return ZgappFilaImportacao
     */
    public function setDataImportacao($dataImportacao)
    {
        $this->dataImportacao = $dataImportacao;

        return $this;
    }

    /**
     * Get dataImportacao
     *
     * @return \DateTime 
     */
    public function getDataImportacao()
    {
        return $this->dataImportacao;
    }

    /**
     * Set nome
     *
     * @param string $nome
     * @return ZgappFilaImportacao
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
     * Set bytes
     *
     * @param integer $bytes
     * @return ZgappFilaImportacao
     */
    public function setBytes($bytes)
    {
        $this->bytes = $bytes;

        return $this;
    }

    /**
     * Get bytes
     *
     * @return integer 
     */
    public function getBytes()
    {
        return $this->bytes;
    }

    /**
     * Set arquivo
     *
     * @param string $arquivo
     * @return ZgappFilaImportacao
     */
    public function setArquivo($arquivo)
    {
        $this->arquivo = $arquivo;

        return $this;
    }

    /**
     * Get arquivo
     *
     * @return string 
     */
    public function getArquivo()
    {
        return $this->arquivo;
    }

    /**
     * Set numLinhas
     *
     * @param integer $numLinhas
     * @return ZgappFilaImportacao
     */
    public function setNumLinhas($numLinhas)
    {
        $this->numLinhas = $numLinhas;

        return $this;
    }

    /**
     * Get numLinhas
     *
     * @return integer 
     */
    public function getNumLinhas()
    {
        return $this->numLinhas;
    }

    /**
     * Set linhaAtual
     *
     * @param integer $linhaAtual
     * @return ZgappFilaImportacao
     */
    public function setLinhaAtual($linhaAtual)
    {
        $this->linhaAtual = $linhaAtual;

        return $this;
    }

    /**
     * Get linhaAtual
     *
     * @return integer 
     */
    public function getLinhaAtual()
    {
        return $this->linhaAtual;
    }

    /**
     * Set codOrganizacao
     *
     * @param \Entidades\ZgadmOrganizacao $codOrganizacao
     * @return ZgappFilaImportacao
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
     * Set codModulo
     *
     * @param \Entidades\ZgappModulo $codModulo
     * @return ZgappFilaImportacao
     */
    public function setCodModulo(\Entidades\ZgappModulo $codModulo = null)
    {
        $this->codModulo = $codModulo;

        return $this;
    }

    /**
     * Get codModulo
     *
     * @return \Entidades\ZgappModulo 
     */
    public function getCodModulo()
    {
        return $this->codModulo;
    }

    /**
     * Set codUsuario
     *
     * @param \Entidades\ZgsegUsuario $codUsuario
     * @return ZgappFilaImportacao
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
     * Set codTipoArquivo
     *
     * @param \Entidades\ZgappImportacaoArquivoTipo $codTipoArquivo
     * @return ZgappFilaImportacao
     */
    public function setCodTipoArquivo(\Entidades\ZgappImportacaoArquivoTipo $codTipoArquivo = null)
    {
        $this->codTipoArquivo = $codTipoArquivo;

        return $this;
    }

    /**
     * Get codTipoArquivo
     *
     * @return \Entidades\ZgappImportacaoArquivoTipo 
     */
    public function getCodTipoArquivo()
    {
        return $this->codTipoArquivo;
    }

    /**
     * Set codStatus
     *
     * @param \Entidades\ZgappImportacaoStatusTipo $codStatus
     * @return ZgappFilaImportacao
     */
    public function setCodStatus(\Entidades\ZgappImportacaoStatusTipo $codStatus = null)
    {
        $this->codStatus = $codStatus;

        return $this;
    }

    /**
     * Get codStatus
     *
     * @return \Entidades\ZgappImportacaoStatusTipo 
     */
    public function getCodStatus()
    {
        return $this->codStatus;
    }

    /**
     * Set codAtividade
     *
     * @param \Entidades\ZgutlAtividade $codAtividade
     * @return ZgappFilaImportacao
     */
    public function setCodAtividade(\Entidades\ZgutlAtividade $codAtividade = null)
    {
        $this->codAtividade = $codAtividade;

        return $this;
    }

    /**
     * Get codAtividade
     *
     * @return \Entidades\ZgutlAtividade 
     */
    public function getCodAtividade()
    {
        return $this->codAtividade;
    }
}
