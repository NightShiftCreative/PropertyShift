<?php 
add_action( 'media_buttons', function($editor_id) { ?>
    
    <a href="#" data-featherlight="#shortcode-selector-real-estate" data-featherlight-persist="true" class="button add-shortcode" title="<?php esc_html_e('Add Real Estate Shortcode', 'rype-real-estate'); ?>">
        <span class="wp-media-buttons-icon add-shortcode-icon"></span><?php esc_html_e('Real Estate Shortcodes', 'rype-real-estate'); ?>
    </a>

    <div class="shortcode-selector" id="shortcode-selector-real-estate">
        <div class="featherlight-header"><?php esc_html_e('Insert a Shortcode', 'rype-real-estate'); ?></div>
        <div class="shortcode-selector-inner">
            <div class="shortcode-selector-list">
                <p><?php esc_html_e('Choose a shortcode to insert from the list below:', 'rype-real-estate'); ?></p>
                <a href="#real-estate-list-properties" class="button has-options"><i class="fa fa-align-justify"></i><?php esc_html_e('List Properties', 'rype-real-estate'); ?></a>
                <a href="#real-estate-list-property-tax" class="button has-options"><i class="fa fa-align-justify"></i><?php esc_html_e('List Property Taxonomy', 'rype-real-estate'); ?></a>
                <a href="#real-estate-list-agents" class="button has-options"><i class="fa fa-group"></i><?php esc_html_e('List Agents', 'rype-real-estate'); ?></a>
            </div>
            <div class="shortcode-selector-options">
                <div class="button cancel-shortcode"><i class="fa fa-reply"></i> <?php esc_html_e('Go Back', 'rype-real-estate'); ?></div>

                <div id="real-estate-list-properties" class="admin-module no-border">
                    <h3><strong><?php esc_html_e('List Properties', 'rype-real-estate'); ?></strong></h3>
                    <div class="form-block">
                        <label><?php esc_html_e('Show Posts', 'rype-real-estate'); ?></label>
                        <input type="number" value="3" class="list-properties-show-posts" />
                    </div>
                    <div class="form-block">
                        <input type="checkbox" value="false" class="list-properties-show-header" />
                        <label style="display:inline;"><?php esc_html_e('Show Header', 'rype-real-estate'); ?></label>
                    </div>
                    <div class="form-block">
                        <input type="checkbox" value="false" class="list-properties-show-pagination" />
                        <label style="display:inline;"><?php esc_html_e('Show Pagination', 'rype-real-estate'); ?></label>
                    </div>
                    <div class="form-block">
                        <label><?php esc_html_e('Layout', 'rype-real-estate'); ?></label>
                        <select class="list-properties-layout" >
                            <option value="grid"><?php esc_html_e('Grid', 'rype-real-estate'); ?></option>
                            <option value="row"><?php esc_html_e('Row', 'rype-real-estate'); ?></option>
                        </select>
                    </div>
                    <div class="form-block">
                        <label><?php esc_html_e('Property Status', 'rype-real-estate'); ?></label>
                        <select class="list-properties-status">
                            <option value=""><?php esc_html_e('Any', 'rype-real-estate'); ?></option>
                            <?php
                            $property_statuses = get_terms(['taxonomy' => 'property_status','hide_empty' => false,]);
                            foreach($property_statuses as $property_status) { ?>
                                <option value="<?php echo $property_status->slug; ?>"><?php echo $property_status->name; ?></option>
                            <?php } ?>
                        </select>
                    </div>
                    <div class="form-block">
                        <label><?php esc_html_e('Property Type', 'rype-real-estate'); ?></label>
                        <select class="list-properties-type">
                            <option value=""><?php esc_html_e('Any', 'rype-real-estate'); ?></option>
                            <?php
                            $property_types = get_terms(['taxonomy' => 'property_type','hide_empty' => false,]);
                            foreach($property_types as $property_type) { ?>
                                <option value="<?php echo $property_type->slug; ?>"><?php echo $property_type->name; ?></option>
                            <?php } ?>
                        </select>
                    </div>
                    <div class="form-block">
                        <label><?php esc_html_e('Property Location', 'rype-real-estate'); ?></label>
                        <select class="list-properties-location">
                            <option value=""><?php esc_html_e('Any', 'rype-real-estate'); ?></option>
                            <?php
                            $property_locations = get_terms(['taxonomy' => 'property_location','hide_empty' => false,]);
                            foreach($property_locations as $property_location) { ?>
                                <option value="<?php echo $property_location->slug; ?>"><?php echo $property_location->name; ?></option>
                            <?php } ?>
                        </select>
                    </div>
                    <div class="form-block">
                        <input type="checkbox" value="false" class="list-properties-featured" />
                        <label style="display:inline;"><?php esc_html_e('Featured', 'rype-real-estate'); ?></label>
                    </div>
                    <a href="#" class="admin-button insert-shortcode insert-shortcode-real-estate"><?php esc_html_e('Insert', 'rype-real-estate'); ?></a>
                </div>

                <div id="real-estate-list-property-tax" class="admin-module no-border">
                    <h3><strong><?php esc_html_e('List Property Taxonomy', 'rype-real-estate'); ?></strong></h3>
                    <div class="form-block">
                        <label><?php esc_html_e('Taxonomy', 'rype-real-estate'); ?></label>
                        <select class="list-property-tax-type" >
                            <option value="property_type"><?php esc_html_e('Property Type', 'rype-real-estate'); ?></option>
                            <option value="property_status"><?php esc_html_e('Property Status', 'rype-real-estate'); ?></option>
                            <option value="property_location"><?php esc_html_e('Property Location', 'rype-real-estate'); ?></option>
                        </select>
                    </div>
                    <div class="form-block">
                        <label><?php esc_html_e('Show Posts', 'rype-real-estate'); ?></label>
                        <input type="number" value="5" class="list-property-tax-show-posts" />
                    </div>
                    <div class="form-block">
                        <label><?php esc_html_e('Layout', 'rype-real-estate'); ?></label>
                        <select class="list-property-tax-layout" >
                            <option value="grid"><?php esc_html_e('Grid', 'rype-real-estate'); ?></option>
                            <option value="carousel"><?php esc_html_e('Carousel', 'rype-real-estate'); ?></option>
                        </select>
                    </div>
                    <div class="form-block">
                        <label><?php esc_html_e('Order By', 'rype-real-estate'); ?></label>
                        <select class="list-property-tax-orderby" >
                            <option value="count"><?php esc_html_e('Count', 'rype-real-estate'); ?></option>
                            <option value="date"><?php esc_html_e('Date', 'rype-real-estate'); ?></option>
                            <option value="name"><?php esc_html_e('Name', 'rype-real-estate'); ?></option>
                            <option value="rand"><?php esc_html_e('Random', 'rype-real-estate'); ?></option>
                        </select>
                    </div>
                    <div class="form-block">
                        <label><?php esc_html_e('Order Direction', 'rype-real-estate'); ?></label>
                        <select class="list-property-tax-order" >
                            <option value="DESC"><?php esc_html_e('Descending', 'rype-real-estate'); ?></option>
                            <option value="ASC"><?php esc_html_e('Ascending', 'rype-real-estate'); ?></option>
                        </select>
                    </div>
                    <div class="form-block">
                        <label><?php esc_html_e('Hide Empty', 'rype-real-estate'); ?></label>
                        <select class="list-property-tax-hide-empty" >
                            <option value="true"><?php esc_html_e('True', 'rype-real-estate'); ?></option>
                            <option value="false"><?php esc_html_e('False', 'rype-real-estate'); ?></option>
                        </select>
                    </div>
                    <a href="#" class="admin-button insert-shortcode insert-shortcode-real-estate"><?php esc_html_e('Insert', 'rype-real-estate'); ?></a>
                </div>

                <div id="real-estate-list-agents" class="admin-module no-border">
                    <h3><strong><?php esc_html_e('List Agents', 'rype-real-estate'); ?></strong></h3>
                    <div class="form-block">
                        <label><?php esc_html_e('Show Posts', 'rype-real-estate'); ?></label>
                        <input type="number" value="4" class="list-agents-show-posts" />
                    </div>
                    <div class="form-block">
                        <input type="checkbox" value="false" class="list-agents-show-pagination" />
                        <label style="display:inline;"><?php esc_html_e('Show Pagination', 'rype-real-estate'); ?></label>
                    </div>
                    <a href="#" class="admin-button insert-shortcode insert-shortcode-real-estate"><?php esc_html_e('Insert', 'rype-real-estate'); ?></a>
                </div>

            </div>
        </div>
    </div>
<?php } ); 


