<?php

/*-----------------------------------------------------------------------------------*/
/*  Load default Property Filter Items
/*-----------------------------------------------------------------------------------*/
function rype_real_estate_load_default_property_filter_items() {
    $property_filter_items_default = array(
        0 => array(
            'name' => esc_html__('Property Type', 'rype-real-estate'),
            'label' => esc_html__('Property Type', 'rype-real-estate'),
            'placeholder' => esc_html__('Any', 'rype-real-estate'),
            'slug' => 'property_type',
            'active' => 'true',
            'custom' => 'false',
        ),
        1 => array(
            'name' => esc_html__('Property Status', 'rype-real-estate'),
            'label' => esc_html__('Property Status', 'rype-real-estate'),
            'placeholder' => esc_html__('Any', 'rype-real-estate'),
            'slug' => 'property_status',
            'active' => 'true',
            'custom' => 'false',
        ),
        2 => array(
            'name' => esc_html__('Property Location', 'rype-real-estate'),
            'label' => esc_html__('Property Location', 'rype-real-estate'),
            'placeholder' => esc_html__('Any', 'rype-real-estate'),
            'slug' => 'property_location',
            'active' => 'true',
            'custom' => 'false',
        ),
        3 => array(
            'name' => esc_html__('Price Range', 'rype-real-estate'),
            'label' => esc_html__('Price Range', 'rype-real-estate'),
            'slug' => 'price',
            'active' => 'true',
            'custom' => 'false',
        ),
        4 => array(
            'name' => esc_html__('Bedrooms', 'rype-real-estate'),
            'label' => esc_html__('Bedrooms', 'rype-real-estate'),
            'placeholder' => esc_html__('Any', 'rype-real-estate'),
            'slug' => 'beds',
            'active' => 'true',
            'custom' => 'false',
        ),
        5 => array(
            'name' => esc_html__('Bathrooms', 'rype-real-estate'),
            'label' => esc_html__('Bathrooms', 'rype-real-estate'),
            'placeholder' => esc_html__('Any', 'rype-real-estate'),
            'slug' => 'baths',
            'active' => 'true',
            'custom' => 'false',
        ),
        6 => array(
            'name' => esc_html__('Area', 'rype-real-estate'),
            'label' => esc_html__('Area', 'rype-real-estate'),
            'placeholder' => esc_html__('Min', 'rype-real-estate'),
            'placeholder_second' => esc_html__('Max', 'rype-real-estate'),
            'slug' => 'area',
            'active' => 'true',
            'custom' => 'false',
        ),
    );

    return $property_filter_items_default;
}

/*-----------------------------------------------------------------------------------*/
/*  Property Filter Custom Post Type
/*-----------------------------------------------------------------------------------*/
add_action( 'init', 'rype_real_estate_create_property_filter_post_type' );
function rype_real_estate_create_property_filter_post_type() {
    register_post_type( 'rype-property-filter',
        array(
            'labels' => array(
                'name' => __( 'Property Filters', 'rype-real-estate' ),
                'singular_name' => __( 'Property Filter', 'rype-real-estate' ),
                'add_new_item' => __( 'Add New Property Filter', 'rype-real-estate' ),
                'search_items' => __( 'Search Property Filters', 'rype-real-estate' ),
                'edit_item' => __( 'Edit Property Filter', 'rype-real-estate' ),
            ),
        'public' => false,
		'publicly_queryable' => true,
		'show_ui' => true,
        'show_in_nav_menus' => false,
        'has_archive' => false,
        'supports' => array('title', 'revisions', 'page_attributes'),
        )
    );
}

/* Add property filter details (meta box) */ 
function rype_real_estate_add_property_filter_meta_box() {
    add_meta_box( 'property-filter-details-meta-box', 'Filter Details', 'rype_real_estate_property_filter_details', 'rype-property-filter', 'normal', 'high' );
}
add_action( 'add_meta_boxes', 'rype_real_estate_add_property_filter_meta_box' );

