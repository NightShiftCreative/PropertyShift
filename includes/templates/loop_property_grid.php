<?php
    global $post; 
    $icon_set = esc_attr(get_option('rypecore_icon_set', 'fa'));
	$properties_page = esc_attr(get_option('rypecore_properties_page'));
    $property_listing_display_time = esc_attr(get_option('rypecore_property_listing_display_time', 'true'));
    $property_listing_display_favorite = esc_attr(get_option('rypecore_property_listing_display_favorite', 'true'));
    $property_listing_display_share = esc_attr(get_option('rypecore_property_listing_display_share', 'true'));
    $property_listing_crop = esc_attr(get_option('rypecore_property_listing_crop', 'true'));
	
	//Get property details
    $values = get_post_custom( $post->ID );
    $featured = isset( $values['rypecore_property_featured'] ) ? esc_attr( $values['rypecore_property_featured'][0] ) : 'false';
	if(function_exists('rype_real_estate_get_property_address')) { $address = rype_real_estate_get_property_address($post->ID); } else { $address = ''; } 
    $price = isset( $values['rypecore_property_price'] ) ? esc_attr( $values['rypecore_property_price'][0] ) : '';
	$price_postfix = isset( $values['rypecore_property_price_postfix'] ) ? esc_attr( $values['rypecore_property_price_postfix'][0] ) : '';
	$area = isset( $values['rypecore_property_area'] ) ? esc_attr( $values['rypecore_property_area'][0] ) : '';
    if(!empty($area) && function_exists('rype_real_estate_format_area')) { $area = rype_real_estate_format_area($area); }
    $area_postfix = isset( $values['rypecore_property_area_postfix'] ) ? esc_attr( $values['rypecore_property_area_postfix'][0] ) : '';
    $bedrooms = isset( $values['rypecore_property_bedrooms'] ) ? esc_attr( $values['rypecore_property_bedrooms'][0] ) : '';
    $bathrooms = isset( $values['rypecore_property_bathrooms'] ) ? esc_attr( $values['rypecore_property_bathrooms'][0] ) : '';
	$garages = isset( $values['rypecore_property_garages'] ) ? esc_attr( $values['rypecore_property_garages'][0] ) : '';
	
	//Get property taxonomies
	if(function_exists('rype_real_estate_get_property_status')) { $property_status = rype_real_estate_get_property_status($post->ID); } else { $property_status = ''; }
    if(function_exists('rype_real_estate_get_property_location')) { 
    	$property_location = rype_real_estate_get_property_location($post->ID, 'parent');
    	$property_location_children = rype_real_estate_get_property_location($post->ID, 'children');
    } else {
    	$property_location = '';
    	$property_location_children = '';
    }	
?>

<div <?php post_class(); ?>>

	<div class="property-img">

		<?php if($featured == 'true') { ?><a href="<?php if(!empty($properties_page)) { echo esc_url($properties_page).'/?featured=true'; } ?>" class="property-tag button alt featured"><?php esc_html_e('Featured', 'rypecore'); ?></a><?php } ?>

		<?php if ( has_post_thumbnail() ) {  ?>
            <div class="img-fade"></div>
			<a href="<?php the_permalink(); ?>" class="property-img-link">
                <?php if($property_listing_crop == 'true') { the_post_thumbnail('property-thumbnail'); } else { the_post_thumbnail('full'); } ?>
            </a>
		<?php } else { ?>
			<a href="<?php the_permalink(); ?>" class="property-img-link"><img src="<?php echo plugins_url( '/rype-real-estate/images/property-img-default.gif' ); ?>" alt="" /></a>
		<?php } ?>
	</div>
	
	<div class="property-content">

		<?php if(!empty($property_status)) { ?>
			<div class="property-tag button status"><?php echo wp_kses_post($property_status); ?></div>
		<?php } ?>

		<?php if($property_listing_display_time == 'true' || $property_listing_display_favorite == 'true' || $property_listing_display_share == 'true') { ?>
			<div class="property-actions">
				<?php if($property_listing_display_time == 'true') {
					$toggle = ns_core_get_icon($icon_set, 'calendar-o', 'clock3', 'clock');
					$content = human_time_diff( get_the_time('U'), current_time('timestamp') ) . esc_html__(' ago', 'rypecore'); 
					echo ns_basics_tooltip($toggle, $content); 
				} ?>
		        <?php if($property_listing_display_favorite == 'true' && function_exists('ns_basics_get_post_likes_button')) { echo ns_basics_get_post_likes_button(get_the_ID()); } ?>
		        <?php if($property_listing_display_share == 'true' && function_exists('ns_basics_get_social_share')) { echo ns_basics_get_social_share(); } ?>
			</div>
		<?php } ?>

		<?php if(!empty($price)) { ?>
			<div class="property-price">
				<?php echo rype_basics_format_price($price); ?>
				<?php if(!empty($price_postfix)) { ?><span class="price-postfix"><?php echo esc_attr($price_postfix); ?></span><?php } ?>
			</div>
		<?php } ?>

		<div class="property-title">
            <h4><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h4>
            <?php if(!empty($address)) { echo '<p class="property-address">'.$address.'</p>'; } ?>
        </div>

        <table class="property-details">
            <tr>
				<?php if(!empty($bedrooms)) { ?><td><?php echo ns_core_get_icon($icon_set, 'bed', 'bed', 'n/a'); ?> <?php echo esc_attr($bedrooms).' '; esc_html_e('Beds', 'rypecore'); ?></td><?php } ?>
				<?php if(!empty($bathrooms)) { ?><td><?php echo ns_core_get_icon($icon_set, 'tint', 'bathtub', 'n/a'); ?> <?php echo esc_attr($bathrooms).' '; esc_html_e('Baths', 'rypecore'); ?></td><?php } ?>
            	<?php if(!empty($area)) { ?><td><?php echo ns_core_get_icon($icon_set, 'expand'); ?> <?php echo esc_attr($area); ?> <?php if(!empty($area_postfix)) { echo '<span class="area-postfix">'.esc_attr($area_postfix).'</span>'; } ?></td><?php } ?>
            </tr>
        </table>
	</div>

</div>