<?php
namespace Zage\App\FilaImportacao;

/**
 * @package: \Zage\App\FilaImportacao\Exception
 * @Author: Daniel Henrique Cassela
 * @version: 1.0.1
 *
 * Gerência de exceções
 */

class Exception extends \Exception {
	
	public static function raiseException(\Exception $e, $mensagem) 	{
		global $log;
		$log->err("FilaImportação raised exception: ".$e->getMessage());
		return new self($mensagem);
	}
	
}
