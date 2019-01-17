<?php
/*****************************************************************/
/** This file maps shortcodes to WPBakery Elements
/** Shortcodes are located in /includes/shortcodes.php
/*****************************************************************/

if(ns_real_estate_is_plugin_active('js_composer/js_composer.php')) {  

	/** LIST PROPERTIES **/
	$num_properties_per_page = esc_attr(get_option('ns_num_properties_per_page', 12));

	add_action('vc_before_init', 'ns_list_properties_callback');
	function ns_list_properties_callback() {
		vc_map(array(
			'name' => esc_html__( 'List Properties', 'ns-real-estate' ),
			'base' => 'ns_list_properties',
			'description' => esc_html__( 'Display your property listings', 'ns-real-estate' ),
			'icon' => plugins_url('/ns-real-estate/images/icon-real-estate.svg'),
			'class' => '',
			'category' => 'NS Real Estate',
			'params' => array(
				array(
					'type' => 'textfield',
					'heading' => esc_html__( 'Show Posts', 'ns-real-estate' ),
					'param_name' => 'show_posts',
					'value' => $num_properties_per_page,
				),
			),
		));
	}

}
?>