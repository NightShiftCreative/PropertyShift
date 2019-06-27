<?php

/*-----------------------------------------------------------------------------------*/
/*  Global Property Functions
/*-----------------------------------------------------------------------------------*/

//rewrite for properties page url conflict
function ns_real_estate_properties_rewrite_rule() {
    add_rewrite_rule('^properties/page/([0-9]+)','index.php?pagename=properties&paged=$matches[1]', 'top');
}
add_action('init', 'ns_real_estate_properties_rewrite_rule');

//returns property count (supply user ID to return property count for that user)
function ns_real_estate_count_properties($type, $user_id = null) {
        $args_total_properties = array(
            'post_type' => 'ns-property',
            'showposts' => -1,
            'author_name' => $user_id,
            'post_status' => $type 
        );

        $meta_posts = get_posts( $args_total_properties );
        $meta_post_count = count( $meta_posts );
        unset( $meta_posts);
        return $meta_post_count;
}

//returns curreny options
function ns_real_estate_get_curreny_options() {
    $currency_options = array();
    $currency_options['symbol'] = esc_attr(get_option('ns_real_estate_currency_symbol', '$'));
    $currency_options['symbol_position'] = esc_attr(get_option('ns_real_estate_currency_symbol_position', 'before'));
    $currency_options['thousand'] = esc_attr(get_option('ns_real_estate_thousand_separator', ','));
    $currency_options['decimal'] = esc_attr(get_option('ns_real_estate_decimal_separator', '.'));
    $currency_options['decimal_num'] = esc_attr(get_option('ns_real_estate_num_decimal', '0'));
    $currency_options['default_area_postfix'] = esc_attr(get_option('ns_real_estate_default_area_postfix', 'Sq Ft'));
    $currency_options['thousand_area'] = esc_attr(get_option('ns_real_estate_thousand_separator_area', ','));
    $currency_options['decimal_area'] = esc_attr(get_option('ns_real_estate_decimal_separator_area', '.'));
    $currency_options['decimal_num_area'] = esc_attr(get_option('ns_real_estate_num_decimal_area', 0));
    return $currency_options;
}

// returns formatted price
function ns_real_estate_format_price($price) {
    $currency_options = ns_real_estate_get_curreny_options();
    $currency_symbol = $currency_options['symbol'];
    $currency_symbol_position = $currency_options['symbol_position'];
    $currency_thousand = $currency_options['thousand'];
    $currency_decimal = $currency_options['decimal'];
    $currency_decimal_num = $currency_options['decimal_num'];

    if(!empty($price)) { $price = number_format($price, $currency_decimal_num, $currency_decimal, $currency_thousand); }
    if($currency_symbol_position == 'before') { $price = $currency_symbol.$price; } else { $price = $price.$currency_symbol; }

    return $price;
}

// returns formatted area
function ns_real_estate_format_area($area) {
    $currency_options = ns_real_estate_get_curreny_options();
    if(!empty($area)) { $area = number_format($area, $currency_options['decimal_num_area'], $currency_options['decimal_area'], $currency_options['thousand_area']); }
    return $area;
}

//delete property custom field
function ns_real_estate_delete_custom_field() {
    $key = isset($_POST['key']) ? $_POST['key'] : '';
    delete_post_meta_by_key('ns_property_custom_field_'.$key);
    die();
}
add_action('wp_ajax_ns_real_estate_delete_custom_field', 'ns_real_estate_delete_custom_field');

/*-----------------------------------------------------------------------------------*/
/*  Get Property Details
/*-----------------------------------------------------------------------------------*/

/* get property type */
function ns_real_estate_get_property_type($post_id, $array = null) {
    $property_type = '';
    $property_type_terms = get_the_terms( $post_id, 'property_type' );
    if ( $property_type_terms && ! is_wp_error( $property_type_terms) ) : 
        $property_type_links = array();
        foreach ( $property_type_terms as $property_type_term ) {
            if($array == 'true') {
                $property_type_links[] = $property_type_term->slug;
            } else {
                $property_type_links[] = '<a href="'. esc_attr(get_term_link($property_type_term ->slug, 'property_type')) .'">'.$property_type_term ->name.'</a>' ;
            }
        }                   
        if($array == 'true') { $property_type = $property_type_links;  } else { $property_type = join( ", ", $property_type_links ); }
    endif;
    return $property_type;
}

/* get property status */
function ns_real_estate_get_property_status($post_id, $array = null) {
    $property_status = '';
    $property_status_terms = get_the_terms( $post_id, 'property_status' );
    if ( $property_status_terms && ! is_wp_error( $property_status_terms) ) : 
        $property_status_links = array();
        foreach ( $property_status_terms as $property_status_term ) {
            if($array == 'true') {
                $property_status_links[] = $property_status_term->slug;
            } else {
                $property_status_links[] = '<a href="'. esc_attr(get_term_link($property_status_term ->slug, 'property_status')) .'">'.$property_status_term ->name.'</a>' ;
            }
        }                   
        if($array == 'true') { $property_status = $property_status_links; } else { $property_status = join( ", ", $property_status_links ); }
    endif;
    return $property_status;
}

/* get property location */
function ns_real_estate_get_property_location($post_id, $output = null, $array = null) {
    $property_location = '';
    $property_location_output = '';
    $property_location_terms = get_the_terms( $post_id, 'property_location');
    if ( $property_location_terms && ! is_wp_error( $property_location_terms) ) : 
        $property_location_links = array();
        $property_location_child_links = array();
        foreach ( $property_location_terms as $property_location_term ) {
            if($property_location_term->parent != 0) {
                if($array == 'true') {
                    $property_location_child_links[] = $property_location_term->slug;
                } else {
                    $property_location_child_links[] = '<a href="'. esc_attr(get_term_link($property_location_term ->slug, 'property_location')) .'">'.$property_location_term ->name.'</a>' ;
                }
            } else {
                if($array == 'true') {
                    $property_location_links[] = $property_location_term->slug;
                } else {
                    $property_location_links[] = '<a href="'. esc_attr(get_term_link($property_location_term ->slug, 'property_location')) .'">'.$property_location_term ->name.'</a>' ;
                }
            }
        }                   
        $property_location = join( "<span>, </span>", $property_location_links );
        $property_location_children = join( "<span>, </span>", $property_location_child_links );
    endif;

    if($array == 'true') {
        if(!empty($property_location_links)) { $property_location_output = array_merge($property_location_links, $property_location_child_links); }
    } else {
        if($output == 'parent') {
            $property_location_output = $property_location;
        } else if($output == 'children') {
            $property_location_output = $property_location_children;
        } else {
            $property_location_output .= $property_location_children;
            if(!empty($property_location_children) && !empty($property_location)) { $property_location_output .= ', '; } 
            $property_location_output .= $property_location;
        }
    }
    
    return $property_location_output; 
}

/* get property full address */
function ns_real_estate_get_property_address($post_id) {
    $icon_set = esc_attr(get_option('ns_core_icon_set', 'fa'));
    if(function_exists('ns_core_load_theme_options')) { $icon_set = ns_core_load_theme_options('ns_core_icon_set'); }
    $values = get_post_custom($post_id);
    $street_address = isset( $values['ns_property_address'] ) ? esc_attr( $values['ns_property_address'][0] ) : '';
    $property_address = '';
    $property_location = ns_real_estate_get_property_location($post_id);
    if(!empty($street_address) || !empty($property_location)) { $property_address .= ns_core_get_icon($icon_set, 'map-marker', 'map-marker', 'location'); }
    if(!empty($street_address)) { $property_address .= $street_address; }
    if(!empty($street_address) && !empty($property_location)) { $property_address .= ', '; }
    if(!empty($property_location)) { $property_address .= $property_location; }
    return $property_address;
}