/* Ouput property filter details form */
function rype_real_estate_property_filter_details($post) {

	$values = get_post_custom( $post->ID );
	$filter_position = isset( $values['rypecore_property_filter_position'] ) ? esc_attr( $values['rypecore_property_filter_position'][0] ) : 'middle';
	$filter_layout = isset( $values['rypecore_property_filter_layout'] ) ? esc_attr( $values['rypecore_property_filter_layout'][0] ) : 'minimal';
	$display_filter_tabs = isset( $values['rypecore_property_filter_display_tabs'] ) ? esc_attr( $values['rypecore_property_filter_display_tabs'][0] ) : 'false';	
	if(isset($values['rypecore_property_filter_items'])) {
		$property_filter_items = $values['rypecore_property_filter_items'];
		$property_filter_items = unserialize($property_filter_items[0]);
	} else {
		$property_filter_items = rype_real_estate_load_default_property_filter_items();
	}
	$price_range_min = isset( $values['rypecore_filter_price_min'] ) ? esc_attr( $values['rypecore_filter_price_min'][0] ) : 0;
	$price_range_max = isset( $values['rypecore_filter_price_max'] ) ? esc_attr( $values['rypecore_filter_price_max'][0] ) : 1000000;
	$price_range_min_start = isset( $values['rypecore_filter_price_min_start'] ) ? esc_attr( $values['rypecore_filter_price_min_start'][0] ) : 200000;
	$price_range_max_start = isset( $values['rypecore_filter_price_max_start'] ) ? esc_attr( $values['rypecore_filter_price_max_start'][0] ) : 600000;
	$submit_text = isset( $values['rypecore_property_filter_submit_text'] ) ? esc_attr( $values['rypecore_property_filter_submit_text'][0] ) : esc_html__('Find Properties', 'rype-real-estate');
	$custom_fields = get_option('rypecore_custom_fields');
	wp_nonce_field( 'rypecore_property_filter_details_meta_box_nonce', 'rypecore_property_filter_details_meta_box_nonce' );
	?>

    <table class="admin-module admin-module-shortcode">
        <tr>
            <td class="admin-module-label">
                <label><?php esc_html_e('Shortcode', 'rype-real-estate'); ?></label>
                <span class="admin-module-note"><?php esc_html_e('Copy/paste it into your post, page, or text widget content:', 'rype-real-estate'); ?></span>
            </td>
            <td class="admin-module-field"><pre>[rype_property_filter id="<?php echo $post->ID; ?>"]</pre></td>
        </tr>
    </table>

    <table class="admin-module">
        <tr>
            <td class="admin-module-label"><label><?php echo esc_html_e('Page Banner Position', 'rype-real-estate'); ?></label></td>
            <td class="admin-module-field">
                <select name="rypecore_property_filter_position" id="property_filter_position">
                    <option value="above" <?php if($filter_position == 'above') { echo 'selected'; } ?>><?php esc_html_e('Above Banner', 'rype-real-estate'); ?></option>
                    <option value="middle" <?php if($filter_position == 'middle') { echo 'selected'; } ?>><?php esc_html_e('Inside Banner', 'rype-real-estate'); ?></option>
                    <option value="below" <?php if($filter_position == 'below') { echo 'selected'; } ?>><?php esc_html_e('Below Banner', 'rype-real-estate'); ?></option>
                </select>
            </td>
        </tr>
    </table>

    <table class="admin-module">
        <tr>
            <td class="admin-module-label"><label><?php echo esc_html_e('Filter Layout', 'rype-real-estate'); ?></label></td>
            <td class="admin-module-field">
                <select name="rypecore_property_filter_layout" id="property_filter_layout">
                    <option value="full" <?php if($filter_layout == 'full') { echo 'selected'; } ?>><?php esc_html_e('Full Width', 'rype-real-estate'); ?></option>
                    <option value="minimal" <?php if($filter_layout == 'minimal') { echo 'selected'; } ?>><?php esc_html_e('Minimal', 'rype-real-estate'); ?></option>
                    <option value="boxed" <?php if($filter_layout == 'boxed') { echo 'selected'; } ?>><?php esc_html_e('Boxed', 'rype-real-estate'); ?></option>
                </select>
            </td>
        </tr>
    </table>

    <table class="admin-module">
        <tr>
            <td class="admin-module-label"><label><?php echo esc_html_e('Display Filter Tabs', 'rype-real-estate'); ?></label></td>
            <td class="admin-module-field">
                <input type="checkbox" id="property_filter_display_tabs" name="rypecore_property_filter_display_tabs" value="true" <?php if($display_filter_tabs == 'true') { echo 'checked'; } ?> />
            </td>
        </tr>
    </table>

	<div class="admin-module admin-module-filter-fields">
        <div class="admin-module-label"><label><?php echo esc_html_e('Filter Fields', 'rype-real-estate'); ?> <span class="admin-module-note"><?php echo esc_html_e('(Drag & drop to rearrange order)', 'rype-real-estate'); ?></span></label></div>
        <ul class="sortable-list filter-fields-list">
            <?php
            $count = 0;
            foreach($property_filter_items as $value) { ?>
                <?php
                    if(isset($value['name'])) { $name = $value['name']; }
                    if(isset($value['label'])) { $label = $value['label']; }
                    if(isset($value['placeholder'])) { $placeholder = $value['placeholder']; } else { $placeholder = null; }
                    if(isset($value['placeholder_second'])) { $placeholder_second = $value['placeholder_second']; } else { $placeholder_second = null; }
                    if(isset($value['slug'])) { $slug = $value['slug']; }
                    if(isset($value['active']) && $value['active'] == 'true') { $active = 'true'; } else { $active = 'false'; }
                    if(isset($value['custom']) && $value['custom'] == 'true') { $custom = 'true'; } else { $custom = 'false'; }
                ?>

                <?php if($custom == 'true') {
                    if(ns_basics_in_array($custom_fields, 'id', $slug)) { ?>
                    <li class="sortable-item custom-filter-field custom-filter-field-<?php echo $slug; ?>">
                        <div class="sortable-item-header">
                            <div class="sort-arrows"><i class="fa fa-bars"></i></div>
                            <span class="sortable-item-action remove right"><i class="fa fa-times"></i> <?php esc_html_e('Remove', 'rype-real-estate'); ?></span>
                            <span class="sortable-item-title custom-filter-field-label"><?php echo esc_attr($name); ?></span> 
                            <span class="admin-module-note"><?php esc_html_e('(Custom Field)', 'rype-real-estate'); ?></span>
                            <div class="clear"></div>
                            <input type="hidden" name="rypecore_property_filter_items[<?php echo $count; ?>][active]" value="true" />
                            <input type="hidden" name="rypecore_property_filter_items[<?php echo $count; ?>][name]" value="<?php echo $name; ?>" class="custom-filter-field-name" />
                            <input type="hidden" name="rypecore_property_filter_items[<?php echo $count; ?>][slug]" value="<?php echo $slug; ?>" class="custom-filter-field-slug" />
                            <input type="hidden" name="rypecore_property_filter_items[<?php echo $count; ?>][custom]" value="true" /> 
                        </div>
                    </li>
                	<?php } ?>
                <?php } else { ?>
                    <li class="sortable-item">

                        <div class="sortable-item-header">
                            <div class="sort-arrows"><i class="fa fa-bars"></i></div>
                            <div class="toggle-switch" title="<?php if($active == 'true') { esc_html_e('Active', 'rype-real-estate'); } else { esc_html_e('Disabled', 'rype-real-estate'); } ?>">
                                <input type="checkbox" name="rypecore_property_filter_items[<?php echo $count; ?>][active]" value="true" class="toggle-switch-checkbox" id="property_filter_item_<?php echo esc_attr($slug); ?>" <?php checked('true', $active, true) ?>>
                                <label class="toggle-switch-label" for="property_filter_item_<?php echo esc_attr($slug); ?>"><?php if($active == 'true') { echo '<span class="on">'.esc_html__('On', 'rype-real-estate').'</span>'; } else { echo '<span>'.esc_html__('Off', 'rype-real-estate').'</span>'; } ?></label>
                            </div>
                            <span class="sortable-item-title"><?php echo esc_attr($name); ?></span><div class="clear"></div>
                            <input type="hidden" name="rypecore_property_filter_items[<?php echo $count; ?>][name]" value="<?php echo $name; ?>" />
                            <input type="hidden" name="rypecore_property_filter_items[<?php echo $count; ?>][slug]" value="<?php echo $slug; ?>" />
                            <input type="hidden" name="rypecore_property_filter_items[<?php echo $count; ?>][custom]" value="<?php echo $custom; ?>" />
                        </div>

                        <a href="#advanced-options-content-<?php echo esc_attr($slug); ?>" class="sortable-item-action advanced-options-toggle right"><i class="fa fa-gear"></i> <?php echo esc_html_e('Additional Settings', 'rype-real-estate'); ?></a>
                        <div id="advanced-options-content-<?php echo esc_attr($slug); ?>" class="advanced-options-content hide-soft">

                            <table class="admin-module">
                                <tr>
                                    <td class="admin-module-label"><label><?php esc_html_e('Label:', 'rype-real-estate'); ?></label></td>
                                    <td class="admin-module-field">
                                        <input type="text" name="rypecore_property_filter_items[<?php echo $count; ?>][label]" value="<?php echo $label; ?>" />
                                    </td>
                                </tr>
                            </table>

                            <?php if(isset($placeholder)) { ?>
                            <table class="admin-module">
                                <tr>
                                    <td class="admin-module-label"><label><?php esc_html_e('Placeholder:', 'rype-real-estate'); ?></label></td>
                                    <td class="admin-module-field">
                                        <input type="text" name="rypecore_property_filter_items[<?php echo $count; ?>][placeholder]" value="<?php echo $placeholder; ?>" />
                                    </td>
                                </tr>
                            </table>
                            <?php } ?>

                            <?php if(isset($placeholder_second)) { ?>
                            <table class="admin-module">
                                <tr>
                                    <td class="admin-module-label"><label><?php esc_html_e('Placeholder Second:', 'rype-real-estate'); ?></label></td>
                                    <td class="admin-module-field">
                                        <input type="text" name="rypecore_property_filter_items[<?php echo $count; ?>][placeholder_second]" value="<?php echo $placeholder_second; ?>" />
                                    </td>
                                </tr>
                            </table>
                            <?php } ?>

                            <?php if($slug == 'price') { ?>                                

                                <table class="admin-module">
                                    <tr>
                                        <td class="admin-module-label"><label><?php esc_html_e('Price Range Minimum', 'rype-real-estate'); ?></label></td>
                                        <td class="admin-module-field"><input type="number" id="filter_price_min" name="rypecore_filter_price_min" value="<?php echo $price_range_min; ?>" /></td>
                                    </tr>
                                </table>

                                <table class="admin-module">
                                    <tr>
                                        <td class="admin-module-label"><label><?php esc_html_e('Price Range Maximum', 'rype-real-estate'); ?></label></td>
                                        <td class="admin-module-field"><input type="number" id="filter_price_max" name="rypecore_filter_price_max" value="<?php echo $price_range_max; ?>" /></td>
                                    </tr>
                                </table>

                                <table class="admin-module">
                                    <tr>
                                        <td class="admin-module-label"><label><?php esc_html_e('Price Range Minimum Start', 'rype-real-estate'); ?></label></td>
                                        <td class="admin-module-field"><input type="number" id="filter_price_min_start" name="rypecore_filter_price_min_start" value="<?php echo $price_range_min_start; ?>" /></td>
                                    </tr>
                                </table>

                                <table class="admin-module">
                                    <tr>
                                        <td class="admin-module-label"><label><?php esc_html_e('Price Range Maximum Start', 'rype-real-estate'); ?></label></td>
                                        <td class="admin-module-field"><input type="number" id="filter_price_max_start" name="rypecore_filter_price_max_start" value="<?php echo $price_range_max_start; ?>" /></td>
                                    </tr>
                                </table>

                                <div class="admin-module-note">
                                    <?php esc_html_e('You can override these setting for specific property statuses ', 'rype-real-estate'); ?>
                                    <a href="<?php echo admin_url().'edit-tags.php?taxonomy=property_status&post_type=properties'; ?>"><?php esc_html_e('here', 'rype-real-estate'); ?></a>
                                </div>
                            <?php } ?>                                      
                        </div>

                    </li>
                <?php } ?>
                <?php $count++; ?>
            <?php } ?>
        </ul>

         <table class="admin-module no-border no-padding-bottom">
            <tr>
                <td class="admin-module-label">
                    <label><?php esc_html_e('Add Custom Field to Filter', 'rype-real-estate'); ?></label>
                    <span class="admin-module-note"><a href="<?php echo admin_url('themes.php?page=theme_options#custom-property-fields'); ?>" target="_blank"><i class="fa fa-cog"></i> <?php esc_html_e('Manage custom fields', 'rype-real-estate'); ?></a></span>
                </td>
                <td class="admin-module-field">
                    <?php 
                    if(!empty($custom_fields)) { 
                        echo '<select class="select-filter-custom-field">';
                        echo '<option value="">Select a field...</option>';
                        foreach($custom_fields as $key=>$custom_field) {
                            if(!is_array($custom_field)) { 
                                $custom_field = array( 
                                    'id' => strtolower(str_replace(' ', '_', $custom_field)),
                                    'name' => $custom_field, 
                                    'type' => 'text',
                                    'front_end' => 'true',
                                ); 
                            }
                            echo '<option value="'.$custom_field['id'].'">'.$custom_field['name'].'</option>';
                        }
                        echo '</select>'; ?>
                        <div class="add-filter-custom-field button button-secondary"><?php esc_html_e('Insert Field', 'rype-real-estate'); ?></div>
                    <?php } else { ?> 
                        <span class="admin-module-note"><?php esc_html_e('No custom fields have been created.', 'rype-real-estate'); ?></span>
                    <?php } ?>
                </td>
            </tr>
        </table>

    </div>

    <table class="admin-module no-border">
        <tr>
            <td class="admin-module-label"><label><?php echo esc_html_e('Submit Button Text', 'rype-real-estate'); ?></label></td>
            <td class="admin-module-field">
                <input type="text" name="rypecore_property_filter_submit_text" id="property_filter_submit_text" value="<?php echo $submit_text; ?>" />
            </td>
        </tr>
    </table>

<?php }

