<?php

namespace Zage\Seg;

/**
 * Dicionario
 *
 * @package Usuario
 * @author Daniel Henrique Cassela
 * @version 1.0.1
 */
class Dicionario {

    /**
     * Construtor
     *
	 * @return void
	 */
	public function __construct() {
		global $log;
		
		parent::__construct();
		$log->debug(__CLASS__.": nova instância");
		
	}
	
    /**
     * Atualiza o dicionário
     */
    public static function atualizaDicionario () {
    	global $em;
    	 
    	try {
	    	$con	= $em->getConnection();
	    	$sm		= $con->getSchemaManager();
	    	$tables = $sm->listTables();
	    	
	    	foreach ($tables as $table) {
	    		$tabela	= self::_getTabelaDicionario($table->getName());
	    		
	    		if (!$tabela) {
	    			$tabela	= new \Entidades\ZgsegDicionario();
	    			$tabela->setNome($table->getName());
	    			$tabela->setDescricao($table->getName());
	    			$tabela->setIndAudit(0);
	    			$em->persist($tabela);
	    			$em->flush();
	    			 
	    		}else{
	    			$em->persist($tabela);
	    		}
	    		
	    		$ordem = 1;
	    		foreach ($table->getColumns() as $column) {
	    			$coluna	= self::_getColunaDicionario($tabela->getCodigo(),$column->getName());
	    			
	    			if (!$coluna) {
	    				$coluna	= new \Entidades\ZgsegDicionarioCampo();
	    				$coluna->setCodDicionario($tabela);
	    				$coluna->setNome($column->getName());
	    				$coluna->setOrdem($ordem);
	    				$coluna->setDescricao($column->getName());
	    				$coluna->setIndAudit(1);
	    				$em->persist($coluna);
	    				$em->flush();
	    				$em->detach($coluna);
	    				
	    			}
	    			$ordem++;
	    		}
	    		$em->detach($tabela);	    		
	    		
	    	}
	    	
	    	return null;
    	} catch (\Exception $e) {
			return ($e->getMessage());
		}
    }
    
    
    /**
     * Resgata o código da tabela
     * @param string $tabela
     */
    protected static function _getTabelaDicionario($tabela) {
    	global $em, $log;
    	
    	$info 	= $em->getRepository('Entidades\ZgsegDicionario')->findOneBy(array('nome' => $tabela));
    	
    	if (!$info) {
    		return false;
    	}else{
    		return $info;
    	}
    	
    }

    /**
     * Resgata o código da coluna
     * @param number $codTabela
     * @param string $coluna
     */
    protected static function _getColunaDicionario($codTabela,$coluna) {
    	global $em, $log;
    	 
    	$info 	= $em->getRepository('Entidades\ZgsegDicionarioCampo')->findOneBy(array('codDicionario' => $codTabela,'nome' => $coluna));
    	 
    	if (!$info) {
    		return false;
    	}else{
    		return $info;
    	}
    	 
    }
    
