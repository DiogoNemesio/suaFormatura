<?php

namespace Entidades;

use Doctrine\ORM\Mapping as ORM;

/**
 * ZgappNotificacaoOrganizacao
 *
 * @ORM\Table(name="ZGAPP_NOTIFICACAO_ORGANIZACAO", indexes={@ORM\Index(name="fk_ZGAPP_NOTIFICACAO_ORGANIZACAO_1_idx", columns={"COD_NOTIFICACAO"}), @ORM\Index(name="fk_ZGAPP_NOTIFICACAO_ORGANIZACAO_2_idx", columns={"COD_ORGANIZACAO"})})
 * @ORM\Entity
 */
class ZgappNotificacaoOrganizacao
{
    /**
     * @var integer
     *
     * @ORM\Column(name="CODIGO", type="bigint", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $codigo;

    /**
     * @var \Entidades\ZgappNotificacao
     *
     * @ORM\ManyToOne(targetEntity="Entidades\ZgappNotificacao")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="COD_NOTIFICACAO", referencedColumnName="CODIGO")
     * })
     */
    private $codNotificacao;

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
     * Set codNotificacao
     *
     * @param \Entidades\ZgappNotificacao $codNotificacao
     * @return ZgappNotificacaoOrganizacao
     */
    public function setCodNotificacao(\Entidades\ZgappNotificacao $codNotificacao = null)
    {
        $this->codNotificacao = $codNotificacao;

        return $this;
    }

    /**
     * Get codNotificacao
     *
     * @return \Entidades\ZgappNotificacao 
     */
    public function getCodNotificacao()
    {
        return $this->codNotificacao;
    }

    /**
     * Set codOrganizacao
     *
     * @param \Entidades\ZgadmOrganizacao $codOrganizacao
     * @return ZgappNotificacaoOrganizacao
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
