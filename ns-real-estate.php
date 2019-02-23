<?php

/**
* Plugin Name: NightShift Real Estate
* Plugin URI: http://nightshiftcreative.co/
* Description: Robust real estate listing system for agents and agencies of any size. 
* Version: 1.0.0
* Author: NightShift Creative
* Author URI: http://nightshiftcreative.co/
* Text Domain: ns-real-estate
**/

/*-----------------------------------------------------------------------------------*/
/*  Load Text Domain
/*-----------------------------------------------------------------------------------*/
load_plugin_textdomain( 'ns-real-estate', false, dirname( plugin_basename( __FILE__ ) ) . '/languages' );

/*-----------------------------------------------------------------------------------*/
/*  Define Global Variables
/*-----------------------------------------------------------------------------------*/
define('NS_SHOP_URL', 'https://studio.nightshiftcreative.co/');
define('NS_BASICS_GITHUB', '/NightShiftCreative/NS-Basics/archive/1.0.0.zip');
define('NS_REAL_ESTATE_GITHUB', '/NightShiftCreative/NS-Real-Estate/');
define('NS_REAL_ESTATE_LICENSE_PAGE', 'ns-real-estate-license-keys' );

/*-----------------------------------------------------------------------------------*/
/*  Automatic Update Checker (checks for new releases on github)
/*-----------------------------------------------------------------------------------*/
require 'includes/plugins/plugin-update-checker/plugin-update-checker.php';
$myUpdateChecker = Puc_v4_Factory::buildUpdateChecker(
    'https://github.com'.constant('NS_REAL_ESTATE_GITHUB'),
    __FILE__,
    'ns-real-estate'
);

/*-----------------------------------------------------------------------------------*/
/*  Require NS Basics
/*-----------------------------------------------------------------------------------*/
require_once( plugin_dir_path( __FILE__ ) . '/includes/plugins/class-tgm-plugin-activation.php');
add_action( 'tgmpa_register', 'ns_real_estate_register_required_plugins' );
function ns_real_estate_register_required_plugins() {

    $plugins = array(
        array(
			'name'         => 'NightShift Basics', // The plugin name.
			'slug'         => 'ns-basics', // The plugin slug (typically the folder name).
			'source'       => 'https://github.com'.constant('NS_BASICS_GITHUB'), // The plugin source.
			'required'     => true, // If false, the plugin is only 'recommended' instead of required.
			'version'	   => '1.0.0',
			'force_activation'   => false,
			'force_deactivation' => false,
			'external_url' => constant('NS_SHOP_URL'),
		),
    );

    $config = array(
        'id'           => 'ns-real-estate',       // Unique ID for hashing notices for multiple instances of TGMPA.
        'default_path' => '',                      // Default absolute path to bundled plugins.
        'menu'         => 'tgmpa-install-plugins', // Menu slug.
        'has_notices'  => true,                    // Show admin notices or not.
        'dismissable'  => false,                    // If false, a user cannot dismiss the nag message.
        'dismiss_msg'  => '',                      // If 'dismissable' is false, this message will be output at top of nag.
        'is_automatic' => true,                   // Automatically activate plugins after installation or not.
        'message'      => '',                      // Message to output right before the plugins table.
    );

    tgmpa( $plugins, $config );
}

/** Check if plugin is activated **/
function ns_real_estate_is_plugin_active( $plugin ) {
    return in_array( $plugin, (array) get_option( 'active_plugins', array() ) );
}

