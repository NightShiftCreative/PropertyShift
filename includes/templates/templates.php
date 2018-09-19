<?php
/*-----------------------------------------------------------------------------------*/
/*  Global
/*-----------------------------------------------------------------------------------*/
function rype_real_estate_template_loader($template, $template_args = array()) {
	$theme_file = locate_template(array( 'template_parts/real_estate/' . $template));

	if(empty($theme_file)) {
		include( plugin_dir_path( __FILE__ ) . $template);
	} else {
		include(get_parent_theme_file_path('/template_parts/real_estate/'.$template));
	}
}

/*-----------------------------------------------------------------------------------*/
/*  Property Templates
/*-----------------------------------------------------------------------------------*/

/*  Load properties template */
function rype_real_estate_template_properties(array $custom_args, $custom_show_filter, $custom_layout, $custom_pagination, $no_post_message = 'Sorry, no properties were found.' ) {
    
    $template = 'loop_properties.php';
	$theme_file = locate_template(array( 'template_parts/real_estate/' . $template));

	if(empty($theme_file)) {
		include( plugin_dir_path( __FILE__ ) . $template);
	} else {
		include(get_parent_theme_file_path('/template_parts/real_estate/'.$template));
	}
     
}

/*  Load property single template */
function rype_real_estate_template_property_single( $content ) {
	ob_start();

	$template = 'loop_property_single.php';
	$theme_file = locate_template(array( 'template_parts/real_estate/' . $template));

	if(is_singular('rype-property')) {
		if(empty($theme_file)) {
	    	include( plugin_dir_path( __FILE__ ) . $template);
	    }
	}

    $content = $content.ob_get_clean();
    return $content;
}
add_filter( 'the_content', 'rype_real_estate_template_property_single', 20 );


/*-----------------------------------------------------------------------------------*/
/*  Agent Templates
/*-----------------------------------------------------------------------------------*/

/*  Load agents template */
function rype_real_estate_template_agents(array $custom_args, $custom_pagination, $no_post_message = 'Sorry, no agents were found.') {
    
    $template = 'loop_agents.php';
	$theme_file = locate_template(array( 'template_parts/real_estate/' . $template));

	if(empty($theme_file)) {
		include( plugin_dir_path( __FILE__ ) . $template);
	} else {
		include(get_parent_theme_file_path('/template_parts/real_estate/'.$template));
	}
}

/*  Load agent single template */
function rype_real_estate_template_agent_single( $content ) {
	ob_start();

	$template = 'loop_agent_single.php';
	$theme_file = locate_template(array( 'template_parts/real_estate/' . $template));

	if(is_singular('rype-agent')) {
		if(empty($theme_file)) {
	    	include( plugin_dir_path( __FILE__ ) . $template);
	    }
	}

    $content = $content.ob_get_clean();
    return $content;
}
add_filter( 'the_content', 'rype_real_estate_template_agent_single', 20 );

?>