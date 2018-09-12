<?php

add_action('admin_menu', 'rype_real_estate_plugin_menu');
function rype_real_estate_plugin_menu() {
    add_menu_page('Rype Real Estate', 'Rype Real Estate', 'administrator', 'rype-real-estate-settings', 'rype_real_estate_settings_page', 'dashicons-admin-home');
    add_submenu_page('rype-real-estate-settings', 'Add-Ons', 'Add-Ons', 'administrator', 'rype-real-estate-add-ons', 'rype_real_estate_add_ons_page');
    add_submenu_page('rype-real-estate-settings', 'Help', 'Help', 'administrator', 'rype-real-estate-help', 'rype_real_estate_help_page');
    add_action( 'admin_init', 'rype_real_estate_register_options' );
}

/*-----------------------------------------------------------------------------------*/
/*  REGISTER SETTINGS
/*-----------------------------------------------------------------------------------*/
function rype_real_estate_register_options() {
    register_setting( 'rype-real-estate-settings-group', 'properties_page');
}

/*-----------------------------------------------------------------------------------*/
/*  OUTPUT SETTINGS PAGE
/*-----------------------------------------------------------------------------------*/
function rype_real_estate_settings_page() {
    $page_name = 'Rype Real Estate';
    $settings_group = 'rype-real-estate-settings-group';
    $pages = array();
    $pages[] = array('slug' => 'rype-real-estate-settings', 'name' => 'Settings', 'active' => 'true');
    $pages[] = array('slug' => 'rype-real-estate-add-ons', 'name' => 'Add-Ons', 'active' => 'false');
    $pages[] = array('slug' => 'rype-real-estate-help', 'name' => 'Help', 'active' => 'false');
    $display_actions = 'true';
    $content = rype_real_estate_settings_page_content();
    echo rype_basics_admin_page($page_name, $settings_group, $pages, $display_actions, $content);
} 

function rype_real_estate_settings_page_content() {
    ob_start(); ?>
    <h3><?php esc_html_e('Settings', 'rype-real-estate'); ?></h3>
    <?php $output = ob_get_clean();
    return $output;
}


/*-----------------------------------------------------------------------------------*/
/*  OUTPUT ADD-ONS PAGE
/*-----------------------------------------------------------------------------------*/
function rype_real_estate_add_ons_page() { 
	$page_name = 'Rype Real Estate';
    $settings_group = 'ype-real-estate-add-ons-group';
    $pages = array();
    $pages[] = array('slug' => 'rype-real-estate-settings', 'name' => 'Settings', 'active' => 'false');
    $pages[] = array('slug' => 'rype-real-estate-add-ons', 'name' => 'Add-Ons', 'active' => 'true');
    $pages[] = array('slug' => 'rype-real-estate-help', 'name' => 'Help', 'active' => 'false');
    $display_actions = 'true';
    $content = rype_real_estate_add_ons_page_content();
    echo rype_basics_admin_page($page_name, $settings_group, $pages, $display_actions, $content);
}

function rype_real_estate_add_ons_page_content() {
    ob_start(); ?>
    <h3><?php esc_html_e('Add-Ons', 'rype-real-estate'); ?></h3>
    <?php $output = ob_get_clean();
    return $output;
}

/*-----------------------------------------------------------------------------------*/
/*  OUTPUT HELP PAGE
/*-----------------------------------------------------------------------------------*/
function rype_real_estate_help_page() { 
    $page_name = 'Rype Real Estate';
    $settings_group = null;
    $pages = array();
    $pages[] = array('slug' => 'rype-real-estate-settings', 'name' => 'Settings', 'active' => 'false');
    $pages[] = array('slug' => 'rype-real-estate-add-ons', 'name' => 'Add-Ons', 'active' => 'false');
    $pages[] = array('slug' => 'rype-real-estate-help', 'name' => 'Help', 'active' => 'true');
    $display_actions = 'false';
    $content = rype_real_estate_help_page_content();
    echo rype_basics_admin_page($page_name, $settings_group, $pages, $display_actions, $content);
}

function rype_real_estate_help_page_content() {
    ob_start(); ?>
    <h3><?php esc_html_e('Help', 'rype-real-estate'); ?></h3>
    <?php $output = ob_get_clean();
    return $output;
}


?>