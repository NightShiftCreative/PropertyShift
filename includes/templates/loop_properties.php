<?php
    //Get global settings
    global $post;
    $properties_page = get_option('rypecore_properties_page');
    $properties_tax_layout = get_option('rypecore_properties_default_layout', 'grid');
    $num_properties_per_page = esc_attr(get_option('rypecore_num_properties_per_page', 12));
    $page_template = get_post_meta($post->ID, '_wp_page_template', true);
    $property_listing_header_display = esc_attr(get_option('rypecore_property_listing_header_display', 'true'));
    
    if(is_front_page()) {  
        $paged = (get_query_var('page')) ? get_query_var('page') : 1;
    } else {  
        $paged = (get_query_var('paged')) ? get_query_var('paged') : 1;
    }

    //Get template args
    if(isset($template_args)) {
        $custom_args = $template_args['custom_args'];
        $custom_show_filter = $template_args['custom_show_filter'];
        $custom_layout = $template_args['custom_layout'];
        $custom_pagination = $template_args['custom_pagination'];
        $no_post_message = $template_args['no_post_message'];
    }
	
    //PAGE SETTINGS
    if(is_tax()) { 
        if(!empty($properties_page)) {
            $properties_page_id = url_to_postid( $properties_page ); 
            $values = get_post_custom( $properties_page_id ); 
            $page_layout = isset( $values['rypecore_page_layout'] ) ? esc_attr( $values['rypecore_page_layout'][0] ) : 'full';
        } else {
            $page_layout = 'full';
        }
    } else { 
        $values = get_post_custom( $post->ID ); 
        $page_layout = isset( $values['rypecore_page_layout'] ) ? esc_attr( $values['rypecore_page_layout'][0] ) : 'full';
    }
	
	//GENERATE PROPERTY CLASS BASED ON PAGE LAYOUT
	if($page_layout == 'full') { 
        $columns_num = 3;
        $property_col_class = 'col-lg-4 col-md-4 col-sm-4'; 
    } else { 
        $columns_num = 2;
        $property_col_class = 'col-lg-6 col-md-6 col-sm-6'; 
    }

    //GET PROPERTY LAYOUT
    if(isset($custom_layout)) {
        if(isset($_GET['custom_layout'])) {
            $property_layout = $_GET['custom_layout']; 
        } else if($custom_layout == 'row') { 
            $property_layout = 'row';  
        } else if($custom_layout == 'tile') {
            $property_layout = 'tile';
        } else {
            $property_layout = 'grid'; 
        }
    } else if(isset($_GET['property_layout'])) {
        $property_layout = $_GET['property_layout']; 
    } else if($page_template == 'template_properties_row.php') {
        $property_layout = 'row';
    } else if($page_template == 'template_properties_map.php' || is_tax()) {
        $property_layout = $properties_tax_layout; 
    } else {
        $property_layout = 'grid'; 
    }

    //DETERMINE HOW POSTS ARE SORTED
    $meta_key = '';
    
    if(isset($_GET['sort_by'])) {
        $sort_by = $_GET['sort_by'];
    } else {
        $sort_by = 'date_desc';
    }

    if ($sort_by == 'date_desc') {
        $order = 'DESC';
    } else if($sort_by == 'date_asc') {
        $order = 'ASC';
    } else if($sort_by == 'price_asc') {
        $order = 'ASC';
        $sort_by = 'meta_value_num';
        $meta_key = 'rypecore_property_price';
    } else if($sort_by == 'price_desc') {
        $order = 'DESC';
        $sort_by = 'meta_value_num';
        $meta_key = 'rypecore_property_price';
    }

    //FILTER FEATURED PROPERTIES
    $meta_query_featured = array();
    if (isset($_GET['featured'])) {
        $meta_query_featured[] = array(
            'key' => 'rypecore_property_featured',
            'value'   => 'true'
        );
    }

	//ADVANCED SEARCH QUERY
    if(isset($_GET['advancedSearch']) && !isset($custom_args)) {

    	if(isset($_GET['priceMin'])) { $priceMin = preg_replace("/[^0-9]/","", $_GET['priceMin']); } else { $priceMin = null; }
        if(isset($_GET['priceMax'])) { $priceMax = preg_replace("/[^0-9]/","", $_GET['priceMax']); } else { $priceMax = null; }

        $areaCompare = '';

        if(empty($_GET['areaMin'])) {
            $areaMin = 0;
        } else {
            $areaMin = preg_replace("/[^0-9]/","", $_GET['areaMin']); 
        }

        if(empty($_GET['areaMax'])) {
            $areaValue = $areaMin;
            $areaCompare = '>=';
        } else {
            $areaMax = preg_replace("/[^0-9]/","", $_GET['areaMax']);
            $areaCompare = 'BETWEEN';
            $areaValue = array( $areaMin, $areaMax );
        }

    	//define meta query
        $meta_query = array();

            if(isset($_GET['priceMin']) && isset($_GET['priceMax'])) {
            	$meta_query[] = array(
                    'key' => 'rypecore_property_price',
                    'value'   => array( $priceMin, $priceMax ),
                    'type'    => 'numeric',
                    'compare' => 'BETWEEN',
                );
            }

            if(!empty($_GET['beds'])) {
                $meta_query[] = array(
                    'key'     => 'rypecore_property_bedrooms',
                    'value'   => $_GET['beds']
                );
            }

            if (!empty($_GET['baths'])) {
                $numBaths = intval($_GET['baths']);
                $numBathsDemical = $numBaths + 0.5;
                $meta_query[] = array(
                    'key' => 'rypecore_property_bathrooms',
                    'compare' => 'IN',
                    'value'   => array($_GET['baths'], $numBathsDemical)
                );
            }

            $meta_query[] = array(
                'key' => 'rypecore_property_area',
                'value'   => $areaValue,
                'type'    => 'numeric',
                'compare' => $areaCompare,
            );

            //custom fields query
            $custom_fields = get_option('rypecore_custom_fields');
            if(!empty($custom_fields)) {
                foreach($custom_fields as $field) {
                    $custom_field_key = strtolower(str_replace(' ', '_', $field['name'])); 
                    if(!empty($_GET[$custom_field_key])) {
                        $meta_query[] = array(
                            'key'     => 'rypecore_custom_field_'.$field['id'],
                            'value'   => $_GET[$custom_field_key]
                        );
                    }
                }
            }

    	$property_listing_args = array(
	        'post_type' => 'rype-property',
	        'posts_per_page' => $num_properties_per_page,
	        'property_status' => $_GET['propertyStatus'],
            'property_location' => $_GET['propertyLocation'],
            'property_type' => $_GET['propertyType'],
            'meta_query' => $meta_query,
            'order' => $order,
            'orderby' => $sort_by,
            'meta_key' => $meta_key,
	        'paged' => $paged
	    );
    } else if(isset($custom_args)) {
        if(!array_key_exists("order", $custom_args)) { $custom_args['order'] = $order; }
        if(!array_key_exists("orderby", $custom_args)) { $custom_args['orderby'] = $sort_by; }
        if(!array_key_exists("meta_key", $custom_args)) { $custom_args['meta_key'] = $meta_key; }
        if(is_front_page()) {
            if(!array_key_exists("page", $custom_args)) { $custom_args['page'] = $paged; }
        } else {
            if(!array_key_exists("paged", $custom_args)) { $custom_args['paged'] = $paged; }
        }
        $property_listing_args = $custom_args;
    } else {

        if(empty($property_location)) { $property_location = ''; }
        if(empty($property_status)) { $property_status = ''; }
        if(empty($property_type)) { $property_type = ''; }

    	$property_listing_args = array(
	        'post_type' => 'rype-property',
	        'posts_per_page' => $num_properties_per_page,
            'meta_query' => $meta_query_featured,
            'order' => $order,
            'orderby' => $sort_by,
            'meta_key' => $meta_key,
	        'paged' => $paged,
            'property_location' => $property_location,
            'property_status' => $property_status,
            'property_type' => $property_type,
	    );
    }

	$property_listing_query = new WP_Query( $property_listing_args );
