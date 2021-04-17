<?php
    //GET GLOBAL SETTINGS
    global $post;
    $properties_page = get_option('ps_properties_page');
    $properties_tax_layout = get_option('ps_properties_default_layout', 'grid');
    $page_template = get_post_meta($post->ID, '_wp_page_template', true);
    $property_listing_header_display = esc_attr(get_option('ps_property_listing_header_display', 'true'));

    //GET CUSTOM ARGS
    if(isset($template_args)) {
        $custom_args = isset($template_args['custom_args']) ? $template_args['custom_args'] : null;
        $custom_show_filter = isset($template_args['custom_show_filter']) ? $template_args['custom_show_filter'] : null;
        $custom_layout = isset($template_args['custom_layout']) ? $template_args['custom_layout'] : null;
        $custom_pagination = isset($template_args['custom_pagination']) ? $template_args['custom_pagination'] : null;
        $custom_cols = isset($template_args['custom_cols']) ? $template_args['custom_cols'] : null;
        $no_post_message = isset($template_args['no_post_message']) ? $template_args['no_post_message'] : null;
    }

    //GET PROPERTY LAYOUT
    if(isset($custom_layout)) {
        if(isset($_GET['custom_layout'])) {
            $property_layout = sanitize_text_field($_GET['custom_layout']); 
        } else if($custom_layout == 'row') { 
            $property_layout = 'row';  
        } else if($custom_layout == 'tile') {
            $property_layout = 'tile';
        } else {
            $property_layout = 'grid'; 
        }
    } else if(isset($_GET['property_layout'])) {
        $property_layout = sanitize_text_field($_GET['property_layout']); 
    } else if($page_template == 'template_properties_row.php') {
        $property_layout = 'row';
    } else if($page_template == 'template_properties_map.php' || is_tax()) {
        $property_layout = $properties_tax_layout; 
    } else {
        $property_layout = 'grid'; 
    }

    //GENERATE COLUMN LAYOUT
    $property_col_num = 2;
    if(isset($property_layout) && $property_layout == 'row') {
        $property_col_num = 1;
    } else if(isset($custom_cols)) { 
        $property_col_num = $custom_cols; 
    }
    $property_col_class = propertyshift_col_class($property_col_num);


    /***************************************************************************/
    /** SET QUERY **/
    /***************************************************************************/
    $properties_obj = new PropertyShift_Properties();
    $property_listing = $properties_obj->get_properties($custom_args);
    $property_listing_query = $property_listing['query'];
?>

<div class="ps-property-listing-wrap">

<?php 
if($property_listing_header_display == 'true') { 
    if(isset($custom_show_filter) && $custom_show_filter != 'true') {
	   //do nothing
    } else {
        propertyshift_template_loader('property-listing-header.php', ['query' => $property_listing_query], false); 
    }
}
?>

<div class="ps-listing ps-property-listing">
<?php
if ( $property_listing_query->have_posts() ) : while ( $property_listing_query->have_posts() ) : $property_listing_query->the_post(); ?>

    <div class="<?php echo esc_attr($property_col_class); ?>">
        <?php propertyshift_template_loader('loop_property_grid.php', null, false); ?>  
    </div>

<?php endwhile; ?>
    <div class="clear"></div>
	</div><!-- end listings -->
	
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
        'prev_text'    => esc_html__('&raquo; Previous', 'propertyshift'),
        'next_text'    => esc_html__('Next &raquo;', 'propertyshift'),
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
	   <div class="page-list"><?php echo paginate_links( $args ); ?></div>
    <?php } ?>
	
<?php else: ?>
	<div class="ps-no-posts">
        <p>
            <?php 
            if(isset($no_post_message)) { echo wp_kses_post($no_post_message); } else { esc_html_e('Sorry, no properties were found.', 'propertyshift'); }
            if(is_user_logged_in() && current_user_can('administrator')) { 
                $new_property_url = esc_url(home_url('/')).'wp-admin/post-new.php?post_type=ps-property';
                printf(__('<em><b><a href="%s" target="_blank"> Click here</a> to add a new property.</b></em>', 'propertyshift'), $new_property_url );  
            } ?>
        </p>
    </div>
    <div class="clear"></div>
	</div><!-- end listings -->
<?php endif; ?>

</div><!-- end listings wrap -->