<?php
/*-----------------------------------------------------------------------------------*/
/* VALIDATE LICENSE KEY
/*-----------------------------------------------------------------------------------*/
function rype_basics_is_valid_license_key($license) {
    if(empty($license['key']) || empty($license['email'])) {
        return array('result' => false, 'error' => '');
    } else {
        $data_args = array('timeout' => 15, 'sslverify' => false);
        $data = wp_remote_get('http://rypecreative.com/rype-test/woocommerce/?wc-api=software-api&request=check&email='.$license['email'].'&license_key='.$license['key'].'&product_id='.$license['slug'], $data_args);
        if(!is_wp_error($data)) {
            $data = $data['body'];
            $obj = json_decode($data);
            if($obj->success == true) {
                return array('result' => true, 'error' => '');
            } else {
                return array('result' => false, 'error' => esc_html__('Your license key and/or license email is invalid.', 'rype-basics'));
            }
        } else {
            return array('result' => false, 'error' => $data->get_error_message());
        }
    }
}

/*-----------------------------------------------------------------------------------*/
/*  UPDATE LICENSE KEY STATUS (fires only when settings are saved)
/*-----------------------------------------------------------------------------------*/
add_action( 'update_option_rype_real_estate_open_houses_license', 'rype_real_estate_activate_license_key', 10, 3 );

function rype_real_estate_activate_license_key( $old_value, $new_value, $option ) {
    $valid_key = rype_basics_is_valid_license_key($new_value);
    if($valid_key['result'] == true) {
        $new_value['registered'] = true;
        $new_value['error'] = '';
    } else {
        $new_value['registered'] = false;
        $new_value['error'] = $valid_key['error'];
    }
    update_option($option, $new_value);
}

/*-----------------------------------------------------------------------------------*/
/* GET LICENSE KEY STATUS
/*-----------------------------------------------------------------------------------*/
function rype_basics_get_license_status($license, $product_link = null, $show_errors = null) {
    if(!rype_basics_is_paid_plugin_active($license['slug'])) { ?>
        <?php if(!empty($product_link)) { ?><a href="<?php echo $product_link; ?>" target="_blank" class="button button-purchase button-green"><?php esc_html_e('Purchase', 'rype-basics'); ?></a><?php } ?>
    <?php } else {
        if($license['registered'] == true) {
            echo '<div class="button button-activated button-green"><i class="fa fa-check"></i> '.esc_html__('Registered', 'rype-basics').'</div>';
        } else {
            echo '<div class="button button-activated button-red">'.esc_html__('Unregistered', 'rype-basics').'</div>';
            if($show_errors == 'true' && !empty($license['error'])) { echo '<span class="admin-module-note license-error">'.$license['error'].'</span>'; }
        }
    }
}

/*-----------------------------------------------------------------------------------*/
/* CHECK IF PAID ADD-ON PLUGIN IS ACTIVE
/*-----------------------------------------------------------------------------------*/
function rype_basics_is_paid_plugin_active($add_on_slug) {
    include_once( ABSPATH . 'wp-admin/includes/plugin.php' );
    if(is_plugin_active($add_on_slug.'/'.$add_on_slug.'.php')) { 
        return true; 
    } else { 
        return false;
    }
}

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

    <?php
        $open_houses_license_default = array('slug' => 'rype-open-houses', 'key' => null, 'email' => null, 'registered' => false, 'error' => null);
        $open_houses_license = get_option('rype_real_estate_open_houses_license', $open_houses_license_default);
        print_r($open_houses_license);
    ?>

    <div class="accordion rc-accordion">

        <h3 class="accordion-tab">
            <i class="fa fa-chevron-right icon"></i> 
            <?php esc_html_e('Open Houses License Key', 'rype-real-estate'); ?>
            <?php echo rype_basics_get_license_status($open_houses_license, '#', 'true') ?>
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