<?php
// Exit if accessed directly
if (!defined( 'ABSPATH')) { exit; }

/**
 *	NS_Real_Estate_Admin class
 *
 *  Outputs admin pages and provides the core methods for building admin interfaces.
 */
class NS_Real_Estate_Admin extends NS_Basics_Admin {

	/************************************************************************/
	// Initialize
	/************************************************************************/

	/**
	 *	Init
	 */
	public function init() {
		add_action('admin_menu', array( $this, 'admin_menu' ));
		add_action('admin_init', array( $this, 'register_settings' ));
		add_filter('ns_basics_admin_field_types', array( $this, 'add_field_types' ));
	}

	/**
	 *	Add admin menu
	 */
	public function admin_menu() {
		add_menu_page('PropertyShift', 'PropertyShift', 'administrator', 'ns-real-estate-settings', array( $this, 'settings_page' ), NS_REAL_ESTATE_DIR.'/images/icon.png', 25);
	    add_submenu_page('ns-real-estate-settings', 'Settings', 'Settings', 'administrator', 'ns-real-estate-settings');
	    add_submenu_page('ns-real-estate-settings', 'Add-Ons', 'Add-Ons', 'administrator', 'ns-real-estate-add-ons', array( $this, 'add_ons_page' ));
	    add_submenu_page('ns-real-estate-settings', 'License Keys', 'License Keys', 'administrator', 'ns-real-estate-license-keys', array( $this, 'license_keys_page' ));
	    add_submenu_page('ns-real-estate-settings', 'Help', 'Help', 'administrator', 'ns-real-estate-help', array( $this, 'help_page' ));
	}

	/**
	 *	Register Settings
	 */
	public function register_settings() {
		$return_defaults = true;
		$settings = $this->load_settings($return_defaults);
	    foreach($settings as $key=>$field) { 
	    	if(!empty($field['args'])) { $args = $field['args']; } else { $args = null; }
	    	register_setting( 'ns-real-estate-settings-group', $key, $args); 
	    } 
	    do_action( 'ns_real_estate_register_settings');
	}
	

