<?php

namespace Zage\Fin;

/**
 * Gerenciar as movimentações bancárias
 * 
 * @package: MovBancaria
 * @Author: Daniel Henrique Cassela
 * @version: 1.0.1
 * 
 */

class MovBancaria extends \Entidades\ZgfinMovBancaria {

	/**
     * Construtor
     *
	 * @return void
	 */
	public function __construct() {
		global $log;
		$log->debug(__CLASS__.": nova Instância");
	}
	
	/**
	 * Código
	 * @var unknown
	 */
	private $_codigo;
	
	/**
	 * Salva uma movimentação 
	 */
	public function salva () {
		global $em,$system,$log,$tr;
		
		#################################################################################
		## Validações de campos 
		#################################################################################
		if (!$this->getCodOrganizacao()) {
			return $tr->trans('Filial deve ser informada');
		}
		
		if (!$this->getCodConta()) {
			return $tr->trans('Conta deve ser informada');
		}
		
		if (!$this->getCodTipoOperacao()) {
			return $tr->trans('Tipo de operação deve ser informada');
		}

		if (!$this->getValor()) {
			return $tr->trans('Valor deve ser informado');
		}
		
		if (\Zage\App\Util::ehNumero($this->getValor()) == false) {
			return $tr->trans('Valor incorreto !!!');
		}

		if (!$this->getDataMovimentacao()) {
			return $tr->trans('Data de movimentação deve ser informada !!!');
		}
		
		if (!$this->getCodOrigem()) {
			return $tr->trans('Origem deve ser informada !!!');
		}
		
		if (!$this->getCodGrupoMov()) {
			return $tr->trans('Grupo de movimentação deve ser informado !!!');
		}
		
		#################################################################################
		## Copia os valores de um objeto para o outro
		#################################################################################
		$mov	= new \Entidades\ZgfinMovBancaria();
		$mov->setCodOrganizacao($this->getCodOrganizacao());
		$mov->setCodConta($this->getCodConta());
		$mov->setCodOrigem($this->getCodOrigem());
		$mov->setCodTipoOperacao($this->getCodTipoOperacao());
		$mov->setDataMovimentacao($this->getDataMovimentacao());
		$mov->setDataOperacao(new \DateTime("now"));
		$mov->setValor($this->getValor());
		$mov->setCodGrupoMov($this->getCodGrupoMov());
		
		try {
			
			$em->persist($mov);
		} catch (\Exception $e) {
			return $e->getMessage();
		}

		return null;
	}
	
	
	/**
	 * Excluir os rateios de uma conta
	 */
	public function exclui($oConta) {
		global $em,$_user,$log,$system,$tr;
	
		#################################################################################
		## Valida o status da conta
		#################################################################################
		$status 	= $oConta->getCodStatus()->getCodigo();
		
		switch ($status) {
			case "A":
			case "C":
				$podeExc	= true;
				break;
			default:
				$podeExc	= false;
				break;
		}
	
		if (!$podeExc) {
			return($tr->trans('Rateio não pode ser excluído, status não permitido (%s)',array('%s' => $oConta->getCodStatus()->getCodigo())));
		}
		
		$rateios	= $em->getRepository('Entidades\ZgfinMovBancaria')->findBy(array('codContaRec' => $oConta->getCodigo())); 
	
		try {
			
			for ($i = 0; $i < sizeof($rateios); $i++) {
				$em->remove($rateios[$i]);
			}
			
			return null;
		} catch (\Exception $e) {
			return $e->getMessage();
		}
	}
	
	
	public function _setCodigo($codigo) {
		$this->_codigo	= $codigo;
	}
	
	public function _getCodigo() {
		return ($this->_codigo);
	}

}