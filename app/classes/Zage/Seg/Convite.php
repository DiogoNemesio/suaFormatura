<?php

namespace Zage\Seg;

/**
 * Convite
 *
 * @package Convite
 * @author Daniel Henrique Cassela
 * @version 1.0.1
 */
class Convite extends \Entidades\ZgsegConvite {

	private $_convite;
	
    /**
     * Construtor
     *
	 * @return void
	 */
	public function __construct() {
		global $log;
		$log->debug(__CLASS__.": nova instância");
	}
	
	/**
	 * Salvar um novo convite
	 */
	public function salvar() {
		global $system,$log,$_user,$em;
		
		#################################################################################
		## Verificar se as informações obrigatórias foram informadas
		#################################################################################
		if (!$this->getCodUsuarioOrigem() 		&& !$this->getCodUsuarioDestino()) 		throw new Exception('Usuário origem ou destino deve ser informado');
		if (!$this->getCodOrganizacaoOrigem()	&& !$this->getCodOrganizacaoDestino()) 	throw new Exception('Organização origem ou destino deve ser informada');
		
		#################################################################################
		## Gerar a senha
		#################################################################################
		$this->setSenha($this->_geraSenha());
		
		#################################################################################
		## Definir as informações padrões
		#################################################################################
		$this->setData(new \DateTime());
		$this->setIndUtilizado(0);
		
		#################################################################################
		## Copiar o objeto
		#################################################################################
		$this->_convite	= new \Entidades\ZgsegConvite();
		$this->_convite->setCodOrganizacaoDestino(	$this->getCodOrganizacaoDestino()	);
		$this->_convite->setCodOrganizacaoOrigem(	$this->getCodOrganizacaoOrigem()	);
		$this->_convite->setCodUsuarioDestino(		$this->getCodUsuarioDestino()		);
		$this->_convite->setCodUsuarioOrigem(		$this->getCodUsuarioOrigem()		);
		$this->_convite->setCodUsuarioSolicitante(	$_user								);
		$this->_convite->setData(					$this->getData()					);
		$this->_convite->setIndUtilizado(			$this->getIndUtilizado()			);
		$this->_convite->setSenha(					$this->getSenha()					);
		$this->_convite->setCodStatus(				$this->getCodStatus()				);
		
		try {
		
			$em->persist($this->_convite);
			
		} catch (\Exception $e) {
			$log->err($e->getTraceAsString());
			die($e->getMessage());
		}
		
	}
	
	/**
	 * Gerar a senha aleatória do convite
	 */
	private function _geraSenha() {
		return md5(time() . rand());
	}
	
	public function _getCodigo() {
		return $this->_convite->getCodigo();
	}
}
