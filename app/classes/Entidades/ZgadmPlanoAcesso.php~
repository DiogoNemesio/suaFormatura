<?php

namespace Entidades;

use Doctrine\ORM\Mapping as ORM;

/**
 * ZgadmPlanoAcesso
 *
 * @ORM\Table(name="ZGADM_PLANO_ACESSO", indexes={@ORM\Index(name="fk_ZGADM_PLANO_ACESSO_1_idx", columns={"COD_PLANO"}), @ORM\Index(name="fk_ZGADM_PLANO_ACESSO_2_idx", columns={"COD_SISTEMA"})})
 * @ORM\Entity
 */
class ZgadmPlanoAcesso
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
     * @var \Entidades\ZgadmPlano
     *
     * @ORM\ManyToOne(targetEntity="Entidades\ZgadmPlano")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="COD_PLANO", referencedColumnName="CODIGO")
     * })
     */
    private $codPlano;

    /**
     * @var \Entidades\ZgadmSistema
     *
     * @ORM\ManyToOne(targetEntity="Entidades\ZgadmSistema")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="COD_SISTEMA", referencedColumnName="CODIGO")
     * })
     */
    private $codSistema;


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
     * Set codPlano
     *
     * @param \Entidades\ZgadmPlano $codPlano
     * @return ZgadmPlanoAcesso
     */
    public function setCodPlano(\Entidades\ZgadmPlano $codPlano = null)
    {
        $this->codPlano = $codPlano;

        return $this;
    }

    /**
     * Get codPlano
     *
     * @return \Entidades\ZgadmPlano 
     */
    public function getCodPlano()
    {
        return $this->codPlano;
    }

    /**
     * Set codSistema
     *
     * @param \Entidades\ZgadmSistema $codSistema
     * @return ZgadmPlanoAcesso
     */
    public function setCodSistema(\Entidades\ZgadmSistema $codSistema = null)
    {
        $this->codSistema = $codSistema;

        return $this;
    }

    /**
     * Get codSistema
     *
     * @return \Entidades\ZgadmSistema 
     */
    public function getCodSistema()
    {
        return $this->codSistema;
    }
}