/* Save property filter details form */
add_action( 'save_post', 'rype_real_estate_save_property_filter_meta_box' );
function rype_real_estate_save_property_filter_meta_box( $post_id ) {

	 // Bail if we're doing an auto save
    if( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) return;

    // if our nonce isn't there, or we can't verify it, bail
    if( !isset( $_POST['rypecore_property_filter_details_meta_box_nonce'] ) || !wp_verify_nonce( $_POST['rypecore_property_filter_details_meta_box_nonce'], 'rypecore_property_filter_details_meta_box_nonce' ) ) return;

    // if our current user can't edit this post, bail
    if( !current_user_can( 'edit_post', $post_id ) ) return;

    // save the data
    $allowed = array(
        'a' => array( // on allow a tags
            'href' => array() // and those anchors can only have href attribute
        )
    );

    if( isset( $_POST['rypecore_property_filter_position'] ) )
        update_post_meta( $post_id, 'rypecore_property_filter_position', wp_kses( $_POST['rypecore_property_filter_position'], $allowed ) );

    if( isset( $_POST['rypecore_property_filter_layout'] ) )
        update_post_meta( $post_id, 'rypecore_property_filter_layout', wp_kses( $_POST['rypecore_property_filter_layout'], $allowed ) );

    if( isset( $_POST['rypecore_property_filter_display_tabs'] ) ) {
        update_post_meta( $post_id, 'rypecore_property_filter_display_tabs', wp_kses( $_POST['rypecore_property_filter_display_tabs'], $allowed ) );
    } else {
        update_post_meta( $post_id, 'rypecore_property_filter_display_tabs', wp_kses( '', $allowed ) );
    }

    if (isset( $_POST['rypecore_property_filter_items'] )) {
        update_post_meta( $post_id, 'rypecore_property_filter_items', $_POST['rypecore_property_filter_items'] );
    } else {
        update_post_meta( $post_id, 'rypecore_property_filter_items', '' );
    }

    if( isset( $_POST['rypecore_filter_price_min'] ) )
        update_post_meta( $post_id, 'rypecore_filter_price_min', wp_kses( $_POST['rypecore_filter_price_min'], $allowed ) );

    if( isset( $_POST['rypecore_filter_price_max'] ) )
        update_post_meta( $post_id, 'rypecore_filter_price_max', wp_kses( $_POST['rypecore_filter_price_max'], $allowed ) );

    if( isset( $_POST['rypecore_filter_price_min_start'] ) )
        update_post_meta( $post_id, 'rypecore_filter_price_min_start', wp_kses( $_POST['rypecore_filter_price_min_start'], $allowed ) );

    if( isset( $_POST['rypecore_filter_price_max_start'] ) )
        update_post_meta( $post_id, 'rypecore_filter_price_max_start', wp_kses( $_POST['rypecore_filter_price_max_start'], $allowed ) );

    if( isset( $_POST['rypecore_property_filter_submit_text'] ) )
        update_post_meta( $post_id, 'rypecore_property_filter_submit_text', wp_kses( $_POST['rypecore_property_filter_submit_text'], $allowed ) );
}

