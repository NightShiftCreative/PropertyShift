<?php

	//Get global settings
    $postID = get_the_id();
    $icon_set = esc_attr(get_option('ns_core_icon_set', 'fa'));
    $properties_page = esc_attr(get_option('ns_properties_page'));
    $property_detail_template = esc_attr(get_option('ns_property_detail_template', 'classic'));
    $google_maps_pin = esc_attr(get_option('ns_real_estate_google_maps_pin'));
    if(empty($google_maps_pin)) { $google_maps_pin = plugins_url( '/rype-real-estate/images/pin.png'); }
    $property_detail_amenities_hide_empty = esc_attr(get_option('ns_property_detail_amenities_hide_empty', 'false'));
    $property_detail_map_zoom = esc_attr(get_option('ns_property_detail_map_zoom', 13));
    $property_detail_items_default = ns_real_estate_load_default_property_detail_items();
    $property_detail_items = get_option('ns_property_detail_items', $property_detail_items_default);
    $property_detail_id = esc_attr(get_option('ns_property_detail_id')); 
    $property_detail_agent_contact_form = esc_attr(get_option('ns_property_detail_agent_contact_form'));

    //Get template location
    if(isset($template_args)) { $template_location = $template_args['location']; } else { $template_location = ''; }
    if($template_location == 'sidebar') { 
        $template_location_sidebar = 'true'; 
    } else { 
        $template_location_sidebar = 'false';
    }

	//Get property details
    $values = get_post_custom( $postID );
    $featured = isset( $values['rypecore_property_featured'] ) ? esc_attr( $values['rypecore_property_featured'][0] ) : 'false';
	if(function_exists('ns_real_estate_get_property_address')) { $address = ns_real_estate_get_property_address($postID); } else { $address = ''; }
	$price = isset( $values['rypecore_property_price'] ) ? esc_attr( $values['rypecore_property_price'][0] ) : '';
	$price_postfix = isset( $values['rypecore_property_price_postfix'] ) ? esc_attr( $values['rypecore_property_price_postfix'][0] ) : '';
    $area = isset( $values['rypecore_property_area'] ) ? esc_attr( $values['rypecore_property_area'][0] ) : '';
    if(!empty($area)) { $area = ns_real_estate_format_area($area); }
    $area_postfix = isset( $values['rypecore_property_area_postfix'] ) ? esc_attr( $values['rypecore_property_area_postfix'][0] ) : '';
    $bedrooms = isset( $values['rypecore_property_bedrooms'] ) ? esc_attr( $values['rypecore_property_bedrooms'][0] ) : '';
    $bathrooms = isset( $values['rypecore_property_bathrooms'] ) ? esc_attr( $values['rypecore_property_bathrooms'][0] ) : '';
    $garages = isset( $values['rypecore_property_garages'] ) ? esc_attr( $values['rypecore_property_garages'][0] ) : '';
    $floor_plans = isset($values['rypecore_floor_plans']) ? $values['rypecore_floor_plans'] : '';
	$latitude = isset( $values['rypecore_property_latitude'] ) ? esc_attr( $values['rypecore_property_latitude'][0] ) : '';
	$longitude = isset( $values['rypecore_property_longitude'] ) ? esc_attr( $values['rypecore_property_longitude'][0] ) : '';
	$video_url = isset( $values['rypecore_property_video_url'] ) ? esc_attr( $values['rypecore_property_video_url'][0] ) : '';
	$video_img = isset( $values['rypecore_property_video_img'] ) ? esc_attr( $values['rypecore_property_video_img'][0] ) : '';
    $additional_images = $values['rypecore_additional_img'];
    $agent_display = isset( $values['rypecore_agent_display'] ) ? esc_attr( $values['rypecore_agent_display'][0] ) : 'none';
    $agent_select = isset( $values['rypecore_agent_select'] ) ? esc_attr( $values['rypecore_agent_select'][0] ) : '';
    $agent_custom_name = isset( $values['rypecore_agent_custom_name'] ) ? esc_attr( $values['rypecore_agent_custom_name'][0] ) : '';
	$agent_custom_email = isset( $values['rypecore_agent_custom_email'] ) ? esc_attr( $values['rypecore_agent_custom_email'][0] ) : '';
	$agent_custom_phone = isset( $values['rypecore_agent_custom_phone'] ) ? esc_attr( $values['rypecore_agent_custom_phone'][0] ) : '';
	$agent_custom_url = isset( $values['rypecore_agent_custom_url'] ) ? esc_attr( $values['rypecore_agent_custom_url'][0] ) : '';

	//Get property taxonomies
    $property_type = ns_real_estate_get_property_type($postID);
    $property_status = ns_real_estate_get_property_status($postID);
    $property_location = ns_real_estate_get_property_location($postID, 'parent');
    $property_location_children = ns_real_estate_get_property_location($postID, 'children');
    $property_amenities = ns_real_estate_get_property_amenities($postID, $property_detail_amenities_hide_empty);

    //Get custom post meta count
    $post_meta = get_post_meta($postID);
    $custom_fields_count = 0;
    foreach($post_meta as $key => $value) {
        if(substr($key, 0, strlen('rypecore_custom_field_')) === 'rypecore_custom_field_' && !empty($value[0])) {
            $custom_fields_count++;
        }
    }
