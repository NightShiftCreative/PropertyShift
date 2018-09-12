<?php

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