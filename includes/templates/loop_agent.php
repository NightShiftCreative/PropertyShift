<?php
    //global settings
    $admin_obj = new PropertyShift_Admin();
    $icon_set = esc_attr(get_option('ns_core_icon_set', 'fa'));
    if(function_exists('ns_core_load_theme_options')) { $icon_set = ns_core_load_theme_options('ns_core_icon_set'); }
    $agent_listing_crop = $admin_obj->load_settings(false, 'ps_agent_listing_crop');

	//Get agent details
    $agents_obj = new PropertyShift_Agents();
    $agent_settings = $agents_obj->load_agent_settings($post->ID);
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
    $agent_properties = $agents_obj->get_agent_properties(get_the_id());
    $agent_properties_count = $agent_properties['count'];
?>

<div <?php post_class(); ?>>

	<div class="agent-img">
		<?php if ( has_post_thumbnail() ) {  ?>
			<a href="<?php the_permalink(); ?>" class="agent-img-link">
                <?php if($agent_listing_crop == 'true') { the_post_thumbnail('agent-thumbnail'); } else { the_post_thumbnail('full'); } ?>  
            </a>
		<?php } else { ?>
			<a href="<?php the_permalink(); ?>" class="agent-img-link"><img src="<?php echo PROPERTYSHIFT_DIR.'/images/agent-img-default.gif'; ?>" alt="" /></a>
		<?php } ?>
	</div>
	
	<div class="agent-content">
		  
        <?php if(isset($agent_properties_count) && $agent_properties_count > 0) { ?>
            <a href="<?php the_permalink(); ?>" class="agent-property-count right"><?php echo esc_attr($agent_properties_count); ?> <?php if($agent_properties_count <= 1) { esc_html_e('Property', 'propertyshift'); } else { esc_html_e('Properties', 'propertyshift'); } ?></a>
        <?php } ?>

        <div class="agent-title left">
            <h4><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h4>
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