<?php
/*-----------------------------------------------------------------------------------*/
/*  Add top bar member links
/*-----------------------------------------------------------------------------------*/
function rype_real_estate_add_member_top_bar_links() { 
	$icon_set = esc_attr(get_option('rypecore_icon_set', 'fa'));
	$members_my_properties_page = get_option('rypecore_members_my_properties_page'); 
	$members_submit_property_page = get_option('rypecore_members_submit_property_page'); ?>
	<?php if(!empty($members_my_properties_page)) { ?><li><a href="<?php echo $members_my_properties_page; ?>"><?php echo rypecore_get_icon($header_vars['icon_set'], 'home'); ?><?php esc_html_e( 'My Properties', 'rype-real-estate' ); ?></a></li><?php } ?>
	<?php if(!empty($members_submit_property_page)) { ?><li><a href="<?php echo $members_submit_property_page; ?>"><?php echo rypecore_get_icon($header_vars['icon_set'], 'plus'); ?><?php esc_html_e( 'Submit Property', 'rype-real-estate' ); ?></a></li><?php } ?>
<?php }
add_filter( 'rao_after_top_bar_member_menu', 'rype_real_estate_add_member_top_bar_links');

/*-----------------------------------------------------------------------------------*/
/* Build property submit form
/*-----------------------------------------------------------------------------------*/

/* Get attachment id by url */
function rype_real_estate_get_attachment_id_by_url( $url ) {
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

/* Insert or update property post */
function rype_real_estate_insert_property_post($edit_property_id = null) {

	$members_submit_property_approval = esc_attr(get_option('rypecore_members_submit_property_approval', 'true'));
	if($members_submit_property_approval == 'true') {$members_submit_property_approval = 'pending';} else {$members_submit_property_approval = 'publish'; }

	$output = array();
	$errors = array();

	// require a title
	if(trim($_POST['title']) === '') {
	    $errors['title'] =  esc_html__('Please enter a title!', 'rype-real-estate'); 
	} else {
	    $title = trim($_POST['title']);
	}

	// require an address
	if(trim($_POST['street_address']) === '') {
	    $errors['address'] =  esc_html__('Please enter an address!', 'rype-real-estate'); 
	} else {
	    $street_address = trim($_POST['street_address']);
	}

	// require a price
	if(trim($_POST['price']) === '') {
	    $errors['price'] =  esc_html__('Please enter a price!', 'rype-real-estate'); 
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
	            'post_content' => $_POST['description'],
	            'post_type' => 'rype-property'
	        );
	        wp_update_post( $post_information );
	        $post_ID = $edit_property_id;
		} else {
			$post_information = array(
		        'post_title' => wp_strip_all_tags( $title ),
		        'post_content' => $_POST['description'],
		        'post_type' => 'rype-property',
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
	                $attachment_id_featured_img = rypecore_upload_user_file( $_FILES['featured_img'] );
	                set_post_thumbnail( $post_ID, $attachment_id_featured_img );
	            }
	            if( is_array($file) && $file['name'] != '' ) {
	                $attachment_id = rypecore_upload_user_file( $file );
	                array_push($additional_img_urls, wp_get_attachment_url( $attachment_id ));
	            }
	        }
	    }  
	    if(!empty($edit_property_id)) { 
	    	$edit_values = get_post_custom( $edit_property_id );
			$edit_additional_images = isset($edit_values['rypecore_additional_img']) ? $edit_values['rypecore_additional_img'] : '';
	    	$additional_img_urls = array_merge($edit_additional_images, $additional_img_urls); 
	    }

	    //Set Post Meta
	    $allowed = '';
	    if( isset( $_POST['street_address'] ) )
	    	update_post_meta( $post_ID, 'rypecore_property_address', wp_kses( $_POST['street_address'], $allowed ) );

	    if( isset( $_POST['price'] ) )
	    	update_post_meta( $post_ID, 'rypecore_property_price', wp_kses( $_POST['price'], $allowed ) );

	    if( isset( $_POST['price_post'] ) )
	    	update_post_meta( $post_ID, 'rypecore_property_price_postfix', wp_kses( $_POST['price_post'], $allowed ) );

	    if( isset( $_POST['beds'] ) )
	    	update_post_meta( $post_ID, 'rypecore_property_bedrooms', wp_kses( $_POST['beds'], $allowed ) );

	    if( isset( $_POST['baths'] ) )
	    	update_post_meta( $post_ID, 'rypecore_property_bathrooms', wp_kses( $_POST['baths'], $allowed ) );

	    if( isset( $_POST['garages'] ) )
	    	update_post_meta( $post_ID, 'rypecore_property_garages', wp_kses( $_POST['garages'], $allowed ) );

	    if( isset( $_POST['area'] ) )
	    	update_post_meta( $post_ID, 'rypecore_property_area', wp_kses( $_POST['area'], $allowed ) );

	    if( isset( $_POST['area_post'] ) )
	    	update_post_meta( $post_ID, 'rypecore_property_area_postfix', wp_kses( $_POST['area_post'], $allowed ) );

	    if( isset( $_POST['video_url'] ) )
	    	update_post_meta( $post_ID, 'rypecore_property_video_url', wp_kses( $_POST['video_url'], $allowed ) );

	    if( isset( $_POST['video_img'] ) )
	    	update_post_meta( $post_ID, 'rypecore_property_video_img', wp_kses( $_POST['video_img'], $allowed ) );

	    if (isset( $_POST['rypecore_floor_plans'] )) {
	        update_post_meta( $post_ID, 'rypecore_floor_plans', $_POST['rypecore_floor_plans'] );
	    }

	    if (isset( $_POST['rypecore_property_custom_fields'] )) {
	        $property_custom_fields = $_POST['rypecore_property_custom_fields'];
	        foreach($property_custom_fields as $custom_field) {
	            update_post_meta( $post_ID, $custom_field['key'], $custom_field['value'] );
	        }
	    }

	    if (!empty( $additional_img_urls )) { 
	        $strAdditionalImgs = implode(",", $additional_img_urls);
	        update_post_meta( $post_ID, 'rypecore_additional_img', $strAdditionalImgs );
	    } else {
	        $strAdditionalImgs = '';
	        update_post_meta( $post_ID, 'rypecore_additional_img', $strAdditionalImgs );
	    }

	    if( isset( $_POST['latitude'] ) )
        	update_post_meta( $post_ID, 'rypecore_property_latitude', wp_kses( $_POST['latitude'], $allowed ) );

	    if( isset( $_POST['longitude'] ) )
	        update_post_meta( $post_ID, 'rypecore_property_longitude', wp_kses( $_POST['longitude'], $allowed ) );

	    if( isset( $_POST['agent_display'] ) )
	        update_post_meta( $post_ID, 'rypecore_agent_display', wp_kses( $_POST['agent_display'], $allowed ) );

	    if( isset( $_POST['agent_select'] ) )
	        update_post_meta( $post_ID, 'rypecore_agent_select', wp_kses( $_POST['agent_select'], $allowed ) );

	    if( isset( $_POST['agent_custom_name'] ) )
	        update_post_meta( $post_ID, 'rypecore_agent_custom_name', wp_kses( $_POST['agent_custom_name'], $allowed ) );

	    if( isset( $_POST['agent_custom_email'] ) )
	        update_post_meta( $post_ID, 'rypecore_agent_custom_email', wp_kses( $_POST['agent_custom_email'], $allowed ) );

	    if( isset( $_POST['agent_custom_phone'] ) )
	        update_post_meta( $post_ID, 'rypecore_agent_custom_phone', wp_kses( $_POST['agent_custom_phone'], $allowed ) );

	    if( isset( $_POST['agent_custom_url'] ) )
	        update_post_meta( $post_ID, 'rypecore_agent_custom_url', wp_kses( $_POST['agent_custom_url'], $allowed ) );

		if($members_submit_property_approval == 'true') {
	        $output['success'] = esc_html__('Your property,', 'rype-real-estate') .' <b>'. $title .',</b> '. esc_html__('was submitted for review!', 'rype-real-estate');
	    } else {
	        $output['success'] = esc_html__('Your property,', 'rype-real-estate') .' <b>'. $title .',</b> '. esc_html__('was published!', 'rype-real-estate');
	    }
	} else {
	    $output['success'] = '';
	}

	$output['errors'] = $errors;
	return $output;
}

