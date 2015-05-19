<?php

namespace Entidades;

use Doctrine\ORM\Mapping as ORM;

/**
 * ZgforEnquete
 *
 * @ORM\Table(name="ZGFOR_ENQUETE", indexes={@ORM\Index(name="fk_ZGFOR_ENQUETE_1_idx", columns={"COD_FORMATURA"})})
 * @ORM\Entity
 */
class ZgforEnquete
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
     * @var \DateTime
     *
     * @ORM\Column(name="DATA_CADASTRO", type="datetime", nullable=false)
     */
    private $dataCadastro;

    /**
     * @var \Entidades\ZgforOrganizacao
     *
     * @ORM\ManyToOne(targetEntity="Entidades\ZgforOrganizacao")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="COD_FORMATURA", referencedColumnName="CODIGO")
     * })
     */
    private $codFormatura;


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
     * @return ZgforEnquete
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
     * Set dataCadastro
     *
     * @param \DateTime $dataCadastro
     * @return ZgforEnquete
     */
    public function setDataCadastro($dataCadastro)
    {
        $this->dataCadastro = $dataCadastro;

        return $this;
    }

    /**
     * Get dataCadastro
     *
     * @return \DateTime 
     */
    public function getDataCadastro()
    {
        return $this->dataCadastro;
    }

    /**
     * Set codFormatura
     *
     * @param \Entidades\ZgforOrganizacao $codFormatura
     * @return ZgforEnquete
     */
    public function setCodFormatura(\Entidades\ZgforOrganizacao $codFormatura = null)
    {
        $this->codFormatura = $codFormatura;

        return $this;
    }

    /**
     * Get codFormatura
     *
     * @return \Entidades\ZgforOrganizacao 
     */
    public function getCodFormatura()
    {
        return $this->codFormatura;
    }
}
