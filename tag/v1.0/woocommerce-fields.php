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

	$new_fields = array(
		'billing_first_name' => array( 
			'default'		=>true,
			'enabled'		=> true,
			'label' 		=> __('First Name', 'woocommerce'), 
			'placeholder' 	=> _x('First Name', 'placeholder', 'woocommerce'), 
			'required' 		=> true, 
			'class'			=> array('form-row-first'),
			'type'			=>'text'
		),
		'billing_last_name' => array( 
			'default'		=>true,
			'enabled'		=> true,
			'label' 		=> __('Last Name', 'woocommerce'), 
			'placeholder' 	=> _x('Last Name', 'placeholder', 'woocommerce'), 
			'required' 		=> true, 
			'class' 		=> array('form-row-last'),
			'type'			=>'text',
			'clear'			=> true
		),	
		//==================================================================
		'billing_cpf' 		=> array(
			'default'		=>true,
			'enabled'		=> true,
			'label' 		=> __('CPF', 'woocommerce'), 
			'placeholder' 	=> _x('999.999.999-99', 'placeholder', 'woocommerce'), 
			'required' 		=> true, 
			'class' 		=> array('form-row-first'),
			'clear'			=> false
		),	
		'billing_rg' 		=> array(
			'default'		=>true,
			'enabled'		=> true,
			'label' 		=> __('RG', 'woocommerce'), 
			'placeholder' 	=> _x('digite o RG', 'placeholder', 'woocommerce'), 
			'required' 		=> true, 
			'class' 		=> array('form-row-last'),
			'clear'			=> true
		),
		//==================================================================
		'billing_nascimento'=> array(
			'default'		=>true,
			'enabled'		=> true,
			'label' 		=> __('Data Nascimento', 'woocommerce'), 
			'placeholder' 	=> _x('dd/mm/aaaa', 'placeholder', 'woocommerce'), 
			'required' 		=> true, 
			'class' 		=> array('form-row-first'),
			'clear'			=> false
		),
		'billing_email'     => array(
			'default'		=>true,
			'enabled'		=> true,
			'label' 		=> __('Email Address', 'woocommerce'), 
			'placeholder' 	=> _x('Email Address', 'placeholder', 'woocommerce'), 
			'required' 		=> true, 
			'class' 		=> array('form-row-last'),
			'clear'			=> true
		),
		//==================================================================
		'billing_address_1' => array( 
			'default'		=>true,
			'enabled'		=> true,
			'label' 		=> __('Address', 'woocommerce'), 
			'placeholder' 	=> _x('Address', 'placeholder', 'woocommerce'), 
			'required' 		=> true, 
			'class' 		=> array('form-row-first'),
		),
		'billing_address_2' => array( 
			'default'		=>true,
			'enabled'		=> true,
			'label' 		=> __('Complemento', 'woocommerce'), 
			'placeholder' 	=> _x('Complemento', 'placeholder', 'woocommerce'), 
			'class' 		=> array('form-row-last'), 
			'clear'			=> true
		),
		//==================================================================
		'billing_number'    => array(
			'label'         => __('Número', 'woocommerce'),
			'placeholder'   => _x('Número da casa, apto', 'placeholder', 'woocommerce'),
			'required'      => true,
			'class'         => array('form-row-first'),
			'clear'         => false
		),
		'billing_district'  => array(
			'label'         => __('Bairro', 'woocommerce'),
			'placeholder'   => _x('Bairro', 'placeholder', 'woocommerce'),
			'required'      => true,
			'class'         => array('form-row-last'),
			'clear'         => true
		),
		//==================================================================
		'billing_postcode' 	=> array( 
			'default'		=>true,
			'enabled'		=> true,
			'label' 		=> __('Postcode/Zip', 'woocommerce'), 
			'placeholder' 	=> _x('Postcode/Zip', 'placeholder', 'woocommerce'), 
			'required' 		=> true, 
			'class'			=> array('form-row-first', 'update_totals_on_change'),
			'clear'			=> false
		),
		'billing_city' 		=> array( 
			'default'		=>true,
			'enabled'		=> true,
			'label' 		=> __('Town/City', 'woocommerce'), 
			'placeholder' 	=> _x('Town/City', 'placeholder', 'woocommerce'), 
			'required' 		=> true, 
			'class' 		=> array('form-row-last'),
			'clear'			=> true
		),
		//==================================================================
		'billing_country' 	=> array( 
			'default'		=>true,
			'enabled'		=> true,
			'type'			=> 'country', 
			'label' 		=> __('Country', 'woocommerce'), 
			'placeholder' 	=> _x('Country', 'placeholder', 'woocommerce'), 
			'required' 		=> true, 
			'class' 		=> array('form-row-first', 'update_totals_on_change', 'country_select'),
			'label_css'		=> array('form-row-first', 'update_totals_on_change', 'country_select')
		),
		'billing_state' 	=> array( 
			'default'		=>true,
			'enabled'		=> true,
			'type'			=> 'state', 
			'label' 		=> __('State/County', 'woocommerce'), 
			'placeholder' 	=> _x('State/County', 'placeholder', 'woocommerce'), 
			'required' 		=> true, 
			'class' 		=> array('form-row-last', 'update_totals_on_change'),
			'clear'			=> true
		),	
		//==================================================================
		'billing_phone' 	=> array(
			'default'		=>true,
			'enabled'		=> true,
			'label' 		=> __('Telefone Principal', 'woocommerce'), 
			'placeholder' 	=> _x('(99)9999-9999', 'placeholder', 'woocommerce'), 
			'required' 		=> true, 
			'class' 		=> array('form-row-first'),
			'clear'			=> false
		),	
		'billing_cellphone' => array(
			'default'		=>true,
			'enabled'		=> true,
			'label' 		=> __('Celular', 'woocommerce'), 
			'placeholder' 	=> _x('(99)9999-9999', 'placeholder', 'woocommerce'), 
			'required' 		=> false, 
			'class' 		=> array('form-row-last'),
			'clear'			=> true
		)
	);		
	return $new_fields;

	/*
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
	*/
}


