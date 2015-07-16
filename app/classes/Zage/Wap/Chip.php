<?php

namespace Zage\Wap;

/**
 * Chip
 * 
 * @package: Chip
 * @Author: Daniel Henrique Cassela
 * @version: 1.0.1
 * 
 */

class Chip extends \Entidades\ZgwapChip {

	/**
	 * Código
	 * @var unknown
	 */
	var $_codigo;
	
	/**
     * Construtor
     *
	 * @return void
	 */
	public function __construct() {
		
	}
	
	/**
	 * Salvar os dados do chip
	 * @return number $codigo
	 */
	public function salvar() {
		global $em,$tr,$log;
		
		#################################################################################
		## Fazer validação dos campos
		#################################################################################
		if (!$this->getCodOrganizacao())				throw new \Exception($tr->trans("Campo Organização é obrigatório !!"));
		if (!$this->getIdentificacao())					throw new \Exception($tr->trans("Campo Identificação é obrigatório!"));
		if (strlen($this->getIdentificacao()) > 40)		throw new \Exception($tr->trans("A identificação não deve conter mais de 40 caracteres!"));
		if (!$this->getNumero())						throw new \Exception($tr->trans("Campo Número é obrigatório !!"));
		if (!$this->getCodPais())						throw new \Exception($tr->trans("Campo País é obrigatório !!"));
		
		#################################################################################
		## Separar o ddd do número
		#################################################################################
		$ddd		= substr($this->getNumero(),0,2);
		$celular	= substr($this->getNumero(),2);
		
		#################################################################################
		## Salvar no banco
		#################################################################################
		try {
			#################################################################################
			## Verifica se o código foi passado para atualizar, se já existir o chip
			#################################################################################
			if ($this->_getCodigo()) {
				$oChip	= $em->getRepository('\Entidades\ZgwapChip')->findOneBy(array('codigo' => $this->_getCodigo()));
				if (!$oChip)	throw new \Exception($tr->trans("Chip não encontrado !!"));
			}else{
				$oChip	= new \Entidades\ZgwapChip();
				$oChip->setDataCadastro(new \DateTime("now"));
				
				#################################################################################
				## Resgatar o status inicial do chip
				#################################################################################
				$oStatus	= $em->getReference('\Entidades\ZgwapChipStatus', "R");
				$oChip->setCodStatus($oStatus);
				
			}

			$oChip->setDdd($ddd);
			$oChip->setIdentificacao($this->getIdentificacao());
			$oChip->setNumero($celular);
			$oChip->setCodOrganizacao($this->getCodOrganizacao());
			$oChip->setCodPais($this->getCodPais());
		
			$em->persist($oChip);
			$em->flush();
			$em->detach($oChip);
			
			return ($oChip->getCodigo());
		
		} catch (\Exception $e) {
			$log->err('Erro ao cadastrar o chip "'.$this->getNumero().'" -> '.$e->getMessage());
			throw new \Exception($e->getMessage());
		}
		
	}
	
	/**
	 * Solicitar o código SMS
	 * @throws \Exception
	 */
	public function solicitaCodigoPorSms() {
		global $log,$tr,$em;
		
		#################################################################################
		## Verifica se o chip existe
		#################################################################################
		if (!$this->_getCodigo()) throw new \Exception($tr->trans("Código do chip deve ser informado !!"));
		$oChip		= $em->getRepository('\Entidades\ZgwapChip')->findOneBy(array('codigo' => $this->_getCodigo()));
		if (!$oChip)	throw new \Exception($tr->trans("Chip não encontrado !!"));
		
		#################################################################################
		## Solicitar o registro através de SMS
		#################################################################################
		if ($oChip->getCodStatus()->getCodigo() == "R") {
			$debug 		= false;
			$waUser 	= $oChip->getCodPais()->getCallingCode() . $oChip->getDdd() . $oChip->getNumero();  	// Telephone number including the country code without '+' or '00'.
			$nickname 	= $oChip->getIdentificacao();    														// This is the username displayed by WhatsApp clients.
			 
			$log->info("Solicitando código SMS para o chip: ".$waUser);

			try {
				// 	Create an instance of WhatsProt.
				$w 			= new \WhatsProt($waUser, $nickname, $debug);
				$return		= $w->codeRequest('sms');
				$log->debug("Retorno SMS: ".serialize($return));
				if ($return->status	!= "ok") {
					throw new \Exception($tr->trans("Erro ao enviar a requisição, status do retorno: ".$return->status));
				}
				
			} catch (\Exception $e) {
				$log->err('Erro ao solicitar código através do SMS do chip "'.$waUser.'" -> '.$e->getMessage());
				throw new \Exception($e->getMessage());
			}
		}else{
			throw new \Exception($tr->trans("Status do chip não permite solicitação de código"));
		}
	}
	
