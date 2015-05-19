<?php

namespace Zage\App\Validador;

/**
 * Validador de CPF
 *
 * @package \Zage\App\Validador\Cpf
 * @author Daniel Henrique Cassela
 * @version 1.0.1
 */
class Cpf extends \Zend\Validator\AbstractValidator {

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
	
		if ((strlen($value) < 11) || (strlen($value) > 14)) {
			$this->error(self::LENGTH);
			$isValid = false;
			return (false);
		}

		$d1 = 0;
		$d2 = 0;
		// Retirar a máscara, caso exista
		$cpf = preg_replace("/[^0-9]/", "", $value);

		// lista de cpf inválidos que serão ignorados
		$ignore_list = array(
			'01234567890',
			'11111111111',
			'22222222222',
			'33333333333',
			'44444444444',
			'55555555555',
			'66666666666',
			'77777777777',
			'88888888888',
			'99999999999'
		);

		// se o tamanho da string for dirente de 11 ou estiver
		// na lista de cpf ignorados já retorna false
		if(strlen($cpf) != 11 || in_array($cpf, $ignore_list)){
			return false;
		} else {
			// inicia o processo para achar o primeiro
			// número verificador usando os primeiros 9 dígitos
			for($i = 0; $i < 9; $i++){
				// inicialmente $d1 vale zero e é somando.
				// O loop passa por todos os 9 dígitos iniciais
				$d1 += $cpf[$i] * (10 - $i);
			}
			// acha o resto da divisão da soma acima por 11
			$r1 = $d1 % 11;
			// se $r1 maior que 1 retorna 11 menos $r1 se não
			// retona o valor zero para $d1
			$d1 = ($r1 > 1) ? (11 - $r1) : 0;
			// inicia o processo para achar o segundo
			// número verificador usando os primeiros 9 dígitos
			for($i = 0; $i < 9; $i++) {
				// inicialmente $d2 vale zero e é somando.
				// O loop passa por todos os 9 dígitos iniciais
				$d2 += $cpf[$i] * (11 - $i);
			}
			// $r2 será o resto da soma do cpf mais $d1 vezes 2
			// dividido por 11
			$r2 = ($d2 + ($d1 * 2)) % 11;
			// se $r2 mair que 1 retorna 11 menos $r2 se não
			// retorna o valor zeroa para $d2
			$d2 = ($r2 > 1) ? (11 - $r2) : 0;
			// retona true se os dois últimos dígitos do cpf
			// forem igual a concatenação de $d1 e $d2 e se não
			// deve retornar false.
			return (substr($cpf, -2) == $d1 . $d2) ? true : false;
		}
	}

}