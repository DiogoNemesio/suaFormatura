<?php

namespace Entidades;

use Doctrine\ORM\Mapping as ORM;

/**
 * ZgfmtOrcamento
 *
 * @ORM\Table(name="ZGFMT_ORCAMENTO", indexes={@ORM\Index(name="fk_ZGFMT_ORCAMENTO_1_idx", columns={"COD_ORGANIZACAO"}), @ORM\Index(name="fk_ZGFMT_ORCAMENTO_2_idx", columns={"COD_PLANO_VERSAO"})})
 * @ORM\Entity
 */
class ZgfmtOrcamento
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
     * @var integer
     *
     * @ORM\Column(name="VERSAO", type="integer", nullable=false)
     */
    private $versao;

    /**
     * @var integer
     *
     * @ORM\Column(name="QTDE_FORMANDOS", type="integer", nullable=false)
     */
    private $qtdeFormandos;

    /**
     * @var integer
     *
     * @ORM\Column(name="QTDE_CONVIDADOS", type="integer", nullable=false)
     */
    private $qtdeConvidados;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="DATA_CONCLUSAO", type="date", nullable=false)
     */
    private $dataConclusao;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="DATA_CADASTRO", type="datetime", nullable=false)
     */
    private $dataCadastro;

    /**
     * @var integer
     *
     * @ORM\Column(name="IND_ACEITE", type="integer", nullable=true)
     */
    private $indAceite;

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
     * @var \Entidades\ZgfmtPlanoOrcamentario
     *
     * @ORM\ManyToOne(targetEntity="Entidades\ZgfmtPlanoOrcamentario")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="COD_PLANO_VERSAO", referencedColumnName="CODIGO")
     * })
     */
    private $codPlanoVersao;


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
     * Set versao
     *
     * @param integer $versao
     * @return ZgfmtOrcamento
     */
    public function setVersao($versao)
    {
        $this->versao = $versao;

        return $this;
    }

    /**
     * Get versao
     *
     * @return integer 
     */
    public function getVersao()
    {
        return $this->versao;
    }

    /**
     * Set qtdeFormandos
     *
     * @param integer $qtdeFormandos
     * @return ZgfmtOrcamento
     */
    public function setQtdeFormandos($qtdeFormandos)
    {
        $this->qtdeFormandos = $qtdeFormandos;

        return $this;
    }

    /**
     * Get qtdeFormandos
     *
     * @return integer 
     */
    public function getQtdeFormandos()
    {
        return $this->qtdeFormandos;
    }

    /**
     * Set qtdeConvidados
     *
     * @param integer $qtdeConvidados
     * @return ZgfmtOrcamento
     */
    public function setQtdeConvidados($qtdeConvidados)
    {
        $this->qtdeConvidados = $qtdeConvidados;

        return $this;
    }

    /**
     * Get qtdeConvidados
     *
     * @return integer 
     */
    public function getQtdeConvidados()
    {
        return $this->qtdeConvidados;
    }

    /**
     * Set dataConclusao
     *
     * @param \DateTime $dataConclusao
     * @return ZgfmtOrcamento
     */
    public function setDataConclusao($dataConclusao)
    {
        $this->dataConclusao = $dataConclusao;

        return $this;
    }

    /**
     * Get dataConclusao
     *
     * @return \DateTime 
     */
    public function getDataConclusao()
    {
        return $this->dataConclusao;
    }

    /**
     * Set dataCadastro
     *
     * @param \DateTime $dataCadastro
     * @return ZgfmtOrcamento
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
     * Set indAceite
     *
     * @param integer $indAceite
     * @return ZgfmtOrcamento
     */
    public function setIndAceite($indAceite)
    {
        $this->indAceite = $indAceite;

        return $this;
    }

    /**
     * Get indAceite
     *
     * @return integer 
     */
    public function getIndAceite()
    {
        return $this->indAceite;
    }

    /**
     * Set codOrganizacao
     *
     * @param \Entidades\ZgadmOrganizacao $codOrganizacao
     * @return ZgfmtOrcamento
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
     * Set codPlanoVersao
     *
     * @param \Entidades\ZgfmtPlanoOrcamentario $codPlanoVersao
     * @return ZgfmtOrcamento
     */
    public function setCodPlanoVersao(\Entidades\ZgfmtPlanoOrcamentario $codPlanoVersao = null)
    {
        $this->codPlanoVersao = $codPlanoVersao;

        return $this;
    }

    /**
     * Get codPlanoVersao
     *
     * @return \Entidades\ZgfmtPlanoOrcamentario 
     */
    public function getCodPlanoVersao()
    {
        return $this->codPlanoVersao;
    }
}