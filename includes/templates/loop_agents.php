<?php
    //Get global settings
	global $post;
    $values = get_post_custom( $post->ID );
    $page_layout = isset( $values['rypecore_page_layout'] ) ? esc_attr( $values['rypecore_page_layout'][0] ) : 'full';
    $num_agents_per_page = esc_attr(get_option('rypecore_num_agents_per_page', 12));
	$paged = (get_query_var('paged')) ? get_query_var('paged') : 1;

    //Get template args
    if(isset($template_args)) {
        $custom_args = $template_args['custom_args'];
        $custom_pagination = $template_args['custom_pagination'];
        $no_post_message = $template_args['no_post_message'];
    }

    //Set pagination
    if(isset($custom_pagination)) { 
        if ($custom_pagination === false || $custom_pagination === 'false') { $custom_pagination = false; } else { $custom_pagination = true; }
        $show_pagination = $custom_pagination; 
    } else { 
        $show_pagination = true; 
    }

    //Set the query
    if(isset($custom_args)) {
        if($show_pagination === true) { $custom_args['paged'] = $paged; }
        $agent_listing_args = $custom_args;
    } else {
        $agent_listing_args = array(
            'post_type' => 'rype-agent',
            'posts_per_page' => $num_agents_per_page,
            'paged' => $paged
        );
    }

	$agent_listing_query = new WP_Query( $agent_listing_args );
?>

<div class="row rype-agent-listing">
<?php if ( $agent_listing_query->have_posts() ) : while ( $agent_listing_query->have_posts() ) : $agent_listing_query->the_post(); ?>

    <?php if($page_layout != 'full') { ?>
        <div class="col-lg-4 col-md-4 col-sm-6"><?php rype_real_estate_template_loader('loop_agent.php', null, false); ?></div>
    <?php } else {  ?>
	   <div class="col-lg-3 col-md-3 col-sm-6"><?php rype_real_estate_template_loader('loop_agent.php', null, false); ?></div>
    <?php } ?>

<?php endwhile; ?>
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
        'prev_text'    => esc_html__('&raquo; Previous', 'rype-real-estate'),
        'next_text'    => esc_html__('Next &raquo;', 'rype-real-estate'),
        'type'         => 'plain',
        'add_args'     => False,
        'add_fragment' => '',
        'before_page_number' => '',
        'after_page_number' => ''
    ); ?>

    <?php 
    if($show_pagination === true) {  ?>
    <div class="page-list">
        <?php echo paginate_links( $args ); ?> 
    </div>
    <?php } ?>

<?php else: ?>
	<div class="col-lg-12">
		<p>
            <?php
            if(isset($no_post_message)) { echo wp_kses_post($no_post_message); } else { esc_html_e('Sorry, no agents were found.', 'rype-real-estate'); } 
            if(is_user_logged_in() && current_user_can('administrator')) { 
                $new_agent_url = esc_url(home_url('/')).'wp-admin/post-new.php?post_type=rype-agent';
                printf(__('<em><b><a href="%s" target="_blank"> Click here</a> to add a new agent.</b></em>', 'rype-real-estate'), $new_agent_url );  
            } ?>
        </p>
	</div>
	</div><!-- end row -->
<?php endif; ?>