//REMOVE <p> AND <br/> TAGS FROM SHORTCODE CONTENT
function rype_real_estate_content_filter($content) {
    $block = join("|",array('rype_list_properties', 'rype_list_property_tax', 'rype_property_filter', 'rype_list_agents'));
    $rep = preg_replace("/(<p>)?\[($block)(\s[^\]]+)?\](<\/p>|<br \/>)?/","[$2$3]",$content);
    $rep = preg_replace("/(<p>)?\[\/($block)](<\/p>|<br \/>)?/","[/$2]",$rep);
return $rep;
}
add_filter("the_content", "rype_real_estate_content_filter");

/****************************************************************************/
/* PROPERTY SHORTCODES */
/****************************************************************************/

/** LIST PROPERTIES **/
add_shortcode('rype_list_properties', 'rype_list_properties');
function rype_list_properties($atts, $content = null) {

    $atts = shortcode_atts(
        array (
            'show_posts' => '3',
            'show_header' => false,
            'show_pagination' => false,
            'layout' => 'grid',
            'property_status' => '',
            'property_location' => '',
            'property_type' => '',
            'featured' => 'false'
        ), $atts);

    $meta_query_featured = array();
    if ($atts['featured'] != 'false') {
        $meta_query_featured[] = array(
            'key' => 'rypecore_property_featured',
            'value'   => 'true'
        );
    }

    $args = array(
        'post_type' => 'rype-property',
        'showposts' => $atts['show_posts'],
        'property_status' => $atts['property_status'],
        'property_location' => $atts['property_location'],
        'property_type' => $atts['property_type'],
        'meta_query' => $meta_query_featured,
    );

    ob_start();
    if(function_exists('rype_real_estate_template_properties')){ 
        rype_real_estate_template_properties($args, $atts['show_header'], $atts['layout'], $atts['show_pagination'], 'Sorry, no properties were found.');
    }
    $output = ob_get_clean();

    return $output;
}

