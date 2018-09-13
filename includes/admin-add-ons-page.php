<?php 
/*-----------------------------------------------------------------------------------*/
/*  OUTPUT ADD-ONS PAGE STRUCTURE
/*-----------------------------------------------------------------------------------*/
function rype_real_estate_add_ons_page() { 
	$page_name = 'Rype Real Estate';
    $settings_group = 'ype-real-estate-add-ons-group';
    $pages = array();
    $pages[] = array('slug' => 'rype-real-estate-settings', 'name' => 'Settings', 'active' => 'false');
    $pages[] = array('slug' => 'rype-real-estate-add-ons', 'name' => 'Add-Ons', 'active' => 'true');
    $pages[] = array('slug' => 'rype-real-estate-help', 'name' => 'Help', 'active' => 'false');
    $display_actions = 'true';
    $content = rype_real_estate_add_ons_page_content();
    $content_class = 'rype-modules';
    echo rype_basics_admin_page($page_name, $settings_group, $pages, $display_actions, $content, $content_class);
}

/*-----------------------------------------------------------------------------------*/
/*  OUTPUT ADD-ONS PAGE CONTENT
/*-----------------------------------------------------------------------------------*/
function rype_real_estate_add_ons_page_content() {
    ob_start(); ?>
    
    <div class="rype-module-group rype-module-group-real-estate">
        <div class="admin-module">
            <div class="rype-module-header">
                <div class="rype-module-icon"><img src="" alt="" /></div>
                <h4><?php esc_html_e('Open Houses', 'rype-real-estate'); ?></h4>
            </div>
            <div class="rype-module-content">
                <span class="admin-module-note"><?php esc_html_e('Sell more properties by advertising open houses. Add unlimited open houses dates and times to your listings.', 'rype-real-estate'); ?></span>
                <?php if(rype_real_estate_is_paid_plugin_active('rype-open-houses')) { ?>
                    <a href="#" class="view-details rype-meta-item"><?php esc_html_e('Register License Key', 'rype-real-estate'); ?></a>
                <?php } else { ?>
                    <a href="#" target="_blank" class="rype-meta-item"><?php esc_html_e('Get Add-On', 'rype-real-estate'); ?> </a>
                <?php } ?>
                <a href="#" target="_blank" class="rype-meta-item"><?php esc_html_e('View Details', 'rype-real-estate'); ?> </a>
            </div>
        </div>

        <div class="admin-module coming-soon"><div class="rype-module-content"><i class="fa fa-plus"></i> <span>More Coming Soon...</span></div></div>
    
        <div class="clear"></div>
    </div>

    <?php $output = ob_get_clean();
    return $output;
}

/*-----------------------------------------------------------------------------------*/
/* Check if add-on plugin is activated 
/*-----------------------------------------------------------------------------------*/
function rype_real_estate_is_paid_plugin_active($add_on_slug) {
    include_once( ABSPATH . 'wp-admin/includes/plugin.php' );
    if(is_plugin_active($add_on_slug.'/'.$add_on_slug.'.php')) { 
        return true; 
    } else { 
        return false;
    }
}