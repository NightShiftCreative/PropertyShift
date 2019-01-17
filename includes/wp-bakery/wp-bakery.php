<?php
/*****************************************************************/
/** This file maps shortcodes to WPBakery Elements
/** Shortcodes are located in /includes/shortcodes.php
/*****************************************************************/

if(ns_real_estate_is_plugin_active('js_composer/js_composer.php')) {  

	/** LIST PROPERTIES **/
	add_action('vc_before_init', 'ns_list_properties_callback');
	function ns_list_properties_callback() {
		vc_map(array(
			'name' => esc_html__( 'List Properties', 'ns-real-estate' ),
			'base' => 'ns_list_properties',
			'class' => '',
			'category' => 'Content',
			'params' => array(
				array(
					'type' => 'textfield',
					'heading' => 'Test',
					'param_name' => 'test',
				),
			),
		));
	}

}
?>