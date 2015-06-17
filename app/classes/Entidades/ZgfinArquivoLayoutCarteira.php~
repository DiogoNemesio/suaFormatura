<?php

namespace Entidades;

use Doctrine\ORM\Mapping as ORM;

/**
 * ZgfinArquivoLayoutCarteira
 *
 * @ORM\Table(name="ZGFIN_ARQUIVO_LAYOUT_CARTEIRA", indexes={@ORM\Index(name="fk_ZGFIN_ARQUIVO_LAYOUT_CARTEIRA_2_idx", columns={"COD_CARTEIRA"}), @ORM\Index(name="fk_ZGFIN_ARQUIVO_LAYOUT_CARTEIRA_1_idx", columns={"COD_LAYOUT"})})
 * @ORM\Entity
 */
class ZgfinArquivoLayoutCarteira
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
     * @var \Entidades\ZgfinArquivoLayout
     *
     * @ORM\ManyToOne(targetEntity="Entidades\ZgfinArquivoLayout")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="COD_LAYOUT", referencedColumnName="CODIGO")
     * })
     */
    private $codLayout;

    /**
     * @var \Entidades\ZgfinCarteira
     *
     * @ORM\ManyToOne(targetEntity="Entidades\ZgfinCarteira")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="COD_CARTEIRA", referencedColumnName="CODIGO")
     * })
     */
    private $codCarteira;


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
     * Set codLayout
     *
     * @param \Entidades\ZgfinArquivoLayout $codLayout
     * @return ZgfinArquivoLayoutCarteira
     */
    public function setCodLayout(\Entidades\ZgfinArquivoLayout $codLayout = null)
    {
        $this->codLayout = $codLayout;

        return $this;
    }

    /**
     * Get codLayout
     *
     * @return \Entidades\ZgfinArquivoLayout 
     */
    public function getCodLayout()
    {
        return $this->codLayout;
    }

    /**
     * Set codCarteira
     *
     * @param \Entidades\ZgfinCarteira $codCarteira
     * @return ZgfinArquivoLayoutCarteira
     */
    public function setCodCarteira(\Entidades\ZgfinCarteira $codCarteira = null)
    {
        $this->codCarteira = $codCarteira;

        return $this;
    }

    /**
     * Get codCarteira
     *
     * @return \Entidades\ZgfinCarteira 
     */
    public function getCodCarteira()
    {
        return $this->codCarteira;
    }
}