?>

<?php 
if($property_listing_header_display == 'true') { 
    if(isset($custom_show_filter) && $custom_show_filter != 'true') {
	   //do nothing
    } else {
        rype_real_estate_template_loader('property-listing-header.php', ['query' => $property_listing_query]); 
    }
}
?>

<div class="row rype-property-listing">
<?php 
$i = 0;

if ( $property_listing_query->have_posts() ) : while ( $property_listing_query->have_posts() ) : $property_listing_query->the_post(); ?>

    <?php if ($property_layout == 'row' || $property_layout == 'grid') { ?>

        <?php if ($property_layout == 'row') { ?>
            <div class="col-lg-12"><?php rype_real_estate_template_loader('loop_property_row.php'); ?></div>
        <?php } else { ?>
            <div class="<?php echo esc_attr($property_col_class); ?>"><?php rype_real_estate_template_loader('loop_property_grid.php'); ?></div>
        <?php } ?>

        <?php if($i % $columns_num == $columns_num - 1 ) {  echo '</div> <div class="row listing">'; } $i++; ?>

    <?php } else if($property_layout == 'tile') {
        rype_real_estate_template_loader('loop_property_grid.php');
    } ?>

<?php endwhile; ?>
	</div><!-- end row -->
	
	<?php 
	wp_reset_postdata();
    $big = 999999999; // need an unlikely integer
    if(is_front_page()) { $current_page = get_query_var('page'); } else { $current_page = get_query_var('paged'); }

    $args = array(
        'base'         => str_replace( $big, '%#%', esc_url( get_pagenum_link( $big ) ) ),
        'format'       => '/page/%#%',
        'total'        => $property_listing_query->max_num_pages,
        'current'      => max( 1, $current_page ),
        'show_all'     => False,
        'end_size'     => 1,
        'mid_size'     => 2,
        'prev_next'    => True,
        'prev_text'    => esc_html__('&raquo; Previous', 'rypecore'),
        'next_text'    => esc_html__('Next &raquo;', 'rypecore'),
        'type'         => 'plain',
        'add_args'     => False,
        'add_fragment' => '',
        'before_page_number' => '',
        'after_page_number' => ''
    ); ?>
	

    <?php 
    //DETERMINE IF PAGINATION IS NEEDED
    if(isset($custom_pagination)) { 
        if ($custom_pagination === false || $custom_pagination === 'false') { $custom_pagination = false; } else { $custom_pagination = true; }
        $show_pagination = $custom_pagination; 
    } else { 
        $show_pagination = true; 
    } 
    
    if($show_pagination === true) {  ?>
	<div class="page-list">
        <?php echo paginate_links( $args ); ?> 
    </div>
    <?php } ?>
	
<?php else: ?>
	<div class="col-lg-12">
        <p>
            <?php 
            if(isset($no_post_message)) { echo wp_kses_post($no_post_message); } else { esc_html_e('Sorry, no properties were found.', 'rypecore'); }
            if(is_user_logged_in() && current_user_can('administrator')) { 
                $new_property_url = esc_url(home_url('/')).'wp-admin/post-new.php?post_type=rype-property';
                printf(__('<em><b><a href="%s" target="_blank"> Click here</a> to add a new property.</b></em>', 'rypecore'), $new_property_url );  
            } ?>
        </p>
    </div>
	</div><!-- end row -->
<?php endif; ?>