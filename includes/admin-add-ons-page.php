<?php 
/*-----------------------------------------------------------------------------------*/
/*  OUTPUT ADD-ONS PAGE STRUCTURE
/*-----------------------------------------------------------------------------------*/
function ns_real_estate_add_ons_page() { 
	$page_name = 'NightShift Real Estate';
    $settings_group = null;
    $pages = ns_real_estate_get_admin_pages();
    $display_actions = 'false';
    $content = ns_real_estate_add_ons_page_content();
    $content_class = 'ns-modules';
    $content_nav = null;
    $alerts = null;
    $ajax = true;
    $icon = plugins_url('/ns-real-estate/images/icon-real-estate.svg');
    echo ns_basics_admin_page($page_name, $settings_group, $pages, $display_actions, $content, $content_class, $content_nav, $alerts, $ajax, $icon);
}

/*-----------------------------------------------------------------------------------*/
/*  OUTPUT ADD-ONS PAGE CONTENT
/*-----------------------------------------------------------------------------------*/
function ns_real_estate_add_ons_page_content() {
    ob_start(); ?>

    <?php 
    $open_houses_license_default = array('slug' => 'ns-open-houses', 'key' => null, 'email' => null, 'registered' => false, 'error' => null);
    $open_houses_license = get_option('ns_real_estate_open_houses_license', $open_houses_license_default);
    ?>
    
    <div class="ns-module-group ns-module-group-real-estate">
        <div class="admin-module">
            <div class="ns-module-header">
                <div class="ns-module-icon"><img src="<?php echo plugins_url('/ns-basics/images/icon-post-sharing.svg'); ?>" alt="" /></div>
                <?php echo ns_basics_get_license_status($open_houses_license, '#', '?page=ns-real-estate-license-keys', 'true'); ?>
                <h4><?php esc_html_e('Open Houses', 'ns-real-estate'); ?></h4>
            </div>
            <div class="ns-module-content">
                <span class="admin-module-note"><?php esc_html_e('Sell more properties by advertising open houses. Add unlimited open houses dates and times to your listings.', 'ns-real-estate'); ?></span>
                <a href="#" target="_blank" class="ns-meta-item"><?php esc_html_e('View Details', 'ns-real-estate'); ?> </a>
            </div>
        </div>

        <div class="clear"></div>
    </div>

    <?php 
    $raw_addons = wp_remote_get(
        constant('NS_SHOP_URL').'/plugins/ns-real-estate/add-ons/',
        array(
            'timeout'     => 10,
            'redirection' => 5,
            'sslverify'   => false
        )
    );

    if(!is_wp_error($raw_addons)) {
        echo '<div class="ns-module-group">';
        $raw_addons = wp_remote_retrieve_body($raw_addons);
        $dom = new DOMDocument();
        libxml_use_internal_errors(true);
        $dom->loadHTML( $raw_addons );

        $finder = new DomXPath($dom);
        $classname = "ns-product-grid";
        $nodes = $finder->query("//*[contains(@class, '$classname')]");
 
        function DOMinnerHTML(DOMNode $element) { 
            $innerHTML = ""; 
            $children  = $element->childNodes;
            foreach ($children as $child) { 
                $innerHTML .= $element->ownerDocument->saveHTML($child);
            }
            return $innerHTML; 
        } 

        foreach($nodes as $node) {
            echo '<div class="admin-module add-on">'.DOMinnerHTML($node).'</div>'; 
        }
        echo '</div>';
    }
    ?>

    <?php $output = ob_get_clean();
    return $output;
}