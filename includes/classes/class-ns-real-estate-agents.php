<?php
// Exit if accessed directly
if (!defined( 'ABSPATH')) { exit; }

/**
 *	NS_Real_Estate_Agents class
 *
 */
class NS_Real_Estate_Agents {

	/**
	 *	Constructor
	 */
	public function __construct() {

	}

	/**
	 *	Load agent detail items
	 */
	public static function load_agent_detail_items() {
		$agent_detail_items_init = array(
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
	            'name' => esc_html__('Contact', 'ns-real-estate'),
	            'label' => esc_html__('Contact', 'ns-real-estate'),
	            'slug' => 'contact',
	            'active' => 'true',
	            'sidebar' => 'false',
	        ),
	        3 => array(
	            'name' => esc_html__('Properties', 'ns-real-estate'),
	            'label' => esc_html__('Properties', 'ns-real-estate'),
	            'slug' => 'properties',
	            'active' => 'true',
	            'sidebar' => 'false',
	        ),
	    );

		$agent_detail_items_init = apply_filters( 'ns_real_estate_agent_detail_items_init_filter', $agent_detail_items_init);
	    return $agent_detail_items_init;
	}

}
?>