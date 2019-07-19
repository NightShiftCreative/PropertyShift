<?php
// Exit if accessed directly
if (!defined( 'ABSPATH')) { exit; }

/**
 *	NS_Real_Estate_Properties class
 *
 */
class NS_Real_Estate_Properties {

	/**
	 *	Constructor
	 */
	public function __construct() {
		$this->add_image_sizes();
	}

	/**
	 *	Add Image Sizes
	 */
	public function add_image_sizes() {
		add_image_size( 'property-thumbnail', 800, 600, array( 'center', 'center' ) );
	}

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