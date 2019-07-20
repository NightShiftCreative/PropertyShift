<?php
// Exit if accessed directly
if (!defined( 'ABSPATH')) { exit; }

/**
 *	NS_Real_Estate_Properties class
 *
 */
class NS_Real_Estate_Properties {

	/************************************************************************/
	// Initialize
	/************************************************************************/

	public function __construct() {

		// Get global settings
		$this->admin_obj = new NS_Real_Estate_Admin();
        $settings_init = $this->admin_obj->load_settings();
        $this->global_settings = $this->admin_obj->get_settings($settings_init);
	}

	/**
	 *	Init
	 */
	public function init() {
		add_action('init', array( $this, 'rewrite_rules' ));
		add_action( 'ns_basics_page_settings_init_filter', array( $this, 'add_page_settings' ));
		$this->add_image_sizes();
		add_action( 'init', array( $this, 'add_custom_post_type' ));
	}

	/**
	 *	Add Image Sizes
	 */
	public function add_image_sizes() {
		add_image_size( 'property-thumbnail', 800, 600, array( 'center', 'center' ) );
	}

	/**
	 *	Rewrite Rules
	 */
	public function rewrite_rules() {
		add_rewrite_rule('^properties/page/([0-9]+)','index.php?pagename=properties&paged=$matches[1]', 'top');
	}

	/************************************************************************/
	// Properties Custom Post Type
	/************************************************************************/

	/**
	 *	Add custom post type
	 */
	public function add_custom_post_type() {
		$properties_slug = $this->global_settings['ns_property_detail_slug'];
	    register_post_type( 'ns-property',
	        array(
	            'labels' => array(
	                'name' => __( 'Properties', 'ns-real-estate' ),
	                'singular_name' => __( 'Property', 'ns-real-estate' ),
	                'add_new_item' => __( 'Add New Property', 'ns-real-estate' ),
	                'search_items' => __( 'Search Properties', 'ns-real-estate' ),
	                'edit_item' => __( 'Edit Property', 'ns-real-estate' ),
	            ),
	        'public' => true,
	        'show_in_menu' => true,
	        'menu_icon' => 'dashicons-admin-home',
	        'has_archive' => false,
	        'supports' => array('title', 'author', 'editor', 'revisions', 'thumbnail', 'page_attributes'),
	        'rewrite' => array('slug' => $properties_slug),
	        )
	    );
	}


	/************************************************************************/
	// Retrieving Property Information
	/************************************************************************/

	/**
	 *	Count properties
	 *
	 * @param string $type
	 * @param int $user_id 
	 */
	public function count_properties($type, $user_id = null) {
		$args_total_properties = array(
            'post_type' => 'ns-property',
            'showposts' => -1,
            'author_name' => $user_id,
            'post_status' => $type 
        );

        $meta_posts = get_posts( $args_total_properties );
        $meta_post_count = count( $meta_posts );
        unset( $meta_posts);
        return $meta_post_count;
	}

	/**
	 *	Get formatted price
	 *
	 * @param string $price
	 */
	public function get_formatted_price($price) {

	    $currency_symbol = $this->global_settings['ns_real_estate_currency_symbol'];
	    $currency_symbol_position = $this->global_settings['ns_real_estate_currency_symbol_position'];
	    $currency_thousand = $this->global_settings['ns_real_estate_thousand_separator'];
	    $currency_decimal = $this->global_settings['ns_real_estate_decimal_separator'];
	    $currency_decimal_num =  $this->global_settings['ns_real_estate_num_decimal'];

	    if(!empty($price)) { $price = number_format($price, $currency_decimal_num, $currency_decimal, $currency_thousand); }
	    if($currency_symbol_position == 'before') { $price = $currency_symbol.$price; } else { $price = $price.$currency_symbol; }

	    return $price;
	}

	/**
	 *	Get formatted area
	 *
	 * @param string $area
	 */
	public function get_formatted_area($area) {
		
	    $decimal_num_area = $this->global_settings['ns_real_estate_num_decimal_area'];
	    $decimal_area = $this->global_settings['ns_real_estate_decimal_separator_area'];
	    $thousand_area =  $this->global_settings['ns_real_estate_thousand_separator_area'];

    	if(!empty($area)) { $area = number_format($area, $decimal_num_area, $decimal_area, $thousand_area); }
    	return $area;
	}


	/************************************************************************/
	// Page Settings Methods
	/************************************************************************/
	
	/**
	 *	Add page settings
	 *
	 * @param array $page_settings_init
	 */
	public function add_page_settings($page_settings_init) {
		
		// Add map banner options
		$page_settings_init['banner_source']['options'][esc_html__('Map Banner', 'ns-real-estate')] = array(
			'value' => 'properties_map', 
			'icon' => NS_BASICS_PLUGIN_DIR.'/images/google-maps-icon.png', 
		);

		// Add filter banner options
		$page_settings_init['property_filter_override'] = array(
			'group' => 'banner',
			'title' => esc_html__('Use Custom Property Filter Settings', 'ns-real-estate'),
			'name' => 'ns_banner_property_filter_override',
			'description' => esc_html__('The global property filter settings can be configured in NS Real Estate > Settings', 'ns-real-estate'),
			'value' => 'false',
			'type' => 'switch',
			'children' => array(
				'property_filter_display' => array(
					'title' => esc_html__('Display Property Filter', 'ns-real-estate'),
					'name' => 'ns_banner_property_filter_display',
					'type' => 'checkbox',
					'value' => 'true',
				),
				'property_filter_id' => array(
					'title' => esc_html__('Select a Filter', 'ns-real-estate'),
					'name' => 'ns_banner_property_filter_id',
					'type' => 'select',
					'options' => array(),
				),
			),
		);

		return $page_settings_init;
	}