/* get property amenities */
function ns_real_estate_get_property_amenities($post_id, $hide_empty = true, $array = null) {
    $property_amenities = '';
    $property_amenities_links = array();

    if($hide_empty == false) {
        $property_amenities_terms =  get_terms(['taxonomy' => 'property_amenities', 'hide_empty' => false,]);
    } else {
        $property_amenities_terms = get_the_terms( $post_id, 'property_amenities' );
    }

    if ( $property_amenities_terms && ! is_wp_error( $property_amenities_terms) ) : 
        foreach ( $property_amenities_terms as $property_amenity_term ) {
            if($array == 'true') {
                $property_amenities_links[] = $property_amenity_term->slug;
            } else {
                if(has_term($property_amenity_term->slug, 'property_amenities', $post_id)) { $icon = '<i class="fa fa-check icon"></i>'; } else { $icon = '<i class="fa fa-times icon"></i>'; }
                $property_amenities_links[] = '<li><a href="'. esc_attr(get_term_link($property_amenity_term->slug, 'property_amenities')) .'">'.$icon.'<span>'.$property_amenity_term->name.'</span></a></li>' ;
            }
        } 
    endif;

    if($array == 'true') { 
        $property_amenities = $property_amenities_links;
    } else { 
        $property_amenities = join( '', $property_amenities_links ); 
        if(!empty($property_amenities)) { $property_amenities = '<ul class="amenities-list clean-list">'.$property_amenities.'</ul>'; }
    }

    return $property_amenities;
}

/* get property walkscore */
function getWalkScore($lat, $lon, $address) {
    $address=urlencode($address);
    $url = "http://api.walkscore.com/score?format=json&address=$address";
    $url .= "&lat=$lat&lon=$lon&wsapikey=f6c3f50b09a7ce69d6d276015e57e996";
    $request = wp_remote_get($url);
    $str = wp_remote_retrieve_body($request);
    return $str; 
} 


/*-----------------------------------------------------------------------------------*/
/*  Properties Custom Post Type
/*-----------------------------------------------------------------------------------*/
add_action( 'init', 'ns_real_estate_create_properties_post_type' );
function ns_real_estate_create_properties_post_type() {
    $properties_slug = get_option('ns_property_detail_slug', 'properties');
    register_post_type( 'ns-property',
        array(
            'labels' => array(
                'name' => __( 'Properties', 'ns-real-estate' ),
                'singular_name' => __( 'Property', 'ns-real-estate' ),
                'add_new_item' => __( 'Add New Property', 'ns-real-estate' ),
                'search_items' => __( 'Search Properties', 'ns-real-estate' ),
                'edit_item' => __( 'Edit Property', 'ns-real-estate' ),
            ),
        'public' => true,
        'show_in_menu' => true,
        'menu_icon' => 'dashicons-admin-home',
        'has_archive' => false,
        'supports' => array('title', 'author', 'editor', 'revisions', 'thumbnail', 'page_attributes'),
        'rewrite' => array('slug' => $properties_slug),
        )
    );
}

 /* Add property details (meta box) */ 
 function ns_real_estate_add_meta_box() {
    add_meta_box( 'property-details-meta-box', 'Property Details', 'ns_real_estate_property_details', 'ns-property', 'normal', 'high' );
 }
add_action( 'add_meta_boxes', 'ns_real_estate_add_meta_box' );

