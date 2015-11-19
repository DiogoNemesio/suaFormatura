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
	$eventoConfApto = \Zage\Fmt\Convite::listaConviteAptoVenda();
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

for ($i = 0; $i < sizeof($eventoConfApto); $i++) {
	$codEvento	 	= ($eventoConfApto[$i]->getCodEvento()) ? $eventoConfApto[$i]->getCodEvento()->getCodigo() : null;
	$eventoDesc		= ($eventoConfApto[$i]->getCodEvento()) ? $eventoConfApto[$i]->getCodEvento()->getCodTipoEvento()->getDescricao() : null;
	$valor			= ($eventoConfApto[$i]->getValor()) ? \Zage\App\Util::formataDinheiro($eventoConfApto[$i]->getValor()) : null;
	$log->info($eventoConfApto[$i]->getCodigo());
	if (isset($codFormando) && !empty($codFormando)) {
		$qtdeDisponivel	= \Zage\Fmt\Convite::qtdeConviteDispFormando($codFormando, $eventoConfApto[$i]->getCodEvento());
		
		if(empty($qtdeDisponivel) || $qtdeDisponivel < 0) {
			$qtdeDisponivel = 0;
			$readonly = "readonly";
		}else{
			$readonly = "";
		}
	
	}
	
	$html .= "<tr class=\"center\"><td class=\"center\" style=\"width: 20px;\"><div class=\"inline\" zg-type=\"zg-div-msg\"></div></td>
				<td>".$eventoDesc."<input type='hidden' name='codEvento[]' value='".$codEvento."'></td>
				<td>R$ ".$valor."<input type='hidden' name='valor[]' value='".$valor."' ></td>
				<td>".$qtdeDisponivel."<input type='hidden' name='quantDisp[]' value='".$qtdeDisponivel."'></td>
				<td><input type='text' name='quantConv[]' id='quantConv' value='' ".$readonly." size='2' zg-data-toggle=\"mask\" zg-data-mask=\"numero\" onchange='zgCalcularTotal();zgValidaQuantDisp();'></td>
				<td><div name='total[".$i."]' zg-name=\"total\">R$ 0,00</div><input type='hidden' name='total[]' value='0'><input type='hidden' name='codConvExtra[]' value='".$eventoConfApto[$i]->getCodigo()."'></td></tr>";
	}

$html 	.= "<tr><td colspan='5' align=\"right\">TAXA DE CONVENIÊNCIA</td><td class=\"center\"><div id='valorConvenienciaID'></div></td>";
$html 	.= "<tr><td colspan='5' align=\"right\"><strong>TOTAL</strong></td><td class=\"center\"><div id='valorTotalID' name='valorTotal'><strong>R$ 0,00</strong></div></td></table>";

$html	.= '<script type="text/javascript" charset="%CHARSET%">';
$html	.= "$('[data-rel=popover]').popover({html:true});";
$html	.= "$('[data-rel=select2]').css('width','100%').select2({allowClear:true});";
$html	.= "$('[zg-data-toggle="."mask"."]').each(function( index ) {";
$html	.= "zgMask($( this ), $( this ).attr('zg-data-mask'));";
$html	.= "});";

$html	.= '</script>';

echo $html;