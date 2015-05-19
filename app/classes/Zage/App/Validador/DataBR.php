<?php

namespace Zage\App\Validador;

/**
 * Validador de Data formato Brasil
 *
 * @package \Zage\App\Validador\DataBR
 * @author Daniel Henrique Cassela
 * @version 1.0.1
 */
class DataBR extends \Zend\Validator\AbstractValidator {

	const LENGTH = 'length';
	
	protected $messageTemplates = array(
			self::LENGTH => "'%value%' deve conter entre 10 caracteres",
	);
	
	/**
	 * Validador
	 * @see \Zend\Validator\ValidatorInterface::isValid()
	 */
	public function isValid($value)	{
		$this->setValue($value);
	
		$isValid = true;
	
		if ((strlen($value) != 10)) {
			$this->error(self::LENGTH);
			$isValid = false;
			return (false);
		}

		try {
			if (date_create_from_format("d/m/Y", $value) === false) {
				return false;
			}
				
			return true;
		} catch (\Exception $e) {
			return false;
		}
	}

}