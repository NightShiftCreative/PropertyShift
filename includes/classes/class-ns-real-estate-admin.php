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
		add_menu_page('NS Real Estate', 'NS Real Estate', 'administrator', 'ns-real-estate-settings', array( $this, 'settings_page' ), NS_REAL_ESTATE_DIR.'/images/icon.png');
	    add_submenu_page('ns-real-estate-settings', 'Settings', 'Settings', 'administrator', 'ns-real-estate-settings');
	    add_submenu_page('ns-real-estate-settings', 'Add-Ons', 'Add-Ons', 'administrator', 'ns-real-estate-add-ons', array( $this, 'add_ons_page' ));
	    add_submenu_page('ns-real-estate-settings', 'License Keys', 'License Keys', 'administrator', 'ns-real-estate-license-keys', array( $this, 'license_keys_page' ));
	    add_submenu_page('ns-real-estate-settings', 'Help', 'Help', 'administrator', 'ns-real-estate-help', array( $this, 'help_page' ));
	}

	/**
	 * Load settings
	 *
	 * @param boolean $return_defaults
	 */
	public function load_settings($return_defaults = false) {

		$settings_init = array(
			'ns_property_detail_slug' => 'properties',
			'ns_property_type_tax_slug' => 'property-type',
			'ns_property_status_tax_slug' => 'property-status',
			'ns_property_location_tax_slug' => 'property-location',
			'ns_property_amenities_tax_slug' => 'property-amenity',
			'ns_property_filter_display' => 'true',
			'ns_property_filter_id' => '',
			'ns_properties_page' => '',
			'ns_num_properties_per_page' => 12,
			'ns_properties_default_layout' => 'grid',
			'ns_property_listing_header_display' => 'true',
			'ns_property_listing_default_sortby' => 'date_desc',
			'ns_property_listing_crop' => 'true',
			'ns_property_listing_display_time' => 'true',
			'ns_property_listing_display_favorite' => 'true',
			'ns_property_listing_display_share' => 'true',
			'ns_property_detail_default_layout' => 'right sidebar',
			'ns_property_detail_id' => 'false',
			'ns_property_detail_items' => array(),
			'ns_property_custom_fields' => array(),
			'ns_agent_detail_slug' => 'agents',
			'ns_num_agents_per_page' => 12,
			'ns_agent_listing_crop' => 'true',
			'ns_agent_detail_items' => array(),
			'ns_real_estate_google_maps_api' => '',
			'ns_real_estate_default_map_zoom' => 10,
			'ns_real_estate_default_map_latitude' => 39.2904,
			'ns_real_estate_default_map_longitude' => -76.5000,
			'ns_real_estate_google_maps_pin' => '',
			'ns_members_my_properties_page' => '',
			'ns_members_submit_property_page' => '',
			'ns_members_submit_property_approval' => 'true',
			'ns_members_add_locations' => 'true',
			'ns_members_add_amenities' => 'true',
			'ns_members_submit_property_fields' => array(),
			'ns_real_estate_currency_symbol' => '$',
			'ns_real_estate_currency_symbol_position' => 'before',
			'ns_real_estate_thousand_separator' => ',',
			'ns_real_estate_decimal_separator' => '.',
			'ns_real_estate_default_area_postfix' => 'Sq Ft',
			'ns_real_estate_thousand_separator_area' => ',',
			'ns_real_estate_decimal_separator_area' => '.',
			'ns_real_estate_num_decimal_area' => 0,
		);
		$settings_init = apply_filters( 'ns_real_estate_settings_init_filter', $settings_init);

		// Return default page settings
		if($return_defaults == true) {

			return $settings_init;

		// Return saved page settings
		} else {
			$settings = array();
			foreach($settings_init as $key=>$value) { $settings[$key] = esc_attr(get_option($key, $value)); }
			$settings = apply_filters( 'ns_real_estate_settings_filter', $settings);
			return $settings;
		}
	}

	/**
	 *	Register Settings
	 */
	public function register_settings() {
		$settings = $this->load_settings(true);
	    foreach($settings as $key => $value) { register_setting( 'ns-real-estate-settings-group', $key); } 
	    do_action( 'ns_real_estate_register_settings');
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
		ob_start(); 

		$settings = $this->load_settings(); ?>

		<div id="properties" class="tab-content">
	        <h2><?php echo esc_html_e('Properties Settings', 'ns-real-estate'); ?></h2>

	        <div class="ns-accordion" data-name="property-url">
	            <div class="ns-accordion-header"><i class="fa fa-chevron-right"></i> <?php echo esc_html_e('Property URL Options', 'ns-real-estate'); ?></div>
	            <div class="ns-accordion-content">

	            	<p class="admin-module-note"><?php esc_html_e('After changing slugs, make sure you re-save your permalinks in Settings > Permalinks.', 'ns-real-estate'); ?></p>
                	<br/>

                	<?php
                	$property_slug_field = array(
                		'title' => esc_html__('Properties Slug', 'ns-real-estate'),
                		'name' => 'ns_property_detail_slug',
                		'description' => esc_html__('Default: properties', 'ns-real-estate'),
                		'value' => $settings['ns_property_detail_slug'],
                		'type' => 'text',
                	);
                	$this->build_admin_field($property_slug_field);

                	$property_type_tax_slug_field = array(
                		'title' => esc_html__('Property Type Taxonomy Slug', 'ns-real-estate'),
                		'name' => 'ns_property_type_tax_slug',
                		'description' => esc_html__('Default: property-type', 'ns-real-estate'),
                		'value' => $settings['ns_property_type_tax_slug'],
                		'type' => 'text',
                	);
                	$this->build_admin_field($property_type_tax_slug_field);

                	$property_status_tax_slug_field = array(
                		'title' => esc_html__('Property Status Taxonomy Slug', 'ns-real-estate'),
                		'name' => 'ns_property_status_tax_slug',
                		'description' => esc_html__('Default: property-status', 'ns-real-estate'),
                		'value' => $settings['ns_property_status_tax_slug'],
                		'type' => 'text',
                	);
                	$this->build_admin_field($property_status_tax_slug_field);

                	$property_location_tax_slug_field = array(
                		'title' => esc_html__('Property Location Taxonomy Slug', 'ns-real-estate'),
                		'name' => 'ns_property_location_tax_slug',
                		'description' => esc_html__('Default: property-location', 'ns-real-estate'),
                		'value' => $settings['ns_property_location_tax_slug'],
                		'type' => 'text',
                	);
                	$this->build_admin_field($property_location_tax_slug_field);

                	$property_amenities_tax_slug_field = array(
                		'title' => esc_html__('Property Amenities Taxonomy Slug', 'ns-real-estate'),
                		'name' => 'ns_property_amenities_tax_slug',
                		'description' => esc_html__('Default: property-amenity', 'ns-real-estate'),
                		'value' => $settings['ns_property_amenities_tax_slug'],
                		'type' => 'text',
                	);
                	$this->build_admin_field($property_amenities_tax_slug_field);
                	?>
	            </div>
	        </div>

	        <div class="ns-accordion" data-name="property-filter">
	            <div class="ns-accordion-header"><i class="fa fa-chevron-right"></i> <?php echo esc_html_e('Property Filter Options', 'ns-real-estate'); ?></div>
	            <div class="ns-accordion-content">
	            	<?php
                	$display_property_filter_field = array(
                		'title' => esc_html__('Display Property Filter in Page Banners', 'ns-real-estate'),
                		'name' => 'ns_property_filter_display',
                		'value' => $settings['ns_property_filter_display'],
                		'type' => 'switch',
                	);
                	$this->build_admin_field($display_property_filter_field);

                	$default_property_filter_field = array(
                		'title' => esc_html__('Default Banner Filter', 'ns-real-estate'),
                		'name' => 'ns_property_filter_id',
                		'description' => esc_html__('This can be overriden on individual pages from the page settings meta box.', 'ns-real-estate'),
                		'value' => $settings['ns_property_filter_id'],
                		'type' => 'select',
                		'options' => array(),
                	);
                	$this->build_admin_field($default_property_filter_field);
                	?>
	            </div>
	        </div>

	        <div class="ns-accordion" data-name="property-listing">
	            <div class="ns-accordion-header"><i class="fa fa-chevron-right"></i> <?php echo esc_html_e('Property Listing Options', 'ns-real-estate'); ?></div>
	            <div class="ns-accordion-content">

	            	<?php
	            	$page_options = array('Select a page' => '');
	            	$pages = get_pages();
	            	foreach ( $pages as $page ) { $page_options[esc_attr($page->post_title)] = get_page_link( $page->ID ); }
	            	$property_listing_page_field = array(
                		'title' => esc_html__('Select Your Property Listings Page', 'ns-real-estate'),
                		'name' => 'ns_properties_page',
                		'value' => $settings['ns_properties_page'],
                		'type' => 'select',
                		'options' => $page_options,
                	);
                	$this->build_admin_field($property_listing_page_field);

                	$num_properties_per_page_field = array(
                		'title' => esc_html__('Number of Properties Per Page', 'ns-real-estate'),
                		'name' => 'ns_num_properties_per_page',
                		'value' => $settings['ns_num_properties_per_page'],
                		'type' => 'number',
                	);
                	$this->build_admin_field($num_properties_per_page_field);

                	$properties_tax_layout_field = array(
                		'title' => esc_html__('Properties Taxonomy Layout', 'ns-real-estate'),
                		'name' => 'ns_properties_default_layout',
                		'value' => $settings['ns_properties_default_layout'],
                		'type' => 'radio_image',
                		'options' => array(
                			esc_html__('Grid', 'ns-basics') => array('value' => 'grid'), 
							esc_html__('Row', 'ns-basics') => array('value' => 'row'),
                		),
                	);
                	$this->build_admin_field($properties_tax_layout_field);

                	$display_listing_header_field = array(
                		'title' => esc_html__('Display Listing Header?', 'ns-real-estate'),
                		'name' => 'ns_property_listing_header_display',
                		'description' => esc_html__('Toggle on/off the filter options that display directly above property listings.', 'ns-real-estate'),
                		'value' => $settings['ns_property_listing_header_display'],
                		'type' => 'switch',
                	);
                	$this->build_admin_field($display_listing_header_field);

                	$default_sort_by_field = array(
                		'title' => esc_html__('Default Sort By', 'ns-real-estate'),
                		'name' => 'ns_property_listing_default_sortby',
                		'description' => esc_html__('Choose the default sorting for property listings.', 'ns-real-estate'),
                		'value' => $settings['ns_property_listing_default_sortby'],
                		'type' => 'select',
                		'options' => array(
                			esc_html__('New to Old', 'ns-real-estate') => 'date_desc',
                			esc_html__('Old to New', 'ns-real-estate') => 'date_asc',
                			esc_html__('Price (High to Low)', 'ns-real-estate') => 'price_desc',
                			esc_html__('Price (Low to High)', 'ns-real-estate') => 'price_asc',
                		),
                	);
                	$this->build_admin_field($default_sort_by_field);

                	$property_img_size = ns_real_estate_get_image_size('property-thumbnail');
                	$property_listing_crop_description = '';
					if(!empty($property_img_size)) { $property_listing_crop_description = esc_html__('If active, property listing thumbnails will be cropped to: ', 'ns-real-estate').$property_img_size['width'].' x '.$property_img_size['height'].' pixels'; }
                	$property_listing_crop_field = array(
                		'title' => esc_html__('Hard crop property listing featured images?', 'ns-real-estate'),
                		'name' => 'ns_property_listing_crop',
                		'description' => $property_listing_crop_description,
                		'value' => $settings['ns_property_listing_crop'],
                		'type' => 'switch',
                	);
                	$this->build_admin_field($property_listing_crop_field);
                	?>

	            </div>
	        </div>

	    </div><!-- end property settings -->

		<?php $output = ob_get_clean();
    	return $output;
	}

	/**
	 *	Add-Ons page
	 */
	private function add_ons_page() {
	}

	/**
	 *	License Keys page
	 */
	private function license_keys_page() {
	}

	/**
	 *	Help page
	 */
	private function help_page() {
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