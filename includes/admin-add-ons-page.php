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
    ob_start();

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
            $anchors = $element->getElementsByTagName('a');
            foreach($anchors as $anchor) { $anchor->setAttribute('target','_blank'); }
            foreach ($children as $child) { $innerHTML .= $element->ownerDocument->saveHTML($child); }
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