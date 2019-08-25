<?php
/*-----------------------------------------------------------------------------------*/
/*  Global Template Loader
/*  Used for core plugin and add-ons
/*-----------------------------------------------------------------------------------*/
function ns_real_estate_template_loader($template, $template_args = array(), $wrapper = true, $plugin_path = null) {
	$theme_file = locate_template(array( 'ns-real-estate/' . $template));

	if($wrapper == true) { echo '<div class="ns-real-estate">'; }
	if(empty($theme_file)) {
		if(empty($plugin_path)) { $plugin_path = plugin_dir_path( __FILE__ ); }
		include( $plugin_path . $template);
	} else {
		include(get_parent_theme_file_path('/ns-real-estate/'.$template));
	}
	if($wrapper == true) { echo '</div>'; }
}

/*-----------------------------------------------------------------------------------*/
/*  Global Single Template Loader
/*  Used for core plugin and add-ons
/*-----------------------------------------------------------------------------------*/
function ns_real_estate_template_loader_single($template, $post_type, $plugin_path = null) {

	$theme_file = locate_template(array( 'ns-real-estate/' . $template));

	if(is_singular($post_type)) {
		if(empty($theme_file)) {
			echo '<div class="ns-real-estate">'; 
			if(empty($plugin_path)) { $plugin_path = plugin_dir_path( __FILE__ ); }
	    	include( $plugin_path . $template);
	    	echo '</div>';
	    } else {
	    	include(get_parent_theme_file_path('/ns-real-estate/'.$template));
	    }
	}
}


/*-----------------------------------------------------------------------------------*/
/*  Property Single Template
/*-----------------------------------------------------------------------------------*/
function ns_real_estate_template_property_single( $content ) {
	ob_start();
	ns_real_estate_template_loader_single('loop_property_single.php', 'ns-property');
    $content = $content.ob_get_clean();
    return $content;
}
add_filter( 'the_content', 'ns_real_estate_template_property_single', 20 );


/*-----------------------------------------------------------------------------------*/
/*  Agent Single Template
/*-----------------------------------------------------------------------------------*/
function ns_real_estate_template_agent_single( $content ) {
	ob_start();
	ns_real_estate_template_loader_single('loop_agent_single.php', 'ps-agent');
    $content = $content.ob_get_clean();
    return $content;
}
add_filter( 'the_content', 'ns_real_estate_template_agent_single', 20 );

?>