	/**
	 * Load settings
	 *
	 * @param boolean $return_defaults
	 *
	 */
	public function load_settings($return_defaults = false, $single_setting = null, $single_esc = true) {

		$settings_init = array(
			'ns_property_detail_slug' => array('value' => 'properties', 'esc' => true, 'args' => array('sanitize_callback' => 'sanitize_title')),
			'ns_property_type_tax_slug' => array('value' => 'property-type', 'esc' => true, 'args' => array('sanitize_callback' => 'sanitize_title')),
			'ns_property_status_tax_slug' => array('value' => 'property-status', 'esc' => true, 'args' => array('sanitize_callback' => 'sanitize_title')),
			'ns_property_location_tax_slug' => array('value' => 'property-location', 'esc' => true, 'args' => array('sanitize_callback' => 'sanitize_title')),
			'ns_property_amenities_tax_slug' => array('value' => 'property-amenity', 'esc' => true, 'args' => array('sanitize_callback' => 'sanitize_title')),
			'ns_property_filter_display' => array('value' => 'true'),
			'ns_property_filter_id' => array('value' => ''),
			'ns_properties_page' => array('value' => ''),
			'ns_num_properties_per_page' => array('value' => 12),
			'ns_properties_default_layout' => array('value' => 'grid'),
			'ns_property_listing_header_display' => array('value' => 'true'),
			'ns_property_listing_default_sortby' => array('value' => 'date_desc'),
			'ns_property_listing_crop' => array('value' => 'true'),
			'ns_property_listing_display_time' => array('value' => 'true'),
			'ns_property_listing_display_favorite' => array('value' => 'true'),
			'ns_property_listing_display_share' => array('value' => 'true'),
			'ns_property_detail_default_layout' => array('value' => 'right sidebar'),
			'ns_property_detail_id' => array('value' => 'false'),
			'ns_property_detail_items' => array('value' => NS_Real_Estate_Properties::load_property_detail_items(), 'esc' => false),
			'ns_property_detail_amenities_hide_empty' => array('value' => 'false'),
			'ns_property_detail_map_zoom' => array('value' => 13),
			'ns_property_detail_map_height' => array('value' => 250),
			'ns_property_detail_agent_contact_form' => array('value' => 'false'),
			'ns_agent_detail_slug' => array('value' => 'agents'),
			'ns_num_agents_per_page' => array('value' => 12),
			'ns_agent_listing_crop' => array('value' => 'true'),
			'ns_agent_detail_items' => array('value' => NS_Real_Estate_Agents::load_agent_detail_items(), 'esc' => false),
			'ns_agent_form_message_placeholder' => array('value' => esc_html__('I am interested in this property and would like to know more.', 'ns-real-estate')),
			'ns_agent_form_success' => array('value' => esc_html__('Thanks! Your email has been delivered!', 'ns-real-estate')),
			'ns_agent_form_submit_text' => array('value' => esc_html__('Contact Agent', 'ns-real-estate')),
			'ns_real_estate_google_maps_api' => array('value' => ''),
			'ns_real_estate_default_map_zoom' => array('value' => 10),
			'ns_real_estate_default_map_latitude' => array('value' => 39.2904),
			'ns_real_estate_default_map_longitude' => array('value' => -76.5000),
			'ns_real_estate_google_maps_pin' => array('value' => NS_REAL_ESTATE_DIR.'/images/pin.png'),
			'ns_members_my_properties_page' => array('value' => ''),
			'ns_members_submit_property_page' => array('value' => ''),
			'ns_members_submit_property_approval' => array('value' => 'true'),
			'ns_members_add_locations' => array('value' => 'true'),
			'ns_members_add_amenities' => array('value' => 'true'),
			'ns_members_submit_property_fields' => array('value' => NS_Real_Estate_Properties::load_property_submit_fields(), 'esc' => false),
			'ns_real_estate_currency_symbol' => array('value' => '$'),
			'ns_real_estate_currency_symbol_position' => array('value' => 'before'),
			'ns_real_estate_thousand_separator' => array('value' => ','),
			'ns_real_estate_decimal_separator' => array('value' => '.'),
			'ns_real_estate_num_decimal' => array('value' => 0),
			'ns_real_estate_default_area_postfix' => array('value' => 'Sq Ft'),
			'ns_real_estate_thousand_separator_area' => array('value' => ','),
			'ns_real_estate_decimal_separator_area' => array('value' => '.'),
			'ns_real_estate_num_decimal_area' => array('value' => 0),
		);
		$settings_init = apply_filters( 'ns_real_estate_settings_init_filter', $settings_init);
		$settings = $this->get_settings($settings_init, $return_defaults, $single_setting, $single_esc);
		if($single_setting == null) { $settings = apply_filters( 'ns_real_estate_settings_saved_filter', $settings); }
		return $settings;
		
	}

	/************************************************************************/
	// Output Pages
	/************************************************************************/

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

		$settings = $this->load_settings();
		?>

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
                		'options' => NS_Real_Estate_Filters::get_filter_ids(),
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

                	$time_stamp_field = array(
                		'title' => esc_html__('Display Time Stamp?', 'ns-real-estate'),
                		'name' => 'ns_property_listing_display_time',
                		'value' => $settings['ns_property_listing_display_time'],
                		'type' => 'switch',
                	);
                	$this->build_admin_field($time_stamp_field);

                	$listing_display_favorite_field = array(
                		'title' => esc_html__('Allow users to favorite properties?', 'ns-real-estate'),
                		'name' => 'ns_property_listing_display_favorite',
                		'value' => $settings['ns_property_listing_display_favorite'],
                		'type' => 'switch',
                	);
                	$this->build_admin_field($listing_display_favorite_field);

                	$listing_display_share_field = array(
                		'title' => esc_html__('Allow users to share properties?', 'ns-real-estate'),
                		'name' => 'ns_property_listing_display_share',
                		'value' => $settings['ns_property_listing_display_share'],
                		'type' => 'switch',
                	);
                	$this->build_admin_field($listing_display_share_field);
                	?>
	            </div>
	        </div>

