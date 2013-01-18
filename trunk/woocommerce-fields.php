<?php
/*
Plugin Name: WooCommerce Fields
Plugin URI: http://wooplugins.com.br/loja/woocommerce-fields/
Description: Customiza a página de checkout e de endereço e coloca validações javascript nos campos.
Version: 2.0
Author: Felipe Matos <chucky_ath@yahoo.com.br>
Author URI: http://felipematos.com
Requires at least: 3.4
Tested up to: 3.5
*/


//-------------------------------------------------------------------------------------------
// ##### PLUGIN AUTO UPDATE CODE #####
//-------------------------------------------------------------------------------------------

//Making sure wordpress does not check this plugin into their repository
add_filter( 'http_request_args', 'wc_fields_prevent_update_check', 10, 2 );
function wc_fields_prevent_update_check( $r, $url ) {
    if ( 0 === strpos( $url, 'http://api.wordpress.org/plugins/update-check/' ) ) {
        $my_plugin = plugin_basename( __FILE__ );
        $plugins = unserialize( $r['body']['plugins'] );
        unset( $plugins->plugins[$my_plugin] );
        unset( $plugins->active[array_search( $my_plugin, $plugins->active )] );
        $r['body']['plugins'] = serialize( $plugins );
    }
    return $r;
}


// TEMP: Enable update check on every request. Normally you don't need this! This is for testing only!
// NOTE: The 
//	if (empty($checked_data->checked))
//		return $checked_data; 
// lines will need to be commented in the check_for_plugin_update function as well.
get_site_transient( 'update_plugins' ); // unset the plugin
set_site_transient( 'update_plugins', '' ); // reset plugin database information
// TEMP: Show which variables are being requested when query plugin API
//add_filter('plugins_api_result', 'wc_fields_result', 10, 3);
//function wc_fields_result($res, $action, $args) {
//	print_r($res);
//	return $res;
//}
// NOTE: All variables and functions will need to be prefixed properly to allow multiple plugins to be updated

$api_url = 'http://update.wooplugins.com.br';
$plugin_slug = basename(dirname(__FILE__));

// Take over the update check
add_filter('pre_set_site_transient_update_plugins', 'wc_fields_check_for_plugin_update');

function wc_fields_check_for_plugin_update($checked_data) {
	global $api_url, $plugin_slug;
	
	//Comment out these two lines during testing.
	if (empty($checked_data->checked))
		return $checked_data;
	
	$args = array(
		'slug' => $plugin_slug,
		'version' => $checked_data->checked[$plugin_slug .'/'. $plugin_slug .'.php'],
	);
	$request_string = array(
			'body' => array(
				'action' => 'basic_check', 
				'request' => serialize($args),
				'api-key' => md5(get_bloginfo('url'))
			),
			'user-agent' => 'WordPress/' . $wp_version . '; ' . get_bloginfo('url')
		);
	
	// Start checking for an update
	$raw_response = wp_remote_post($api_url, $request_string);
	
	if (!is_wp_error($raw_response) && ($raw_response['response']['code'] == 200))
		$response = unserialize($raw_response['body']);
	
	if (is_object($response) && !empty($response)) // Feed the update data into WP updater
		$checked_data->response[$plugin_slug .'/'. $plugin_slug .'.php'] = $response;
	
	return $checked_data;
}


// Take over the Plugin info screen
add_filter('plugins_api', 'wc_fields_plugin_api_call', 10, 3);

function wc_fields_plugin_api_call($def, $action, $args) {
	global $plugin_slug, $api_url;
	
	if ($args->slug != $plugin_slug)
		return false;
	
	// Get the current version
	$plugin_info = get_site_transient('update_plugins');
	$current_version = $plugin_info->checked[$plugin_slug .'/'. $plugin_slug .'.php'];
	$args->version = $current_version;
	
	$request_string = array(
			'body' => array(
				'action' => $action, 
				'request' => serialize($args),
				'api-key' => md5(get_bloginfo('url'))
			),
			'user-agent' => 'WordPress/' . $wp_version . '; ' . get_bloginfo('url')
		);
	
	$request = wp_remote_post($api_url, $request_string);
	
	if (is_wp_error($request)) {
		$res = new WP_Error('plugins_api_failed', __('An Unexpected HTTP Error occurred during the API request.</p> <p><a href="?" onclick="document.location.reload(); return false;">Try again</a>'), $request->get_error_message());
	} else {
		$res = unserialize($request['body']);
		
		if ($res === false)
			$res = new WP_Error('plugins_api_failed', __('An unknown error occurred'), $request['body']);
	}
	
	return $res;
}
//-------------------------------------------------------------------------------------------
// ##### PLUGIN AUTO UPDATE CODE #####
//-------------------------------------------------------------------------------------------