	/**
	 * Registrar o código SMS
	 * @throws \Exception
	 */
	public function registrar() {
		global $log,$tr,$em;
	
		#################################################################################
		## Verifica se o chip existe
		#################################################################################
		if (!$this->_getCodigo()) throw new \Exception($tr->trans("Código do chip deve ser informado !!"));
		$oChip		= $em->getRepository('\Entidades\ZgwapChip')->findOneBy(array('codigo' => $this->_getCodigo()));
		if (!$oChip)	throw new \Exception($tr->trans("Chip não encontrado !!"));

		#################################################################################
		## Validar o código
		#################################################################################
		if (!$this->getCode()) 					throw new \Exception($tr->trans("Código SMS deve ser informado !!"));
		if (strlen($this->getCode()) < 3) 		throw new \Exception($tr->trans("Código SMS deve conter mais de 3 caracteres!"));
		$code 	= str_replace("-", "", $this->getCode());

		#################################################################################
		## Formatar os campos para efetuar o registro
		#################################################################################
		$debug 		= false;
		$waUser 	= $oChip->getCodPais()->getCallingCode() . $oChip->getDdd(). $oChip->getNumero();  	// Telephone number including the country code without '+' or '00'.
		$nickname 	= $oChip->getIdentificacao();    													// This is the username displayed by WhatsApp clients.
		
		#################################################################################
		## Fazer o registro
		#################################################################################
		try {
			$w 			= new \WhatsProt($waUser, $nickname, $debug);
			$log->info("Vou registrar o número: ".$waUser, "Nickname: ".$nickname);
			$return		= $w->codeRegister($code);
			$log->info(serialize($return));
			$status		= $return->status;
			$senha		= $return->pw;
		
			if ($status != "ok") {
				$log->err("Falha no registro do chip: $waUser -> ".serialize($return));
				throw new \Exception("Falha ao registrar o chip: $waUser, retorno dos servidores whatsapp: ".$status);
			}
		
		} catch (\Exception $e) {
			$log->err("Falha no registro do chip: $waUser -> ".$e->getMessage());
			throw new \Exception("Falha ao registrar o chip, entre em contato com os administradores do sistema através do email: contato@suaformatura.com");
		}
		

		#################################################################################
		## Atualizar o Chip com a senha retornada
		#################################################################################
		
		#################################################################################
		## Resgatar o status que será salvo
		#################################################################################
		$oStatus	= $em->getReference('\Entidades\ZgwapChipStatus', "A");
		
		try {
			$oChip->setCodStatus($oStatus);
			$oChip->setSenha($senha);
			$oChip->setCode($code);
			$oChip->setDataRegistro(new \DateTime("now"));
		
			$em->persist($oChip);
			$em->flush();
			$em->detach($oChip);
		
		} catch (\Exception $e) {
			$log->err("Falha ao salvar os dados de registro do chip: $waUser -> ".$e->getMessage());
			throw new \Exception("Falha ao salvar os dados de registro do chip: $waUser -> ".$e->getMessage());
		}
		
	}
	
	
	/**
	 * Definir o código
	 * @param number $codigo
	 */
	public function _setCodigo($codigo) {
		$this->_codigo	= $codigo;
	}
	
	/**
	 * Retornar o código
	 * @return $_codigo
	 */
	public function _getCodigo() {
		return ($this->_codigo);
	}

}