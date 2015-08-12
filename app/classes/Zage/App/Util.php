<?php

namespace Zage\App;

/**
 * Funções diversas
 *
 * @package \Zage\App\Util
 * @author Daniel Henrique Cassela
 * @version 1.0.1
 * @created 17/07/2013
 */

class Util {
	
	/**
	 * Construtor privado, a classe deve ser usada de forma statica
	 */
	private function __construct() {
		
	}
	
	/**
	 * Validação de e-mail
	 * @param unknown $email
	 * @return boolean
	 */
	public static function validarEMail($email) {
		$validator = new \Zend\Validator\EmailAddress();
		return $validator->isValid($email);
	}
	
	/**
	 * Resgatar o conteúdo de um arquivo
	 * @param string $arquivo
	 * @return string 
	 */
	public static function getConteudoArquivo ($arquivo) {
		/** Checar se o arquivo existe **/
		if (file_exists($arquivo)) {
			try {
				/** Abre o arquivo somente para leitura **/
				$handle         = fopen($arquivo, "r");
	
				/** Lê o conteudo do arquivo em uma variavel **/
				$conteudo       = fread ($handle, filesize ($arquivo));
	
				/** Fecha o arquivo **/
				fclose($handle);
	
				return($conteudo);
	
			} catch (\Exception $e) {
				\Zage\App\Erro::halt('Código do Erro: "getConteudoArquivo": '.$e->getMessage());
			}
		}else{
			return null;
		}
	}
	
	
	/**
	 * Implementação de Anti injeção de SQL
	 * @param string $string
	 * @return string
	 */
	public static function antiInjection($string) {
		
		if (is_array($string)) return $string;
	
		/** remove palavras que contenham sintaxe sql **/
		$string = preg_replace("/(from|select|insert|delete|where|drop table|show tables|#|\*|--|\\\\)/i","",$string);
	
		/** limpa espaços vazio **/
		$string = trim($string);
	
		/** tira tags html e php **/
		$string = strip_tags($string);//
	
		/** Converte caracteres especiais para a realidade HTML **/
		$string = htmlspecialchars($string);
	
		if (!get_magic_quotes_gpc()) {
			$string = addslashes($string);
		}
	
		return ($string);
	}
	
	/**
	 * Retornar o mês por extenso
	 * @param integer $mes
	 * @return string
	 */
	public static function mesPorExtenso($mes) {
		$mes    = (int) $mes;
		switch (fmod($mes,12)) {
			case 1:
			case "01":
				return('Janeiro');
			case 2:
			case "02":
				return('Fevereiro');
			case 3:
			case "03":
				return('Março');
			case 4:
			case "04":
				return('Abril');
			case 5:
			case "05":
				return('Maio');
			case 6:
			case "06":
				return('Junho');
			case 7:
			case "07":
				return('Julho');
			case 8:
			case "08":
				return('Agosto');
			case 9:
			case "09":
				return('Setembro');
			case "10":
				return('Outubro');
			case "11":
				return('Novembro');
			case "0":
			case "12":
				return('Dezembro');
			default:
				return('??????');
		}
	}
	
	/**
	 * Descobrir o mime type de um arquivo
	 * @param unknown $arquivo
	 */
	public static function getMimeType($arquivo) {
		return(MIME_Type::autoDetect($arquivo));
	}
	
	/**
	 * Descompactar um arquivo, retornando o conteúdo descompactado
	 * @param unknown $arquivo
	 * @return boolean|string
	 */
	public static function descompacta ($arquivo) {
		 
		/** Verifica se o arquivo existe e pode ser lido **/
		if ((!file_exists($arquivo)) || (!is_readable($arquivo))) return false;
		 
		/** Verifica o mime type do arquivo **/
		switch (\Zage\App\Util::getMimeType($arquivo)) {
			case 'application/x-bzip2':
				try {
					$bz = bzopen($arquivo, "r");
					while (!feof($bz)) {
						$arquivo_descomprimido .= bzread($bz, 4096);
					}
					bzclose($bz);
					return ($arquivo_descomprimido);
				} catch (\Exception $e) {
					\Zage\App\Erro::halt('Erro ao tentar descompactar o arquivo: '.$arquivo. ' Trace: '.$e->getTraceAsString());
				}
		}
		 
	}
	
