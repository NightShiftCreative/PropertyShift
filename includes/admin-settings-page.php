<?php
/*-----------------------------------------------------------------------------------*/
/*  ADD ADMIN MENU PAGES
/*-----------------------------------------------------------------------------------*/
add_action('admin_menu', 'ns_real_estate_plugin_menu');
function ns_real_estate_plugin_menu() {
    add_menu_page('NS Real Estate', 'NS Real Estate', 'administrator', 'ns-real-estate-settings', 'ns_real_estate_settings_page', 'dashicons-admin-home');
    add_submenu_page('ns-real-estate-settings', 'Settings', 'Settings', 'administrator', 'ns-real-estate-settings');
    add_submenu_page('ns-real-estate-settings', 'Add-Ons', 'Add-Ons', 'administrator', 'ns-real-estate-add-ons', 'ns_real_estate_add_ons_page');
    add_submenu_page('ns-real-estate-settings', 'License Keys', 'License Keys', 'administrator', 'ns-real-estate-license-keys', 'ns_real_estate_license_keys_page');
    add_submenu_page('ns-real-estate-settings', 'Help', 'Help', 'administrator', 'ns-real-estate-help', 'ns_real_estate_help_page');
    add_action( 'admin_init', 'ns_real_estate_register_options' );
}

/*-----------------------------------------------------------------------------------*/
/*  REGISTER SETTINGS
/*-----------------------------------------------------------------------------------*/
function ns_real_estate_register_options() {

    //PROPERTY SETTINGS
    register_setting( 'ns-real-estate-settings-group', 'ns_property_detail_slug', 'ns_real_estate_sanitize_slug');
    register_setting( 'ns-real-estate-settings-group', 'ns_property_type_tax_slug', 'ns_real_estate_sanitize_slug');
    register_setting( 'ns-real-estate-settings-group', 'ns_property_status_tax_slug', 'ns_real_estate_sanitize_slug');
    register_setting( 'ns-real-estate-settings-group', 'ns_property_location_tax_slug', 'ns_real_estate_sanitize_slug');
    register_setting( 'ns-real-estate-settings-group', 'ns_property_amenities_tax_slug', 'ns_real_estate_sanitize_slug');

    register_setting( 'ns-real-estate-settings-group', 'ns_property_filter_display' );
    register_setting( 'ns-real-estate-settings-group', 'ns_property_filter_id' );

    register_setting( 'ns-real-estate-settings-group', 'ns_properties_page' );
    register_setting( 'ns-real-estate-settings-group', 'ns_num_properties_per_page' );
    register_setting( 'ns-real-estate-settings-group', 'ns_properties_default_layout' );
    register_setting( 'ns-real-estate-settings-group', 'ns_property_listing_header_display' );
    register_setting( 'ns-real-estate-settings-group', 'ns_property_listing_default_sortby' );
    register_setting( 'ns-real-estate-settings-group', 'ns_property_listing_crop' );
    register_setting( 'ns-real-estate-settings-group', 'ns_property_listing_display_time' );
    register_setting( 'ns-real-estate-settings-group', 'ns_property_listing_display_favorite' );
    register_setting( 'ns-real-estate-settings-group', 'ns_property_listing_display_share' );

    register_setting( 'ns-real-estate-settings-group', 'ns_property_detail_template' );
    register_setting( 'ns-real-estate-settings-group', 'ns_property_detail_display_gallery_agent' );
    register_setting( 'ns-real-estate-settings-group', 'ns_property_detail_default_layout' );
    register_setting( 'ns-real-estate-settings-group', 'ns_property_detail_id' );
    register_setting( 'ns-real-estate-settings-group', 'ns_property_detail_items' );
    register_setting( 'ns-real-estate-settings-group', 'ns_property_detail_amenities_hide_empty' );
    register_setting( 'ns-real-estate-settings-group', 'ns_property_detail_map_zoom' );
    register_setting( 'ns-real-estate-settings-group', 'ns_property_detail_map_height' );
    register_setting( 'ns-real-estate-settings-group', 'ns_property_detail_agent_contact_form' );

    register_setting( 'ns-real-estate-settings-group', 'ns_property_custom_fields' );

    //AGENT SETTINGS
    register_setting( 'ns-real-estate-settings-group', 'ns_num_agents_per_page' );
    register_setting( 'ns-real-estate-settings-group', 'ns_agent_detail_slug', 'ns_real_estate_sanitize_slug' );
    register_setting( 'ns-real-estate-settings-group', 'ns_agent_listing_crop' );
    register_setting( 'ns-real-estate-settings-group', 'ns_agent_detail_items' );
    register_setting( 'ns-real-estate-settings-group', 'ns_agent_form_message_placeholder' );
    register_setting( 'ns-real-estate-settings-group', 'ns_agent_form_success' );
    register_setting( 'ns-real-estate-settings-group', 'ns_agent_form_submit_text' );

    //MAP SETTINGS
    register_setting( 'ns-real-estate-settings-group', 'ns_real_estate_google_maps_api' );
    register_setting( 'ns-real-estate-settings-group', 'ns_real_estate_default_map_zoom' );
    register_setting( 'ns-real-estate-settings-group', 'ns_real_estate_default_map_latitude' );
    register_setting( 'ns-real-estate-settings-group', 'ns_real_estate_default_map_longitude' );
    register_setting( 'ns-real-estate-settings-group', 'ns_real_estate_google_maps_pin' );

    //MEMBER SETTINGS
    register_setting( 'ns-real-estate-settings-group', 'ns_members_my_properties_page' );
    register_setting( 'ns-real-estate-settings-group', 'ns_members_submit_property_page' );
    register_setting( 'ns-real-estate-settings-group', 'ns_members_submit_property_approval' );
    register_setting( 'ns-real-estate-settings-group', 'ns_members_add_locations' );
    register_setting( 'ns-real-estate-settings-group', 'ns_members_add_amenities' );

    //CURRENCY SETTINGS
    register_setting( 'ns-real-estate-settings-group', 'ns_real_estate_currency_symbol' );
    register_setting( 'ns-real-estate-settings-group', 'ns_real_estate_currency_symbol_position' );
    register_setting( 'ns-real-estate-settings-group', 'ns_real_estate_thousand_separator' );
    register_setting( 'ns-real-estate-settings-group', 'ns_real_estate_decimal_separator' );
    register_setting( 'ns-real-estate-settings-group', 'ns_real_estate_num_decimal' );
    register_setting( 'ns-real-estate-settings-group', 'ns_real_estate_default_area_postfix' );
    register_setting( 'ns-real-estate-settings-group', 'ns_real_estate_thousand_separator_area' );
    register_setting( 'ns-real-estate-settings-group', 'ns_real_estate_decimal_separator_area' );
    register_setting( 'ns-real-estate-settings-group', 'ns_real_estate_num_decimal_area' );

    //LICENSE KEY SETTINGS
    register_setting( 'ns-real-estate-license-keys-group', 'ns_real_estate_open_houses_license');

    //ADD-ON SETTINGS
    do_action( 'ns_real_estate_register_settings');
}

function ns_real_estate_sanitize_slug($option) {
    $option = sanitize_title($option);
    return $option;
}

/*-----------------------------------------------------------------------------------*/
/*  OUTPUT SETTINGS PAGE
/*-----------------------------------------------------------------------------------*/
function ns_real_estate_get_admin_pages() {
    $pages = array();
    $pages[] = array('slug' => 'ns-real-estate-settings', 'name' => esc_html__('Settings', 'ns-real-estate'));
    $pages[] = array('slug' => 'ns-real-estate-add-ons', 'name' => esc_html__('Add-Ons', 'ns-real-estate'));
    $pages[] = array('slug' => 'ns-real-estate-license-keys', 'name' => esc_html__('License', 'ns-real-estate'));
    $pages[] = array('slug' => 'ns-real-estate-help', 'name' => esc_html__('Help', 'ns-real-estate'));
    return $pages;
}

function ns_real_estate_settings_page() {
    $page_name = 'NightShift Real Estate';
    $settings_group = 'ns-real-estate-settings-group';
    $pages = ns_real_estate_get_admin_pages();
    $display_actions = 'true';
    $content = ns_real_estate_settings_page_content();
    $content_class = null;
    $content_nav = array(
        array('name' => 'Properties', 'link' => '#properties', 'icon' => 'fa-home'),
        array('name' => 'Agents', 'link' => '#agents', 'icon' => 'fa-users'),
        array('name' => 'Maps', 'link' => '#maps', 'icon' => 'fa-map'),
        array('name' => 'Members', 'link' => '#members', 'icon' => 'fa-key'),
        array('name' => 'Currency & Numbers', 'link' => '#currency', 'icon' => 'fa-money-bill-alt'),
    );
    
    //add alerts
    $alerts = array();
    if(!current_theme_supports('ns-real-estate')) {
        $current_theme = wp_get_theme();
        $incompatible_theme_alert = ns_basics_admin_alert('info', esc_html__('The active theme ('.$current_theme->name.') does not declare support for NS Real Estate.', 'ns-real-estate'), $action = '#', $action_text = esc_html__('Get a compatible theme', 'ns-real-estate'), true); 
        $alerts[] = $incompatible_theme_alert; 
    }

    $google_maps_api = esc_attr(get_option('ns_real_estate_google_maps_api'));
    if(empty($google_maps_api)) {
        $google_api_key_alert = ns_basics_admin_alert('warning', esc_html__('Please provide a Google Maps API Key within the Maps tab.', 'ns-real-estate'), $action = null, $action_text = null, true);
        $alerts[] = $google_api_key_alert; 
    }

    $properties_page = esc_attr(get_option('ns_properties_page'));
    if(empty($properties_page)) {
        $properties_page_alert = ns_basics_admin_alert('warning', esc_html__('You have not set your properties listing page. Go to Properties > Property Listing Options, to set this field.', 'ns-real-estate'), $action = null, $action_text = null, true);
        $alerts[] = $properties_page_alert; 
    }

    echo ns_basics_admin_page($page_name, $settings_group, $pages, $display_actions, $content, $content_class, $content_nav, $alerts);
} 

