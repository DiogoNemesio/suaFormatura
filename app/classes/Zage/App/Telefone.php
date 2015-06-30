<?php

namespace Zage\App;


/**
 * Telefone
 *
 * @package Telefone
 * @author Diogo Nemésio
 * @version 1.0.1
 */
class Telefone {

	/**
	 * Proprietário do telefone
	 * @var unknown
	 */
	private $_proprietário;
	
	/**
	 * Ententidade do telefone
	 * @var unknown
	 */
	private $_entidadeTel;
	
	/**
	 * Telefones
	 * @var unknown
	 */
	private $_telefone;
	
	/**
	 * Codigo do tipo de telefone
	 * @var unknown
	 */
	private $_codTipoTel;
	
	/**
	 * Codigo do telefone
	 * @var unknown
	 */
	private $_codTelefone;
	
	/**
     * Construtor
     *
	 * @return void
	 */
	public function __construct() {
		global $log;
		$log->debug(__CLASS__.": nova instância");
		
	}
	
	public function _setProprietario($prop) {
		$this->_proprietário = $prop;
	}
	
	public function _getProprietario() {
		return ($this->_proprietário);
	}
	
	public function _setEntidadeTel($entidade) {
		$this->_entidadeTel = $entidade;
	}
	
	public function _getEntidadeTel() {
		return ($this->_entidadeTel);
	}
	
	public function _setTelefone(array $telefone) {
		$this->_telefone = $telefone;
	}
	
	public function _getTelefone() {
		return ($this->_telefone);
	}
	
	public function _setCodTipoTel(array $tipoTelefone) {
		$this->_codTipoTel = $tipoTelefone;
	}
	
	public function _getCodTipoTel() {
		return ($this->_codTipoTel);
	}
	
	public function _setCodTelefone(array $codTelefone) {
		$this->_codTelefone = $codTelefone;
	}
	
	public function _getCodTelefone() {
		return ($this->_codTelefone);
	}
	
    
	
	/**
	 * Salvar telefones
	 */
	public function salvar() {
		global $em,$system,$log,$tr;
	
		#################################################################################
		## Valida campos
		#################################################################################
		
					
		#################################################################################
		## Salvar Telefones
		#################################################################################

		$telefones		= $em->getRepository('Entidades\ZgsegUsuarioTelefone')->findBy(array('codUsuario' => $this->_getCodigo()));
		$codTelefone 	= $this->_getCodTelefone();
		$codTipoTel		= $this->_getCodTipoTel();
		$telefone		= $this->_getTelefone();
		$entidade		= $this->_getEntidadeTel();
		
		/*** Exclusão ***/
		for ($i = 0; $i < sizeof($telefones); $i++) {
			if (!in_array($telefones[$i]->getCodigo(), $codTelefone)) {
				try {
					$em->remove($telefones[$i]);
				} catch (\Exception $e) {
					$system->criaAviso(\Zage\App\Aviso\Tipo::ERRO,"Não foi possível excluir o telefone: ".$telefones[$i]->getTelefone()." Erro: ".$e->getMessage());
					echo '1'.\Zage\App\Util::encodeUrl('||'.htmlentities($e->getMessage()));
					exit;
				}
			}
		}
		
		/***  Criação / Alteração ***/
		for ($i = 0; $i < sizeof($codTelefone); $i++) {
			$infoTel		= $em->getRepository(''.$entidade.'')->findOneBy(array('codigo' => $codTelefone[$i] , 'codUsuario' => $this->_getCodigo()));
		
			if (!$infoTel) {
				$infoTel		= new $entidade();
			}
		
			if ($infoTel->getCodTipoTelefone() !== $codTipoTel[$i] || $infoTel->getTelefone() !== $telefone[$i]) {
		
				$oTipoTel	= $em->getRepository('Entidades\ZgappTelefoneTipo')->findOneBy(array('codigo' => $codTipoTel[$i]));
		
				$infoTel->setCodUsuario($this->_usuario);
				$infoTel->setCodTipoTelefone($oTipoTel);
				$infoTel->setTelefone($telefone[$i]);
		
				$em->persist($infoTel);
			}
		}
	}
	
	public function _getCodigo() {
		return $this->_usuario->getCodigo();
	}
	
	public function _getUsuario() {
		return $this->_usuario;
	}
	
}
