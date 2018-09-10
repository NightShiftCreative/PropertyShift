<?php 

    /************************************************************************/
    /* MISC STYLES */
    /************************************************************************/
    $property_detail_map_height = esc_attr(get_option('rypecore_property_detail_map_height', 250));
    $misc_css = "";

    //PROPERTIES
    $misc_css .= ".property-single-item #map-canvas-one-pin { height:{$property_detail_map_height}px; }";

    wp_add_inline_style( 'rype-real-estate-dynamic-styles', $misc_css );


    /************************************************************************/
    /* RTL(Right to Left) STYLES */
    /************************************************************************/
    if(isset($_GET['rtl'])) { $rtl = $_GET['rtl']; } else { $rtl = esc_attr(get_option('rypecore_rtl')); }  

    if($rtl == 'true') {
        $rtl_css = "";
        
        //RTL STYLES GO HERE

        wp_add_inline_style( 'rype-real-estate-dynamic-styles', $rtl_css );
    }
    
?>