<?php
    //global settings
    $icon_set = esc_attr(get_option('ns_core_icon_set', 'fa'));
    $agent_listing_crop = esc_attr(get_option('ns_agent_listing_crop', 'true'));

	//Get agent details
	$agent_details_values = get_post_custom( $post->ID );
    $agent_title = isset( $agent_details_values['rypecore_agent_title'] ) ? esc_attr( $agent_details_values['rypecore_agent_title'][0] ) : '';
	$agent_email = isset( $agent_details_values['rypecore_agent_email'] ) ? esc_attr( $agent_details_values['rypecore_agent_email'][0] ) : '';
	$agent_mobile_phone = isset( $agent_details_values['rypecore_agent_mobile_phone'] ) ? esc_attr( $agent_details_values['rypecore_agent_mobile_phone'][0] ) : '';
	$agent_office_phone = isset( $agent_details_values['rypecore_agent_office_phone'] ) ? esc_attr( $agent_details_values['rypecore_agent_office_phone'][0] ) : '';
	$agent_fb = isset( $agent_details_values['rypecore_agent_fb'] ) ? esc_attr( $agent_details_values['rypecore_agent_fb'][0] ) : '';
	$agent_twitter = isset( $agent_details_values['rypecore_agent_twitter'] ) ? esc_attr( $agent_details_values['rypecore_agent_twitter'][0] ) : '';
	$agent_google = isset( $agent_details_values['rypecore_agent_google'] ) ? esc_attr( $agent_details_values['rypecore_agent_google'][0] ) : '';
	$agent_linkedin = isset( $agent_details_values['rypecore_agent_linkedin'] ) ? esc_attr( $agent_details_values['rypecore_agent_linkedin'][0] ) : '';
	$agent_youtube = isset( $agent_details_values['rypecore_agent_youtube'] ) ? esc_attr( $agent_details_values['rypecore_agent_youtube'][0] ) : '';
	$agent_instagram = isset( $agent_details_values['rypecore_agent_instagram'] ) ? esc_attr( $agent_details_values['rypecore_agent_instagram'][0] ) : '';

    //property post count
    $args = array(
        'post_type' => 'ns-property',
        'showposts' => -1,
        'meta_key' => 'ns_agent_select',
        'meta_value' => get_the_ID()
    );

    $meta_posts = get_posts( $args );
    $meta_post_count = count( $meta_posts );
    unset( $meta_posts);
?>

<div <?php post_class(); ?>>

	<div class="agent-img">
		<?php if ( has_post_thumbnail() ) {  ?>
            <div class="img-fade"></div>
			<a href="<?php the_permalink(); ?>" class="agent-img-link">
                <?php if($agent_listing_crop == 'true') { the_post_thumbnail('agent-thumbnail'); } else { the_post_thumbnail('full'); } ?>  
            </a>
		<?php } else { ?>
			<a href="<?php the_permalink(); ?>" class="agent-img-link"><img src="<?php echo plugins_url( '/ns-real-estate/images/agent-img-default.gif' ); ?>" alt="" /></a>
		<?php } ?>
	</div>
	
	<div class="agent-content">
		  
        <div class="agent-details">

            <?php if(isset($meta_post_count) && $meta_post_count > 0) { ?>
                <a href="<?php the_permalink(); ?>" class="right"><?php echo esc_attr($meta_post_count); ?> <?php if($meta_post_count <= 1) { esc_html_e('Property', 'ns-real-estate'); } else { esc_html_e('Properties', 'ns-real-estate'); } ?></a>
            <?php } ?>

            <div class="agent-title left">
                <h4><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h4>
                <?php if(!empty($agent_title)) { ?><p title="<?php echo esc_attr($agent_title); ?>"><?php echo esc_attr($agent_title); ?></p><?php } ?>
            </div>
            <div class="clear"></div>

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