<?php

namespace Entidades;

use Doctrine\ORM\Mapping as ORM;

/**
 * ZgadmOrganizacaoLogo
 *
 * @ORM\Table(name="ZGADM_ORGANIZACAO_LOGO", uniqueConstraints={@ORM\UniqueConstraint(name="COD_ORGANIZACAO_UNIQUE", columns={"COD_ORGANIZACAO"})})
 * @ORM\Entity
 */
class ZgadmOrganizacaoLogo
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
     * @ORM\Column(name="LOGOMARCA", type="blob", nullable=true)
     */
    private $logomarca;

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
     * Set logomarca
     *
     * @param string $logomarca
     * @return ZgadmOrganizacaoLogo
     */
    public function setLogomarca($logomarca)
    {
        $this->logomarca = $logomarca;

        return $this;
    }

    /**
     * Get logomarca
     *
     * @return string 
     */
    public function getLogomarca()
    {
        return $this->logomarca;
    }

    /**
     * Set codOrganizacao
     *
     * @param \Entidades\ZgadmOrganizacao $codOrganizacao
     * @return ZgadmOrganizacaoLogo
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
