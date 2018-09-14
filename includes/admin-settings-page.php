<?php
/*-----------------------------------------------------------------------------------*/
/*  ADD ADMIN MENU PAGES
/*-----------------------------------------------------------------------------------*/
add_action('admin_menu', 'rype_real_estate_plugin_menu');
function rype_real_estate_plugin_menu() {
    add_menu_page('Rype Real Estate', 'Rype Real Estate', 'administrator', 'rype-real-estate-settings', 'rype_real_estate_settings_page', 'dashicons-admin-home');
    add_submenu_page('rype-real-estate-settings', 'Add-Ons', 'Add-Ons', 'administrator', 'rype-real-estate-add-ons', 'rype_real_estate_add_ons_page');
    add_submenu_page('rype-real-estate-settings', 'License Keys', 'License Keys', 'administrator', 'rype-real-estate-license-keys', 'rype_real_estate_license_keys_page');
    add_submenu_page('rype-real-estate-settings', 'Help', 'Help', 'administrator', 'rype-real-estate-help', 'rype_real_estate_help_page');
    add_action( 'admin_init', 'rype_real_estate_register_options' );
}

/*-----------------------------------------------------------------------------------*/
/*  REGISTER SETTINGS
/*-----------------------------------------------------------------------------------*/
function rype_real_estate_register_options() {

    //MAIN SETTINGS
    register_setting( 'rype-real-estate-settings-group', 'properties_page');

    //LICENSE KEY SETTINGS
    register_setting( 'rype-real-estate-license-keys-group', 'rype_real_estate_open_houses_license');
}

/*-----------------------------------------------------------------------------------*/
/*  OUTPUT SETTINGS PAGE
/*-----------------------------------------------------------------------------------*/
function rype_real_estate_get_admin_pages() {
    $pages = array();
    $pages[] = array('slug' => 'rype-real-estate-settings', 'name' => 'Settings');
    $pages[] = array('slug' => 'rype-real-estate-add-ons', 'name' => 'Add-Ons');
    $pages[] = array('slug' => 'rype-real-estate-license-keys', 'name' => 'License Keys');
    $pages[] = array('slug' => 'rype-real-estate-help', 'name' => 'Help');
    return $pages;
}

function rype_real_estate_settings_page() {
    $page_name = 'Rype Real Estate';
    $settings_group = 'rype-real-estate-settings-group';
    $pages = rype_real_estate_get_admin_pages();
    $display_actions = 'true';
    $content = rype_real_estate_settings_page_content();
    echo rype_basics_admin_page($page_name, $settings_group, $pages, $display_actions, $content);
} 

function rype_real_estate_settings_page_content() {
    ob_start(); 
    //content goes here
    $output = ob_get_clean();
    return $output;
}

/*-----------------------------------------------------------------------------------*/
/*  Load default Property Detail Items
/*-----------------------------------------------------------------------------------*/
function rao_load_default_property_detail_items() {
    $property_detail_items_default = array(
        0 => array(
            'name' => esc_html__('Overview', 'rype-real-estate'),
            'label' => esc_html__('Overview', 'rype-real-estate'),
            'slug' => 'overview',
            'active' => 'true',
            'sidebar' => 'false',
        ),
        1 => array(
            'name' => esc_html__('Description', 'rype-real-estate'),
            'label' => esc_html__('Description', 'rype-real-estate'),
            'slug' => 'description',
            'active' => 'true',
            'sidebar' => 'false',
        ),
        2 => array(
            'name' => esc_html__('Gallery', 'rype-real-estate'),
            'label' => esc_html__('Gallery', 'rype-real-estate'),
            'slug' => 'gallery',
            'active' => 'true',
            'sidebar' => 'false',
        ),
        3 => array(
            'name' => esc_html__('Video', 'rype-real-estate'),
            'label' => esc_html__('Video', 'rype-real-estate'),
            'slug' => 'video',
            'active' => 'true',
            'sidebar' => 'false',
        ),
        4 => array(
            'name' => esc_html__('Amenities', 'rype-real-estate'),
            'label' => esc_html__('Amenities', 'rype-real-estate'),
            'slug' => 'amenities',
            'active' => 'true',
            'sidebar' => 'false',
        ),
        5 => array(
            'name' => esc_html__('Floor Plans', 'rype-real-estate'),
            'label' => esc_html__('Floor Plans', 'rype-real-estate'),
            'slug' => 'floor_plans',
            'active' => 'true',
            'sidebar' => 'false',
        ),
        6 => array(
            'name' => esc_html__('Location', 'rype-real-estate'),
            'label' => esc_html__('Location', 'rype-real-estate'),
            'slug' => 'location',
            'active' => 'true',
            'sidebar' => 'false',
        ),
        7 => array(
            'name' => esc_html__('Walk Score', 'rype-real-estate'),
            'label' => esc_html__('Walk Score', 'rype-real-estate'),
            'slug' => 'walk_score',
            'active' => 'true',
            'sidebar' => 'false',
        ),
        8 => array(
            'name' => esc_html__('Agent Info', 'rype-real-estate'),
            'label' => 'Agent Information',
            'slug' => 'agent_info',
            'active' => 'true',
            'sidebar' => 'false',
        ),
        9 => array(
            'name' => esc_html__('Related Properties', 'rype-real-estate'),
            'label' => 'Related Properties',
            'slug' => 'related',
            'active' => 'true',
            'sidebar' => 'false',
        ),
    );

    return $property_detail_items_default;
}

?>