/* Ouput property details form */
function ns_real_estate_property_details($post) {

    $values = get_post_custom( $post->ID );
    $featured = isset( $values['ns_property_featured'] ) ? esc_attr( $values['ns_property_featured'][0] ) : 'false';
    $address = isset( $values['ns_property_address'] ) ? esc_attr( $values['ns_property_address'][0] ) : '';
    $price = isset( $values['ns_property_price'] ) ? esc_attr( $values['ns_property_price'][0] ) : '';
    $price_postfix = isset( $values['ns_property_price_postfix'] ) ? esc_attr( $values['ns_property_price_postfix'][0] ) : '';
    $bedrooms = isset( $values['ns_property_bedrooms'] ) ? esc_attr( $values['ns_property_bedrooms'][0] ) : '';
    $bathrooms = isset( $values['ns_property_bathrooms'] ) ? esc_attr( $values['ns_property_bathrooms'][0] ) : '';
    $garages = isset( $values['ns_property_garages'] ) ? esc_attr( $values['ns_property_garages'][0] ) : '';
    $area = isset( $values['ns_property_area'] ) ? esc_attr( $values['ns_property_area'][0] ) : '';
    $area_postfix_default = esc_attr(get_option('ns_property_default_area_postfix', 'Sq Ft'));
    $area_postfix = isset( $values['ns_property_area_postfix'] ) ? esc_attr( $values['ns_property_area_postfix'][0] ) : $area_postfix_default;
    $description = isset( $values['ns_property_description'] ) ? $values['ns_property_description'][0] : '';
    $additional_images = isset($values['ns_additional_img']) ? $values['ns_additional_img'] : '';
    $floor_plans = isset($values['ns_property_floor_plans']) ? $values['ns_property_floor_plans'] : '';
    $latitude = isset( $values['ns_property_latitude'] ) ? esc_attr( $values['ns_property_latitude'][0] ) : '';
    $longitude = isset( $values['ns_property_longitude'] ) ? esc_attr( $values['ns_property_longitude'][0] ) : '';
    $video_url = isset( $values['ns_property_video_url'] ) ? esc_attr( $values['ns_property_video_url'][0] ) : '';
    $video_img = isset( $values['ns_property_video_img'] ) ? esc_attr( $values['ns_property_video_img'][0] ) : '';
    $agent_display = isset( $values['ns_agent_display'] ) ? esc_attr( $values['ns_agent_display'][0] ) : 'none';
    $agent_select = isset( $values['ns_agent_select'] ) ? esc_attr( $values['ns_agent_select'][0] ) : '';
    $agent_custom_name = isset( $values['ns_agent_custom_name'] ) ? esc_attr( $values['ns_agent_custom_name'][0] ) : '';
    $agent_custom_email = isset( $values['ns_agent_custom_email'] ) ? esc_attr( $values['ns_agent_custom_email'][0] ) : '';
    $agent_custom_phone = isset( $values['ns_agent_custom_phone'] ) ? esc_attr( $values['ns_agent_custom_phone'][0] ) : '';
    $agent_custom_url = isset( $values['ns_agent_custom_url'] ) ? esc_attr( $values['ns_agent_custom_url'][0] ) : '';
    wp_nonce_field( 'ns_property_details_meta_box_nonce', 'ns_property_details_meta_box_nonce' );
    ?>

    <div class="ns-tabs meta-box-form meta-box-form-property-details">
        <ul class="ns-tabs-nav">
            <li><a href="#general" title="<?php esc_html_e('General Info', 'ns-real-estate'); ?>"><i class="fa fa-home"></i> <span class="tab-text"><?php echo esc_html_e('General Info', 'ns-real-estate'); ?></span></a></li>
            <li><a href="#description" title="<?php esc_html_e('Description', 'ns-real-estate'); ?>"><i class="fa fa-pencil-alt"></i> <span class="tab-text"><?php echo esc_html_e('Description', 'ns-real-estate'); ?></span></a></li>
            <li><a href="#gallery" title="<?php esc_html_e('Gallery', 'ns-real-estate'); ?>"><i class="fa fa-image"></i> <span class="tab-text"><?php echo esc_html_e('Gallery', 'ns-real-estate'); ?></span></a></li>
            <li><a href="#floor-plans" title="<?php esc_html_e('Floor Plans', 'ns-real-estate'); ?>"><i class="fa fa-th-large"></i> <span class="tab-text"><?php echo esc_html_e('Floor Plans', 'ns-real-estate'); ?></span></a></li>
            <li><a href="#map" title="<?php esc_html_e('Map', 'ns-real-estate'); ?>" onclick="refreshMap()"><i class="fa fa-map"></i> <span class="tab-text"><?php echo esc_html_e('Map', 'ns-real-estate'); ?></span></a></li>
            <li><a href="#video" title="<?php esc_html_e('Video', 'ns-real-estate'); ?>"><i class="fa fa-video"></i> <span class="tab-text"><?php echo esc_html_e('Video', 'ns-real-estate'); ?></span></a></li>
            <li><a href="#agent" title="<?php esc_html_e('Owner Info', 'ns-real-estate'); ?>"><i class="fa fa-user"></i> <span class="tab-text"><?php echo esc_html_e('Owner Info', 'ns-real-estate'); ?></span></a></li>
            <?php do_action('ns_real_estate_after_property_tabs'); ?>
        </ul>

        <div class="ns-tabs-content">
        <div class="tab-loader"><img src="<?php echo esc_url(home_url('/')); ?>wp-admin/images/spinner.gif" alt="" /> <?php echo esc_html_e('Loading...', 'ns-real-estate'); ?></div>

        <!--*************************************************-->
        <!-- GENERAL INFO -->
        <!--*************************************************-->
        <div id="general" class="tab-content">
            <h3><?php echo esc_html_e('General Info', 'ns-real-estate'); ?></h3>

            <table class="admin-module">
                <tr>
                    <td class="admin-module-label"><label><?php echo esc_html_e('Property ID', 'ns-real-estate'); ?></label></td>
                    <td class="admin-module-field"><?php echo get_the_id(); ?></td>
                 </tr>
            </table>

            <table class="admin-module">
                <tr>
                    <td class="admin-module-label"><label><?php echo esc_html_e('Featured Property', 'ns-real-estate'); ?></label></td>
                    <td class="admin-module-field"><input type="checkbox" id="property_featured" name="ns_property_featured" value="true" <?php if($featured == 'true') { echo 'checked'; } ?> /></td>
                 </tr>
            </table>

            <table class="admin-module">
                <tr>
                    <td class="admin-module-label">
                        <label><?php echo esc_html_e('Street Address', 'ns-real-estate'); ?></label>
                        <span class="admin-module-note"><?php echo esc_html_e('Provide the address for the property', 'ns-real-estate'); ?></span>
                    </td>
                    <td class="admin-module-field"><input type="text" name="ns_property_address" id="property_address" value="<?php echo $address; ?>" /></td>
                 </tr>
            </table>

            <table class="admin-module">
                <tr>
                    <td class="admin-module-label">
                        <label><?php echo esc_html_e('Price', 'ns-real-estate'); ?></label>
                        <span class="admin-module-note"><?php echo esc_html_e('Use only numbers. Do not include commas or dollar sign (ex.- 250000)', 'ns-real-estate'); ?></span>
                    </td>
                    <td class="admin-module-field"><input type="number" min="0" name="ns_property_price" value="<?php echo $price; ?>" /></td>
                 </tr>
            </table>

            <table class="admin-module">
                <tr>
                    <td class="admin-module-label">
                        <label><?php echo esc_html_e('Price Postfix', 'ns-real-estate'); ?></label>
                        <span class="admin-module-note"><?php echo esc_html_e('Provide the text displayed after the price (ex.- Per Month)', 'ns-real-estate'); ?></span>
                    </td>
                    <td class="admin-module-field"><input type="text" name="ns_property_price_postfix" value="<?php echo $price_postfix; ?>" /></td>
                 </tr>
            </table>

            <table class="admin-module">
                <tr>
                    <td class="admin-module-label">
                        <label><?php echo esc_html_e('Bedrooms', 'ns-real-estate'); ?></label>
                        <span class="admin-module-note"><?php echo esc_html_e('Provide the number of bedrooms', 'ns-real-estate'); ?></span>
                    </td>
                    <td class="admin-module-field"><input type="number" min="0" name="ns_property_bedrooms" value="<?php echo $bedrooms; ?>" /></td>
                 </tr>
            </table>

            <table class="admin-module">
                <tr>
                    <td class="admin-module-label">
                        <label><?php echo esc_html_e('Bathrooms', 'ns-real-estate'); ?></label>
                        <span class="admin-module-note"><?php echo esc_html_e('Provide the number of bathrooms', 'ns-real-estate'); ?></span>
                    </td>
                    <td class="admin-module-field"><input type="number" min="0" step="0.5" name="ns_property_bathrooms" value="<?php echo $bathrooms; ?>" /></td>
                 </tr>
            </table>

            <table class="admin-module">
                <tr>
                    <td class="admin-module-label">
                        <label><?php echo esc_html_e('Garages', 'ns-real-estate'); ?></label>
                        <span class="admin-module-note"><?php echo esc_html_e('Provide the number of garages', 'ns-real-estate'); ?></span>
                    </td>
                    <td class="admin-module-field"><input type="number" min="0" name="ns_property_garages" value="<?php echo $garages; ?>" /></td>
                 </tr>
            </table>

            <table class="admin-module">
                <tr>
                    <td class="admin-module-label">
                        <label><?php echo esc_html_e('Area', 'ns-real-estate'); ?></label>
                        <span class="admin-module-note"><?php echo esc_html_e('Provide the area. Use only numbers and decimals, do not include commas.', 'ns-real-estate'); ?></span>
                    </td>
                    <td class="admin-module-field"><input type="number" min="0" step="0.01" name="ns_property_area" value="<?php echo $area; ?>" /></td>
                 </tr>
            </table>

            <table class="admin-module">
                <tr>
                    <td class="admin-module-label">
                        <label><?php echo esc_html_e('Area Postfix', 'ns-real-estate'); ?></label>
                        <span class="admin-module-note"><?php echo esc_html_e('Provide the text to display directly after the area (ex. - Sq Ft)', 'ns-real-estate'); ?></span>
                    </td>
                    <td class="admin-module-field"><input type="text" name="ns_property_area_postfix" value="<?php echo $area_postfix; ?>" /></td>
                 </tr>
            </table>

            <div class="admin-module admin-module-custom-fields admin-module-custom-fields-property no-border">
                <label><strong><?php echo esc_html_e('Custom Fields', 'ns-real-estate'); ?></strong></label><br/>
                <?php 
                    $custom_fields = get_option('ns_property_custom_fields');
                    if(!empty($custom_fields)) { 
                        $count = 0;
                        echo '<div class="custom-fields-container">';                    
                        foreach ($custom_fields as $custom_field) { 
                            if(!is_array($custom_field)) { 
                                $custom_field = array( 
                                    'id' => strtolower(str_replace(' ', '_', $custom_field)),
                                    'name' => $custom_field, 
                                    'type' => 'text',
                                ); 
                            } ?>
                            <table class="custom-field-item">
                                <tr>
                                    <td>   
                                        <label><?php echo $custom_field['name']; ?>:</label> 
                                        <?php if(isset($custom_field['type']) && $custom_field['type'] == 'select') { ?>
                                            <select name="ns_property_custom_fields[<?php echo $count; ?>][value]">
                                                <option value=""><?php esc_html_e('Select an option...', 'ns-real-estate'); ?></option>
                                                <?php 
                                                    if(isset($custom_field['select_options'])) { $selectOptions = $custom_field['select_options']; } else { $selectOptions =  ''; }
                                                    if(!empty($selectOptions)) {
                                                        foreach($selectOptions as $option) { ?>
                                                            <option value="<?php echo $option; ?>" <?php if(get_post_meta($post->ID, 'ns_property_custom_field_'.$custom_field['id'], true) == $option) { echo 'selected'; } ?>><?php echo $option; ?></option>
                                                        <?php }
                                                    }
                                                ?>
                                            </select>
                                        <?php } else { ?>
                                            <input type="<?php if(isset($custom_field['type']) && $custom_field['type'] == 'num') { echo 'number'; } else { echo 'text'; } ?>" name="ns_property_custom_fields[<?php echo $count; ?>][value]" value="<?php echo get_post_meta($post->ID, 'ns_property_custom_field_'.$custom_field['id'], true); ?>" />
                                        <?php } ?>
                                        <input type="hidden" name="ns_property_custom_fields[<?php echo $count; ?>][key]" value="ns_property_custom_field_<?php echo $custom_field['id']; ?>" />
                                    </td>
                                </tr>
                            </table>
                        <?php $count++; }
                        echo '</div>';
                    } else { ?>
                        <span class="admin-module-note"><?php esc_html_e('No custom fields have been created.', 'ns-real-estate'); ?></span>
                    <?php }
                ?>
                <span class="admin-module-note"><a href="<?php echo admin_url('admin.php?page=ns-real-estate-settings#properties&custom-fields'); ?>" target="_blank"><i class="fa fa-cog"></i> <?php esc_html_e('Manage custom fields', 'ns-real-estate'); ?></a></span>
            </div>

        </div>

        <!--*************************************************-->
        <!-- DESCRIPTION -->
        <!--*************************************************-->
        <div id="description" class="tab-content">
            <h3><?php echo esc_html_e('Description', 'ns-real-estate'); ?></h3>
            <?php 
            $editor_id = 'propertydescription';
            $settings = array('textarea_name' => 'ns_property_description', 'editor_height' => 180);
            wp_editor( $description, $editor_id, $settings);
            ?>
        </div>

        <!--*************************************************-->
        <!-- GALLERY -->
        <!--*************************************************-->
        <div id="gallery" class="tab-content">
            <h3><?php echo esc_html_e('Gallery', 'ns-real-estate'); ?></h3>
            <?php if(function_exists('ns_basics_generate_gallery')) { echo ns_basics_generate_gallery($additional_images); } ?>
        </div>
        
        <!--*************************************************-->
        <!-- FLOOR PLANS -->
        <!--*************************************************-->
        <div id="floor-plans" class="tab-content">
            <h3><?php echo esc_html_e('Floor Plans', 'ns-real-estate'); ?></h3>
            <div class="admin-module admin-module-floor-plans admin-module-repeater no-padding no-border">  
                
                <div class="repeater-container">
                <?php 
                if(!empty($floor_plans) && !empty($floor_plans[0])) {  
                    $floor_plans = unserialize($floor_plans[0]); 
                    $count = 0;                     
                    foreach ($floor_plans as $floor_plan) { ?>
                        <div class="ns-accordion">
                            <div class="ns-accordion-header"><i class="fa fa-chevron-right"></i> <span class="repeater-title-mirror floor-plan-title-mirror"><?php echo $floor_plan['title']; ?></span> <span class="action delete delete-floor-plan"><i class="fa fa-trash"></i> Delete</span></div>
                            <div class="ns-accordion-content floor-plan-item"> 
                                <div class="floor-plan-left"> 
                                    <label><?php esc_html_e('Title:', 'ns-real-estate'); ?> </label> <input class="repeater-title floor-plan-title" type="text" name="ns_property_floor_plans[<?php echo $count; ?>][title]" placeholder="New Floor Plan" value="<?php echo $floor_plan['title']; ?>" /><br/>
                                    <label><?php esc_html_e('Size:', 'ns-real-estate'); ?> </label> <input type="text" name="ns_property_floor_plans[<?php echo $count; ?>][size]" value="<?php echo $floor_plan['size']; ?>" /><br/>
                                    <label><?php esc_html_e('Rooms:', 'ns-real-estate'); ?> </label> <input type="number" name="ns_property_floor_plans[<?php echo $count; ?>][rooms]" value="<?php echo $floor_plan['rooms']; ?>" /><br/>
                                    <label><?php esc_html_e('Bathrooms:', 'ns-real-estate'); ?> </label> <input type="number" name="ns_property_floor_plans[<?php echo $count; ?>][baths]" value="<?php echo $floor_plan['baths']; ?>" /><br/>
                                </div>
                                <div class="floor-plan-right">
                                    <label><?php esc_html_e('Description:', 'ns-real-estate'); ?></label>
                                    <textarea name="ns_property_floor_plans[<?php echo $count; ?>][description]"><?php echo $floor_plan['description']; ?></textarea>
                                    <div class="floor-plan-img">
                                        <label><?php esc_html_e('Image:', 'ns-real-estate'); ?> </label> 
                                        <input type="text" name="ns_property_floor_plans[<?php echo $count; ?>][img]" value="<?php echo $floor_plan['img']; ?>" />
                                        <input id="_btn" class="ns_upload_image_button" type="button" value="<?php esc_html_e('Upload Image', 'ns-real-estate'); ?>" />
                                        <span class="button-secondary remove"><?php esc_html_e('Remove', 'ns-real-estate'); ?></span>
                                    </div>
                                </div>
                                <div class="clear"></div>
                            </div>
                        </div> 
                        <?php $count++; ?>
                    <?php }
                } ?>
                </div>
                
                <?php if(empty($floor_plans) && empty($floor_plans[0])) { echo '<p class="admin-module-note no-floor-plan">'.esc_html__('No floor plans were found.', 'ns-real-estate').'</p>'; } ?>
                <span class="admin-button add-repeater"><i class="fa fa-plus"></i> <?php esc_html_e('Create New Floor Plan', 'ns-real-estate'); ?></span>
            </div>
        </div>

        <!--*************************************************-->
        <!-- MAP -->
        <!--*************************************************-->
        <div id="map" class="tab-content">
            <h3><?php echo esc_html_e('Map', 'ns-real-estate'); ?></h3>

            <div class="admin-module admin-module-company-location no-border">

                <table class="admin-module no-border">
                    <tr>
                        <td class="admin-module-label"><label><?php echo esc_html_e('Latitude', 'ns-real-estate'); ?></label></td>
                        <td class="admin-module-field"><input type="text" name="ns_property_latitude" id="property_latitude" value="<?php echo $latitude; ?>" /></td>
                     </tr>
                </table>

                <table class="admin-module no-border">
                    <tr>
                        <td class="admin-module-label"><label><?php echo esc_html_e('Longitude', 'ns-real-estate'); ?></label></td>
                        <td class="admin-module-field"><input type="text" name="ns_property_longitude" id="property_longitude" value="<?php echo $longitude; ?>" /></td>
                     </tr>
                </table>

                <?php include(plugin_dir_path( __FILE__ ) . 'admin_map.php'); ?>
            </div>
        </div>

        <!--*************************************************-->
        <!-- VIDEO -->
        <!--*************************************************-->
        <div id="video" class="tab-content">
            <h3><?php echo esc_html_e('Video', 'ns-real-estate'); ?></h3>

            <table class="admin-module">
                <tr>
                    <td class="admin-module-label"><label><?php echo esc_html_e('Video URL', 'ns-real-estate'); ?></label></td>
                    <td class="admin-module-field"><input type="text" name="ns_property_video_url" id="property_video_url" value="<?php echo $video_url; ?>" /></td>
                </tr>
            </table>

            <table class="admin-module admin-module-upload no-border">
                <tr>
                    <td class="admin-module-label"><label><?php echo esc_html_e('Video Cover Image', 'ns-real-estate'); ?></label></td>
                    <td class="admin-module-field">
                        <input type="text" id="property_video_img" name="ns_property_video_img" value="<?php echo $video_img; ?>" />
                        <input id="_btn" class="ns_upload_image_button" type="button" value="<?php echo esc_html_e('Upload Image', 'ns-real-estate'); ?>" />
                        <span class="button-secondary remove"><?php echo esc_html_e('Remove', 'ns-real-estate'); ?></span>
                    </td>
                </tr>
            </table>
        </div>

        <!--*************************************************-->
        <!-- OWNER INFO -->
        <!--*************************************************-->
        <div id="agent" class="tab-content">
            <h3><?php echo esc_html_e('Owner Info', 'ns-real-estate'); ?></h3>

            <table class="admin-module">
                <tr>
                    <td class="admin-module-label"><label><?php echo esc_html_e('What to display for owner information?', 'ns-real-estate'); ?></label></td>
                    <td class="admin-module-field">
                        <p><input type="radio" name="ns_agent_display" id="agent_display_none" value="none" <?php if($agent_display == 'none') { echo 'checked="checked"'; } ?> /><?php echo esc_html_e('None', 'ns-real-estate'); ?></p>
                        <p><input type="radio" name="ns_agent_display" id="agent_display_author" value="author" <?php if($agent_display == 'author') { echo 'checked="checked"'; } ?> /><?php echo esc_html_e('Author Info', 'ns-real-estate'); ?></p>
                        <p><input type="radio" name="ns_agent_display" id="agent_display_agent" value="agent" <?php if($agent_display == 'agent') { echo 'checked="checked"'; } ?> /><?php echo esc_html_e('Agent Info (select agent below)', 'ns-real-estate'); ?></p>
                        <p><input type="radio" name="ns_agent_display" id="agent_display_custom" value="custom" <?php if($agent_display == 'custom') { echo 'checked="checked"'; } ?> /><?php echo esc_html_e('Custom Info (fill out details below)', 'ns-real-estate'); ?></p>
                    </td>
                </tr>
            </table>

            <table class="admin-module">
                <?php
                    $agent_listing_args = array(
                        'post_type' => 'ns-agent',
                        'posts_per_page' => -1
                        );
                    $agent_listing_query = new WP_Query( $agent_listing_args );
                ?>
                <tr>
                    <td class="admin-module-label"><label><?php echo esc_html_e('Select Agent', 'ns-real-estate'); ?></label></td>
                    <td class="admin-module-field">
                        <select name="ns_agent_select">
                         <option value=""></option>
                            <?php if ( $agent_listing_query->have_posts() ) : while ( $agent_listing_query->have_posts() ) : $agent_listing_query->the_post(); ?>

                             <option value="<?php echo get_the_ID(); ?>" <?php if ($agent_select == get_the_ID()) { echo 'selected'; } ?>><?php the_title(); ?></option>
                             <?php wp_reset_postdata(); ?>

                            <?php endwhile; ?>
                            <?php else: ?>
                            <?php endif; ?>
                        </select>
                    </td>
                </tr>
            </table>

            <table class="admin-module no-border">
                <tr>
                    <td class="admin-module-label"><label><?php echo esc_html_e('Custom Owner/Agent Details', 'ns-real-estate'); ?></label></td>
                    <td class="admin-module-field">
                        <p><label><?php echo esc_html_e('Name:', 'ns-real-estate'); ?></label><input type="text" name="ns_agent_custom_name" value="<?php echo $agent_custom_name; ?>" /></p>
                        <p><label><?php echo esc_html_e('Email:', 'ns-real-estate'); ?></label><input type="text" name="ns_agent_custom_email" value="<?php echo $agent_custom_email; ?>" /></p>
                        <p><label><?php echo esc_html_e('Phone:', 'ns-real-estate'); ?></label><input type="text" name="ns_agent_custom_phone" value="<?php echo $agent_custom_phone; ?>" /></p>
                        <p><label><?php echo esc_html_e('Website:', 'ns-real-estate'); ?></label><input type="text" name="ns_agent_custom_url" value="<?php echo $agent_custom_url; ?>" /></p>
                    </td>
                </tr>
            </table>

        </div>

        <!--*************************************************-->
        <!-- ADD-ONS -->
        <!--*************************************************-->
        <?php do_action('ns_real_estate_after_property_tab_content', $values); ?>

        </div><!-- end tab content -->
        <div class="clear"></div>
    </div>

<?php
}