function ns_real_estate_settings_page_content() {
    ob_start(); ?>

    <div id="properties" class="tab-content">
        <h2><?php echo esc_html_e('Properties Settings', 'ns-real-estate'); ?></h2>

        <div class="accordion ns-accordion">
            <h3 class="accordion-tab"><i class="fa fa-chevron-right icon"></i> <?php echo esc_html_e('Property URL Options', 'ns-real-estate'); ?></h3>
            <div>
                <p class="admin-module-note"><?php esc_html_e('After changing slugs, make sure you re-save your permalinks in Settings > Permalinks.', 'ns-real-estate'); ?></p>
                <br/>

                <table class="admin-module admin-module-property-slug">
                    <tr>
                        <td class="admin-module-label">
                            <label><?php echo esc_html_e('Properties Slug', 'ns-real-estate'); ?></label>
                            <span class="admin-module-note"><?php esc_html_e('Default: properties', 'ns-real-estate'); ?></span>
                        </td>
                        <td class="admin-module-field">
                            <span><?php echo esc_url(home_url('/')); ?></span> <input type="text" style="width:150px;" id="property_detail_slug" name="ns_property_detail_slug" value="<?php echo esc_attr( get_option('ns_property_detail_slug', 'properties') ); ?>" />
                        </td>
                    </tr>
                </table>

                <table class="admin-module">
                    <tr>
                        <td class="admin-module-label">
                            <label><?php echo esc_html_e('Property Type Taxonomy Slug', 'ns-real-estate'); ?></label>
                            <span class="admin-module-note"><?php esc_html_e('Default: property-type', 'ns-real-estate'); ?></span>
                        </td>
                        <td class="admin-module-field">
                            <span><?php echo esc_url(home_url('/')); ?></span> <input type="text" style="width:150px;" id="property_type_tax_slug" name="ns_property_type_tax_slug" value="<?php echo esc_attr( get_option('ns_property_type_tax_slug', 'property-type') ); ?>" />
                        </td>
                    </tr>
                </table>

                <table class="admin-module">
                    <tr>
                        <td class="admin-module-label">
                            <label><?php echo esc_html_e('Property Status Taxonomy Slug', 'ns-real-estate'); ?></label>
                            <span class="admin-module-note"><?php esc_html_e('Default: property-status', 'ns-real-estate'); ?></span>
                        </td>
                        <td class="admin-module-field">
                            <span><?php echo esc_url(home_url('/')); ?></span> <input type="text" style="width:150px;" id="property_status_tax_slug" name="ns_property_status_tax_slug" value="<?php echo esc_attr( get_option('ns_property_status_tax_slug', 'property-status') ); ?>" />
                        </td>
                    </tr>
                </table>

                <table class="admin-module">
                    <tr>
                        <td class="admin-module-label">
                            <label><?php echo esc_html_e('Property Location Taxonomy Slug', 'ns-real-estate'); ?></label>
                            <span class="admin-module-note"><?php esc_html_e('Default: property-location', 'ns-real-estate'); ?></span>
                        </td>
                        <td class="admin-module-field">
                            <span><?php echo esc_url(home_url('/')); ?></span> <input type="text" style="width:150px;" id="property_location_tax_slug" name="ns_property_location_tax_slug" value="<?php echo esc_attr( get_option('ns_property_location_tax_slug', 'property-location') ); ?>" />
                        </td>
                    </tr>
                </table>

                <table class="admin-module no-border">
                    <tr>
                        <td class="admin-module-label">
                            <label><?php echo esc_html_e('Property Amenities Taxonomy Slug', 'ns-real-estate'); ?></label>
                            <span class="admin-module-note"><?php esc_html_e('Default: property-amenity', 'ns-real-estate'); ?></span>
                        </td>
                        <td class="admin-module-field">
                            <span><?php echo esc_url(home_url('/')); ?></span> <input type="text" style="width:150px;" id="property_amenities_tax_slug" name="ns_property_amenities_tax_slug" value="<?php echo esc_attr( get_option('ns_property_amenities_tax_slug', 'property-amenity') ); ?>" />
                        </td>
                    </tr>
                </table>
            </div>
        </div><!-- end property url options -->

        <div class="accordion ns-accordion">
            <h3 class="accordion-tab"><i class="fa fa-chevron-right icon"></i> <?php echo esc_html_e('Property Filter Options', 'ns-real-estate'); ?></h3>
            <div>

                <table class="admin-module">
                    <tr>
                        <td class="admin-module-label"><label><?php echo esc_html_e('Display Property Filter in Page Banners', 'ns-real-estate'); ?></label></td>
                        <td class="admin-module-field">
                            <div class="toggle-switch" title="<?php if(get_option('ns_property_filter_display', 'true') == 'true') { esc_html_e('Active', 'ns-real-estate'); } else { esc_html_e('Disabled', 'ns-real-estate'); } ?>">
                                <input type="checkbox" name="ns_property_filter_display" value="true" class="toggle-switch-checkbox" id="property_filter_display" <?php checked('true', get_option('ns_property_filter_display', 'true'), true) ?>>
                                <label class="toggle-switch-label" for="property_filter_display"><?php if(get_option('ns_property_filter_display', 'true') == 'true') { echo '<span class="on">'.esc_html__('On', 'ns-real-estate').'</span>'; } else { echo '<span>'.esc_html__('Off', 'ns-real-estate').'</span>'; } ?></label>
                            </div>
                        </td>
                    </tr>
                </table>

                <table class="admin-module no-border">
                    <tr>
                        <td class="admin-module-label">
                            <label><?php esc_html_e('Default Banner Filter', 'ns-real-estate'); ?></label>
                            <span class="admin-module-note">
                                <?php esc_html_e('This can be overriden on individual pages from the page settings meta box.', 'ns-real-estate'); ?>
                            </span>
                        </td>
                        <td class="admin-module-field">
                            <select name="ns_property_filter_id" id="property_filter_id">
                                <option value=""></option>
                                <?php
                                    $filter_listing_args = array(
                                        'post_type' => 'ns-property-filter',
                                        'posts_per_page' => -1
                                        );
                                    $filter_listing_query = new WP_Query( $filter_listing_args );
                                ?>
                                <?php if ( $filter_listing_query->have_posts() ) : while ( $filter_listing_query->have_posts() ) : $filter_listing_query->the_post(); ?>
                                    <option value="<?php echo get_the_id(); ?>" <?php if(get_option('ns_property_filter_id') == get_the_id()) { echo 'selected'; } ?>><?php echo get_the_title().' (#'.get_the_id().')'; ?></option>
                                    <?php wp_reset_postdata(); ?>
                                <?php endwhile; ?>
                                <?php else: ?>
                                <?php endif; ?>
                            </select>
                            <div><br/><a href="<?php echo admin_url('edit.php?post_type=ns-property-filter'); ?>" target="_blank"><i class="fa fa-cog"></i> <?php esc_html_e('Manage property filters', 'ns-real-estate'); ?></a></div>
                        </td>
                    </tr>
                </table>

            </div>
        </div><!-- end property filter options -->

        <div class="accordion ns-accordion">
            <h3 class="accordion-tab"><i class="fa fa-chevron-right icon"></i> <?php echo esc_html_e('Property Listing Options', 'ns-real-estate'); ?></h3>
            <div>

                <table class="admin-module">
                    <tr>
                        <td class="admin-module-label"><label><?php echo esc_html_e('Select Your Property Listings Page', 'ns-real-estate'); ?></label></td>
                        <td class="admin-module-field">
                            <select name="ns_properties_page">
                                <option value="">
                                <?php echo esc_attr( esc_html__( 'Select page', 'ns-real-estate' ) ); ?></option> 
                                    <?php 
                                    $pages = get_pages(); 
                                    foreach ( $pages as $page ) { ?>
                                    <option value="<?php echo get_page_link( $page->ID ); ?>" <?php if(esc_attr(get_option('ns_properties_page')) == get_page_link( $page->ID )) { echo 'selected'; } ?>>
                                        <?php echo esc_attr($page->post_title); ?>
                                    </option>
                                <?php } ?>
                            </select>
                        </td>
                    </tr>
                </table>

                <table class="admin-module">
                    <tr>
                        <td class="admin-module-label"><label><?php echo esc_html_e('Number of Properties Per Page', 'ns-real-estate'); ?></label></td>
                        <td class="admin-module-field"><input type="number" id="num_properties_per_page" name="ns_num_properties_per_page" value="<?php echo esc_attr( get_option('ns_num_properties_per_page', 12) ); ?>" /></td>
                    </tr>
                </table>

                <table class="admin-module">
                    <tr>
                        <td class="admin-module-label"><label><?php echo esc_html_e('Properties Taxonomy Layout', 'ns-real-estate'); ?></label></td>
                        <td class="admin-module-field">
                            <p><input type="radio" id="properties_default_layout" name="ns_properties_default_layout" value="grid" <?php if(esc_attr( get_option('ns_properties_default_layout', 'grid')) == 'grid') { echo 'checked'; } ?> /><?php echo esc_html_e('Grid', 'ns-real-estate'); ?></p>
                            <p><input type="radio" id="properties_default_layout" name="ns_properties_default_layout" value="row" <?php if(esc_attr( get_option('ns_properties_default_layout', 'grid')) == 'row') { echo 'checked'; } ?> /><?php echo esc_html_e('Row', 'ns-real-estate'); ?></p>
                        </td>
                    </tr>
                </table>

                <table class="admin-module">
                    <tr>
                        <td class="admin-module-label">
                            <label><?php echo esc_html_e('Display Listing Header?', 'ns-real-estate'); ?></label>
                            <div class="admin-module-note"><?php echo esc_html_e('Toggle on/off the filter options that display directly above property listings.', 'ns-real-estate'); ?></div>
                        </td>
                        <td class="admin-module-field">
                            <div class="toggle-switch" title="<?php if(get_option('ns_property_listing_header_display', 'true') == 'true') { esc_html_e('Active', 'ns-real-estate'); } else { esc_html_e('Disabled', 'ns-real-estate'); } ?>">
                                <input type="checkbox" name="ns_property_listing_header_display" value="true" class="toggle-switch-checkbox" id="property_listing_header_display" <?php checked('true', get_option('ns_property_listing_header_display', 'true'), true) ?>>
                                <label class="toggle-switch-label" for="property_listing_header_display"><?php if(get_option('ns_property_listing_header_display', 'true') == 'true') { echo '<span class="on">'.esc_html__('On', 'ns-real-estate').'</span>'; } else { echo '<span>'.esc_html__('Off', 'ns-real-estate').'</span>'; } ?></label>
                            </div>
                        </td>
                    </tr>
                </table>

                <table class="admin-module">
                    <tr>
                        <td class="admin-module-label">
                            <label><?php echo esc_html_e('Default Sort By', 'ns-real-estate'); ?></label>
                            <div class="admin-module-note"><?php echo esc_html_e('Choose the default sorting for property listings.', 'ns-real-estate'); ?></div>
                        </td>
                        <td class="admin-module-field">
                            <select name="ns_property_listing_default_sortby">
                                <option value="date_desc" <?php if(esc_attr( get_option('ns_property_listing_default_sortby', 'date_desc')) == 'date_desc') { echo 'selected'; } ?>><?php echo esc_html_e('New to Old', 'ns-real-estate'); ?></option>
                                <option value="date_asc" <?php if(esc_attr( get_option('ns_property_listing_default_sortby', 'date_desc')) == 'date_asc') { echo 'selected'; } ?>><?php echo esc_html_e('Old to New', 'ns-real-estate'); ?></option>
                                <option value="price_desc" <?php if(esc_attr( get_option('ns_property_listing_default_sortby', 'date_desc')) == 'price_desc') { echo 'selected'; } ?>><?php echo esc_html_e('Price (High to Low)', 'ns-real-estate'); ?></option>
                                <option value="price_asc" <?php if(esc_attr( get_option('ns_property_listing_default_sortby', 'date_desc')) == 'price_asc') { echo 'selected'; } ?>><?php echo esc_html_e('Price (Low to High)', 'ns-real-estate'); ?></option>
                            </select>
                        </td>
                    </tr>
                </table>

                <table class="admin-module">
                    <tr>
                        <td class="admin-module-label">
                            <label><?php echo esc_html_e('Hard crop property listing featured images?', 'ns-real-estate'); ?></label>
                            <span class="admin-module-note"><?php esc_html_e('If active, property listing thumbnails will be cropped to 800 x 600 pixels.', 'ns-real-estate'); ?></span>
                        </td>
                        <td class="admin-module-field">
                            <div class="toggle-switch" title="<?php if(get_option('ns_property_listing_crop', 'true') == 'true') { esc_html_e('Active', 'ns-real-estate'); } else { esc_html_e('Disabled', 'ns-real-estate'); } ?>">
                                <input type="checkbox" name="ns_property_listing_crop" value="true" class="toggle-switch-checkbox" id="property_listing_crop" <?php checked('true', get_option('ns_property_listing_crop', 'true'), true) ?>>
                                <label class="toggle-switch-label" for="property_listing_crop"><?php if(get_option('ns_property_listing_crop', 'true') == 'true') { echo '<span class="on">'.esc_html__('On', 'ns-real-estate').'</span>'; } else { echo '<span>'.esc_html__('Off', 'ns-real-estate').'</span>'; } ?></label>
                            </div>
                        </td>
                    </tr>
                </table>

                <table class="admin-module">
                    <tr>
                        <td class="admin-module-label"><label><?php echo esc_html_e('Display Time Stamp?', 'ns-real-estate'); ?></label></td>
                        <td class="admin-module-field">
                            <div class="toggle-switch" title="<?php if(get_option('ns_property_listing_display_time', 'true') == 'true') { esc_html_e('Active', 'ns-real-estate'); } else { esc_html_e('Disabled', 'ns-real-estate'); } ?>">
                                <input type="checkbox" name="ns_property_listing_display_time" value="true" class="toggle-switch-checkbox" id="property_listing_display_time" <?php checked('true', get_option('ns_property_listing_display_time', 'true'), true) ?>>
                                <label class="toggle-switch-label" for="property_listing_display_time"><?php if(get_option('ns_property_listing_display_time', 'true') == 'true') { echo '<span class="on">'.esc_html__('On', 'ns-real-estate').'</span>'; } else { echo '<span>'.esc_html__('Off', 'ns-real-estate').'</span>'; } ?></label>
                            </div>
                        </td>
                    </tr>
                </table>

                <table class="admin-module">
                    <tr>
                        <td class="admin-module-label"><label><?php echo esc_html_e('Allow users to favorite properties?', 'ns-real-estate'); ?></label></td>
                        <td class="admin-module-field">
                            <div class="toggle-switch" title="<?php if(get_option('ns_property_listing_display_favorite', 'true') == 'true') { esc_html_e('Active', 'ns-real-estate'); } else { esc_html_e('Disabled', 'ns-real-estate'); } ?>">
                                <input type="checkbox" name="ns_property_listing_display_favorite" value="true" class="toggle-switch-checkbox" id="property_listing_display_favorite" <?php checked('true', get_option('ns_property_listing_display_favorite', 'true'), true) ?>>
                                <label class="toggle-switch-label" for="property_listing_display_favorite"><?php if(get_option('ns_property_listing_display_favorite', 'true') == 'true') { echo '<span class="on">'.esc_html__('On', 'ns-real-estate').'</span>'; } else { echo '<span>'.esc_html__('Off', 'ns-real-estate').'</span>'; } ?></label>
                            </div>
                        </td>
                    </tr>
                </table>

                <table class="admin-module no-border">
                    <tr>
                        <td class="admin-module-label"><label><?php echo esc_html_e('Allow users to share properties?', 'ns-real-estate'); ?></label></td>
                        <td class="admin-module-field">
                            <div class="toggle-switch" title="<?php if(get_option('ns_property_listing_display_share', 'true') == 'true') { esc_html_e('Active', 'ns-real-estate'); } else { esc_html_e('Disabled', 'ns-real-estate'); } ?>">
                                <input type="checkbox" name="ns_property_listing_display_share" value="true" class="toggle-switch-checkbox" id="property_listing_display_share" <?php checked('true', get_option('ns_property_listing_display_share', 'true'), true) ?>>
                                <label class="toggle-switch-label" for="property_listing_display_share"><?php if(get_option('ns_property_listing_display_share', 'true') == 'true') { echo '<span class="on">'.esc_html__('On', 'ns-real-estate').'</span>'; } else { echo '<span>'.esc_html__('Off', 'ns-real-estate').'</span>'; } ?></label>
                            </div>
                        </td>
                    </tr>
                </table>
                            
            </div>
        </div><!-- end property listing options -->

        <div class="accordion ns-accordion">
            <h3 class="accordion-tab"><i class="fa fa-chevron-right icon"></i> <?php echo esc_html_e('Property Detail Options', 'ns-real-estate'); ?></h3>
            <div>

                <table class="admin-module">
                    <tr>
                        <td class="admin-module-label"><label><?php echo esc_html_e('Property Detail Template', 'ns-real-estate'); ?></label></td>
                        <td class="admin-module-field">
                            <p><input type="radio" id="property_detail_template_classic" name="ns_property_detail_template" value="classic" <?php if(esc_attr( get_option('ns_property_detail_template', 'classic')) == 'classic') { echo 'checked'; } ?> /><?php echo esc_html_e('Classic', 'ns-real-estate'); ?></p>
                            <p><input type="radio" id="property_detail_template_full" name="ns_property_detail_template" value="full" <?php if(esc_attr( get_option('ns_property_detail_template', 'classic')) == 'full') { echo 'checked'; } ?> /><?php echo esc_html_e('Full Width Gallery', 'ns-real-estate'); ?></p>
                            <p><input type="radio" id="property_detail_template_agent_contact" name="ns_property_detail_template" value="agent_contact" <?php if(esc_attr( get_option('ns_property_detail_template', 'classic')) == 'agent_contact') { echo 'checked'; } ?> /><?php echo esc_html_e('Boxed Gallery', 'ns-real-estate'); ?></p>
                            <p class="admin-module-property-detail-display-gallery-agent <?php if(get_option('ns_property_detail_template', 'classic') != 'agent_contact') { echo 'hide-soft'; } ?>">
                                <input type="checkbox" id="property_detail_display_gallery_agent" name="ns_property_detail_display_gallery_agent" value="true" <?php checked('true', get_option('ns_property_detail_display_gallery_agent', 'true'), true) ?> />
                                <label for="property_detail_display_gallery_agent"><?php echo esc_html_e('Display agent contact information in gallery?', 'ns-real-estate'); ?></label>
                            </p>
                        </td>
                    </tr>
                </table>

                <table class="admin-module">
                    <tr>
                        <td class="admin-module-label"><label><?php echo esc_html_e('Select the default page layout for property detail pages', 'ns-real-estate'); ?></label></td>
                        <td class="admin-module-field">
                            <?php $property_detail_default_layout = get_option('ns_property_detail_default_layout', 'right sidebar'); ?>
                            <table class="left right-bump">
                            <tr>
                            <td><input type="radio" name="ns_property_detail_default_layout" id="page_layout_full" value="full" <?php if($property_detail_default_layout == 'full') { echo 'checked="checked"'; } ?> /></td>
                            <td><img class="sidebar-icon" src="<?php echo plugins_url('/ns-basics/images/full-width-icon.png'); ?>" alt="" /></td>
                            </tr>
                            <tr><td></td><td><?php echo esc_html_e('Full Width', 'ns-real-estate'); ?></td></tr>
                            </table>

                            <table class="left">
                            <tr>
                            <td><input type="radio" name="ns_property_detail_default_layout" id="page_layout_right_sidebar" value="right sidebar" <?php if($property_detail_default_layout == 'right sidebar') { echo 'checked="checked"'; } ?> /></td>
                            <td><img class="sidebar-icon" src="<?php echo plugins_url('/ns-basics/images/right-sidebar-icon.png'); ?>" alt="" /></td>
                            </tr>
                            <tr><td></td><td><?php echo esc_html_e('Right Sidebar', 'ns-real-estate'); ?></td></tr>
                            </table>

                            <table class="left">
                            <tr>
                            <td><input type="radio" name="ns_property_detail_default_layout" id="page_layout_left_sidebar" value="left sidebar" <?php if($property_detail_default_layout == 'left sidebar') { echo 'checked="checked"'; } ?> /></td>
                            <td><img class="sidebar-icon" src="<?php echo plugins_url('/ns-basics/images/left-sidebar-icon.png'); ?>" alt="" /></td>
                            </tr>
                            <tr><td></td><td><?php echo esc_html_e('Left Sidebar', 'ns-real-estate'); ?></td></tr>
                            </table>
                            <div class="clear"></div>
                        </td>
                    </tr>
                </table>

                <table class="admin-module">
                    <tr>
                        <td class="admin-module-label"><label><?php echo esc_html_e('Show Property ID on Front-End', 'ns-real-estate'); ?></label></td>
                        <td class="admin-module-field">
                            <div class="toggle-switch" title="<?php if(get_option('ns_property_detail_id') == 'true') { esc_html_e('Active', 'ns-real-estate'); } else { esc_html_e('Disabled', 'ns-real-estate'); } ?>">
                                <input type="checkbox" name="ns_property_detail_id" value="true" class="toggle-switch-checkbox" id="property_detail_id" <?php checked('true', get_option('ns_property_detail_id'), true) ?>>
                                <label class="toggle-switch-label" for="property_detail_id"><?php if(get_option('ns_property_detail_id') == 'true') { echo '<span class="on">'.esc_html__('On', 'ns-real-estate').'</span>'; } else { echo '<span>'.esc_html__('Off', 'ns-real-estate').'</span>'; } ?></label>
                            </div>
                        </td>
                    </tr>
                </table>

                <div class="admin-module no-border">
                    <div class="admin-module-label"><label><?php echo esc_html_e('Property Detail Sections', 'ns-real-estate'); ?> <span class="admin-module-note"><?php echo esc_html_e('(Drag & drop to rearrange order)', 'ns-real-estate'); ?></span></label></div>
                    <ul class="sortable-list property-detail-items-list">
                        <?php
                        $property_detail_items_default = ns_real_estate_load_default_property_detail_items();
                        $property_detail_items = get_option('ns_property_detail_items', $property_detail_items_default);
                        $count = 0;

                        foreach($property_detail_items as $value) { ?>
                            <?php
                                if(isset($value['name'])) { $name = $value['name']; }
                                if(isset($value['label'])) { $label = $value['label']; }
                                if(isset($value['slug'])) { $slug = $value['slug']; }
                                if(isset($value['active']) && $value['active'] == 'true') { $active = 'true'; } else { $active = 'false'; }
                                if(isset($value['sidebar']) && $value['sidebar'] == 'true') { $sidebar = 'true'; } else { $sidebar = 'false'; }
                                
                                //If item is an add-on, check if it is active
                                if(isset($value['add_on'])) { 
                                    if(ns_basics_is_paid_plugin_active($value['add_on'])) { $add_on = 'true'; } else { $add_on = 'false'; }
                                } else {
                                    $add_on = 'true'; 
                                }
                            ?>

                            <?php if($add_on == 'true') { ?>
                            <li class="sortable-item">

                                <div class="sortable-item-header">
                                    <div class="sort-arrows"><i class="fa fa-bars"></i></div>
                                    <div class="toggle-switch" title="<?php if($active == 'true') { esc_html_e('Active', 'ns-real-estate'); } else { esc_html_e('Disabled', 'ns-real-estate'); } ?>">
                                        <input type="checkbox" name="ns_property_detail_items[<?php echo $count; ?>][active]" value="true" class="toggle-switch-checkbox" id="property_detail_item_<?php echo esc_attr($slug); ?>" <?php checked('true', $active, true) ?>>
                                        <label class="toggle-switch-label" for="property_detail_item_<?php echo esc_attr($slug); ?>"><?php if($active == 'true') { echo '<span class="on">'.esc_html__('On', 'ns-real-estate').'</span>'; } else { echo '<span>'.esc_html__('Off', 'ns-real-estate').'</span>'; } ?></label>
                                    </div>
                                    <span class="sortable-item-title"><?php echo esc_attr($name); ?></span><div class="clear"></div>
                                    <input type="hidden" name="ns_property_detail_items[<?php echo $count; ?>][name]" value="<?php echo $name; ?>" />
                                    <input type="hidden" name="ns_property_detail_items[<?php echo $count; ?>][slug]" value="<?php echo $slug; ?>" />
                                    <?php if(isset($value['add_on'])) { ?><input type="hidden" name="ns_property_detail_items[<?php echo $count; ?>][add_on]" value="<?php echo $value['add_on']; ?>" /><?php } ?>
                                </div>

                                <a href="#advanced-options-content-<?php echo esc_attr($slug); ?>" class="sortable-item-action advanced-options-toggle right"><i class="fa fa-gear"></i> <?php echo esc_html_e('Additional Settings', 'ns-real-estate'); ?></a>
                                <div id="advanced-options-content-<?php echo esc_attr($slug); ?>" class="advanced-options-content hide-soft">    
                                    
                                    <table class="admin-module">
                                        <tr>
                                            <td class="admin-module-label"><label><?php esc_html_e('Label:', 'ns-real-estate'); ?></label></td>
                                            <td class="admin-module-field">
                                                <input type="text" class="sortable-item-label-input" name="ns_property_detail_items[<?php echo $count; ?>][label]" value="<?php echo $label; ?>" />
                                            </td>
                                        </tr>
                                    </table>
                                
                                    <table class="admin-module">
                                        <tr>
                                            <td class="admin-module-label"><label><?php esc_html_e('Display in Sidebar', 'ns-real-estate'); ?></label></td>
                                            <td class="admin-module-field">
                                                <input type="checkbox" name="ns_property_detail_items[<?php echo $count; ?>][sidebar]" value="true" <?php checked('true', $sidebar, true) ?> />
                                            </td>
                                        </tr>
                                    </table>

                                    <?php if($slug == 'amenities') { ?>
                                        <table class="admin-module no-border">
                                            <tr>
                                                <td class="admin-module-label"><label><?php echo esc_html_e('Hide empty amenities?', 'ns-real-estate'); ?></label></td>
                                                <td class="admin-module-field">
                                                    <input type="checkbox" id="property_detail_amenities_hide_empty" name="ns_property_detail_amenities_hide_empty" value="true" <?php checked('true', get_option('ns_property_detail_amenities_hide_empty'), true) ?> />
                                                </td>
                                            </tr>
                                        </table>
                                    <?php } ?> 

                                    <?php if($slug == 'location') { ?>
                                        <table class="admin-module">
                                            <tr>
                                                <td class="admin-module-label"><label><?php echo esc_html_e('Map Zoom', 'ns-real-estate'); ?></label></td>
                                                <td class="admin-module-field">
                                                    <select name="ns_property_detail_map_zoom">
                                                        <option value="1" <?php if(esc_attr(get_option('ns_property_detail_map_zoom', 13)) == '1') { echo 'selected'; } ?>>1</option>
                                                        <option value="2" <?php if(esc_attr(get_option('ns_property_detail_map_zoom', 13)) == '2') { echo 'selected'; } ?>>2</option>
                                                        <option value="3" <?php if(esc_attr(get_option('ns_property_detail_map_zoom', 13)) == '3') { echo 'selected'; } ?>>3</option>
                                                        <option value="4" <?php if(esc_attr(get_option('ns_property_detail_map_zoom', 13)) == '4') { echo 'selected'; } ?>>4</option>
                                                        <option value="5" <?php if(esc_attr(get_option('ns_property_detail_map_zoom', 13)) == '5') { echo 'selected'; } ?>>5</option>
                                                        <option value="6" <?php if(esc_attr(get_option('ns_property_detail_map_zoom', 13)) == '6') { echo 'selected'; } ?>>6</option>
                                                        <option value="7" <?php if(esc_attr(get_option('ns_property_detail_map_zoom', 13)) == '7') { echo 'selected'; } ?>>7</option>
                                                        <option value="8" <?php if(esc_attr(get_option('ns_property_detail_map_zoom', 13)) == '8') { echo 'selected'; } ?>>8</option>
                                                        <option value="9" <?php if(esc_attr(get_option('ns_property_detail_map_zoom', 13)) == '9') { echo 'selected'; } ?>>9</option>
                                                        <option value="10" <?php if(esc_attr(get_option('ns_property_detail_map_zoom', 13)) == '10') { echo 'selected'; } ?>>10</option>
                                                        <option value="11" <?php if(esc_attr(get_option('ns_property_detail_map_zoom', 13)) == '11') { echo 'selected'; } ?>>11</option>
                                                        <option value="12" <?php if(esc_attr(get_option('ns_property_detail_map_zoom', 13)) == '12') { echo 'selected'; } ?>>12</option>
                                                        <option value="13" <?php if(esc_attr(get_option('ns_property_detail_map_zoom', 13)) == '13') { echo 'selected'; } ?>>13</option>
                                                        <option value="14" <?php if(esc_attr(get_option('ns_property_detail_map_zoom', 13)) == '14') { echo 'selected'; } ?>>14</option>
                                                        <option value="15" <?php if(esc_attr(get_option('ns_property_detail_map_zoom', 13)) == '15') { echo 'selected'; } ?>>15</option>
                                                    </select>
                                                </td>
                                            </tr>
                                        </table>

                                        <table class="admin-module">
                                            <tr>
                                                <td class="admin-module-label"><label><?php echo esc_html_e('Map Height', 'ns-real-estate'); ?></label></td>
                                                <td class="admin-module-field">
                                                    <input type="number" id="property_detail_map_height" name="ns_property_detail_map_height" value="<?php echo esc_attr( get_option('ns_property_detail_map_height', 250) ); ?>" /> Px
                                                </td>
                                            </tr>
                                        </table>
                                    <?php } ?>

                                    <?php if($slug == 'agent_info') { ?>
                                        <table class="admin-module">
                                            <tr>
                                                <td class="admin-module-label">
                                                    <label><?php echo esc_html_e('Display agent contact form underneath agent information?', 'ns-real-estate'); ?></label>
                                                    <span class="admin-module-note"><?php esc_html_e('Configure the agent contact form options in Theme Options > Agents > Agent Detail Options.', 'ns-real-estate'); ?></span>
                                                </td>
                                                <td class="admin-module-field">
                                                    <input type="checkbox" id="property_detail_agent_contact_form" name="ns_property_detail_agent_contact_form" value="true" <?php checked('true', get_option('ns_property_detail_agent_contact_form'), true) ?> />
                                                </td>
                                            </tr>
                                        </table>
                                    <?php } ?>
                                </div>

                            </li>
                            <?php } ?>
                            <?php $count++; ?>
                        <?php } ?>
                    </ul>
                </div>
            </div>
        </div><!-- end property detail options -->

        <div class="accordion ns-accordion" id="accordion-custom-fields">
            <h3 class="accordion-tab"><i class="fa fa-chevron-right icon"></i> <?php echo esc_html_e('Property Custom Fields', 'ns-real-estate'); ?></h3>
            <div>
                <div class="admin-module admin-module-custom-fields admin-module-custom-fields-theme-options no-border">
                    <div class="sortable-list custom-fields-container">
                        <?php 
                            $custom_fields = get_option('ns_property_custom_fields');
                            if(!empty($custom_fields)) {  
                                $count = 0;                      
                                foreach ($custom_fields as $custom_field) {

                                    if(!is_array($custom_field)) { 
                                        $custom_field = array( 
                                            'id' => strtolower(str_replace(' ', '_', $custom_field)),
                                            'name' => $custom_field, 
                                            'type' => 'text',
                                            'front_end' => 'true',
                                        ); 
                                    } ?>
                                    <table class="custom-field-item sortable-item"> 
                                        <tr>
                                            <td>
                                                <label><strong><?php esc_html_e('Field Name:', 'ns-real-estate'); ?></strong></label> 
                                                <input type="text" class="custom-field-name-input" name="ns_property_custom_fields[<?php echo $count; ?>][name]" value="<?php echo $custom_field['name']; ?>" />
                                                <input type="hidden" class="custom-field-id" name="ns_property_custom_fields[<?php echo $count; ?>][id]" value="<?php echo $custom_field['id']; ?>" readonly />
                                                <div class="edit-custom-field-form hide-soft">

                                                    <table class="admin-module">
                                                        <tr>
                                                            <td class="admin-module-label"><label><?php esc_html_e('Field Type', 'ns-real-estate'); ?></label></td>
                                                            <td class="admin-module-field">
                                                                <select class="custom-field-type-select" name="ns_property_custom_fields[<?php echo $count; ?>][type]">
                                                                    <option value="text" <?php if(isset($custom_field['type']) && $custom_field['type'] == 'text') { echo 'selected'; } ?>><?php esc_html_e('Text Input', 'ns-real-estate'); ?></option>
                                                                    <option value="num" <?php if(isset($custom_field['type']) && $custom_field['type'] == 'num') { echo 'selected'; } ?>><?php esc_html_e('Number Input', 'ns-real-estate'); ?></option>
                                                                    <option value="select" <?php if(isset($custom_field['type']) && $custom_field['type'] == 'select') { echo 'selected'; } ?>><?php esc_html_e('Select Dropdown', 'ns-real-estate'); ?></option>
                                                                </select>
                                                            </td>
                                                        </tr>
                                                    </table>

                                                    <table class="admin-module admin-module-select-options <?php if($custom_field['type'] != 'select') { echo 'hide-soft'; } ?>">
                                                        <tr>
                                                            <td class="admin-module-label"><label><?php esc_html_e('Select Options:', 'ns-real-estate'); ?></label></td>
                                                            <td class="admin-module-field">
                                                                <div class="custom-field-select-options-container">
                                                                    <?php 
                                                                        if(isset($custom_field['select_options'])) { $selectOptions = $custom_field['select_options']; } else { $selectOptions =  ''; }
                                                                        if(!empty($selectOptions)) {
                                                                            foreach($selectOptions as $option) {
                                                                                echo '<p><input type="text" name="ns_property_custom_fields['.$count.'][select_options][]" value="'.$option.'" /><span class="delete-custom-field-select"><i class="fa fa-times"></i></span></p>';
                                                                            }
                                                                        } ?>
                                                                     </div>
                                                                    <div class="button add-custom-field-select"><?php esc_html_e('Add Select Option', 'ns-real-estate'); ?></div>
                                                                </div>
                                                            </td>
                                                        </tr>
                                                    </table>

                                                    <table class="admin-module no-border">
                                                        <tr>
                                                            <td class="admin-module-label"><label><?php esc_html_e('Display in Front-end Property Submit Form', 'ns-real-estate'); ?></label></td>
                                                            <td class="admin-module-field">
                                                                <input type="checkbox" value="true" name="ns_property_custom_fields[<?php echo $count; ?>][front_end]" <?php if(isset($custom_field['front_end'])) { echo 'checked'; } ?> />
                                                            </td>
                                                        </tr>
                                                    </table>

                                                </div>
                                            </td>
                                            <td class="custom-field-action edit-custom-field"><div class="sortable-item-action"><i class="fa fa-cog"></i> <?php echo esc_html_e('Edit', 'ns-real-estate'); ?></div></td>
                                            <td class="custom-field-action delete-custom-field"><div class="sortable-item-action"><i class="fa fa-trash"></i> <?php echo esc_html_e('Remove', 'ns-real-estate'); ?></div></td>
                                        </tr>
                                    </table> 
                                    <?php $count++; ?> 
                                <?php }
                            } else { ?> <span class="admin-module-note"><?php esc_html_e('No custom fields have been created.', 'ns-real-estate'); ?></span><br/>  <?php } ?>
                    </div>

                    <div class="new-custom-field">
                        <div class="new-custom-field-form hide-soft">
                            <input type="text" style="display:block;" class="add-custom-field-value" placeholder="<?php esc_html_e('Field Name', 'ns-real-estate'); ?>" />
                            <span class="admin-button add-custom-field"><?php esc_html_e('Add Field', 'ns-real-estate'); ?></span>
                            <span class="button button-secondary cancel-custom-field"><i class="fa fa-times"></i> <?php esc_html_e('Cancel', 'ns-real-estate'); ?></span>
                        </div>
                        <span class="admin-button new-custom-field-toggle"><i class="fa fa-plus"></i> <?php esc_html_e('Create New Field', 'ns-real-estate'); ?></span>
                    </div>
                </div>
            </div>
        </div><!-- end property custom fields -->

    </div><!-- end propery settings -->

    <div id="agents" class="tab-content">
        <h2><?php echo esc_html_e('Agent Settings', 'ns-real-estate'); ?></h2>

        <div class="accordion ns-accordion">
            <h3 class="accordion-tab"><i class="fa fa-chevron-right icon"></i> <?php echo esc_html_e('Agent Listing Options', 'ns-real-estate'); ?></h3>
            <div>

                <table class="admin-module">
                    <tr>
                        <td class="admin-module-label">
                            <label><?php echo esc_html_e('Agents Slug', 'ns-real-estate'); ?></label>
                            <span class="admin-module-note"><?php esc_html_e('After changing the slug, make sure you re-save your permalinks in Settings > Permalinks. The default slug is agents.', 'ns-real-estate'); ?></span>
                        </td>
                        <td class="admin-module-field">
                            <span><?php echo esc_url(home_url('/')); ?></span> <input type="text" style="width:150px;" id="agent_detail_slug" name="ns_agent_detail_slug" value="<?php echo esc_attr( get_option('ns_agent_detail_slug', 'agents') ); ?>" />
                        </td>
                    </tr>
                </table>

                <table class="admin-module">
                    <tr>
                        <td class="admin-module-label"><label><?php echo esc_html_e('Number of Agents Per Page', 'ns-real-estate'); ?></label></td>
                        <td class="admin-module-field"><input type="number" id="num_agents_per_page" name="ns_num_agents_per_page" value="<?php echo esc_attr( get_option('ns_num_agents_per_page', 12) ); ?>" /></td>
                    </tr>
                </table>

                <table class="admin-module no-border">
                    <tr>
                        <td class="admin-module-label">
                            <label><?php echo esc_html_e('Hard crop agent listing featured images?', 'ns-real-estate'); ?></label>
                            <span class="admin-module-note"><?php esc_html_e('If active, agent listing thumbnails will be cropped to 800 x 600 pixels.', 'ns-real-estate'); ?></span>
                        </td>
                        <td class="admin-module-field">
                            <div class="toggle-switch" title="<?php if(get_option('ns_agent_listing_crop', 'true') == 'true') { esc_html_e('Active', 'ns-real-estate'); } else { esc_html_e('Disabled', 'ns-real-estate'); } ?>">
                                <input type="checkbox" name="ns_agent_listing_crop" value="true" class="toggle-switch-checkbox" id="agent_listing_crop" <?php checked('true', get_option('ns_agent_listing_crop', 'true'), true) ?>>
                                <label class="toggle-switch-label" for="agent_listing_crop"><?php if(get_option('ns_agent_listing_crop', 'true') == 'true') { echo '<span class="on">'.esc_html__('On', 'ns-real-estate').'</span>'; } else { echo '<span>'.esc_html__('Off', 'ns-real-estate').'</span>'; } ?></label>
                            </div>
                        </td>
                    </tr>
                </table>
            </div>
        </div><!-- end agent listing options -->

        <div class="accordion ns-accordion">
            <h3 class="accordion-tab"><i class="fa fa-chevron-right icon"></i> <?php echo esc_html_e('Agent Detail Options', 'ns-real-estate'); ?></h3>
            <div>

                <div class="admin-module no-border">
                    <div class="admin-module-label"><label><?php echo esc_html_e('Agent Detail Sections', 'ns-real-estate'); ?> <span class="admin-module-note"><?php echo esc_html_e('(Drag & drop to rearrange order)', 'ns-real-estate'); ?></span></label></div>

                    <ul class="sortable-list agent-detail-items-list">
                        <?php
                        $agent_detail_items_default = ns_real_estate_load_default_agent_detail_items();
                        $agent_detail_items = get_option('ns_agent_detail_items', $agent_detail_items_default);
                        $count = 0;

                        foreach($agent_detail_items as $value) { ?>
                            <?php
                                if(isset($value['name'])) { $name = $value['name']; }
                                if(isset($value['label'])) { $label = $value['label']; }
                                if(isset($value['slug'])) { $slug = $value['slug']; }
                                if(isset($value['active']) && $value['active'] == 'true') { $active = 'true'; } else { $active = 'false'; }
                                if(isset($value['sidebar']) && $value['sidebar'] == 'true') { $sidebar = 'true'; } else { $sidebar = 'false'; }
                            ?>
                            <li class="sortable-item">
                                
                                <div class="sortable-item-header">
                                    <div class="sort-arrows"><i class="fa fa-bars"></i></div>
                                    <div class="toggle-switch" title="<?php if($active == 'true') { esc_html_e('Active', 'ns-real-estate'); } else { esc_html_e('Disabled', 'ns-real-estate'); } ?>">
                                        <input type="checkbox" name="ns_agent_detail_items[<?php echo $count; ?>][active]" value="true" class="toggle-switch-checkbox" id="agent_detail_item_<?php echo esc_attr($slug); ?>" <?php checked('true', $active, true) ?>>
                                        <label class="toggle-switch-label" for="agent_detail_item_<?php echo esc_attr($slug); ?>"><?php if($active == 'true') { echo '<span class="on">'.esc_html__('On', 'ns-real-estate').'</span>'; } else { echo '<span>'.esc_html__('Off', 'ns-real-estate').'</span>'; } ?></label>
                                    </div>
                                    <span class="sortable-item-title"><?php echo esc_attr($name); ?></span><div class="clear"></div>
                                    <input type="hidden" name="ns_agent_detail_items[<?php echo $count; ?>][name]" value="<?php echo $name; ?>" />
                                    <input type="hidden" name="ns_agent_detail_items[<?php echo $count; ?>][slug]" value="<?php echo $slug; ?>" />
                                </div>
                            
                                <a href="#advanced-options-content-<?php echo esc_attr($slug); ?>" class="sortable-item-action advanced-options-toggle right"><i class="fa fa-gear"></i> <?php esc_html_e('Additional Settings', 'ns-real-estate'); ?></a>
                                <div id="advanced-options-content-<?php echo esc_attr($slug); ?>" class="advanced-options-content hide-soft">  
                                    
                                    <table class="admin-module">
                                        <tr>
                                            <td class="admin-module-label"><label><?php esc_html_e('Label:', 'ns-real-estate'); ?></label></td>
                                            <td class="admin-module-field">
                                                <input type="text" class="sortable-item-label-input" name="ns_agent_detail_items[<?php echo $count; ?>][label]" value="<?php echo $label; ?>" /> 
                                            </td>
                                        </tr>
                                    </table>

                                    <table class="admin-module">
                                        <tr>
                                            <td class="admin-module-label"><label><?php esc_html_e('Display in Sidebar', 'ns-real-estate'); ?></label></td>
                                            <td class="admin-module-field">
                                                <input type="checkbox" name="ns_agent_detail_items[<?php echo $count; ?>][sidebar]" value="true" <?php checked('true', $sidebar, true) ?> />
                                            </td>
                                        </tr>
                                    </table>

                                    <?php if($slug == 'contact') { ?>
                                        <div class="admin-module">
                                            <label><?php echo esc_html_e('Message Placeholder on Property Pages', 'ns-real-estate'); ?></label><br/>
                                            <input type="text" name="ns_agent_form_message_placeholder" value="<?php echo esc_attr( get_option('ns_agent_form_message_placeholder', esc_html__('I am interested in this property and would like to know more.', 'ns-real-estate')) ); ?>" />
                                        </div>
                                        <div class="admin-module">
                                            <label><?php echo esc_html_e('Success Message', 'ns-real-estate'); ?></label><br/>
                                            <input type="text" name="ns_agent_form_success" value="<?php echo esc_attr( get_option('ns_agent_form_success', esc_html__('Thanks! Your email has been delivered!', 'ns-real-estate')) ); ?>" />
                                        </div>
                                        <div class="admin-module">
                                            <label for="agent_form_submit_text"><?php esc_html_e('Submit Button Text', 'ns-real-estate'); ?></label><br/>
                                            <input type="text" id="agent_form_submit_text" name="ns_agent_form_submit_text" value="<?php echo esc_attr( get_option('ns_agent_form_submit_text', esc_html__('Contact Agent', 'ns-real-estate')) ); ?>" />
                                        </div>
                                    <?php } ?>

                                </div>

                            </li>
                            <?php $count++; ?>
                        <?php } ?>
                    </ul>
                </div>

            </div>
        </div><!-- end agent detail options -->

    </div><!-- end agent options -->

    <div id="maps" class="tab-content">
        <h2><?php echo esc_html_e('Map Settings', 'ns-real-estate'); ?></h2>

        <table class="admin-module">
            <tr>
                <td class="admin-module-label">
                    <label><?php esc_html_e('Google Maps API Key', 'ns-real-estate'); ?></label>
                    <div class="admin-module-note"><?php echo wp_kses_post(__('Provide your unique Google maps API key. <a target="_blank" href="https://developers.google.com/maps/documentation/javascript/get-api-key">Click here</a> to get a key.', 'ns-real-estate')); ?></div>
                </td>
                <td class="admin-module-field">
                    <input type="text" id="google_maps_api" name="ns_real_estate_google_maps_api" value="<?php echo esc_attr( get_option('ns_real_estate_google_maps_api') ); ?>" />
                </td>
            </tr>
        </table>

        <table class="admin-module">
            <tr>
                <td class="admin-module-label">
                    <label><?php esc_html_e('Default Map Zoom', 'ns-real-estate'); ?></label>
                    <div class="admin-module-note"><?php echo esc_html_e('The map zoom ranges from 1 - 19. Zoom level 1 being the most zoomed out.', 'ns-real-estate'); ?></div>
                </td>
                <td class="admin-module-field">
                    <input type="number" min="1" max="19" id="home_default_map_zoom" name="ns_real_estate_default_map_zoom" value="<?php echo esc_attr( get_option('ns_real_estate_default_map_zoom', 10) ); ?>" />
                </td>
            </tr>
        </table>

        <table class="admin-module">
            <tr>
                <td class="admin-module-label">
                    <label><?php esc_html_e('Default Map Latitude', 'ns-real-estate'); ?></label>
                </td>
                <td class="admin-module-field">
                    <input type="text" id="home_default_map_latitude" name="ns_real_estate_default_map_latitude" value="<?php echo esc_attr( get_option('ns_real_estate_default_map_latitude', 39.2904) ); ?>" />
                </td>
            </tr>
        </table>

        <table class="admin-module">
            <tr>
                <td class="admin-module-label">
                    <label><?php esc_html_e('Default Map Longitude', 'ns-real-estate'); ?></label>
                </td>
                <td class="admin-module-field">
                    <input type="text" id="home_default_map_longitude" name="ns_real_estate_default_map_longitude" value="<?php echo esc_attr( get_option('ns_real_estate_default_map_longitude', -76.5000) ); ?>" />
                </td>
            </tr>
        </table>

        <table class="admin-module no-border">
            <tr>
                <td class="admin-module-label">
                    <label><?php esc_html_e('Custom Pin Image', 'ns-real-estate'); ?></label>
                    <div class="admin-module-note"><?php echo esc_html_e('Replace the default map pin with a custom image. Recommended size: 50x50 pixels.', 'ns-real-estate'); ?></div>
                </td>
                <td class="admin-module-field">
                    <input type="text" id="google_maps_pin" name="ns_real_estate_google_maps_pin" value="<?php echo esc_attr( get_option('ns_real_estate_google_maps_pin') ); ?>" />
                    <input id="_btn" class="ns_upload_image_button" type="button" value="<?php echo esc_html_e('Upload Image', 'ns-real-estate'); ?>" />
                    <span class="button-secondary remove"><?php echo esc_html_e('Remove', 'ns-real-estate'); ?></span>
                </td>
            </tr>
        </table>
    </div><!-- end maps -->

    <div id="members" class="tab-content">
        <h2><?php echo esc_html_e('Member Settings', 'ns-real-estate'); ?></h2>

        <table class="admin-module">
            <tr>
                <td class="admin-module-label">
                    <label><?php echo esc_html_e('Select My Properties Page', 'ns-real-estate'); ?></label>
                    <span class="admin-module-note"><?php esc_html_e('Create a page and assign it the My Properties template.', 'ns-real-estate'); ?></span>
                </td>
                <td class="admin-module-field">
                    <select name="ns_members_my_properties_page">
                        <option value="">
                        <?php echo esc_attr( esc_html__( 'Select page', 'ns-real-estate' ) ); ?></option> 
                            <?php 
                            $pages = get_pages(); 
                            foreach ( $pages as $page ) { ?>
                            <option value="<?php echo get_page_link( $page->ID ); ?>" <?php if(esc_attr(get_option('ns_members_my_properties_page')) == get_page_link( $page->ID )) { echo 'selected'; } ?>>
                                <?php echo esc_attr($page->post_title); ?>
                            </option>
                        <?php } ?>
                    </select>
                </td>
            </tr>
        </table>

        <table class="admin-module">
            <tr>
                <td class="admin-module-label">
                    <label><?php echo esc_html_e('Select Submit Property Page', 'ns-real-estate'); ?></label>
                    <span class="admin-module-note"><?php esc_html_e('Create a page and assign it the Submit Property template.', 'ns-real-estate'); ?></span>
                </td>
                <td class="admin-module-field">
                    <select name="ns_members_submit_property_page">
                        <option value="">
                        <?php echo esc_attr( esc_html__( 'Select page', 'ns-real-estate' ) ); ?></option> 
                            <?php 
                            $pages = get_pages(); 
                            foreach ( $pages as $page ) { ?>
                            <option value="<?php echo get_page_link( $page->ID ); ?>" <?php if(esc_attr(get_option('ns_members_submit_property_page')) == get_page_link( $page->ID )) { echo 'selected'; } ?>>
                                <?php echo esc_attr($page->post_title); ?>
                            </option>
                        <?php } ?>
                    </select>
                </td>
            </tr>
        </table>

        <table class="admin-module">
            <tr>
                <td class="admin-module-label"><label><?php echo esc_html_e('Front-end property submissions must be approved before being published', 'ns-real-estate'); ?></label></td>
                <td class="admin-module-field">
                    <div class="toggle-switch" title="<?php if(get_option('ns_members_submit_property_approval', 'true') == 'true') { esc_html_e('Active', 'ns-real-estate'); } else { esc_html_e('Disabled', 'ns-real-estate'); } ?>">
                        <input type="checkbox" name="ns_members_submit_property_approval" value="true" class="toggle-switch-checkbox" id="members_submit_property_approval" <?php checked('true', get_option('ns_members_submit_property_approval', 'true'), true) ?>>
                        <label class="toggle-switch-label" for="members_submit_property_approval"><?php if(get_option('ns_members_submit_property_approval', 'true') == 'true') { echo '<span class="on">'.esc_html__('On', 'ns-real-estate').'</span>'; } else { echo '<span>'.esc_html__('Off', 'ns-real-estate').'</span>'; } ?></label>
                    </div>
                </td>
            </tr>
        </table>

        <table class="admin-module">
            <tr>
                <td class="admin-module-label"><label><?php echo esc_html_e('Allow members to add new property locations from the front-end', 'ns-real-estate'); ?></label></td>
                <td class="admin-module-field">
                    <div class="toggle-switch" title="<?php if(get_option('ns_members_add_locations', 'true') == 'true') { esc_html_e('Active', 'ns-real-estate'); } else { esc_html_e('Disabled', 'ns-real-estate'); } ?>">
                        <input type="checkbox" name="ns_members_add_locations" value="true" class="toggle-switch-checkbox" id="members_add_locations" <?php checked('true', get_option('ns_members_add_locations', 'true'), true) ?>>
                        <label class="toggle-switch-label" for="members_add_locations"><?php if(get_option('ns_members_add_locations', 'true') == 'true') { echo '<span class="on">'.esc_html__('On', 'ns-real-estate').'</span>'; } else { echo '<span>'.esc_html__('Off', 'ns-real-estate').'</span>'; } ?></label>
                    </div>
                </td>
            </tr>
        </table>

        <table class="admin-module">
            <tr>
                <td class="admin-module-label"><label><?php echo esc_html_e('Allow members to add new property amenities from the front-end', 'ns-real-estate'); ?></label></td>
                <td class="admin-module-field">
                    <div class="toggle-switch" title="<?php if(get_option('ns_members_add_amenities', 'true') == 'true') { esc_html_e('Active', 'ns-real-estate'); } else { esc_html_e('Disabled', 'ns-real-estate'); } ?>">
                        <input type="checkbox" name="ns_members_add_amenities" value="true" class="toggle-switch-checkbox" id="members_add_amenities" <?php checked('true', get_option('ns_members_add_amenities', 'true'), true) ?>>
                        <label class="toggle-switch-label" for="members_add_amenities"><?php if(get_option('ns_members_add_amenities', 'true') == 'true') { echo '<span class="on">'.esc_html__('On', 'ns-real-estate').'</span>'; } else { echo '<span>'.esc_html__('Off', 'ns-real-estate').'</span>'; } ?></label>
                    </div>
                </td>
            </tr>
        </table>

        <!-- Hook in for Add-Ons -->
        <?php do_action( 'ns_real_estate_after_member_settings'); ?>
    </div><!-- end member options -->

    <div id="currency" class="tab-content">
        <h2><?php echo esc_html_e('Currency & Numbers', 'ns-real-estate'); ?></h2>

        <?php $currency_options = ns_real_estate_get_curreny_options(); ?>

        <table class="admin-module">
             <tr>
                <td class="admin-module-label"><label><?php echo esc_html_e('Currency Symbol', 'ns-real-estate'); ?></label></td>
                <td class="admin-module-field"><input type="text" id="currency_symbol" name="ns_real_estate_currency_symbol" value="<?php echo $currency_options['symbol']; ?>" /></td>
            </tr>
        </table>

        <table class="admin-module">
            <tr>
                <td class="admin-module-label"><label><?php echo esc_html_e('Currency Symbol Position', 'ns-real-estate'); ?></label></td>
                <td class="admin-module-field">
                    <p><input type="radio" id="currency_symbol_position" name="ns_real_estate_currency_symbol_position" value="before" <?php if($currency_options['symbol_position'] == 'before') { echo 'checked'; } ?> /><?php echo esc_html_e('Display before price', 'ns-real-estate'); ?></p>
                    <p><input type="radio" id="currency_symbol_position" name="ns_real_estate_currency_symbol_position" value="after" <?php if($currency_options['symbol_position'] == 'after') { echo 'checked'; } ?> /><?php echo esc_html_e('Display after price', 'ns-real-estate'); ?></p>
                </td>
            </tr>
        </table>

        <table class="admin-module">
            <tr>
                <td class="admin-module-label"><label><?php echo esc_html_e('Thousand Separator', 'ns-real-estate'); ?></label></td>
                <td class="admin-module-field"><input type="text" id="thousand_separator" name="ns_real_estate_thousand_separator" value="<?php echo $currency_options['thousand']; ?>" /></td>
            </tr>
        </table>

        <table class="admin-module">
            <tr>
                <td class="admin-module-label"><label><?php echo esc_html_e('Decimal Separator', 'ns-real-estate'); ?></label></td>
                <td class="admin-module-field"><input type="text" id="decimal_separator" name="ns_real_estate_decimal_separator" value="<?php echo $currency_options['decimal']; ?>" /></td>
            </tr>
        </table>

        <table class="admin-module no-border">
            <tr>
                <td class="admin-module-label"><label for="num_decimal"><?php echo esc_html_e('Number of Decimals', 'ns-real-estate'); ?></label></td>
                <td class="admin-module-field"><input type="number" min="0" max="5" id="num_decimal" name="ns_real_estate_num_decimal" value="<?php echo $currency_options['decimal_num']; ?>" /></td>
            </tr>
        </table>

        <hr><br/><h2><?php esc_html_e('Area Formatting', 'ns-real-estate'); ?></h2>

        <table class="admin-module no-border">
            <tr>
                <td class="admin-module-label"><label for="default_area_postfix"><?php echo esc_html_e('Deafult Area Postfix', 'ns-real-estate'); ?></label></td>
                <td class="admin-module-field"><input type="text" id="default_area_postfix" name="ns_real_estate_default_area_postfix" value="<?php echo $currency_options['default_area_postfix']; ?>" /></td>
            </tr>
        </table>

        <table class="admin-module no-border">
            <tr>
                <td class="admin-module-label"><label for="thousand_separator_area"><?php echo esc_html_e('Area Thousand Separator', 'ns-real-estate'); ?></label></td>
                <td class="admin-module-field"><input type="text" id="thousand_separator_area" name="ns_real_estate_thousand_separator_area" value="<?php echo $currency_options['thousand_area']; ?>" /></td>
            </tr>
        </table>

        <table class="admin-module no-border">
            <tr>
                <td class="admin-module-label"><label for="decimal_separator_area"><?php echo esc_html_e('Area Decimal Separator', 'ns-real-estate'); ?></label></td>
                <td class="admin-module-field"><input type="text" id="decimal_separator_area" name="ns_real_estate_decimal_separator_area" value="<?php echo $currency_options['decimal_area']; ?>" /></td>
            </tr>
        </table>

        <table class="admin-module no-border">
            <tr>
                <td class="admin-module-label"><label for="num_decimal_area"><?php echo esc_html_e('Area Number of Decimals', 'ns-real-estate'); ?></label></td>
                <td class="admin-module-field"><input type="number" min="0" max="5" id="num_decimal_area" name="ns_real_estate_num_decimal_area" value="<?php echo $currency_options['decimal_num_area']; ?>" /></td>
            </tr>
        </table>

    </div>

    <?php $output = ob_get_clean();
    return $output;
}

