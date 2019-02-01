<?php
/*-----------------------------------------------------------------------------------*/
/*  GENERATE LICENSE KEYS PAGE
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

/*-----------------------------------------------------------------------------------*/
/*  OUTPUT PAGE CONTENT
/*-----------------------------------------------------------------------------------*/
function ns_real_estate_license_keys_page_content() {
    ob_start(); ?>

    <div class="admin-module-note">
        <?php esc_html_e('All premium add-ons require a valid license key. You can manage your license keys here.', 'ns-real-estate'); ?>
        <a href="#" target="_blank">Lost your key?</a>
    </div>
    
    <?php do_action( 'ns_real_estate_register_license_keys'); ?>

    <?php $output = ob_get_clean();
    return $output;
}


?>