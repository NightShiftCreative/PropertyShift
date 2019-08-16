<?php

/**
* Plugin Name: Nightshift Real Estate
* Plugin URI: http://nightshiftcreative.co/
* Description: Robust real estate listing system for agents and agencies of any size. 
* Version: 1.0.0
* Author: Nightshift Creative
* Author URI: http://nightshiftcreative.co/
* Text Domain: ns-real-estate
**/

// Exit if accessed directly
if (!defined( 'ABSPATH')) { exit; }

class NS_Real_Estate {

	/**
	 * Constructor - intialize the plugin
	 */
	public function __construct() {
		
		//Add actions & filters
		require_once( plugin_dir_path( __FILE__ ) . '/includes/plugins/class-tgm-plugin-activation.php');
		add_action( 'tgmpa_register', array( $this, 'require_plugins' ) );
		if($this->is_plugin_active('ns-basics/ns-basics.php')) {
			add_action( 'admin_enqueue_scripts', array( $this, 'admin_scripts' ) );
			add_action( 'wp_enqueue_scripts', array( $this, 'frontend_scripts' ) );
		}

		//Functions
		$this->load_plugin_textdomain();
		$this->define_constants();
		$this->update_checker();
		if($this->is_plugin_active('ns-basics/ns-basics.php')) { $this->includes(); }
	}

	/**
	 * Load the textdomain for translation
	 */
	public function load_plugin_textdomain() {
		load_plugin_textdomain( 'ns-real-estate', false, dirname( plugin_basename( __FILE__ ) ) . '/languages' );
	}

	/**
	 * Define constants
	 */
	public function define_constants() {
		define('NS_SHOP_URL', 'https://products.nightshiftcreative.co/');
		define('NS_BASICS_GITHUB', '/NightShiftCreative/NS-Basics/archive/1.0.0.zip');
		define('NS_REAL_ESTATE_GITHUB', '/NightShiftCreative/NS-Real-Estate/');
		define('NS_REAL_ESTATE_LICENSE_PAGE', 'ns-real-estate-license-keys' );
		define('NS_REAL_ESTATE_DIR', plugins_url('', __FILE__));
	}

	/**
	 * Update Checker
	 */
	public function update_checker() {
		require 'includes/plugins/plugin-update-checker/plugin-update-checker.php';
		$myUpdateChecker = Puc_v4_Factory::buildUpdateChecker(
		    'https://github.com'.constant('NS_REAL_ESTATE_GITHUB'),
		    __FILE__,
		    'ns-real-estate'
		);
	}

	/**
	 * Require Plugins
	 */
	public function require_plugins() {
		$plugins = array(
	        array(
				'name'         => 'Nightshift Basics', // The plugin name.
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

	/**
	 * Check if plugin is activated
	 *
	 * @param string $plugin
	 */
	public function is_plugin_active($plugin) {
		return in_array( $plugin, (array) get_option( 'active_plugins', array() ) );
	}

	/**
	 * Load admin scripts and styles
	 */
	public function admin_scripts() {
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

	/**
	 * Load front end scripts and styles
	 */
	public function frontend_scripts() {
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

	/**
	 * Load Includes
	 */
	public function includes() {

		/************************************************************************/
		// Include functions
		/************************************************************************/
		include( plugin_dir_path( __FILE__ ) . 'includes/global-functions.php');
		include( plugin_dir_path( __FILE__ ) . 'includes/templates/templates.php');
		if($this->is_plugin_active('js_composer/js_composer.php')) { 
			include( plugin_dir_path( __FILE__ ) . 'includes/wp-bakery/wp-bakery.php');
		}

		/************************************************************************/
		// Include classes
		/************************************************************************/

		include( plugin_dir_path( __FILE__ ) . 'includes/classes/class-ns-real-estate-admin.php');
		include( plugin_dir_path( __FILE__ ) . 'includes/classes/class-ns-real-estate-properties.php');
		include( plugin_dir_path( __FILE__ ) . 'includes/classes/class-ns-real-estate-agents.php');
		include( plugin_dir_path( __FILE__ ) . 'includes/classes/class-ns-real-estate-filters.php');
		include( plugin_dir_path( __FILE__ ) . 'includes/classes/class-ns-real-estate-maps.php');
		include( plugin_dir_path( __FILE__ ) . 'includes/classes/class-ns-real-estate-license-keys.php');
		include( plugin_dir_path( __FILE__ ) . 'includes/classes/class-ns-real-estate-shortcodes.php');
		include( plugin_dir_path( __FILE__ ) . 'includes/classes/widgets/list_agents_widget.php');
		include( plugin_dir_path( __FILE__ ) . 'includes/classes/widgets/list_properties_widget.php');
		include( plugin_dir_path( __FILE__ ) . 'includes/classes/widgets/list_property_categories_widget.php');
		include( plugin_dir_path( __FILE__ ) . 'includes/classes/widgets/mortgage_widget.php');
		include( plugin_dir_path( __FILE__ ) . 'includes/classes/widgets/property_filter_widget.php');

		// Setup the admin
		if(is_admin()) { 
			$this->admin = new NS_Real_Estate_Admin(); 
			$this->admin->init();
		}

		// Load properties class
		$this->properties = new NS_Real_Estate_Properties();
		$this->properties->init();

		// Load agents class
		$this->agents = new NS_Real_Estate_Agents();
		$this->agents->init();

		// Load filters class
		$this->filters = new NS_Real_Estate_Filters();
		$this->filters->init();

		// Load maps class
		$this->maps = new NS_Real_Estate_Maps();
		$this->maps->init();

		// Load license keys class
		$this->license_keys = new NS_Real_Estate_License_Keys();

		// Load shortcodes class
		$this->shortcodes = new NS_Real_Estate_Shortcodes();

	}

}


/**
 *  Load the main class
 */
function ns_real_estate() {
	global $ns_real_estate;
	if(!isset($ns_real_estate)) { $ns_real_estate = new NS_Real_Estate(); }
	return $ns_real_estate;
}
ns_real_estate();
?>