<?php
    //GET GLOBAL SETTINGS
    $num_agents_per_page = esc_attr(get_option('ps_num_agents_per_page', 12));
    $current_page = get_query_var('paged') ? (int) get_query_var('paged') : 1;

    //GET CUSTOM ARGS
    if(isset($template_args)) {
        $custom_args = $template_args['custom_args'];
        $custom_pagination = $template_args['custom_pagination'];
        $custom_cols = $template_args['custom_cols'];
        $no_post_message = $template_args['no_post_message'];
    }

    //GENERATE COLUMN LAYOUT
    $agent_col_num = 3;
    if(isset($custom_cols)) { $agent_col_num = $custom_cols; }
    $agent_col_class = propertyshift_col_class($agent_col_num);

    //SET QUERY ARGS
    $agent_listing_args = array(
        'role__in' => array('ps_agent', 'administrator'),
        'number' => $num_agents_per_page,
        'paged' => $current_page,
    );

    //OVERWRITE QUERY WITH CUSTOM ARGS
    if(isset($custom_args)) { $agent_listing_args = propertyshift_overwrite_query_args($agent_listing_args, $custom_args); }

    //FILTER AND SET QUERY
    $agent_listing_args = apply_filters('propertyshift_pre_get_agents', $agent_listing_args);
    $agents_query = new WP_User_Query($agent_listing_args);
    $agents = $agents_query->get_results();
?>

<div class="row ps-agent-listing">
    <?php 
    if(!empty($agents)) {
        $counter = 1;
        foreach($agents as $agent) {

            $agents_obj = new PropertyShift_Agents();
            $agent_settings = $agents_obj->load_agent_settings($agent->ID);
            if($agent_settings['show_in_listings']['value'] == 'true') { ?>

                <div class="<?php echo esc_attr($agent_col_class); ?>">
                    <?php 
                        $template_args = array();
                        $template_args['id'] = $agent->ID;
                        propertyshift_template_loader('loop_agent.php', $template_args, false); 
                    ?>
                </div>

                <?php 
                if($counter % $agent_col_num == 0) { echo '<div class="clear"></div>'; } 
                $counter++; 
            }

        }
    } else { 
        if(isset($no_post_message)) { echo wp_kses_post($no_post_message); } else { esc_html_e('Sorry, no agents were found.', 'propertyshift'); } 
    } ?>
    <div class="clear"></div>

    <?php
    //output pagination
    $total_users = $agents_query->get_total();
    $num_pages = ceil($total_users / $agent_listing_args['number']);
    $big = 999999999;

    $pagination_args = array(
        'base' => str_replace( $big, '%#%', esc_url( get_pagenum_link( $big ) ) ),
        'format' => '/page/%#%',
        'total' => $num_pages,
        'current' => $current_page,
        'prev_text'    => esc_html__('&raquo; Previous', 'propertyshift'),
        'next_text'    => esc_html__('Next &raquo;', 'propertyshift'),
        'end_size' => 1,
        'mid_size' => 5,
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
    <div class="page-list page-list-agents">
        <?php echo paginate_links($pagination_args); ?> 
    </div>
    <?php } ?>

</div><!-- end agent listings -->