<?php

namespace Entidades;

use Doctrine\ORM\Mapping as ORM;

/**
 * ZgfmtParticipacaoEvento
 *
 * @ORM\Table(name="ZGFMT_PARTICIPACAO_EVENTO", indexes={@ORM\Index(name="fk_ZGFMT_PARTICIPACAO_EVENTO_1_idx", columns={"COD_ORGANIZACAO"}), @ORM\Index(name="fk_ZGFMT_PARTICIPACAO_EVENTO_2_idx", columns={"COD_EVENTO"}), @ORM\Index(name="fk_ZGFMT_PARTICIPACAO_EVENTO_3_idx", columns={"COD_FORMANDO"})})
 * @ORM\Entity
 */
class ZgfmtParticipacaoEvento
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
     * @var float
     *
     * @ORM\Column(name="VALOR", type="float", precision=10, scale=0, nullable=false)
     */
    private $valor;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="DATA_CADASTRO", type="datetime", nullable=false)
     */
    private $dataCadastro;

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
     * @var \Entidades\ZgfmtEvento
     *
     * @ORM\ManyToOne(targetEntity="Entidades\ZgfmtEvento")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="COD_EVENTO", referencedColumnName="CODIGO")
     * })
     */
    private $codEvento;

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
     * Set valor
     *
     * @param float $valor
     * @return ZgfmtParticipacaoEvento
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
     * Set dataCadastro
     *
     * @param \DateTime $dataCadastro
     * @return ZgfmtParticipacaoEvento
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
     * Set codOrganizacao
     *
     * @param \Entidades\ZgadmOrganizacao $codOrganizacao
     * @return ZgfmtParticipacaoEvento
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
     * Set codEvento
     *
     * @param \Entidades\ZgfmtEvento $codEvento
     * @return ZgfmtParticipacaoEvento
     */
    public function setCodEvento(\Entidades\ZgfmtEvento $codEvento = null)
    {
        $this->codEvento = $codEvento;

        return $this;
    }

    /**
     * Get codEvento
     *
     * @return \Entidades\ZgfmtEvento 
     */
    public function getCodEvento()
    {
        return $this->codEvento;
    }

    /**
     * Set codFormando
     *
     * @param \Entidades\ZgsegUsuario $codFormando
     * @return ZgfmtParticipacaoEvento
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