/*-----------------------------------------------------------------------------------*/
/*  Load default Property Detail Items
/*-----------------------------------------------------------------------------------*/
function ns_real_estate_load_default_property_detail_items() {
    $property_detail_items_default = array(
        0 => array(
            'name' => esc_html__('Overview', 'ns-real-estate'),
            'label' => esc_html__('Overview', 'ns-real-estate'),
            'slug' => 'overview',
            'active' => 'true',
            'sidebar' => 'false',
        ),
        1 => array(
            'name' => esc_html__('Description', 'ns-real-estate'),
            'label' => esc_html__('Description', 'ns-real-estate'),
            'slug' => 'description',
            'active' => 'true',
            'sidebar' => 'false',
        ),
        2 => array(
            'name' => esc_html__('Gallery', 'ns-real-estate'),
            'label' => esc_html__('Gallery', 'ns-real-estate'),
            'slug' => 'gallery',
            'active' => 'true',
            'sidebar' => 'false',
        ),
        3 => array(
            'name' => esc_html__('Video', 'ns-real-estate'),
            'label' => esc_html__('Video', 'ns-real-estate'),
            'slug' => 'video',
            'active' => 'true',
            'sidebar' => 'false',
        ),
        4 => array(
            'name' => esc_html__('Amenities', 'ns-real-estate'),
            'label' => esc_html__('Amenities', 'ns-real-estate'),
            'slug' => 'amenities',
            'active' => 'true',
            'sidebar' => 'false',
        ),
        5 => array(
            'name' => esc_html__('Floor Plans', 'ns-real-estate'),
            'label' => esc_html__('Floor Plans', 'ns-real-estate'),
            'slug' => 'floor_plans',
            'active' => 'true',
            'sidebar' => 'false',
        ),
        6 => array(
            'name' => esc_html__('Location', 'ns-real-estate'),
            'label' => esc_html__('Location', 'ns-real-estate'),
            'slug' => 'location',
            'active' => 'true',
            'sidebar' => 'false',
        ),
        7 => array(
            'name' => esc_html__('Walk Score', 'ns-real-estate'),
            'label' => esc_html__('Walk Score', 'ns-real-estate'),
            'slug' => 'walk_score',
            'active' => 'true',
            'sidebar' => 'false',
        ),
        8 => array(
            'name' => esc_html__('Agent Info', 'ns-real-estate'),
            'label' => 'Agent Information',
            'slug' => 'agent_info',
            'active' => 'true',
            'sidebar' => 'false',
        ),
        9 => array(
            'name' => esc_html__('Related Properties', 'ns-real-estate'),
            'label' => 'Related Properties',
            'slug' => 'related',
            'active' => 'true',
            'sidebar' => 'false',
        ),
    );

    return $property_detail_items_default;
}

