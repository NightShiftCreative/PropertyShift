<?php

/*-----------------------------------------------------------------------------------*/
/*  Load property single template
/*-----------------------------------------------------------------------------------*/
function rype_real_estate_the_content_filter( $content ) {
	ob_start();

	$template = 'loop_property_single.php';
	$theme_file = locate_template(array( 'template_parts/real_estate/' . $template));

	if(is_singular('rype-property')) {
		if(empty($theme_file)) {
	    	include( plugin_dir_path( __FILE__ ) . 'loop_property_single.php');
	    }
	}

    $content = ob_get_clean();
    return $content;
}
add_filter( 'the_content', 'rype_real_estate_the_content_filter', 20 );

?>