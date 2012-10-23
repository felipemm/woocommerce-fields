<?php
/*
Plugin Name: WooCommerce Fields
Plugin URI: http://felipematos.com/loja
Description: Customiza a página de checkout e de endereço.
Version: 1.0
Author: Felipe Matos <chucky_ath@yahoo.com.br>
Author URI: http://felipematos.com
Requires at least: 3.4
Tested up to: 3.4.2
*/

add_filter( 'woocommerce_billing_fields' , 'custom_override_billing_fields' );

function custom_override_billing_fields($fields){
	unset($fields['billing_company']);

	$fields['billing_address_1']['label'] = 'Endereço';
	$fields['billing_address_2']['label'] = 'Complemento';
	
	//cria novos campos
	$fields['billing_number'] = array(
		'label'         => __('Número', 'woocommerce'),
		'placeholder'   => _x('Número da casa, apto', 'placeholder', 'woocommerce'),
		'required'      => true,
		'class'         => array('form-row-first'),
		'clear'         => false
     );

	$fields['billing_district'] = array(
		'label'         => __('Bairro', 'woocommerce'),
		'placeholder'   => _x('Bairro', 'placeholder', 'woocommerce'),
		'required'      => true,
		'class'         => array('form-row-last'),
		'clear'         => true
     );
	
	return $fields;
}


add_filter( 'woocommerce_checkout_fields' , 'custom_override_checkout_fields' );

function custom_override_checkout_fields( $fields ) {
	//remove o campo de notas do pedido
	unset($fields['order']['order_comments']);
	unset($fields['billing']['billing_company']);
	//altera o label do campo de endereço
	$fields['billing']['billing_address_1']['label'] = 'Endereço';
	$fields['billing']['billing_address_2']['label'] = 'Complemento';
	
	//cria novos campos
	$fields['billing']['billing_number'] = array(
		'label'         => __('Número', 'woocommerce'),
		'placeholder'   => _x('Número da casa, apto', 'placeholder', 'woocommerce'),
		'required'      => true,
		'class'         => array('form-row-first'),
		'clear'         => false
     );

	$fields['billing']['billing_district'] = array(
		'label'         => __('Bairro', 'woocommerce'),
		'placeholder'   => _x('Bairro', 'placeholder', 'woocommerce'),
		'required'      => true,
		'class'         => array('form-row-last'),
		'clear'         => true
     );

	return $fields;
}



add_action('woocommerce_checkout_process', 'my_custom_checkout_field_process');
 
function my_custom_checkout_field_process() {
    global $woocommerce;
 
    // Check if set, if its not set add an error.
    if (!validaCep($_POST['billing_postcode'])){
		$woocommerce->add_error( __('CEP inválido. Formato deve ser 99999-999.') );
	}
		 
	if (!validaTelefone($_POST['billing_phone'])){
		$woocommerce->add_error( __('Telefone inválido. Formato (xx)xxxx-xxxx ou (xx)9xxxx-xxxx.') );
	}
}



function validaCep($cep){
	$cep = trim($cep);
	$avaliaCep = ereg("^[0-9]{5}-[0-9]{3}$", $cep);
	return $avaliaCep;
}
function validaTelefone($telefone){
	$telefone = trim($telefone);
	$avalia = ereg("^\([0-9]{2}\)[0-9]{4}-[0-9]{4}$", $telefone);
	if($avalia == false) $avalia = ereg("^\([0-9]{2}\)[0-9]{5}-[0-9]{4}$", $telefone);
	return $avalia;
}


?>