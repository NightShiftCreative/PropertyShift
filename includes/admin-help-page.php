<?php

/*-----------------------------------------------------------------------------------*/
/*  OUTPUT HELP PAGE
/*-----------------------------------------------------------------------------------*/
function ns_real_estate_help_page() { 
    $page_name = 'NightShift Real Estate';
    $settings_group = null;
    $pages = ns_real_estate_get_admin_pages();
    $display_actions = 'false';
    $content = ns_real_estate_help_page_content();
    $content_class = 'ns-modules';
    $content_nav = null;
    $alerts = null;
    $ajax = true;
    $icon = plugins_url('/ns-real-estate/images/icon-real-estate.svg');
    echo ns_basics_admin_page($page_name, $settings_group, $pages, $display_actions, $content, $content_class, $content_nav, $alerts, $ajax, $icon);
}

function ns_real_estate_help_page_content() {
    ob_start(); 
    //content goes here
    $output = ob_get_clean();
    return $output;
}

?>