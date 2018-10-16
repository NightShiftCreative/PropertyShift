<?php
/*-----------------------------------------------------------------------------------*/
/*  UPDATE LICENSE KEY STATUS
/*  - fires only when settings are saved
/*  - processed in Rype Basics plugin (rype-basics/includes/license-keys.php)
/*-----------------------------------------------------------------------------------*/
add_action( 'update_option_rype_real_estate_open_houses_license', 'rype_real_estate_activate_license_key', 10, 3 );

/*-----------------------------------------------------------------------------------*/
/*  OUTPUT LICENSE KEYS PAGE
/*-----------------------------------------------------------------------------------*/
function rype_real_estate_license_keys_page() {
    $page_name = 'Rype Real Estate';
    $settings_group = 'rype-real-estate-license-keys-group';
    $pages = rype_real_estate_get_admin_pages();
    $display_actions = 'true';
    $content = rype_real_estate_license_keys_page_content();
    $content_class = null;
    $content_nav = null;
    $alerts = null;
    $ajax = false;
    echo ns_basics_admin_page($page_name, $settings_group, $pages, $display_actions, $content, $content_classl, $content_nav, $alerts, $ajax);
} 

function rype_real_estate_license_keys_page_content() {
    ob_start(); ?>

    <?php
        $open_houses_license_default = array('slug' => 'rype-open-houses', 'key' => null, 'email' => null, 'registered' => false, 'error' => null);
        $open_houses_license = get_option('rype_real_estate_open_houses_license', $open_houses_license_default);
    ?>

    <div class="accordion rc-accordion">

        <h3 class="accordion-tab rype-license-tab">
            <i class="fa fa-chevron-right icon"></i> 
            <?php esc_html_e('Open Houses License Key', 'rype-real-estate'); ?>
            <?php echo rype_basics_get_license_status($open_houses_license, '#', null, 'true'); ?>
        </h3>
        <div>
            <table class="admin-module">
                <tr>
                    <td class="admin-module-label">
                        <label><?php esc_html_e('License Key', 'rype-real-estate'); ?></label>
                        <span class="admin-module-note"><?php esc_html_e('Provide your license key. You can find your key in your account.', 'rype-real-estate'); ?></span>
                    </td>
                    <td class="admin-module-field">
                        <input type="text" name="rype_real_estate_open_houses_license[key]" value="<?php echo $open_houses_license['key']; ?>" />
                        <input type="hidden" name="rype_real_estate_open_houses_license[slug]" value="<?php echo $open_houses_license['slug']; ?>" />
                        <input type="hidden" name="rype_real_estate_open_houses_license[registered]" value="<?php echo $open_houses_license['registered']; ?>" />
                    </td>
                </tr>
            </table>
            <table class="admin-module no-border">
                <tr>
                    <td class="admin-module-label">
                        <label><?php esc_html_e('Email', 'rype-real-estate'); ?></label>
                        <span class="admin-module-note"><?php esc_html_e('Provide the email you used when purchasing this license key.', 'rype-real-estate'); ?></span>
                    </td>
                    <td class="admin-module-field"><input type="email" name="rype_real_estate_open_houses_license[email]" value="<?php echo $open_houses_license['email']; ?>" /></td>
                </tr>
            </table>
        </div>

    </div>

    <?php $output = ob_get_clean();
    return $output;
}


?>