/* Save property details form */
add_action( 'save_post', 'ns_real_estate_save_meta_box' );
function ns_real_estate_save_meta_box( $post_id ) {

    // Bail if we're doing an auto save
    if( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) return;

    // if our nonce isn't there, or we can't verify it, bail
    if( !isset( $_POST['ns_property_details_meta_box_nonce'] ) || !wp_verify_nonce( $_POST['ns_property_details_meta_box_nonce'], 'ns_property_details_meta_box_nonce' ) ) return;

    // if our current user can't edit this post, bail
    if( !current_user_can( 'edit_post', $post_id ) ) return;

    // save the data
    $allowed = array(
        'a' => array( // on allow a tags
            'href' => array() // and those anchors can only have href attribute
        )
    );

    if( isset( $_POST['ns_property_featured'] ) ) {
        update_post_meta( $post_id, 'ns_property_featured', wp_kses( $_POST['ns_property_featured'], $allowed ) );
    } else {
        update_post_meta( $post_id, 'ns_property_featured', wp_kses( '', $allowed ) );
    }

    if( isset( $_POST['ns_property_address'] ) )
        update_post_meta( $post_id, 'ns_property_address', wp_kses( $_POST['ns_property_address'], $allowed ) );
    
    if( isset( $_POST['ns_property_price'] ) )
        update_post_meta( $post_id, 'ns_property_price', wp_kses( $_POST['ns_property_price'], $allowed ) );

    if( isset( $_POST['ns_property_price_postfix'] ) )
        update_post_meta( $post_id, 'ns_property_price_postfix', wp_kses( $_POST['ns_property_price_postfix'], $allowed ) );
        
    if( isset( $_POST['ns_property_bedrooms'] ) )
        update_post_meta( $post_id, 'ns_property_bedrooms', wp_kses( $_POST['ns_property_bedrooms'], $allowed ) );
        
    if( isset( $_POST['ns_property_bathrooms'] ) )
        update_post_meta( $post_id, 'ns_property_bathrooms', wp_kses( $_POST['ns_property_bathrooms'], $allowed ) );
        
    if( isset( $_POST['ns_property_garages'] ) )
        update_post_meta( $post_id, 'ns_property_garages', wp_kses( $_POST['ns_property_garages'], $allowed ) );
        
    if( isset( $_POST['ns_property_area'] ) )
        update_post_meta( $post_id, 'ns_property_area', wp_kses( $_POST['ns_property_area'], $allowed ) );
        
    if( isset( $_POST['ns_property_area_postfix'] ) )
        update_post_meta( $post_id, 'ns_property_area_postfix', wp_kses( $_POST['ns_property_area_postfix'], $allowed ) );

    if (isset( $_POST['ns_property_custom_fields'] )) {
        $property_custom_fields = $_POST['ns_property_custom_fields'];
        foreach($property_custom_fields as $custom_field) {
            update_post_meta( $post_id, $custom_field['key'], $custom_field['value'] );
        }
    }

    if( isset( $_POST['ns_property_description'] ) )
        update_post_meta( $post_id, 'ns_property_description', wp_kses_post($_POST['ns_property_description']) );

    if (isset( $_POST['ns_additional_img'] )) {
        $strAdditionalImgs = implode(",", $_POST['ns_additional_img']);
        update_post_meta( $post_id, 'ns_additional_img', $strAdditionalImgs );
    } else {
        $strAdditionalImgs = '';
        update_post_meta( $post_id, 'ns_additional_img', $strAdditionalImgs );
    }

    if (isset( $_POST['ns_property_floor_plans'] )) {
        update_post_meta( $post_id, 'ns_property_floor_plans', $_POST['ns_property_floor_plans'] );
    } else {
        update_post_meta( $post_id, 'ns_property_floor_plans', '' );
    }
    
    if( isset( $_POST['ns_property_latitude'] ) )
        update_post_meta( $post_id, 'ns_property_latitude', wp_kses( $_POST['ns_property_latitude'], $allowed ) );

    if( isset( $_POST['ns_property_longitude'] ) )
        update_post_meta( $post_id, 'ns_property_longitude', wp_kses( $_POST['ns_property_longitude'], $allowed ) );

    if( isset( $_POST['ns_property_video_url'] ) )
        update_post_meta( $post_id, 'ns_property_video_url', $_POST['ns_property_video_url']);

    if( isset( $_POST['ns_property_video_img'] ) )
        update_post_meta( $post_id, 'ns_property_video_img', wp_kses( $_POST['ns_property_video_img'], $allowed ) );

    if( isset( $_POST['ns_agent_display'] ) )
        update_post_meta( $post_id, 'ns_agent_display', wp_kses( $_POST['ns_agent_display'], $allowed ) );

    if( isset( $_POST['ns_agent_select'] ) )
        update_post_meta( $post_id, 'ns_agent_select', wp_kses( $_POST['ns_agent_select'], $allowed ) );

    if( isset( $_POST['ns_agent_custom_name'] ) )
        update_post_meta( $post_id, 'ns_agent_custom_name', wp_kses( $_POST['ns_agent_custom_name'], $allowed ) );

    if( isset( $_POST['ns_agent_custom_email'] ) )
        update_post_meta( $post_id, 'ns_agent_custom_email', wp_kses( $_POST['ns_agent_custom_email'], $allowed ) );

    if( isset( $_POST['ns_agent_custom_phone'] ) )
        update_post_meta( $post_id, 'ns_agent_custom_phone', wp_kses( $_POST['ns_agent_custom_phone'], $allowed ) );

    if( isset( $_POST['ns_agent_custom_url'] ) )
        update_post_meta( $post_id, 'ns_agent_custom_url', wp_kses( $_POST['ns_agent_custom_url'], $allowed ) );

    //hook in for other add-ons
    do_action('ns_real_estate_save_property_details', $post_id);
}