    public static function atualizaTrilha() {
    	global $em,$log,$db;
    	
    	$NL			= chr(10);
   		$tabelas 	= $em->getRepository('Entidades\ZgsegDicionario')->findBy(array('indAudit' => 1));

		foreach ($tabelas as $tabela) {
   			 	
			//echo "Tabela: ".$tabela->getNome()."<BR>";
			$trgBaseName	= "ZG_TRG_";
				
			$sqlDEL1	= "DROP TRIGGER IF EXISTS `".$trgBaseName.$tabela->getCodigo()."_IN`";
			$sqlDEL2	= "DROP TRIGGER IF EXISTS `".$trgBaseName.$tabela->getCodigo()."_UP`";
			$sqlDEL3	= "DROP TRIGGER IF EXISTS `".$trgBaseName.$tabela->getCodigo()."_DE`";

			$indAudit	= \Zage\Adm\Parametro::getValor('APP_AUDIT');
			
			if ($indAudit == 1) {
			
				$sqlIN		= 'CREATE TRIGGER `'.$trgBaseName.$tabela->getCodigo().'_IN` AFTER INSERT ON '.$tabela->getNome().$NL;
				$sqlUP		= 'CREATE TRIGGER `'.$trgBaseName.$tabela->getCodigo().'_UP` AFTER UPDATE ON '.$tabela->getNome().$NL;
				$sqlDE		= 'CREATE TRIGGER `'.$trgBaseName.$tabela->getCodigo().'_DE` AFTER DELETE ON '.$tabela->getNome().$NL;
				
				$sqlIN		.= 'FOR EACH ROW '.$NL;
				$sqlUP		.= 'FOR EACH ROW '.$NL;
				$sqlDE		.= 'FOR EACH ROW '.$NL;
				
				$sqlIN		.= "BEGIN ".$NL;
				$sqlUP		.= "BEGIN ".$NL;
				$sqlDE		.= "BEGIN ".$NL;
	
				$colunas	= $em->getRepository('Entidades\ZgsegDicionarioCampo')->findBy(array('codDicionario' => $tabela->getCodigo(),'indAudit' => 1));
			
	    		foreach ($colunas as $coluna) {
	    			//echo "Coluna: ".$coluna->getNome()." - ";
	   				$sqlIN .= "INSERT INTO ZGSEG_LOG (COD_ORGANIZACAO,CODIGO,COD_USUARIO,DATA,COD_TIPO_EVENTO,COD_CAMPO,VALOR_ANTERIOR,VALOR_POSTERIOR,HISTORICO) VALUES (@ZG_ORG,NULL,@ZG_USER,SYSDATE(),'I','".$coluna->getCodigo()."',NULL,NEW.".$coluna->getNome().",NULL); ".$NL;
	   				$sqlDE .= "INSERT INTO ZGSEG_LOG (COD_ORGANIZACAO,CODIGO,COD_USUARIO,DATA,COD_TIPO_EVENTO,COD_CAMPO,VALOR_ANTERIOR,VALOR_POSTERIOR,HISTORICO) VALUES (@ZG_ORG,NULL,@ZG_USER,SYSDATE(),'E','".$coluna->getCodigo()."',OLD.".$coluna->getNome().",NULL,NULL); ".$NL;
	   				
	   				$sqlUP .= " IF (OLD.".$coluna->getNome()." <> NEW.".$coluna->getNome()." ) THEN".$NL;
	   				$sqlUP .= "		INSERT INTO ZGSEG_LOG (COD_ORGANIZACAO,CODIGO,COD_USUARIO,DATA,COD_TIPO_EVENTO,COD_CAMPO,VALOR_ANTERIOR,VALOR_POSTERIOR,HISTORICO) VALUES (@ZG_ORG,NULL,@ZG_USER,SYSDATE(),'A','".$coluna->getCodigo()."',OLD.".$coluna->getNome().",NEW.".$coluna->getNome().",NULL); ".$NL;
	   				$sqlUP .= " END IF;".$NL;
	    		}
    			
	    		$sqlIN 	.= 'END;'.$NL;
	    		$sqlUP 	.= 'END;'.$NL;
	    		$sqlDE 	.= 'END;'.$NL;
			}
	    	
    		//echo "SQL UPDATE: ".str_replace($NL,"<BR>",$sqlUP)."<BR>";
    			
    		try {
	    		$db->Executa($sqlDEL1);
	    		$db->Executa($sqlDEL2);
	    		$db->Executa($sqlDEL3);
	    		
	    		if ($indAudit == 1) {
	    		
    				$db->Executa($sqlIN);
    				$db->Executa($sqlUP);
    				$db->Executa($sqlDE);
	    		}
    		} catch (\Exception $e) {
    			return ($e->getMessage());
    		}
    				
		}
	    return null;
    }
	

}
