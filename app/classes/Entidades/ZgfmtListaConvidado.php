<?php

namespace Entidades;

use Doctrine\ORM\Mapping as ORM;

/**
 * ZgfmtListaConvidado
 *
 * @ORM\Table(name="ZGFMT_LISTA_CONVIDADO")
 * @ORM\Entity
 */
class ZgfmtListaConvidado
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
     * @var integer
     *
     * @ORM\Column(name="COD_EVENTO", type="integer", nullable=false)
     */
    private $codEvento;

    /**
     * @var integer
     *
     * @ORM\Column(name="COD_USUARIO", type="integer", nullable=false)
     */
    private $codUsuario;

    /**
     * @var string
     *
     * @ORM\Column(name="NOME", type="string", length=60, nullable=false)
     */
    private $nome;

    /**
     * @var string
     *
     * @ORM\Column(name="SEXO", type="string", length=1, nullable=false)
     */
    private $sexo;

    /**
     * @var integer
     *
     * @ORM\Column(name="IDADE", type="integer", nullable=true)
     */
    private $idade;


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
     * Set codEvento
     *
     * @param integer $codEvento
     * @return ZgfmtListaConvidado
     */
    public function setCodEvento($codEvento)
    {
        $this->codEvento = $codEvento;

        return $this;
    }

    /**
     * Get codEvento
     *
     * @return integer 
     */
    public function getCodEvento()
    {
        return $this->codEvento;
    }

    /**
     * Set codUsuario
     *
     * @param integer $codUsuario
     * @return ZgfmtListaConvidado
     */
    public function setCodUsuario($codUsuario)
    {
        $this->codUsuario = $codUsuario;

        return $this;
    }

    /**
     * Get codUsuario
     *
     * @return integer 
     */
    public function getCodUsuario()
    {
        return $this->codUsuario;
    }

    /**
     * Set nome
     *
     * @param string $nome
     * @return ZgfmtListaConvidado
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
     * Set sexo
     *
     * @param string $sexo
     * @return ZgfmtListaConvidado
     */
    public function setSexo($sexo)
    {
        $this->sexo = $sexo;

        return $this;
    }

    /**
     * Get sexo
     *
     * @return string 
     */
    public function getSexo()
    {
        return $this->sexo;
    }

    /**
     * Set idade
     *
     * @param integer $idade
     * @return ZgfmtListaConvidado
     */
    public function setIdade($idade)
    {
        $this->idade = $idade;

        return $this;
    }

    /**
     * Get idade
     *
     * @return integer 
     */
    public function getIdade()
    {
        return $this->idade;
    }
}
