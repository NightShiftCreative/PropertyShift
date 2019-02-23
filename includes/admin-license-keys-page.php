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
        <?php esc_html_e('All premium add-ons require a valid license key for updates and support.', 'ns-real-estate'); ?><br/>
        <?php esc_html_e('Your licenses keys can be found in your account on the Nightshift Studio website.', 'ns-real-estate'); ?>
    </div><br/>
    
    <?php do_action( 'ns_real_estate_register_license_keys'); ?>

    <?php $output = ob_get_clean();
    return $output;
}

/*-----------------------------------------------------------------------------------*/
/*  ACTIVATE LICENSE
/*-----------------------------------------------------------------------------------*/
define('NS_LICENSE_PAGE', 'ns-real-estate-license-keys' );

function ns_real_estate_activate_license($license, $status_name, $item_id) {

    $api_params = array(
        'edd_action' => 'activate_license',
        'license'    => $license,
        'item_id'  => $item_id, // the name of our product in EDD
        'url'      => home_url()
    );

    $response = wp_remote_post( NS_SHOP_URL, array( 'timeout' => 15, 'sslverify' => false, 'body' => $api_params ) );

    if ( is_wp_error( $response ) || 200 !== wp_remote_retrieve_response_code( $response ) ) {
        if ( is_wp_error( $response ) ) {
            $message = $response->get_error_message();
        } else {
            $message = __( 'An error occurred, please try again.', 'ns-real-estate' );
        }
    } else {

        $license_data = json_decode( wp_remote_retrieve_body( $response ) );

        if(false === $license_data->success ) {
            switch( $license_data->error ) {
                case 'expired' :
                    $message = sprintf(
                         __( 'Your license key expired on %s.' ),
                        date_i18n( get_option( 'date_format' ), strtotime( $license_data->expires, current_time( 'timestamp' ) ) )
                    );
                    break;
                case 'disabled' :
                case 'revoked' :
                    $message = __( 'Your license key has been disabled.', 'ns-real-estate' );
                    break;
                case 'missing' :
                    $message = __( 'Invalid license.', 'ns-real-estate' );
                    break;
                case 'invalid' :
                case 'site_inactive' :
                    $message = __( 'Your license is not active for this URL.', 'ns-real-estate' );
                    break;
                case 'item_name_mismatch' :
                    $message = sprintf( __( 'This appears to be an invalid license key for %s.' ), 'NS Open Houses' );
                    break;
                case 'no_activations_left':
                    $message = __( 'Your license key has reached its activation limit.', 'ns-real-estate' );
                    break;
                default :
                    $message = __( 'An error occurred, please try again.', 'ns-real-estate' );
                    break;
            }
        }
    }

    if(!empty($message)) {
        $base_url = admin_url( 'admin.php?page=' . NS_LICENSE_PAGE );
        $redirect = add_query_arg( array( 'sl_activation' => 'false', 'message' => urlencode( $message ) ), $base_url );
        wp_redirect( $redirect );
        exit();
    }

    update_option($status_name, $license_data->license);
    wp_redirect( admin_url( 'admin.php?page=' . NS_LICENSE_PAGE ) );
    exit();
}

/*-----------------------------------------------------------------------------------*/
/*  DEACTIVATE LICENSE
/*-----------------------------------------------------------------------------------*/
function ns_real_estate_deactivate_license($license, $status_name, $item_id) {
    $api_params = array(
        'edd_action' => 'deactivate_license',
        'license'    => $license,
        'item_id'  => $item_id, // the name of our product in EDD
        'url'      => home_url()
    );

    $response = wp_remote_post( NS_SHOP_URL, array( 'timeout' => 15, 'sslverify' => false, 'body' => $api_params ) );

    if ( is_wp_error( $response ) || 200 !== wp_remote_retrieve_response_code( $response ) ) {

        if ( is_wp_error( $response ) ) {
            $message = $response->get_error_message();
        } else {
            $message = __( 'An error occurred, please try again.' );
        }

        $base_url = admin_url( 'admin.php?page=' . NS_LICENSE_PAGE );
        $redirect = add_query_arg( array( 'sl_activation' => 'false', 'message' => urlencode( $message ) ), $base_url );
        wp_redirect( $redirect );
        exit();
    }

    delete_option($status_name);
    wp_redirect( admin_url( 'admin.php?page=' . NS_LICENSE_PAGE ) );
    exit();
}

/*-----------------------------------------------------------------------------------*/
/*  Catch activation errors and display
/*-----------------------------------------------------------------------------------*/
function ns_real_estate_license_admin_notices() {
    if ( isset( $_GET['sl_activation'] ) && ! empty( $_GET['message'] ) ) {

        switch( $_GET['sl_activation'] ) {
            case 'false':
                $message = urldecode( $_GET['message'] ); ?>
                <div class="error"><p><?php echo $message; ?></p></div>
                <?php
                break;
            case 'true':
            default:
                // Custom message here on successful activation
                break;
        }
    }
}
add_action( 'admin_notices', 'ns_real_estate_license_admin_notices' );

?>