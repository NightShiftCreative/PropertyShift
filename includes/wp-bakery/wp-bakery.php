<?php
/*****************************************************************/
/** This file maps shortcodes to WPBakery Elements
/** Shortcodes are located in /includes/shortcodes.php
/*****************************************************************/ 

add_action('vc_before_init', 'ns_real_estate_vc_map');
function ns_real_estate_vc_map() {

	/** GET GLOBAL SETTINGS **/
	$num_properties_per_page = esc_attr(get_option('ns_num_properties_per_page', 12));
	$num_agents_per_page = esc_attr(get_option('ns_num_agents_per_page', 12));

	/** LIST PROPERTIES **/
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
				'heading' => esc_html__( 'Number of Properties', 'ns-real-estate' ),
				'param_name' => 'show_posts',
				'value' => $num_properties_per_page,
			),
			array(
				'type' => 'dropdown',
				'heading' => esc_html__( 'Show Header', 'ns-real-estate' ),
				'param_name' => 'show_header',
				'value' => array('Yes' => 'true', 'No' => 'false'),
				'std' => 'false',
			),
			array(
				'type' => 'dropdown',
				'heading' => esc_html__( 'Show Pagination', 'ns-real-estate' ),
				'param_name' => 'show_pagination',
				'value' => array('Yes' => 'true', 'No' => 'false'),
				'std' => 'false',
			),
			array(
				'type' => 'dropdown',
				'heading' => esc_html__( 'Layout', 'ns-real-estate' ),
				'param_name' => 'layout',
				'value' => array('Grid' => 'grid', 'Row' => 'row'),
				'std' => 'false',
			),
			array(
				'type' => 'textfield',
				'heading' => esc_html__( 'Property Status', 'ns-real-estate' ),
				'param_name' => 'property_status',
				'description' => esc_html__( 'Enter the property status slug', 'ns-real-estate' ),
			),
			array(
				'type' => 'textfield',
				'heading' => esc_html__( 'Property Location', 'ns-real-estate' ),
				'param_name' => 'property_location',
				'description' => esc_html__( 'Enter the property location slug', 'ns-real-estate' ),
			),
			array(
				'type' => 'textfield',
				'heading' => esc_html__( 'Property Type', 'ns-real-estate' ),
				'param_name' => 'property_type',
				'description' => esc_html__( 'Enter the property type slug', 'ns-real-estate' ),
			),
			array(
				'type' => 'dropdown',
				'heading' => esc_html__( 'Filter By', 'ns-real-estate' ),
				'param_name' => 'featured',
				'value' => array('Most Recent' => 'false', 'Featured' => 'true'),
				'std' => 'false',
			),
		),
	));

	/** LIST PROPERTY TAXONOMY **/
	vc_map(array(
			'name' => esc_html__( 'List Property Taxonomy', 'ns-real-estate' ),
			'base' => 'ns_list_property_tax',
			'description' => esc_html__( 'Display property taxonomy terms', 'ns-real-estate' ),
			'icon' => plugins_url('/ns-real-estate/images/icon-real-estate.svg'),
			'class' => '',
			'category' => 'NS Real Estate',
			'params' => array(
				array(
					'type' => 'dropdown',
					'heading' => esc_html__( 'Taxonomy', 'ns-real-estate' ),
					'param_name' => 'tax',
					'value' => array(
						esc_html__( 'Property Type', 'ns-real-estate' ) => 'property_type', 
						esc_html__( 'Property Status', 'ns-real-estate' ) => 'property_status', 
						esc_html__( 'Property Location', 'ns-real-estate' ) => 'property_location', 
					),
				),
				array(
					'type' => 'textfield',
					'heading' => esc_html__( 'Terms', 'ns-real-estate' ),
					'param_name' => 'terms',
					'description' => esc_html__( 'Comma separated list of term slugs. If left empty, all terms will display.', 'ns-real-estate' ),
				),
				array(
					'type' => 'dropdown',
					'heading' => esc_html__( 'Layout', 'ns-real-estate' ),
					'param_name' => 'layout',
					'value' => array(
						esc_html__( 'Grid', 'ns-real-estate' ) => 'grid', 
						esc_html__( 'Carousel', 'ns-real-estate' ) => 'carousel', 
					),
				),
				array(
					'type' => 'textfield',
					'heading' => esc_html__( 'Number of Terms', 'ns-real-estate' ),
					'param_name' => 'show_posts',
					'value' => 5,
				),
				array(
					'type' => 'dropdown',
					'heading' => esc_html__( 'Order By', 'ns-real-estate' ),
					'param_name' => 'orderby',
					'value' => array(
						esc_html__( 'Count', 'ns-real-estate' ) => 'count', 
						esc_html__( 'Name', 'ns-real-estate' ) => 'name', 
					),
				),
				array(
					'type' => 'dropdown',
					'heading' => esc_html__( 'Order Direction', 'ns-real-estate' ),
					'param_name' => 'order',
					'value' => array(
						esc_html__( 'Descending', 'ns-real-estate' ) => 'DESC', 
						esc_html__( 'Ascending', 'ns-real-estate' ) => 'ASC', 
					),
				),
				array(
					'type' => 'dropdown',
					'heading' => esc_html__( 'Hide Empty Terms', 'ns-real-estate' ),
					'param_name' => 'hide_empty',
					'value' => array(
						esc_html__( 'True', 'ns-real-estate' ) => 'true', 
						esc_html__( 'False', 'ns-real-estate' ) => 'false', 
					),
				),
			),
	));

	/** SUBMIT PROPERTY FORM **/
	vc_map(array(
		'name' => esc_html__( 'Submit Property Form', 'ns-real-estate' ),
		'base' => 'ns_submit_property',
		'description' => esc_html__( 'Allow users to submit a property', 'ns-real-estate' ),
		'icon' => plugins_url('/ns-real-estate/images/icon-real-estate.svg'),
		'class' => '',
		'category' => 'NS Real Estate',
	));

	/** MY PROPERTIES **/
	vc_map(array(
		'name' => esc_html__( 'My Properties', 'ns-real-estate' ),
		'base' => 'ns_my_properties',
		'description' => esc_html__( 'Display the current users properties', 'ns-real-estate' ),
		'icon' => plugins_url('/ns-real-estate/images/icon-real-estate.svg'),
		'class' => '',
		'category' => 'NS Real Estate',
	));

	/** PROPERTY FILTER **/
	vc_map(array(
		'name' => esc_html__( 'Property Filter', 'ns-real-estate' ),
		'base' => 'ns_property_filter',
		'description' => esc_html__( 'Display a property search filter', 'ns-real-estate' ),
		'icon' => plugins_url('/ns-real-estate/images/icon-real-estate.svg'),
		'class' => '',
		'category' => 'NS Real Estate',
		'params' => array(
			array(
				'type' => 'textfield',
				'heading' => esc_html__( 'Filter ID', 'ns-real-estate' ),
				'param_name' => 'id',
				'description' => __( 'Filters can be created and edited <a href="/wp-admin/edit.php?post_type=ns-property-filter" target="_blank">here.</a>', 'ns-real-estate' ),
			),
		),
	));

	/** LIST AGENTS **/
	vc_map(array(
		'name' => esc_html__( 'List Agents', 'ns-real-estate' ),
		'base' => 'ns_list_agents',
		'description' => esc_html__( 'Display a list of agents', 'ns-real-estate' ),
		'icon' => plugins_url('/ns-real-estate/images/icon-real-estate.svg'),
		'class' => '',
		'category' => 'NS Real Estate',
		'params' => array(
			array(
				'type' => 'textfield',
				'heading' => esc_html__( 'Number of Agents', 'ns-real-estate' ),
				'param_name' => 'show_posts',
				'value' => $num_agents_per_page,
			),
			array(
				'type' => 'dropdown',
				'heading' => esc_html__( 'Show Pagination', 'ns-real-estate' ),
				'param_name' => 'show_pagination',
				'value' => array('Yes' => 'true', 'No' => 'false'),
				'std' => 'false',
			),
		),
	));

}
?>