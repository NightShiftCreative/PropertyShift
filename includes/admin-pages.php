<?php

add_action('admin_menu', 'rype_real_estate_plugin_menu');
function rype_real_estate_plugin_menu() {
    add_menu_page('Rype Real Estate', 'Rype Real Estate', 'administrator', 'rype-real-estate-plugin-settings', 'rype_real_estate_plugin_settings_page', 'dashicons-admin-generic');
    //add_submenu_page('rype-real-estate-plugin-settings', 'License Keys', 'License Keys', 'administrator', 'rao-license-keys', 'rao_license_keys_page');
    //add_action( 'admin_init', 'rao_register_options' );
}

/*-----------------------------------------------------------------------------------*/
/*  OUTPUT SETTINGS PAGE
/*-----------------------------------------------------------------------------------*/
function rype_real_estate_plugin_settings_page() { ?>

    <div class="wrap">
        <h1><?php esc_html_e('Rype Real Estate', 'rype-add-ons'); ?></h1>
    </div>

<?php } 

?>