=== Mask Contact Form ===
Contributors: Marcus
Donate link: Marcus
Tags: contact form, mask, input, contact form 7
Requires at least: 3.0.1
Tested up to: 4.9.1
Requires PHP: 5.2.4
Stable tag: 1.0
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html
 
Este plugin é adiciona mascara para os campos de formulário, sendo a mascara de telefone nativa compátivel com contact form 7 ele executa 1 operação.
 
== Description ==
 Este plugin adiciona mascara para os campos de formulário, sendo a mascara de telefone nativa compátivel com contact form 7 ele executa 1 operação.
Para a utilização do plugin deve utiliza umas das seguintes classes:
 
== Installation ==
 
Exemplo:
  jQuery('.date').mask('99/99/9999');
  jQuery('.tempo').mask('00:00:00');
  jQuery('.date_time').mask('00/00/0000 00:00:00');
  jQuery('.cep').mask('00000-000');
  jQuery('.phone').mask('0000-0000');
  jQuery('.phone-2').mask('(00) 0000-0000');
  jQuery('.phone_us').mask('(000) 000-0000');
  jQuery('.mixed').mask('AAA 000-S0S');
  jQuery('.cpf').mask('000.000.000-00', {reverse: true});
  jQuery('.money').mask('000.000.000.000.000,00', {reverse: true});
  jQuery('.cnpj').mask('00.000.000/0000-00', {reverse: true});
  jQuery('.wpcf7-tel').mask(maskBehavior, options);

Onde você deve informar no campo input uma dessas class, Exemplo:
 
1. Faça upload dos arquivos para a pasta `/wp-content/plugins/`  (mantenha a pasta original do plugin)

1. Ative o plugin na interface de 'Plugins' do WordPress

1. Para utilizar use as classes determinadas
 
== Frequently Asked Questions ==
  
== Screenshots ==
 
== Changelog ==
 
== Arbitrary section ==
 
== A brief Markdown Example ==

  jQuery('.date').mask('99/99/9999');
  jQuery('.tempo').mask('00:00:00');
  jQuery('.date_time').mask('00/00/0000 00:00:00');
  jQuery('.cep').mask('00000-000');
  jQuery('.phone').mask('0000-0000');
  jQuery('.phone-2').mask('(00) 0000-0000');
  jQuery('.phone_us').mask('(000) 000-0000');
  jQuery('.mixed').mask('AAA 000-S0S');
  jQuery('.cpf').mask('000.000.000-00', {reverse: true});
  jQuery('.money').mask('000.000.000.000.000,00', {reverse: true});
  jQuery('.cnpj').mask('00.000.000/0000-00', {reverse: true});
  jQuery('.wpcf7-tel').mask(maskBehavior, options);

<input type="tel" class="wpcf7-tel"> Onde nesse exemplo usamos a classe wpcf7-tel que é nátiva para o formulário de contato Contact Form 7 