add_filter( 'woocommerce_checkout_fields' , 'custom_override_checkout_fields' );

function custom_override_checkout_fields( $fields ) {

	$new_fields = array(
		'billing_first_name' => array( 
			'default'		=>true,
			'enabled'		=> true,
			'label' 		=> __('First Name', 'woocommerce'), 
			'placeholder' 	=> _x('First Name', 'placeholder', 'woocommerce'), 
			'required' 		=> true, 
			'class'			=> array('form-row-first'),
			'type'			=>'text'
		),
		'billing_last_name' => array( 
			'default'		=>true,
			'enabled'		=> true,
			'label' 		=> __('Sobrenome', 'woocommerce'), 
			'placeholder' 	=> _x('Sobrenome', 'placeholder', 'woocommerce'), 
			'required' 		=> true, 
			'class' 		=> array('form-row-last'),
			'type'			=>'text',
			'clear'			=> true
		),	
		//==================================================================
		'billing_cpf' 		=> array(
			'default'		=>true,
			'enabled'		=> true,
			'label' 		=> __('CPF', 'woocommerce'), 
			'placeholder' 	=> _x('999.999.999-99', 'placeholder', 'woocommerce'), 
			'required' 		=> true, 
			'class' 		=> array('form-row-first'),
			'clear'			=> false
		),	
		'billing_rg' 		=> array(
			'default'		=>true,
			'enabled'		=> true,
			'label' 		=> __('RG', 'woocommerce'), 
			'placeholder' 	=> _x('digite o RG', 'placeholder', 'woocommerce'), 
			'required' 		=> true, 
			'class' 		=> array('form-row-last'),
			'clear'			=> true
		),
		//==================================================================
		'billing_nascimento'=> array(
			'default'		=>true,
			'enabled'		=> true,
			'label' 		=> __('Data Nascimento', 'woocommerce'), 
			'placeholder' 	=> _x('dd/mm/aaaa', 'placeholder', 'woocommerce'), 
			'required' 		=> true, 
			'class' 		=> array('form-row-first'),
			'clear'			=> false
		),
		'billing_email'     => array(
			'default'		=>true,
			'enabled'		=> true,
			'label' 		=> __('Email Address', 'woocommerce'), 
			'placeholder' 	=> _x('Email Address', 'placeholder', 'woocommerce'), 
			'required' 		=> true, 
			'class' 		=> array('form-row-last'),
			'clear'			=> true
		),
		//==================================================================
		'billing_address_1' => array( 
			'default'		=>true,
			'enabled'		=> true,
			'label' 		=> __('Address', 'woocommerce'), 
			'placeholder' 	=> _x('Address', 'placeholder', 'woocommerce'), 
			'required' 		=> true, 
			'class' 		=> array('form-row-first'),
		),
		'billing_number'    => array(
			'label'         => __('Número', 'woocommerce'),
			'placeholder'   => _x('Número da casa, apto', 'placeholder', 'woocommerce'),
			'required'      => true,
			'class'         => array('form-row-last'),
			'clear'         => true
		),
		//==================================================================
		'billing_address_2' => array( 
			'default'		=>true,
			'enabled'		=> true,
			'label' 		=> __('Complemento', 'woocommerce'), 
			'placeholder' 	=> _x('Complemento', 'placeholder', 'woocommerce'), 
			'class' 		=> array('form-row-first'), 
			'clear'			=> false
		),
		'billing_district'  => array(
			'label'         => __('Bairro', 'woocommerce'),
			'placeholder'   => _x('Bairro', 'placeholder', 'woocommerce'),
			'required'      => true,
			'class'         => array('form-row-last'),
			'clear'         => true
		),
		//==================================================================
		'billing_postcode' 	=> array( 
			'default'		=>true,
			'enabled'		=> true,
			'label' 		=> __('Postcode/Zip', 'woocommerce'), 
			'placeholder' 	=> _x('Postcode/Zip', 'placeholder', 'woocommerce'), 
			'required' 		=> true, 
			'class'			=> array('form-row-first', 'update_totals_on_change'),
			'clear'			=> false
		),
		'billing_city' 		=> array( 
			'default'		=>true,
			'enabled'		=> true,
			'label' 		=> __('Town/City', 'woocommerce'), 
			'placeholder' 	=> _x('Town/City', 'placeholder', 'woocommerce'), 
			'required' 		=> true, 
			'class' 		=> array('form-row-last'),
			'clear'			=> true
		),
		//==================================================================
		'billing_country' 	=> array( 
			'default'		=>true,
			'enabled'		=> true,
			'type'			=> 'country', 
			'label' 		=> __('Country', 'woocommerce'), 
			'placeholder' 	=> _x('Country', 'placeholder', 'woocommerce'), 
			'required' 		=> true, 
			'class' 		=> array('form-row-first', 'update_totals_on_change', 'country_select'),
			'label_css'		=> array('form-row-first', 'update_totals_on_change', 'country_select')
		),
		'billing_state' 	=> array( 
			'default'		=>true,
			'enabled'		=> true,
			'type'			=> 'state', 
			'label' 		=> __('State/County', 'woocommerce'), 
			'placeholder' 	=> _x('State/County', 'placeholder', 'woocommerce'), 
			'required' 		=> true, 
			'class' 		=> array('form-row-last', 'update_totals_on_change'),
			'clear'			=> true
		),	
		//==================================================================
		'billing_phone' 	=> array(
			'default'		=>true,
			'enabled'		=> true,
			'label' 		=> __('Phone', 'woocommerce'), 
			'placeholder' 	=> _x('Phone', 'placeholder', 'woocommerce'), 
			'required' 		=> true, 
			'class' 		=> array('form-row-first'),
			'clear'			=> false
		),	
		'billing_cellphone' => array(
			'default'		=>true,
			'enabled'		=> true,
			'label' 		=> __('Celular', 'woocommerce'), 
			'placeholder' 	=> _x('(99)9999-9999', 'placeholder', 'woocommerce'), 
			'required' 		=> false, 
			'class' 		=> array('form-row-last'),
			'clear'			=> true
		)
	);	

	$fields['billing'] = $new_fields;
	unset($fields['order']['order_comments']);
	$fields['account']['account_password-2']['label'] = __('Confirme a Senha', 'woocommerce');
	return $fields;
	
	
	
	/*
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
	*/
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
	//if (!validaTelefone($_POST['billing_cellphone'])){
	//	$woocommerce->add_error( __('Celular inválido. Formato (xx)xxxx-xxxx ou (xx)9xxxx-xxxx.') );
	//}

	if (!validaCPF($_POST['billing_cpf'])){
		$woocommerce->add_error( __('CPF inválido. Formato xxx.xxx.xxx-xx.') );
	}
}



function validaCep($cep){
	$cep = trim($cep);
	$avaliaCep = preg_match("/^[0-9]{5}-[0-9]{3}$/", $cep);
	return $avaliaCep;
}
function validaTelefone($telefone){
	$telefone = trim($telefone);
	$avalia = preg_match("/^\([0-9]{2}\)[0-9]{4}-[0-9]{4}$/", $telefone);
	if($avalia == false) $avalia = preg_match("/^\([0-9]{2}\)[0-9]{5}-[0-9]{4}$/", $telefone);
	return $avalia;
}
function validaCPF($cpf){
	$cpf = trim($cpf);
	$avaliaCPF = preg_match("/^[0-9]{3}.[0-9]{3}.[0-9]{3}-[0-9]{2}$/", $cpf);
	return $avaliaCPF;
}

?>