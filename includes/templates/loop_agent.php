<?php
    //global settings
    $icon_set = esc_attr(get_option('ns_core_icon_set', 'fa'));
    if(function_exists('ns_core_load_theme_options')) { $icon_set = ns_core_load_theme_options('ns_core_icon_set'); }

    //Get agent details
    $agent_id = $template_args['id'];

    $agents_obj = new PropertyShift_Agents();
    $agent_settings = $agents_obj->load_agent_settings($agent_id);
    $agent_display_name = $agent_settings['display_name']['value'];
    $agent_avatar_url = $agent_settings['avatar_url']['value'];
    $agent_email = $agent_settings['email']['value'];
    $agent_title = $agent_settings['job_title']['value'];
    $agent_mobile_phone = $agent_settings['mobile_phone']['value'];
    $agent_office_phone = $agent_settings['office_phone']['value'];
    $agent_fb = $agent_settings['facebook']['value'];
    $agent_twitter = $agent_settings['twitter']['value'];
    $agent_google = $agent_settings['google']['value'];
    $agent_linkedin = $agent_settings['linkedin']['value'];
    $agent_youtube = $agent_settings['youtube']['value'];
    $agent_instagram = $agent_settings['instagram']['value'];

    //Get agent property count
    $agent_properties = $agents_obj->get_agent_properties($agent_id);
    $agent_properties_count = $agent_properties['count'];
?>

<div <?php post_class(); ?>>

	<div class="agent-img">
		<?php if(!empty($agent_avatar_url)) {  ?>
			<a href="<?php echo get_author_posts_url($agent_id); ?>" class="agent-img-link">
                <img src="<?php echo $agent_avatar_url; ?>" alt="<?php echo $agent_display_name; ?>" />  
            </a>
		<?php } else { ?>
			<a href="<?php echo get_author_posts_url($agent_id); ?>" class="agent-img-link"><img src="<?php echo PROPERTYSHIFT_DIR.'/images/agent-img-default.gif'; ?>" alt="" /></a>
		<?php } ?>
	</div>
	
	<div class="agent-content">
		  
        <?php if(isset($agent_properties_count) && $agent_properties_count > 0) { ?>
            <a href="<?php echo get_author_posts_url($agent_id); ?>" class="agent-property-count right"><?php echo esc_attr($agent_properties_count); ?> <?php if($agent_properties_count <= 1) { esc_html_e('Property', 'propertyshift'); } else { esc_html_e('Properties', 'propertyshift'); } ?></a>
        <?php } ?>

        <div class="agent-title left">
            <h4><a href="<?php echo get_author_posts_url($agent_id); ?>"><?php echo $agent_display_name; ?></a></h4>
            <?php if(!empty($agent_title)) { ?><p title="<?php echo esc_attr($agent_title); ?>"><?php echo esc_attr($agent_title); ?></p><?php } ?>
        </div>
        <div class="clear"></div>

        <div class="agent-details">
            <?php if(!empty($agent_email)) { ?><p title="<?php echo esc_attr($agent_email); ?>"><?php echo ns_core_get_icon($icon_set, 'envelope', 'envelope', 'mail'); ?><?php echo esc_attr($agent_email); ?></p><?php } ?>
            <?php if(!empty($agent_mobile_phone)) { ?><p title="<?php echo esc_attr($agent_mobile_phone); ?>"><?php echo ns_core_get_icon($icon_set, 'phone', 'telephone'); ?><?php echo esc_attr($agent_mobile_phone); ?></p><?php } ?>
        </div>

        <?php if(!empty($agent_fb) || !empty($agent_twitter) || !empty($agent_google) || !empty($agent_linkedin) || !empty($agent_youtube) || !empty($agent_instagram)) { ?>
        <div class="center">
            <ul class="social-icons circle clean-list">
                <?php if(!empty($agent_fb)) { ?><li class="agent-footer-item"><a href="<?php echo esc_url($agent_fb); ?>" target="_blank"><i class="fab fa-facebook-f"></i></a></li><?php } ?>
                <?php if(!empty($agent_twitter)) { ?><li class="agent-footer-item"><a href="<?php echo esc_url($agent_twitter); ?>" target="_blank"><i class="fab fa-twitter"></i></a></li><?php } ?>
                <?php if(!empty($agent_google)) { ?><li class="agent-footer-item"><a href="<?php echo esc_url($agent_google); ?>" target="_blank"><i class="fab fa-google-plus-g"></i></a></li><?php } ?>
                <?php if(!empty($agent_linkedin)) { ?><li class="agent-footer-item"><a href="<?php echo esc_url($agent_linkedin); ?>" target="_blank"><i class="fab fa-linkedin-in"></i></a></li><?php } ?>
                <?php if(!empty($agent_youtube)) { ?><li class="agent-footer-item"><a href="<?php echo esc_url($agent_youtube); ?>" target="_blank"><i class="fab fa-youtube"></i></a></li><?php } ?>
                <?php if(!empty($agent_instagram)) { ?><li class="agent-footer-item"><a href="<?php echo esc_url($agent_instagram); ?>" target="_blank"><i class="fab fa-instagram"></i></a></li><?php } ?>
            </ul>
        </div>
        <?php } ?>

	</div>
</div>