/*-----------------------------------------------------------------------------------*/
/*  Load default Agent Detail Items
/*-----------------------------------------------------------------------------------*/
function ns_real_estate_load_default_agent_detail_items() {
    $agent_detail_items_default = array(
        0 => array(
            'name' => esc_html__('Overview', 'ns-real-estate'),
            'label' => esc_html__('Overview', 'ns-real-estate'),
            'slug' => 'overview',
            'active' => 'true',
            'sidebar' => 'false',
        ),
        1 => array(
            'name' => esc_html__('Description', 'ns-real-estate'),
            'label' => esc_html__('Description', 'ns-real-estate'),
            'slug' => 'description',
            'active' => 'true',
            'sidebar' => 'false',
        ),
        2 => array(
            'name' => esc_html__('Contact', 'ns-real-estate'),
            'label' => esc_html__('Contact', 'ns-real-estate'),
            'slug' => 'contact',
            'active' => 'true',
            'sidebar' => 'false',
        ),
        3 => array(
            'name' => esc_html__('Properties', 'ns-real-estate'),
            'label' => esc_html__('Properties', 'ns-real-estate'),
            'slug' => 'properties',
            'active' => 'true',
            'sidebar' => 'false',
        ),
    );

    return $agent_detail_items_default;
}

/*-----------------------------------------------------------------------------------*/
/*  Add Real Estate Invidiual Page Options
/*-----------------------------------------------------------------------------------*/
function ns_real_estate_map_options($values) { 
    $banner_source = isset( $values['ns_basics_banner_source'] ) ? esc_attr( $values['ns_basics_banner_source'][0] ) : 'image_banner';
    ?> 
    <label class="selectable-item <?php if($banner_source == 'properties_map') { echo 'active'; } ?>" for="banner_source_properties_map">
        <img src="<?php echo plugins_url('/ns-basics/images/google-maps-icon.png'); ?>" alt="" /><br/>
        <input type="radio" id="banner_source_properties_map" name="ns_basics_banner_source" value="properties_map" <?php checked('properties_map', $banner_source, true) ?> /> <?php esc_html_e('Properties Map', 'ns-real-estate'); ?><br/>
    </label>
<?php }
add_action( 'ns_basics_before_page_banner_options', 'ns_real_estate_map_options' );

