<?php
// Exit if accessed directly
if (!defined( 'ABSPATH')) { exit; }

/**
 *	NS_Real_Estate_License_Keys class
 *
 */
class NS_Real_Estate_License_Keys {

	/**
	 * Retrieve License Key
	 */
	public function get_license($item_id) {
		$license = array();
	    $license['key_name'] = 'ns_'.$item_id.'_license_key';
	    $license['key'] = trim(get_option('ns_'.$item_id.'_license_key'));
	    $license['status_name'] = 'ns_'.$item_id.'_license_status';
	    $license['status'] = get_option('ns_'.$item_id.'_license_status');
	    return $license;
	}

	/**
	 * Build License Key Form
	 */
	public function build_license_key_form($item_name, $item_id) {

		$license = $this->get_license($item_id);
	    $license_key = $license['key'];
	    $license_status = $license['status'];
	    settings_fields('ns-real-estate-licenses-group'); 
	    ?>

	    <div class="ns-accordion ns-license-key">
	        <div class="ns-accordion-header">
	            <i class="fa fa-chevron-right"></i> 
	            <?php echo $item_name; ?> <?php esc_html_e('License', 'ns-real-estate'); ?>
	            <?php if($license_status !== false && $license_status == 'valid' ) { echo '<div class="button admin-button green">'.esc_html__('Active', 'ns-real-estate').'</div>';  }?>
	        </div>
	        <div class="ns-accordion-content">

	            <table class="admin-module">
	                <tr>
	                    <td class="admin-module-label">
	                        <label><?php echo esc_html_e('License Key', 'ns-real-estate'); ?></label>
	                        <span class="admin-module-note"><?php esc_html_e('Enter your license key here.', 'ns-real-estate'); ?></span>
	                    </td>
	                    <td class="admin-module-field">
	                        <?php if($license_status == false) { ?>
	                            <input class="license-key-input" name="<?php echo $license['key_name'] ?>" type="text" value="<?php esc_attr_e( $license_key ); ?>" />
	                        <?php } else { ?>
	                            <input value="<?php esc_attr_e( $license_key ); ?>" disabled />
	                            <input type="hidden" name="<?php echo $license['key_name'] ?>" value="<?php esc_attr_e( $license_key ); ?>" /> 
	                            <span class="admin-module-note"><?php echo esc_html_e('Deactivate to modify license key', 'ns-real-estate'); ?></span>
	                        <?php } ?>
	                    </td>
	                </tr>
	            </table>

	            <?php if( false !== $license_key && !empty($license_key) ) { ?>
	            <table class="admin-module">
	                <tr>
	                    <td class="admin-module-label"><label><?php echo esc_html_e('License Actions', 'ns-real-estate'); ?></label></td>
	                    <td class="admin-module-field">
	                        <?php if( $license_status !== false && $license_status == 'valid' ) { ?>
	                            <?php wp_nonce_field( 'ns_nonce', 'ns_nonce' ); ?>
	                            <button style="width:150px;" type="submit" class="button-secondary activate-license-button" name="ns_real_estate_deactivate_license" value="<?php echo $item_id; ?>"><?php echo esc_html_e('Deactivate License', 'ns-real-estate'); ?></button>
	                        <?php } else {
	                            wp_nonce_field( 'ns_nonce', 'ns_nonce' ); ?>
	                            <button style="width:150px;" type="submit" class="button-secondary activate-license-button" name="ns_real_estate_activate_license" value="<?php echo $item_id; ?>"><?php echo esc_html_e('Activate License', 'ns-real-estate'); ?></button>
	                        <?php } ?>
	                    </td>
	                </tr>
	            </table>
	            <?php } ?>

	        </div>
	    </div>

	<?php }

}
?>