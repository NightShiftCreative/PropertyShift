<?php

/**
* Plugin Name: Rype Real Estate
* Plugin URI: http://rypecreative.com/
* Description: Robust property and agent listings.
* Version: 1.0.0
* Author: Rype Creative
* Author URI: http://rypecreative.com/
* Text Domain: rype-real-estate
**/

/*-----------------------------------------------------------------------------------*/
/*  Load Text Domain
/*-----------------------------------------------------------------------------------*/
load_plugin_textdomain( 'rype-real-estate', false, dirname( plugin_basename( __FILE__ ) ) . '/languages' );

/*-----------------------------------------------------------------------------------*/
/*  Automatic Update Checker (checks for new releases on github)
/*-----------------------------------------------------------------------------------*/
require 'includes/plugins/plugin-update-checker/plugin-update-checker.php';
$myUpdateChecker = Puc_v4_Factory::buildUpdateChecker(
    'https://github.com/RypeCreative/Rype-Real-Estate/',
    __FILE__,
    'rype-real-estate'
);

/*-----------------------------------------------------------------------------------*/
/*  Require Rype Basics
/*-----------------------------------------------------------------------------------*/
require_once( plugin_dir_path( __FILE__ ) . '/includes/plugins/class-tgm-plugin-activation.php');
add_action( 'tgmpa_register', 'rype_real_estate_register_required_plugins' );
function rype_real_estate_register_required_plugins() {

    $plugins = array(
        array(
			'name'         => 'Rype Basics', // The plugin name.
			'slug'         => 'rype-basics', // The plugin slug (typically the folder name).
			'source'       => 'https://github.com/RypeCreative/Rype-Basics/archive/1.0.0.zip', // The plugin source.
			'required'     => true, // If false, the plugin is only 'recommended' instead of required.
			'version'	   => '1.0.0',
			'force_activation'   => false,
			'force_deactivation' => false,
		),
    );

    $config = array(
        'id'           => 'rype-real-estate',       // Unique ID for hashing notices for multiple instances of TGMPA.
        'default_path' => '',                      // Default absolute path to bundled plugins.
        'menu'         => 'tgmpa-install-plugins', // Menu slug.
        'has_notices'  => true,                    // Show admin notices or not.
        'dismissable'  => false,                    // If false, a user cannot dismiss the nag message.
        'dismiss_msg'  => '',                      // If 'dismissable' is false, this message will be output at top of nag.
        'is_automatic' => true,                   // Automatically activate plugins after installation or not.
        'message'      => '',                      // Message to output right before the plugins table.
        'strings'      => array(
			'notice_can_install_required'     => _n_noop(
				'Rype Real Estate requires the following plugin: %1$s.',
				'Rype Real Estate requires the following plugins: %1$s.',
				'rype-real-estate'
			),
		),
    );

    tgmpa( $plugins, $config );
}

/** Check if plugin is activated **/
function rype_real_estate_is_plugin_active( $plugin ) {
    return in_array( $plugin, (array) get_option( 'active_plugins', array() ) );
}