	/**
	 * Checar se um IP é válido
	 * @param string $ip
	 * @return boolean
	 */
	public static function validaIP ($ip) {
		/** Verificar se o IP está no format global do IPV4 **/
		if (preg_match("/^(\d{1,3})\.(\d{1,3})\.(\d{1,3})\.(\d{1,3})$/",$ip))  {
			/** Separar cada bloco em uma array **/
			$parts	= explode(".",$ip);
			 
			/** Checar se cada bloco está correto **/
			foreach($parts as $ip_parts) {
				if (intval($ip_parts)>255 || intval($ip_parts)<0) {
					return false;
				} else {
					return true;
				}
			}
		}else{
			return false;
		}
	}
	

	/**
	 * Retornar um número em formato de moeda (BR)
	 *
	 * @param number
	 * @return string
	 */
	public static function to_money($n) {
		if (!$n)	$n = 0;
		$temp		= self::to_float($n);
		return('R$ '.\Zage\App\Util::fnumber_format($temp, 2, ',', '.'));
	}
	
	/**
	 * Formatar um número
	 * @param float $number
	 * @param string $decimals
	 * @param string $sep1
	 * @param string $sep2
	 */
	function fnumber_format($number, $decimals='', $sep1='', $sep2='') {
	
		if (($number * pow(10 , $decimals + 1) % 10 ) == 5)  //if next not significant digit is 5
			$number -= pow(10 , -($decimals+1));
	
		return number_format($number, $decimals, $sep1, $sep2);
	
	}

	/**
	 * Retornar um número formatado
	 *
	 * @param number
	 * @return string
	 */
	public static function to_number($n) {
		$temp = str_replace(".","",$n);
		$temp = str_replace(",",".",$temp);
		return(number_format($temp, 0, ',', '.'));
	}
	
	/**
	 * Retornar um número formatado
	 *
	 * @param number
	 * @return float
	 */
	public static function formataDinheiro($n) {
		$valor		= self::to_float($n);
		return(number_format($valor,2,',',''));
	}
	
	
	/**
	 * Retornar um número transformado em float
	 *
	 * @param number
	 * @return float
	 */
	public static function to_float($n) {
		
		$posPonto	= strpos($n, ".");
		$posVirg	= strpos($n, ",");
		
		if ($posPonto === false && $posVirg === false) {
			return $n;
		}elseif ($posPonto !== false && $posVirg !== false) {
			if ($posPonto > $posVirg) {
				$temp = str_replace(",","",$n);
			}else{
				$temp = str_replace(".","",$n);
				$temp = str_replace(",",".",$temp);
			}
		}elseif ($posPonto === false) {
			$temp 	= str_replace(",",".",$n);
		}else{
			$temp	= $n;
		}
		
		return(floatval($temp));
	}
	
	/**
	 * Retornar Primeiro dia do mês
	 *
	 * @param date (formato dd/mm/yyyy)
	 * @return date (formato dd/mm/yyyy)
	 */
	public static function getFirstDayOfMonth($data) {
		list($dia,$mes,$ano)	= split('/',$data);
		$timeStamp				= mktime(0,0,0,$mes,1,$ano); //Create time stamp of the first day
    	$firstDay				= date('d/m/Y',$timeStamp);  //get first day of the given month		
		return($firstDay);
	}

	/**
	 * Retornar último dia do mês
	 *
	 * @param date (formato dd/mm/yyyy)
	 * @return date (formato dd/mm/yyyy)
	 */
	public static function getLastDayOfMonth($data) {
		list($dia,$mes,$ano)	= split('/',$data);
		$timeStamp				= mktime(0,0,0,$mes,1,$ano);    		//Create time stamp of the first day
		list($t,$m,$a)			= split('/',date('t/m/Y',$timeStamp)); 	//Find the last date of the month and separating it
    	$lastDayTimeStamp		= mktime(0,0,0,$m,$t,$a);				//create time stamp of the last date of the give month
		$lastDay				= date('d/m/Y',$lastDayTimeStamp);
		return($lastDay);
	}
	

	/**
	 * Formatar um CGC
	 *
	 * @param number 
	 * @return string 
	 */
	public static function formatCGC($cgc) {
		if (((strlen($cgc)) < 13) || ((strlen($cgc)) > 14)) {
			return $cgc;
		}else{
			if ((strlen($cgc)) == 13) $cgc = "0".$cgc;
			return (substr($cgc,0,2).'.'.substr($cgc,2,3).'.'.substr($cgc,5,3).'/'.substr($cgc,8,4).'-'.substr($cgc,12,2)) ;
		}
	}