/** LIST PROPERTY TAXONOMY **/
add_shortcode('rype_list_property_tax', 'rype_list_property_tax');
function rype_list_property_tax($atts, $content = null) {

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
                    $output .= '<span>'.$property_type->count.' '.esc_html__( 'Properties', 'rype-real-estate' ).'</span>';
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
                    $output .= '<a href="'. esc_attr(get_term_link($property_type->slug, $atts['tax'])) .'" style="background:url('. $term_img .') no-repeat center; background-size:cover;" class="property-cat"><div class="img-overlay black"></div><h3>'. $property_type->name .'</h3><span class="button outline small">'.$property_type->count.' '. esc_html__( 'Properties', 'rype-real-estate' ) .'</span></a>'; 
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

/** PROPERTY FILTER **/
add_shortcode('rype_property_filter', 'rype_property_filter');
function rype_property_filter($atts, $content = null) {

    $atts = shortcode_atts(array ('id' => '',), $atts);
    ob_start();

    $property_filter_id = $atts['id'];
    $values = get_post_custom( $property_filter_id );
    $property_filter_layout = isset( $values['rypecore_property_filter_layout'] ) ? esc_attr( $values['rypecore_property_filter_layout'][0] ) : 'middle';      
    $shortcode_filter = 'true';
    if($property_filter_layout == 'minimal') {
        include(get_parent_theme_file_path('/template_parts/real_estate/property-filter-minimal.php'));
    } else {
        include(get_parent_theme_file_path('/template_parts/real_estate/property-filter.php'));
    }

    $output = ob_get_clean();
    return $output;
}

/****************************************************************************/
/* AGENT SHORTCODES */
/****************************************************************************/

/** LIST AGENTS **/
add_shortcode('rype_list_agents', 'rype_list_agents');
function rype_list_agents($atts, $content = null) {

    $atts = shortcode_atts(
    array (
        'show_posts' => '4',
        'show_pagination' => false,
    ), $atts);

    $args = array(
        'post_type' => 'rype-agent',
        'showposts' => $atts['show_posts'],
    );

    ob_start();
    if(function_exists('rype_real_estate_get_custom_agents')){ 
        rype_real_estate_get_custom_agents($args, $atts['show_pagination'], esc_html__( 'Sorry, no agents were found.', 'rype-real-estate' )); 
    }
    $output = ob_get_clean();

    return $output;
}

?>