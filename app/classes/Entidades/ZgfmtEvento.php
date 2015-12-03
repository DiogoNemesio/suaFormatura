<?php

namespace Entidades;

use Doctrine\ORM\Mapping as ORM;

/**
 * ZgfmtEvento
 *
 * @ORM\Table(name="ZGFMT_EVENTO", indexes={@ORM\Index(name="fk_ZGFOR_EVENTO_1_idx", columns={"COD_TIPO_EVENTO"}), @ORM\Index(name="fk_ZGFOR_EVENTO_2_idx", columns={"COD_FORMATURA"}), @ORM\Index(name="fk_ZGFOR_EVENTO_3_idx", columns={"COD_LOGRADOURO"}), @ORM\Index(name="ZGFOR_EVENTO_1_UN", columns={"COD_FORMATURA", "COD_TIPO_EVENTO"}), @ORM\Index(name="fk_ZGFMT_EVENTO_1_idx", columns={"COD_LOCAL"})})
 * @ORM\Entity
 */
class ZgfmtEvento
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
     * @ORM\Column(name="QTDE_CONVIDADO", type="integer", nullable=true)
     */
    private $qtdeConvidado;

    /**
     * @var float
     *
     * @ORM\Column(name="VALOR_AVULSO", type="float", precision=10, scale=0, nullable=true)
     */
    private $valorAvulso;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="DATA", type="datetime", nullable=true)
     */
    private $data;

    /**
     * @var string
     *
     * @ORM\Column(name="LOCAL", type="string", length=60, nullable=true)
     */
    private $local;

    /**
     * @var string
     *
     * @ORM\Column(name="CEP", type="string", length=8, nullable=true)
     */
    private $cep;

    /**
     * @var string
     *
     * @ORM\Column(name="ENDERECO", type="string", length=100, nullable=true)
     */
    private $endereco;

    /**
     * @var string
     *
     * @ORM\Column(name="BAIRRO", type="string", length=60, nullable=true)
     */
    private $bairro;

    /**
     * @var string
     *
     * @ORM\Column(name="COMPLEMENTO", type="string", length=100, nullable=true)
     */
    private $complemento;

    /**
     * @var string
     *
     * @ORM\Column(name="NUMERO", type="string", length=10, nullable=true)
     */
    private $numero;

    /**
     * @var string
     *
     * @ORM\Column(name="LATITUDE", type="string", length=15, nullable=true)
     */
    private $latitude;

    /**
     * @var string
     *
     * @ORM\Column(name="LONGITUDE", type="string", length=15, nullable=true)
     */
    private $longitude;

    /**
     * @var \Entidades\ZgfmtEventoTipo
     *
     * @ORM\ManyToOne(targetEntity="Entidades\ZgfmtEventoTipo")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="COD_TIPO_EVENTO", referencedColumnName="CODIGO")
     * })
     */
    private $codTipoEvento;

    /**
     * @var \Entidades\ZgadmOrganizacao
     *
     * @ORM\ManyToOne(targetEntity="Entidades\ZgadmOrganizacao")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="COD_FORMATURA", referencedColumnName="CODIGO")
     * })
     */
    private $codFormatura;

    /**
     * @var \Entidades\ZgadmLogradouro
     *
     * @ORM\ManyToOne(targetEntity="Entidades\ZgadmLogradouro")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="COD_LOGRADOURO", referencedColumnName="CODIGO")
     * })
     */
    private $codLogradouro;

    /**
     * @var \Entidades\ZgadmOrganizacao
     *
     * @ORM\ManyToOne(targetEntity="Entidades\ZgadmOrganizacao")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="COD_LOCAL", referencedColumnName="CODIGO")
     * })
     */
    private $codLocal;


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
     * Set qtdeConvidado
     *
     * @param integer $qtdeConvidado
     * @return ZgfmtEvento
     */
    public function setQtdeConvidado($qtdeConvidado)
    {
        $this->qtdeConvidado = $qtdeConvidado;

        return $this;
    }

    /**
     * Get qtdeConvidado
     *
     * @return integer 
     */
    public function getQtdeConvidado()
    {
        return $this->qtdeConvidado;
    }

    /**
     * Set valorAvulso
     *
     * @param float $valorAvulso
     * @return ZgfmtEvento
     */
    public function setValorAvulso($valorAvulso)
    {
        $this->valorAvulso = $valorAvulso;

        return $this;
    }

    /**
     * Get valorAvulso
     *
     * @return float 
     */
    public function getValorAvulso()
    {
        return $this->valorAvulso;
    }

    /**
     * Set data
     *
     * @param \DateTime $data
     * @return ZgfmtEvento
     */
    public function setData($data)
    {
        $this->data = $data;

        return $this;
    }

    /**
     * Get data
     *
     * @return \DateTime 
     */
    public function getData()
    {
        return $this->data;
    }

    /**
     * Set local
     *
     * @param string $local
     * @return ZgfmtEvento
     */
    public function setLocal($local)
    {
        $this->local = $local;

        return $this;
    }

    /**
     * Get local
     *
     * @return string 
     */
    public function getLocal()
    {
        return $this->local;
    }

    /**
     * Set cep
     *
     * @param string $cep
     * @return ZgfmtEvento
     */
    public function setCep($cep)
    {
        $this->cep = $cep;

        return $this;
    }

    /**
     * Get cep
     *
     * @return string 
     */
    public function getCep()
    {
        return $this->cep;
    }

    /**
     * Set endereco
     *
     * @param string $endereco
     * @return ZgfmtEvento
     */
    public function setEndereco($endereco)
    {
        $this->endereco = $endereco;

        return $this;
    }

    /**
     * Get endereco
     *
     * @return string 
     */
    public function getEndereco()
    {
        return $this->endereco;
    }

    /**
     * Set bairro
     *
     * @param string $bairro
     * @return ZgfmtEvento
     */
    public function setBairro($bairro)
    {
        $this->bairro = $bairro;

        return $this;
    }

    /**
     * Get bairro
     *
     * @return string 
     */
    public function getBairro()
    {
        return $this->bairro;
    }

    /**
     * Set complemento
     *
     * @param string $complemento
     * @return ZgfmtEvento
     */
    public function setComplemento($complemento)
    {
        $this->complemento = $complemento;

        return $this;
    }

    /**
     * Get complemento
     *
     * @return string 
     */
    public function getComplemento()
    {
        return $this->complemento;
    }

    /**
     * Set numero
     *
     * @param string $numero
     * @return ZgfmtEvento
     */
    public function setNumero($numero)
    {
        $this->numero = $numero;

        return $this;
    }

    /**
     * Get numero
     *
     * @return string 
     */
    public function getNumero()
    {
        return $this->numero;
    }

    /**
     * Set latitude
     *
     * @param string $latitude
     * @return ZgfmtEvento
     */
    public function setLatitude($latitude)
    {
        $this->latitude = $latitude;

        return $this;
    }

    /**
     * Get latitude
     *
     * @return string 
     */
    public function getLatitude()
    {
        return $this->latitude;
    }

    /**
     * Set longitude
     *
     * @param string $longitude
     * @return ZgfmtEvento
     */
    public function setLongitude($longitude)
    {
        $this->longitude = $longitude;

        return $this;
    }

    /**
     * Get longitude
     *
     * @return string 
     */
    public function getLongitude()
    {
        return $this->longitude;
    }

    /**
     * Set codTipoEvento
     *
     * @param \Entidades\ZgfmtEventoTipo $codTipoEvento
     * @return ZgfmtEvento
     */
    public function setCodTipoEvento(\Entidades\ZgfmtEventoTipo $codTipoEvento = null)
    {
        $this->codTipoEvento = $codTipoEvento;

        return $this;
    }

    /**
     * Get codTipoEvento
     *
     * @return \Entidades\ZgfmtEventoTipo 
     */
    public function getCodTipoEvento()
    {
        return $this->codTipoEvento;
    }

    /**
     * Set codFormatura
     *
     * @param \Entidades\ZgadmOrganizacao $codFormatura
     * @return ZgfmtEvento
     */
    public function setCodFormatura(\Entidades\ZgadmOrganizacao $codFormatura = null)
    {
        $this->codFormatura = $codFormatura;

        return $this;
    }

    /**
     * Get codFormatura
     *
     * @return \Entidades\ZgadmOrganizacao 
     */
    public function getCodFormatura()
    {
        return $this->codFormatura;
    }

    /**
     * Set codLogradouro
     *
     * @param \Entidades\ZgadmLogradouro $codLogradouro
     * @return ZgfmtEvento
     */
    public function setCodLogradouro(\Entidades\ZgadmLogradouro $codLogradouro = null)
    {
        $this->codLogradouro = $codLogradouro;

        return $this;
    }

    /**
     * Get codLogradouro
     *
     * @return \Entidades\ZgadmLogradouro 
     */
    public function getCodLogradouro()
    {
        return $this->codLogradouro;
    }

    /**
     * Set codLocal
     *
     * @param \Entidades\ZgadmOrganizacao $codLocal
     * @return ZgfmtEvento
     */
    public function setCodLocal(\Entidades\ZgadmOrganizacao $codLocal = null)
    {
        $this->codLocal = $codLocal;

        return $this;
    }

    /**
     * Get codLocal
     *
     * @return \Entidades\ZgadmOrganizacao 
     */
    public function getCodLocal()
    {
        return $this->codLocal;
    }
}