function ns_real_estate_page_banner_filter_options($values) { ?>
    <?php 
    $banner_property_filter_override = isset( $values['ns_banner_property_filter_override'] ) ? esc_attr( $values['ns_banner_property_filter_override'][0] ) : 'true'; 
    $banner_property_filter_display = isset( $values['ns_banner_property_filter_display'] ) ? esc_attr( $values['ns_banner_property_filter_display'][0] ) : 'true';
    $banner_property_filter_id = isset( $values['ns_banner_property_filter_id'] ) ? esc_attr( $values['ns_banner_property_filter_id'][0] ) : '';
    ?>

    <h4 style="font-size:15px;"><?php esc_html_e('Property Filter', 'ns-real-estate'); ?></h4>

    <table class="admin-module">
        <tr>
            <td class="admin-module-label"><label><?php echo esc_html_e('Use Global Property Filter Settings', 'ns-real-estate'); ?></label></td>
            <td class="admin-module-field"><input id="banner_property_filter_override" type="checkbox" name="ns_banner_property_filter_override" value="true" <?php if($banner_property_filter_override == 'true') { echo 'checked'; } ?> /></td>
        </tr>
    </table>

    <div class="admin-module no-border no-padding-top admin-module-page-banner-property-filter-options <?php if($banner_property_filter_override == 'true') { echo 'hide-soft'; } ?>">

        <table class="admin-module">
            <tr>
                <td class="admin-module-label"><label><?php echo esc_html_e('Display Property Filter', 'ns-real-estate'); ?></label></td>
                <td class="admin-module-field"><input id="banner_property_filter_display" type="checkbox" name="ns_banner_property_filter_display" value="true" <?php if($banner_property_filter_display == 'true') { echo 'checked'; } ?> /></td>
            </tr>
        </table>

        <table class="admin-module no-border">
            <tr>
                <td class="admin-module-label">
                    <label><?php esc_html_e('Select a Filter', 'ns-real-estate'); ?></label>
                    <span class="admin-module-note"><a href="<?php echo admin_url('edit.php?post_type=ns-property-filter'); ?>" target="_blank"><i class="fa fa-cog"></i> <?php esc_html_e('Manage property filters', 'ns-real-estate'); ?></a></span>
                </td>
                <td class="admin-module-field">
                    <select name="ns_banner_property_filter_id" id="banner_property_filter_id">
                        <?php
                            $filter_listing_args = array(
                                'post_type' => 'ns-property-filter',
                                'posts_per_page' => -1
                                );
                            $filter_listing_query = new WP_Query( $filter_listing_args );
                        ?>
                        <?php if ( $filter_listing_query->have_posts() ) : while ( $filter_listing_query->have_posts() ) : $filter_listing_query->the_post(); ?>
                            <option value="<?php echo get_the_id(); ?>" <?php if($banner_property_filter_id == get_the_id()) { echo 'selected'; } ?>><?php echo get_the_title().' (#'.get_the_id().')'; ?></option>
                            <?php wp_reset_postdata(); ?>
                        <?php endwhile; ?>
                        <?php else: ?>
                        <?php endif; ?>
                    </select>
                </td>
            </tr>
        </table>

    </div>
<?php }
add_action( 'ns_basics_banner_options_end', 'ns_real_estate_page_banner_filter_options' );

