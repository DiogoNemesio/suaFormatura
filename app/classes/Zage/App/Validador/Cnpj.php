<?php

namespace Zage\App\Validador;

/**
 * Validador de Cnpj
 *
 * @package \Zage\App\Validador\Cnpj
 * @author Daniel Henrique Cassela
 * @version 1.0.1
 */
class Cnpj extends \Zend\Validator\AbstractValidator {

	const LENGTH = 'length';
	
	protected $messageTemplates = array(
			self::LENGTH => "'%value%' deve conter entre 11 e 14 caracteres",
	);
	
	/**
	 * Validador
	 * @see \Zend\Validator\ValidatorInterface::isValid()
	 */
	public function isValid($value)	{
		$this->setValue($value);
	
		$isValid = true;
	
		if ((strlen($value) < 14) || (strlen($value) > 18)) {
			$this->error(self::LENGTH);
			$isValid = false;
			return (false);
		}

		$cnpj = preg_replace('/[^0-9]/', '', (string) $value);
		
		// Valida tamanho
		if (strlen($cnpj) != 14) return false;
		
		// Valida primeiro dígito verificador
		for ($i = 0, $j = 5, $soma = 0; $i < 12; $i++) {
			$soma += $cnpj{$i} * $j;
			$j = ($j == 2) ? 9 : $j - 1;
		}
		
		$resto = $soma % 11;
		
		if ($cnpj{12} != ($resto < 2 ? 0 : 11 - $resto)) return false;
		
		// Valida segundo dígito verificador
		for ($i = 0, $j = 6, $soma = 0; $i < 13; $i++) {
			$soma += $cnpj{$i} * $j;
			$j = ($j == 2) ? 9 : $j - 1;
		}
		
		$resto = $soma % 11;
		
		return $cnpj{13} == ($resto < 2 ? 0 : 11 - $resto);
	}

}