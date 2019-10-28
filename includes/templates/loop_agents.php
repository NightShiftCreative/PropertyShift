<?php
    //GET GLOBAL SETTINGS
    $num_agents_per_page = esc_attr(get_option('ps_num_agents_per_page', 12));

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
                $agent_col_num = 1;
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

    //GET AGENTS
    $agent_listing_args = array(
        'role__in' => array('ps_agent', 'administrator'),
        'number' => $num_agents_per_page,
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

    $agents = get_users($agent_listing_args);

?>

<div class="row ps-agent-listing">
<?php 
if(!empty($agents)) {
    $counter = 1;
    foreach($agents as $agent) {

        $agents_obj = new PropertyShift_Agents();
        $agent_settings = $agents_obj->load_agent_settings($agent->ID);
        if($agent_settings['show_in_listings']['value']) { ?>

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
</div>