?>	

	<div class="property-single">
	
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
                        <div class="property-single-item gallery property-<?php echo esc_attr($slug); ?>">
                            
                            <div class="property-title">
                                <h4><?php the_title(); ?></h4>
                                            
                                <?php if(!empty($price)) { ?>
                                    <div class="property-price-single right">
                                        <?php echo ns_real_estate_format_price($price); ?>
                                        <?php if(!empty($price_postfix)) { ?><span class="price-postfix"><?php echo esc_attr($price_postfix); ?></span><?php } ?>
                                    </div>
                                <?php } ?>
                                            
                                <?php if(!empty($address)) { echo '<p class="property-address">'.$address.'</p>'; } ?>
                                <div class="clear"></div>
                            </div>

                            <div class="property-single-tags">
                                <?php if($featured == 'true') { ?><a href="<?php if(!empty($properties_page)) { echo esc_url($properties_page).'/?featured=true'; } ?>" class="property-tag button alt featured"><?php esc_html_e('Featured', 'rype-real-estate'); ?></a><?php } ?>
                                <?php if(!empty($property_status)) { ?>
                                    <div class="property-tag button status"><?php echo wp_kses_post($property_status); ?></div>
                                <?php } ?>
                                <?php if($property_detail_id == 'true') { ?><div class="property-id"><?php esc_html_e('Property ID', 'rype-real-estate'); ?>: <?php echo get_the_id(); ?></div><?php } ?>
                                <?php if(!empty($property_type)) { ?><div class="property-type"><?php esc_html_e('Property Type:', 'rype-real-estate'); ?> <?php echo wp_kses_post($property_type); ?></div><?php } ?>
                            </div>
                            <div class="clear"></div>

                            <?php if(!empty($bedrooms) || !empty($bathrooms) || !empty($area) || !empty($garages)) { ?>
                            <table class="property-details-single">
                                <tr>
                                    <?php if(!empty($bedrooms)) { ?><td><?php echo ns_core_get_icon($icon_set, 'bed', 'bed', 'n/a'); ?> <span><?php echo esc_attr($bedrooms); ?></span> <?php esc_html_e('Beds', 'rype-real-estate'); ?></td><?php } ?>
                                    <?php if(!empty($bathrooms)) { ?><td><?php echo ns_core_get_icon($icon_set, 'tint', 'bathtub', 'n/a'); ?> <span><?php echo esc_attr($bathrooms); ?></span> <?php esc_html_e('Baths', 'rype-real-estate'); ?></td><?php } ?>
                                    <?php if(!empty($area)) { ?><td><?php echo ns_core_get_icon($icon_set, 'expand'); ?> <span><?php echo esc_attr($area); ?></span> <?php echo esc_attr($area_postfix); ?></td><?php } ?>
                                    <?php if(!empty($garages)) { ?><td><?php echo ns_core_get_icon($icon_set, 'car', 'car2', 'n/a'); ?> <span><?php echo esc_attr($garages); ?></span> <?php esc_html_e('Garages', 'rype-real-estate'); ?></td><?php } ?>
                                </tr>
                            </table>
                            <?php } ?>
                        </div>
                    <?php } ?>


                	<?php if($slug == 'gallery') { ?>
                    <!--******************************************************-->
                    <!-- PROPERTY GALLERY -->
                    <!--******************************************************-->
						<div class="property-single-item gallery widget property-<?php echo esc_attr($slug); ?>">
                            <?php if(!empty($label)) { ?>
                                <div class="module-header module-header-left">
                                    <h4><?php echo esc_attr($label); ?></h4>
                                    <div class="widget-divider"><div class="bar"></div></div>
                                </div>
                            <?php } ?>

                            <?php if ( has_post_thumbnail() ) { 
                                the_post_thumbnail('full'); 
                            } else { 
                                echo '<img src="'.plugins_url( '/rype-real-estate/images/property-img-default.gif' ).'" alt="" />'; 
                            } ?>

							<?php if(!empty($additional_images[0])) { ?>
						        <div class="gallery-images">
                                    <?php 
                                        $additional_images = explode(",", $additional_images[0]);
                                        foreach ($additional_images as $additional_image) {
                                            if(!empty($additional_image)) {
                                                echo '<a href="'.$additional_image.'" target="_blank"><img src="'. $additional_image .'" alt="" /></a>';
                                            }
                                        } ?>
                                    <div class="clear"></div>
                                </div>
						    <?php } ?>
						</div>
                	<?php } ?>

                	<?php if($slug == 'description' && (!empty($post->post_content) || $custom_fields_count > 0) ) { ?>
                    <!--******************************************************-->
                    <!-- DESCRIPTION -->
                    <!--******************************************************-->
						<div class="property-single-item content widget property-<?php echo esc_attr($slug); ?>">
							<?php if(!empty($label)) { ?>
                                <div class="module-header module-header-left">
                                    <h4><?php echo esc_attr($label); ?></h4>
                                    <div class="widget-divider"><div class="bar"></div></div>
                                </div>
                            <?php } ?>
							<?php //the_content(); ?>

                            <?php 
                                $custom_fields = get_option('ns_property_custom_fields');
                                if(!empty($custom_fields)) { 
                                    $count = 0; ?>
                                    <ul class="additional-details-list clean-list <?php if($custom_fields_count <= 3) { echo 'one-col'; } ?>">                    
                                    <?php foreach ($custom_fields as $custom_field) { 
                                        $fieldValue = get_post_meta($postID, 'rypecore_custom_field_'.$custom_field['id'], true);  
                                        if(!empty($fieldValue)) { ?>
                                            <li>   
                                                <?php echo $custom_field['name']; ?>: 
                                                <span><?php echo $fieldValue; ?></span>
                                            </li>
                                        <?php } ?>
                                    <?php $count++; }
                                    echo '</ul>';
                                } 
                            ?>

						</div>
                	<?php } ?>

                	<?php if($slug == 'amenities' && !empty($property_amenities)) { ?>
                    <!--******************************************************-->
                    <!-- AMENITIES -->
                    <!--******************************************************-->
						<div class="property-single-item widget property-<?php echo esc_attr($slug); ?>">
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
                        <div class="property-single-item widget property-<?php echo esc_attr($slug); ?>">
                            <?php if(!empty($label)) { ?>
                                <div class="module-header module-header-left">
                                    <h4><?php echo esc_attr($label); ?></h4>
                                    <div class="widget-divider"><div class="bar"></div></div>
                                </div>
                            <?php } ?>

                            <div class="accordion" class="accordion-floor-plans">
                                <?php 
                                    $floor_plans = unserialize($floor_plans[0]);

                                    if(!empty($floor_plans)) {   
                                        foreach ($floor_plans as $floor_plan) { ?>
                                            <h3 class="accordion-tab"><?php echo esc_html_e($floor_plan['title'], 'rype-real-estate'); ?></h3>
                                            <div class="floor-plan-item"> 
                                                <table>
                                                    <tr>
                                                        <td><strong><?php esc_html_e('Size', 'rype-real-estate'); ?></strong></td>
                                                        <td><strong><?php esc_html_e('Rooms', 'rype-real-estate'); ?></strong></td>
                                                        <td><strong><?php esc_html_e('Bathrooms', 'rype-real-estate'); ?></strong></td>
                                                    </tr>
                                                    <tr>
                                                        <td><?php if(!empty($floor_plan['size'])) { echo esc_attr($floor_plan['size']); } else { echo '--'; } ?></td>
                                                        <td><?php if(!empty($floor_plan['rooms'])) { echo esc_attr($floor_plan['rooms']); } else { echo '--'; } ?></td>
                                                        <td><?php if(!empty($floor_plan['baths'])) { echo esc_attr($floor_plan['baths']); } else { echo '--'; } ?></td>
                                                    </tr>
                                                </table>
                                                <?php if(!empty($floor_plan['description'])) { echo '<p>'.esc_html__($floor_plan['description'], 'rype-real-estate').'</p>'; } ?>
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
						<div class="property-single-item widget property-<?php echo esc_attr($slug); ?>">
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
						<div class="property-single-item widget property-<?php echo esc_attr($slug); ?>">
						
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
                                <a href="<?php echo esc_url($walkScoreData['ws_link']); ?>" target="_blank" class="button"><?php esc_html_e('View More Details', 'rype-real-estate'); ?></a>
							</div>
						</div>
					<?php } ?>

                	<?php if($slug == 'video' && !empty($video_url)) { ?>
                    <!--******************************************************-->
                    <!-- VIDEO -->
                    <!--******************************************************-->
						<div class="property-single-item widget property-<?php echo esc_attr($slug); ?>">
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
									<img src="<?php echo esc_url( get_template_directory_uri() ); ?>/images/property-img-default.gif" alt="" />
								<?php } ?>
							</a>
						</div>
                	<?php } ?>

                    <?php if($slug == 'agent_info' && $agent_display != 'none') { ?>
                    <!--******************************************************-->
                    <!-- OWNER INFO -->
                    <!--******************************************************-->
						<div class="property-single-item widget property-<?php echo esc_attr($slug); ?>">
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
                                            echo '<img src="'. esc_url( get_template_directory_uri() ) .'/images/avatar-default.png" alt="avatar" />';
                                        }
                                    ?>
                                    <p><?php echo ns_core_get_icon($icon_set, 'user'); ?> <?php the_author_meta('display_name'); ?></p>
                                    <p><?php echo ns_core_get_icon($icon_set, 'envelope'); ?> <?php the_author_meta('user_email'); ?></p>
                                    <?php if (get_the_author_meta('user_url')  != '') { ?><a href="<?php the_author_meta('user_url'); ?>" target="_blank"><?php echo ns_core_get_icon($icon_set, 'globe', 'link'); ?> <?php the_author_meta('user_url'); ?> </a><?php } ?>
									<div class="clear"></div>
								</div>

                            <?php } else if($agent_display == 'agent') { ?>
                                
                                <?php

                                    $agent_listing_args = array(
                                        'post_type' => 'rype-agent',
                                        'posts_per_page' => 1,
                                        'p' => $agent_select
                                        );

                                    $agent_listing_query = new WP_Query( $agent_listing_args );
                                ?>

                                <?php if ( $agent_listing_query->have_posts() ) : while ( $agent_listing_query->have_posts() ) : $agent_listing_query->the_post(); ?>

                                    <?php
                                    //Get post meta data
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
                                        $agent_form_source = isset( $agent_details_values['rypecore_agent_form_source'] ) ? esc_attr( $agent_details_values['rypecore_agent_form_source'][0] ) : 'default';
                                        $agent_form_id = isset( $agent_details_values['rypecore_agent_form_id'] ) ? esc_attr( $agent_details_values['rypecore_agent_form_id'][0] ) : '';

                                        //property post count
                                        $args = array(
                                            'post_type' => 'ns-property',
                                            'showposts' => -1,
                                            'meta_key' => 'rypecore_agent_select',
                                            'meta_value' => get_the_ID()
                                        );

                                        $meta_posts = get_posts( $args );
                                        $meta_post_count = count( $meta_posts );
                                        unset( $meta_posts);
                                    ?>

                                    <div class="agent property-agent">
                                        
                                        <a href="<?php the_permalink(); ?>" class="agent-img">
                                            <?php if(isset($meta_post_count) && $meta_post_count > 0) { ?>
                                                <div class="button alt agent-tag"><?php echo esc_attr($meta_post_count); ?> <?php if($meta_post_count <= 1) { esc_html_e('Property', 'rype-real-estate'); } else { esc_html_e('Properties', 'rype-real-estate'); } ?></div>
                                            <?php } ?>
                                            <?php if ( has_post_thumbnail() ) {  ?>
                                                <div class="img-fade"></div>
                                                <?php the_post_thumbnail('full'); ?>
                                            <?php } else { ?>
                                                <img src="<?php echo plugins_url( '/rype-real-estate/images/agent-img-default.gif' ); ?>" alt="" />
                                            <?php } ?>
                                        </a>

                                        <div class="agent-content">
                                            <a href="<?php the_permalink(); ?>" class="button button-icon right"><i class="fa fa-angle-right"></i><?php esc_html_e('Contact Agent', 'rype-real-estate'); ?></a>
                                            <div class="agent-details">
                                                <h4><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h4>
                                                <?php if(!empty($agent_title)) { ?><p><?php echo ns_core_get_icon($icon_set, 'tag'); ?><?php echo esc_attr($agent_title); ?></p><?php } ?>
                                                <?php if(!empty($agent_email)) { ?><p><?php echo ns_core_get_icon($icon_set, 'envelope', 'envelope', 'mail'); ?><?php echo esc_attr($agent_email); ?></p><?php } ?>
                                                <?php if(!empty($agent_mobile_phone)) { ?><p><?php echo ns_core_get_icon($icon_set, 'phone', 'telephone'); ?><?php echo esc_attr($agent_mobile_phone); ?></p><?php } ?>
                                                <?php if(!empty($agent_office_phone)) { ?><p><?php echo ns_core_get_icon($icon_set, 'building', 'apartment', 'briefcase'); ?><?php echo esc_attr($agent_office_phone); ?></p><?php } ?>
                                            </div>
                                            <?php if(!empty($agent_fb) || !empty($agent_twitter) || !empty($agent_google) || !empty($agent_linkedin) || !empty($agent_youtube) || !empty($agent_instagram)) { ?>
                                                <ul class="social-icons circle clean-list">
                                                    <?php if(!empty($agent_fb)) { ?><li><a href="<?php echo esc_url($agent_fb); ?>" target="_blank"><i class="fa fa-facebook"></i></a></li><?php } ?>
                                                    <?php if(!empty($agent_twitter)) { ?><li><a href="<?php echo esc_url($agent_twitter); ?>" target="_blank"><i class="fa fa-twitter"></i></a></li><?php } ?>
                                                    <?php if(!empty($agent_google)) { ?><li><a href="<?php echo esc_url($agent_google); ?>" target="_blank"><i class="fa fa-google-plus"></i></a></li><?php } ?>
                                                    <?php if(!empty($agent_linkedin)) { ?><li><a href="<?php echo esc_url($agent_linkedin); ?>" target="_blank"><i class="fa fa-linkedin"></i></a></li><?php } ?>
                                                    <?php if(!empty($agent_youtube)) { ?><li><a href="<?php echo esc_url($agent_youtube); ?>" target="_blank"><i class="fa fa-youtube"></i></a></li><?php } ?>
                                                    <?php if(!empty($agent_instagram)) { ?><li><a href="<?php echo esc_url($agent_instagram); ?>" target="_blank"><i class="fa fa-instagram"></i></a></li><?php } ?>
                                                </ul>
                                            <?php } ?>
                                        </div>
                                        <div class="clear"></div>
                                        <?php
                                        if($property_detail_agent_contact_form == 'true') {
                                            if($agent_form_source == 'contact-form-7') {
                                                $agent_form_title = get_the_title( $agent_form_id );
                                                echo do_shortcode('[contact-form-7 id="<?php echo esc_attr($agent_form_id); ?>" title="'.$agent_form_title.'"]');
                                            } else { 
                                                if(function_exists('rype_real_estate_agent_contact_form')) {
                                                    if(!empty($agent_email)) { 
                                                        rype_real_estate_agent_contact_form($agent_email); 
                                                    }
                                                } else {
                                                    esc_html_e('Please install required plugins to display the contact form.', 'rype-real-estate');
                                                }
                                            } 
                                        }
                                        ?>
                                    </div><!-- end agent -->

                                <?php endwhile; ?>
                                    <?php wp_reset_postdata(); ?>
                                <?php else: ?>
                                    <div class="col-lg-12"><p><?php esc_html_e('Sorry, no agents have been posted yet.', 'rype-real-estate'); ?></p></div>
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
						<div class="property-single-item widget property-<?php echo esc_attr($slug); ?>">
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
                                $template_args_related_properties['no_post_message'] = esc_html__( 'Sorry, no related properties were found.', 'rype-real-estate' );
                                
                                //Load template
                                rype_real_estate_template_loader('loop_properties.php', $template_args_related_properties);
                            ?>
						</div>
					<?php } ?>

                    <?php if(!empty($add_on)) { ?>
                        <!--******************************************************-->
                        <!-- ADD-ONS -->
                        <!--******************************************************-->
                        <?php do_action('rype_real_estate_property_detail_items', $values, $value); ?>
                    <?php } ?>

                <?php } ?>
            <?php } //end foreach ?>
        <?php } //end if ?>


	</div><!-- end property single -->