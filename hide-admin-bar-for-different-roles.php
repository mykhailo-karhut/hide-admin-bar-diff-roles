<?php
/*
Plugin Name: Hide admin bar for different roles
Description: Hide admin bar
Plugin URI: https://uk.wordpress.org/plugins/hide-admin-bar-for-different-roles
Author: Mykhailo Karhut
Version: 1.0.0
Text Domain: hide-admin-bar-diff-roles
Domain Path: /languages
Author URI: 
*/

add_action( 'admin_menu', 'habdr_options_page_admin_bar' );
add_action( 'admin_enqueue_scripts', 'habdr_scripts_styles' );
add_action( 'admin_init', 'habdr_setting' );
add_action(	'init', 'habdr_disable_admin_bar', 9);
add_filter( 'plugin_action_links', 'habdr_plugin_action_links', 10, 2 );

function habdr_load_plugin_textdomain() {
    habdr_load_plugin_textdomain( 'hide-admin-bar-diff-roles', FALSE, basename( dirname( __FILE__ ) ) . '/languages/' );
}

function habdr_options_data() {
	if(get_option( 'habdr_options_admin_bar' )) {
		$options = get_option( 'habdr_options_admin_bar' );
	} else {
		$options = '';
	}
	return $options;
}

function habdr_get_custom_roles() {
	global $wp_roles;
    $roles = $wp_roles->get_names();
    return $roles;
}

function habdr_scripts_styles() {
	wp_enqueue_style( 'habdr-styles', plugins_url('css/habdr-styles.css', __FILE__) );
}

function habdr_options_page_admin_bar() {
	// $page_title, $menu_title, $capability, $menu_slug, $function
	add_management_page( esc_html__('Admin bar', 'hide-admin-bar-diff-roles'), esc_html__('Admin bar', 'hide-admin-bar-diff-roles'), 'manage_options', 'habdr-options-admin-bar', 'habdr_option_page' );
}

function habdr_setting() {
	// $option_group, $option_name, $sanitize_callback
	register_setting( 'habdr_options_group', 'habdr_options_admin_bar', '' );

	// $id, $title, $callback, $page
	add_settings_section( 'habdr_options_section', '', '', 'habdr-options-admin-bar' );

	// $id, $title, $callback, $page, $section, $args
	add_settings_field( 'habdr_list_roles', '', 'habdr_roles', 'habdr-options-admin-bar', 'habdr_options_section', array( 'label_for' => 'habdr_list_roles' ) );
}

function habdr_roles() {
	$options = habdr_options_data();
?>
	<div class="wrap">
		<h2><?php esc_html_e('Choose roles for hide admin bar', 'hide-admin-bar-diff-roles'); ?></h2><br/>
		<?php 
	    	foreach (habdr_get_custom_roles() as $key => $value) {
	    		?>
	    		<fieldset class="option">
	    			<label>
		    			<input type="checkbox" name="habdr_options_admin_bar[<?php echo $key; ?>]" value="1" <?php (!empty($options))? checked( 1 == $options[$key] ) : ''; ?>>
		    			<?php echo $value; ?>
		    		</label>
	    		</fieldset><br/>
	    		<?php
	    	}
		?>
	</div>
<?php
}

function habdr_option_page() {
?>
	<div class="wrap">
		<form action="options.php" method="POST" enctype="multipart/form-data">
			<?php settings_fields( 'habdr_options_group' ); ?>
			<?php do_settings_sections( 'habdr-options-admin-bar' ); ?>
			<?php submit_button(); ?>
		</form>
	</div>
<?php
}

function habdr_disable_admin_bar() {
	$options = habdr_options_data();
	if($options) {
		foreach ($options as $key => $value) {
			if(current_user_can($key)) {
				add_filter( 'show_admin_bar', '__return_false' );
			}
		}
	}
}

function habdr_plugin_action_links( $actions, $plugin_file ){
	if( false === strpos( $plugin_file, basename(__FILE__) ) )
		return $actions;

	$settings_link = '<a href="tools.php?page=habdr-options-admin-bar' .'">Settings</a>'; 
	array_unshift( $actions, $settings_link ); 
	return $actions; 
}