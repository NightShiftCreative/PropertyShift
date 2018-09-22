<?php
/*-----------------------------------------------------------------------------------*/
/*  Global Template Loader
/*-----------------------------------------------------------------------------------*/
function rype_real_estate_template_loader($template, $template_args = array(), $wrapper = true, $class = null) {
	$theme_file = locate_template(array( 'template_parts/real_estate/' . $template));

	if($wrapper == true) { echo '<div class="rype-real-estate">'; }
	if(empty($theme_file)) {
		include( plugin_dir_path( __FILE__ ) . $template);
	} else {
		include(get_parent_theme_file_path('/template_parts/real_estate/'.$template));
	}
	if($wrapper == true) { echo '</div>'; }
}

/*-----------------------------------------------------------------------------------*/
/*  Property Single Template
/*-----------------------------------------------------------------------------------*/
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
/*  Agent Single Template
/*-----------------------------------------------------------------------------------*/
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