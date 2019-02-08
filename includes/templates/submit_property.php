<?php global $current_user, $wp_roles; ?>    

<!-- start submit property -->
<div class="user-dashboard">
	<?php if(is_user_logged_in()) { 

	//global settings
	$icon_set = esc_attr(get_option('ns_core_icon_set', 'fa'));
    $members_submit_property_fields_default = ns_real_estate_default_property_submit_fields();
    $members_submit_property_fields = get_option('ns_members_submit_property_fields', $members_submit_property_fields_default); 
    if(empty($members_submit_property_fields)) { $members_submit_property_fields = array(); }
	$members_my_properties_page = get_option('ns_members_my_properties_page');
	$members_add_locations = esc_attr(get_option('ns_members_add_locations', 'true'));
	$members_add_amenities = esc_attr(get_option('ns_members_add_amenities', 'true'));
    $area_postfix_default = esc_attr(get_option('ns_property_default_area_postfix', 'Sq Ft'));

	//intialize variables
	$errors = '';
	$success = '';

	//If editting property, get data and determine form action
	if (isset($_GET['edit_property']) && !empty($_GET['edit_property'])) {
	    $form_submit_text = esc_html__('Update Property', 'ns-real-estate');
	    $edit_property_id = $_GET['edit_property'];
	    $form_action = '?edit_property='.esc_attr($edit_property_id);
	    $values = get_post_custom( $edit_property_id );
	    $edit_address = isset( $values['ns_property_address'] ) ? esc_attr( $values['ns_property_address'][0] ) : '';
    	$edit_price = isset( $values['ns_property_price'] ) ? esc_attr( $values['ns_property_price'][0] ) : '';
    	$edit_price_postfix = isset( $values['ns_property_price_postfix'] ) ? esc_attr( $values['ns_property_price_postfix'][0] ) : '';
    	$edit_bedrooms = isset( $values['ns_property_bedrooms'] ) ? esc_attr( $values['ns_property_bedrooms'][0] ) : '';
    	$edit_bathrooms = isset( $values['ns_property_bathrooms'] ) ? esc_attr( $values['ns_property_bathrooms'][0] ) : '';
    	$edit_garages = isset( $values['ns_property_garages'] ) ? esc_attr( $values['ns_property_garages'][0] ) : '';
    	$edit_area = isset( $values['ns_property_area'] ) ? esc_attr( $values['ns_property_area'][0] ) : '';
    	$edit_area_postfix = isset( $values['ns_property_area_postfix'] ) ? esc_attr( $values['ns_property_area_postfix'][0] ) : $area_postfix_default;
    	$edit_description = isset( $values['ns_property_description'] ) ? $values['ns_property_description'][0] : '';
        $edit_floor_plans = isset($values['ns_property_floor_plans']) ? $values['ns_property_floor_plans'] : '';
        $edit_additional_images = isset($values['ns_additional_img']) ? $values['ns_additional_img'] : '';
    	$edit_video_url = isset( $values['ns_property_video_url'] ) ? esc_attr( $values['ns_property_video_url'][0] ) : '';
    	$edit_video_img = isset( $values['ns_property_video_img'] ) ? esc_attr( $values['ns_property_video_img'][0] ) : '';
    	$latitude = isset( $values['ns_property_latitude'] ) ? esc_attr( $values['ns_property_latitude'][0] ) : '';
    	$longitude = isset( $values['ns_property_longitude'] ) ? esc_attr( $values['ns_property_longitude'][0] ) : '';
    	$edit_agent_display = isset( $values['ns_agent_display'] ) ? esc_attr( $values['ns_agent_display'][0] ) : 'none';
    	$edit_agent_select = isset( $values['ns_agent_select'] ) ? esc_attr( $values['ns_agent_select'][0] ) : '';
    	$edit_agent_custom_name = isset( $values['ns_agent_custom_name'] ) ? esc_attr( $values['ns_agent_custom_name'][0] ) : '';
    	$edit_agent_custom_email = isset( $values['ns_agent_custom_email'] ) ? esc_attr( $values['ns_agent_custom_email'][0] ) : '';
    	$edit_agent_custom_phone = isset( $values['ns_agent_custom_phone'] ) ? esc_attr( $values['ns_agent_custom_phone'][0] ) : '';
    	$edit_agent_custom_url = isset( $values['ns_agent_custom_url'] ) ? esc_attr( $values['ns_agent_custom_url'][0] ) : '';
    	$edit_property_location = ns_real_estate_get_property_location($edit_property_id , null, 'true');
    	$edit_property_amenities = ns_real_estate_get_property_amenities($edit_property_id , true, 'true');
    	$edit_property_status = ns_real_estate_get_property_status($edit_property_id, 'true');
    	$edit_property_type = ns_real_estate_get_property_type($edit_property_id, 'true');

    	//delete additional image
		if (!empty($_GET['additional_img_attachment_id'])) {
		    $image_to_delete = $_GET['additional_img_attachment_id'];
		    $image_to_delete_url = wp_get_attachment_url( $image_to_delete );
		    $new_edit_additional_images = explode(",", $edit_additional_images[0]);
		                    
		    $key = array_search($image_to_delete_url, $new_edit_additional_images);
		    if($key!==false) { unset($new_edit_additional_images[$key]); }

		    //update values
		    $strAdditionalImgs = implode(",", $new_edit_additional_images);
		    update_post_meta( $edit_property_id, 'ns_additional_img', $strAdditionalImgs );
		}
    	
	} else {
		$edit_property_id = '';
		$form_action = '';
		$form_submit_text = esc_html__('Submit Property', 'ns-real-estate');
	}

	//If form was submitted insert/update post
	if (!empty($_POST)) {

		if(isset($_GET['edit_property']) && !empty($_GET['edit_property'])) {
    		$inserted_post = ns_real_estate_insert_property_post($_GET['edit_property']);
    	} else {
    		$inserted_post = ns_real_estate_insert_property_post();
    	}
		$errors = $inserted_post['errors'];
		$success = $inserted_post['success'];
	} ?>

	<!-- start submit property form -->
	<div class="user-submit-property-form">

		<?php if ($success != '') { ?>
	        <div class="alert-box success"><h4><?php echo wp_kses_post($success); ?></h4><?php if (!empty($members_my_properties_page)) { echo '<a href="'.esc_url($members_my_properties_page).'">'. esc_html__('View your properties.', 'ns-real-estate').'</a>'; } ?></div>
	    <?php } ?>

	    <form method="post" action="<?php echo get_the_permalink().$form_action; ?>" enctype="multipart/form-data">

			<div class="submit-property-section" id="general-info">
		    	<h3><?php esc_html_e('General Info', 'ns-real-estate'); ?></h3>
		    	
                <div class="form-block form-block-property-title">
                    <?php if(isset($errors['title'])) { ?>
                        <div class="alert-box error"><h4><?php echo esc_attr($errors['title']); ?></h4></div>
                    <?php } ?>
                    <label><?php esc_html_e('Title*', 'ns-real-estate'); ?></label>
                    <input class="required border" type="text" name="title" value="<?php if(!empty($edit_property_id)) { echo get_the_title( $edit_property_id ); } else { echo esc_attr($_POST['title']); } ?>" />
                </div>

                <?php if(in_array('Description', $members_submit_property_fields)) { ?>
                <div class="form-block form-block-property-description">
                    <label><?php esc_html_e('Description', 'ns-real-estate'); ?></label>
                    <?php 
                    $editor_id = 'propertydescription';
                    $settings = array('textarea_name' => 'description', 'editor_height' => 180, 'quicktags' => array('buttons' => ','));
                    wp_editor( $edit_description, $editor_id, $settings);
                    ?>
                </div>
                <?php } ?>

		    	<div class="row">
				<div class="col-lg-6 col-md-6">

					<div class="row form-block-property-price">
						<?php if(isset($errors['price'])) { ?>
						       <div class="col-lg-12"><div class="alert-box error"><h4><?php echo esc_attr($errors['price']); ?></h4></div></div>
						   <?php } ?>
						<div class="col-lg-6 col-md-6 form-block">
	                           <label><?php esc_html_e('Price*', 'ns-real-estate'); ?></label>
							<input class="required border" type="number" name="price" value="<?php if(isset($edit_price)) { echo $edit_price; } else { echo esc_attr($_POST['price']); } ?>" />
						</div>

                        <?php if(in_array('Price Postfix', $members_submit_property_fields )) { ?>
						<div class="col-lg-6 col-md-6 form-block">
	                           <label><?php esc_html_e('Price Postfix', 'ns-real-estate'); ?></label>
							<input type="text" class="border" name="price_post" value="<?php if(isset($edit_price_postfix)) { echo $edit_price_postfix; } else { echo esc_attr($_POST['price_post']); } ?>" />
						</div>
                        <?php } ?>
					</div>

                    <?php if(in_array('Beds', $members_submit_property_fields )) { ?>
					<div class="form-block form-block-property-beds">
	                    <label><?php esc_html_e('Bedrooms', 'ns-real-estate'); ?></label>
						<input type="number" class="border" name="beds" value="<?php if(isset($edit_bedrooms)) { echo $edit_bedrooms; } else { echo esc_attr($_POST['beds']); } ?>" />
					</div>
                    <?php } ?>

                    <?php if(in_array('Baths', $members_submit_property_fields )) { ?>
					<div class="form-block form-block-property-baths">
	                    <label><?php esc_html_e('Bathrooms', 'ns-real-estate'); ?></label>
						<input type="number" class="border" name="baths" value="<?php if(isset($edit_bathrooms)) { echo $edit_bathrooms; } else { echo esc_attr($_POST['baths']); } ?>" />
					</div>
                    <?php } ?>

                    <?php if(in_array('Garages', $members_submit_property_fields )) { ?>
					<div class="form-block form-block-property-garages">
	                    <label><?php esc_html_e('Garages', 'ns-real-estate'); ?></label>
						<input type="number" class="border" name="garages" value="<?php if(isset($edit_garages)) { echo $edit_garages; } else { echo esc_attr($_POST['garages']); } ?>" />
					</div>
                    <?php } ?>

					<div class="row form-block-property-area">
                        <?php if(in_array('Area', $members_submit_property_fields )) { ?>
						<div class="col-lg-6 col-md-6 form-block">
                            <label><?php esc_html_e('Area', 'ns-real-estate'); ?></label>
							<input type="number" class="border" name="area" value="<?php if(isset($edit_area)) { echo $edit_area; } else { echo esc_attr($_POST['area']); } ?>" />
						</div>
                        <?php } ?>

                        <?php if(in_array('Area Postfix', $members_submit_property_fields )) { ?>
						<div class="col-lg-6 col-md-6 form-block">
                            <label><?php esc_html_e('Area Postfix', 'ns-real-estate'); ?></label>
							<input type="text" class="border" name="area_post" value="<?php if(isset($edit_area_postfix)) { echo $edit_area_postfix; } else if($_POST['area_post']) { echo esc_attr($_POST['area_post']); } else { echo $area_postfix_default; } ?>" />
						</div>
                        <?php } ?>
					</div>

                    <?php if(in_array('Video', $members_submit_property_fields )) { ?>
					<div class="form-block form-block-property-video-url">
                        <label><?php esc_html_e('Video URL', 'ns-real-estate'); ?></label>
						<input type="text" class="border" name="video_url" value="<?php if(isset($edit_video_url)) { echo $edit_video_url; } else { echo esc_url($_POST['video_url']); } ?>" />
					</div>

					<div class="form-block form-block-property-video-img">
                        <label><?php esc_html_e('Video Cover Image', 'ns-real-estate'); ?></label>
						<input type="text" class="border" name="video_img" value="<?php if(isset($edit_video_img)) { echo $edit_video_img; } else { echo esc_url($_POST['video_img']); } ?>" />
					</div>
                    <?php }  ?>

				</div><!-- end col -->

				<div class="col-lg-6 col-md-6">
					<div class="form-block form-block-property-address">
                        <?php if(isset($errors['address'])) { ?>
                            <div class="alert-box error"><h4><?php echo esc_attr($errors['address']); ?></h4></div>
                        <?php } ?>
                        <label><?php esc_html_e('Street Address*', 'ns-real-estate'); ?></label>
                        <input class="required border" type="text" name="street_address" value="<?php if(isset($edit_address)) { echo $edit_address; } else { echo esc_attr($_POST['street_address']); } ?>" />
                    </div>

                    <?php if(in_array('Property Location', $members_submit_property_fields )) { ?>
                    <div class="form-block form-block-property-location border">
                        <label for="property-location"><?php esc_html_e('Property Location', 'ns-real-estate'); ?></label>
                        <select data-placeholder="<?php esc_html_e('Select a location...', 'ns-real-estate'); ?>" name="property_location[]" id="property-location" multiple>
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
                        <div class="property-add-tax-form property-location-new">
                            <span class="property-location-new-toggle note"><?php esc_html_e("Don't see your location?", 'ns-real-estate'); ?> <a href="#"><?php esc_html_e('Add a new one.', 'ns-real-estate'); ?></a></span>
                            <div class="property-location-new-content show-none">
                                <input class="border" type="text" placeholder="Location name" />
                                <a href="#" class="button"><?php echo ns_core_get_icon($icon_set, 'plus', 'plus'); ?> <?php esc_html_e('Add', 'ns-real-estate'); ?></a>
                                <div class="clear"></div>
                            </div>
                        </div>
                        <?php } ?>
		            </div>
                    <?php } ?>

                    <?php if(in_array('Amenities', $members_submit_property_fields )) { ?>
		            <div class="form-block form-block-property-amenities border">
                        <label for="property-amenities"><?php esc_html_e('Amenities', 'ns-real-estate'); ?></label>
                        <select data-placeholder="<?php esc_html_e('Select an amenity...', 'ns-real-estate'); ?>" name="property_amenities[]" id="property-amenities" multiple>
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
                        <div class="property-add-tax-form property-location-new">
                            <span class="property-location-new-toggle note"><?php esc_html_e("Don't see your amenity?", 'ns-real-estate'); ?> <a href="#"><?php esc_html_e('Add a new one.', 'ns-real-estate'); ?></a></span>
                            <div class="property-location-new-content show-none">
                                <input class="border" type="text" placeholder="Location name" />
                                <a href="#" class="button"><?php echo ns_core_get_icon($icon_set, 'plus', 'plus'); ?> <?php esc_html_e('Add', 'ns-real-estate'); ?></a>
                                <div class="clear"></div>
                            </div>
                        </div>
                        <?php } ?>
		            </div>
                    <?php } ?>

                    <?php if(in_array('Property Type', $members_submit_property_fields )) { ?>
		            <div class="form-block form-block-property-type border">
                        <label for="property-type"><?php esc_html_e('Property Type', 'ns-real-estate'); ?></label>
		                <select name="property_type" id="property-type">
		                    <option value=""><?php esc_html_e('Select a property type...', 'ns-real-estate'); ?></option>
		                    <?php 
		                        $property_type_terms = get_terms('property_type', array('hide_empty' => false)); 
		                        foreach ( $property_type_terms as $property_type_term ) { ?>
		                            <option value="<?php echo esc_attr($property_type_term->slug); ?>" <?php if(isset($edit_property_type)) { if(in_array($property_type_term->slug, $edit_property_type)) { echo 'selected'; } } else if(isset($_POST['property_type'])) { if($_POST['property_type'] == $property_type_term->slug) { echo 'selected'; } } ?>><?php echo esc_attr($property_type_term ->name); ?></option>;
		                    <?php } ?>
		                </select>
		            </div>
                    <?php } ?>

                    <?php if(in_array('Property Status', $members_submit_property_fields )) { ?>
					<div class="form-block form-block-property-status border">
                        <label for="contract-type"><?php esc_html_e('Contract Type', 'ns-real-estate'); ?></label>
		                <select name="contract_type" id="contract-type">
		                    <option value=""><?php esc_html_e('Select a contract type...', 'ns-real-estate'); ?></option>
		                    <?php 
		                        $property_status_terms = get_terms('property_status', array('hide_empty' => false)); 
		                        foreach ( $property_status_terms as $property_status_term ) { ?>
		                            <option value="<?php echo esc_attr($property_status_term->slug); ?>" <?php if(isset($edit_property_status)) { if(in_array($property_status_term->slug, $edit_property_status)) { echo 'selected'; } } else if(isset($_POST['contract_type'])) { if($_POST['contract_type'] == $property_status_term->slug) { echo 'selected'; } } ?>><?php echo esc_attr($property_status_term ->name); ?></option>;
		                    <?php } ?>
		                </select>
		            </div>
                    <?php } ?>

				</div><!-- end col -->
				</div><!-- end row -->

                <!-- hook in for add-ons -->
                <?php do_action('ns_real_estate_after_property_submit_general', $edit_property_id); ?>

			</div><!-- end general info -->

            <div class="submit-property-section" id="property-custom-fields">
                <?php 
                $custom_fields = get_option('ns_property_custom_fields');
                if(!empty($custom_fields)) { ?>
                    <div class="module-border form-block-property-custom-fields">
                    <h3><?php esc_html_e('Additional Details', 'ns-real-estate'); ?></h3>
                    <?php $count = 0;
                    echo '<div class="row">';                    
                    foreach ($custom_fields as $custom_field) { 
                        if(isset($custom_field['front_end'])) {
                            $custom_field_key = strtolower(str_replace(' ', '_', $custom_field['name'])); 
                            if(isset($edit_property_id) && !empty($edit_property_id)) { $fieldValue = get_post_meta($edit_property_id, 'ns_property_custom_field_'.$custom_field['id'], true); }  ?>
                            <div class="col-lg-4 col-md-4 custom-field-item custom-field-<?php echo $custom_field_key; ?>">
                                <div class="form-block border">
                                    <label title="<?php echo $custom_field['name']; ?>"><?php echo $custom_field['name']; ?>:</label> 
                                    <?php if(isset($custom_field['type']) && $custom_field['type'] == 'select') { ?>
                                        <select name="ns_property_custom_fields[<?php echo $count; ?>][value]">
                                            <option value=""><?php esc_html_e('Select an option...', 'ns-real-estate'); ?></option>
                                            <?php 
                                                if(isset($custom_field['select_options'])) { $selectOptions = $custom_field['select_options']; } else { $selectOptions =  ''; }
                                                if(!empty($selectOptions)) {
                                                    foreach($selectOptions as $option) { ?>
                                                        <option value="<?php echo $option; ?>" <?php if(isset($fieldValue) && $fieldValue == $option) { echo 'selected'; } else { if($_POST['ns_property_custom_fields'][$count]['value'] == $option) { echo 'selected'; } } ?>><?php echo $option; ?></option>
                                                    <?php }
                                                }
                                            ?>
                                        </select>
                                    <?php } else { ?>
                                        <input type="<?php if(isset($custom_field['type']) && $custom_field['type'] == 'num') { echo 'number'; } else { echo 'text'; } ?>" class="border" name="ns_property_custom_fields[<?php echo $count; ?>][value]" value="<?php if(isset($fieldValue)) { echo $fieldValue; } else { echo esc_attr($_POST['ns_property_custom_fields'][$count]['value']); } ?>" />
                                    <?php } ?>
                                    <input type="hidden" name="ns_property_custom_fields[<?php echo $count; ?>][key]" value="ns_property_custom_field_<?php echo $custom_field['id']; ?>" />
                                </div>
                            </div>
                            <?php $count++; ?>
                        <?php } ?>
                    <?php }
                    echo '</div>';
                    echo '</div>';
                } ?>
            </div><!-- end property custom fields -->

            <?php if(in_array('Floor Plans', $members_submit_property_fields )) { ?>
            <div class="submit-property-section" id="property-floor-plans">
                <div class="module-border form-block-property-floor-plans">
                    <h3><?php esc_html_e('Floor Plans', 'ns-real-estate'); ?></h3>
                    <div class="form-block property-floor-plans">
                        <div class="accordion">
                            <?php
                            $edit_floor_plans = unserialize($edit_floor_plans[0]); 
                            if(!empty($edit_floor_plans)) { 
                                $count = 0;                      
                                foreach ($edit_floor_plans as $floor_plan) { ?>
                                    <h4 class="accordion-tab"><span class="floor-plan-title-mirror"><?php echo $floor_plan['title']; ?></span> <span class="delete-floor-plan right"><i class="fa fa-trash"></i> <?php esc_html_e('Delete', 'ns-real-estate'); ?></span></h4>
                                    <div class="floor-plan-item"> 
                                        <div class="floor-plan-left"> 
                                            <label><?php esc_html_e('Title:', 'ns-real-estate'); ?> </label> <input class="border floor-plan-title" type="text" name="ns_property_floor_plans[<?php echo $count; ?>][title]" placeholder="<?php esc_html_e('New Floor Plan', 'ns-real-estate'); ?>" value="<?php echo $floor_plan['title']; ?>" /><br/>
                                            <label><?php esc_html_e('Size:', 'ns-real-estate'); ?> </label> <input class="border" type="text" name="ns_property_floor_plans[<?php echo $count; ?>][size]" value="<?php echo $floor_plan['size']; ?>" /><br/>
                                            <label><?php esc_html_e('Rooms:', 'ns-real-estate'); ?> </label> <input class="border" type="number" name="ns_property_floor_plans[<?php echo $count; ?>][rooms]" value="<?php echo $floor_plan['rooms']; ?>" /><br/>
                                            <label><?php esc_html_e('Bathrooms:', 'ns-real-estate'); ?> </label> <input class="border" type="number" name="ns_property_floor_plans[<?php echo $count; ?>][baths]" value="<?php echo $floor_plan['baths']; ?>" /><br/>
                                        </div>
                                        <div class="floor-plan-right">
                                            <label><?php esc_html_e('Description:', 'ns-real-estate'); ?></label>
                                            <textarea class="border" name="ns_property_floor_plans[<?php echo $count; ?>][description]"><?php echo $floor_plan['description']; ?></textarea>
                                            <div>
                                                <label><?php esc_html_e('Image', 'ns-real-estate'); ?></label>
                                                <input class="border" type="text" name="ns_property_floor_plans[<?php echo $count; ?>][img]" value="<?php echo $floor_plan['img']; ?>" />
                                                <span><em><?php esc_html_e('Provide the absolute url to a hosted image.', 'ns-real-estate'); ?></em></span>
                                            </div>
                                        </div>
                                        <div class="clear"></div>
                                    </div> 
                                    <?php $count++; ?>
                                <?php }
                            }
                            ?>
                        </div>
                        <div class="button light small add-floor-plan"><i class="fa fa-plus"></i> <?php esc_html_e('Create New Floor Plan', 'ns-real-estate'); ?></div>
                    </div>
                </div>
            </div><!-- end floor plans -->
            <?php } ?>

            <?php if(in_array('Featured Image', $members_submit_property_fields) || in_array('Gallery Images', $members_submit_property_fields)) { ?>
			<div class="submit-property-section" id="property-images">
				<h3><?php esc_html_e('Property Images', 'ns-real-estate'); ?></h3>

                <?php if(in_array('Featured Image', $members_submit_property_fields )) { ?>
				<div class="form-block featured-img">
					<?php if(isset($edit_property_id) && !empty($edit_property_id)) { echo get_the_post_thumbnail( $edit_property_id, 'thumbnail', array( 'class' => 'featured-img' ) ); } ?>
	                <label for="featured_img"><?php esc_html_e('Featured Image', 'ns-real-estate'); ?></label><br/>
	                <input id="featured_img" name="featured_img" type="file">
	            </div>
                <?php } ?>

                <?php if(in_array('Gallery Images', $members_submit_property_fields )) { ?>
	            <div class="form-block">
	            	<label><?php esc_html_e('Gallery Images', 'ns-real-estate'); ?></label>
	            	
	            	<div class="additional-img-container">

	            		<?php 
		            	if(isset($edit_additional_images)) {
		            		$edit_additional_images = explode(",", $edit_additional_images[0]);
		            		if(!empty($edit_additional_images)) {
		            			foreach ($edit_additional_images as $edit_additional_image) {
		            				if(!empty($edit_additional_image)) {
			            				$additional_img_attachment_id = ns_real_estate_get_attachment_id_by_url($edit_additional_image); ?>
			            				<table>
			            					<tr>
			            					<td>
			            					<div class="media-uploader-additional-img">
		                        			<img class="additional-img-preview" src="<?php echo $edit_additional_image; ?>" alt="" />
		                        			<a href="<?php echo get_the_permalink().'?edit_property='.$edit_property_id.'&additional_img_attachment_id='.$additional_img_attachment_id; ?>" class="delete-additional-img right"><i class="fa fa-trash"></i> <?php esc_html_e('Delete', 'ns-real-estate'); ?></a>
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
	                        <span class="delete-additional-img right"><i class="fa fa-trash"></i> <?php esc_html_e('Delete', 'ns-real-estate'); ?></span>
	                        </div>
	                        </td>
	                        </tr>
	                    </table>
	                </div>
	                <span class="button light small add-additional-img"><i class="fa fa-plus"></i>  <?php esc_html_e('Add Image', 'ns-real-estate'); ?></span>
	            </div>
                <?php } ?>

			</div><!-- end property images -->
            <?php } ?>

            <?php if(in_array('Map', $members_submit_property_fields )) { ?>
			<div class="submit-property-section" id="map">
            	<h3><?php esc_html_e('Map', 'ns-real-estate'); ?></h3>
            	<div class="left">
                    <div class="row">
                        <div class="col-lg-6 col-md-6 form-block">
                            <input type="text" class="border" name="latitude" id="property_latitude" placeholder="<?php esc_html_e('Latitude', 'ns-real-estate'); ?>" value="<?php if(isset($latitude)) { echo $latitude; } else { echo esc_attr($_POST['latitude']); } ?>" />
                        </div>

                        <div class="col-lg-6 col-md-6 form-block">
                            <input type="text" class="border" name="longitude" id="property_longitude" placeholder="<?php esc_html_e('Longitude', 'ns-real-estate'); ?>" value="<?php if(isset($longitude)) { echo $longitude; } else { echo esc_attr($_POST['longitude']); } ?>" />
                        </div>
                    </div>
            	</div>
            	<?php include(plugin_dir_path( __DIR__ ) . 'admin_map.php'); ?>
            </div>
            <?php } ?>

            <?php if(in_array('Owner Info', $members_submit_property_fields )) { ?>
            <div class="submit-property-section" id="owner-info">
	            <h3><?php esc_html_e('Owner Info', 'ns-real-estate'); ?></h3>

	            <div class="form-block form-block-property-agent-display">
	                <label><?php esc_html_e('What do you want displayed for the agent info?', 'ns-real-estate'); ?></label><br/>
	                <input type="radio" name="agent_display" id="agent_display_author" value="author" <?php if (!empty($_POST)) { if($_POST['agent_display'] == 'author') { echo 'checked'; } } else if(!isset($_POST['agent_display'])) { echo 'checked'; }  ?> /><?php esc_html_e('Your Profile Info', 'ns-real-estate'); ?><br/>
	                <input type="radio" name="agent_display" id="agent_display_agent" value="agent" <?php if(isset($edit_agent_display)) { if($edit_agent_display == 'agent') { echo 'checked'; } } else { if($_POST['agent_display'] == 'agent') { echo 'checked'; } } ?> /><?php esc_html_e('Existing Agent', 'ns-real-estate'); ?><br/>
	                <input type="radio" name="agent_display" id="agent_display_custom" value="custom" <?php if(isset($edit_agent_display)) { if($edit_agent_display == 'custom') { echo 'checked'; } } else { if($_POST['agent_display'] == 'custom') { echo 'checked'; } } ?> /><?php esc_html_e('Custom', 'ns-real-estate'); ?><br/>
	                <input type="radio" name="agent_display" id="agent_display_none" value="none" <?php if(isset($edit_agent_display)) { if($edit_agent_display == 'none') { echo 'checked'; } } else { if($_POST['agent_display'] == 'none') { echo 'checked'; } } ?> /><?php esc_html_e('None', 'ns-real-estate'); ?><br/>
	            </div>
	            <br/>

	            <div class="form-block form-block-agent-options form-block-select-agent <?php if(isset($edit_agent_display) && $edit_agent_display == 'agent') { echo 'show'; } else { echo 'show-none'; } ?>">
	                <?php
	                    $agent_listing_args = array(
	                        'post_type' => 'ns-agent',
	                        'posts_per_page' => -1
	                    );

	                    $agent_listing_query = new WP_Query( $agent_listing_args );
	                ?>
	                <label for="agent_select"><?php esc_html_e('Select Agent', 'ns-real-estate'); ?></label><br/>
	                <div class="form-block border">
	                    <select name="agent_select">
	                        <option value="" placeholder="<?php esc_html_e('Select an option...', 'ns-real-estate'); ?>"></option>
	                        <?php if ( $agent_listing_query->have_posts() ) : while ( $agent_listing_query->have_posts() ) : $agent_listing_query->the_post(); ?>

	                        <option value="<?php echo get_the_ID(); ?>" <?php if(isset($edit_agent_select) && $edit_agent_select == get_the_ID()) { echo 'selected'; } else { if ($_POST['agent_select'] == get_the_ID()) { echo 'selected'; } } ?>><?php the_title(); ?></option>

	                    <?php endwhile; ?>
	                        </select>
	                    <?php else: ?>
	                        <option value=""><?php esc_html_e('Sorry, no agents have been posted yet.', 'ns-real-estate'); ?></option>
	                        </select>
	                    <?php endif; ?>
	                </div>
	            </div>

	            <div class="form-block form-block-agent-options form-block-custom-agent <?php if(isset($edit_agent_display) && $edit_agent_display == 'custom') { echo 'show'; } else { echo 'show-none'; } ?>">
	                <label><?php esc_html_e('Custom Owner/Agent Details', 'ns-real-estate'); ?></label>
	                <input type="text" class="border" name="agent_custom_name" placeholder="<?php esc_html_e('Name', 'ns-real-estate'); ?>" value="<?php if(isset($edit_agent_custom_name)) { echo $edit_agent_custom_name; } else { echo esc_attr($_POST['agent_custom_name']); } ?>" />
	                <input type="text" class="border" name="agent_custom_email" placeholder="<?php esc_html_e('Email', 'ns-real-estate'); ?>" value="<?php if(isset($edit_agent_custom_email)) { echo $edit_agent_custom_email; } else { echo esc_attr($_POST['agent_custom_email']); } ?>" />
	                <input type="text" class="border" name="agent_custom_phone" placeholder="<?php esc_html_e('Phone', 'ns-real-estate'); ?>" value="<?php if(isset($edit_agent_custom_phone)) { echo $edit_agent_custom_phone; } else { echo esc_attr($_POST['agent_custom_phone']); } ?>" />
	                <input type="text" class="border" name="agent_custom_url" placeholder="<?php esc_html_e('Website', 'ns-real-estate'); ?>" value="<?php if(isset($edit_agent_custom_url)) { echo esc_url($edit_agent_custom_url); } else { echo esc_url($_POST['agent_custom_url']); } ?>" />
	            </div>
	        </div><!-- end owner info -->
            <?php } ?>

	        <input type="submit" class="button alt right" value="<?php echo $form_submit_text; ?>" />
	    </form>

	</div><!-- end form container -->
	
	<?php } else {
        ns_basics_template_loader('alert_not_logged_in.php', null, false);
    } ?>
</div><!-- end submit property -->