	        <div class="ns-accordion" data-name="property-detail">
	            <div class="ns-accordion-header"><i class="fa fa-chevron-right"></i> <?php echo esc_html_e('Property Detail Options', 'ns-real-estate'); ?></div>
	            <div class="ns-accordion-content">

	            	<?php
	            	$property_detail_default_layout_field = array(
                		'title' => esc_html__('Select the default page layout for property detail pages', 'ns-real-estate'),
                		'name' => 'ns_property_detail_default_layout',
                		'value' => $settings['ns_property_detail_default_layout'],
                		'type' => 'radio_image',
                		'options' => array(
                			esc_html__('Full Width', 'ns-basics') => array('value' => 'full', 'icon' => NS_BASICS_PLUGIN_DIR.'/images/full-width-icon.png'), 
							esc_html__('Right Sidebar', 'ns-basics') => array('value' => 'right sidebar', 'icon' => NS_BASICS_PLUGIN_DIR.'/images/right-sidebar-icon.png'),
							esc_html__('Left Sidebar', 'ns-basics') => array('value' => 'left sidebar', 'icon' => NS_BASICS_PLUGIN_DIR.'/images/left-sidebar-icon.png'),
                		),
                	);
                	$this->build_admin_field($property_detail_default_layout_field);
                	
                	$property_detail_id_field = array(
                		'title' => esc_html__('Show Property Code on Front-End', 'ns-real-estate'),
                		'name' => 'ns_property_detail_id',
                		'value' => $settings['ns_property_detail_id'],
                		'type' => 'switch',
                	);
                	$this->build_admin_field($property_detail_id_field);

                	$property_detail_items_field = array(
                		'title' => esc_html__('Property Detail Sections', 'ns-real-estate'),
                		'name' => 'ns_property_detail_items',
                		'description' => esc_html__('Drag & drop the sections to rearrange their order', 'ns-real-estate'),
                		'value' => $settings['ns_property_detail_items'],
                		'type' => 'sortable',
                		'display_sidebar' => true,
                		'children' => array(
                			'hide_empty_amenities' => array(
                				'title' => esc_html__('Hide empty amenities?', 'ns-real-estate'),
	                			'name' => 'ns_property_detail_amenities_hide_empty',
	                			'value' => $settings['ns_property_detail_amenities_hide_empty'],
	                			'type' => 'checkbox',
	                			'parent_val' => 'amenities',
                			),
                			'map_zoom' => array(
                				'title' => esc_html__('Map Zoom', 'ns-real-estate'),
	                			'name' => 'ns_property_detail_map_zoom',
	                			'value' => $settings['ns_property_detail_map_zoom'],
	                			'type' => 'select',
	                			'options' => array('1' => '1', '2' => '2', '3' => '3', '4' => '4', '5' => '5', '6' => '6', '7' => '7', '8' => '8', '9' => '9', '10' => '10', '11' => '11', '12' => '12', '13' => '13', '14' => '14', '15' => '15'),
	                			'parent_val' => 'location',
                			),
                			'map_height' => array(
                				'title' => esc_html__('Map Height', 'ns-real-estate'),
	                			'name' => 'ns_property_detail_map_height',
	                			'value' => $settings['ns_property_detail_map_height'],
	                			'type' => 'number',
	                			'parent_val' => 'location',
                			),
                			'agent_contact_form' => array(
                				'title' => esc_html__('Display agent contact form underneath agent information?', 'ns-real-estate'),
	                			'name' => 'ns_property_detail_agent_contact_form',
	                			'description' => esc_html__('Configure the agent contact form options in the Agent Settings tab.', 'ns-real-estate'),
	                			'value' => $settings['ns_property_detail_agent_contact_form'],
	                			'type' => 'checkbox',
	                			'parent_val' => 'agent_info',
                			),
                		),
                	);
                	$this->build_admin_field($property_detail_items_field);
	            	?>
	            </div>
	        </div>

