function isNumeric(sText) {
   var ValidChars = "0123456789.";
   var IsNumber=true;
   var Char;

   for (i = 0; i < sText.length && IsNumber == true; i++) { 
      Char = sText.charAt(i); 
      if (ValidChars.indexOf(Char) == -1) {
         IsNumber = false;
      }
   }
   return IsNumber;
}

function isMoney(sText) {
	var pattern = /(^\d{1,3}(\.?\d{3})*(.\d{2})?$)|(^\d{1,3}(.?\d{3})*(\,\d{2})?$)/ ;
	if (pattern.test(sText)) {
    	return true;
    } 
    return false;
}

function isDataBR(sText) {
	var pattern = /^(0?[1-9]|[12][0-9]|3[01])[\/\-](0?[1-9]|1[012])[\/\-]\d{4}$/;
	if (pattern.test(sText)) {
    	return true;
    } 
    return false;
}

function validaCep(sText) {
	if (sText.length != 8) {
		return false;
	}else if (!isNumeric(sText)) {
		return false;
	}
	
	return true;
}


function lpad (str,len,pad) {
  pad = pad || ' ';
  while(str.length < len) str = pad + str;
  return str;
}

function rpad (str,len,pad) {
  pad = pad || ' ';
  while(str.length < len) str = str + pad;
  return str;
}

function checaRetornoOK (mensagem) {
	if (mensagem.charAt(0) == "0") {
		return true;
	}else{
		return false;
	}
}

function zgGetMsgRetorno (pStr) {
	var vTemp,vArray;
	
	vTemp	= $.base64.decode(pStr.substring(1));
	vArray	= vTemp.split("|");
	vMsg	= vArray[2];
	
	return vMsg;
}

function zgGetCodRetorno (pStr,pIndex) {
	var vTemp,vArray,vIndex;
	
	if (pIndex === undefined) {
		vIndex	= 1;
	}else{
		vIndex  = pIndex;
	}
	
	vTemp	= $.base64.decode(pStr.substring(1));
	vArray	= vTemp.split("|");
	vCod	= vArray[vIndex];
	
	return vCod;
}

function zgEncodeUrl(pStr) {
	return $.base64.encode(pStr);
}

function zgDecodeUrl(pStr) {
	return $.base64.decode(pStr);
}

function zgMostraErro(pDiv,pMsg) {
	$('#'+pDiv).html('<div class="alert alert-danger"><button type="button" class="close" data-dismiss="alert"><i class="fa fa-times"></i></button>'+pMsg+'</div>');
}

function zgMostraMsg(pDiv,pMsg) {
	$('#'+pDiv).html('<div class="alert alert-info"><button type="button" class="close" data-dismiss="alert"><i class="fa fa-times"></i></button>'+pMsg+'</div>');
}


function mostraMensagem(msg) {
	var url = 'msg.php?mensagem='+msg;
	$('#zgDivMsgID').load(url,function(){
	    $(this).modal({
	        keyboard:true,
	        backdrop:true
	    });
	}).modal('show');
}

function removenull(str) {
    var new_str = str;
    if (str == '') {
        new_str = str.replace('', " - ");
    }
    else if (str == null) {
        new_str = " - ";
    }
    return new_str;
}

/** Funções de validação **/
function validarCNPJ(cnpj) {
	 
    cnpj = cnpj.replace(/[^\d]+/g,'');
 
    if (cnpj == '') 			return false;
    if (cnpj.length != 14)      return false;
 
    // Elimina CNPJs invalidos conhecidos
    if (cnpj == "00000000000000" ||
        cnpj == "11111111111111" ||
        cnpj == "22222222222222" ||
        cnpj == "33333333333333" ||
        cnpj == "44444444444444" ||
        cnpj == "55555555555555" ||
        cnpj == "66666666666666" ||
        cnpj == "77777777777777" ||
        cnpj == "88888888888888" ||
        cnpj == "99999999999999")
        return false;
         
    // Valida DVs
    tamanho = cnpj.length - 2;
    numeros = cnpj.substring(0,tamanho);
    digitos = cnpj.substring(tamanho);
    soma 	= 0;
    pos 	= tamanho - 7;
    
    for (i = tamanho; i >= 1; i--) {
      soma += numeros.charAt(tamanho - i) * pos--;
      if (pos < 2)	pos = 9;
    }
    resultado = soma % 11 < 2 ? 0 : 11 - soma % 11;
    if (resultado != digitos.charAt(0)) return false;
         
    tamanho = tamanho + 1;
    numeros = cnpj.substring(0,tamanho);
    soma = 0;
    pos = tamanho - 7;
    for (i = tamanho; i >= 1; i--) {
      soma += numeros.charAt(tamanho - i) * pos--;
      if (pos < 2) pos = 9;
    }
    resultado = soma % 11 < 2 ? 0 : 11 - soma % 11;
    if (resultado != digitos.charAt(1)) return false;

    return true;
    
}

function validarCPF(cpf) {
	 
    cpf = cpf.replace(/[^\d]+/g,'');
    
    if (cpf == '') 			return false;
    if (cpf.length != 11)   return false;
 
    // Elimina CPFs invalidos conhecidos
    if (cpf == "00000000000" ||
    	cpf == "11111111111" ||
    	cpf == "22222222222" ||
    	cpf == "33333333333" ||
    	cpf == "44444444444" ||
    	cpf == "55555555555" ||
    	cpf == "66666666666" ||
    	cpf == "77777777777" ||
    	cpf == "88888888888" ||
    	cpf == "99999999999")
        return false;
         
    // Valida DVs
    tamanho = cpf.length - 2;
    numeros = cpf.substring(0,tamanho);
    digitos = cpf.substring(tamanho);
    soma 	= 0;
    
    for (i = 10; i > 1; i--)
        soma += numeros.charAt(10 - i) * i;
    resultado = soma % 11 < 2 ? 0 : 11 - soma % 11;
    if (resultado != digitos.charAt(0))
    	return false;
    numeros = cpf.substring(0,10);
    soma = 0;
    for (i = 11; i > 1; i--)
        soma += numeros.charAt(11 - i) * i;
    resultado = soma % 11 < 2 ? 0 : 11 - soma % 11;
    if (resultado != digitos.charAt(1))
        return false;
    return true;
    
}

function validarEmail(email) {
	var re = /\S+@\S+\.\S+/;
    return re.test(email);
}


function zgValidaNumero($str) {
	return $str - parseFloat($str) >= 0;
}
