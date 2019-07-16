<?php

/*-----------------------------------------------------------------------------------*/
/*  Get image sizes
/*-----------------------------------------------------------------------------------*/
function ns_real_estate_get_image_size($size_name) {
    global $_wp_additional_image_sizes;
    $size_output = array();
    $wp_img_sizes = get_intermediate_image_sizes();
    foreach($wp_img_sizes as $size) {
        if($size == $size_name) {
            $size_output['width'] = $_wp_additional_image_sizes[$size]['width'];
            $size_output['height'] = $_wp_additional_image_sizes[$size]['height'];
        }    
    }
    return $size_output;
}

?>