/*-----------------------------------------------------------------------------------*/
/*  Register Custom Taxonomies
/*-----------------------------------------------------------------------------------*/
function ns_real_estate_property_type_init() {
    $property_type_tax_slug = get_option('ns_property_type_tax_slug', 'property-type');
    $labels = array(
    'name'                          => __( 'Property Type', 'ns-real-estate' ),
    'singular_name'                 => __( 'Property Type', 'ns-real-estate' ),
    'search_items'                  => __( 'Search Property Types', 'ns-real-estate' ),
    'popular_items'                 => __( 'Popular Property Types', 'ns-real-estate' ),
    'all_items'                     => __( 'All Property Types', 'ns-real-estate' ),
    'parent_item'                   => __( 'Parent Property Type', 'ns-real-estate' ),
    'edit_item'                     => __( 'Edit Property Type', 'ns-real-estate' ),
    'update_item'                   => __( 'Update Property Type', 'ns-real-estate' ),
    'add_new_item'                  => __( 'Add New Property Type', 'ns-real-estate' ),
    'new_item_name'                 => __( 'New Property Type', 'ns-real-estate' ),
    'separate_items_with_commas'    => __( 'Separate property types with commas', 'ns-real-estate' ),
    'add_or_remove_items'           => __( 'Add or remove property types', 'ns-real-estate' ),
    'choose_from_most_used'         => __( 'Choose from most used property types', 'ns-real-estate' )
    );
    
    register_taxonomy(
        'property_type',
        'ns-property',
        array(
            'label'         => __( 'Property Types', 'ns-real-estate' ),
            'labels'        => $labels,
            'hierarchical'  => true,
            'rewrite' => array( 'slug' => $property_type_tax_slug )
        )
    );
}
add_action( 'init', 'ns_real_estate_property_type_init' );