	        <!-- Hook in for Add-Ons -->
        	<?php do_action( 'ns_real_estate_after_property_settings'); ?>

	    </div><!-- end property settings -->

	    <div id="agents" class="tab-content">
	        <h2><?php echo esc_html_e('Agent Settings', 'ns-real-estate'); ?></h2>

	        <div class="ns-accordion" data-name="agent-listing">
	            <div class="ns-accordion-header"><i class="fa fa-chevron-right"></i> <?php echo esc_html_e('Agent Listing Options', 'ns-real-estate'); ?></div>
	            <div class="ns-accordion-content">

	            	<?php
	            	$agent_detail_slug_field = array(
                		'title' => esc_html__('Agents Slug', 'ns-real-estate'),
                		'name' => 'ns_agent_detail_slug',
                		'description' => esc_html__('After changing the slug, make sure you re-save your permalinks in Settings > Permalinks. The default slug is agents.', 'ns-real-estate'),
                		'value' => $settings['ns_agent_detail_slug'],
                		'type' => 'text',
                	);
                	$this->build_admin_field($agent_detail_slug_field);

                	$agents_num_field = array(
                		'title' => esc_html__('Number of Agents Per Page', 'ns-real-estate'),
                		'name' => 'ns_num_agents_per_page',
                		'value' => $settings['ns_num_agents_per_page'],
                		'type' => 'number',
                	);
                	$this->build_admin_field($agents_num_field);

                	$agent_listing_crop_field = array(
                		'title' => esc_html__('Hard crop agent listing featured images?', 'ns-real-estate'),
                		'name' => 'ns_agent_listing_crop',
                		'description' => esc_html__('If active, agent listing thumbnails will be cropped to 800 x 600 pixels.', 'ns-real-estate'),
                		'value' => $settings['ns_agent_listing_crop'],
                		'type' => 'switch',
                	);
                	$this->build_admin_field($agent_listing_crop_field);
	            	?>
	            </div>
	        </div>

	        <div class="ns-accordion" data-name="agent-detail">
	            <div class="ns-accordion-header"><i class="fa fa-chevron-right"></i> <?php echo esc_html_e('Agent Detail Options', 'ns-real-estate'); ?></div>
	            <div class="ns-accordion-content">

	            	<?php
	            	$agent_detail_items_field = array(
                		'title' => esc_html__('Agent Detail Sections', 'ns-real-estate'),
                		'name' => 'ns_agent_detail_items',
                		'description' => esc_html__('Drag & drop the sections to rearrange their order', 'ns-real-estate'),
                		'value' => $settings['ns_agent_detail_items'],
                		'type' => 'sortable',
                		'display_sidebar' => true, 
                		'children' => array(
                			'form_message_placeholder' => array(
                				'title' => esc_html__('Message Placeholder on Property Pages', 'ns-real-estate'),
	                			'name' => 'ns_agent_form_message_placeholder',
	                			'value' => $settings['ns_agent_form_message_placeholder'],
	                			'type' => 'text',
	                			'parent_val' => 'contact',
                			),
                			'form_success' => array(
                				'title' => esc_html__('Success Message', 'ns-real-estate'),
	                			'name' => 'ns_agent_form_success',
	                			'value' => $settings['ns_agent_form_success'],
	                			'type' => 'text',
	                			'parent_val' => 'contact',
                			),
                			'form_submit_text' => array(
                				'title' => esc_html__('Submit Button Text', 'ns-real-estate'),
	                			'name' => 'ns_agent_form_submit_text',
	                			'value' => $settings['ns_agent_form_submit_text'],
	                			'type' => 'text',
	                			'parent_val' => 'contact',
                			),
                		),
                	);
                	$this->build_admin_field($agent_detail_items_field);
                	?>

	            </div>
	        </div>

        	<?php do_action( 'ns_real_estate_after_agent_settings'); ?>

	    </div><!-- end agent settings -->

