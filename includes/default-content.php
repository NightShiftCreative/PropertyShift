<?php
/*-----------------------------------------------------------------------------------*/
/*  Functions run on activation
/*-----------------------------------------------------------------------------------*/

	//Insert default property
	$default_property = array(
      'post_name' => 'sample-property', // The name (slug) for your post
      'post_status' => 'publish', //Set the status of the new post.
      'post_title' => esc_html__('Sample Property', 'ns-real-estate'), //The title of your post.
      'post_type' => 'ns-property', //Sometimes you want to post a page.
      'post_content' => esc_html__('This is your first property. Edit or delete it, then start listing!', 'ns-real-estate'),
    );  
    if(function_exists('ns_core_post_exists_by_slug')) {
    	if (!ns_core_post_exists_by_slug('sample-property', 'ns-property')) { wp_insert_post($default_property); }
    }

  

?>