function ns_real_estate_property_status_init() {
    $property_status_tax_slug = get_option('ns_property_status_tax_slug', 'property-status');
    $labels = array(
    'name'                          => __( 'Property Status', 'ns-real-estate' ),
    'singular_name'                 => __( 'Property Status', 'ns-real-estate' ),
    'search_items'                  => __( 'Search Property Statuses', 'ns-real-estate' ),
    'popular_items'                 => __( 'Popular Property Statuses', 'ns-real-estate' ),
    'all_items'                     => __( 'All Property Statuses', 'ns-real-estate' ),
    'parent_item'                   => __( 'Parent Property Status', 'ns-real-estate' ),
    'edit_item'                     => __( 'Edit Property Status', 'ns-real-estate' ),
    'update_item'                   => __( 'Update Property Status', 'ns-real-estate' ),
    'add_new_item'                  => __( 'Add New Property Status', 'ns-real-estate' ),
    'new_item_name'                 => __( 'New Property Status', 'ns-real-estate' ),
    'separate_items_with_commas'    => __( 'Separate property statuses with commas', 'ns-real-estate' ),
    'add_or_remove_items'           => __( 'Add or remove property statuses', 'ns-real-estate' ),
    'choose_from_most_used'         => __( 'Choose from most used property statuses', 'ns-real-estate' )
    );
    
    register_taxonomy(
        'property_status',
        'ns-property',
        array(
            'label'         => __( 'Property Status', 'ns-real-estate' ),
            'labels'        => $labels,
            'hierarchical'  => true,
            'rewrite' => array( 'slug' => $property_status_tax_slug )
        )
    );
}
add_action( 'init', 'ns_real_estate_property_status_init' );

function ns_real_estate_property_location_init() {
    $property_location_tax_slug = get_option('ns_property_location_tax_slug', 'property-location');
    $labels = array(
    'name'                          => __( 'Property Location', 'ns-real-estate' ),
    'singular_name'                 => __( 'Property Location', 'ns-real-estate' ),
    'search_items'                  => __( 'Search Property Locations', 'ns-real-estate' ),
    'popular_items'                 => __( 'Popular Property Locations', 'ns-real-estate' ),
    'all_items'                     => __( 'All Property Locations', 'ns-real-estate' ),
    'parent_item'                   => __( 'Parent Property Location', 'ns-real-estate' ),
    'edit_item'                     => __( 'Edit Property Location', 'ns-real-estate' ),
    'update_item'                   => __( 'Update Property Location', 'ns-real-estate' ),
    'add_new_item'                  => __( 'Add New Property Location', 'ns-real-estate' ),
    'new_item_name'                 => __( 'New Property Location', 'ns-real-estate' ),
    'separate_items_with_commas'    => __( 'Separate property locations with commas', 'ns-real-estate' ),
    'add_or_remove_items'           => __( 'Add or remove property locations', 'ns-real-estate' ),
    'choose_from_most_used'         => __( 'Choose from most used property locations', 'ns-real-estate' )
    );
    
    register_taxonomy(
        'property_location',
        'ns-property',
        array(
            'label'         => __( 'Property Location', 'ns-real-estate' ),
            'labels'        => $labels,
            'hierarchical'  => true,
            'rewrite' => array( 'slug' => $property_location_tax_slug )
        )
    );
}
add_action( 'init', 'ns_real_estate_property_location_init' );

function ns_real_estate_property_amenities_init() {
    $property_amenities_tax_slug = get_option('ns_property_amenities_tax_slug', 'property-amenity');
    $labels = array(
    'name'                          => __( 'Amenities', 'ns-real-estate' ),
    'singular_name'                 => __( 'Amenity', 'ns-real-estate' ),
    'search_items'                  => __( 'Search Amenities', 'ns-real-estate' ),
    'popular_items'                 => __( 'Popular Amenities', 'ns-real-estate' ),
    'all_items'                     => __( 'All Amenities', 'ns-real-estate' ),
    'parent_item'                   => __( 'Parent Amenity', 'ns-real-estate' ),
    'edit_item'                     => __( 'Edit Amenity', 'ns-real-estate' ),
    'update_item'                   => __( 'Update Amenity', 'ns-real-estate' ),
    'add_new_item'                  => __( 'Add New Amenity', 'ns-real-estate' ),
    'new_item_name'                 => __( 'New Amenity', 'ns-real-estate' ),
    'separate_items_with_commas'    => __( 'Separate amenities with commas', 'ns-real-estate' ),
    'add_or_remove_items'           => __( 'Add or remove amenities', 'ns-real-estate' ),
    'choose_from_most_used'         => __( 'Choose from most used amenities', 'ns-real-estate' )
    );
    
    register_taxonomy(
        'property_amenities',
        'ns-property',
        array(
            'label'         => __( 'Amenities', 'ns-real-estate' ),
            'labels'        => $labels,
            'hierarchical'  => true,
            'rewrite' => array( 'slug' => $property_amenities_tax_slug )
        )
    );
}
add_action( 'init', 'ns_real_estate_property_amenities_init' );

