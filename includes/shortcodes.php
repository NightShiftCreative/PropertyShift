<?php 
add_action( 'media_buttons', function($editor_id) { ?>
    
    <?php if($editor_id == 'content') { ?>
    <a href="#" data-featherlight="#shortcode-selector-real-estate" data-featherlight-persist="true" class="button add-shortcode" title="<?php esc_html_e('Add Real Estate Shortcode', 'ns-real-estate'); ?>">
        <span class="wp-media-buttons-icon add-shortcode-icon"></span><?php esc_html_e('Real Estate Shortcodes', 'ns-real-estate'); ?>
    </a>

    <div class="shortcode-selector" id="shortcode-selector-real-estate">
        <div class="featherlight-header"><?php esc_html_e('Insert a Shortcode', 'ns-real-estate'); ?></div>
        <div class="shortcode-selector-inner">
            <div class="shortcode-selector-list">
                <p><?php esc_html_e('Choose a shortcode to insert from the list below:', 'ns-real-estate'); ?></p>
                <a href="#real-estate-list-properties" class="button has-options"><i class="fa fa-align-justify"></i><?php esc_html_e('List Properties', 'ns-real-estate'); ?></a>
                <a href="#real-estate-list-property-tax" class="button has-options"><i class="fa fa-align-justify"></i><?php esc_html_e('List Property Taxonomy', 'ns-real-estate'); ?></a>
                <a href="#real-estate-submit-property" class="button has-options"><i class="fa fa-plus"></i><?php esc_html_e('Submit Property Form', 'ns-real-estate'); ?></a>
                <a href="#real-estate-my-properties" class="button has-options"><i class="fa fa-plus"></i><?php esc_html_e('My Properties', 'ns-real-estate'); ?></a>
                <a href="#real-estate-filter" class="button has-options"><i class="fa fa-filter"></i><?php esc_html_e('Property Filter', 'ns-real-estate'); ?></a>
                <a href="#real-estate-list-agents" class="button has-options"><i class="fa fa-group"></i><?php esc_html_e('List Agents', 'ns-real-estate'); ?></a>
            </div>
            <div class="shortcode-selector-options">
                <div class="button cancel-shortcode"><i class="fa fa-reply"></i> <?php esc_html_e('Go Back', 'ns-real-estate'); ?></div>

                <div id="real-estate-list-properties" class="admin-module no-border">
                    <h3><strong><?php esc_html_e('List Properties', 'ns-real-estate'); ?></strong></h3>
                    <div class="form-block">
                        <label><?php esc_html_e('Show Posts', 'ns-real-estate'); ?></label>
                        <input type="number" value="3" class="list-properties-show-posts" />
                    </div>
                    <div class="form-block">
                        <input type="checkbox" value="false" class="list-properties-show-header" />
                        <label style="display:inline;"><?php esc_html_e('Show Header', 'ns-real-estate'); ?></label>
                    </div>
                    <div class="form-block">
                        <input type="checkbox" value="false" class="list-properties-show-pagination" />
                        <label style="display:inline;"><?php esc_html_e('Show Pagination', 'ns-real-estate'); ?></label>
                    </div>
                    <div class="form-block">
                        <label><?php esc_html_e('Layout', 'ns-real-estate'); ?></label>
                        <select class="list-properties-layout" >
                            <option value="grid"><?php esc_html_e('Grid', 'ns-real-estate'); ?></option>
                            <option value="row"><?php esc_html_e('Row', 'ns-real-estate'); ?></option>
                        </select>
                    </div>
                    <div class="form-block">
                        <label><?php esc_html_e('Property Status', 'ns-real-estate'); ?></label>
                        <select class="list-properties-status">
                            <option value=""><?php esc_html_e('Any', 'ns-real-estate'); ?></option>
                            <?php
                            $property_statuses = get_terms(['taxonomy' => 'property_status','hide_empty' => false,]);
                            foreach($property_statuses as $property_status) { ?>
                                <option value="<?php echo $property_status->slug; ?>"><?php echo $property_status->name; ?></option>
                            <?php } ?>
                        </select>
                    </div>
                    <div class="form-block">
                        <label><?php esc_html_e('Property Type', 'ns-real-estate'); ?></label>
                        <select class="list-properties-type">
                            <option value=""><?php esc_html_e('Any', 'ns-real-estate'); ?></option>
                            <?php
                            $property_types = get_terms(['taxonomy' => 'property_type','hide_empty' => false,]);
                            foreach($property_types as $property_type) { ?>
                                <option value="<?php echo $property_type->slug; ?>"><?php echo $property_type->name; ?></option>
                            <?php } ?>
                        </select>
                    </div>
                    <div class="form-block">
                        <label><?php esc_html_e('Property Location', 'ns-real-estate'); ?></label>
                        <select class="list-properties-location">
                            <option value=""><?php esc_html_e('Any', 'ns-real-estate'); ?></option>
                            <?php
                            $property_locations = get_terms(['taxonomy' => 'property_location','hide_empty' => false,]);
                            foreach($property_locations as $property_location) { ?>
                                <option value="<?php echo $property_location->slug; ?>"><?php echo $property_location->name; ?></option>
                            <?php } ?>
                        </select>
                    </div>
                    <div class="form-block">
                        <input type="checkbox" value="false" class="list-properties-featured" />
                        <label style="display:inline;"><?php esc_html_e('Featured', 'ns-real-estate'); ?></label>
                    </div>
                    <a href="#" class="admin-button insert-shortcode insert-shortcode-real-estate"><?php esc_html_e('Insert', 'ns-real-estate'); ?></a>
                </div>

                <div id="real-estate-list-property-tax" class="admin-module no-border">
                    <h3><strong><?php esc_html_e('List Property Taxonomy', 'ns-real-estate'); ?></strong></h3>
                    <div class="form-block">
                        <label><?php esc_html_e('Taxonomy', 'ns-real-estate'); ?></label>
                        <select class="list-property-tax-type" >
                            <option value="property_type"><?php esc_html_e('Property Type', 'ns-real-estate'); ?></option>
                            <option value="property_status"><?php esc_html_e('Property Status', 'ns-real-estate'); ?></option>
                            <option value="property_location"><?php esc_html_e('Property Location', 'ns-real-estate'); ?></option>
                        </select>
                    </div>
                    <div class="form-block">
                        <label><?php esc_html_e('Show Posts', 'ns-real-estate'); ?></label>
                        <input type="number" value="5" class="list-property-tax-show-posts" />
                    </div>
                    <div class="form-block">
                        <label><?php esc_html_e('Layout', 'ns-real-estate'); ?></label>
                        <select class="list-property-tax-layout" >
                            <option value="grid"><?php esc_html_e('Grid', 'ns-real-estate'); ?></option>
                            <option value="carousel"><?php esc_html_e('Carousel', 'ns-real-estate'); ?></option>
                        </select>
                    </div>
                    <div class="form-block">
                        <label><?php esc_html_e('Order By', 'ns-real-estate'); ?></label>
                        <select class="list-property-tax-orderby" >
                            <option value="count"><?php esc_html_e('Count', 'ns-real-estate'); ?></option>
                            <option value="date"><?php esc_html_e('Date', 'ns-real-estate'); ?></option>
                            <option value="name"><?php esc_html_e('Name', 'ns-real-estate'); ?></option>
                            <option value="rand"><?php esc_html_e('Random', 'ns-real-estate'); ?></option>
                        </select>
                    </div>
                    <div class="form-block">
                        <label><?php esc_html_e('Order Direction', 'ns-real-estate'); ?></label>
                        <select class="list-property-tax-order" >
                            <option value="DESC"><?php esc_html_e('Descending', 'ns-real-estate'); ?></option>
                            <option value="ASC"><?php esc_html_e('Ascending', 'ns-real-estate'); ?></option>
                        </select>
                    </div>
                    <div class="form-block">
                        <label><?php esc_html_e('Hide Empty', 'ns-real-estate'); ?></label>
                        <select class="list-property-tax-hide-empty" >
                            <option value="true"><?php esc_html_e('True', 'ns-real-estate'); ?></option>
                            <option value="false"><?php esc_html_e('False', 'ns-real-estate'); ?></option>
                        </select>
                    </div>
                    <a href="#" class="admin-button insert-shortcode insert-shortcode-real-estate"><?php esc_html_e('Insert', 'ns-real-estate'); ?></a>
                </div>

                <div id="real-estate-submit-property" class="admin-module no-border">
                    <h3><strong><?php esc_html_e('Submit Property Form', 'ns-real-estate'); ?></strong></h3>
                    <a href="#" class="admin-button insert-shortcode insert-shortcode-real-estate"><?php esc_html_e('Insert', 'ns-real-estate'); ?></a>
                </div>

                <div id="real-estate-my-properties" class="admin-module no-border">
                    <h3><strong><?php esc_html_e('My Properties', 'ns-real-estate'); ?></strong></h3>
                    <a href="#" class="admin-button insert-shortcode insert-shortcode-real-estate"><?php esc_html_e('Insert', 'ns-real-estate'); ?></a>
                </div>

                <div id="real-estate-filter" class="admin-module no-border">
                    <h3><strong><?php esc_html_e('Property Filter', 'ns-real-estate'); ?></strong></h3>
                    <div class="form-block">
                        <label><?php esc_html_e('Select Filter', 'ns-real-estate'); ?></label>
                        <select class="property-filter-select">
                            <?php
                            $filter_listing_args = array(
                                'post_type' => 'ns-property-filter',
                                'posts_per_page' => -1
                            );
                            $propertyFilters = get_posts($filter_listing_args);
                            foreach($propertyFilters as $filter) { ?>
                                <option value="<?php echo $filter->ID; ?>"><?php echo $filter->post_title.' (#'.$filter->ID.')'; ?></option>
                            <?php } ?>
                        </select>
                    </div>
                    <a href="#" class="admin-button insert-shortcode insert-shortcode-real-estate"><?php esc_html_e('Insert', 'ns-real-estate'); ?></a>
                </div>

                <div id="real-estate-list-agents" class="admin-module no-border">
                    <h3><strong><?php esc_html_e('List Agents', 'ns-real-estate'); ?></strong></h3>
                    <div class="form-block">
                        <label><?php esc_html_e('Show Posts', 'ns-real-estate'); ?></label>
                        <input type="number" value="4" class="list-agents-show-posts" />
                    </div>
                    <div class="form-block">
                        <input type="checkbox" value="false" class="list-agents-show-pagination" />
                        <label style="display:inline;"><?php esc_html_e('Show Pagination', 'ns-real-estate'); ?></label>
                    </div>
                    <a href="#" class="admin-button insert-shortcode insert-shortcode-real-estate"><?php esc_html_e('Insert', 'ns-real-estate'); ?></a>
                </div>

            </div>
        </div>
    </div>
    <?php } ?>
<?php } ); 


