<?php
/*-----------------------------------------------------------------------------------*/
/*  Add top bar member links
/*-----------------------------------------------------------------------------------*/
function ns_real_estate_add_member_top_bar_links() { 
	$icon_set = esc_attr(get_option('ns_core_icon_set', 'fa'));
	$members_my_properties_page = get_option('ns_members_my_properties_page'); 
	$members_submit_property_page = get_option('ns_members_submit_property_page'); ?>
	<?php if(!empty($members_my_properties_page)) { ?><li><a href="<?php echo $members_my_properties_page; ?>"><?php echo ns_core_get_icon($header_vars['icon_set'], 'home'); ?><?php esc_html_e( 'My Properties', 'ns-real-estate' ); ?></a></li><?php } ?>
	<?php if(!empty($members_submit_property_page)) { ?><li><a href="<?php echo $members_submit_property_page; ?>"><?php echo ns_core_get_icon($header_vars['icon_set'], 'plus'); ?><?php esc_html_e( 'Submit Property', 'ns-real-estate' ); ?></a></li><?php } ?>
<?php }
add_filter( 'ns_core_after_top_bar_member_menu', 'ns_real_estate_add_member_top_bar_links');

/*-----------------------------------------------------------------------------------*/
/* Load default property submit fields */
/*-----------------------------------------------------------------------------------*/
function ns_real_estate_default_property_submit_fields() {
	$submit_property_fields_default = array(
		'Price Postfix',
		'Description',
		'Beds',
		'Baths',
		'Garages',
		'Area',
		'Area Postfix',
		'Video',
		'Property Location',
		'Property Type',
		'Property Status',
		'Amenities',
		'Floor Plans',
		'Featured Image',
		'Gallery Images',
		'Map',
		'Owner Info'
    );
    return $submit_property_fields_default;
}

/*-----------------------------------------------------------------------------------*/
/* Get attachment id by url */
/*-----------------------------------------------------------------------------------*/
function ns_real_estate_get_attachment_id_by_url( $url ) {
    // Split the $url into two parts with the wp-content directory as the separator
    $parsed_url = explode( parse_url( WP_CONTENT_URL, PHP_URL_PATH ), $url );
    // Get the host of the current site and the host of the $url, ignoring www
    $this_host = str_ireplace( 'www.', '', parse_url( home_url(), PHP_URL_HOST ) );
    $file_host = str_ireplace( 'www.', '', parse_url( $url, PHP_URL_HOST ) );
    // Return nothing if there aren't any $url parts or if the current host and $url host do not match
    if ( ! isset( $parsed_url[1] ) || empty( $parsed_url[1] ) || ( $this_host != $file_host ) ) {
    return;
    }

    global $wpdb;
    $attachment = $wpdb->get_col( $wpdb->prepare( "SELECT ID FROM {$wpdb->prefix}posts WHERE guid RLIKE %s;", $parsed_url[1] ) );
    // Returns null if no attachment is found
    return $attachment[0];
} 

