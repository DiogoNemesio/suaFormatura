<?php

namespace Entidades;

use Doctrine\ORM\Mapping as ORM;

/**
 * ZgappMenu
 *
 * @ORM\Table(name="ZGAPP_MENU", indexes={@ORM\Index(name="fk_MENU_1_idx", columns={"COD_TIPO"}), @ORM\Index(name="fk_MENU_2_idx", columns={"COD_MENU_PAI"}), @ORM\Index(name="fk_MENU_3_idx", columns={"COD_MODULO"})})
 * @ORM\Entity
 */
class ZgappMenu
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
     * @ORM\Column(name="NOME", type="string", length=25, nullable=false)
     */
    private $nome;

    /**
     * @var string
     *
     * @ORM\Column(name="DESCRICAO", type="string", length=60, nullable=false)
     */
    private $descricao;

    /**
     * @var string
     *
     * @ORM\Column(name="LINK", type="string", length=100, nullable=true)
     */
    private $link;

    /**
     * @var integer
     *
     * @ORM\Column(name="NIVEL", type="integer", nullable=false)
     */
    private $nivel;

    /**
     * @var string
     *
     * @ORM\Column(name="ICONE", type="string", length=40, nullable=true)
     */
    private $icone;

    /**
     * @var string
     *
     * @ORM\Column(name="IND_FIXO", type="string", length=1, nullable=false)
     */
    private $indFixo;

    /**
     * @var string
     *
     * @ORM\Column(name="IND_SISTEMA", type="string", length=1, nullable=false)
     */
    private $indSistema;

    /**
     * @var \Entidades\ZgappMenuTipo
     *
     * @ORM\ManyToOne(targetEntity="Entidades\ZgappMenuTipo")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="COD_TIPO", referencedColumnName="CODIGO")
     * })
     */
    private $codTipo;

    /**
     * @var \Entidades\ZgappMenu
     *
     * @ORM\ManyToOne(targetEntity="Entidades\ZgappMenu")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="COD_MENU_PAI", referencedColumnName="CODIGO")
     * })
     */
    private $codMenuPai;

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
     * @return ZgappMenu
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
     * @return ZgappMenu
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
     * Set link
     *
     * @param string $link
     * @return ZgappMenu
     */
    public function setLink($link)
    {
        $this->link = $link;

        return $this;
    }

    /**
     * Get link
     *
     * @return string 
     */
    public function getLink()
    {
        return $this->link;
    }

    /**
     * Set nivel
     *
     * @param integer $nivel
     * @return ZgappMenu
     */
    public function setNivel($nivel)
    {
        $this->nivel = $nivel;

        return $this;
    }

    /**
     * Get nivel
     *
     * @return integer 
     */
    public function getNivel()
    {
        return $this->nivel;
    }

    /**
     * Set icone
     *
     * @param string $icone
     * @return ZgappMenu
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
     * Set indFixo
     *
     * @param string $indFixo
     * @return ZgappMenu
     */
    public function setIndFixo($indFixo)
    {
        $this->indFixo = $indFixo;

        return $this;
    }

    /**
     * Get indFixo
     *
     * @return string 
     */
    public function getIndFixo()
    {
        return $this->indFixo;
    }

    /**
     * Set indSistema
     *
     * @param string $indSistema
     * @return ZgappMenu
     */
    public function setIndSistema($indSistema)
    {
        $this->indSistema = $indSistema;

        return $this;
    }

    /**
     * Get indSistema
     *
     * @return string 
     */
    public function getIndSistema()
    {
        return $this->indSistema;
    }

    /**
     * Set codTipo
     *
     * @param \Entidades\ZgappMenuTipo $codTipo
     * @return ZgappMenu
     */
    public function setCodTipo(\Entidades\ZgappMenuTipo $codTipo = null)
    {
        $this->codTipo = $codTipo;

        return $this;
    }

    /**
     * Get codTipo
     *
     * @return \Entidades\ZgappMenuTipo 
     */
    public function getCodTipo()
    {
        return $this->codTipo;
    }

    /**
     * Set codMenuPai
     *
     * @param \Entidades\ZgappMenu $codMenuPai
     * @return ZgappMenu
     */
    public function setCodMenuPai(\Entidades\ZgappMenu $codMenuPai = null)
    {
        $this->codMenuPai = $codMenuPai;

        return $this;
    }

    /**
     * Get codMenuPai
     *
     * @return \Entidades\ZgappMenu 
     */
    public function getCodMenuPai()
    {
        return $this->codMenuPai;
    }

    /**
     * Set codModulo
     *
     * @param \Entidades\ZgappModulo $codModulo
     * @return ZgappMenu
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
