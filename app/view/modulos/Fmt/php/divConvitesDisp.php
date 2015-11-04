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
## Resgata a variável ID que está criptografada
#################################################################################
if (isset($_GET['id'])) {
	$id = \Zage\App\Util::antiInjection($_GET["id"]);
}elseif (isset($_POST['id'])) {
	$id = \Zage\App\Util::antiInjection($_POST["id"]);
}elseif (isset($id)) 	{
	$id = \Zage\App\Util::antiInjection($id);
}else{
	\Zage\App\Erro::halt($tr->trans('Falta de Parâmetros'));
}

#################################################################################
## Descompacta o ID
#################################################################################
\Zage\App\Util::descompactaId($id);

#################################################################################
## Verifica se o usuário tem permissão no menu
#################################################################################
$system->checaPermissao($_codMenu_);

#################################################################################
## Resgata as variáveis postadas
#################################################################################
if (isset($_GET['codFormando'])) 		$codFormando		= \Zage\App\Util::antiInjection($_GET['codFormando']);

################################################################################
# Resgata as informações do banco
################################################################################
try {
	$info = \Zage\Fmt\Convite::listaConviteAptoVenda();
} catch ( \Exception $e ) {
	\Zage\App\Erro::halt ( $e->getMessage () );
}

echo '<table id="layRegTableConviteID" class="table table-striped table-bordered table-hover">
<thead>
<tr>
<th class="col-sm-1 center"></th>
<th class="col-sm-3 center">EVENTO</th>
<th class="col-sm-3 center">VALOR</th>
<th class="col-sm-1 center">DISPONÍVEL</th>
<th class="col-sm-1 center">QUANTIDADE</th>
<th class="col-sm-2 center">TOTAL</th>
</tr>
</thead>';

for ($i = 0; $i < sizeof($info); $i++) {
	$codEvento		 = ($info[$i]->getCodTipoEvento()) ? $info[$i]->getCodTipoEvento()->getCodigo() : null;
	$eventoDesc		 = ($info[$i]->getCodTipoEvento()) ? $info[$i]->getCodTipoEvento()->getDescricao() : null;
	$taxaConv		 = ($info[$i]->getTaxaConveniencia()) ? $info[$i]->getTaxaConveniencia() : null;
	$valor			 = ($info[$i]->getValor()) ? $info[$i]->getValor() : null;
	$dataCadastro	 = ($info[$i]->getDataCadastro() != null) ? $info[$i]->getDataCadastro()->format($system->config["data"]["datetimeSimplesFormat"]) : null;
	$convDis		 = null;
	
	if (isset($codFormando) && !empty($codFormando)) {
		$convDis	= \Zage\Fmt\Convite::listaConviteDispFormando($codFormando, $codEvento);

		if(empty($convDis) || $convDis == 0) {
			$oConf = $em->getRepository('Entidades\ZgfmtConviteExtraConf')->findOneBy(array('codTipoEvento' => $codEvento));
			$convDis = $oConf->getQtdeMaxAluno();
		}
	}else{
		$convDis 	= " - ";
	}
	
	$html .= "<tr class=\"center\"><td class=\"center\" style=\"width: 20px;\"><div class=\"inline\" zg-type=\"zg-div-msg\"></div></td>
				<td>".$eventoDesc."<input type='hidden' name='codTipoEvento[".$i."]' value='".$codEvento."' ></td>
				<td>".$valor."<input type='hidden' name='valor[]' value='".$valor."' ></td>
				<td>".$convDis."<input type='hidden' name='quantDisp[]' value='".$convDis."' ></td>
				<td><input type='text' name='quantConv[]' id='quantConv' value='0' size='2' onchange='zgCalcularTotal();'></td>
				<td><div name='total[".$i."]' zg-name=\"total\">R$ 0,00</div><input type='hidden' name='total[".$i."]' value='0'><input type='hidden' name='codConvExtra[]' value='".$info[$i]->getCodigo()."'></td></tr>";
}

$html .= "<tr><td></td><td></td><td></td><td></td><td></td><td><div id='valorTotalID' name='valorTotal'>TOTAL: R$ 0,00</div></td></table>";

echo $html;