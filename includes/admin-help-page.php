<?php

/*-----------------------------------------------------------------------------------*/
/*  OUTPUT HELP PAGE
/*-----------------------------------------------------------------------------------*/
function rype_real_estate_help_page() { 
    $page_name = 'Rype Real Estate';
    $settings_group = null;
    $pages = rype_real_estate_get_admin_pages();
    $display_actions = 'false';
    $content = rype_real_estate_help_page_content();
    echo rype_basics_admin_page($page_name, $settings_group, $pages, $display_actions, $content);
}

function rype_real_estate_help_page_content() {
    ob_start(); 
    //content goes here
    $output = ob_get_clean();
    return $output;
}

?>