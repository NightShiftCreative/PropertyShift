<?php

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
/*  GET ALL PAGES
/*-----------------------------------------------------------------------------------*/
function rype_real_estate_get_admin_pages() {
    $pages = array();
    $pages[] = array('slug' => 'rype-real-estate-settings', 'name' => 'Settings');
    $pages[] = array('slug' => 'rype-real-estate-add-ons', 'name' => 'Add-Ons');
    $pages[] = array('slug' => 'rype-real-estate-license-keys', 'name' => 'License Keys');
    $pages[] = array('slug' => 'rype-real-estate-help', 'name' => 'Help');
    return $pages;
}

/*-----------------------------------------------------------------------------------*/
/*  OUTPUT SETTINGS PAGE
/*-----------------------------------------------------------------------------------*/
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


?>