	/**
	 * Retirar todos os espaços em branco contínuos de uma string
	 *
	 * @param string
	 * @return string 
	 */
	public static function retiraEspacos($string) {
		
		$str 	= $string;
		
		while (strpos($str, '  ') !== false) {
			$str = str_replace('  ',' ',$str);
		}
		
		return (trim($str));
	}


	/**
	 * Retornar uma quantidade de caracter 
	 *
	 * @param string
	 * @return string 
	 */
	public static function qtdStr($chr,$qtd) {
		$string	= '';
		for ($i = 1; $i <= $qtd; $i++) $string .= $chr;
		return ($string);
	}

	/**
	 * Adicionar caracteres a esquerda de uma string
	 *
	 * @param string
	 * @return string 
	 */
	public static function lpad( $string, $length, $pad = ' ' ) { 
		return str_pad( $string, $length, $pad, STR_PAD_LEFT );
	}
	
	/**
	 * Adicionar caracteres a direita de uma string
	 *
	 * @param string
	 * @return string 
	 */
	public static function rpad( $string, $length, $pad = ' ' ) { 
		return str_pad( $string, $length, $pad, STR_PAD_RIGHT );
	}
	
	/**
	 * Formatar um CEP
	 *
	 * @param number 
	 * @return string 
	 */
	public static function formatCEP($cep) {
		if ((strlen($cep)) !== 8)  {
			return $cep;
		}else{
			return (substr($cep,0,5) . '-'.substr($cep,5,3));
		}
	}

	/**
	 * Formatar uma Data
	 *
	 * @param string
	 * @return date
	 */
	public static function toDate($date) {
		//global $log;
		
		/** Descobrindo o formato usado **/
		$pos = strpos($date, "/");

		//$log->debug("Data Antes: ".$date);
		
		if ($pos == 2) { // formato dd/mm/yyyy
			$dia		= substr($date,0,2);
			$mes		= substr($date,3,2);
			$ano		= substr($date,6,4);
		}elseif ($pos == false) { // formato yyyymmdd
			$ano		= substr($date,0,4);
			$mes		= substr($date,4,2);
			$dia		= substr($date,6,2);
		}else{ 				// formato yyyy/mm/dd
			$ano		= substr($date,0,4);
			$mes		= substr($date,5,2);
			$dia		= substr($date,8,2);
		}
		
		return ($dia.'/'.$mes.'/'.$ano);
	}
	
	/**
	 * Transformar um ano em 4 dígitos
	 *
	 * @param string
	 * @return number
	 */
	public static function toYear4($ano) {
		if ((strlen($ano)) !== 2)  return $ano;
		
		if ($ano > 60 ) {
			return ($ano + 1900);
		}else{
			return ($ano + 2000);
		}
	}
	
	/**
	 * Enviar os header para o browser fazer download do arquivo
	 *
	 * @param varchar Nome do Arquivo
	 * @param varchar Tipo do Arquivo

	 */
	public static function sendHeaderDownload($nomeArquivo,$tipo) {
		header("Pragma: public");
  		header("Expires: 0");
  		header("Pragma: no-cache");
  		header("Cache-Control: no-store, no-cache, must-revalidate, post-check=0, pre-check=0");
		header("Content-Type: application/force-download");
  		header("Content-Type: application/octet-stream");
  		header("Content-Type: application/download");
		header('Content-disposition: attachment; filename='.$nomeArquivo);
  		header("Content-Type: application/".$tipo);
  		header("Content-Transfer-Encoding: binary");
	}

	/**
	 * Enviar os header para o browser fazer download do arquivo
	 *
	 * @param varchar Nome do Arquivo
	 * @param varchar Tipo do Arquivo
	
	 */
	public static function sendHeaderPDF($nomeArquivo) {
		header("Pragma: public");
		header("Expires: 0");
		header("Pragma: no-cache");
		header('Content-disposition: inline; filename="'.$nomeArquivo.'"');
		header("Cache-Control: no-store, no-cache, must-revalidate, post-check=0, pre-check=0");
		header('Content-Type: application/pdf');
	}
	
	
	/**
	 * Comparação entre 2 números float
	 * @param unknown $f1
	 * @param unknown $f2
	 * @param number $precision
	 * @return boolean
	 */
	public static function floatcmp($f1,$f2,$precision = 10) {
		$e = pow(10,$precision);
		return (intval($f1 * $e) == intval($f2 * $e));
	}
	
	
	/**
	 * Codifica uma string
	 * @param string $string
	 */
	public static function encodeUrl($string) {
		return(base64_encode($string));
	}
	
