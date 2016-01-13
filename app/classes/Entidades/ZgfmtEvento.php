<?php

namespace Entidades;

use Doctrine\ORM\Mapping as ORM;

/**
 * ZgfmtEvento
 *
 * @ORM\Table(name="ZGFMT_EVENTO", indexes={@ORM\Index(name="fk_ZGFOR_EVENTO_1_idx", columns={"COD_TIPO_EVENTO"}), @ORM\Index(name="fk_ZGFOR_EVENTO_2_idx", columns={"COD_FORMATURA"}), @ORM\Index(name="ZGFOR_EVENTO_1_UN", columns={"COD_FORMATURA", "COD_TIPO_EVENTO"}), @ORM\Index(name="fk_ZGFMT_EVENTO_1_idx", columns={"COD_PESSOA"})})
 * @ORM\Entity
 */
class ZgfmtEvento
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
     * @var integer
     *
     * @ORM\Column(name="QTDE_CONVITE", type="integer", nullable=true)
     */
    private $qtdeConvite;

    /**
     * @var float
     *
     * @ORM\Column(name="VALOR_AVULSO", type="float", precision=10, scale=0, nullable=true)
     */
    private $valorAvulso;

    /**
     * @var \Entidades\ZgfmtEventoTipo
     *
     * @ORM\ManyToOne(targetEntity="Entidades\ZgfmtEventoTipo")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="COD_TIPO_EVENTO", referencedColumnName="CODIGO")
     * })
     */
    private $codTipoEvento;

    /**
     * @var \Entidades\ZgadmOrganizacao
     *
     * @ORM\ManyToOne(targetEntity="Entidades\ZgadmOrganizacao")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="COD_FORMATURA", referencedColumnName="CODIGO")
     * })
     */
    private $codFormatura;

    /**
     * @var \Entidades\ZgfinPessoa
     *
     * @ORM\ManyToOne(targetEntity="Entidades\ZgfinPessoa")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="COD_PESSOA", referencedColumnName="CODIGO")
     * })
     */
    private $codPessoa;


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
     * @return ZgfmtEvento
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
     * Set qtdeConvite
     *
     * @param integer $qtdeConvite
     * @return ZgfmtEvento
     */
    public function setQtdeConvite($qtdeConvite)
    {
        $this->qtdeConvite = $qtdeConvite;

        return $this;
    }

    /**
     * Get qtdeConvite
     *
     * @return integer 
     */
    public function getQtdeConvite()
    {
        return $this->qtdeConvite;
    }

    /**
     * Set valorAvulso
     *
     * @param float $valorAvulso
     * @return ZgfmtEvento
     */
    public function setValorAvulso($valorAvulso)
    {
        $this->valorAvulso = $valorAvulso;

        return $this;
    }

    /**
     * Get valorAvulso
     *
     * @return float 
     */
    public function getValorAvulso()
    {
        return $this->valorAvulso;
    }

    /**
     * Set codTipoEvento
     *
     * @param \Entidades\ZgfmtEventoTipo $codTipoEvento
     * @return ZgfmtEvento
     */
    public function setCodTipoEvento(\Entidades\ZgfmtEventoTipo $codTipoEvento = null)
    {
        $this->codTipoEvento = $codTipoEvento;

        return $this;
    }

    /**
     * Get codTipoEvento
     *
     * @return \Entidades\ZgfmtEventoTipo 
     */
    public function getCodTipoEvento()
    {
        return $this->codTipoEvento;
    }

    /**
     * Set codFormatura
     *
     * @param \Entidades\ZgadmOrganizacao $codFormatura
     * @return ZgfmtEvento
     */
    public function setCodFormatura(\Entidades\ZgadmOrganizacao $codFormatura = null)
    {
        $this->codFormatura = $codFormatura;

        return $this;
    }

    /**
     * Get codFormatura
     *
     * @return \Entidades\ZgadmOrganizacao 
     */
    public function getCodFormatura()
    {
        return $this->codFormatura;
    }

    /**
     * Set codPessoa
     *
     * @param \Entidades\ZgfinPessoa $codPessoa
     * @return ZgfmtEvento
     */
    public function setCodPessoa(\Entidades\ZgfinPessoa $codPessoa = null)
    {
        $this->codPessoa = $codPessoa;

        return $this;
    }

    /**
     * Get codPessoa
     *
     * @return \Entidades\ZgfinPessoa 
     */
    public function getCodPessoa()
    {
        return $this->codPessoa;
    }
}
