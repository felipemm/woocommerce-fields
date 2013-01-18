function mascara(o,f){  
	v_obj=o  
	v_fun=f  
	setTimeout("execmascara()",1)  
}  
function execmascara(){  
	v_obj.value=v_fun(v_obj.value)  
}  
function mtel(v){  
	v=v.replace(/\D/g,"");             //Remove tudo o que não é dígito  
	v=v.replace(/^(\d{2})(\d)/g,"($1)$2"); //Coloca parênteses em volta dos dois primeiros dígitos  
	v=v.replace(/(\d)(\d{4})$/,"$1-$2");    //Coloca hífen entre o quarto e o quinto dígitos  
	return v;  
}  
//valida numero inteiro com mascara
function mascaraInteiro(el, event){
	if (event.keyCode < 48 || event.keyCode > 57){
		event.returnValue = false;
		return false;
	}
	return true;
}
//formata de forma generica os campos
function formataCampo(campo, Mascara, evento) {
	var boleanoMascara;
	var Digitato = evento.keyCode;
	exp = /\-|\.|\/|\(|\)| /g
	campoSoNumeros = campo.value.toString().replace( exp, '' );
	var posicaoCampo = 0;
	var NovoValorCampo='';
	var TamanhoMascara = campoSoNumeros.length;;
	if (Digitato != 8) { // backspace
		for(i=0; i<= TamanhoMascara; i++) {
			boleanoMascara  = ((Mascara.charAt(i) == '-') || (Mascara.charAt(i) == '.') || (Mascara.charAt(i) == '/'))
			boleanoMascara  = boleanoMascara || ((Mascara.charAt(i) == '(') || (Mascara.charAt(i) == ')') || (Mascara.charAt(i) == ' '))
			if (boleanoMascara) {
				NovoValorCampo += Mascara.charAt(i);
				TamanhoMascara++;
			}else {
				NovoValorCampo += campoSoNumeros.charAt(posicaoCampo);
				posicaoCampo++;
			}
		}
		campo.value = NovoValorCampo;
		return true;
	} else {
		return true;
	}
}
//função para validar a máscara do campo de cep
function MascaraCEP(cep,event){
	if(mascaraInteiro(cep,event)==false){
		event.returnValue = false;
	}
	return formataCampo(cep, '00000-000', event);
}
function MascaraCPF(cpf,event){
	if(mascaraInteiro(cpf,event)==false){
		event.returnValue = false;
	}
	return formataCampo(cpf, '000.000.000-00', event);
}
function MascaraData(data,event){
	if(mascaraInteiro(data,event)==false){
		event.returnValue = false;
	}
	return formataCampo(data, '00/00/0000', event);
}
//==============================================================================
//variáveis dos campos a serem alterados
var cep      = document.getElementById('billing_postcode');
var celular = document.getElementById('billing_cellphone');	     	
var telefone = document.getElementById('billing_phone');	     	
var cpf = document.getElementById('billing_cpf');	     	
var data_nasc= document.getElementById('billing_nascimento');	     	
//==============================================================================
//coloca as validações nos campos de cep e telefone
cep.maxLength=9;
cep.onkeypress= function(event){MascaraCEP(this, event);};
telefone.maxLength=14;
telefone.onkeydown= function(event){mascara(this,mtel);};
celular.maxLength=14;
celular.onkeydown= function(event){mascara(this,mtel);};
cpf.maxLength=14;
cpf.onkeypress= function(event){MascaraCPF(this,event);};
data_nasc.maxLength=10;
data_nasc.onkeypress= function(event){MascaraData(this,event);};