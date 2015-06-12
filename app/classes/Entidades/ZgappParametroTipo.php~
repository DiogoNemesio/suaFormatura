<?php

namespace Entidades;

use Doctrine\ORM\Mapping as ORM;

/**
 * ZgappParametroTipo
 *
 * @ORM\Table(name="ZGAPP_PARAMETRO_TIPO", indexes={@ORM\Index(name="fk_ZGAPP_PARAMETRO_TIPO_1_idx", columns={"COD_MASCARA"})})
 * @ORM\Entity
 */
class ZgappParametroTipo
{
    /**
     * @var string
     *
     * @ORM\Column(name="CODIGO", type="string", length=4, nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $codigo;

    /**
     * @var string
     *
     * @ORM\Column(name="NOME", type="string", length=40, nullable=false)
     */
    private $nome;

    /**
     * @var string
     *
     * @ORM\Column(name="DESCRICAO", type="string", length=60, nullable=true)
     */
    private $descricao;

    /**
     * @var \Entidades\ZgappMascara
     *
     * @ORM\ManyToOne(targetEntity="Entidades\ZgappMascara")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="COD_MASCARA", referencedColumnName="CODIGO")
     * })
     */
    private $codMascara;


    /**
     * Get codigo
     *
     * @return string 
     */
    public function getCodigo()
    {
        return $this->codigo;
    }

    /**
     * Set nome
     *
     * @param string $nome
     * @return ZgappParametroTipo
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
     * Set descricao
     *
     * @param string $descricao
     * @return ZgappParametroTipo
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
     * Set codMascara
     *
     * @param \Entidades\ZgappMascara $codMascara
     * @return ZgappParametroTipo
     */
    public function setCodMascara(\Entidades\ZgappMascara $codMascara = null)
    {
        $this->codMascara = $codMascara;

        return $this;
    }

    /**
     * Get codMascara
     *
     * @return \Entidades\ZgappMascara 
     */
    public function getCodMascara()
    {
        return $this->codMascara;
    }
}