/*-----------------------------------------------------------------------------------*/
/*  Add Custom Columns to Property Filter Post Type
/*-----------------------------------------------------------------------------------*/
add_filter( 'manage_edit-rype-property-filter_columns', 'rype_real_estate_edit_property_filter_columns' ) ;
function rype_real_estate_edit_property_filter_columns( $columns ) {

    $columns = array(
        'cb' => '<input type="checkbox" />',
        'title' => __( 'Property', 'rype-real-estate' ),
        'shortcode' => __( 'Shortcode', 'rype-real-estate' ),
        'date' => __( 'Date', 'rype-real-estate' )
    );

    return $columns;
}

add_action( 'manage_rype-property-filter_posts_custom_column', 'rype_real_estate_manage_property_filter_columns', 10, 2 );
function rype_real_estate_manage_property_filter_columns( $column, $post_id ) {
    global $post;

    switch( $column ) {

        case 'shortcode' :
            echo '<pre>[rype_property_filter id="'.$post_id.'"]</pre>';
            break;

        /* Just break out of the switch statement for everything else. */
        default :
            break;
    }
}

/*-----------------------------------------------------------------------------------*/
/*  Output Page Banner Property Filter
/*-----------------------------------------------------------------------------------*/
function rype_real_estate_page_banner_property_filter_global() {

    //Global settings
    $property_filter_display = esc_attr(get_option('ns_property_filter_display', 'true'));
    $property_filter_id = esc_attr(get_option('ns_property_filter_id'));

    //Individual page settings
    global $post;
    if(function_exists('ns_core_get_page_id')) { $page_id = ns_core_get_page_id(); } else { $page_id = $post->ID; }
    $values = get_post_custom( $page_id );
    $banner_property_filter_override = isset( $values['rypecore_banner_property_filter_override'] ) ? esc_attr( $values['rypecore_banner_property_filter_override'][0] ) : 'true'; 
    if($banner_property_filter_override != 'true') {
        $property_filter_display = isset( $values['rypecore_banner_property_filter_display'] ) ? esc_attr( $values['rypecore_banner_property_filter_display'][0] ) : 'true';
        $property_filter_id = isset( $values['rypecore_banner_property_filter_id'] ) ? esc_attr( $values['rypecore_banner_property_filter_id'][0] ) : '';
    }

    //Get filter details
    $values = get_post_custom( $property_filter_id );
    $property_filter_position = isset( $values['rypecore_property_filter_position'] ) ? esc_attr( $values['rypecore_property_filter_position'][0] ) : 'middle';

    //If filter position above, change to classic header
    if($property_filter_position == 'above') {
        function rype_real_estate_properties_filter_custom_header_var($header_vars) { 
            if($header_vars['header_style'] == 'transparent') { $header_vars['header_style'] = ''; }
            return $header_vars;
        }
        add_filter( 'ns_basics_custom_header_vars', 'rype_real_estate_properties_filter_custom_header_var');
    }

    //generate filter position hook name
    if($property_filter_position == 'above') { 
        $property_filter_position = 'ns_basics_before_page_banner'; 
    } else if($property_filter_position == 'middle') {
        $property_filter_position = 'ns_basics_after_subheader_title'; 
    } else { 
        $property_filter_position = 'ns_basics_after_page_banner'; 
    }

    //output filter template
    if($property_filter_display == 'true') {
        function rype_real_estate_page_banner_property_filter($values) {
            $banner_property_filter_override = isset( $values['rypecore_banner_property_filter_override'] ) ? esc_attr( $values['rypecore_banner_property_filter_override'][0] ) : 'true'; 
            if($banner_property_filter_override != 'true') {
                $property_filter_id = isset( $values['rypecore_banner_property_filter_id'] ) ? esc_attr( $values['rypecore_banner_property_filter_id'][0] ) : '';
            } else {
                $property_filter_id = esc_attr(get_option('ns_property_filter_id'));
            }
            
            //Get filter details
            $values = get_post_custom( $property_filter_id );
            $property_filter_layout = isset( $values['rypecore_property_filter_layout'] ) ? esc_attr( $values['rypecore_property_filter_layout'][0] ) : 'middle';
            
            if($property_filter_layout == 'minimal') {
                rype_real_estate_template_loader('property-filter-minimal.php');
            } else {
                rype_real_estate_template_loader('property-filter.php');
            }
            
        }
        add_filter( $property_filter_position, 'rype_real_estate_page_banner_property_filter');
    }
}
add_action('template_redirect', 'rype_real_estate_page_banner_property_filter_global');

?>