if(rype_real_estate_is_plugin_active('rype-basics/rype-basics.php')) {


	/*-----------------------------------------------------------------------------------*/
	/*	Include Admin Plugin Scripts and Stylesheets
	/*-----------------------------------------------------------------------------------*/
	function rype_real_estate_admin_scripts() {
		if (is_admin()) {
			wp_enqueue_script('rype-real-estate-admin-js', plugins_url('/js/rype-real-estate-admin.js', __FILE__), array('jquery', 'jquery-ui-core', 'jquery-ui-tabs', 'media-upload', 'thickbox'), '', true);
			wp_enqueue_style('rype-real-estate-admin-css', plugins_url('/css/rype-real-estate-admin.css',  __FILE__), array(), '1.0', 'all');

			/* localize scripts */
	        $translation_array = array(
	            'admin_url' => esc_url(get_admin_url()),
	            'delete_text' => __( 'Delete', 'rype-real-estate' ),
	            'remove_text' => __( 'Remove', 'rype-real-estate' ),
	            'edit_text' => __( 'Edit', 'rype-real-estate' ),
	            'upload_img' => __( 'Upload Image', 'rype-real-estate' ),
	            'floor_plan_title' => __( 'Title:', 'rype-real-estate' ),
	            'floor_plan_size' => __( 'Size:', 'rype-real-estate' ),
	            'floor_plan_rooms' => __( 'Bedrooms:', 'rype-real-estate' ),
	            'floor_plan_bathrooms' => __( 'Bathrooms:', 'rype-real-estate' ),
	            'floor_plan_img' => __( 'Image:', 'rype-real-estate' ),
	            'floor_plan_description' => __( 'Description:', 'rype-real-estate' ),
	            'new_floor_plan' => __( 'New Floor Plan', 'rype-real-estate' ),
	            'value_text' => __( 'Field Name:', 'rype-real-estate' ),
	            'option_name_text' => __( 'Option name', 'rype-real-estate' ),
	            'custom_field_dup_error' => __( 'A custom field with the same name is already in use!', 'rype-real-estate' ),
	            'field_type_text' => __( 'Field Type', 'rype-real-estate' ),
	            'text_input_text' => __( 'Text Input', 'rype-real-estate' ),
	            'num_input_text' => __( 'Number Input', 'rype-real-estate' ),
	            'select_text' => __( 'Select Dropdown', 'rype-real-estate' ),
	            'select_options_text' => __( 'Select Options:', 'rype-real-estate' ),
	            'select_options_add' => __( 'Add Select Option', 'rype-real-estate' ),
	            'delete_custom_field_confirm' =>  __( 'Removing this field will remove it from all properties. Are you sure you want to proceed?', 'rype-real-estate' ),
	            'front_end_text' => __( 'Display in Front-end Property Submit Form', 'rype-real-estate' ),
	        );
	        wp_localize_script( 'rype-real-estate-admin-js', 'rype_real_estate_local_script', $translation_array );
		}
	}
	add_action('admin_enqueue_scripts', 'rype_real_estate_admin_scripts');

	/*-----------------------------------------------------------------------------------*/
	/*  Include Front-End Scripts and Styles
	/*-----------------------------------------------------------------------------------*/
	function rype_real_estate_front_end_scripts() {
	    if (!is_admin()) {
	    	wp_enqueue_script('nouislider', plugins_url('/assets/noUiSlider/nouislider.min.js', __FILE__), array('jquery'), '', true);
	        wp_enqueue_style('nouislider', plugins_url('/assets/noUiSlider/nouislider.min.css',  __FILE__), array(), '1.0', 'all');
	        wp_enqueue_script('wnumb', plugins_url('/assets/noUiSlider/wNumb.js', __FILE__), array('jquery'), '', true);
	        wp_enqueue_style('rype-real-estate', plugins_url('/css/rype-real-estate.css',  __FILE__), array(), '1.0', 'all');
	    	wp_enqueue_script('rype-real-estate', plugins_url('/js/rype-real-estate.js', __FILE__), array('jquery', 'jquery-ui-core', 'jquery-ui-datepicker'), '', true);
	    
	    	/* localize scripts */
	        $translation_array = array(
	            'admin_url' => esc_url(get_admin_url()),
	            'delete_text' => __( 'Delete', 'rype-real-estate' ),
	            'purchase_price' => __( 'Purchase Price', 'rype-real-estate' ),
	            'down_payment' => __( 'Down Payment', 'rype-real-estate' ),
	            'percent' => __( 'Percent', 'rype-real-estate' ),
	            'fixed' => __( 'Fixed', 'rype-real-estate' ),
	            'rate' => __( 'Rate', 'rype-real-estate' ),
	            'term' => __( 'Term', 'rype-real-estate' ),
	            'years' => __( 'Years', 'rype-real-estate' ),
	            'months' => __( 'Months', 'rype-real-estate' ),
	            'calculate' => __( 'Calculate', 'rype-real-estate' ),
	            'monthly_payment' => __( 'Your monthly payment:', 'rype-real-estate' ),
	            'required_field' => __( 'This field is required', 'rype-real-estate' ),
	            'floor_plan_title' => __( 'Title:', 'rype-real-estate' ),
	            'floor_plan_size' => __( 'Size:', 'rype-real-estate' ),
	            'floor_plan_rooms' => __( 'Bedrooms:', 'rype-real-estate' ),
	            'floor_plan_bathrooms' => __( 'Bathrooms:', 'rype-real-estate' ),
	            'floor_plan_img' => __( 'Image:', 'rype-real-estate' ),
	            'floor_plan_description' => __( 'Description:', 'rype-real-estate' ),
	            'new_floor_plan' => __( 'New Floor Plan', 'rype-real-estate' ),
	            'floor_plan_note' => __( 'Provide the absolute url to a hosted image.', 'rype-real-estate' ),
	        );
	        wp_localize_script( 'rype-real-estate', 'rype_real_estate_local_script', $translation_array );
	    
	        //dynamic styles
        	wp_enqueue_style('rype-real-estate-dynamic-styles', plugins_url('/css/dynamic-styles.css', __FILE__));
        	include( plugin_dir_path( __FILE__ ) . '/css/dynamic_styles.php');
	    }
	}
	add_action('wp_enqueue_scripts', 'rype_real_estate_front_end_scripts');

	/*-----------------------------------------------------------------------------------*/
	/*  ADD ADMIN PAGES AND SETTINGS
	/*-----------------------------------------------------------------------------------*/
	include( plugin_dir_path( __FILE__ ) . 'includes/admin-settings-page.php');
	include( plugin_dir_path( __FILE__ ) . 'includes/admin-add-ons-page.php');
	include( plugin_dir_path( __FILE__ ) . 'includes/admin-license-keys-page.php');
	include( plugin_dir_path( __FILE__ ) . 'includes/admin-help-page.php');

	/*-----------------------------------------------------------------------------------*/
	/*  Includes Property Related Functions
	/*-----------------------------------------------------------------------------------*/
	include( plugin_dir_path( __FILE__ ) . '/includes/property-functions.php');
	include( plugin_dir_path( __FILE__ ) . '/includes/property-submit-functions.php');
	include( plugin_dir_path( __FILE__ ) . '/includes/filter-functions.php');

}
?>