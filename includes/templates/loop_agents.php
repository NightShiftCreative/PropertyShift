<?php
    //GET GLOBAL SETTINGS
    $num_agents_per_page = esc_attr(get_option('ps_num_agents_per_page', 12));

    //GENERATE COLUMN LAYOUT
    $agent_col_class = 'col-lg-4 col-md-4 col-sm-6 ns-listing-col'; 
    $agent_col_num = 3;

    //GET AGENTS
    $agent_args = array(
        'role__in' => array('ps_agent', 'administrator'),
        'number' => $num_agents_per_page,
    );
    $agents = get_users($agent_args);

?>

<div class="row ps-agent-listing">
<?php $counter = 1; ?>
<?php foreach($agents as $agent) { ?>

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
    ?>

<?php } ?>