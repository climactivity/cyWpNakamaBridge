<?php

/*
 
Plugin Name: Wordpress To Nakama Bridge
 
Plugin URI: https://app.climactivity.de 
 
Description: Connects to the game server 
Version: 0.0.1
 
Author: Climactivity
 
Author URI: https://github.com/climactivity
 
License: MIT
 
Text Domain: cy
 
*/

// -------------
// Settings Page
// -------------
add_action( 'admin_menu', 'cy_add_admin_menu' );
add_action( 'admin_init', 'cy_settings_init' );


function cy_add_admin_menu(  ) { 

	add_menu_page( 'Wordpress To Nakama Bridge', 'Wordpress To Nakama Bridge', 'manage_options', 'wordpress_to_nakama_bridge', 'cy_options_page' );

}


function cy_settings_init(  ) { 

	register_setting( 'pluginPage', 'cy_settings' );

	add_settings_section(
		'cy_pluginPage_section', 
		__( 'Setup connection to game server', 'cy' ), 
		'cy_settings_section_callback', 
		'pluginPage'
	);

	add_settings_field( 
		'cy_text_field_hostname', 
		__( 'Hostname', 'cy' ), 
		'cy_text_field_0_render', 
		'pluginPage', 
		'cy_pluginPage_section' 
	);

	add_settings_field( 
		'cy_text_field_server_key', 
		__( 'Server API Key', 'cy' ), 
		'cy_text_field_1_render', 
		'pluginPage', 
		'cy_pluginPage_section' 
	);


}


function cy_text_field_0_render(  ) { 

	$options = get_option( 'cy_settings' );
	?>
	<input type='text' name='cy_settings[cy_text_field_hostname]' value='<?php echo $options['cy_text_field_hostname']; ?>'>
	<?php

}


function cy_text_field_1_render(  ) { 

	$options = get_option( 'cy_settings' );
	?>
	<input type='text' name='cy_settings[cy_text_field_server_key]' value='<?php echo $options['cy_text_field_server_key']; ?>'>
	<?php

}


function cy_settings_section_callback(  ) { 

	echo __( 'Used to communicate with the game server', 'cy' );

}


function cy_options_page(  ) { 

		?>
		<form action='options.php' method='post'>

			<h2>Wordpress To Nakama Bridge</h2>

			<?php
			settings_fields( 'pluginPage' );
			do_settings_sections( 'pluginPage' );
			submit_button();
			//
			?>

		</form>
		<?php cy_test_connection_render() ?>
		<?php

}

add_action( 'admin_post_wp_cy_test', 'wp_cy_test' );




function cy_test_connection_render() {
	$options = get_option( 'cy_settings' );
	$works = "Not tested"; 
	$works = $_GET['connection_test']; //get_query_var( 'connection_test', 'Not tested' );
	$redirect = urlencode( remove_query_arg( 'connection_test', $_SERVER['REQUEST_URI'] ) );
	$redirect = urlencode( $_SERVER['REQUEST_URI'] )
	?> 
	<form action="<?php echo admin_url( 'admin-post.php' ); ?>" method='post'>
		<input type="hidden" name="action" value="wp_cy_test">
		Connection to game server works? <span>  <?php echo $works ?> </span>
		<input type="hidden" name="_wp_http_referer" value="<?php echo $redirect; ?>">
		<?php submit_button( 'Test Connection' ); ?>
	</form>	<?php
}

// -------------
// Actually do things
// -------------

if ( !class_exists( 'CyGameServerConnection' ) ) {
	require_once('include/CyGameServerConnection.php');
}

function wp_cy_test() {
	$options = get_option( 'cy_settings' );
	$nkConnection = new CyGameServerConnection( );
	$result = $nkConnection->test_connection();
	$url = add_query_arg( 'connection_test',  $result, urldecode( $_POST['_wp_http_referer'] ) );
	wp_safe_redirect( $url );
	exit;
}

?>