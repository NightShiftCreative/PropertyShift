<?php 

    $admin_obj = new NS_Real_Estate_Admin();
    $settings_init = $admin_obj->load_settings();
    $settings = $admin_obj->get_settings($settings_init);

    /************************************************************************/
    /* MISC STYLES */
    /************************************************************************/
    $misc_css = "";
    $misc_css .= ".property-single-item #map-canvas-one-pin { height:{$settings['ns_property_detail_map_height']}px; }";

    wp_add_inline_style( 'ns-real-estate-dynamic-styles', $misc_css );


    /************************************************************************/
    /* RTL(Right to Left) STYLES */
    /************************************************************************/
    if(isset($_GET['rtl'])) { $rtl = $_GET['rtl']; } else { $rtl = esc_attr(get_option('ns_core_rtl')); }  

    if($rtl == 'true') {
        $rtl_css = "";
        
        //RTL STYLES GO HERE

        wp_add_inline_style( 'ns-real-estate-dynamic-styles', $rtl_css );
    }
    
?>