function ns_real_estate_save_page_banner_options($post_id) {
    $allowed = array();

    if( isset( $_POST['ns_banner_property_filter_override'] ) ) {
        update_post_meta( $post_id, 'ns_banner_property_filter_override', wp_kses( $_POST['ns_banner_property_filter_override'], $allowed ) );
    } else {
        update_post_meta( $post_id, 'ns_banner_property_filter_override', wp_kses( '', $allowed ) );
    }

    if( isset( $_POST['ns_banner_property_filter_display'] ) ) {
        update_post_meta( $post_id, 'ns_banner_property_filter_display', wp_kses( $_POST['ns_banner_property_filter_display'], $allowed ) );
    } else {
        update_post_meta( $post_id, 'ns_banner_property_filter_display', wp_kses( '', $allowed ) );
    }

    if( isset( $_POST['ns_banner_property_filter_id'] ) )
        update_post_meta( $post_id, 'ns_banner_property_filter_id', wp_kses( $_POST['ns_banner_property_filter_id'], $allowed ) );
            
}
add_action( 'ns_basics_after_page_settings_save', 'ns_real_estate_save_page_banner_options' );


/*-----------------------------------------------------------------------------------*/
/*  Add Google Maps API Key notice
/*-----------------------------------------------------------------------------------*/
function ns_real_estate_google_maps_api_notice() {
    $google_maps_api = esc_attr(get_option('ns_real_estate_google_maps_api'));

    if(empty($google_maps_api)) {
        $class = 'notice notice-error is-dismissible';
        $message = wp_kses_post(__( 'NightShift Real Estate <strong>requires</strong> a Google Maps API key! Please provide your key in the plugin settings. If you do not have one, <a href="https://developers.google.com/maps/documentation/javascript/get-api-key" target="_blank">click here</a>.', 'ns-real-estate' ));
        printf( '<div class="%1$s"><p>%2$s</p></div>', esc_attr( $class ), $message ); 
    }
}
add_action( 'admin_notices', 'ns_real_estate_google_maps_api_notice' );

?>