<?php
// Exit if accessed directly
if (!defined( 'ABSPATH')) { exit; }

/**
 *	NS_Real_Estate_Admin class
 *
 *  Outputs admin pages and provides the core methods for building admin interfaces.
 */
class NS_Real_Estate_Admin extends NS_Basics_Admin {

	/**
	 *	Constructor
	 */
	public function __construct() {
		//Add actions & filters
		add_action('admin_menu', array( $this, 'admin_menu' ));
		add_action('admin_init', array( $this, 'register_settings' ));
	}

	/**
	 *	Add admin menu
	 */
	public function admin_menu() {
		add_menu_page('NS Real Estate', 'NS Real Estate', 'administrator', 'ns-real-estate-settings', array( $this, 'settings_page' ), plugins_url('../images/icon.png', __FILE__));
	    add_submenu_page('ns-real-estate-settings', 'Settings', 'Settings', 'administrator', 'ns-real-estate-settings');
	    add_submenu_page('ns-real-estate-settings', 'Add-Ons', 'Add-Ons', 'administrator', 'ns-real-estate-add-ons', array( $this, 'add_ons_page' ));
	    add_submenu_page('ns-real-estate-settings', 'License Keys', 'License Keys', 'administrator', 'ns-real-estate-license-keys', array( $this, 'license_keys_page' ));
	    add_submenu_page('ns-real-estate-settings', 'Help', 'Help', 'administrator', 'ns-real-estate-help', array( $this, 'help_page' ));
	}

	/**
	 *	Register Settings
	 */
	public function register_settings() {
	
	}

	/**
	 *	Settings page
	 */
	public function settings_page() {
	    
	    $content_nav = array(
	        array('name' => esc_html__('Properties', 'ns-real-estate'), 'link' => '#properties', 'icon' => 'fa-home', 'order' => 1),
	        array('name' => esc_html__('Agents', 'ns-real-estate'), 'link' => '#agents', 'icon' => 'fa-user-tie', 'order' => 2),
	        array('name' => esc_html__('Maps', 'ns-real-estate'), 'link' => '#maps', 'icon' => 'fa-map', 'order' => 3),
	        array('name' => esc_html__('Members', 'ns-real-estate'), 'link' => '#members', 'icon' => 'fa-key', 'order' => 4),
	        array('name' => esc_html__('Currency & Numbers', 'ns-real-estate'), 'link' => '#currency', 'icon' => 'fa-money-bill-alt', 'order' => 5),
	    );
	    $content_nav = apply_filters( 'ns_real_estate_setting_tabs_filter', $content_nav);
	    usort($content_nav, function ($a, $b) {return ($a["order"]-$b["order"]); });
	    
	    //add alerts
	    $alerts = array();
	    if(!current_theme_supports('ns-real-estate')) {
	        $current_theme = wp_get_theme();
	        $incompatible_theme_alert = $this->admin_alert('info', esc_html__('The active theme ('.$current_theme->name.') does not declare support for NS Real Estate.', 'ns-real-estate'), $action = '#', $action_text = esc_html__('Get a compatible theme', 'ns-real-estate'), true); 
	        $alerts[] = $incompatible_theme_alert; 
	    }

	    $google_maps_api = esc_attr(get_option('ns_real_estate_google_maps_api'));
	    if(empty($google_maps_api)) {
	        $google_api_key_alert = $this->admin_alert('warning', esc_html__('Please provide a Google Maps API Key within the Maps tab.', 'ns-real-estate'), $action = null, $action_text = null, true);
	        $alerts[] = $google_api_key_alert; 
	    }

	    $properties_page = esc_attr(get_option('ns_properties_page'));
	    if(empty($properties_page)) {
	        $properties_page_alert = $this->admin_alert('warning', esc_html__('You have not set your properties listing page. Go to Properties > Property Listing Options, to set this field.', 'ns-real-estate'), $action = null, $action_text = null, true);
	        $alerts[] = $properties_page_alert; 
	    }

	    $args = array(
			'page_name' => 'Nightshift Real Estate',
			'settings_group' => 'ns-real-estate-settings-group',
			'pages' => $this->get_admin_pages(),
			'display_actions' => 'true',
			'content' => $this->settings_page_content(),
			'content_class' => null,
			'content_nav'=> $content_nav,
			'alerts' => $alerts,
			'ajax' => true,
			'icon' => plugins_url('/ns-real-estate/images/icon-real-estate.svg'),
		);
	    echo $this->build_admin_page($args);
	}

	/**
	 *	Settings page content
	 */
	public function settings_page_content() {
		
	}

	/**
	 *	Add-Ons page
	 */
	public function add_ons_page() {
	}

	/**
	 *	License Keys page
	 */
	public function license_keys_page() {
	}

	/**
	 *	Help page
	 */
	public function help_page() {
	}

	/**
	 *	Get admin pages
	 */
	public function get_admin_pages() {
		$pages = array();
	    $pages[] = array('slug' => 'ns-real-estate-settings', 'name' => esc_html__('Settings', 'ns-real-estate'));
	    $pages[] = array('slug' => 'ns-real-estate-add-ons', 'name' => esc_html__('Add-Ons', 'ns-real-estate'));
	    $pages[] = array('slug' => 'ns-real-estate-license-keys', 'name' => esc_html__('License Keys', 'ns-real-estate'));
	    $pages[] = array('slug' => 'ns-real-estate-help', 'name' => esc_html__('Help', 'ns-real-estate'));
	    return $pages;
	}

	
}

?>