	    <div id="maps" class="tab-content">
	        <h2><?php echo esc_html_e('Map Settings', 'ns-real-estate'); ?></h2>

	        <?php
	        $google_maps_api_field = array(
                'title' => esc_html__('Google Maps API Key', 'ns-real-estate'),
                'name' => 'ns_real_estate_google_maps_api',
                'description' => wp_kses_post(__('Provide your unique Google maps API key. <a target="_blank" href="https://developers.google.com/maps/documentation/javascript/get-api-key">Click here</a> to get a key.', 'ns-real-estate')),
                'value' => $settings['ns_real_estate_google_maps_api'],
                'type' => 'text',
            );
            $this->build_admin_field($google_maps_api_field);

            $map_zoom_field = array(
                'title' => esc_html__('Default Map Zoom', 'ns-real-estate'),
                'name' => 'ns_real_estate_default_map_zoom',
                'description' => esc_html__('The map zoom ranges from 1 - 19. Zoom level 1 being the most zoomed out.', 'ns-real-estate'),
                'value' => $settings['ns_real_estate_default_map_zoom'],
                'type' => 'number',
                'min' => 1,
                'max' => 19,
            );
            $this->build_admin_field($map_zoom_field);

            $map_lat_field = array(
                'title' => esc_html__('Default Map Latitude', 'ns-real-estate'),
                'name' => 'ns_real_estate_default_map_latitude',
                'value' => $settings['ns_real_estate_default_map_latitude'],
                'type' => 'text',
            );
            $this->build_admin_field($map_lat_field);

            $map_long_field = array(
                'title' => esc_html__('Default Map Longitude', 'ns-real-estate'),
                'name' => 'ns_real_estate_default_map_longitude',
                'value' => $settings['ns_real_estate_default_map_longitude'],
                'type' => 'text',
            );
            $this->build_admin_field($map_long_field);

            $map_custom_pin_field = array(
                'title' => esc_html__('Custom Pin Image', 'ns-real-estate'),
                'name' => 'ns_real_estate_google_maps_pin',
                'value' => $settings['ns_real_estate_google_maps_pin'],
                'type' => 'image_upload',
            );
            $this->build_admin_field($map_custom_pin_field);
	        ?>

	        <?php do_action( 'ns_real_estate_after_map_settings'); ?>

	    </div><!-- end map settings -->

	    <div id="members" class="tab-content">
	        <h2><?php echo esc_html_e('Member Settings', 'ns-real-estate'); ?></h2>

	        <?php
	        $page_options = array('Select a page' => '');
	        $pages = get_pages();
	        foreach ( $pages as $page ) { $page_options[esc_attr($page->post_title)] = get_page_link( $page->ID ); }
	        $my_properties_page_field = array(
                'title' => esc_html__('Select My Properties Page', 'ns-real-estate'),
                'name' => 'ns_members_my_properties_page',
                'description' => esc_html__('Create a page and assign it the My Properties template.', 'ns-real-estate'),
                'value' => $settings['ns_members_my_properties_page'],
                'type' => 'select',
                'options' => $page_options,
            );
            $this->build_admin_field($my_properties_page_field);

            $submit_property_page_field = array(
                'title' => esc_html__('Select Submit Property Page', 'ns-real-estate'),
                'name' => 'ns_members_submit_property_page',
                'description' => esc_html__('Create a page and assign it the Submit Property template.', 'ns-real-estate'),
                'value' => $settings['ns_members_submit_property_page'],
                'type' => 'select',
                'options' => $page_options,
            );
            $this->build_admin_field($submit_property_page_field);

            $submit_property_approval = array(
                'title' => esc_html__('Front-end property submissions must be approved before being published', 'ns-real-estate'),
                'name' => 'ns_members_submit_property_approval',
                'value' => $settings['ns_members_submit_property_approval'],
                'type' => 'switch',
            );
            $this->build_admin_field($submit_property_approval);