add_action('wp_head', 'wc_fields_add_js');

function wc_fields_add_js(){
	//$plugin_url = get_settings('home').'/wp-content/plugins/'.dirname(plugin_basename(__FILE__));
	//wp_enqueue_script('loadjs', $plugin_url . '/example.js');
	
	if(is_page('finalizar-compra') || is_page('checkout')){
		$plugin_url = plugin_dir_url(__FILE__) . 'woocommerce_checkout.js';
	}
	if(is_page('editar-endereco') || is_page('edit-address')){
		$plugin_url = plugin_dir_url(__FILE__) . 'woocommerce_edit_address.js';
	}
	
	wp_enqueue_script('loadjs', $plugin_url);
	//echo $plugin_url;
}


//-----------------------------------------------------------------------------------------
// ### UPDATE AND SHOW CUSTOM USER INFO ###
//-----------------------------------------------------------------------------------------
add_action( 'show_user_profile', 'my_show_extra_profile_fields' );
add_action( 'edit_user_profile', 'my_show_extra_profile_fields' );

function my_show_extra_profile_fields( $user ) { 
	?>
	<h3>WooCommerce Extra Fields</h3>
	<table class="form-table">
		<tr>
			<th><label for="billing_cpf">CPF</label></th>
			<td>
				<input type="text" name="billing_cpf" id="billing_cpf" value="<?php echo esc_attr( get_the_author_meta( 'billing_cpf', $user->ID ) ); ?>" class="regular-text" />
				<br/>
				<span class="description">Digite o seu CPF.</span>
			</td>
		</tr>
		<tr>
			<th><label for="billing_rg">RG</label></th>
			<td>
				<input type="text" name="billing_rg" id="billing_rg" value="<?php echo esc_attr( get_the_author_meta( 'billing_rg', $user->ID ) ); ?>" class="regular-text" />
				<br/>
				<span class="description">Digite o seu RG.</span>
			</td>
		</tr>
		<tr>
			<th><label for="billing_nascimento">Data de Nascimento</label></th>
			<td>
				<input type="text" name="billing_nascimento" id="billing_nascimento" value="<?php echo esc_attr( get_the_author_meta( 'billing_nascimento', $user->ID ) ); ?>" class="regular-text" />
				<br/>
				<span class="description">Digite sua data de nascimento</span>
			</td>
		</tr>
		<tr>
			<th><label for="billing_number">Número da Casa</label></th>
			<td>
				<input type="text" name="billing_number" id="billing_number" value="<?php echo esc_attr( get_the_author_meta( 'billing_number', $user->ID ) ); ?>" class="regular-text" />
				<br/>
				<span class="description">Digite o número da sua casa</span>
			</td>
		</tr>
		<tr>
			<th><label for="billing_district">Bairro</label></th>
			<td>
				<input type="text" name="billing_district" id="billing_district" value="<?php echo esc_attr( get_the_author_meta( 'billing_district', $user->ID ) ); ?>" class="regular-text" />
				<br/>
				<span class="description">Digite o seu bairro</span>
			</td>
		</tr>
		<tr>
			<th><label for="billing_cellphone">Celular</label></th>
			<td>
				<input type="text" name="billing_cellphone" id="billing_cellphone" value="<?php echo esc_attr( get_the_author_meta( 'billing_cellphone', $user->ID ) ); ?>" class="regular-text" />
				<br/>
				<span class="description">Digite o seu celular</span>
			</td>
		</tr>
		<tr>
	</table>
	<?php 
}

add_action( 'personal_options_update', 'my_save_extra_profile_fields' );
add_action( 'edit_user_profile_update', 'my_save_extra_profile_fields' );

function my_save_extra_profile_fields( $user_id ) {
	if ( !current_user_can( 'edit_user', $user_id ) ) return false;
	
	/* Copy and paste this line for additional fields. Make sure to change 'twitter' to the field ID. */
	update_usermeta( $user_id, 'billing_rg', $_POST['billing_rg'] );
	update_usermeta( $user_id, 'billing_cpf', $_POST['billing_cpf'] );
	update_usermeta( $user_id, 'billing_nascimento', $_POST['billing_nascimento'] );
	update_usermeta( $user_id, 'billing_number', $_POST['billing_number'] );
	update_usermeta( $user_id, 'billing_district', $_POST['billing_district'] );
	update_usermeta( $user_id, 'billing_cellphone', $_POST['billing_cellphone'] );
}
//-----------------------------------------------------------------------------------------
// ### UPDATE AND SHOW CUSTOM USER INFO ###
//-----------------------------------------------------------------------------------------





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