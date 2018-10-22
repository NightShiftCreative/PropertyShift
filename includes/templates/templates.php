<?php
/*-----------------------------------------------------------------------------------*/
/*  Global Template Loader
/*-----------------------------------------------------------------------------------*/
function ns_real_estate_template_loader($template, $template_args = array(), $wrapper = true) {
	$theme_file = locate_template(array( 'ns-real-estate/' . $template));

	if($wrapper == true) { echo '<div class="ns-real-estate">'; }
	if(empty($theme_file)) {
		include( plugin_dir_path( __FILE__ ) . $template);
	} else {
		include(get_parent_theme_file_path('/ns-real-estate/'.$template));
	}
	if($wrapper == true) { echo '</div>'; }
}

/*-----------------------------------------------------------------------------------*/
/*  Property Single Template
/*-----------------------------------------------------------------------------------*/
function ns_real_estate_template_property_single( $content ) {
	ob_start();

	$template = 'loop_property_single.php';
	$theme_file = locate_template(array( 'ns-real-estate/' . $template));

	if(is_singular('ns-property')) {
		if(empty($theme_file)) {
			echo '<div class="ns-real-estate">'; 
	    	include( plugin_dir_path( __FILE__ ) . $template);
	    	echo '</div>';
	    }
	}

    $content = $content.ob_get_clean();
    return $content;
}
add_filter( 'the_content', 'ns_real_estate_template_property_single', 20 );


/*-----------------------------------------------------------------------------------*/
/*  Agent Single Template
/*-----------------------------------------------------------------------------------*/
function ns_real_estate_template_agent_single( $content ) {
	ob_start();

	$template = 'loop_agent_single.php';
	$theme_file = locate_template(array( 'ns-real-estate/' . $template));

	if(is_singular('ns-agent')) {
		if(empty($theme_file)) {
			echo '<div class="ns-real-estate">'; 
	    	include( plugin_dir_path( __FILE__ ) . $template);
	    	echo '</div>';
	    }
	}

    $content = $content.ob_get_clean();
    return $content;
}
add_filter( 'the_content', 'ns_real_estate_template_agent_single', 20 );

?>