            $submit_add_locations = array(
                'title' => esc_html__('Allow members to add new property locations from the front-end', 'ns-real-estate'),
                'name' => 'ns_members_add_locations',
                'value' => $settings['ns_members_add_locations'],
                'type' => 'switch',
            );
            $this->build_admin_field($submit_add_locations);

            $submit_add_amenities = array(
                'title' => esc_html__('Allow members to add new property amenities from the front-end', 'ns-real-estate'),
                'name' => 'ns_members_add_amenities',
                'value' => $settings['ns_members_add_amenities'],
                'type' => 'switch',
            );
            $this->build_admin_field($submit_add_amenities);

            $submit_form_fields = array(
                'title' => esc_html__('Property Submit Form Fields', 'ns-real-estate'),
                'name' => 'ns_members_submit_property_fields',
                'description' => esc_html__('Choose which fields display on the front-end property submit form.', 'ns-real-estate'),
                'value' => $settings['ns_members_submit_property_fields'],
                'options' => NS_Real_Estate_Properties::load_property_submit_fields(),
                'type' => 'checkbox_group',
            );
            $this->build_admin_field($submit_form_fields);
	        ?>

	        <?php do_action( 'ns_real_estate_after_member_settings'); ?>

	    </div><!-- end members settings -->

	    <div id="currency" class="tab-content">
	        <h2><?php echo esc_html_e('Currency & Numbers', 'ns-real-estate'); ?></h2>

	        <?php
	        $currency_symbol_field = array(
                'title' => esc_html__('Currency Symbol', 'ns-real-estate'),
                'name' => 'ns_real_estate_currency_symbol',
                'value' => $settings['ns_real_estate_currency_symbol'],
                'type' => 'text',
            );
            $this->build_admin_field($currency_symbol_field);

            $currency_symbol_position_field = array(
                'title' => esc_html__('Currency Symbol Position', 'ns-real-estate'),
                'name' => 'ns_real_estate_currency_symbol_position',
                'value' => $settings['ns_real_estate_currency_symbol_position'],
                'type' => 'radio_image',
                'options' => array(esc_html__('Display before price', 'ns-real-estate') => array('value' => 'before'), esc_html__('Display after price', 'ns-real-estate') => array('value' => 'after')),
            );
            $this->build_admin_field($currency_symbol_position_field);

            $currency_thousand_field = array(
                'title' => esc_html__('Thousand Separator', 'ns-real-estate'),
                'name' => 'ns_real_estate_thousand_separator',
                'value' => $settings['ns_real_estate_thousand_separator'],
                'type' => 'text',
            );
            $this->build_admin_field($currency_thousand_field);

            $currency_decimal_field = array(
                'title' => esc_html__('Decimal Separator', 'ns-real-estate'),
                'name' => 'ns_real_estate_decimal_separator',
                'value' => $settings['ns_real_estate_decimal_separator'],
                'type' => 'text',
            );
            $this->build_admin_field($currency_decimal_field);

            $currency_decimal_num_field = array(
                'title' => esc_html__('Number of Decimals', 'ns-real-estate'),
                'name' => 'ns_real_estate_num_decimal',
                'value' => $settings['ns_real_estate_num_decimal'],
                'type' => 'number',
                'min' => 0,
                'max' => 5,
            );
            $this->build_admin_field($currency_decimal_num_field);

            echo '<br/><br/><h2>'.esc_html__('Area Formatting', 'ns-real-estate').'</h2>';
            $area_postfix_field = array(
                'title' => esc_html__('Deafult Area Postfix', 'ns-real-estate'),
                'name' => 'ns_real_estate_default_area_postfix',
                'value' => $settings['ns_real_estate_default_area_postfix'],
                'type' => 'text',
            );
            $this->build_admin_field($area_postfix_field);

            $area_thousand_field = array(
                'title' => esc_html__('Area Thousand Separator', 'ns-real-estate'),
                'name' => 'ns_real_estate_thousand_separator_area',
                'value' => $settings['ns_real_estate_thousand_separator_area'],
                'type' => 'text',
            );
            $this->build_admin_field($area_thousand_field);

