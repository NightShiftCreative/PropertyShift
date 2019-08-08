<?php

	//Get global settings
    global $post;
    $postID = get_the_id();
    $icon_set = esc_attr(get_option('ns_core_icon_set', 'fa'));
    if(function_exists('ns_core_load_theme_options')) { $icon_set = ns_core_load_theme_options('ns_core_icon_set'); }
    
    $admin_obj = new NS_Real_Estate_Admin();
    $properties_page = $admin_obj->load_settings(false, 'ns_properties_page');
    $google_maps_pin = $admin_obj->load_settings(false, 'ns_real_estate_google_maps_pin');
    $property_detail_amenities_hide_empty = $admin_obj->load_settings(false, 'ns_property_detail_amenities_hide_empty');
    $property_detail_map_zoom = $admin_obj->load_settings(false, 'ns_property_detail_map_zoom');
    $property_detail_items = $admin_obj->load_settings(false, 'ns_property_detail_items', false);
    $property_detail_id = $admin_obj->load_settings(false, 'ns_property_detail_id');
    $property_detail_agent_contact_form = $admin_obj->load_settings(false, 'ns_property_detail_agent_contact_form');

    //Get template location
    if(isset($template_args)) { $template_location = $template_args['location']; } else { $template_location = ''; }
    if($template_location == 'sidebar') { 
        $template_location_sidebar = 'true'; 
    } else { 
        $template_location_sidebar = 'false';
    }

	//Get property details
    $property_obj = new NS_Real_Estate_Properties();
    $property_settings = $property_obj->load_property_settings($post->ID);
    $featured = $property_settings['featured']['value'];
    $address = $property_obj->get_full_address($post->ID);
    $price = $property_settings['price']['value'];
    $price_postfix = $property_settings['price_postfix']['value'];
    $area = $property_settings['area']['value'];
    if(!empty($area)) { $area = $property_obj->get_formatted_area($area); }
    $area_postfix = $property_settings['area_postfix']['value'];
    $bedrooms = $property_settings['beds']['value'];
    $bathrooms = $property_settings['baths']['value'];
    $garages = $property_settings['garages']['value'];
    $description = $property_settings['description']['value'];
    $floor_plans = $property_settings['floor_plans']['value'];
    $latitude = $property_settings['latitude']['value'];
    $longitude = $property_settings['longitude']['value'];
    $video_url = $property_settings['video_url']['value'];
    $video_img = $property_settings['video_cover']['value'];
    $additional_images = $property_settings['gallery']['value'];
    $agent_display = $property_settings['owner_display']['value'];
    $agent_select = $property_settings['owner_display']['children']['agent']['value'];
    $agent_custom_name = $property_settings['owner_display']['children']['owner_custom_name']['value'];
    $agent_custom_email = $property_settings['owner_display']['children']['owner_custom_email']['value'];
    $agent_custom_phone = $property_settings['owner_display']['children']['owner_custom_phone']['value'];
    $agent_custom_url = $property_settings['owner_display']['children']['owner_custom_url']['value'];

    $property_type = $property_obj->get_tax($postID, 'property_type');
    $property_status = $property_obj->get_tax($postID, 'property_status');
    $property_location = $property_obj->get_tax($postID, 'property_location');
    $property_amenities = $property_obj->get_tax_amenities($postID, $property_detail_amenities_hide_empty, null);
