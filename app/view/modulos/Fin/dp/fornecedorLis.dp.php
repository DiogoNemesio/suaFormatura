<?php
#################################################################################
## Includes
#################################################################################
if (defined('DOC_ROOT')) {
	include_once(DOC_ROOT . 'include.php');
}else{
	include_once('../include.php');
}

#################################################################################
## Verifica se o usuário está autenticado
#################################################################################
include_once(BIN_PATH . 'auth.php');

#################################################################################
## Resgata as variáveis postadas
#################################################################################
if (isset($_GET['q']))			$q			= \Zage\App\Util::antiInjection($_GET["q"]);

$pessoas		= \Zage\Fin\Pessoa::busca($q,false,true,false);
$array			= array();
$numItens		= \Zage\Adm\Parametro::getValor('APP_BS_TA_ITENS');

for ($i = 0; $i < sizeof($pessoas); $i++) {
	
	
	if ($pessoas[$i]->getCodTipoPessoa()->getCodigo() == "F") {
		$infoCgc	= \Zage\App\Mascara::tipo(\Zage\App\Mascara\Tipo::TP_CPF)->aplicaMascara($pessoas[$i]->getCgc());
	}else{
		$infoCgc	= \Zage\App\Mascara::tipo(\Zage\App\Mascara\Tipo::TP_CNPJ)->aplicaMascara($pessoas[$i]->getCgc());
	}
	
	$array[$i]["id"]		= $pessoas[$i]->getCodigo();
	$array[$i]["name"]		= $pessoas[$i]->getNome() . " (".$infoCgc.")";
	
	if ($i > $numItens ) break;
}

//echo json_encode($arr);
echo json_encode($array);