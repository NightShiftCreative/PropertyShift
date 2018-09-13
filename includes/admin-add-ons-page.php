<?php 
/*-----------------------------------------------------------------------------------*/
/*  OUTPUT ADD-ONS PAGE STRUCTURE
/*-----------------------------------------------------------------------------------*/
function rype_real_estate_add_ons_page() { 
	$page_name = 'Rype Real Estate';
    $settings_group = null;
    $pages = rype_real_estate_get_admin_pages();
    $display_actions = 'false';
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
                <?php if(rype_basics_is_paid_plugin_active('rype-open-houses')) { ?>
                    <a href="?page=rype-real-estate-license-keys" class="view-details rype-meta-item"><?php esc_html_e('Register License Key', 'rype-real-estate'); ?></a>
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