//REMOVE <p> AND <br/> TAGS FROM SHORTCODE CONTENT
function ns_real_estate_content_filter($content) {
    $block = join("|",array('ns_list_properties', 'ns_list_property_tax', 'ns_submit_property', 'ns_my_properties', 'ns_property_filter', 'ns_list_agents'));
    $rep = preg_replace("/(<p>)?\[($block)(\s[^\]]+)?\](<\/p>|<br \/>)?/","[$2$3]",$content);
    $rep = preg_replace("/(<p>)?\[\/($block)](<\/p>|<br \/>)?/","[/$2]",$rep);
return $rep;
}
add_filter("the_content", "ns_real_estate_content_filter");

/****************************************************************************/
/* PROPERTY SHORTCODES */
/****************************************************************************/

/** LIST PROPERTIES **/
add_shortcode('ns_list_properties', 'ns_list_properties');
function ns_list_properties($atts, $content = null) {

    $num_properties_per_page = esc_attr(get_option('ns_num_properties_per_page', 12));
    $atts = shortcode_atts(
        array (
            'show_posts' => $num_properties_per_page,
            'show_header' => false,
            'show_pagination' => false,
            'layout' => 'grid',
            'cols' => null,
            'property_status' => '',
            'property_location' => '',
            'property_type' => '',
            'featured' => 'false'
    ), $atts);

    $meta_query_featured = array();
    if ($atts['featured'] != 'false') {
        $meta_query_featured[] = array(
            'key' => 'ns_property_featured',
            'value'   => 'true'
        );
    }

    $args = array(
        'posts_per_page' => $atts['show_posts'],
        'property_status' => $atts['property_status'],
        'property_location' => $atts['property_location'],
        'property_type' => $atts['property_type'],
        'meta_query' => $meta_query_featured,
    );

    ob_start();
    if(function_exists('ns_real_estate_template_loader')) {

        //Set template args
        $template_args = array();
        $template_args['custom_args'] = $args;
        $template_args['custom_show_filter'] = $atts['show_header'];
        $template_args['custom_layout'] = $atts['layout'];
        $template_args['custom_pagination'] = $atts['show_pagination'];
        $template_args['custom_cols'] = $atts['cols'];
        $template_args['no_post_message'] = esc_html__( 'Sorry, no properties were found.', 'ns-real-estate' );

        //Load template
        ns_real_estate_template_loader('loop_properties.php', $template_args);
    }
    $output = ob_get_clean();

    return $output;
}

