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

/*-----------------------------------------------------------------------------------*/
/*  Define Constants
/*-----------------------------------------------------------------------------------*/
define( 'EDD_SAMPLE_STORE_URL', 'https://studio.nightshiftcreative.co' );
define( 'EDD_SAMPLE_ITEM_ID', 505 );
define( 'EDD_SAMPLE_PLUGIN_LICENSE_PAGE', 'ns-real-estate-license-keys' );

/*-----------------------------------------------------------------------------------*/
/*  Activate License Key
/*-----------------------------------------------------------------------------------*/
function edd_sample_activate_license() {
    if( isset( $_POST['edd_license_activate'] ) ) {

        if( ! check_admin_referer( 'edd_sample_nonce', 'edd_sample_nonce' ) )
            return;

        $license = trim( get_option( 'edd_sample_license_key' ) );
        $api_params = array(
            'edd_action' => 'activate_license',
            'license'    => $license,
            'item_id'  => EDD_SAMPLE_ITEM_ID, // the name of our product in EDD
            'url'        => home_url()
        );

        $response = wp_remote_post( EDD_SAMPLE_STORE_URL, array( 'timeout' => 15, 'sslverify' => false, 'body' => $api_params ) );

        if ( is_wp_error( $response ) || 200 !== wp_remote_retrieve_response_code( $response ) ) {
            if ( is_wp_error( $response ) ) {
                $message = $response->get_error_message();
            } else {
                $message = __( 'An error occurred, please try again.' );
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
                        $message = __( 'Your license key has been disabled.' );
                        break;
                    case 'missing' :
                        $message = __( 'Invalid license.' );
                        break;
                    case 'invalid' :
                    case 'site_inactive' :
                        $message = __( 'Your license is not active for this URL.' );
                        break;
                    case 'item_name_mismatch' :
                        $message = sprintf( __( 'This appears to be an invalid license key for %s.' ), EDD_SAMPLE_ITEM_NAME );
                        break;
                    case 'no_activations_left':
                        $message = __( 'Your license key has reached its activation limit.' );
                        break;
                    default :
                        $message = __( 'An error occurred, please try again.' );
                        break;
                }
            }
        }

        if(!empty( $message)) {
            $base_url = admin_url( 'admin.php?page=' . EDD_SAMPLE_PLUGIN_LICENSE_PAGE );
            $redirect = add_query_arg( array( 'sl_activation' => 'false', 'message' => urlencode( $message ) ), $base_url );
            wp_redirect( $redirect );
            exit();
        }

        update_option( 'edd_sample_license_status', $license_data->license );
        wp_redirect( admin_url( 'admin.php?page=' . EDD_SAMPLE_PLUGIN_LICENSE_PAGE ) );
        exit();
    }
}
add_action('admin_init', 'edd_sample_activate_license');


/*-----------------------------------------------------------------------------------*/
/*  De-activate License Key
/*-----------------------------------------------------------------------------------*/
function edd_sample_deactivate_license() {
    if( isset( $_POST['edd_license_deactivate'] ) ) {

        if( ! check_admin_referer( 'edd_sample_nonce', 'edd_sample_nonce' ) )
            return; // get out if we didn't click the Activate button

        $license = trim( get_option( 'edd_sample_license_key' ) );
        $api_params = array(
            'edd_action' => 'deactivate_license',
            'license'    => $license,
            'item_id'  => EDD_SAMPLE_ITEM_ID, // the name of our product in EDD
            'url'        => home_url()
        );

        $response = wp_remote_post( EDD_SAMPLE_STORE_URL, array( 'timeout' => 15, 'sslverify' => false, 'body' => $api_params ) );

        // make sure the response came back okay
        if ( is_wp_error( $response ) || 200 !== wp_remote_retrieve_response_code( $response ) ) {

            if ( is_wp_error( $response ) ) {
                $message = $response->get_error_message();
            } else {
                $message = __( 'An error occurred, please try again.' );
            }

            $base_url = admin_url( 'admin.php?page=' . EDD_SAMPLE_PLUGIN_LICENSE_PAGE );
            $redirect = add_query_arg( array( 'sl_activation' => 'false', 'message' => urlencode( $message ) ), $base_url );

            wp_redirect( $redirect );
            exit();
        }

        $license_data = json_decode( wp_remote_retrieve_body( $response ) );
        if( $license_data->license == 'deactivated' ) {
            delete_option( 'edd_sample_license_status' );
        }

        wp_redirect( admin_url( 'admin.php?page=' . EDD_SAMPLE_PLUGIN_LICENSE_PAGE ) );
        exit();
    }
}
add_action('admin_init', 'edd_sample_deactivate_license');

/*-----------------------------------------------------------------------------------*/
/*  Catch activation errors and display
/*-----------------------------------------------------------------------------------*/
function edd_sample_admin_notices() {
    if ( isset( $_GET['sl_activation'] ) && ! empty( $_GET['message'] ) ) {

        switch( $_GET['sl_activation'] ) {
            case 'false':
                $message = urldecode( $_GET['message'] );
                ?>
                <div class="error">
                    <p><?php echo $message; ?></p>
                </div>
                <?php
                break;
            case 'true':
            default:
                // Developers can put a custom success message here for when activation is successful if they way.
                break;

        }
    }
}
add_action( 'admin_notices', 'edd_sample_admin_notices' );



?>