            $area_decimal_field = array(
                'title' => esc_html__('Area Decimal Separator', 'ns-real-estate'),
                'name' => 'ns_real_estate_decimal_separator_area',
                'value' => $settings['ns_real_estate_decimal_separator_area'],
                'type' => 'text',
            );
            $this->build_admin_field($area_decimal_field);

            $area_decimal_num_field = array(
                'title' => esc_html__('Area Number of Decimals', 'ns-real-estate'),
                'name' => 'ns_real_estate_num_decimal_area',
                'value' => $settings['ns_real_estate_num_decimal_area'],
                'type' => 'number',
                'min' => 0,
                'max' => 5,
            );
            $this->build_admin_field($area_decimal_num_field);

	        do_action( 'ns_real_estate_after_currency_settings'); ?>

	    </div><!-- end currency settings -->

	    <?php do_action( 'ns_real_estate_after_settings'); ?>

		<?php $output = ob_get_clean();
    	return $output;
	}

	/**
	 *	Add-Ons page
	 */
	public function add_ons_page() {
		$args = array(
			'page_name' => 'Nightshift Real Estate',
			'pages' => $this->get_admin_pages(),
			'content' => $this->add_ons_page_content(),
			'content_class' => 'ns-modules',
			'icon' => plugins_url('/ns-real-estate/images/icon-real-estate.svg'),
		);
	    echo $this->build_admin_page($args);
	}

	public function add_ons_page_content() {
		ob_start();

		$raw_addons = wp_remote_get(
	        constant('NS_SHOP_URL').'/plugins/ns-real-estate/add-ons/',
	        array('timeout'     => 10, 'redirection' => 5, 'sslverify'   => false)
	    );

	    if(!is_wp_error($raw_addons)) {
	        echo '<div class="ns-module-group">';
	        $raw_addons = wp_remote_retrieve_body($raw_addons);
	        $dom = new DOMDocument();
	        libxml_use_internal_errors(true);
	        $dom->loadHTML( $raw_addons );

	        $finder = new DomXPath($dom);
	        $classname = "ns-product-grid";
	        $nodes = $finder->query("//*[contains(@class, '$classname')]");
	 
	        function DOMinnerHTML(DOMNode $element) { 
	            $innerHTML = ""; 
	            $children  = $element->childNodes;
	            $anchors = $element->getElementsByTagName('a');
	            foreach($anchors as $anchor) { $anchor->setAttribute('target','_blank'); }
	            foreach ($children as $child) { $innerHTML .= $element->ownerDocument->saveHTML($child); }
	            return $innerHTML; 
	        } 

	        foreach($nodes as $node) { echo '<div class="admin-module add-on">'.DOMinnerHTML($node).'</div>'; }
	        echo '</div>';
	    }

		$output = ob_get_clean();
    	return $output;
	}

	/**
	 *	License Keys page
	 */
	public function license_keys_page() {
		$args = array(
			'page_name' => 'Nightshift Real Estate',
			'settings_group' => 'ns-real-estate-license-keys-group',
			'pages' => $this->get_admin_pages(),
			'content' => $this->license_keys_page_content(),
			'display_actions' => 'true',
			'ajax' => false,
			'icon' => plugins_url('/ns-real-estate/images/icon-real-estate.svg'),
		);
	    echo $this->build_admin_page($args);
	}

	public function license_keys_page_content() {
		ob_start(); ?>

	    <div class="admin-module-note">
	        <?php esc_html_e('All premium add-ons require a valid license key for updates and support.', 'ns-real-estate'); ?><br/>
	        <?php esc_html_e('Your licenses keys can be found in your account on the Nightshift Products website.', 'ns-real-estate'); ?>
	    </div><br/>
	    
	    <?php do_action( 'ns_real_estate_register_license_keys'); ?>

	    <?php $output = ob_get_clean();
	    return $output;
	}

	/**
	 *	Help page
	 */
	public function help_page() {
		$args = array(
			'page_name' => 'Nightshift Real Estate',
			'pages' => $this->get_admin_pages(),
			'content' => $this->resources_page_content(),
			'display_actions' => 'false',
			'icon' => plugins_url('/ns-real-estate/images/icon-real-estate.svg'),
		);
	    echo $this->build_admin_page($args);
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

	/************************************************************************/
	// Add Field Types
	/************************************************************************/
	
	/**
	 *	Add field types
	 */
	public function add_field_types($field_types) {
		$field_types['floor_plans'] = array($this, 'build_admin_field_floor_plans');
		return $field_types;
	}

	/**
	 *	Build floor plans admin field
	 */
	public function build_admin_field_floor_plans($field) { ?>

		<div class="repeater-container floor-plans">
			<div class="repeater-items">
				<?php 
				$floor_plans = $field['value']; 
				if(!empty($floor_plans) && !empty($floor_plans[0])) { 
	                $count = 0;                     
	                foreach ($floor_plans as $floor_plan) { ?>
	                	<div class="ns-accordion">
                            <div class="ns-accordion-header"><i class="fa fa-chevron-right"></i> <span class="repeater-title-mirror floor-plan-title-mirror"><?php echo $floor_plan['title']; ?></span> <span class="action delete delete-floor-plan"><i class="fa fa-trash"></i> Delete</span></div>
                            <div class="ns-accordion-content floor-plan-item"> 
                                <div class="floor-plan-left"> 
                                    <label><?php esc_html_e('Title:', 'ns-real-estate'); ?> </label> <input class="repeater-title floor-plan-title" type="text" name="<?php echo $field['name']; ?>[<?php echo $count; ?>][title]" placeholder="New Floor Plan" value="<?php echo $floor_plan['title']; ?>" /><br/>
                                    <label><?php esc_html_e('Size:', 'ns-real-estate'); ?> </label> <input type="text" name="<?php echo $field['name']; ?>[<?php echo $count; ?>][size]" value="<?php echo $floor_plan['size']; ?>" /><br/>
                                    <label><?php esc_html_e('Rooms:', 'ns-real-estate'); ?> </label> <input type="number" name="<?php echo $field['name']; ?>[<?php echo $count; ?>][rooms]" value="<?php echo $floor_plan['rooms']; ?>" /><br/>
                                    <label><?php esc_html_e('Bathrooms:', 'ns-real-estate'); ?> </label> <input type="number" name="<?php echo $field['name']; ?>[<?php echo $count; ?>][baths]" value="<?php echo $floor_plan['baths']; ?>" /><br/>
                                </div>
                                <div class="floor-plan-right">
                                    <label><?php esc_html_e('Description:', 'ns-real-estate'); ?></label>
                                    <textarea name="<?php echo $field['name']; ?>[<?php echo $count; ?>][description]"><?php echo $floor_plan['description']; ?></textarea>
                                    <div class="floor-plan-img">
                                        <label><?php esc_html_e('Image:', 'ns-real-estate'); ?> </label> 
                                        <input type="text" name="<?php echo $field['name']; ?>[<?php echo $count; ?>][img]" value="<?php echo $floor_plan['img']; ?>" />
                                        <input id="_btn" class="ns_upload_image_button" type="button" value="<?php esc_html_e('Upload Image', 'ns-real-estate'); ?>" />
                                        <span class="button-secondary remove"><?php esc_html_e('Remove', 'ns-real-estate'); ?></span>
                                    </div>
                                </div>
                                <div class="clear"></div>
                            </div>
                        </div> 
	                	<?php $count++; 
	                }
				} ?>
			</div>

			<?php if(empty($floor_plans) && empty($floor_plans[0])) { echo '<p class="admin-module-note no-floor-plan">'.esc_html__('No floor plans were found.', 'ns-real-estate').'</p>'; } ?>
	        <span class="admin-button add-repeater"><i class="fa fa-plus"></i> <?php esc_html_e('Create New Floor Plan', 'ns-real-estate'); ?></span>
	    </div>
	<?php }


}

?>