/** PROPERTIES MAP **/
add_shortcode('ns_properties_map', 'ns_properties_map');
function ns_properties_map($atts, $content = null) {
    ns_real_estate_template_loader('properties_map.php');
}

/** LIST PROPERTY TAXONOMY **/
add_shortcode('ns_list_property_tax', 'ns_list_property_tax');
function ns_list_property_tax($atts, $content = null) {

    $atts = shortcode_atts(
    array (
        'tax' => 'property_type',
        'terms' => '',
        'layout' => 'grid',
        'show_posts' => 5,
        'orderby' => 'count',
        'order' => 'DESC',
        'hide_empty' => 'true',
    ), $atts);

    $count = 1;
    $output = '';

    $args = array('taxonomy' => $atts['tax'], 'orderby' => $atts['orderby'], 'order' => $atts['order']);
    if(!empty($atts['terms'])) { $term_slugs = explode(', ', $atts['terms']); $args['slug'] = $term_slugs; }
    if($atts['hide_empty'] == 'false') { $args['hide_empty'] = false; } else { $args['hide_empty'] = true; }

    $property_types = get_terms($args);

    if ( !empty( $property_types ) && !is_wp_error( $property_types ) ) { 

        if($atts['layout'] == 'carousel') {
            $output .= '<div class="slider-wrap slider-wrap-tax">';
            $output .= '<div class="slider-nav slider-nav-tax"><span class="slider-prev"><i class="fa fa-angle-left"></i></span><span class="slider-next"><i class="fa fa-angle-right"></i></span></div>';
            $output .= '<div class="slider slider-tax">';
            foreach ( $property_types as $property_type ) { 
                if($count <= $atts['show_posts']) {
                    $term_data = get_option('taxonomy_'.$property_type->term_id);
                    if (isset($term_data['img'])) { $term_img = $term_data['img']; } else { $term_img = ''; } 
                    $output .= '<div class="slide slide-tax">';
                    $output .= '<a href="'. esc_attr(get_term_link($property_type->slug, $atts['tax'])) .'">';
                    if(!empty($term_img)) { $output .= '<img src="'.$term_img.'" alt="" />'; }
                    $output .= '<h4>'.$property_type->name.'</h4>';
                    $output .= '<span>'.$property_type->count.' '.esc_html__( 'Properties', 'ns-real-estate' ).'</span>';
                    $output .= '</a>';
                    $output .= '</div>';
                    $count++;
                }
                else {
                    break;
                }
            }
            $output .= '</div>';
            $output .= '</div>';
        } else {
            $output .= '<div class="row row-property-tax">';
            foreach ( $property_types as $property_type ) { 
               if($count <= $atts['show_posts']) {
                    $term_data = get_option('taxonomy_'.$property_type->term_id);
                    if (isset($term_data['img'])) { $term_img = $term_data['img']; } else { $term_img = ''; } 

                    if($count == 1) { $output .= '<div class="col-lg-8 col-md-8 col-property-tax">'; } else { $output .= '<div class="col-lg-4 col-md-4 col-property-tax">'; }
                    $output .= '<a href="'. esc_attr(get_term_link($property_type->slug, $atts['tax'])) .'" style="background:url('. $term_img .') no-repeat center; background-size:cover;" class="property-cat"><div class="img-overlay black"></div><h3>'. $property_type->name .'</h3><span class="button outline small">'.$property_type->count.' '. esc_html__( 'Properties', 'ns-real-estate' ) .'</span></a>'; 
                    $output .= '</div>';
                    $count++;
                } else {
                    break;
                }
            } 
            $output .= '</div>';
        }
    }

    return $output;
}