/*-----------------------------------------------------------------------------------*/
/*  Add Custom Columns to Properties Post Type
/*-----------------------------------------------------------------------------------*/
add_filter( 'manage_edit-ns-property_columns', 'ns_real_estate_edit_properties_columns' ) ;

function ns_real_estate_edit_properties_columns( $columns ) {

    $columns = array(
        'cb' => '<input type="checkbox" />',
        'title' => __( 'Property', 'ns-real-estate' ),
        'thumbnail' => __('Image', 'ns-real-estate'),
        'location' => __( 'Location', 'ns-real-estate' ),
        'type' => __( 'Type', 'ns-real-estate' ),
        'status' => __( 'Status', 'ns-real-estate' ),
        'price'  => __( 'Price','ns-real-estate' ),
        'author' => __('Author', 'ns-real-estate'),
        'date' => __( 'Date', 'ns-real-estate' )
    );

    return $columns;
}


add_action( 'manage_ns-property_posts_custom_column', 'ns_real_estate_manage_properties_columns', 10, 2 );

function ns_real_estate_manage_properties_columns( $column, $post_id ) {
    global $post;

    switch( $column ) {

        case 'thumbnail' :

            if(has_post_thumbnail()) {
                echo the_post_thumbnail('thumbnail');
            } else {
                echo '--';
            }
            break;

        case 'price' :

            $values = get_post_custom( $post_id );
            $price = isset( $values['ns_property_price'] ) ? esc_attr( $values['ns_property_price'][0] ) : '';
            if(!empty($price)) { $price = ns_real_estate_format_price($price); }

            if(empty($price)) {
                echo '--';
            } else {
                echo $price;
            }
            break;

        case 'location' :

            //Get property location
            $property_location_terms = get_the_terms( $post_id, 'property_location' );
            if ( $property_location_terms && ! is_wp_error( $property_location_terms) ) : 
                $property_location_links = array();
                foreach ( $property_location_terms as $property_location_term ) {
                    $property_location_links[] = $property_location_term ->name;
                }                   
                $property_location = join( ", ", $property_location_links );
            endif;

            if ( empty( $property_location ) )
                echo '--';
            else
                echo $property_location;
            break;

        case 'type' :

            //Get property type
               $property_type_terms = get_the_terms( $post_id, 'property_type' );
               if ( $property_type_terms && ! is_wp_error( $property_type_terms) ) : 
                   $property_type_links = array();
                   foreach ( $property_type_terms as $property_type_term ) {
                       $property_type_links[] = $property_type_term ->name;
                   }                   
                   $property_type = join( ", ", $property_type_links );
               endif;

               if ( empty( $property_type ) )
                    echo '--';
                else
                    echo $property_type;
                break;

        case 'status' :

                //Get property status
                $property_status_terms = get_the_terms( $post_id, 'property_status' );
                if ( $property_status_terms && ! is_wp_error( $property_status_terms) ) : 
                    $property_status_links = array();
                    foreach ( $property_status_terms as $property_status_term ) {
                        $property_status_links[] = $property_status_term ->name;
                    }                   
                    $property_status = join( ", ", $property_status_links );
                endif;

                if ( empty( $property_status ) )
                    echo '--';
                else
                    echo $property_status;
                break;

        /* Just break out of the switch statement for everything else. */
        default :
            break;
    }
}

/*-----------------------------------------------------------------------------------*/
/*  Customize Property Taxonomies Admin Page
/*-----------------------------------------------------------------------------------*/
add_action( 'property_type_edit_form_fields', 'ns_real_estate_properties_extra_tax_fields', 10, 2);
add_action( 'edited_property_type', 'ns_real_estate_properties_save_extra_taxonomy_fields', 10, 2);
add_action('property_type_add_form_fields','ns_real_estate_properties_extra_tax_fields', 10, 2 );  
add_action('created_property_type','ns_real_estate_properties_save_extra_taxonomy_fields', 10, 2);

add_action( 'property_status_edit_form_fields', 'ns_real_estate_properties_extra_tax_fields', 10, 2);
add_action( 'property_status_edit_form_fields', 'ns_real_estate_properties_tax_price_range_fields', 10, 2);
add_action( 'edited_property_status', 'ns_real_estate_properties_save_extra_taxonomy_fields', 10, 2);
add_action('property_status_add_form_fields','ns_real_estate_properties_extra_tax_fields', 10, 2 ); 
add_action('property_status_add_form_fields','ns_real_estate_properties_tax_price_range_fields', 10, 2 );
add_action('created_property_status','ns_real_estate_properties_save_extra_taxonomy_fields', 10, 2);

add_action( 'property_location_edit_form_fields', 'ns_real_estate_properties_extra_tax_fields', 10, 2);
add_action( 'edited_property_location', 'ns_real_estate_properties_save_extra_taxonomy_fields', 10, 2);
add_action('property_location_add_form_fields','ns_real_estate_properties_extra_tax_fields', 10, 2 );  
add_action('created_property_location','ns_real_estate_properties_save_extra_taxonomy_fields', 10, 2);

function ns_real_estate_properties_extra_tax_fields($tag) {
   //check for existing taxonomy meta for term ID
    if(is_object($tag)) { $t_id = $tag->term_id; } else { $t_id = ''; }
    $term_meta = get_option( "taxonomy_$t_id");
    ?>
    <tr class="form-field">
        <th scope="row" valign="top"><label for="cat_Image_url"><?php esc_html_e('Category Image Url', 'ns-real-estate'); ?></label></th>
        <td>
            <div class="admin-module admin-module-tax-field admin-module-tax-img no-border">
                <input type="text" class="property-tax-img" name="term_meta[img]" id="term_meta[img]" size="3" style="width:60%;" value="<?php echo $term_meta['img'] ? $term_meta['img'] : ''; ?>">
                <input id="_btn" class="button-secondary ns_upload_image_button" type="button" value="<?php esc_html_e('Upload Image', 'ns-real-estate'); ?>" />
                <span class="button button-secondary remove"><?php esc_html_e('Remove', 'ns-real-estate'); ?></span><br/>
                <span class="description" style="font-size:13px;"><?php esc_html_e('Image for Term, use full url', 'ns-real-estate'); ?></span><br/><br/>
            </div>
        </td>
    </tr>
<?php
}

