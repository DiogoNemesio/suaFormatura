<?php

namespace Entidades;

use Doctrine\ORM\Mapping as ORM;

/**
 * ZgsegSexoTipoTr
 *
 * @ORM\Table(name="ZGSEG_SEXO_TIPO_TR", indexes={@ORM\Index(name="fk_ZGSEG_SEXO_TIPO_TR_1_idx", columns={"COD_SEXO"}), @ORM\Index(name="fk_ZGSEG_SEXO_TIPO_TR_2_idx", columns={"COD_LANG"})})
 * @ORM\Entity
 */
class ZgsegSexoTipoTr
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
     * @ORM\Column(name="DESCRICAO", type="string", length=60, nullable=false)
     */
    private $descricao;

    /**
     * @var \Entidades\ZgsegSexoTipo
     *
     * @ORM\ManyToOne(targetEntity="Entidades\ZgsegSexoTipo")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="COD_SEXO", referencedColumnName="CODIGO")
     * })
     */
    private $codSexo;

    /**
     * @var \Entidades\ZgappLang
     *
     * @ORM\ManyToOne(targetEntity="Entidades\ZgappLang")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="COD_LANG", referencedColumnName="CODIGO")
     * })
     */
    private $codLang;


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
     * Set descricao
     *
     * @param string $descricao
     * @return ZgsegSexoTipoTr
     */
    public function setDescricao($descricao)
    {
        $this->descricao = $descricao;

        return $this;
    }

    /**
     * Get descricao
     *
     * @return string 
     */
    public function getDescricao()
    {
        return $this->descricao;
    }

    /**
     * Set codSexo
     *
     * @param \Entidades\ZgsegSexoTipo $codSexo
     * @return ZgsegSexoTipoTr
     */
    public function setCodSexo(\Entidades\ZgsegSexoTipo $codSexo = null)
    {
        $this->codSexo = $codSexo;

        return $this;
    }

    /**
     * Get codSexo
     *
     * @return \Entidades\ZgsegSexoTipo 
     */
    public function getCodSexo()
    {
        return $this->codSexo;
    }

    /**
     * Set codLang
     *
     * @param \Entidades\ZgappLang $codLang
     * @return ZgsegSexoTipoTr
     */
    public function setCodLang(\Entidades\ZgappLang $codLang = null)
    {
        $this->codLang = $codLang;

        return $this;
    }

    /**
     * Get codLang
     *
     * @return \Entidades\ZgappLang 
     */
    public function getCodLang()
    {
        return $this->codLang;
    }
}