if(ns_real_estate_is_plugin_active('ns-basics/ns-basics.php')) {

	/*-----------------------------------------------------------------------------------*/
	/*	Include Admin Plugin Scripts and Stylesheets
	/*-----------------------------------------------------------------------------------*/
	function ns_real_estate_admin_scripts() {
		if (is_admin()) {

			$google_maps_api = esc_attr(get_option('ns_real_estate_google_maps_api'));

			wp_enqueue_script('ns-real-estate-admin-js', plugins_url('/js/ns-real-estate-admin.js', __FILE__), array('jquery', 'jquery-ui-core', 'jquery-ui-tabs', 'media-upload', 'thickbox'), '', true);
			wp_enqueue_style('ns-real-estate-admin-css', plugins_url('/css/ns-real-estate-admin.css',  __FILE__), array(), '1.0', 'all');
			wp_enqueue_script( 'ns-real-estate-google-maps', 'https://maps.googleapis.com/maps/api/js?key='.$google_maps_api.'&libraries=places', '', '', false );

			/* localize scripts */
	        $translation_array = array(
	            'admin_url' => esc_url(get_admin_url()),
	            'delete_text' => __( 'Delete', 'ns-real-estate' ),
	            'remove_text' => __( 'Remove', 'ns-real-estate' ),
	            'edit_text' => __( 'Edit', 'ns-real-estate' ),
	            'upload_img' => __( 'Upload Image', 'ns-real-estate' ),
	            'floor_plan_title' => __( 'Title:', 'ns-real-estate' ),
	            'floor_plan_size' => __( 'Size:', 'ns-real-estate' ),
	            'floor_plan_rooms' => __( 'Bedrooms:', 'ns-real-estate' ),
	            'floor_plan_bathrooms' => __( 'Bathrooms:', 'ns-real-estate' ),
	            'floor_plan_img' => __( 'Image:', 'ns-real-estate' ),
	            'floor_plan_description' => __( 'Description:', 'ns-real-estate' ),
	            'new_floor_plan' => __( 'New Floor Plan', 'ns-real-estate' ),
	            'value_text' => __( 'Field Name:', 'ns-real-estate' ),
	            'option_name_text' => __( 'Option name', 'ns-real-estate' ),
	            'custom_field_dup_error' => __( 'A custom field with the same name is already in use!', 'ns-real-estate' ),
	            'field_type_text' => __( 'Field Type', 'ns-real-estate' ),
	            'text_input_text' => __( 'Text Input', 'ns-real-estate' ),
	            'num_input_text' => __( 'Number Input', 'ns-real-estate' ),
	            'select_text' => __( 'Select Dropdown', 'ns-real-estate' ),
	            'select_options_text' => __( 'Select Options:', 'ns-real-estate' ),
	            'select_options_add' => __( 'Add Select Option', 'ns-real-estate' ),
	            'delete_custom_field_confirm' =>  __( 'Removing this field will remove it from all properties. Are you sure you want to proceed?', 'ns-real-estate' ),
	        );
	        wp_localize_script( 'ns-real-estate-admin-js', 'ns_real_estate_local_script', $translation_array );
		}
	}
	add_action('admin_enqueue_scripts', 'ns_real_estate_admin_scripts');

	/*-----------------------------------------------------------------------------------*/
	/*  Include Front-End Scripts and Styles
	/*-----------------------------------------------------------------------------------*/
	function ns_real_estate_front_end_scripts() {
	    if (!is_admin()) {

	    	$google_maps_api = esc_attr(get_option('ns_real_estate_google_maps_api'));
	    	
	    	wp_enqueue_script('nouislider', plugins_url('/assets/noUiSlider/nouislider.min.js', __FILE__), array('jquery'), '', true);
	        wp_enqueue_style('nouislider', plugins_url('/assets/noUiSlider/nouislider.min.css',  __FILE__), array(), '1.0', 'all');
	        wp_enqueue_script('wnumb', plugins_url('/assets/noUiSlider/wNumb.js', __FILE__), array('jquery'), '', true);
	        wp_enqueue_style('ns-real-estate', plugins_url('/css/ns-real-estate.css',  __FILE__), array(), '1.0', 'all');
	    	wp_enqueue_script('ns-real-estate', plugins_url('/js/ns-real-estate.js', __FILE__), array('jquery', 'jquery-ui-core', 'jquery-ui-datepicker'), '', true);
	    	wp_enqueue_script( 'ns-real-estate-google-maps', 'https://maps.googleapis.com/maps/api/js?key='.$google_maps_api.'&libraries=places', '', '', false );

	    	/* localize scripts */
	        $translation_array = array(
	            'admin_url' => esc_url(get_admin_url()),
	            'delete_text' => __( 'Delete', 'ns-real-estate' ),
	            'purchase_price' => __( 'Purchase Price', 'ns-real-estate' ),
	            'down_payment' => __( 'Down Payment', 'ns-real-estate' ),
	            'percent' => __( 'Percent', 'ns-real-estate' ),
	            'fixed' => __( 'Fixed', 'ns-real-estate' ),
	            'rate' => __( 'Rate', 'ns-real-estate' ),
	            'term' => __( 'Term', 'ns-real-estate' ),
	            'years' => __( 'Years', 'ns-real-estate' ),
	            'months' => __( 'Months', 'ns-real-estate' ),
	            'calculate' => __( 'Calculate', 'ns-real-estate' ),
	            'monthly_payment' => __( 'Your monthly payment:', 'ns-real-estate' ),
	            'required_field' => __( 'This field is required', 'ns-real-estate' ),
	            'floor_plan_title' => __( 'Title:', 'ns-real-estate' ),
	            'floor_plan_size' => __( 'Size:', 'ns-real-estate' ),
	            'floor_plan_rooms' => __( 'Bedrooms:', 'ns-real-estate' ),
	            'floor_plan_bathrooms' => __( 'Bathrooms:', 'ns-real-estate' ),
	            'floor_plan_img' => __( 'Image:', 'ns-real-estate' ),
	            'floor_plan_description' => __( 'Description:', 'ns-real-estate' ),
	            'new_floor_plan' => __( 'New Floor Plan', 'ns-real-estate' ),
	            'floor_plan_note' => __( 'Provide the absolute url to a hosted image.', 'ns-real-estate' ),
	        );
	        wp_localize_script( 'ns-real-estate', 'ns_real_estate_local_script', $translation_array );
	    
	        /* dynamic scripts */
        	include( plugin_dir_path( __FILE__ ) . '/js/dynamic_scripts.php');

	        //dynamic styles
        	wp_enqueue_style('ns-real-estate-dynamic-styles', plugins_url('/css/dynamic-styles.css', __FILE__));
        	include( plugin_dir_path( __FILE__ ) . '/css/dynamic_styles.php');
	    }
	}
	add_action('wp_enqueue_scripts', 'ns_real_estate_front_end_scripts');

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

	/*-----------------------------------------------------------------------------------*/
	/*  Includes Agent Related Functions
	/*-----------------------------------------------------------------------------------*/
	include( plugin_dir_path( __FILE__ ) . '/includes/agent-functions.php');

	/*-----------------------------------------------------------------------------------*/
	/*  Includes Shortcodes
	/*-----------------------------------------------------------------------------------*/
	include( plugin_dir_path( __FILE__ ) . '/includes/shortcodes.php');

	/*-----------------------------------------------------------------------------------*/
	/*  Includes Widgets
	/*-----------------------------------------------------------------------------------*/
	include( plugin_dir_path( __FILE__ ) . '/includes/custom_widgets/list_properties_widget.php');
	include( plugin_dir_path( __FILE__ ) . '/includes/custom_widgets/list_agents_widget.php');
	include( plugin_dir_path( __FILE__ ) . '/includes/custom_widgets/list_property_categories_widget.php');
	include( plugin_dir_path( __FILE__ ) . '/includes/custom_widgets/mortgage_widget.php');
	include( plugin_dir_path( __FILE__ ) . '/includes/custom_widgets/property_filter_widget.php');

	/*-----------------------------------------------------------------------------------*/
	/*  Includes Templates
	/*-----------------------------------------------------------------------------------*/
	include( plugin_dir_path( __FILE__ ) . '/includes/templates/templates.php');

	/*-----------------------------------------------------------------------------------*/
	/*  Includes WPBakery
	/*-----------------------------------------------------------------------------------*/
	include( plugin_dir_path( __FILE__ ) . '/includes/wp-bakery/wp-bakery.php');
}
?>