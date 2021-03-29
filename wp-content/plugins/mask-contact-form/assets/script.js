jQuery(document).ready(function(){
  jQuery('.date').mask('99/99/9999');
  jQuery('.tempo').mask('00:00:00');
  jQuery('.date_time').mask('00/00/0000 00:00:00');
  jQuery('.cep').mask('00000-000');
  jQuery('.phone').mask('0000-0000');
  jQuery('.phone-2').mask('+55 (00) 0000-00009');
  jQuery('.phone_us').mask('(000) 000-0000');
  jQuery('.mixed').mask('AAA 000-S0S');
  jQuery('.cpf').mask('000.000.000-00', {reverse: true});
  jQuery('.money').mask('000.000.000.000.000,00', {reverse: true});
  jQuery('.cnpj').mask('00.000.000/0000-00', {reverse: true});
  jQuery('.wpcf7-tel').mask(maskBehavior, options);

	var maskBehavior = function (val) {
	  return val.replace(/\D/g, '').length === 11 ? '(00) 00000-0000' : '(00) 0000-00009';
	},
	options = {onKeyPress: function(val, e, field, options) {
	        field.mask(maskBehavior.apply({}, arguments), options);
	    }
	};





});