/*-----------------------------------------------------------------------------------*/
/* Insert or update property post */
/*-----------------------------------------------------------------------------------*/
function ns_real_estate_insert_property_post($edit_property_id = null) {

	$members_submit_property_approval = esc_attr(get_option('ns_members_submit_property_approval', 'true'));
	if($members_submit_property_approval == 'true') {$members_submit_property_approval = 'pending';} else {$members_submit_property_approval = 'publish'; }

	$output = array();
	$errors = array();

	// require a title
	if(trim($_POST['title']) === '') {
	    $errors['title'] =  esc_html__('Please enter a title!', 'ns-real-estate'); 
	} else {
	    $title = trim($_POST['title']);
	}

	// require an address
	if(trim($_POST['street_address']) === '') {
	    $errors['address'] =  esc_html__('Please enter an address!', 'ns-real-estate'); 
	} else {
	    $street_address = trim($_POST['street_address']);
	}

	// require a price
	if(trim($_POST['price']) === '') {
	    $errors['price'] =  esc_html__('Please enter a price!', 'ns-real-estate'); 
	} else {
	    $price = trim($_POST['price']);
	}

	// Get property taxonomies
	if(isset($_POST['property_location'])) { $property_location = $_POST['property_location']; }
	if(isset($_POST['property_type'])) { $property_type = $_POST['property_type']; }
	if(isset($_POST['contract_type'])) { $property_status = $_POST['contract_type']; }
	if(isset($_POST['property_amenities'])) { $property_amenities = $_POST['property_amenities']; }

	// If there are no errors
	if(empty($errors)) {

		//Insert or update post
		if(!empty($edit_property_id)) {
			$post_information = array(
	            'ID'           => $edit_property_id,
	            'post_title' => wp_strip_all_tags( $title ),
	            'post_type' => 'ns-property'
	        );
	        wp_update_post( $post_information );
	        $post_ID = $edit_property_id;
		} else {
			$post_information = array(
		        'post_title' => wp_strip_all_tags( $title ),
		        'post_type' => 'ns-property',
		        'post_status' => $members_submit_property_approval
		    );
		    $post_ID = wp_insert_post( $post_information );
		}

	    //Set taxonomies
	    wp_set_object_terms($post_ID, $property_location, 'property_location', false);
	    wp_set_object_terms($post_ID, $property_type, 'property_type', false);
	    wp_set_object_terms($post_ID, $property_status, 'property_status', false);
	    wp_set_object_terms($post_ID, $property_amenities, 'property_amenities', false);

	    //upload property images
	    if(!empty($_FILES)) {
	        $additional_img_urls = array();
	        foreach( $_FILES as $file ) {

	            if($_FILES['featured_img']['tmp_name']) {
	                $attachment_id_featured_img = ns_basics_upload_user_file( $_FILES['featured_img'] );
	                set_post_thumbnail( $post_ID, $attachment_id_featured_img );
	            }
	            if( is_array($file) && $file['name'] != '' ) {
	                $attachment_id = ns_basics_upload_user_file( $file );
	                array_push($additional_img_urls, wp_get_attachment_url( $attachment_id ));
	            }
	        }
	    }  
	    if(!empty($edit_property_id)) { 
	    	$edit_values = get_post_custom( $edit_property_id );
			$edit_additional_images = isset($edit_values['ns_additional_img']) ? $edit_values['ns_additional_img'] : '';
	    	$additional_img_urls = array_merge($edit_additional_images, $additional_img_urls); 
	    }

	    //Set Post Meta
	    $allowed = '';
	    if( isset( $_POST['street_address'] ) )
	    	update_post_meta( $post_ID, 'ns_property_address', wp_kses( $_POST['street_address'], $allowed ) );

	    if( isset( $_POST['price'] ) )
	    	update_post_meta( $post_ID, 'ns_property_price', wp_kses( $_POST['price'], $allowed ) );

	    if( isset( $_POST['price_post'] ) )
	    	update_post_meta( $post_ID, 'ns_property_price_postfix', wp_kses( $_POST['price_post'], $allowed ) );

	    if( isset( $_POST['beds'] ) )
	    	update_post_meta( $post_ID, 'ns_property_bedrooms', wp_kses( $_POST['beds'], $allowed ) );

	    if( isset( $_POST['baths'] ) )
	    	update_post_meta( $post_ID, 'ns_property_bathrooms', wp_kses( $_POST['baths'], $allowed ) );

	    if( isset( $_POST['garages'] ) )
	    	update_post_meta( $post_ID, 'ns_property_garages', wp_kses( $_POST['garages'], $allowed ) );

	    if( isset( $_POST['area'] ) )
	    	update_post_meta( $post_ID, 'ns_property_area', wp_kses( $_POST['area'], $allowed ) );

	    if( isset( $_POST['area_post'] ) )
	    	update_post_meta( $post_ID, 'ns_property_area_postfix', wp_kses( $_POST['area_post'], $allowed ) );

	    if( isset( $_POST['video_url'] ) )
	    	update_post_meta( $post_ID, 'ns_property_video_url', wp_kses( $_POST['video_url'], $allowed ) );

	    if( isset( $_POST['video_img'] ) )
	    	update_post_meta( $post_ID, 'ns_property_video_img', wp_kses( $_POST['video_img'], $allowed ) );

	    if (isset( $_POST['ns_property_floor_plans'] )) {
	        update_post_meta( $post_ID, 'ns_property_floor_plans', $_POST['ns_property_floor_plans'] );
	    }

	    if (isset( $_POST['ns_property_custom_fields'] )) {
	        $property_custom_fields = $_POST['ns_property_custom_fields'];
	        foreach($property_custom_fields as $custom_field) {
	            update_post_meta( $post_ID, $custom_field['key'], $custom_field['value'] );
	        }
	    }

	    if( isset( $_POST['description'] ) )
        	update_post_meta( $post_ID, 'ns_property_description', wp_kses_post($_POST['description']) );

	    if (!empty( $additional_img_urls )) { 
	        $strAdditionalImgs = implode(",", $additional_img_urls);
	        update_post_meta( $post_ID, 'ns_additional_img', $strAdditionalImgs );
	    } else {
	        $strAdditionalImgs = '';
	        update_post_meta( $post_ID, 'ns_additional_img', $strAdditionalImgs );
	    }

	    if( isset( $_POST['latitude'] ) )
        	update_post_meta( $post_ID, 'ns_property_latitude', wp_kses( $_POST['latitude'], $allowed ) );

	    if( isset( $_POST['longitude'] ) )
	        update_post_meta( $post_ID, 'ns_property_longitude', wp_kses( $_POST['longitude'], $allowed ) );

	    if( isset( $_POST['agent_display'] ) )
	        update_post_meta( $post_ID, 'ns_agent_display', wp_kses( $_POST['agent_display'], $allowed ) );

	    if( isset( $_POST['agent_select'] ) )
	        update_post_meta( $post_ID, 'ns_agent_select', wp_kses( $_POST['agent_select'], $allowed ) );

	    if( isset( $_POST['agent_custom_name'] ) )
	        update_post_meta( $post_ID, 'ns_agent_custom_name', wp_kses( $_POST['agent_custom_name'], $allowed ) );

	    if( isset( $_POST['agent_custom_email'] ) )
	        update_post_meta( $post_ID, 'ns_agent_custom_email', wp_kses( $_POST['agent_custom_email'], $allowed ) );

	    if( isset( $_POST['agent_custom_phone'] ) )
	        update_post_meta( $post_ID, 'ns_agent_custom_phone', wp_kses( $_POST['agent_custom_phone'], $allowed ) );

	    if( isset( $_POST['agent_custom_url'] ) )
	        update_post_meta( $post_ID, 'ns_agent_custom_url', wp_kses( $_POST['agent_custom_url'], $allowed ) );

	    //hook in for other add-ons
    	do_action('ns_real_estate_save_property_submit', $post_ID);

		if($members_submit_property_approval == 'true') {
	        $output['success'] = esc_html__('Your property,', 'ns-real-estate') .' <b>'. $title .',</b> '. esc_html__('was submitted for review!', 'ns-real-estate');
	    } else {
	        $output['success'] = esc_html__('Your property,', 'ns-real-estate') .' <b>'. $title .',</b> '. esc_html__('was published!', 'ns-real-estate');
	    }
	} else {
	    $output['success'] = '';
	}

	$output['errors'] = $errors;
	return $output;
}