?>	

	<div class="property-single">

        <?php do_action('ns_real_estate_before_property_detail', $values); ?>
	
		<?php if(!empty($property_detail_items)) {
            foreach($property_detail_items as $value) { ?>
                <?php
                    if(isset($value['name'])) { $name = $value['name']; }
                    if(isset($value['label'])) { $label = $value['label']; }
                    if(isset($value['slug'])) { $slug = $value['slug']; }
                    if(isset($value['active']) && $value['active'] == 'true') { $active = 'true'; } else { $active = 'false'; }
                    if(isset($value['sidebar']) && $value['sidebar'] == 'true') { $sidebar = 'true'; } else { $sidebar = 'false'; }
                    if(isset($value['add_on'])) { $add_on = $value['add_on']; } else { $add_on = ''; }
                ?>

                <?php if($active == 'true' && ($sidebar == $template_location_sidebar)) { ?>
                	
                    <?php if($slug == 'overview') { ?>
                        <!--******************************************************-->
                        <!-- PROPERTY OVERVIEW -->
                        <!--******************************************************-->
                        <div class="property-single-item ns-single-item widget property-<?php echo esc_attr($slug); ?>">
                            
                            <div class="property-title">
                                <?php if(!empty($price)) { ?>
                                    <div class="property-price-single right">
                                        <?php echo $property_obj->get_formatted_price($price); ?>
                                        <?php if(!empty($price_postfix)) { ?><span class="price-postfix"><?php echo esc_attr($price_postfix); ?></span><?php } ?>
                                    </div>
                                <?php } ?>
                                            
                                <?php if(!empty($address)) { echo '<p class="property-address">'.ns_core_get_icon($icon_set, 'map-marker', 'map-marker', 'location').$address.'</p>'; } ?>
                                <div class="clear"></div>
                            </div>

                            <div class="property-title-below">
                                <div class="left">
                                    <?php if($featured == 'true') { ?><a href="<?php if(!empty($properties_page)) { echo esc_url($properties_page).'/?featured=true'; } ?>" class="property-tag button alt featured"><?php esc_html_e('Featured', 'ns-real-estate'); ?></a><?php } ?>
                                    <?php if(!empty($property_status)) { ?>
                                        <div class="property-tag button status"><?php echo wp_kses_post($property_status); ?></div>
                                    <?php } ?>
                                    <?php if($property_detail_id == 'true') { ?><div class="property-id"><?php esc_html_e('Property ID', 'ns-real-estate'); ?>: <?php echo get_the_id(); ?></div><?php } ?>
                                    <?php if(!empty($property_type)) { ?><div class="property-type"><?php esc_html_e('Property Type:', 'ns-real-estate'); ?> <?php echo wp_kses_post($property_type); ?></div><?php } ?>
                                </div>
                                <div class="right property-actions">
                                    <?php do_action('ns_real_estate_property_actions'); ?>
                                </div>
                                <div class="clear"></div>
                            </div>
                            <div class="clear"></div>

                            <?php if(!empty($bedrooms) || !empty($bathrooms) || !empty($area)) { ?>
                            <table class="property-details-single">
                                <tr>
                                    <?php if(!empty($bedrooms)) { ?><td><?php echo ns_core_get_icon($icon_set, 'bed', 'bed', 'n/a'); ?> <span><?php echo esc_attr($bedrooms); ?></span> <?php esc_html_e('Beds', 'ns-real-estate'); ?></td><?php } ?>
                                    <?php if(!empty($bathrooms)) { ?><td><?php echo ns_core_get_icon($icon_set, 'tint', 'bathtub', 'n/a'); ?> <span><?php echo esc_attr($bathrooms); ?></span> <?php esc_html_e('Baths', 'ns-real-estate'); ?></td><?php } ?>
                                    <?php if(!empty($area)) { ?><td><?php echo ns_core_get_icon($icon_set, 'expand'); ?> <span><?php echo esc_attr($area); ?></span> <?php echo esc_attr($area_postfix); ?></td><?php } ?>
                                </tr>
                            </table>
                            <?php } ?>

                        </div>
                    <?php } ?>

                    <?php if($slug == 'description' && (!empty($description)) ) { ?>
                    <!--******************************************************-->
                    <!-- DESCRIPTION -->
                    <!--******************************************************-->
                        <div class="property-single-item ns-single-item content widget property-<?php echo esc_attr($slug); ?>">
                            <?php if(!empty($label)) { ?>
                                <div class="module-header module-header-left">
                                    <h4><?php echo esc_attr($label); ?></h4>
                                    <div class="widget-divider"><div class="bar"></div></div>
                                </div>
                            <?php } ?>
                            <?php echo $description; ?>
                        </div>
                    <?php } ?>

                	<?php if($slug == 'gallery' && !empty($additional_images[0])) { ?>
                    <!--******************************************************-->
                    <!-- PROPERTY GALLERY -->
                    <!--******************************************************-->
						<div class="property-single-item ns-single-item widget property-<?php echo esc_attr($slug); ?>">
                            <?php if(!empty($label)) { ?>
                                <div class="module-header module-header-left">
                                    <h4><?php echo esc_attr($label); ?></h4>
                                    <div class="widget-divider"><div class="bar"></div></div>
                                </div>
                            <?php } ?>

						    <div class="gallery-images">
                                <?php
                                    foreach ($additional_images as $additional_image) {
                                        $image_id = ns_basics_get_image_id($additional_image);
                                        $image_thumb = wp_get_attachment_image_src($image_id, 'property-thumbnail');
                                        if(!empty($image_thumb) && !empty($image_thumb[0])) {
                                            echo '<a href="'.$additional_image.'" target="_blank"><img src="'.$image_thumb[0].'" alt="" /></a>';
                                        } else {
                                            echo '<a href="'.$additional_image.'" target="_blank"><img src="'.$additional_image.'" alt="" /></a>';
                                        }
                                    } ?>
                                <div class="clear"></div>
                            </div>

						</div>
                	<?php } ?>

                    <?php if($slug == 'property_details') { ?>
                        <!--******************************************************-->
                        <!-- PROPERTY DETAILS -->
                        <!--******************************************************-->
                        <div class="property-single-item ns-single-item widget property-<?php echo esc_attr($slug); ?>">
                            <?php if(!empty($label)) { ?>
                                <div class="module-header module-header-left">
                                    <h4><?php echo esc_attr($label); ?></h4>
                                    <div class="widget-divider"><div class="bar"></div></div>
                                </div>
                            <?php } ?>

                            <div class="property-details-full">
                                <?php if($property_detail_id == 'true') { ?><div class="property-detail-item"><?php esc_html_e('Property ID', 'ns-real-estate'); ?>:<span><?php echo get_the_id(); ?></span></div><?php } ?>
                                <?php if(!empty($bedrooms)) { ?><div class="property-detail-item"><?php esc_html_e('Beds', 'ns-real-estate'); ?>:<span><?php echo esc_attr($bedrooms); ?></span></div><?php } ?>
                                <?php if(!empty($bathrooms)) { ?><div class="property-detail-item"><?php esc_html_e('Baths', 'ns-real-estate'); ?>:<span><?php echo esc_attr($bathrooms); ?></span></div><?php } ?>
                                <?php if(!empty($area)) { ?><div class="property-detail-item"><?php esc_html_e('Area', 'ns-real-estate'); ?>:<span><?php echo esc_attr($area); ?> <?php echo esc_attr($area_postfix); ?></span></div><?php } ?>
                                <?php if(!empty($garages)) { ?><div class="property-detail-item"><?php esc_html_e('Garages', 'ns-real-estate'); ?>:<span><?php echo esc_attr($garages); ?></span></div><?php } ?>
                                <?php if(!empty($property_status)) { ?><div class="property-detail-item"><?php esc_html_e('Status', 'ns-real-estate'); ?>:<span><?php echo wp_kses_post($property_status); ?></span></div><?php } ?>
                                <?php if(!empty($property_type)) { ?><div class="property-detail-item"><?php esc_html_e('Type', 'ns-real-estate'); ?>:<span><?php echo wp_kses_post($property_type); ?></span></div><?php } ?>
                                <div class="property-detail-item publish-date"><?php esc_html_e('Posted On', 'ns-real-estate'); ?>:<span><?php echo get_the_date(); ?></span></div>
                                <?php do_action('ns_real_estate_property_details_widget', $postID); ?>
                                <div class="clear"></div>
                            </div>

                        </div>
                    <?php } ?>

                	<?php if($slug == 'amenities' && !empty($property_amenities)) { ?>
                    <!--******************************************************-->
                    <!-- AMENITIES -->
                    <!--******************************************************-->
						<div class="property-single-item ns-single-item widget property-<?php echo esc_attr($slug); ?>">
					        <?php if(!empty($label)) { ?>
                                <div class="module-header module-header-left">
                                    <h4><?php echo esc_attr($label); ?></h4>
                                    <div class="widget-divider"><div class="bar"></div></div>
                                </div>
                            <?php } ?>
                            <?php echo $property_amenities; ?>
						</div>
                	<?php } ?>

                    <?php if($slug == 'floor_plans' && !empty($floor_plans[0])) { ?>
                    <!--******************************************************-->
                    <!-- FLOOR PLANS -->
                    <!--******************************************************-->
                        <div class="property-single-item ns-single-item widget property-<?php echo esc_attr($slug); ?>">
                            <?php if(!empty($label)) { ?>
                                <div class="module-header module-header-left">
                                    <h4><?php echo esc_attr($label); ?></h4>
                                    <div class="widget-divider"><div class="bar"></div></div>
                                </div>
                            <?php } ?>

                            <div class="accordion" class="accordion-floor-plans">
                                <?php 
                                    if(!empty($floor_plans)) {   
                                        foreach ($floor_plans as $floor_plan) { ?>
                                            <h3 class="accordion-tab"><?php echo esc_html_e($floor_plan['title'], 'ns-real-estate'); ?></h3>
                                            <div class="floor-plan-item"> 
                                                <table>
                                                    <tr>
                                                        <td><strong><?php esc_html_e('Size', 'ns-real-estate'); ?></strong></td>
                                                        <td><strong><?php esc_html_e('Rooms', 'ns-real-estate'); ?></strong></td>
                                                        <td><strong><?php esc_html_e('Bathrooms', 'ns-real-estate'); ?></strong></td>
                                                    </tr>
                                                    <tr>
                                                        <td><?php if(!empty($floor_plan['size'])) { echo esc_attr($floor_plan['size']); } else { echo '--'; } ?></td>
                                                        <td><?php if(!empty($floor_plan['rooms'])) { echo esc_attr($floor_plan['rooms']); } else { echo '--'; } ?></td>
                                                        <td><?php if(!empty($floor_plan['baths'])) { echo esc_attr($floor_plan['baths']); } else { echo '--'; } ?></td>
                                                    </tr>
                                                </table>
                                                <?php if(!empty($floor_plan['description'])) { echo '<p>'.esc_html__($floor_plan['description'], 'ns-real-estate').'</p>'; } ?>
                                                <?php if(!empty($floor_plan['img'])) { echo '<img class="floor-plan-img" src="'.$floor_plan['img'].'" alt="" />'; } ?>
                                            </div> 
                                        <?php }
                                    } 
                                 ?>
                            </div>

                        </div>
                    <?php } ?>
						
					<?php if($slug == 'location' && !empty($latitude) && !empty($longitude)) { ?>
                    <!--******************************************************-->
                    <!-- LOCATION -->
                    <!--******************************************************-->
						<div class="property-single-item ns-single-item widget property-<?php echo esc_attr($slug); ?>">
							<?php if(!empty($label)) { ?>
                                <div class="module-header module-header-left">
                                    <h4><?php echo esc_attr($label); ?></h4>
                                    <div class="widget-divider"><div class="bar"></div></div>
                                </div>
                            <?php } ?>
							<div id="map-canvas-one-pin"></div>
							
							<script>
								"use strict";

                                //intialize the map
                                function initialize() {
									var propertyLatlng = new google.maps.LatLng(<?php echo esc_attr($latitude); ?>, <?php echo esc_attr($longitude); ?>);
										var mapOptions = {
										zoom: <?php echo esc_attr($property_detail_map_zoom); ?>,
										center: propertyLatlng
									};
									
									var map = new google.maps.Map(document.getElementById('map-canvas-one-pin'), mapOptions);

									//add a marker1
									var marker = new google.maps.Marker({
										position: propertyLatlng,
										map: map,
										icon: '<?php echo $google_maps_pin; ?>'
									});
										
									//show info box for marker1
									var contentString = '<div class="info-box"><?php the_title(); ?></div>';

									var infowindow = new google.maps.InfoWindow({ content: contentString });

									google.maps.event.addListener(marker, 'click', function() {
										infowindow.open(map,marker);
									});
								}
									
								google.maps.event.addDomListener(window, 'load', initialize);
							</script>
						</div>
					<?php } ?>
					
					<?php if($slug == 'walk_score' && (!empty($latitude) && !empty($longitude))) { ?>
                    <!--******************************************************-->
                    <!-- WALK SCORE -->
                    <!--******************************************************-->
						<div class="property-single-item ns-single-item widget property-<?php echo esc_attr($slug); ?>">
						
							<?php 
							$json = getWalkScore($latitude,$longitude,$address);
							$walkScoreData = json_decode($json, true);
							?>

                            <?php if(!empty($label)) { ?>
                                <div class="module-header module-header-left">
                                    <h4>
                                        <span class="right">
                                            <img src="<?php echo esc_url($walkScoreData['logo_url']); ?>" alt="" />
                                            <a target="_blank" href="<?php echo esc_url($walkScoreData['help_link']); ?>"><img src="<?php echo esc_url($walkScoreData['more_info_icon']); ?>" alt="" /></a>
                                        </span>
                                        <?php echo esc_attr($label); ?>
                                    </h4>
                                    <div class="widget-divider"><div class="bar"></div></div>
                                </div>
                            <?php } ?>
							
							<div class="walk-score">
								<h2><?php echo esc_attr($walkScoreData['walkscore']); ?><span>/100</span></h2>
								<p><?php echo esc_attr($walkScoreData['description']); ?></p>
                                <a href="<?php echo esc_url($walkScoreData['ws_link']); ?>" target="_blank" class="button"><?php esc_html_e('View More Details', 'ns-real-estate'); ?></a>
							</div>
						</div>
					<?php } ?>

                	<?php if($slug == 'video' && !empty($video_url)) { ?>
                    <!--******************************************************-->
                    <!-- VIDEO -->
                    <!--******************************************************-->
						<div class="property-single-item ns-single-item widget property-<?php echo esc_attr($slug); ?>">
					        <?php if(!empty($label)) { ?>
                                <div class="module-header module-header-left">
                                    <h4><?php echo esc_attr($label); ?></h4>
                                    <div class="widget-divider"><div class="bar"></div></div>
                                </div>
                            <?php } ?>
							<a href="<?php echo esc_url($video_url); ?>" data-fancybox class="video-cover">
								<div class="video-cover-content"><i class="fa fa-play icon"></i></div>
								<?php if(!empty($video_img)) { ?>
									<img src="<?php echo esc_url($video_img); ?>" alt="" />
								<?php } else { ?>
                                    <img src="<?php echo plugins_url( '/ns-real-estate/images/property-img-default.gif' ); ?>" alt="" />
								<?php } ?>
							</a>
						</div>
                	<?php } ?>

                    <?php if($slug == 'agent_info' && $agent_display != 'none') { ?>
                    <!--******************************************************-->
                    <!-- OWNER INFO -->
                    <!--******************************************************-->
						<div class="property-single-item ns-single-item widget property-<?php echo esc_attr($slug); ?>">
                            <?php if(!empty($label)) { ?>
                                <div class="module-header module-header-left">
                                    <h4><?php echo esc_attr($label); ?></h4>
                                    <div class="widget-divider"><div class="bar"></div></div>
                                </div>
                            <?php } ?>
                            <?php if($agent_display == 'author') { ?>

                                <div class="author-info">
                                    <?php 
                                        $avatar_id = get_user_meta( get_the_author_meta( 'ID' ), 'avatar', true ); 
                                        if(!empty($avatar_id)) {
                                            echo wp_get_attachment_image($avatar_id, array('96', '96'));
                                        } else {
                                            echo '<img src="'.plugins_url( '/ns-real-estate/images/agent-img-default.gif' ).'" alt="Agent Image" />';
                                        }
                                    ?>
                                    <p class="author-display-name"><?php echo ns_core_get_icon($icon_set, 'user'); ?> <?php the_author_meta('display_name'); ?></p>
                                    <p class="author-email"><?php echo ns_core_get_icon($icon_set, 'envelope'); ?> <?php the_author_meta('user_email'); ?></p>
                                    <?php if (get_the_author_meta('user_url')  != '') { ?><a class="author-url" href="<?php the_author_meta('user_url'); ?>" target="_blank"><?php echo ns_core_get_icon($icon_set, 'globe', 'link'); ?> <?php the_author_meta('user_url'); ?> </a><?php } ?>
									<div class="clear"></div>
								</div>

                            <?php } else if($agent_display == 'agent') { ?>
                                
                                <?php

                                    $agent_listing_args = array(
                                        'post_type' => 'ns-agent',
                                        'posts_per_page' => 1,
                                        'p' => $agent_select
                                    );

                                    $agent_listing_query = new WP_Query( $agent_listing_args );
                                ?>

                                <?php if ( $agent_listing_query->have_posts() ) : while ( $agent_listing_query->have_posts() ) : $agent_listing_query->the_post(); ?>

                                    <?php
                                    $agent_obj = new NS_Real_Estate_Agents();
                                    $agent_settings = $agent_obj->load_agent_settings($post->ID);
                                    $agent_title = $agent_settings['job_title']['value'];
                                    $agent_email = $agent_settings['email']['value'];
                                    $agent_mobile_phone = $agent_settings['mobile_phone']['value'];
                                    $agent_office_phone = $agent_settings['office_phone']['value'];
                                    $agent_fb = $agent_settings['facebook']['value'];
                                    $agent_twitter = $agent_settings['twitter']['value'];
                                    $agent_google = $agent_settings['google']['value'];
                                    $agent_linkedin = $agent_settings['linkedin']['value'];
                                    $agent_youtube = $agent_settings['youtube']['value'];
                                    $agent_instagram = $agent_settings['instagram']['value'];
                                    $agent_form_source = $agent_settings['contact_form_source']['value'];
                                    $agent_form_id = $agent_settings['contact_form_source']['children']['contact_form_7_id']['value'];

                                    //Get agent property count
                                    $agent_properties = $agent_obj->get_agent_properties(get_the_id());
                                    $agent_properties_count = $agent_properties['count'];
                                    ?>

                                    <div class="ns-agent property-agent">
                                        
                                        <a href="<?php the_permalink(); ?>" class="agent-img">
                                            <?php if(isset($agent_properties_count) && $agent_properties_count > 0) { ?>
                                                <div class="button alt agent-tag"><?php echo esc_attr($agent_properties_count); ?> <?php if($agent_properties_count <= 1) { esc_html_e('Property', 'ns-real-estate'); } else { esc_html_e('Properties', 'ns-real-estate'); } ?></div>
                                            <?php } ?>
                                            <?php if ( has_post_thumbnail() ) {  ?>
                                                <div class="img-fade"></div>
                                                <?php the_post_thumbnail('full'); ?>
                                            <?php } else { ?>
                                                <img src="<?php echo plugins_url( '/ns-real-estate/images/agent-img-default.gif' ); ?>" alt="" />
                                            <?php } ?>
                                        </a>

                                        <div class="agent-content">
                                            <div class="agent-details">
                                                <h4><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h4>
                                                <p><a href="<?php the_permalink(); ?>" class="button button-icon"><i class="fa fa-angle-right icon"></i><?php esc_html_e('View Agent Profile', 'ns-real-estate'); ?></a></p>
                                                
                                                <?php if(!empty($agent_title)) { ?><p><span><?php echo esc_attr($agent_title); ?></span><?php echo ns_core_get_icon($icon_set, 'tag'); ?><?php esc_html_e('Title', 'ns-real-estate'); ?>:</p><?php } ?>
                                                <?php if(!empty($agent_email)) { ?><p><span><?php echo esc_attr($agent_email); ?></span><?php echo ns_core_get_icon($icon_set, 'envelope', 'envelope', 'mail'); ?><?php esc_html_e('Email', 'ns-real-estate'); ?>:</p><?php } ?>
                                                <?php if(!empty($agent_mobile_phone)) { ?><p><span><?php echo esc_attr($agent_mobile_phone); ?></span><?php echo ns_core_get_icon($icon_set, 'phone', 'telephone'); ?><?php esc_html_e('Mobile', 'ns-real-estate'); ?>:</p><?php } ?>
                                                <?php if(!empty($agent_office_phone)) { ?><p><span><?php echo esc_attr($agent_office_phone); ?></span><?php echo ns_core_get_icon($icon_set, 'building', 'apartment', 'briefcase'); ?><?php esc_html_e('Office', 'ns-real-estate'); ?>:</p><?php } ?>
                                                <?php do_action('ns_real_estate_after_agent_details', $post->ID); ?>

                                                <?php if(!empty($agent_fb) || !empty($agent_twitter) || !empty($agent_google) || !empty($agent_linkedin) || !empty($agent_youtube) || !empty($agent_instagram)) { ?>
                                                <ul class="social-icons circle clean-list">
                                                    <?php if(!empty($agent_fb)) { ?><li><a href="<?php echo esc_url($agent_fb); ?>" target="_blank"><i class="fab fa-facebook"></i></a></li><?php } ?>
                                                    <?php if(!empty($agent_twitter)) { ?><li><a href="<?php echo esc_url($agent_twitter); ?>" target="_blank"><i class="fab fa-twitter"></i></a></li><?php } ?>
                                                    <?php if(!empty($agent_google)) { ?><li><a href="<?php echo esc_url($agent_google); ?>" target="_blank"><i class="fab fa-google-plus"></i></a></li><?php } ?>
                                                    <?php if(!empty($agent_linkedin)) { ?><li><a href="<?php echo esc_url($agent_linkedin); ?>" target="_blank"><i class="fab fa-linkedin"></i></a></li><?php } ?>
                                                    <?php if(!empty($agent_youtube)) { ?><li><a href="<?php echo esc_url($agent_youtube); ?>" target="_blank"><i class="fab fa-youtube"></i></a></li><?php } ?>
                                                    <?php if(!empty($agent_instagram)) { ?><li><a href="<?php echo esc_url($agent_instagram); ?>" target="_blank"><i class="fab fa-instagram"></i></a></li><?php } ?>
                                                </ul>
                                                <?php } ?>
                                            </div>
                                        </div>
                                        <div class="clear"></div>
                                        <?php
                                        if($property_detail_agent_contact_form == 'true') {
                                            if($agent_form_source == 'contact-form-7') {
                                                $agent_form_title = get_the_title( $agent_form_id );
                                                echo do_shortcode('[contact-form-7 id="<?php echo esc_attr($agent_form_id); ?>" title="'.$agent_form_title.'"]');
                                            } else {
                                                ns_real_estate_template_loader('agent_contact_form.php');
                                            } 
                                        }
                                        ?>
                                    </div><!-- end agent -->

                                <?php endwhile; ?>
                                    <?php wp_reset_postdata(); ?>
                                <?php else: ?>
                                    <div class="col-lg-12"><p><?php esc_html_e('Sorry, no agents have been posted yet.', 'ns-real-estate'); ?></p></div>
                                <?php endif; ?>

                            <?php } else if($agent_display == 'custom') { ?>
                            	<?php if(!empty($agent_custom_name)) { echo '<p>'.ns_core_get_icon($icon_set, 'user').' '.$agent_custom_name.'</p>'; } ?>
                            	<?php if(!empty($agent_custom_email)) { echo '<p>'.ns_core_get_icon($icon_set, 'envelope', 'envelope', 'mail').' '.$agent_custom_email.'</p>'; } ?>
                            	<?php if(!empty($agent_custom_phone)) { echo '<p>'.ns_core_get_icon($icon_set, 'phone', 'telephone').' '.$agent_custom_phone.'</p>'; } ?>
                            	<?php if(!empty($agent_custom_url)) { echo '<p><a href="'.$agent_custom_url.'" target="_blank">'.ns_core_get_icon($icon_set, 'globe', 'link').' '.$agent_custom_url.'</a></p>'; } ?>
                            <?php } ?>
						</div>
                    <?php } ?>
					
					<?php if($slug == 'related') { ?>
                    <!--******************************************************-->
                    <!-- RELATED PROPERTIES -->
                    <!--******************************************************-->
						<div class="property-single-item ns-single-item widget property-<?php echo esc_attr($slug); ?>">
                            <?php if(!empty($label)) { ?>
                                <div class="module-header module-header-left">
                                    <h4><?php echo esc_attr($label); ?></h4>
                                    <div class="widget-divider"><div class="bar"></div></div>
                                </div>
                            <?php } ?>
                            <?php 
                                $args_related_properties = array(
                                    'post_type' => 'ns-property',
                                    'showposts' => 2,
                                    'tax_query' => array(
                                        'relation' => 'OR',
                                        array(
                                            'taxonomy' => 'property_status',
                                            'field' => 'slug',
                                            'terms' => $property_status
                                        ),
                                        array(
                                            'taxonomy' => 'property_type',
                                            'field' => 'slug',
                                            'terms' => $property_type
                                        ),
                                        array(
                                            'taxonomy' => 'property_location',
                                            'field' => 'slug',
                                            'terms' => $property_location
                                        ),
                                    ),
                                    'orderby' => 'rand',
                                    'post__not_in' => array( $postID )
                                );

                                //Set template args
                                $template_args_related_properties = array();
                                $template_args_related_properties['custom_args'] = $args_related_properties;
                                $template_args_related_properties['custom_show_filter'] = false;
                                $template_args_related_properties['custom_layout'] = 'grid';
                                $template_args_related_properties['custom_pagination'] = false;
                                if($template_location_sidebar == 'true') { $template_args_related_properties['custom_cols'] = 1; }
                                $template_args_related_properties['no_post_message'] = esc_html__( 'Sorry, no related properties were found.', 'ns-real-estate' );
                                
                                //Load template
                                ns_real_estate_template_loader('loop_properties.php', $template_args_related_properties);
                            ?>
						</div>
					<?php } ?>

                    <?php if(!empty($add_on)) { ?>
                        <!--******************************************************-->
                        <!-- ADD-ONS -->
                        <!--******************************************************-->
                        <?php do_action('ns_real_estate_property_detail_items', $values, $value); ?>
                    <?php } ?>

                <?php } ?>
            <?php } //end foreach ?>
        <?php } //end if ?>

        <?php do_action('ns_real_estate_after_property_detail', $values); ?>

	</div><!-- end property single -->