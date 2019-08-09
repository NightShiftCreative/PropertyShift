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


/*-----------------------------------------------------------------------------------*/
/* Get attachment id by url */
/*-----------------------------------------------------------------------------------*/
function ns_real_estate_get_attachment_id_by_url( $url ) {
    // Split the $url into two parts with the wp-content directory as the separator
    $parsed_url = explode( parse_url( WP_CONTENT_URL, PHP_URL_PATH ), $url );
    // Get the host of the current site and the host of the $url, ignoring www
    $this_host = str_ireplace( 'www.', '', parse_url( home_url(), PHP_URL_HOST ) );
    $file_host = str_ireplace( 'www.', '', parse_url( $url, PHP_URL_HOST ) );
    // Return nothing if there aren't any $url parts or if the current host and $url host do not match
    if ( ! isset( $parsed_url[1] ) || empty( $parsed_url[1] ) || ( $this_host != $file_host ) ) {
    return;
    }

    global $wpdb;
    $attachment = $wpdb->get_col( $wpdb->prepare( "SELECT ID FROM {$wpdb->prefix}posts WHERE guid RLIKE %s;", $parsed_url[1] ) );
    // Returns null if no attachment is found
    return $attachment[0];
} 

?>