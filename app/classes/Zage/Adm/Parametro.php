<?php

namespace Zage\Adm;

/**
 * Parâmetros do sistema
 * 
 * @package: Parametro
 * @Author: Daniel Henrique Cassela
 * @version: 1.0.1
 * 
 */

class Parametro {

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
     * Resgata os menus por tipo de usuário
     *
     * @param integer $usuario
     * @return array
     */
    public static function lista ($parametro = null) {
		global $db;
		
    	return (
    		$db->extraiTodos("
				SELECT	P.*
				FROM	ZGADM_PARAMETRO P
				WHERE	P.PARAMETRO LIKE '%".$parametro."%'
				ORDER	BY PARAMETRO
			")
   		);
    }
    

    /**
	 * Salva o valor de um parâmetro
     */
    public function salva($parametro,$valor) {
		global $log,$db;
		$log->debug("Parametro: ".$parametro.' Valor: '.$valor);
    	try {
			$db->con->beginTransaction();
			$db->Executa("UPDATE ZGADM_PARAMETRO P SET P.VALOR = ? WHERE PARAMETRO = ?",
				array($valor,$parametro)
			);
			$db->con->commit();
			return null;
		}catch (\Exception $e) {
			$db->con->rollback();
			return($e->getMessage());
		}
    }
    
    /**
     * Resgata o valor do parâmetro
     *
     * @param varchar $parametro
     * @return array
     */
    public static function getValor ($parametro) {
		global $system,$em;
		
		$info	= $em->getRepository('Entidades\ZgadmParametro')->findOneBy(array('codOrganizacao' => $system->getCodOrganizacao(), 'parametro' => $parametro));
		
		if (!$info) return null;
		
		return ($info->getValor());
   	
    }
	
    /**
     * 
     * Resgata o código para carregamento dinâmico de códigos html
     */
    public static function getDinamicHtmlLoad () {
		global $db;
		
    	$html	= $db->extraiTodos("
			SELECT	H.URL
			FROM	ZGAPP_LOAD_HTML H
			WHERE	ATIVO 	= 1
			ORDER 	BY H.ORDEM
		");
    	
    	$return = '<!-- Carregado dinamicamente através do dinamicHtmlLoad -->'.PHP_EOL;
    	foreach ($html as $data) {
		//for ($i = 0; $i < sizeof($html); $i++) {
			$return .= $data->URL.PHP_EOL;
		}
		$return .= '<!-- Fim do carregamento dinâmico (dinamicHtmlLoad) -->'.PHP_EOL;
		return ($return);
    }

}