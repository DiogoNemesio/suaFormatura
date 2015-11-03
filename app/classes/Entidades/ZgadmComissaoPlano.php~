<?php

namespace Entidades;

use Doctrine\ORM\Mapping as ORM;

/**
 * ZgadmComissaoPlano
 *
 * @ORM\Table(name="ZGADM_COMISSAO_PLANO")
 * @ORM\Entity
 */
class ZgadmComissaoPlano
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
     * @ORM\Column(name="NOME", type="string", length=60, nullable=false)
     */
    private $nome;

    /**
     * @var float
     *
     * @ORM\Column(name="VALOR_INICIAL", type="float", precision=10, scale=0, nullable=false)
     */
    private $valorInicial;

    /**
     * @var float
     *
     * @ORM\Column(name="VALOR_FINAL", type="float", precision=10, scale=0, nullable=false)
     */
    private $valorFinal;

    /**
     * @var float
     *
     * @ORM\Column(name="PCT_COMISSAO", type="float", precision=10, scale=0, nullable=false)
     */
    private $pctComissao;


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
     * @return ZgadmComissaoPlano
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
     * Set valorInicial
     *
     * @param float $valorInicial
     * @return ZgadmComissaoPlano
     */
    public function setValorInicial($valorInicial)
    {
        $this->valorInicial = $valorInicial;

        return $this;
    }

    /**
     * Get valorInicial
     *
     * @return float 
     */
    public function getValorInicial()
    {
        return $this->valorInicial;
    }

    /**
     * Set valorFinal
     *
     * @param float $valorFinal
     * @return ZgadmComissaoPlano
     */
    public function setValorFinal($valorFinal)
    {
        $this->valorFinal = $valorFinal;

        return $this;
    }

    /**
     * Get valorFinal
     *
     * @return float 
     */
    public function getValorFinal()
    {
        return $this->valorFinal;
    }

    /**
     * Set pctComissao
     *
     * @param float $pctComissao
     * @return ZgadmComissaoPlano
     */
    public function setPctComissao($pctComissao)
    {
        $this->pctComissao = $pctComissao;

        return $this;
    }

    /**
     * Get pctComissao
     *
     * @return float 
     */
    public function getPctComissao()
    {
        return $this->pctComissao;
    }
}
