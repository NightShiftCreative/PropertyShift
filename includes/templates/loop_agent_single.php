<?php
    //Global settings
    $admin_obj = new PropertyShift_Admin();
    $icon_set = esc_attr(get_option('ns_core_icon_set', 'fa'));
    if(function_exists('ns_core_load_theme_options')) { $icon_set = ns_core_load_theme_options('ns_core_icon_set'); }
    $num_properties_per_page = $admin_obj->load_settings(false, 'ps_num_properties_per_page');
    $agent_detail_items = $admin_obj->load_settings(false, 'ps_agent_detail_items', false);

    //Get template location
    if(isset($template_args)) { $template_location = $template_args['location']; } else { $template_location = ''; }
    if($template_location == 'sidebar') { 
        $template_location_sidebar = 'true'; 
    } else { 
        $template_location_sidebar = 'false';
    }

	//Get agent details
    $agents_obj = new PropertyShift_Agents();
    $agent_settings = $agents_obj->load_agent_settings($post->ID);
    $agent_avatar_url = $agent_settings['avatar_url']['value'];
    $agent_email = $agent_settings['email']['value'];
    $agent_title = $agent_settings['job_title']['value'];
    $agent_mobile_phone = $agent_settings['mobile_phone']['value'];
    $agent_office_phone = $agent_settings['office_phone']['value'];
    $agent_description = $agent_settings['description']['value'];
    $agent_fb = $agent_settings['facebook']['value'];
    $agent_twitter = $agent_settings['twitter']['value'];
    $agent_google = $agent_settings['google']['value'];
    $agent_linkedin = $agent_settings['linkedin']['value'];
    $agent_youtube = $agent_settings['youtube']['value'];
    $agent_instagram = $agent_settings['instagram']['value'];
    $agent_form_source = $agent_settings['contact_form_source']['value'];
    $agent_form_id = $agent_settings['contact_form_7_id']['value'];

    //Get agent properties
    $agent_properties = $agents_obj->get_agent_properties(get_the_id(), $num_properties_per_page);
    $agent_properties_count = $agent_properties['count'];
