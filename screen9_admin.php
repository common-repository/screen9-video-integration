<?php
if(is_admin()){
	add_action('admin_menu', 'add_plugin_page');
	add_action('admin_init', 'page_init');
}

function add_plugin_page(){
	add_options_page(__("Settings Screen9", SCREEN9_TEXT_DOMAIN), __("Settings Screen9", SCREEN9_TEXT_DOMAIN), 'manage_options', 'screen9-settings', 'create_screen9_admin');
}

function create_screen9_admin(){
	?>
<div class="wrap">
<?php screen_icon(); ?>
	<h2><?php _e('Settings Screen9', SCREEN9_TEXT_DOMAIN); ?></h2>
	<form method="post" action="options.php">
	<?php
	settings_fields('screen9_option_group');
	do_settings_sections('screen9-settings');
	?>
	<?php submit_button(); ?>
	</form>
</div>
	<?php
}

function page_init(){
	register_setting('screen9_option_group','screen9_customer_id');
	register_setting('screen9_option_group','screen9_user_id');
	register_setting('screen9_option_group','screen9_api_url');

	add_settings_section(
	    'screen9_credentials',
	    __("Credentials", SCREEN9_TEXT_DOMAIN),
	    '',
	    'screen9-settings'
	    );

	    add_settings_field(
	    'screen9_customer_id', 
	    'Customer-id:', 
	    'create_input_field', 
	    'screen9-settings',
	    'screen9_credentials',
	    array( 'field' => 'screen9_customer_id' )
	    );
	    add_settings_field(
	    'screen9_user_id', 
	    'User-id:', 
	    'create_input_field', 
	    'screen9-settings',
	    'screen9_credentials',
	    array( 'field' => 'screen9_user_id',
	    		'description' => __("Id of the screen9 user uploaded material should belong to", SCREEN9_TEXT_DOMAIN) )
	    );
	    add_settings_field(
	    'screen9_api_url', 
	    'Api-URL:', 
	    'create_input_field', 
	    'screen9-settings',
	    'screen9_credentials',
	    array( 'field' => 'screen9_api_url' )
	    );
}

function create_input_field($args){
	$field = $args['field'];
	$value = get_option($field);
	echo sprintf('<input type="text" name="%s" id="%s" value="%s" />', $field, $field, $value);
	if(isset($args['description'])){
		echo sprintf('<p class="description">%s</p>',$args['description']);
	} 
}
