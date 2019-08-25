<?php
    //GET GLOBAL SETTINGS
	global $post;
    $num_agents_per_page = esc_attr(get_option('ns_num_agents_per_page', 12));

    //GET CUSTOM ARGS
    if(isset($template_args)) {
        $custom_args = $template_args['custom_args'];
        $custom_pagination = $template_args['custom_pagination'];
        $custom_cols = $template_args['custom_cols'];
        $no_post_message = $template_args['no_post_message'];
    }

    //GENERATE COLUMN LAYOUT
    $agent_col_class = 'col-lg-4 col-md-4 col-sm-6 ns-listing-col'; 
    $agent_col_num = 3;

    if(isset($custom_cols)) {
        switch($custom_cols) {
            case 1:
                $agent_col_class = 'col-lg-12 ns-listing-col';
                $$agent_col_num = 1;
                break;
            case 2:
                $agent_col_class = 'col-lg-6 ns-listing-col'; 
                $agent_col_num = 2;
                break;
            case 3:
                $agent_col_class = 'col-lg-4 ns-listing-col'; 
                $agent_col_num = 3;
                break;
            case 4:
                $agent_col_class = 'col-lg-3 ns-listing-col';
                $agent_col_num = 4; 
                break;
        }
    }

    /***************************************************************************/
    /** SET QUERY ARGS **/
    /***************************************************************************/

    //SET PAGED VARIABLE
    if(is_front_page()) {  
        $paged = (get_query_var('page')) ? get_query_var('page') : 1;
    } else {  
        $paged = (get_query_var('paged')) ? get_query_var('paged') : 1;
    }

    //SET ARGS
    $agent_listing_args = array(
        'post_type' => 'ps-agent',
        'posts_per_page' => $num_agents_per_page,
        'paged' => $paged
    );

    //OVERWRITE QUERY WITH CUSTOM ARGS
    if(isset($custom_args)) {
        foreach($agent_listing_args as $key=>$value) {
            if(array_key_exists($key, $custom_args)) { 
                if(!empty($custom_args[$key])) { $agent_listing_args[$key] = $custom_args[$key]; }
            } 
        }
        foreach($custom_args as $key=>$value) {
            if(!array_key_exists($key, $agent_listing_args)) { 
                if(!empty($custom_args[$key])) { $agent_listing_args[$key] = $custom_args[$key]; }
            } 
        }
    }

	$agent_listing_query = new WP_Query( $agent_listing_args );
?>

<div class="row ps-agent-listing">
<?php $counter = 1; ?>
<?php if ( $agent_listing_query->have_posts() ) : while ( $agent_listing_query->have_posts() ) : $agent_listing_query->the_post(); ?>

    <div class="<?php echo esc_attr($agent_col_class); ?>"><?php propertyshift_template_loader('loop_agent.php', null, false); ?></div>

    <?php 
    if($counter % $agent_col_num == 0) { echo '<div class="clear"></div>'; } 
    $counter++; 
    ?>

<?php endwhile; ?>
    <div class="clear"></div>
	</div><!-- end row -->

    <?php 
    wp_reset_postdata();
    $big = 999999999; // need an unlikely integer

    $args = array(
        'base'         => str_replace( $big, '%#%', esc_url( get_pagenum_link( $big ) ) ),
        'format'       => '/page/%#%',
        'total'        => $agent_listing_query->max_num_pages,
        'current'      => max( 1, get_query_var('paged') ),
        'show_all'     => False,
        'end_size'     => 1,
        'mid_size'     => 2,
        'prev_next'    => True,
        'prev_text'    => esc_html__('&raquo; Previous', 'ns-real-estate'),
        'next_text'    => esc_html__('Next &raquo;', 'ns-real-estate'),
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
            if(isset($no_post_message)) { echo wp_kses_post($no_post_message); } else { esc_html_e('Sorry, no agents were found.', 'ns-real-estate'); } 
            if(is_user_logged_in() && current_user_can('administrator')) { 
                $new_agent_url = esc_url(home_url('/')).'wp-admin/post-new.php?post_type=ps-agent';
                printf(__('<em><b><a href="%s" target="_blank"> Click here</a> to add a new agent.</b></em>', 'ns-real-estate'), $new_agent_url );  
            } ?>
        </p>
	</div>
    <div class="clear"></div>
	</div><!-- end row -->
<?php endif; ?>