<?php

namespace Entidades;

use Doctrine\ORM\Mapping as ORM;

/**
 * ZgappParametroSecao
 *
 * @ORM\Table(name="ZGAPP_PARAMETRO_SECAO", indexes={@ORM\Index(name="fk_ZGAPP_PARAMETRO_SECAO_1_idx", columns={"COD_MODULO"})})
 * @ORM\Entity
 */
class ZgappParametroSecao
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
     * @ORM\Column(name="NOME", type="string", length=40, nullable=false)
     */
    private $nome;

    /**
     * @var integer
     *
     * @ORM\Column(name="ORDEM", type="integer", nullable=true)
     */
    private $ordem;

    /**
     * @var string
     *
     * @ORM\Column(name="ICONE", type="string", length=60, nullable=true)
     */
    private $icone;

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
     * Get codigo
     *
     * @return integer 
     */
    public function getCodigo()
    {
        return $this->codigo;
    }

    /**
     * Set nome
     *
     * @param string $nome
     * @return ZgappParametroSecao
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
     * Set ordem
     *
     * @param integer $ordem
     * @return ZgappParametroSecao
     */
    public function setOrdem($ordem)
    {
        $this->ordem = $ordem;

        return $this;
    }

    /**
     * Get ordem
     *
     * @return integer 
     */
    public function getOrdem()
    {
        return $this->ordem;
    }

    /**
     * Set icone
     *
     * @param string $icone
     * @return ZgappParametroSecao
     */
    public function setIcone($icone)
    {
        $this->icone = $icone;

        return $this;
    }

    /**
     * Get icone
     *
     * @return string 
     */
    public function getIcone()
    {
        return $this->icone;
    }

    /**
     * Set codModulo
     *
     * @param \Entidades\ZgappModulo $codModulo
     * @return ZgappParametroSecao
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
}
