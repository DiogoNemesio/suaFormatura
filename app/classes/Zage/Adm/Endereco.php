<?php

namespace Zage\Adm;

/**
 * Gerenciar endereços
 * 
 * @package: Cidade
 * @Author: Daniel Henrique Cassela
 * @version: 1.0.1
 * 
 */

class Endereco {

	/**
     * Construtor
     *
	 * @return void
	 */
	private function __construct() {
		global $log;
		$log->debug(__CLASS__.": nova Instância");
	}
	
	/**
	 *
	 * Busca as informações do endereço pelo CEP
	 */
	public static function buscaPorCep ($cep) {
		global $em,$system;

		$qb 	= $em->createQueryBuilder();
		
		try {
			$qb->select('l')
			->from('\Entidades\ZgadmLogradouro','l')
			->where(
				$qb->expr()->eq('l.cep'	, ':cep')
			)
			->setParameter('cep', $cep);
			$query 		= $qb->getQuery();
			return($query->getResult());
		} catch (\Exception $e) {
			\Zage\App\Erro::halt($e->getMessage());
		}
	}
	
	/**
	 * Retornar o endereço formatado
	 * @param string $endereco
	 * @param string $numero
	 * @param string $bairro
	 */
	public static function formataEndereco($endereco,$numero,$bairro,$complemento = null) {
		if (!$endereco && !$numero && !$bairro && !$complemento) return null;
		return trim($endereco . " ".$numero. ", ".$bairro." ".$complemento);
	}
}