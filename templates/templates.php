<?php

/*-----------------------------------------------------------------------------------*/
/*  Load property single template
/*-----------------------------------------------------------------------------------*/
function rype_real_estate_load_property_single_template($template) {
    global $post;

    if ($post->post_type == "rype-property" && $template !== locate_template(array("single-rype-property.php"))){
        return plugin_dir_path( __FILE__ ) . "single-rype-property.php";
    }

    return $template;
}

add_filter('single_template', 'rype_real_estate_load_property_single_template');

?>