?>	

	<?php if (!empty($agent_detail_items)) { 
		foreach($agent_detail_items as $value) { ?>

				<?php
                    if(isset($value['name'])) { $name = $value['name']; }
                    if(isset($value['label'])) { $label = $value['label']; }
                    if(isset($value['slug'])) { $slug = $value['slug']; }
                    if(isset($value['active']) && $value['active'] == 'true') { $active = 'true'; } else { $active = 'false'; }
                    if(isset($value['sidebar']) && $value['sidebar'] == 'true') { $sidebar = 'true'; } else { $sidebar = 'false'; }
                ?>

                <?php if($active == 'true' && ($sidebar == $template_location_sidebar)) { ?>
					
                	<?php if($slug == 'overview') { ?>
                    <!--******************************************************-->
                    <!-- OVERVIEW -->
                    <!--******************************************************-->
                	<div class="agent-single-item ps-single-item widget agent-<?php echo esc_attr($slug); ?>">

                        <div class="agent-img">
                            <?php if(!empty($agent_avatar_url)) {  ?>
                                <img src="<?php echo $agent_avatar_url; ?>" alt="<?php echo get_the_title(); ?>" />  
                            <?php } else { ?>
                                <img src="<?php echo PROPERTYSHIFT_DIR.'/images/agent-img-default.gif'; ?>" alt="" />
                            <?php } ?>
                        </div>

                        <?php if(isset($agent_properties_count) && $agent_properties_count > 0) { ?>
                            <div class="button alt button-icon agent-tag agent-assigned"><?php echo ns_core_get_icon($icon_set, 'home'); ?><?php echo esc_attr($agent_properties_count); ?> <?php if($agent_properties_count <= 1) { esc_html_e('Assigned Property', 'propertyshift'); } else { esc_html_e('Assigned Properties', 'propertyshift'); } ?></div>
                        <?php } ?>

                        <div class="agent-content">
                            <div class="agent-details">
        	                	<?php if(!empty($agent_title)) { ?><p><span><?php echo esc_attr($agent_title); ?></span><?php echo ns_core_get_icon($icon_set, 'tag'); ?><?php esc_html_e('Title', 'propertyshift'); ?>:</p><?php } ?>
        	                	<?php if(!empty($agent_email)) { ?><p><span><?php echo esc_attr($agent_email); ?></span><?php echo ns_core_get_icon($icon_set, 'envelope', 'envelope', 'mail'); ?><?php esc_html_e('Email', 'propertyshift'); ?>:</p><?php } ?>
        	                	<?php if(!empty($agent_mobile_phone)) { ?><p><span><?php echo esc_attr($agent_mobile_phone); ?></span><?php echo ns_core_get_icon($icon_set, 'phone', 'telephone'); ?><?php esc_html_e('Mobile', 'propertyshift'); ?>:</p><?php } ?>
        	                	<?php if(!empty($agent_office_phone)) { ?><p><span><?php echo esc_attr($agent_office_phone); ?></span><?php echo ns_core_get_icon($icon_set, 'building', 'apartment', 'briefcase'); ?><?php esc_html_e('Office', 'propertyshift'); ?>:</p><?php } ?>
                                <?php do_action('propertyshift_after_agent_details', $post->ID); ?>
                            </div>
                            <?php if(in_array('agent_detail_item_contact', $agent_detail_items)) { ?> 
                                <div class="button button-icon agent-message right"><?php echo ns_core_get_icon($icon_set, 'envelope'); ?><?php esc_html_e('Message Agent', 'propertyshift'); ?></div>
                            <?php } ?>
                            <?php if(!empty($agent_fb) || !empty($agent_twitter) || !empty($agent_google) || !empty($agent_linkedin) || !empty($agent_youtube) || !empty($agent_instagram)) { ?>
                            <div class="center">
                                <ul class="social-icons circle clean-list">
                                    <?php if(!empty($agent_fb)) { ?><li class="agent-footer-item"><a href="<?php echo esc_url($agent_fb); ?>" target="_blank"><i class="fab fa-facebook"></i></a></li><?php } ?>
                                    <?php if(!empty($agent_twitter)) { ?><li class="agent-footer-item"><a href="<?php echo esc_url($agent_twitter); ?>" target="_blank"><i class="fab fa-twitter"></i></a></li><?php } ?>
                                    <?php if(!empty($agent_google)) { ?><li class="agent-footer-item"><a href="<?php echo esc_url($agent_google); ?>" target="_blank"><i class="fab fa-google-plus"></i></a></li><?php } ?>
                                    <?php if(!empty($agent_linkedin)) { ?><li class="agent-footer-item"><a href="<?php echo esc_url($agent_linkedin); ?>" target="_blank"><i class="fab fa-linkedin"></i></a></li><?php } ?>
                                    <?php if(!empty($agent_youtube)) { ?><li class="agent-footer-item"><a href="<?php echo esc_url($agent_youtube); ?>" target="_blank"><i class="fab fa-youtube"></i></a></li><?php } ?>
                                    <?php if(!empty($agent_instagram)) { ?><li class="agent-footer-item"><a href="<?php echo esc_url($agent_instagram); ?>" target="_blank"><i class="fab fa-instagram"></i></a></li><?php } ?>
                                </ul>
                            </div>
                            <?php } ?>
                        </div>

                        <div class="clear"></div>
	                </div>
                	<?php } ?>

                	<?php if($slug == 'description' && !empty($agent_description)) { ?>
                    <!--******************************************************-->
                    <!-- DESCRIPTION -->
                    <!--******************************************************-->
                		<div class="agent-single-item ps-single-item content widget agent-<?php echo esc_attr($slug); ?>">
                			<?php if(!empty($label)) { ?>
                                <div class="module-header module-header-left">
                                    <h4><?php echo esc_attr($label); ?></h4>
                                    <div class="widget-divider"><div class="bar"></div></div>
                                </div>
                            <?php } ?>
                			<?php echo $agent_description; ?>
                		</div>
                	<?php } ?>

                	<?php if($slug == 'contact') { ?>
                    <!--******************************************************-->
                    <!-- CONTACT -->
                    <!--******************************************************-->
                        <a class="anchor" name="anchor-agent-contact"></a>
                		<div class="agent-single-item ps-single-item widget agent-<?php echo esc_attr($slug); ?>">
                			<?php if(!empty($label)) { ?>
                                <div class="module-header module-header-left">
                                    <h4><?php echo esc_attr($label); ?></h4>
                                    <div class="widget-divider"><div class="bar"></div></div>
                                </div>
                            <?php } ?>
                            <?php
                                if($agent_form_source == 'contact-form-7') {
                                    $agent_form_title = get_the_title( $agent_form_id );
                                    echo do_shortcode('[contact-form-7 id="'.esc_attr($agent_form_id).'" title="'.$agent_form_title.'"]');
                                } else { 
                                    propertyshift_template_loader('agent_contact_form.php');
                                } 
                            ?>
                		</div>
                	<?php } ?>

                	<?php if($slug == 'properties') { ?>
                    <!--******************************************************-->
                    <!-- AGENT PROPERTIES -->
                    <!--******************************************************-->
                        <a class="anchor" name="anchor-agent-properties"></a>
                		<div class="agent-single-item ps-single-item widget agent-<?php echo esc_attr($slug); ?>">
                		    <?php if(!empty($label)) { ?>
                                <div class="module-header module-header-left">
                                    <h4><?php echo esc_attr($label); ?></h4>
                                    <div class="widget-divider"><div class="bar"></div></div>
                                </div>
                            <?php } ?>
                	        <?php 
                                //Set template args
                                $template_args_properties = array();
                                $template_args_properties['custom_args'] = $agent_properties['args'];
                                $template_args_properties['custom_show_filter'] = false;
                                $template_args_properties['custom_layout'] = 'grid';
                                $template_args_properties['custom_pagination'] = true;
                                if($template_location_sidebar == 'true') { $template_args_properties['custom_cols'] = 1; }
                                $template_args_properties['no_post_message'] = esc_html__( 'Sorry, no properties were found.', 'propertyshift' );
                                
                                //Load template
                                propertyshift_template_loader('loop_properties.php', $template_args_properties);
                            ?>
                        </div>
                	<?php } ?>

                <?php } ?>

        <?php } //end foreach ?>
	<?php } ?>