	/************************************************************************/
	// Property Detail Methods
	/************************************************************************/

	/**
	 *	Load property detail items
	 */
	public static function load_property_detail_items() {
		$property_detail_items_init = array(
	        0 => array(
	            'name' => esc_html__('Overview', 'ns-real-estate'),
	            'label' => esc_html__('Overview', 'ns-real-estate'),
	            'slug' => 'overview',
	            'active' => 'true',
	            'sidebar' => 'false',
	        ),
	        1 => array(
	            'name' => esc_html__('Description', 'ns-real-estate'),
	            'label' => esc_html__('Description', 'ns-real-estate'),
	            'slug' => 'description',
	            'active' => 'true',
	            'sidebar' => 'false',
	        ),
	        2 => array(
	            'name' => esc_html__('Gallery', 'ns-real-estate'),
	            'label' => esc_html__('Gallery', 'ns-real-estate'),
	            'slug' => 'gallery',
	            'active' => 'true',
	            'sidebar' => 'false',
	        ),
	        3 => array(
	            'name' => esc_html__('Property Details', 'ns-real-estate'),
	            'label' => esc_html__('Property Details', 'ns-real-estate'),
	            'slug' => 'property_details',
	            'active' => 'true',
	            'sidebar' => 'false',
	        ),
	        4 => array(
	            'name' => esc_html__('Video', 'ns-real-estate'),
	            'label' => esc_html__('Video', 'ns-real-estate'),
	            'slug' => 'video',
	            'active' => 'true',
	            'sidebar' => 'false',
	        ),
	        5 => array(
	            'name' => esc_html__('Amenities', 'ns-real-estate'),
	            'label' => esc_html__('Amenities', 'ns-real-estate'),
	            'slug' => 'amenities',
	            'active' => 'true',
	            'sidebar' => 'false',
	        ),
	        6 => array(
	            'name' => esc_html__('Floor Plans', 'ns-real-estate'),
	            'label' => esc_html__('Floor Plans', 'ns-real-estate'),
	            'slug' => 'floor_plans',
	            'active' => 'true',
	            'sidebar' => 'false',
	        ),
	        7 => array(
	            'name' => esc_html__('Location', 'ns-real-estate'),
	            'label' => esc_html__('Location', 'ns-real-estate'),
	            'slug' => 'location',
	            'active' => 'true',
	            'sidebar' => 'false',
	        ),
	        8 => array(
	            'name' => esc_html__('Walk Score', 'ns-real-estate'),
	            'label' => esc_html__('Walk Score', 'ns-real-estate'),
	            'slug' => 'walk_score',
	            'active' => 'true',
	            'sidebar' => 'false',
	        ),
	        9 => array(
	            'name' => esc_html__('Agent Info', 'ns-real-estate'),
	            'label' => 'Agent Information',
	            'slug' => 'agent_info',
	            'active' => 'true',
	            'sidebar' => 'false',
	        ),
	        10 => array(
	            'name' => esc_html__('Related Properties', 'ns-real-estate'),
	            'label' => 'Related Properties',
	            'slug' => 'related',
	            'active' => 'true',
	            'sidebar' => 'false',
	        ),
	    );

		$property_detail_items_init = apply_filters( 'ns_real_estate_property_detail_items_init_filter', $property_detail_items_init);
	    return $property_detail_items_init;
	}

	/************************************************************************/
	// Property Submit Methods
	/************************************************************************/

	/**
	 *	Load front-end property submit fields
	 */
	public static function load_property_submit_fields() {
		$property_submit_fields_init = array(
			'property_title' => array('value' => esc_html__('Property Title (required)', 'ns-real-estate'), 'attributes' => array('disabled', 'checked')),
            'price' => array('value' => esc_html__('Price (required)', 'ns-real-estate'), 'attributes' => array('disabled', 'checked')),
            'price_postfix' => array('value' => esc_html__('Price Postfix', 'ns-real-estate')),
            'street_address' => array('value' => esc_html__('Street Address (required)', 'ns-real-estate'), 'attributes' => array('disabled', 'checked')),
            'description' => array('value' => esc_html__('Description', 'ns-real-estate')),
            'beds' => array('value' => esc_html__('Beds', 'ns-real-estate')),
            'baths' => array('value' => esc_html__('Baths', 'ns-real-estate')),
            'garages' => array('value' => esc_html__('Garages', 'ns-real-estate')),
            'area' => array('value' => esc_html__('Area', 'ns-real-estate')),
            'area_postfix' => array('value' => esc_html__('Area Postfix', 'ns-real-estate')),
            'video' => array('value' => esc_html__('Video', 'ns-real-estate')),
            'property_location' => array('value' => esc_html__('Property Location', 'ns-real-estate')),
            'property_type' => array('value' => esc_html__('Property Type', 'ns-real-estate')),
            'property_status' => array('value' => esc_html__('Property Status', 'ns-real-estate')),
            'amenities' => array('value' => esc_html__('Amenities', 'ns-real-estate')),
            'floor_plans' => array('value' => esc_html__('Floor Plans', 'ns-real-estate')),
            'featured_image' => array('value' => esc_html__('Featured Image', 'ns-real-estate')),
            'gallery_images' => array('value' => esc_html__('Gallery Images', 'ns-real-estate')),
            'map' => array('value' => esc_html__('Map', 'ns-real-estate')),
            'owner_info' => array('value' => esc_html__('Owner Info', 'ns-real-estate')),
	    );
	    $property_submit_fields_init = apply_filters( 'ns_real_estate_property_submit_fields_init_filter', $property_submit_fields_init);
	    return $property_submit_fields_init;
	}

}
?>