<?php

namespace Entidades;

use Doctrine\ORM\Mapping as ORM;

/**
 * ZgfmtEventoParticipacao
 *
 * @ORM\Table(name="ZGFMT_EVENTO_PARTICIPACAO", uniqueConstraints={@ORM\UniqueConstraint(name="ZGFMT_EVENTO_PARTICIPACAO_uk01", columns={"COD_ORGANIZACAO", "COD_EVENTO", "COD_FORMANDO"})}, indexes={@ORM\Index(name="fk_ZGFMT_EVENTO_PARTICIPACAO_1_idx", columns={"COD_ORGANIZACAO"}), @ORM\Index(name="fk_ZGFMT_EVENTO_PARTICIPACAO_2_idx", columns={"COD_EVENTO"}), @ORM\Index(name="fk_ZGFMT_EVENTO_PARTICIPACAO_3_idx", columns={"COD_FORMANDO"})})
 * @ORM\Entity
 */
class ZgfmtEventoParticipacao
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
     * Set dataCadastro
     *
     * @param \DateTime $dataCadastro
     * @return ZgfmtEventoParticipacao
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
     * @return ZgfmtEventoParticipacao
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
     * @return ZgfmtEventoParticipacao
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
     * @return ZgfmtEventoParticipacao
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
