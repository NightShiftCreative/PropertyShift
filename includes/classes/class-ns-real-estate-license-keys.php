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

}
?>