<?php
/*-----------------------------------------------------------------------------------*/
/*  UPDATE LICENSE KEY STATUS
/*  - fires only when settings are saved
/*  - processed in NS Basics plugin (ns-basics/includes/license-keys.php)
/*-----------------------------------------------------------------------------------*/
add_action( 'update_option_ns_real_estate_open_houses_license', 'ns_basics_activate_license_key', 10, 3 );

/*-----------------------------------------------------------------------------------*/
/*  OUTPUT LICENSE KEYS PAGE
/*-----------------------------------------------------------------------------------*/
function ns_real_estate_license_keys_page() {
    $page_name = 'NightShift Real Estate';
    $settings_group = 'ns-real-estate-license-keys-group';
    $pages = ns_real_estate_get_admin_pages();
    $display_actions = 'true';
    $content = ns_real_estate_license_keys_page_content();
    $content_class = null;
    $content_nav = null;
    $alerts = null;
    $ajax = false;
    $icon = plugins_url('/ns-real-estate/images/icon-real-estate.svg');
    echo ns_basics_admin_page($page_name, $settings_group, $pages, $display_actions, $content, $content_classl, $content_nav, $alerts, $ajax, $icon);
} 

function ns_real_estate_license_keys_page_content() {
    ob_start();

    do_action( 'ns_real_estate_register_license_keys');

    $output = ob_get_clean();
    return $output;
}


?>