function ns_real_estate_properties_tax_price_range_fields($tag) {
    if(is_object($tag)) { $t_id = $tag->term_id; } else { $t_id = ''; }
    $term_meta = get_option( "taxonomy_$t_id");
    ?>
    <tr class="form-field">
        <th scope="row" valign="top">
            <strong><?php esc_html_e('Price Range Settings', 'ns-real-estate'); ?></strong>
            <p class="admin-module-note"><?php esc_html_e('Settings here will override the defaults set in the theme options for this term only.', 'ns-real-estate'); ?></p>
        </th>
        <td>
            <div class="admin-module admin-module-tax-field tax-price-range-field no-border">
                <label for="price_range_min"><?php esc_html_e('Minimum', 'ns-real-estate'); ?></label>
                <input type="number" class="property-tax-price-range-min" name="term_meta[price_range_min]" id="term_meta[price_range_min]" size="3" value="<?php echo $term_meta['price_range_min'] ? $term_meta['price_range_min'] : ''; ?>">
            </div>
            <div class="admin-module admin-module-tax-field tax-price-range-field no-border">
                <label for="price_range_max"><?php esc_html_e('Maximum', 'ns-real-estate'); ?></label>
                <input type="number" class="property-tax-price-range-max" name="term_meta[price_range_max]" id="term_meta[price_range_max]" size="3" value="<?php echo $term_meta['price_range_max'] ? $term_meta['price_range_max'] : ''; ?>">
            </div>
            <div class="admin-module admin-module-tax-field tax-price-range-field no-border">
                <label for="price_range_min_start"><?php esc_html_e('Minimum Start', 'ns-real-estate'); ?></label>
                <input type="number" class="property-tax-price-range-min-start" name="term_meta[price_range_min_start]" id="term_meta[price_range_min_start]" size="3" value="<?php echo $term_meta['price_range_min_start'] ? $term_meta['price_range_min_start'] : ''; ?>">
            </div>
            <div class="admin-module admin-module-tax-field tax-price-range-field no-border">
                <label for="price_range_max_start"><?php esc_html_e('Maximum Start', 'ns-real-estate'); ?></label>
                <input type="number" class="property-tax-price-range-max-start" name="term_meta[price_range_max_start]" id="term_meta[price_range_max_start]" size="3" value="<?php echo $term_meta['price_range_max_start'] ? $term_meta['price_range_max_start'] : ''; ?>">
            </div>
        </td>
    </tr>
<?php }

// save extra taxonomy fields callback function
function ns_real_estate_properties_save_extra_taxonomy_fields( $term_id ) {
    if ( isset( $_POST['term_meta'] ) ) {
        $t_id = $term_id;
        $term_meta = get_option( "taxonomy_$t_id");
        $cat_keys = array_keys($_POST['term_meta']);
            foreach ($cat_keys as $key){
            if (isset($_POST['term_meta'][$key])){
                $term_meta[$key] = $_POST['term_meta'][$key];
            }
        }
        //save the option array
        update_option( "taxonomy_$t_id", $term_meta );
    }
}

/*-----------------------------------------------------------------------------------*/
/*  Add Page Settings Metabox to Edit Property Page
/*-----------------------------------------------------------------------------------*/
function ns_real_estate_add_page_settings_metabox($post_types) {
    $post_types[] = 'ns-property';
    return $post_types;
}
add_filter( 'ns_basics_page_settings_post_types', 'ns_real_estate_add_page_settings_metabox', 10, 3 );

//set property default page layout
function ns_real_estate_set_default_property_layout($page_layout_default) {
    if($_GET['post_type'] == 'ns-property') {
        $property_detail_default_layout = esc_attr(get_option('ns_property_detail_default_layout', 'right sidebar'));
        $page_layout_default = $property_detail_default_layout; 
    }
    return $page_layout_default;
}
add_filter( 'ns_basics_page_layout_sidebar', 'ns_real_estate_set_default_property_layout');

//set property default sidebar widget area
function ns_real_estate_set_default_property_widget_area($page_layout_widget_area_default) {
    if($_GET['post_type'] == 'ns-property') { $page_layout_widget_area_default = 'Properties_sidebar';  }
    return $page_layout_widget_area_default;
}
add_filter( 'ns_basics_page_layout_widget_area', 'ns_real_estate_set_default_property_widget_area');


/*-----------------------------------------------------------------------------------*/
/*  Output Properties Map Banner
/*-----------------------------------------------------------------------------------*/
function ns_real_estate_properties_map_banner($banner_source) { 
    if($banner_source == 'properties_map') { 
        ns_real_estate_template_loader('properties_map.php');
    }
}
add_filter( 'ns_core_custom_banner_source', 'ns_real_estate_properties_map_banner');

function ns_real_estate_properties_map_custom_header_var($header_vars) { 
    $page_id = ns_core_get_page_id();
    $values = get_post_custom( $page_id);
    $banner_source = isset( $values['ns_basics_banner_source'] ) ? esc_attr( $values['ns_basics_banner_source'][0] ) : 'image_banner';
    if($banner_source == 'properties_map' && $header_vars['header_style'] == 'transparent') { $header_vars['header_style'] = ''; }
    return $header_vars;
}
add_filter( 'ns_basics_custom_header_vars', 'ns_real_estate_properties_map_custom_header_var');

/*-----------------------------------------------------------------------------------*/
/*  Output Property Dashboard Widgets
/*-----------------------------------------------------------------------------------*/
function ns_real_estate_dashboard_stats() {
    global $current_user; ?>
    <div class="user-dashboard-widget stat">
        <span><?php echo ns_real_estate_count_properties(array('publish', 'pending'), $current_user->user_login); ?></span> 
        <?php esc_html_e('Total Properties', 'ns-real-estate'); ?>
    </div>

    <div class="user-dashboard-widget stat">
        <span><?php echo ns_real_estate_count_properties(array('pending'), $current_user->user_login); ?></span> 
        <?php esc_html_e('Pending Properties', 'ns-real-estate'); ?>
    </div>
<?php }
add_filter( 'ns_basics_dashboard_stats', 'ns_real_estate_dashboard_stats');

function ns_real_estate_dashboard_widgets($banner_source) {
    global $current_user; ?>

    <div class="user-dashboard-widget user-dashboard-widget-full">
        <div class="module-header module-header-left">
            <h4><strong><?php esc_html_e('Recent Properties', 'ns-real-estate'); ?></strong></h4>
        </div>
        <?php 
            $args_recent = array(
                'post_type' => 'ns-property',
                'showposts' => 4,
                'author_name' => $current_user->user_login
            );
            
            //Set template args
            $template_args = array();
            $template_args['custom_args'] = $args_recent;
            $template_args['custom_show_filter'] = false;
            $template_args['custom_layout'] = 'grid';
            $template_args['custom_pagination'] = false;
            $template_args['no_post_message'] = esc_html__( 'Sorry, no recent properties were found.', 'ns-real-estate' );
                                
            //Load template
            ns_real_estate_template_loader('loop_properties.php', $template_args_related_properties);
        ?>
    </div>
<?php }
add_filter( 'ns_basics_after_dashboard', 'ns_real_estate_dashboard_widgets');

/*-----------------------------------------------------------------------------------*/
/*  Add Property Image Sizes
/*-----------------------------------------------------------------------------------*/
add_image_size( 'property-thumbnail', 800, 600, array( 'center', 'center' ) );

//returns an image size dimensions
function ns_real_estate_get_image_size($size_name) {
    global $_wp_additional_image_sizes;
    $size_output = array();
    $wp_img_sizes = get_intermediate_image_sizes();
    foreach($wp_img_sizes as $size) {
        if($size == $size_name) {
            $size_output['width'] = $_wp_additional_image_sizes[$size]['width'];
            $size_output['height'] = $_wp_additional_image_sizes[$size]['height'];
        }    
    }
    return $size_output;
}

/*-----------------------------------------------------------------------------------*/
/*  Register Properties Sidebar
/*-----------------------------------------------------------------------------------*/
function ns_real_estate_properties_sidebar_init() {
    register_sidebar( array(
        'name' => esc_html__( 'Properties Sidebar', 'ns-real-estate' ),
        'id' => 'properties_sidebar',
        'before_widget' => '<div class="widget widget-sidebar widget-sidebar-properties %2$s">',
        'after_widget' => '</div>',
        'before_title' => '<h4>',
        'after_title' => '</h4>',
    ) );
}
add_action( 'widgets_init', 'ns_real_estate_properties_sidebar_init' );

?>