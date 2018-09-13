<?php

/*-----------------------------------------------------------------------------------*/
/*  OUTPUT LICENSE KEYS PAGE
/*-----------------------------------------------------------------------------------*/
function rype_real_estate_license_keys_page() {
    $page_name = 'Rype Real Estate';
    $settings_group = 'rype-real-estate-license-keys-group';
    $pages = rype_real_estate_get_admin_pages();
    $display_actions = 'true';
    $content = rype_real_estate_license_keys_page_content();
    echo rype_basics_admin_page($page_name, $settings_group, $pages, $display_actions, $content);
} 

function rype_real_estate_license_keys_page_content() {
    ob_start(); ?>
    
    <h3><?php esc_html_e('Open Houses License Key', 'rype-real-estate'); ?></h3>
    <table class="admin-module no-border">
        <tr>
            <td class="admin-module-label">
                <label><?php esc_html_e('License Key', 'rype-real-estate'); ?></label>
                <span class="admin-module-note"><?php esc_html_e('Provide your license key. You can find your key in your account.', 'rype-real-estate'); ?></span>
            </td>
            <td class="admin-module-field"><input type="text" name="rype_real_estate_open_houses_license_key" /></td>
        </tr>
    </table>
    <table class="admin-module no-border">
        <tr>
            <td class="admin-module-label">
                <label><?php esc_html_e('Email', 'rype-real-estate'); ?></label>
                <span class="admin-module-note"><?php esc_html_e('Provide the email you used when you purchased the license key.', 'rype-real-estate'); ?></span>
            </td>
            <td class="admin-module-field"><input type="email" name="rype_real_estate_open_houses_license_email" /></td>
        </tr>
    </table>

    <?php $output = ob_get_clean();
    return $output;
}


?>