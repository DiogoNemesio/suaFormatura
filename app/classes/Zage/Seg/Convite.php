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
		$convite	= new \Entidades\ZgsegConvite();
		$convite->setCodOrganizacaoDestino(	$this->getCodOrganizacaoDestino()	);
		$convite->setCodOrganizacaoOrigem(	$this->getCodOrganizacaoOrigem()	);
		$convite->setCodUsuarioDestino(		$this->getCodUsuarioDestino()		);
		$convite->setCodUsuarioOrigem(		$this->getCodUsuarioOrigem()		);
		$convite->setCodUsuarioSolicitante(	$_user								);
		$convite->setData(					$this->getData()					);
		$convite->setIndUtilizado(			$this->getIndUtilizado()			);
		$convite->setSenha(					$this->getSenha()					);
		
		try {
		
			$em->persist($convite);
			
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
}