/*-----------------------------------------------------------------------------------*/
/* Output and process property submit form
/*-----------------------------------------------------------------------------------*/
function rype_real_estate_property_submit_form() { 

	//global settings
	$icon_set = esc_attr(get_option('rypecore_icon_set', 'fa'));
	$members_my_properties_page = get_option('rypecore_members_my_properties_page');
	$members_add_locations = esc_attr(get_option('rypecore_members_add_locations', 'true'));
	$members_add_amenities = esc_attr(get_option('rypecore_members_add_amenities', 'true'));

	//intialize variables
	$errors = '';
	$success = '';

	//If editting property, get data and determine form action
	if (isset($_GET['edit_property']) && !empty($_GET['edit_property'])) {
	    $form_submit_text = esc_html__('Update Property', 'rype-real-estate');
	    $edit_property_id = $_GET['edit_property'];
	    $form_action = '?edit_property='.esc_attr($edit_property_id);
	    $values = get_post_custom( $edit_property_id );
	    $edit_address = isset( $values['rypecore_property_address'] ) ? esc_attr( $values['rypecore_property_address'][0] ) : '';
    	$edit_price = isset( $values['rypecore_property_price'] ) ? esc_attr( $values['rypecore_property_price'][0] ) : '';
    	$edit_price_postfix = isset( $values['rypecore_property_price_postfix'] ) ? esc_attr( $values['rypecore_property_price_postfix'][0] ) : '';
    	$edit_bedrooms = isset( $values['rypecore_property_bedrooms'] ) ? esc_attr( $values['rypecore_property_bedrooms'][0] ) : '';
    	$edit_bathrooms = isset( $values['rypecore_property_bathrooms'] ) ? esc_attr( $values['rypecore_property_bathrooms'][0] ) : '';
    	$edit_garages = isset( $values['rypecore_property_garages'] ) ? esc_attr( $values['rypecore_property_garages'][0] ) : '';
    	$edit_area = isset( $values['rypecore_property_area'] ) ? esc_attr( $values['rypecore_property_area'][0] ) : '';
    	$area_postfix_default = esc_attr(get_option('rypecore_default_area_postfix', 'Sq Ft'));
    	$edit_area_postfix = isset( $values['rypecore_property_area_postfix'] ) ? esc_attr( $values['rypecore_property_area_postfix'][0] ) : $area_postfix_default;
    	$edit_floor_plans = isset($values['rypecore_floor_plans']) ? $values['rypecore_floor_plans'] : '';
    	$edit_additional_images = isset($values['rypecore_additional_img']) ? $values['rypecore_additional_img'] : '';
    	$edit_video_url = isset( $values['rypecore_property_video_url'] ) ? esc_attr( $values['rypecore_property_video_url'][0] ) : '';
    	$edit_video_img = isset( $values['rypecore_property_video_img'] ) ? esc_attr( $values['rypecore_property_video_img'][0] ) : '';
    	$latitude = isset( $values['rypecore_property_latitude'] ) ? esc_attr( $values['rypecore_property_latitude'][0] ) : '';
    	$longitude = isset( $values['rypecore_property_longitude'] ) ? esc_attr( $values['rypecore_property_longitude'][0] ) : '';
    	$edit_agent_display = isset( $values['rypecore_agent_display'] ) ? esc_attr( $values['rypecore_agent_display'][0] ) : 'none';
    	$edit_agent_select = isset( $values['rypecore_agent_select'] ) ? esc_attr( $values['rypecore_agent_select'][0] ) : '';
    	$edit_agent_custom_name = isset( $values['rypecore_agent_custom_name'] ) ? esc_attr( $values['rypecore_agent_custom_name'][0] ) : '';
    	$edit_agent_custom_email = isset( $values['rypecore_agent_custom_email'] ) ? esc_attr( $values['rypecore_agent_custom_email'][0] ) : '';
    	$edit_agent_custom_phone = isset( $values['rypecore_agent_custom_phone'] ) ? esc_attr( $values['rypecore_agent_custom_phone'][0] ) : '';
    	$edit_agent_custom_url = isset( $values['rypecore_agent_custom_url'] ) ? esc_attr( $values['rypecore_agent_custom_url'][0] ) : '';
    	$edit_property_location = rype_real_estate_get_property_location($edit_property_id , null, 'true');
    	$edit_property_amenities = rype_real_estate_get_property_amenities($edit_property_id , true, 'true');
    	$edit_property_status = rype_real_estate_get_property_status($edit_property_id, 'true');
    	$edit_property_type = rype_real_estate_get_property_type($edit_property_id, 'true');
    	$edit_content_post = get_post($edit_property_id);
    	$edit_content = $edit_content_post->post_content;
    	$edit_content = str_replace(']]>', ']]&gt;', $edit_content);

    	//delete additional image
		if (!empty($_GET['additional_img_attachment_id'])) {
		    $image_to_delete = $_GET['additional_img_attachment_id'];
		    $image_to_delete_url = wp_get_attachment_url( $image_to_delete );
		    $new_edit_additional_images = explode(",", $edit_additional_images[0]);
		                    
		    $key = array_search($image_to_delete_url, $new_edit_additional_images);
		    if($key!==false) { unset($new_edit_additional_images[$key]); }

		    //update values
		    $strAdditionalImgs = implode(",", $new_edit_additional_images);
		    update_post_meta( $edit_property_id, 'rypecore_additional_img', $strAdditionalImgs );
		}
    	
	} else {
		$edit_property_id = '';
		$form_action = '';
		$form_submit_text = esc_html__('Submit Property', 'rype-real-estate');
	}

	//If form was submitted insert/update post
	if (!empty($_POST)) {

		if(isset($_GET['edit_property']) && !empty($_GET['edit_property'])) {
    		$inserted_post = rype_real_estate_insert_property_post($_GET['edit_property']);
    	} else {
    		$inserted_post = rype_real_estate_insert_property_post();
    	}
		$errors = $inserted_post['errors'];
		$success = $inserted_post['success'];
	} ?>

	<!-- start submit property form -->
	<div class="user-submit-property-form">

		<?php if ($success != '') { ?>
	        <div class="alert-box success"><h4><?php echo wp_kses_post($success); ?></h4><?php if (!empty($members_my_properties_page)) { echo '<a href="'.esc_url($members_my_properties_page).'">'. esc_html__('View your properties.', 'rype-real-estate').'</a>'; } ?></div>
	    <?php } ?>

	    <form class="multi-page-form" method="post" action="<?php echo get_the_permalink().$form_action; ?>" enctype="multipart/form-data">
	    	
	    	<div class="multi-page-form-progress">
				<a href="#general-info" class="multi-page-form-progress-item active"><span class="progress-item-num">1</span> <span class="progress-item-text"><?php esc_html_e('General Info', 'rype-real-estate'); ?></span></a>
				<a href="#property-images" class="multi-page-form-progress-item"><span class="progress-item-num">2</span> <span class="progress-item-text"><?php esc_html_e('Property Images', 'rype-real-estate'); ?></span></a>
	            <a href="#map" onclick="refreshMap()" class="multi-page-form-progress-item"><span class="progress-item-num">4</span> <span class="progress-item-text"><?php esc_html_e('Map', 'rype-real-estate'); ?></span></a>
				<a href="#owner-info" class="multi-page-form-progress-item"><span class="progress-item-num">5</span> <span class="progress-item-text"><?php esc_html_e('Owner Info', 'rype-real-estate'); ?></span></a>
			</div>

			<div class="multi-page-form-content active" id="general-info">
		    	<h3><?php esc_html_e('General Info', 'rype-real-estate'); ?></h3>
		    	
		    	<div class="row">

				<div class="col-lg-6 col-md-6">
			    	<div class="form-block form-block-property-title">
		                <label><?php esc_html_e('Title*', 'rype-real-estate'); ?></label>
						<?php if(isset($errors['title'])) { ?>
							<div class="alert-box error"><h4><?php echo esc_attr($errors['title']); ?></h4></div>
						<?php } ?>
						<input class="required border" type="text" name="title" value="<?php if(!empty($edit_property_id)) { echo get_the_title( $edit_property_id ); } else { echo esc_attr($_POST['title']); } ?>" />
					</div>

					<div class="row form-block-property-price">
						<?php if(isset($errors['price'])) { ?>
						       <div class="col-lg-12"><div class="alert-box error"><h4><?php echo esc_attr($errors['price']); ?></h4></div></div>
						   <?php } ?>
						<div class="col-lg-6 col-md-6 form-block">
	                           <label><?php esc_html_e('Price*', 'rype-real-estate'); ?></label>
							<input class="required border" type="number" name="price" value="<?php if(isset($edit_price)) { echo $edit_price; } else { echo esc_attr($_POST['price']); } ?>" />
						</div>

						<div class="col-lg-6 col-md-6 form-block">
	                           <label><?php esc_html_e('Price Postfix', 'rype-real-estate'); ?></label>
							<input type="text" class="border" name="price_post" value="<?php if(isset($edit_price_postfix)) { echo $edit_price_postfix; } else { echo esc_attr($_POST['price_post']); } ?>" />
						</div>
					</div>

					<div class="form-block form-block-property-beds">
	                    <label><?php esc_html_e('Bedrooms', 'rype-real-estate'); ?></label>
						<input type="number" class="border" name="beds" value="<?php if(isset($edit_bedrooms)) { echo $edit_bedrooms; } else { echo esc_attr($_POST['beds']); } ?>" />
					</div>

					<div class="form-block form-block-property-baths">
	                    <label><?php esc_html_e('Bathrooms', 'rype-real-estate'); ?></label>
						<input type="number" class="border" name="baths" value="<?php if(isset($edit_bathrooms)) { echo $edit_bathrooms; } else { echo esc_attr($_POST['baths']); } ?>" />
					</div>

					<div class="form-block form-block-property-garages">
	                    <label><?php esc_html_e('Garages', 'rype-real-estate'); ?></label>
						<input type="number" class="border" name="garages" value="<?php if(isset($edit_garages)) { echo $edit_garages; } else { echo esc_attr($_POST['garages']); } ?>" />
					</div>

					<div class="row form-block-property-area">
						<div class="col-lg-6 col-md-6 form-block">
                            <label><?php esc_html_e('Area', 'rype-real-estate'); ?></label>
							<input type="number" class="border" name="area" value="<?php if(isset($edit_area)) { echo $edit_area; } else { echo esc_attr($_POST['area']); } ?>" />
						</div>

						<div class="col-lg-6 col-md-6 form-block">
                            <label><?php esc_html_e('Area Postfix', 'rype-real-estate'); ?></label>
							<input type="text" class="border" name="area_post" value="<?php if(isset($edit_area_postfix)) { echo $edit_area_postfix; } else { echo esc_attr($_POST['area_post']); } ?>" />
						</div>
					</div>

					<div class="form-block form-block-property-video-url">
                        <label><?php esc_html_e('Video URL', 'rype-real-estate'); ?></label>
						<input type="text" class="border" name="video_url" value="<?php if(isset($edit_video_url)) { echo $edit_video_url; } else { echo esc_url($_POST['video_url']); } ?>" />
					</div>

					<div class="form-block form-block-property-video-img">
                        <label><?php esc_html_e('Video Cover Image', 'rype-real-estate'); ?></label>
						<input type="text" class="border" name="video_img" value="<?php if(isset($edit_video_img)) { echo $edit_video_img; } else { echo esc_url($_POST['video_img']); } ?>" />
					</div>
				</div><!-- end col -->

				<div class="col-lg-6 col-md-6">
					<div class="form-block form-block-property-address">
                        <label><?php esc_html_e('Street Address*', 'rype-real-estate'); ?></label>
                        <?php if(isset($errors['address'])) { ?>
                            <div class="alert-box error"><h4><?php echo esc_attr($errors['address']); ?></h4></div>
                        <?php } ?>
                        <input class="required border" type="text" name="street_address" value="<?php if(isset($edit_address)) { echo $edit_address; } else { echo esc_attr($_POST['street_address']); } ?>" />
                    </div>

                    <div class="form-block form-block-property-location border">
                        <label for="property-location"><?php esc_html_e('Property Location', 'rype-real-estate'); ?></label>
                        <select data-placeholder="<?php esc_html_e('Select a location...', 'rype-real-estate'); ?>" name="property_location[]" id="property-location" multiple>
                            <?php
                            $property_locations = get_terms('property_location', array( 'hide_empty' => false, 'parent' => 0 )); 
                            if ( !empty( $property_locations ) && !is_wp_error( $property_locations ) ) { ?>
                                <?php foreach ( $property_locations as $property_location ) { ?>
                                    <option value="<?php echo esc_attr($property_location->slug); ?>" <?php if(isset($edit_property_location) && in_array($property_location->slug, $edit_property_location)) { echo 'selected'; } else if(isset($_POST['property_location']) && in_array($property_location->slug, $_POST['property_location'])) { echo 'selected'; } ?>><?php echo esc_attr($property_location->name); ?></option>
                                    <?php 
                                        $term_children = get_term_children($property_location->term_id, 'property_location'); 
                                        if(!empty($term_children)) {
                                            echo '<optgroup>';
                                            foreach ( $term_children as $child ) {
                                                $term = get_term_by( 'id', $child, 'property_location' ); ?>
                                                <option value="<?php echo $term->slug; ?>" <?php if(isset($edit_property_location) && in_array($term->slug, $edit_property_location)) { echo 'selected'; } else if(isset($_POST['property_location']) && in_array($term->slug, $_POST['property_location'])) { echo 'selected'; } ?>><?php echo $term->name; ?></option>
                                            <?php }
                                            echo '</optgroup>';
                                        }
                                    ?>
                                <?php } ?>
                            <?php } ?>
                        </select>

                        <?php if($members_add_locations == 'true') { ?>
                        <div class="property-location-new">
                            <span class="property-location-new-toggle"><?php esc_html_e("Don't see your location?", 'rype-real-estate'); ?> <a href="#"><?php esc_html_e('Add a new one.', 'rype-real-estate'); ?></a></span>
                            <div class="property-location-new-content show-none">
                                <input class="border" type="text" placeholder="Location name" />
                                <a href="#" class="button"><?php echo rypecore_get_icon($icon_set, 'plus', 'plus'); ?> <?php esc_html_e('Add', 'rype-real-estate'); ?></a>
                                <div class="clear"></div>
                            </div>
                        </div>
                        <?php } ?>
		            </div>

		            <div class="form-block form-block-property-amenities border">
                        <label for="property-amenities"><?php esc_html_e('Amenities', 'rype-real-estate'); ?></label>
                        <select data-placeholder="<?php esc_html_e('Select an amenity...', 'rype-real-estate'); ?>" name="property_amenities[]" id="property-amenities" multiple>
                            <?php
                            $property_amenities = get_terms('property_amenities', array( 'hide_empty' => false, 'parent' => 0 )); 
                            if ( !empty( $property_amenities ) && !is_wp_error( $property_amenities ) ) { ?>
                                <?php foreach ( $property_amenities as $property_amenity ) { ?>
                                    <option value="<?php echo esc_attr($property_amenity->slug); ?>" <?php if(isset($edit_property_amenities) && in_array($property_amenity->slug, $edit_property_amenities)) { echo 'selected'; } else if(isset($_POST['property_amenities']) && in_array($property_amenity->slug, $_POST['property_amenities'])) { echo 'selected'; } ?>><?php echo esc_attr($property_amenity->name); ?></option>
                                    <?php 
                                        $term_children = get_term_children($property_amenity->term_id, 'property_amenities'); 
                                        if(!empty($term_children)) {
                                            echo '<optgroup>';
                                            foreach ( $term_children as $child ) {
                                                $term = get_term_by( 'id', $child, 'property_amenities' ); ?>
                                                <option value="<?php echo $term->slug; ?>" <?php if(isset($edit_property_amenities) && in_array($term->slug, $edit_property_amenities)) { echo 'selected'; } else if(isset($_POST['property_amenities']) && in_array($term->slug, $_POST['property_amenities'])) { echo 'selected'; } ?>><?php echo $term->name; ?></option>
                                            <?php }
                                            echo '</optgroup>';
                                        }
                                    ?>
                                <?php } ?>
                            <?php } ?>
                        </select>

                        <?php if($members_add_amenities == 'true') { ?>
                        <div class="property-location-new">
                            <span class="property-location-new-toggle"><?php esc_html_e("Don't see your amenity?", 'rype-real-estate'); ?> <a href="#"><?php esc_html_e('Add a new one.', 'rype-real-estate'); ?></a></span>
                            <div class="property-location-new-content show-none">
                                <input class="border" type="text" placeholder="Location name" />
                                <a href="#" class="button"><?php echo rypecore_get_icon($icon_set, 'plus', 'plus'); ?> <?php esc_html_e('Add', 'rype-real-estate'); ?></a>
                                <div class="clear"></div>
                            </div>
                        </div>
                        <?php } ?>
		            </div>

		            <div class="form-block form-block-property-type border">
                        <label for="property-type"><?php esc_html_e('Property Type', 'rype-real-estate'); ?></label>
		                <select name="property_type" id="property-type">
		                    <option value=""><?php esc_html_e('Select a property type...', 'rype-real-estate'); ?></option>
		                    <?php 
		                        $property_type_terms = get_terms('property_type', array('hide_empty' => false)); 
		                        foreach ( $property_type_terms as $property_type_term ) { ?>
		                            <option value="<?php echo esc_attr($property_type_term->slug); ?>" <?php if(isset($edit_property_type)) { if(in_array($property_type_term->slug, $edit_property_type)) { echo 'selected'; } } else if(isset($_POST['property_type'])) { if($_POST['property_type'] == $property_type_term->slug) { echo 'selected'; } } ?>><?php echo esc_attr($property_type_term ->name); ?></option>;
		                    <?php } ?>
		                </select>
		            </div>

					<div class="form-block form-block-property-status border">
                        <label for="contract-type"><?php esc_html_e('Contract Type', 'rype-real-estate'); ?></label>
		                <select name="contract_type" id="contract-type">
		                    <option value=""><?php esc_html_e('Select a contract type...', 'rype-real-estate'); ?></option>
		                    <?php 
		                        $property_status_terms = get_terms('property_status', array('hide_empty' => false)); 
		                        foreach ( $property_status_terms as $property_status_term ) { ?>
		                            <option value="<?php echo esc_attr($property_status_term->slug); ?>" <?php if(isset($edit_property_status)) { if(in_array($property_status_term->slug, $edit_property_status)) { echo 'selected'; } } else if(isset($_POST['contract_type'])) { if($_POST['contract_type'] == $property_status_term->slug) { echo 'selected'; } } ?>><?php echo esc_attr($property_status_term ->name); ?></option>;
		                    <?php } ?>
		                </select>
		            </div>

		            <div class="form-block form-block-property-description">
                        <label><?php esc_html_e('Description', 'rype-real-estate'); ?></label>
						<textarea class="border" name="description"><?php if(isset($edit_content)) { echo $edit_content; } else { echo $_POST['description']; } ?></textarea>
					</div>
				</div><!-- end col -->
				</div><!-- end row -->

				<div class="module-border form-block-property-floor-plans">
	                <h3><?php esc_html_e('Floor Plans', 'rype-real-estate'); ?></h3>
	                <div class="form-block property-floor-plans">
	                    <div class="accordion">
	                    	<?php
	                    	$edit_floor_plans = unserialize($edit_floor_plans[0]); 
	                        if(!empty($edit_floor_plans)) { 
	                            $count = 0;                      
	                            foreach ($edit_floor_plans as $floor_plan) { ?>
	                                <h3 class="accordion-tab"><span class="floor-plan-title-mirror"><?php echo $floor_plan['title']; ?></span> <span class="delete-floor-plan right"><i class="fa fa-trash"></i> <?php esc_html_e('Delete', 'rype-real-estate'); ?></span></h3>
	                                <div class="floor-plan-item"> 
	                                    <div class="floor-plan-left"> 
	                                        <label><?php esc_html_e('Title:', 'rype-real-estate'); ?> </label> <input class="border floor-plan-title" type="text" name="rypecore_floor_plans[<?php echo $count; ?>][title]" placeholder="<?php esc_html_e('New Floor Plan', 'rype-real-estate'); ?>" value="<?php echo $floor_plan['title']; ?>" /><br/>
	                                        <label><?php esc_html_e('Size:', 'rype-real-estate'); ?> </label> <input class="border" type="text" name="rypecore_floor_plans[<?php echo $count; ?>][size]" value="<?php echo $floor_plan['size']; ?>" /><br/>
	                                        <label><?php esc_html_e('Rooms:', 'rype-real-estate'); ?> </label> <input class="border" type="number" name="rypecore_floor_plans[<?php echo $count; ?>][rooms]" value="<?php echo $floor_plan['rooms']; ?>" /><br/>
	                                        <label><?php esc_html_e('Bathrooms:', 'rype-real-estate'); ?> </label> <input class="border" type="number" name="rypecore_floor_plans[<?php echo $count; ?>][baths]" value="<?php echo $floor_plan['baths']; ?>" /><br/>
	                                    </div>
	                                    <div class="floor-plan-right">
	                                        <label><?php esc_html_e('Description:', 'rype-real-estate'); ?></label>
	                                        <textarea class="border" name="rypecore_floor_plans[<?php echo $count; ?>][description]"><?php echo $floor_plan['description']; ?></textarea>
	                                        <div>
	                                            <label><?php esc_html_e('Image', 'rype-real-estate'); ?></label>
	                                            <input class="border" type="text" name="rypecore_floor_plans[<?php echo $count; ?>][img]" value="<?php echo $floor_plan['img']; ?>" />
	                                            <span><em><?php esc_html_e('Provide the absolute url to a hosted image.', 'rype-real-estate'); ?></em></span>
	                                        </div>
	                                    </div>
	                                    <div class="clear"></div>
	                                </div> 
	                                <?php $count++; ?>
	                            <?php }
	                        }
	                        ?>
	                    </div>
	                    <span class="button light small add-floor-plan"><i class="fa fa-plus"></i> <?php esc_html_e('Create New Floor Plan', 'rype-real-estate'); ?></span>
	                </div>
	            </div>

	            <?php 
                $custom_fields = get_option('rypecore_custom_fields');
                if(!empty($custom_fields)) { ?>
                    <div class="module-border form-block-property-custom-fields">
                    <h3><?php esc_html_e('Additional Details', 'rype-real-estate'); ?></h3>
                    <?php $count = 0;
                    echo '<div class="row">';                    
                    foreach ($custom_fields as $custom_field) { 
                        if(isset($custom_field['front_end'])) {
                            $custom_field_key = strtolower(str_replace(' ', '_', $custom_field['name'])); 
                            if(isset($edit_property_id) && !empty($edit_property_id)) { $fieldValue = get_post_meta($edit_property_id, 'rypecore_custom_field_'.$custom_field['id'], true); }  ?>
                            <div class="col-lg-4 col-md-4 custom-field-item custom-field-<?php echo $custom_field_key; ?>">
                                <div class="form-block border">
                                    <label title="<?php echo $custom_field['name']; ?>"><?php echo $custom_field['name']; ?>:</label> 
                                    <?php if(isset($custom_field['type']) && $custom_field['type'] == 'select') { ?>
                                        <select name="rypecore_property_custom_fields[<?php echo $count; ?>][value]">
                                            <option value=""><?php esc_html_e('Select an option...', 'rype-real-estate'); ?></option>
                                            <?php 
                                                if(isset($custom_field['select_options'])) { $selectOptions = $custom_field['select_options']; } else { $selectOptions =  ''; }
                                                if(!empty($selectOptions)) {
                                                    foreach($selectOptions as $option) { ?>
                                                        <option value="<?php echo $option; ?>" <?php if(isset($fieldValue) && $fieldValue == $option) { echo 'selected'; } else { if($_POST['rypecore_property_custom_fields'][$count]['value'] == $option) { echo 'selected'; } } ?>><?php echo $option; ?></option>
                                                    <?php }
                                                }
                                            ?>
                                        </select>
                                    <?php } else { ?>
                                        <input type="<?php if(isset($custom_field['type']) && $custom_field['type'] == 'num') { echo 'number'; } else { echo 'text'; } ?>" class="border" name="rypecore_property_custom_fields[<?php echo $count; ?>][value]" value="<?php if(isset($fieldValue)) { echo $fieldValue; } else { echo esc_attr($_POST['rypecore_property_custom_fields'][$count]['value']); } ?>" />
                                    <?php } ?>
                                    <input type="hidden" name="rypecore_property_custom_fields[<?php echo $count; ?>][key]" value="rypecore_custom_field_<?php echo $custom_field['id']; ?>" />
                                </div>
                            </div>
                            <?php $count++; ?>
                        <?php } ?>
                    <?php }
                    echo '</div>';
                    echo '</div>';
                } ?>

                <!-- hook in for add-ons -->
                <?php do_action('rype_real_estate_after_property_submit_general', $edit_property_id); ?>

			</div><!-- end general info -->

			<div class="multi-page-form-content" id="property-images">
				<h3><?php esc_html_e('Property Images', 'rype-real-estate'); ?></h3>

				<div class="form-block featured-img">
					<?php if(isset($edit_property_id) && !empty($edit_property_id)) { echo get_the_post_thumbnail( $edit_property_id, 'thumbnail', array( 'class' => 'featured-img' ) ); } ?>
	                <label for="featured_img"><?php esc_html_e('Featured Image', 'rype-real-estate'); ?></label><br/>
	                <input id="featured_img" name="featured_img" type="file">
	            </div>

	            <div class="form-block gallery">
	            	<label><?php esc_html_e('Gallery Images', 'rype-real-estate'); ?></label>
	            	
	            	<div class="additional-img-container">

	            		<?php 
		            	if(isset($edit_additional_images)) {
		            		$edit_additional_images = explode(",", $edit_additional_images[0]);
		            		if(!empty($edit_additional_images)) {
		            			foreach ($edit_additional_images as $edit_additional_image) {
		            				if(!empty($edit_additional_image)) {
			            				$additional_img_attachment_id = rype_real_estate_get_attachment_id_by_url($edit_additional_image); ?>
			            				<table>
			            					<tr>
			            					<td>
			            					<div class="media-uploader-additional-img">
		                        			<img class="additional-img-preview" src="<?php echo $edit_additional_image; ?>" alt="" />
		                        			<a href="<?php echo get_the_permalink().'?edit_property='.$edit_property_id.'&additional_img_attachment_id='.$additional_img_attachment_id; ?>" class="delete-additional-img right"><i class="fa fa-trash"></i> <?php esc_html_e('Delete', 'rype-real-estate'); ?></a>
		                        			</div>
			            					</td>
			            					</tr>
			            				</table>
			            			<?php }
		            			}
		            		}
		            	} ?>

	            		<table>
	                        <tr>
	                        <td>
	                        <div class="media-uploader-additional-img">
	                        <input type="file" class="additional_img" name="additional_img1" value="" />
	                        <span class="delete-additional-img right"><i class="fa fa-trash"></i> <?php esc_html_e('Delete', 'rype-real-estate'); ?></span>
	                        </div>
	                        </td>
	                        </tr>
	                    </table>
	                </div>
	                <span class="button light small add-additional-img"><i class="fa fa-plus"></i>  <?php esc_html_e('Add Image', 'rype-real-estate'); ?></span>

	            </div>
			</div><!-- end property images -->

			<div class="multi-page-form-content" id="map">
            	<h3><?php esc_html_e('Map', 'rype-real-estate'); ?></h3>
            	<div class="left">
                    <div class="row">
                        <div class="col-lg-6 col-md-6 form-block">
                            <input type="text" class="border" name="latitude" id="property_latitude" placeholder="<?php esc_html_e('Latitude', 'rype-real-estate'); ?>" value="<?php if(isset($latitude)) { echo $latitude; } else { echo esc_attr($_POST['latitude']); } ?>" />
                        </div>

                        <div class="col-lg-6 col-md-6 form-block">
                            <input type="text" class="border" name="longitude" id="property_longitude" placeholder="<?php esc_html_e('Longitude', 'rype-real-estate'); ?>" value="<?php if(isset($longitude)) { echo $longitude; } else { echo esc_attr($_POST['longitude']); } ?>" />
                        </div>
                    </div>
            	</div>
            	<?php include(get_parent_theme_file_path('/admin/admin_map.php')); ?>
            </div>

            <div class="multi-page-form-content" id="owner-info">
	            <h3><?php esc_html_e('Owner Info', 'rype-real-estate'); ?></h3>

	            <div class="form-block form-block-property-agent-display">
	                <label><?php esc_html_e('What do you want displayed for the agent info?', 'rype-real-estate'); ?></label><br/>
	                <input type="radio" name="agent_display" id="agent_display_author" value="author" <?php if (!empty($_POST)) { if($_POST['agent_display'] == 'author') { echo 'checked'; } } else if(!isset($_POST['agent_display'])) { echo 'checked'; }  ?> /><?php esc_html_e('Your Profile Info', 'rype-real-estate'); ?><br/>
	                <input type="radio" name="agent_display" id="agent_display_agent" value="agent" <?php if(isset($edit_agent_display)) { if($edit_agent_display == 'agent') { echo 'checked'; } } else { if($_POST['agent_display'] == 'agent') { echo 'checked'; } } ?> /><?php esc_html_e('Existing Agent', 'rype-real-estate'); ?><br/>
	                <input type="radio" name="agent_display" id="agent_display_custom" value="custom" <?php if(isset($edit_agent_display)) { if($edit_agent_display == 'custom') { echo 'checked'; } } else { if($_POST['agent_display'] == 'custom') { echo 'checked'; } } ?> /><?php esc_html_e('Custom', 'rype-real-estate'); ?><br/>
	                <input type="radio" name="agent_display" id="agent_display_none" value="none" <?php if(isset($edit_agent_display)) { if($edit_agent_display == 'none') { echo 'checked'; } } else { if($_POST['agent_display'] == 'none') { echo 'checked'; } } ?> /><?php esc_html_e('None', 'rype-real-estate'); ?><br/>
	            </div>
	            <br/>

	            <div class="form-block form-block-agent-options form-block-select-agent <?php if(isset($edit_agent_display) && $edit_agent_display == 'agent') { echo 'show'; } else { echo 'show-none'; } ?>">
	                <?php
	                    $agent_listing_args = array(
	                        'post_type' => 'rype-agent',
	                        'posts_per_page' => -1
	                    );

	                    $agent_listing_query = new WP_Query( $agent_listing_args );
	                ?>
	                <label for="agent_select"><?php esc_html_e('Select Agent', 'rype-real-estate'); ?></label><br/>
	                <div class="form-block border">
	                    <select name="agent_select">
	                        <option value="" placeholder="<?php esc_html_e('Select an option...', 'rype-real-estate'); ?>"></option>
	                        <?php if ( $agent_listing_query->have_posts() ) : while ( $agent_listing_query->have_posts() ) : $agent_listing_query->the_post(); ?>

	                        <option value="<?php echo get_the_ID(); ?>" <?php if(isset($edit_agent_select) && $edit_agent_select == get_the_ID()) { echo 'selected'; } else { if ($_POST['agent_select'] == get_the_ID()) { echo 'selected'; } } ?>><?php the_title(); ?></option>

	                    <?php endwhile; ?>
	                        </select>
	                    <?php else: ?>
	                        <option value=""><?php esc_html_e('Sorry, no agents have been posted yet.', 'rype-real-estate'); ?></option>
	                        </select>
	                    <?php endif; ?>
	                </div>
	            </div>

	            <div class="form-block form-block-agent-options form-block-custom-agent <?php if(isset($edit_agent_display) && $edit_agent_display == 'custom') { echo 'show'; } else { echo 'show-none'; } ?>">
	                <label><?php esc_html_e('Custom Owner/Agent Details', 'rype-real-estate'); ?></label>
	                <input type="text" class="border" name="agent_custom_name" placeholder="<?php esc_html_e('Name', 'rype-real-estate'); ?>" value="<?php if(isset($edit_agent_custom_name)) { echo $edit_agent_custom_name; } else { echo esc_attr($_POST['agent_custom_name']); } ?>" />
	                <input type="text" class="border" name="agent_custom_email" placeholder="<?php esc_html_e('Email', 'rype-real-estate'); ?>" value="<?php if(isset($edit_agent_custom_email)) { echo $edit_agent_custom_email; } else { echo esc_attr($_POST['agent_custom_email']); } ?>" />
	                <input type="text" class="border" name="agent_custom_phone" placeholder="<?php esc_html_e('Phone', 'rype-real-estate'); ?>" value="<?php if(isset($edit_agent_custom_phone)) { echo $edit_agent_custom_phone; } else { echo esc_attr($_POST['agent_custom_phone']); } ?>" />
	                <input type="text" class="border" name="agent_custom_url" placeholder="<?php esc_html_e('Website', 'rype-real-estate'); ?>" value="<?php if(isset($edit_agent_custom_url)) { echo esc_url($edit_agent_custom_url); } else { echo esc_url($_POST['agent_custom_url']); } ?>" />
	            </div>
	        </div><!-- end owner info -->

			<div class="multi-page-form-nav">
				<div class="multi-page-form-nav-item form-prev button show-none" onclick="refreshMap()"><?php esc_html_e('Previous', 'rype-real-estate'); ?></div>
				<div class="multi-page-form-nav-item button disabled" onclick="refreshMap()"><?php esc_html_e('Previous', 'rype-real-estate'); ?></div>
				<div class="multi-page-form-nav-item form-next button right" onclick="refreshMap()"><?php esc_html_e('Next', 'rype-real-estate'); ?></div>
				<input type="submit" class="button alt right" value="<?php echo $form_submit_text; ?>" />
				<div class="clear"></div> 
			</div>

	    </form>

	</div><!-- end form container -->

<?php } //end function ?>