	/**
	 * Codifica uma string
	 * @param string $string
	 */
	public static function decodeUrl($string) {
		return(base64_decode($string));
	}
	
	/**
	 * Descompacta um id
	 * @param string $id
	 */
	public static function descompactaId($id) {
		if ($id != null) {
			$var    = base64_decode($id);
			$vars   = explode("&",$var);
			for ($i = 0; $i < sizeof($vars); $i++) {
				if ($vars[$i] != '') {
					list($variavel,$valor)  = explode('=',$vars[$i]);
					eval('global $'.$variavel.';');
					eval('$'.$variavel.' = "'.$valor.'";');
				}
			}
		}
	}
	
	/**
	 * Retornar os números de uma string
	 * @param string $str
	 * @return number
	 */
	public static function getNumbers($str) {
		preg_match_all('/\d+/', $str, $matches);
		return implode("", $matches[0]);
	}
	
	/**
	 *
	 * Resgatar o caminho completo do arquivo por extensão
	 * @param string $arquivo
	 * @param string $extensao
	 * @param string $tipo
	 */
	public static function getCaminhoCorrespondente($caminho,$extensao,$tipo = \Zage\App\ZWS::CAMINHO_ABSOLUTO) {
		global $log;
		
		/** define o tipo padrão **/
		if (!$tipo)     $tipo   = \Zage\App\ZWS::CAMINHO_ABSOLUTO;
		
		/** Verifica se o caminho passado é uma URL ou um caminho **/
		if ($tipo == \Zage\App\ZWS::CAMINHO_ABSOLUTO) { 
			/** Caminho absoluto **/
			//$tipo   	= \Zage\App\ZWS::CAMINHO_ABSOLUTO;

			/** Resgata o nome/dir base do arquivo **/
			$base   	= pathinfo($caminho,PATHINFO_BASENAME);
			$baseDir	= realpath(pathinfo($caminho,PATHINFO_DIRNAME)."/../");

			/** Resgata o nome do arquivo sem a extensão **/
			$base  		= substr($base,0,strpos($base,'.'));

		} else {
			/** Url **/
			//$tipo   = \Zage\App\ZWS::CAMINHO_RELATIVO;
			
			/** Resgata o nome/dir base do arquivo **/
			$base		= pathinfo($caminho,PATHINFO_BASENAME);
			$dir		= pathinfo($caminho,PATHINFO_DIRNAME);
			$baseDir	= dirname($dir);
			$baseDir	= str_replace('\\',"/",$baseDir);
			$modPath	= str_replace('//',"/",MOD_PATH);
			$modPath	= str_replace('\\',"/",$modPath);
			$modulo		= str_replace($modPath,"",$baseDir);
			$baseDir	= ROOT_URL . "/$modulo/";

			/** Resgata o nome do arquivo sem a extensão **/
			$base   = substr($base,0,strpos($base,'.'));
		}
		
		//echo "Caminho: $caminho, Base: $base, Dir: $dir, BaseDir: $baseDir,ModPath: $modPath, Modulo: $modulo<br>";
	
		
		switch (strtolower($extensao)) {
			case \Zage\App\ZWS::EXT_HTML:
				$dir	= "html";
				break;
			case \Zage\App\ZWS::EXT_DP:
				$dir	= "dp";
				break;
			case \Zage\App\ZWS::EXT_XML:
				$dir	= "xml";
				break;
			case \Zage\App\ZWS::EXT_PHP:
				$dir	= "php";
				break;
			default:
				return ($arquivo);
				break;
		}
	
		if ($tipo == \Zage\App\ZWS::CAMINHO_RELATIVO) {
			return ($baseDir . '/' . $base . "." . $extensao);
		}else{
			return ($baseDir . '/' . $dir . '/' . $base . "." . $extensao);
		}
	}
	
	/**
	 * Mostrar o tamanho de um arquivo num formato legível
	 * @param number $bytes
	 * @param number $decimals
	 * @return string
	 */
	public static function mostraTamanhoLegivel($bytes, $decimals = 2) {
		$size = array('B','kB','MB','GB','TB','PB','EB','ZB','YB');
		$factor = floor((strlen($bytes) - 1) / 3);
		return sprintf("%.{$decimals}f", $bytes / pow(1024, $factor)) . @$size[$factor];
	}
	
