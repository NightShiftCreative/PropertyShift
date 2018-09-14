<?php
/*-----------------------------------------------------------------------------------*/
/*  ADD ADMIN MENU PAGES
/*-----------------------------------------------------------------------------------*/
add_action('admin_menu', 'rype_real_estate_plugin_menu');
function rype_real_estate_plugin_menu() {
    add_menu_page('Rype Real Estate', 'Rype Real Estate', 'administrator', 'rype-real-estate-settings', 'rype_real_estate_settings_page', 'dashicons-admin-home');
    add_submenu_page('rype-real-estate-settings', 'Add-Ons', 'Add-Ons', 'administrator', 'rype-real-estate-add-ons', 'rype_real_estate_add_ons_page');
    add_submenu_page('rype-real-estate-settings', 'License Keys', 'License Keys', 'administrator', 'rype-real-estate-license-keys', 'rype_real_estate_license_keys_page');
    add_submenu_page('rype-real-estate-settings', 'Help', 'Help', 'administrator', 'rype-real-estate-help', 'rype_real_estate_help_page');
    add_action( 'admin_init', 'rype_real_estate_register_options' );
}

/*-----------------------------------------------------------------------------------*/
/*  REGISTER SETTINGS
/*-----------------------------------------------------------------------------------*/
function rype_real_estate_register_options() {

    //MAIN SETTINGS
    register_setting( 'rype-real-estate-settings-group', 'properties_page');
    register_setting( 'rype-real-estate-settings-group', 'rypecore_property_detail_slug', 'rype_real_estate_sanitize_slug');
    register_setting( 'rype-real-estate-settings-group', 'rypecore_property_type_tax_slug', 'rype_real_estate_sanitize_slug');
    register_setting( 'rype-real-estate-settings-group', 'rypecore_property_status_tax_slug', 'rype_real_estate_sanitize_slug');
    register_setting( 'rype-real-estate-settings-group', 'rypecore_property_location_tax_slug', 'rype_real_estate_sanitize_slug');
    register_setting( 'rype-real-estate-settings-group', 'rypecore_property_amenities_tax_slug', 'rype_real_estate_sanitize_slug');

    //LICENSE KEY SETTINGS
    register_setting( 'rype-real-estate-license-keys-group', 'rype_real_estate_open_houses_license');
}

function rype_real_estate_sanitize_slug($option) {
    $option = sanitize_title($option);
    return $option;
}

/*-----------------------------------------------------------------------------------*/
/*  OUTPUT SETTINGS PAGE
/*-----------------------------------------------------------------------------------*/
function rype_real_estate_get_admin_pages() {
    $pages = array();
    $pages[] = array('slug' => 'rype-real-estate-settings', 'name' => 'Settings');
    $pages[] = array('slug' => 'rype-real-estate-add-ons', 'name' => 'Add-Ons');
    $pages[] = array('slug' => 'rype-real-estate-license-keys', 'name' => 'License Keys');
    $pages[] = array('slug' => 'rype-real-estate-help', 'name' => 'Help');
    return $pages;
}

function rype_real_estate_settings_page() {
    $page_name = 'Rype Real Estate';
    $settings_group = 'rype-real-estate-settings-group';
    $pages = rype_real_estate_get_admin_pages();
    $display_actions = 'true';
    $content = rype_real_estate_settings_page_content();
    $content_class = null;
    $content_nav = array(
        array('name' => 'General', 'link' => '#general', 'icon' => 'fa-globe'),
        array('name' => 'Properties', 'link' => '#properties', 'icon' => 'fa-home'),
        array('name' => 'Agents', 'link' => '#agents', 'icon' => 'fa-group'),
    );
    echo rype_basics_admin_page($page_name, $settings_group, $pages, $display_actions, $content, $content_class, $content_nav);
} 