/** SUBMIT PROPERTY FORM **/
add_shortcode('ns_submit_property', 'ns_submit_property');
function ns_submit_property($atts, $content = null) {
    ob_start();
    ns_real_estate_template_loader('submit_property.php');
    $output = ob_get_clean();
    return $output;
}

/** MY PROPERTIES **/
add_shortcode('ns_my_properties', 'ns_my_properties');
function ns_my_properties($atts, $content=null) {
    $atts = shortcode_atts(
        array (
        'show_posts' => '',
    ), $atts);

    ob_start();

    //Set template args
    $template_args = array();
        
    //Load template
    ns_real_estate_template_loader('my_properties.php', $template_args);

    $output = ob_get_clean();

    return $output;
}

/** PROPERTY FILTER **/
add_shortcode('ns_property_filter', 'ns_property_filter');
function ns_property_filter($atts, $content = null) {

    $atts = shortcode_atts(array ('id' => '',), $atts);
    ob_start();

    $property_filter_id = $atts['id'];
    $values = get_post_custom( $property_filter_id );
    $property_filter_layout = isset( $values['ns_property_filter_layout'] ) ? esc_attr( $values['ns_property_filter_layout'][0] ) : 'middle';

    //Set template args
    $template_args = array();
    $template_args['id'] = $property_filter_id;
    $template_args['shortcode_filter'] = 'true';

    //Load template
    if($property_filter_layout == 'minimal') {
        ns_real_estate_template_loader('property-filter-minimal.php', $template_args);
    } else {
        ns_real_estate_template_loader('property-filter.php', $template_args);
    }

    $output = ob_get_clean();
    return $output;
}

/****************************************************************************/
/* AGENT SHORTCODES */
/****************************************************************************/

/** LIST AGENTS **/
add_shortcode('ns_list_agents', 'ns_list_agents');
function ns_list_agents($atts, $content = null) {

    $num_agents_per_page = esc_attr(get_option('ns_num_agents_per_page', 12));
    $atts = shortcode_atts(
    array (
        'show_posts' => $num_agents_per_page,
        'show_pagination' => false,
    ), $atts);

    $custom_args = array(
        'showposts' => $atts['show_posts'],
    );

    ob_start();
    if(function_exists('ns_real_estate_template_loader')){ 
        
        //Set template args
        $template_args = array();
        $template_args['custom_args'] = $custom_args;
        $template_args['custom_pagination'] = $atts['show_pagination'];
        
        //Load template
        ns_real_estate_template_loader('loop_agents.php', $template_args);
    }
    $output = ob_get_clean();

    return $output;
}

?>