	/**
	 * Converter um número para o formato float do PHP
	 * @param string $valor
	 */
	public static function toPHPNumber($valor) {
		global $log;
		
		return self::to_float($valor);
		
		$temp		= $valor;
		$return		= number_format(floatval($temp), 2, ',', '');
		return($return);
	}
	
	/**
	 * Converter um número para o formato float do MySQL
	 * @param string $valor
	 */
	public static function toMysqlNumber($valor) {
		if (empty($valor)) return 0;
		
		$temp		= self::to_float($valor);
		return(self::fnumber_format($temp, 2, '.', ''));
		
		
		$v	= str_replace('.','',$valor);
		$v	= str_replace(',','.',$v);
		return($v);
	}
	
	
	/**
	 * Validar uma data
	 * @param string $string
	 * @param string $formato
	 * @return boolean
	 */
	public static function validaData($string,$formato) {
		try {
			if (date_create_from_format($formato, $string) === false) {
				return false;
			}
			
			return true;
		} catch (\Exception $e) {
			return false;
		}
		
	}
	
	/**
	 * Retirar os acentos de uma string
	 * @param string $texto
	 * @return string
	 */
	public static function retiraAcentos($texto){
		$texto = preg_replace( '/[`^~\'"]/', null, iconv( 'UTF-8', 'ASCII//TRANSLIT', $texto ) );
		return ($texto);
	}
	
	public static function  validaCnpj($cnpj) {
		$cnpj = preg_replace ( '/[^0-9]/', '', ( string ) $cnpj );

		// Valida tamanho
		if (strlen ( $cnpj ) != 14) 	return false;

		// Valida primeiro dígito verificador
		for($i = 0, $j = 5, $soma = 0; $i < 12; $i ++) {
			$soma += $cnpj {$i} * $j;
			$j = ($j == 2) ? 9 : $j - 1;
		}
		$resto = $soma % 11;
		
		if ($cnpj {12} != ($resto < 2 ? 0 : 11 - $resto)) return false;

		// Valida segundo dígito verificador
		for($i = 0, $j = 6, $soma = 0; $i < 13; $i ++) {
			$soma += $cnpj {$i} * $j;
			$j = ($j == 2) ? 9 : $j - 1;
		}
		$resto = $soma % 11;
		return $cnpj {13} == ($resto < 2 ? 0 : 11 - $resto);
	}
	
	public static function validaCep($cep) {
		// retira espacos em branco
		$cep = trim($cep);
		// expressao regular para avaliar o cep
		return ereg("^[0-9]{5}-[0-9]{3}$", $cep);
	}
	
	public static function ehNumero($n) {
		return (preg_match ("/^([0-9.,\-]+)$/", $n));
	}
	
	public static function geraCorAleatoria() {
		$i0		= rand(0,15);
		$i1		= rand(0,15);
		$i2		= rand(0,15);
		$i3		= rand(0,15);
		$i4		= rand(0,15);
		$i5		= rand(0,15);
		
		$hex	= array(
			0 => "0",
			1 => "1",
			2 => "2",
			3 => "3",
			4 => "4",
			5 => "5",
			6 => "6",
			7 => "7",
			8 => "8",
			9 => "9",
			10 => "A",
			11 => "B",
			12 => "C",
			13 => "D",
			14 => "E",
			15 => "F",
		);
		
		return "#".$hex[$i0].$hex[$i1].$hex[$i2].$hex[$i3].$hex[$i4].$hex[$i5];
	}
	
	/**
	 * Resgatar o ip do cliente
	 */
	public static function getIPUsuario() {
		$ipaddress = '';
		if ($_SERVER['HTTP_CLIENT_IP'])
			$ipaddress = $_SERVER['HTTP_CLIENT_IP'];
		else if($_SERVER['HTTP_X_FORWARDED_FOR'])
			$ipaddress = $_SERVER['HTTP_X_FORWARDED_FOR'];
		else if($_SERVER['HTTP_X_FORWARDED'])
			$ipaddress = $_SERVER['HTTP_X_FORWARDED'];
		else if($_SERVER['HTTP_FORWARDED_FOR'])
			$ipaddress = $_SERVER['HTTP_FORWARDED_FOR'];
		else if($_SERVER['HTTP_FORWARDED'])
			$ipaddress = $_SERVER['HTTP_FORWARDED'];
		else if($_SERVER['REMOTE_ADDR'])
			$ipaddress = $_SERVER['REMOTE_ADDR'];
		else
			$ipaddress = 'UNKNOWN';
		return $ipaddress;
	}
	
}