function rype_real_estate_settings_page_content() {
    ob_start(); ?>
    
    <div class="accordion rc-accordion">
        <h3 class="accordion-tab"><i class="fa fa-chevron-right icon"></i> <?php echo esc_html_e('Property URL Options', 'rype-real-estate'); ?></h3>
        <div>

            <p class="admin-module-note"><?php esc_html_e('After changing slugs, make sure you re-save your permalinks in Settings > Permalinks.', 'rype-real-estate'); ?></p>
                <br/>

                <table class="admin-module admin-module-property-slug">
                    <tr>
                        <td class="admin-module-label">
                            <label><?php echo esc_html_e('Properties Slug', 'rype-real-estate'); ?></label>
                            <span class="admin-module-note"><?php esc_html_e('Default: properties', 'rype-real-estate'); ?></span>
                        </td>
                        <td class="admin-module-field">
                            <span><?php echo esc_url(home_url('/')); ?></span> <input type="text" style="width:150px;" id="property_detail_slug" name="rypecore_property_detail_slug" value="<?php echo esc_attr( get_option('rypecore_property_detail_slug', 'properties') ); ?>" />
                        </td>
                    </tr>
                </table>

                <table class="admin-module">
                    <tr>
                        <td class="admin-module-label">
                            <label><?php echo esc_html_e('Property Type Taxonomy Slug', 'rype-real-estate'); ?></label>
                            <span class="admin-module-note"><?php esc_html_e('Default: property-type', 'rype-real-estate'); ?></span>
                        </td>
                        <td class="admin-module-field">
                            <span><?php echo esc_url(home_url('/')); ?></span> <input type="text" style="width:150px;" id="property_type_tax_slug" name="rypecore_property_type_tax_slug" value="<?php echo esc_attr( get_option('rypecore_property_type_tax_slug', 'property-type') ); ?>" />
                        </td>
                    </tr>
                </table>

                <table class="admin-module">
                    <tr>
                        <td class="admin-module-label">
                            <label><?php echo esc_html_e('Property Status Taxonomy Slug', 'rype-real-estate'); ?></label>
                            <span class="admin-module-note"><?php esc_html_e('Default: property-status', 'rype-real-estate'); ?></span>
                        </td>
                        <td class="admin-module-field">
                            <span><?php echo esc_url(home_url('/')); ?></span> <input type="text" style="width:150px;" id="property_status_tax_slug" name="rypecore_property_status_tax_slug" value="<?php echo esc_attr( get_option('rypecore_property_status_tax_slug', 'property-status') ); ?>" />
                        </td>
                    </tr>
                </table>

                <table class="admin-module">
                    <tr>
                        <td class="admin-module-label">
                            <label><?php echo esc_html_e('Property Location Taxonomy Slug', 'rype-real-estate'); ?></label>
                            <span class="admin-module-note"><?php esc_html_e('Default: property-location', 'rype-real-estate'); ?></span>
                        </td>
                        <td class="admin-module-field">
                            <span><?php echo esc_url(home_url('/')); ?></span> <input type="text" style="width:150px;" id="property_location_tax_slug" name="rypecore_property_location_tax_slug" value="<?php echo esc_attr( get_option('rypecore_property_location_tax_slug', 'property-location') ); ?>" />
                        </td>
                    </tr>
                </table>

                <table class="admin-module no-border">
                    <tr>
                        <td class="admin-module-label">
                            <label><?php echo esc_html_e('Property Amenities Taxonomy Slug', 'rype-real-estate'); ?></label>
                            <span class="admin-module-note"><?php esc_html_e('Default: property-amenity', 'rype-real-estate'); ?></span>
                        </td>
                        <td class="admin-module-field">
                            <span><?php echo esc_url(home_url('/')); ?></span> <input type="text" style="width:150px;" id="property_amenities_tax_slug" name="rypecore_property_amenities_tax_slug" value="<?php echo esc_attr( get_option('rypecore_property_amenities_tax_slug', 'property-amenity') ); ?>" />
                        </td>
                    </tr>
                </table>

        </div>
    </div>

    <?php $output = ob_get_clean();
    return $output;
}

/*-----------------------------------------------------------------------------------*/
/*  Load default Property Detail Items
/*-----------------------------------------------------------------------------------*/
function rao_load_default_property_detail_items() {
    $property_detail_items_default = array(
        0 => array(
            'name' => esc_html__('Overview', 'rype-real-estate'),
            'label' => esc_html__('Overview', 'rype-real-estate'),
            'slug' => 'overview',
            'active' => 'true',
            'sidebar' => 'false',
        ),
        1 => array(
            'name' => esc_html__('Description', 'rype-real-estate'),
            'label' => esc_html__('Description', 'rype-real-estate'),
            'slug' => 'description',
            'active' => 'true',
            'sidebar' => 'false',
        ),
        2 => array(
            'name' => esc_html__('Gallery', 'rype-real-estate'),
            'label' => esc_html__('Gallery', 'rype-real-estate'),
            'slug' => 'gallery',
            'active' => 'true',
            'sidebar' => 'false',
        ),
        3 => array(
            'name' => esc_html__('Video', 'rype-real-estate'),
            'label' => esc_html__('Video', 'rype-real-estate'),
            'slug' => 'video',
            'active' => 'true',
            'sidebar' => 'false',
        ),
        4 => array(
            'name' => esc_html__('Amenities', 'rype-real-estate'),
            'label' => esc_html__('Amenities', 'rype-real-estate'),
            'slug' => 'amenities',
            'active' => 'true',
            'sidebar' => 'false',
        ),
        5 => array(
            'name' => esc_html__('Floor Plans', 'rype-real-estate'),
            'label' => esc_html__('Floor Plans', 'rype-real-estate'),
            'slug' => 'floor_plans',
            'active' => 'true',
            'sidebar' => 'false',
        ),
        6 => array(
            'name' => esc_html__('Location', 'rype-real-estate'),
            'label' => esc_html__('Location', 'rype-real-estate'),
            'slug' => 'location',
            'active' => 'true',
            'sidebar' => 'false',
        ),
        7 => array(
            'name' => esc_html__('Walk Score', 'rype-real-estate'),
            'label' => esc_html__('Walk Score', 'rype-real-estate'),
            'slug' => 'walk_score',
            'active' => 'true',
            'sidebar' => 'false',
        ),
        8 => array(
            'name' => esc_html__('Agent Info', 'rype-real-estate'),
            'label' => 'Agent Information',
            'slug' => 'agent_info',
            'active' => 'true',
            'sidebar' => 'false',
        ),
        9 => array(
            'name' => esc_html__('Related Properties', 'rype-real-estate'),
            'label' => 'Related Properties',
            'slug' => 'related',
            'active' => 'true',
            'sidebar' => 'false',
        ),
    );

    return $property_detail_items_default;
}

?>