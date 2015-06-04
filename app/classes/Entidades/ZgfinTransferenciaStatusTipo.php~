<?php

namespace Entidades;

use Doctrine\ORM\Mapping as ORM;

/**
 * ZgfinTransferenciaStatusTipo
 *
 * @ORM\Table(name="ZGFIN_TRANSFERENCIA_STATUS_TIPO")
 * @ORM\Entity
 */
class ZgfinTransferenciaStatusTipo
{
    /**
     * @var string
     *
     * @ORM\Column(name="CODIGO", type="string", length=2, nullable=false)
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
     * @var string
     *
     * @ORM\Column(name="ESTILO_NORMAL", type="string", length=60, nullable=true)
     */
    private $estiloNormal;

    /**
     * @var string
     *
     * @ORM\Column(name="ESTILO_VENCIDO", type="string", length=60, nullable=true)
     */
    private $estiloVencido;


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
     * Set descricao
     *
     * @param string $descricao
     * @return ZgfinTransferenciaStatusTipo
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
     * Set estiloNormal
     *
     * @param string $estiloNormal
     * @return ZgfinTransferenciaStatusTipo
     */
    public function setEstiloNormal($estiloNormal)
    {
        $this->estiloNormal = $estiloNormal;

        return $this;
    }

    /**
     * Get estiloNormal
     *
     * @return string 
     */
    public function getEstiloNormal()
    {
        return $this->estiloNormal;
    }

    /**
     * Set estiloVencido
     *
     * @param string $estiloVencido
     * @return ZgfinTransferenciaStatusTipo
     */
    public function setEstiloVencido($estiloVencido)
    {
        $this->estiloVencido = $estiloVencido;

        return $this;
    }

    /**
     * Get estiloVencido
     *
     * @return string 
     */
    public function getEstiloVencido()
    {
        return $this->estiloVencido;
    }
}
